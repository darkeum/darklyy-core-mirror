<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Certificates Manager Controller
*/

namespace Certificates\Controller;

use \Boot\Controller\AbstractController;
use \Certificates\Model\Certificates;
use \Boot\System\Categories\Categories;
use \Certificates\Grid\IndexGrid;
use \Boot\Support\Facades\Translation;
use \Boot\Services\Request;


class ManagerController extends AbstractController
{
    public static $module = 'certificates';
    public static $lang = 'certificates';

    public static function index()
    {
        $grid = new IndexGrid;
        display('@'.static::$module.'/manager/index.html.twig', [
            'title' => Translation::trans(static::$lang),
            'grid' =>  $grid->render(),
        ]);
    }

    public static function create()
    {
        $categories = new Categories;           
        $dir = ROOT.'files/'.static::$module.'/temp';
		if (!file_exists($dir)) {
			mkdir($dir, 0777);
        }   
        display('@'.static::$module.'/manager/editor.html.twig', [
            'title' => Translation::trans(static::$lang.'.added'),
            'categories' =>  $categories->getModuleCategories(static::$module),
            'do' => Translation::trans(static::$lang.'.editor.add'),      
            'type' => 'store',
        ]);
    }

    public static function store(Request $request)
    {
        Certificates::create($request->request->all());
        display('@'.static::$module.'/manager/info.html.twig', [
            'title' => Translation::trans(static::$lang.'.editor.added'),
            'info' => Translation::trans(static::$lang.'.info.add'),
        ]);
    }    

    public static function update($id, Request $request)
    {
        Certificates::find($id)->update($request->request->all());
        display('@'.static::$module.'/manager/info.html.twig', [
            'title' => Translation::trans(static::$lang.'.editor.edited'),
            'info' => Translation::trans(static::$lang.'.info.edit'),
        ]);
    }

    public static function edit($id)
    {
        $categories = new Categories;   
        $data = Certificates::find($id);
        display('@'.static::$module.'/manager/editor.html.twig', [
            'title' => Translation::trans(static::$lang.'.editor.edited'),
            'categories' =>  $categories->getModuleCategories(static::$module),
            'do' => Translation::trans(static::$lang.'.editor.edit'),
            'data' => $data,
            'type' => 'update',
            'id' => $id,
        ]);
    }
}
