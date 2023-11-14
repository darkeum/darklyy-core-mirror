<?php 

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Partner Public Controller
*/

namespace Partner\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;
use \Partner\Models\Partner;
use \Boot\System\Categories\Categories;

class PublicController extends AbstractController{

    public static $module = 'partner';

    public static function index($route = null)
    {
        $model = Partner::with('categories')
            ->where('status', 1)
            ->orderBy('id', 'desc');

        $categories = new Categories; 

        $breadcrumbs = breadcrumbs('partner');

        if ($route) {
            $cat = $categories->getCategoryBySlug($route);
            if ($cat) {
                $model->where('category', $cat);
                array_push($breadcrumbs, ['name' => $categories->getCategory($cat)->get('name')]);
            } else {
                return redirect()->route('partner.index');
            }
        }

        return view('partner::list', [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories->getModuleCategories('partner'),
            'items' => $model->paginate(config('partner-config.paginate'))
        ]);

    }

    public static function tags($tag = null)
    {
        if(!empty($tag)) {
            $model = Partner::with('categories')
                ->where('status', 1)
                ->whereJsonContains('tags', $tag)
                ->orderBy('id', 'desc');

            $categories = new Categories;

            $title = 'Тег: '. $tag;

            $crumbs  = [
                ['name' => $title, 'url' =>  url('{static::$module}/tags/', $tag)],
            ];
            $breadcrumbs = breadcrumbs('partner', $crumbs);

            return view('partner::list', [
                'breadcrumbs' => $breadcrumbs,
                'categories' => $categories->getModuleCategories('partner'),
                'items' => $model->paginate(config('partner-config.paginate'))
            ]);

        } else {
            return redirect()->route('partner.index');
        }        
    }

    public static function show($categories = null, $post = null)
    {

        $item = Partner::with('categories')
            ->where('slug', $post)
            ->where('status', 1)
            ->get()
            ->last();

        $cat = new Categories;

        if ($cat->getCategory($item->category)->get('altname') !== $categories) {
            return redirect()->route('partner.index');
        }

        $title = $item->title;
        $titleCategory = $cat->getCategory($item->category)->get('name');

        $crumbs  = [
            ['name' => $titleCategory, 'url' => '/'.static::$module.'/' . $cat->getCategoryFullUrl($item->category)],
            ['name' => $title, 'url' => $cat->getCategoryFullUrl($item->category)],
        ];
        $breadcrumbs = breadcrumbs('partner', $crumbs);

        return view('partner::full', [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $cat->getModuleCategories(static::$module),
            'item' => $item
        ]);
    }

    public static function order(){
        $error_output = '';
        $success_output = '';
        $type = 'success';
        if (!empty($_POST['name']) && !empty($_POST['phone']) && !empty($_POST['message'])) {
    
            $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
            $recaptcha_secret = '6LfddtUZAAAAAOJmTgbOOIec5WaFIOnIrOqBFk7I';
            $recaptcha_response = $_POST['recaptcha_response'];
            $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
            $recaptcha = json_decode($recaptcha);
            $dataArray = array();
            $dataArray['type'] = 'partner';
            $dataArray['status'] = '1';
            if ($recaptcha->success == true && $recaptcha->score >= 0.5 && $recaptcha->action == 'contact') {              
                $dadata = new \Dadata\DadataClient('ddeafb9ff43e79578243d8f031f6e9d8b047ca0e', 'e03a8f8ceaa114f8d8e0e09af92a0f3b069f1d40');
                $response = $dadata->clean("name", filter($_POST['name']));
                if ($response['qc'] == '0') {
                    $dataArray['surname'] = $response['surname'];
                    $dataArray['name'] = $response['name'];
                    $dataArray['ochestvo'] = $response['patronymic'];
                    $dataArray['fio_source'] = $response;
                    $responsePhone = $dadata->clean("phone", filter($_POST['phone']));
                    if ($responsePhone['qc'] != '2') {
                        $dataArray['phone'] = $responsePhone['phone'];
                        $dataArray['phone_source'] = $responsePhone;
                    } else {
                        $type = 'error';
                        $error_output = "Пожалуйста укажите реальный номер телефон";
                    }
                } else {
                    $type = 'error';
                    $error_output = "Пожалуйста укажите реальное имя";
                }               
                $dataArray['message'] = filter($_POST['message']);
                if ($type == 'success') {
                    $order = new \Order\Model\Order;
                    $order->create($dataArray);
                    $success_output = 'Ваше сообщение успешно отправлено';
                }
            } else {
                $error_output = "Вы не прошли проверку на робота, попробуйте ещё раз! Если ошибка сохраняется позвоните нам по телефону +7(3412)908-123 или напишите нам на электронную почту info@atri-energo.ru";
                $type = 'error';
            }
        } else {
            $error_output = "Пожалуйста заполните все обязательные поля!";
            $type = 'error';
        }
        $output = array(
            'type'		=>	$type,
            'error'     =>  $error_output,
            'success'   =>  $success_output
        );
        echo json_encode($output);
        exit;
    }
}