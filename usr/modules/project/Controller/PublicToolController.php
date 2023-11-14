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

namespace Project\Controller;

use \Boot\Controller\AbstractController;
use \Boot\System\Categories\Categories;
use \Boot\Services\Request;
use \Boot\Support\Facades\Router;
use \Boot\System\Validation\Validator;
use \Order\Model\Order;
use \Order\Model\OrderHeat;

class PublicToolController extends AbstractController{

    public static function index(){ 
        $title = 'Инструменты';  
        $breadcrumbs  = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => 'Проектирование', 'url' => router('project.index')],
            ['name' => $title, 'url' => router('project.tool.index')],
        ];
        display('project/tool/index.html.twig', [
            'breadcrumbs' =>  $breadcrumbs,
            'title' => $title,
            'page_title' => $title,
            'page_description' => $title,
        ]);
    }

    public static function pipes($route = null){ 
        $title = 'Таблица Шевелева';  
        $breadcrumbs  = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => 'Проектирование', 'url' => router('project.index')],
            ['name' => 'Инструменты', 'url' => router('project.tool.index')],
            ['name' =>  $title, 'url' => router('project.tool.pipes')],
        ];
        display('project/tool/pipes.html.twig', [
            'breadcrumbs' =>  $breadcrumbs,
            'title' => $title,
            'page_title' => $title,
            'page_description' => $title,
        ]);
    }

    public static function power($route = null){ 
        $title = 'Перевод единиц мощности';  
        $breadcrumbs  = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => 'Проектирование', 'url' => router('project.index')],
            ['name' => 'Инструменты', 'url' => router('project.tool.index')],
            ['name' =>  $title, 'url' => router('project.tool.power')],
        ];
        display('project/tool/power.html.twig', [
            'breadcrumbs' =>  $breadcrumbs,
            'title' => $title,
            'page_title' => $title,
            'page_description' => $title,
        ]);
    }

    public static function scales($route = null){ 
        $title = 'Калькулятор метиза, крепежа и металлопроката';  
        $breadcrumbs  = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => 'Проектирование', 'url' => router('project.index')],
            ['name' => 'Инструменты', 'url' => router('project.tool.index')],
            ['name' =>  $title, 'url' => router('project.tool.scales')],
        ];
        display('project/tool/scales.html.twig', [
            'breadcrumbs' =>  $breadcrumbs,
            'title' => $title,
            'page_title' => $title,
            'page_description' => $title,
        ]);
    }




}