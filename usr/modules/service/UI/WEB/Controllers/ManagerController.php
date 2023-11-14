<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Portfolio Manager Controller
*/

namespace Service\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;
use \Order\Model\Order;
use \Order\Grid\IndexGrid;
use \Boot\System\Categories\Categories;
use \Boot\Support\Str;
use \Boot\Support\Facades\Auth;
use \Boot\Support\Facades\Translation;
use \Boot\Services\Request;
use \Boot\Models\Users;

class ManagerController extends AbstractController
{

    public static function index()
    {
        $grid = new IndexGrid;
        display('@order/manager/index.html.twig', [
            'title' => Translation::trans('order'),
            'grid' =>  $grid->render(),
        ]);
    }

    public static function show($id, Request $request)
    {
        $order = Order::find($id);
        display('@order/manager/show.html.twig', [
            'title' => Translation::trans('order'),
            'order' => $order,
           
        ]);
    }


    public static function create()
    {
        $categories = new Categories;
        $_SESSION["RF"]["fff"] = "files/cats/";
        display('@order/manager/editor.html.twig', [
            'title' => Translation::trans('portfolio_added'),
            'categories' =>  $categories->getModuleCategories('portfolio'),
            'do' => Translation::trans('portfolio_do_add'),
            'type' => 'store',
        ]);
    }

    public static function store(Request $request)
    {
        Portfolio::create(static::prepareRequest($request->request->all()));
        display('@order/manager/editor.html.twig', [
            'title' => Translation::trans('portfolio_added'),
            'info' => Translation::trans('portfolio_add_info'),
        ]);
    }

    protected static function prepareRequest($request)
    {
        $result = [];
        $result['title'] = $request['title'];
        $result['uid'] = Auth::id();
        $result['description'] = $request['description'];
        $result['category'] = intval($request['category']);
        $result['slug'] = empty($request['slug']) ? Str::slug($result['title']) : $request['slug'];
        $result['address'] = $request['address'];
        if(!empty($request['coord'])) {
            $result['coord'] = $request['coord'];
        }
      
        
        if(!empty($request['preview'])) {
            $result['preview'] = static::prepare_image($request['preview'], 'preview');
        } else {
            $result['preview'] = [];
        }
        $gallery = [];
        if(!empty($request['gallery'])) {
            if(Str::isJson($request['gallery'])) {
                $galleryArray = json_decode($request['gallery'], true);
                foreach ($galleryArray as $value) {
                    $gallery[] = static::prepare_image($value, 'gallery');
                }
            } else {
                $gallery[] = static::prepare_image($request['gallery'], 'gallery');
            }
        }
    //    dd($gallery);
        $result['gallery'] = $gallery;
        $result['tags'] = explode(',', $request['tags']);
        $result['full'] = $request['full'];
        $result['media'] = $request['media'];
        $result['subtitle'] = $request['subtitle'];


        return $result;
    }

    public static function prepare_image($source, $name ='preview') {    
        $image = str_replace("https://atri-energo.ru/", "", $source);
        $image = str_replace("atri-energo.ru/", "", $image);
        $image = rawurldecode($image);
        $purl = 'files/portfolio/thumb/';
        $path_info = pathinfo($image);
        $ext = $path_info['extension'];
        $file_full = $image;
        $src = ROOT . $file_full;
        // dd($src);	
        $info = pathinfo($src);
        switch (mb_strtolower($ext)) {
            default:
                $img = imageCreateFromJpeg($src);
                break;

            case 'png':
                $img = imageCreateFromPng($src);
                break;

            case 'jpg':
                $img = imageCreateFromJpeg($src);
                break;

            case 'gif':
                $img = imageCreateFromGif($src);
                break;
        }
        $uniqid = uniqid();
        imageWebp($img, ROOT . $purl . '/'.$name.'_' . $uniqid . '.' . 'webp', 80);
        imagedestroy($img);
        $preview_array = array();
        $preview_array['full'] = $source;
        $preview_array['full_webp'] = '/' . $purl . $name.'_' . $uniqid . '.webp';
        return $preview_array;
    }

    public static function update($id, Request $request)
    {
        Portfolio::find($id)->update(static::prepareRequest($request->request->all()));
        display('@portfolio/manager/info.html.twig', [
            'title' => Translation::trans('portfolio_edit'),
            'info' => Translation::trans('portfolio_edit_info'),
        ]);
    }

    public static function edit($id)
    {
        $categories = new Categories;
        $response = Portfolio::find($id);
        $data = $response;
        $gallery = [];
        if(!empty($response['gallery'])) {
            foreach ($response['gallery'] as $value) {
                $gallery[] = $value['full'];
            }   
        }          
        display('@portfolio/manager/editor.html.twig', [
            'title' => Translation::trans('portfolio_edit'),
            'categories' =>  $categories->getModuleCategories('portfolio'),
            'do' => Translation::trans('portfolio_do_edit'),
            'data' => $data,
            'gallery' => !empty($gallery) ? json_encode($gallery, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '',
            'type' => 'update',
            'id' => $id,
        ]);
    }
}
