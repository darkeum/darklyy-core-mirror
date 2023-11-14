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

namespace Home\UI\WEB\Controllers;

use Boot\Controller\AbstractController;
use Boot\Models\Categories;
use News\Models\News;
use Slider\Models\Slider;
use Partner\Models\Partner;

class PublicController extends AbstractController {

    public static $module = 'home';

    public static function index($route = null) {
        $news = News::where('status', 1)->orderBy('id', 'DESC')->limit(3)->get();
        $partner = Partner::where('status', 1)->orderBy('id', 'DESC')->limit(10)->get();
        $category = Categories::where('module', 'catalog_v2')->where('parent_id', '0')->where('hidden', 0)->orderBy('position', 'ASC')->limit(8)->get();
        $slides = Slider::orderBy('priority', 'DESC')->limit(5)->get();

        // 407 309 310 281 296 272 313 292 294

        return view('home::home', [
                'menu_index' => true,
                'menu_view' => true,

                'news' => $news,
                'partner' => $partner,
                'category' => $category,
                'slides' => $slides,

                'main_class' => 'theme-home',
                'footer_class' => 'footer-home'
        ]);
    }


}