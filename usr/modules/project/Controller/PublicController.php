<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Str Helper class
*/

namespace Project\Controller;

use \Boot\Controller\AbstractController;

class PublicController extends AbstractController
{

    public static function index($route = null)
    {
        $title = 'Проектирование';
        $breadcrumbs = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => $title, 'url' => router('project.index')],
        ];

        display('project/index.html.twig', [
            'breadcrumbs' => $breadcrumbs,
            'title' => $title,
            'page_title' => $title,
            'page_description' => $title,
        ]);
    }

    public static function order($route = null)
    {
        $title = 'Заявка на проектирование';
        $breadcrumbs = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => 'Проектирование', 'url' => router('project.index')],
            ['name' => $title, 'url' => router('project.order')],
        ];

        display('project/order.html.twig', [
            'breadcrumbs' => $breadcrumbs,
            'title' => $title,
            'page_title' => $title,
            'page_description' => $title,
        ]);
    }
}