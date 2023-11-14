<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Portfolio Public Controller
*/

namespace Portfolio\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;
use \Portfolio\Models\Portfolio;
use \Boot\System\Categories\Categories;
use \Boot\System\Validation\Validator;
use \Boot\Services\Request;
use \Boot\Support\Cache;
use \Boot\Support\Facades\Auth;
use \Boot\Support\Facades\Router;
use \Order\Models\Order;
use \Order\ModelS\OrderJob;

class PublicController extends AbstractController
{
    public static $module = 'portfolio';

    public static function index($route = null)
    {
        $model = Portfolio::with('categories')
            ->where('status', 1)
            ->orderBy('title', 'desc');

        $categories = new Categories;

        $breadcrumbs = breadcrumbs('portfolio');

        if ($route) {
            $cat = $categories->getCategoryBySlug($route);
            if ($cat) {
                $model->where('category', $cat);
                array_push($breadcrumbs, ['name' => $categories->getCategory($cat)->get('name')]);
            } else {
                return redirect()->route('portfolio.index');
            }
        }

        return view('portfolio::list', [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories->getModuleCategories('portfolio'),
            'items' => $model->paginate(config('portfolio-config.paginate'))
        ]);

    }
 
    public static function show($categories = null, $post = null)
    {
        $items = Portfolio::with('categories')
            ->where('status', 1)
            ->orderBy('title', 'desc')
            ->get();
        $item = $items->where('slug', $post)->last();

        $cat = new Categories;

        if ($cat->getCategory($item->category)->get('altname') !== $categories) {
            return redirect()->route('portfolio.index');
        }

        $title = $item->title;
        $titleCategory = $cat->getCategory($item->category)->get('name');

        $crumbs  = [
            ['name' => $titleCategory, 'url' => url('portfolio', $cat->getCategoryFullUrl($item->category))],
            ['name' => $title, 'url' => $cat->getCategoryFullUrl($item->category)],
        ];

        $breadcrumbs = breadcrumbs('portfolio', $crumbs);

        // Через Helper
        // prepare_image($item->preview['source'] ?? false, 'files/portfolio/' . $item->id . '/thumb');
        
        return view('portfolio::full', [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $cat->getModuleCategories('portfolio'),
            'item' => $item,
            'items' => $items
        ]);
    }
}
