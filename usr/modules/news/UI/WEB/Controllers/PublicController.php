<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description Str Helper class
*/

namespace News\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;
use News\Models\News;
use \Boot\System\Categories\Categories;

class PublicController extends AbstractController
{
    public static $module = 'news';

    public static function index($route = null)
    {
        $model = News::with('categories')
            ->where('status', 1)
            //->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc');

        $categories = new Categories;

        $breadcrumbs = breadcrumbs('news');

        if ($route) {
            $cat = $categories->getCategoryBySlug($route);
            if ($cat) {
                $model->where('category', $cat);
                array_push($breadcrumbs, ['name' => $categories->getCategory($cat)->get('name')]);
            } else {
                return redirect()->route('news.index');
            }
        }

        return view('news::list', [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories->getModuleCategories('news'),
            'items' => $model->paginate(config('news-config.paginate'))
        ]);
    }

    public static function tags($tag = null)
    {
        if (!empty($tag)) {
            $model = News::with('categories')
                ->where('status', 1)
                ->whereJsonContains('tags', $tag)
                ->orderBy('id', 'desc');

            $categories = new Categories;

            $title = 'Новости и статьи: ' . $tag;

            $crumbs = [
                ['name' => $title, 'url' => url('news/tags', $tag)],
            ];
            $breadcrumbs = breadcrumbs('news', $crumbs);

            return view('news::list', [
                'title' => $title,
                'breadcrumbs' => $breadcrumbs,
                'categories' => $categories->getModuleCategories('news'),
                'items' => $model->paginate(config('news-config.paginate'))
            ]);

        } else {
            return redirect()->route('news.index');
        }

    }

    public static function date($date = null)
    {
        if (!empty($date)) {
            $dateFrom = date('Y-m-d H:i:s', strtotime($date));
            $dateTo = date('Y-m-d H:i:s', strtotime($date . ' +1 day'));
            $model = News::with('categories')
                ->where('status', 1)
                ->whereBetween('published_at', [$dateFrom, $dateTo]);

            $categories = new Categories;

            $title = 'Новости и статьи по дате: ' . $date;

            $crumbs = [
                ['name' => $date, 'url' => url('news/tags', $date)],
            ];
            $breadcrumbs = breadcrumbs('news', $crumbs);

            return view('news::list', [
                'title' => $title,
                'breadcrumbs' => $breadcrumbs,
                'categories' => $categories->getModuleCategories('news'),
                'items' => $model->paginate(config('news-config.paginate'))
            ]);

        } else {
            return redirect()->route('news.index');
        }

    }

    public static function show($categories = null, $post = null)
    {
        $item = News::with('categories')
                ->where('slug', $post)
                ->first();
        $cat = new Categories;

        if ($cat->getCategory($item->category)->get('altname') !== $categories) {
            return redirect()->route('news.index');
        }

        $item->increment('views');

        $title = $item->title;
        $titleCategory = $cat->getCategory($item->category)->get('name');

        $crumbs = [
            ['name' => $titleCategory, 'url' => url('news', $cat->getCategoryFullUrl($item->category))],
            ['name' => $title],
        ];
        $breadcrumbs = breadcrumbs('news', $crumbs);

        // Через Helper
        //prepare_image($item->preview['source'] ?? false,'files/news/'.$item->id.'/thumb', $item );
        //dd(prepare_image($item->preview['source']));

        return view('news::full', [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $cat->getModuleCategories('news'),
            'item' => $item
        ]);

    }

}