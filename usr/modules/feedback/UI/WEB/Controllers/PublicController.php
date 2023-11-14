<?php 

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description Feedback public controller
*/

namespace Feedback\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;
use \Boot\System\Categories\Categories;

class PublicController extends AbstractController{

    public static function index($route = null){   
        
        $crumbs  = [
            ['name' => 'Контакты', 'url' => route('feedback.index')],
        ];

        $breadcrumbs = breadcrumbs('feedback', $crumbs);

        return view('feedback::page', [
            'title' => 'Контакты',
            'breadcrumbs' =>  $breadcrumbs,
            'categories' =>  gen_cats('about, news, revs, docs, certs_serv, objects, job, partners, contacts')
        ]);

    }
}