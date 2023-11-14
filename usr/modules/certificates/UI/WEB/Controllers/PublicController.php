<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Certificates Public Controller
*/

namespace Certificates\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;
use \Certificates\Models\Certificates;
use \Boot\System\Categories\Categories;

class PublicController extends AbstractController
{

    public static $module = 'certificates';

    public static function index($route = null)
    {
        $model = Certificates::with('categories')
            ->where('status', 1)
            ->orderBy('id', 'desc');

        $categories = new Categories;

        $breadcrumbs = breadcrumbs('certificates');

        if ($route) {
            $cat = $categories->getCategoryBySlug($route);
            if ($cat) {
                $model->where('category', $cat);
                array_push($breadcrumbs, ['name' => $categories->getCategory($cat)->get('name')]);
            } else {
                return redirect()->route('certificates.index');
            }
        }

        return view('certificates::list', [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories->getModuleCategories('certificates'),
            'items' => $model->paginate(config('certificates-config.paginate'))
        ]);
    }

    public static function tags($tag = null)
    {
        if (!empty($tag)) {
            $model = Certificates::with('categories')
                ->where('status', 1)
                ->whereJsonContains('tags', $tag)
                ->orderBy('id', 'desc');

            $categories = new Categories;

            $title = 'Тег: ' . $tag;

            $crumbs = [
                ['name' => $title, 'url' => url('certificates/tags', $tag)],
            ];
            $breadcrumbs = breadcrumbs('certificates', $crumbs);

            return view('certificates::list', [
                'title' => $title,
                'breadcrumbs' => $breadcrumbs,
                'categories' => $categories->getModuleCategories(static::$module),
                'items' => $model->paginate(config('certificates-config.paginate'))
            ]);

        } else {
            return redirect()->route('certificates.index');
        }
    }

    public static function show($categories = null, $post = null)
    {
        $item = $model = Certificates::with('categories')
            ->where('slug', $post)
            ->where('status', 1)
            ->get()
            ->last();

        $cat = new Categories;

        if ($cat->getCategory($item->category)->get('altname') !== $categories) {
            return redirect()->route('certificates.index');
        }

        $model->increment('views');

        $title = $item->title;
        $titleCategory = $cat->getCategory($item->category)->get('name');

        $crumbs = [
            ['name' => $titleCategory, 'url' => '/' . static::$module . '/' . $cat->getCategoryFullUrl($item->category)],
            ['name' => $title, 'url' => $cat->getCategoryFullUrl($item->category)],
        ];
        $breadcrumbs = breadcrumbs('certificates', $crumbs);

        // Через Helper
        prepare_image($item->preview['source'] ?? false, 'files/certificates/' . $item->id . '/thumb');

        return view('certificates::full', [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $cat->getModuleCategories('certificates'),
            'item' => $item
        ]);
    }
}