<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Job Public Controller
*/

namespace Job\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;
use \Job\Models\Job;
use \Boot\System\Categories\Categories;
use \Boot\System\Validation\Validator;
// use \Boot\Services\Route;
use \Boot\Services\Request;
use \Boot\Support\Cache;
use \Boot\Support\Facades\Auth;
use \Boot\Support\Facades\Router;
use \Order\Models\Order;
use \Order\Models\OrderJob;
use \Boot\Models\Users;

class PublicController extends AbstractController
{
    public static $module = 'job';

    public static function index($route = null)
    {
        $model = Job::with('categories')->orderBy('title', 'desc');
        $categories = new Categories;
        $breadcrumbs = breadcrumbs('job');
        
        if ($route) {
            $cat = $categories->getCategoryBySlug($route);
            $url = 'category';
            if ($cat) {
                $model->where('category', $cat);
                array_push($breadcrumbs, ['name' => $categories->getCategory($cat)->get('name')]);
                $category = $categories->getCategory($cat);
            } else {
                return redirect()->route('job.index');
            }
        } else {
            $url = 'index';
        }
        
        return view('job::pages.' . $url, [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories->getModuleCategories('job'),
            'category' => $category ?? false,
            'items' => $model->paginate(config('job-config.paginate'))
        ]);
    }

    public static function subscribe()
    {
        $title = "Подписка на вакансии";
        $categories = new Categories;
        $crumbs  = [
            ['name' => $title, 'url' => url('job/subscribe')],
        ];
        $breadcrumbs = breadcrumbs('job', $crumbs);

        return view('job::pages.subscribe', [
            'title' =>  $title,
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories->getModuleCategories('job'),
        ]);
    }

    // Не делалось - Отправка
    public static function actionSubscribe()
    {
        $error_output = '';
        $success_output = '';
        $v = new Validator;
        $vv = $v->make([
            'g-recaptcha-response'  => 'required|recaptcha',
            'email'                 => 'required|email',

        ]);
        $vv->validate();
        if ($vv->fails()) {
            $type = 'error';
            $errors = $vv->errors();
            $error_output = $errors->firstOfAll();
        } else {
            $type = 'success';
            $success_output = 'Ваше сообщение успешно отправлено';
        }

        $output = array(
            'type'        =>    $type,
            'error'     =>  $error_output,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    public static function send()
    {
        $title = "Отправить резюме";
        $categories = new Categories;
        $crumbs  = [
            ['name' => $title, 'url' => url('job/send')],
        ];
        $breadcrumbs = breadcrumbs('job', $crumbs);
        return view('job::pages.send', [
            'title' =>  $title,
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories->getModuleCategories('job'),
        ]);
    }

    // Не делалось - Отправка
    public static function actionSend()
    {
        // dd($_POST, $_FILES);
        $error_output = '';
        $success_output = '';
        $type = 'success';
        if (!empty($_POST['name']) && !empty($_POST['phone']) && !empty($_POST['email']) && !empty($_POST['category']) && !empty($_POST['position'])) {
            $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
            $recaptcha_secret = '6LfddtUZAAAAAOJmTgbOOIec5WaFIOnIrOqBFk7I';
            $recaptcha_response = $_POST['recaptcha_response'];
            $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
            $recaptcha = json_decode($recaptcha);
            $dataArray = array();
            $dataArray['type'] = 'job';
            $dataArray['status'] = '1';
            $dataArray['email'] = $_POST['email'];
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
                    $order = Order::create($dataArray);
                    $request = [];
                    $request['category'] = $_POST['category'] == 'other' ? 0 : $_POST['category'];
                    $request['position'] = $_POST['position'];
                    $request['order_id'] =  $order->id;
                    $request['summary_url'] = $_POST['summary_url'] ?? '';
                    // if(!empty($_FILES['summary']))
                    // if($foo = new Upload($_FILES['summary']))
                    // {
                    // 	$foo->file_new_name_body = 'av' .$core->auth->user_id;
                    // 	$foo->image_resize = true;
                    // 	$foo->image_x = $user['avatar_width'];
                    // 	$foo->image_ratio_y = true;
                    // 	$foo->file_overwrite = true;
                    // 	$foo->file_auto_rename = false;
                    // 	$foo->Process(ROOT.'files/job/users/');
                    // 	$foo->allowed = array("image/*");

                    // 	if ($foo->processed) 
                    // 	{
                    // 		$foo->Clean();
                    // 	}
                    // }
                    OrderJob::create($request);


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
            'type'        =>    $type,
            'error'     =>  $error_output,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    public static function show($categories = null, $post = null)
    {
        $item = Job::with('categories')
            ->where('slug', $post)
            ->get()
            ->last();

        $cat = new Categories;

        if ($cat->getCategory($item->category)->get('altname') !== $categories) {
            return redirect()->route('job.index');
        }

        $title = $item->title;
        $titleCategory = $cat->getCategory($item->category)->get('name');

        $crumbs  = [
            ['name' => $titleCategory, 'url' => urldecode(url(static::$module, $cat->getCategoryFullUrl($item->category)))],
            ['name' => $title, 'url' => $cat->getCategoryFullUrl($item->category)],
        ];
        $breadcrumbs = breadcrumbs('job', $crumbs);

        $responsible = Users::find($item->responsible);

        return view('job::pages.view', [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $cat->getModuleCategories(static::$module),
            'item' => $item,
            'responsible' => $responsible,
            'send_cat' => $item->category,
            'send_position' => $item->title,
        ]);

    }
}
