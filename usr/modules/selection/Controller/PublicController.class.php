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

namespace Selection\Controller;

use \Boot\Controller\AbstractController;
use \Certificates\Model\Certificates;
use \Boot\System\Categories\Categories;
use \Boot\Support\Facades\Router;

class PublicController extends AbstractController{

    public static $module = 'selection';

    public static function construct($route = null){   
        
header("Access-Control-Allow-Origin: *");
// header("X-Frame-Options: SAMEORIGIN");
        define('ROOT_CONTRUCTOR', ROOT.'/usr/modules/selection/Construct');
        require_once(ROOT_CONTRUCTOR.'/index.php');
        echo <<<HTML
            <script>
                window.onload = function(){
                    document.domain = "atri-energo.ru";
                }
            </script>
HTML;
        exit;
    }

    public static function blockConstruct($route = null){   
        
        define('ROOT_CONTRUCTOR', ROOT.'/usr/modules/selection/blockConstruct');
        require_once(ROOT_CONTRUCTOR.'/index.php');
        exit;
    }
    

    public static function scheme($route = null){   
        
        header("Access-Control-Allow-Origin: *");
        echo '<img width=100% height=100% src="https://atri-energo.ru/usr/tpl/dev2/assest/images/1/8255-2.png" >';;
    }   

}