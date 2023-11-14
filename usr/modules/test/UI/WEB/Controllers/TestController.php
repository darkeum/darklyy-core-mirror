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

namespace Test\UI\WEB\Controllers;

use \Boot\Models\Users;
// use \Boot\Models\Users;
use \Boot\Services\Request;
use \Users\UI\WEB\Grids\Index;
use Illuminate\Support\Facades\Mail;
use \Boot\Support\Facades\Translation;
use \Boot\System\Categories\Categories;
use \Boot\Controller\AbstractController;


class TestController extends AbstractController
{
    

    public static function boiler_add()
    {
      \Firebase\JWT\JWT::encode($payload, 'eJLttiTlhKRbbZDDwSu3WWfvdLQM5Ttp');
    }

   
}
