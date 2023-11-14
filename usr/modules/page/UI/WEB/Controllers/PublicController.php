<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Service Public Controller
*/

namespace Page\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;
use Illuminate\Support\Facades\View;



class PublicController extends AbstractController
{
    public static function index($route = null)
    {
        $t_route = config('config.pages');

        // Проверка наличие строки в конфиге и файла blade
        if (isset($t_route[$route]) and View::exists('page::pages.' . $route)) {
            $title = $t_route[$route]['title'] ?? $t_route[$route];
        } else {
            return redirect()->route('home');
        }

        // Проверка на тип страницы (Блог или Фулл)
        if (isset($t_route[$route]['full'])) {

            return view('page::full', []);

        } else {

            // Проверка категорий в конфиге - Если нет то берем по умолчанию из конфига
            if (isset($t_route[$route]['cats'])) {
                 $categories = gen_cats($t_route[$route]['cats']);
            } else {
                $def_cats = config('config.default');
                // $categories = $def_cats['cats'];
                $categories = gen_cats($def_cats['cats']);
            }

            $breadcrumbs = [
                ['name' => 'Главная', 'url' => url('/')],
                // ['name' => 'Страница', 'url' => url('/')],
                ['name' => $title],
            ];

            return view('page::' . 'page', [
                'title' =>  $title,
                'breadcrumbs' => $breadcrumbs,
                'categories' => $categories,
                'share' => $t_route[$route]['share'] ?? false
            ]);
        }
    }

}
