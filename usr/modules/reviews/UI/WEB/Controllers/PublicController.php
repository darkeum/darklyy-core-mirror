<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Reviews Public Controller
*/

namespace Reviews\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;
use \Reviews\Models\Reviews;
use \Boot\System\Categories\Categories;

class PublicController extends AbstractController
{
    public static $module = 'reviews';

    public static function index($route = null)
    {
        $model = Reviews::with('categories')
            ->with('organizations')
            ->where('status', 1)
            ->orderBy('id', 'desc');

        $categories = new Categories;

        $breadcrumbs = breadcrumbs('reviews');

        if ($route) {
            $cat = $categories->getCategoryBySlug($route);
            if ($cat) {
                $model->where('category', $cat);
                array_push($breadcrumbs, ['name' => $categories->getCategory($cat)->get('name')]);
            } else {
                return redirect()->route('reviews.index');
            }
        }

        return view('reviews::list', [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories->getModuleCategories('reviews'),
            'items' => $model->paginate(config('reviews-config.paginate'))
        ]);
    }

    public static function tags($tag = null)
    {
        if (!empty($tag)) {
            $model = Reviews::with('categories')
                ->with('organizations')
                ->where('status', 1)
                ->whereJsonContains('tags', $tag)
                ->orderBy('id', 'desc');
            $categories = new Categories;

            $title = 'Отзывы клиентов: ' . $tag;

            $crumbs = [
                ['name' => $tag, 'url' => url('reviews/tags', $tag)],
            ];
            $breadcrumbs = breadcrumbs('reviews', $crumbs);

            return view('reviews::list', [
                'title' => $title,
                'breadcrumbs' => $breadcrumbs,
                'categories' => $categories->getModuleCategories('reviews'),
                'items' => $model->paginate(config('reviews-config.paginate'))
            ]);

        } else {
            return redirect()->route('reviews.index');
        }

    }

    public static function show($categories = null, $post = null)
    {
        $item = Reviews::with('categories')
            ->with('organizations')
            ->where('slug', $post)
            ->get()
            ->last();

        $cat = new Categories;
        if ($cat->getCategory($item->category)->get('altname') !== $categories) {
            return redirect()->route('reviews.index');
        }

        $title = $item->title;
        $titleCategory = $cat->getCategory($item->category)->get('name');

        $crumbs = [
            ['name' => $titleCategory, 'url' => url('reviews', $cat->getCategoryFullUrl($item->category))],
            ['name' => $title, 'url' => url($cat->getCategoryFullUrl($item->category))],
        ];
        $breadcrumbs = breadcrumbs('reviews', $crumbs);

        // Через Helper
        prepare_image($item->preview['source'] ?? false,'files/reviews/'.$item->id.'/thumb' );

        return view('reviews::full', [
            'breadcrumbs' => $breadcrumbs,
            'title' => $title,
            'categories' => $cat->getModuleCategories('reviews'),
            'item' => $item
        ]);

    }

}
