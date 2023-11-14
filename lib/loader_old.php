<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
*/

//подключаем автоматический загрузчик классов


use \Boot\Builder;
use \Boot\System\Http\Response;
use \Boot\System\Cookies\Cookie;
use \Boot\Support\Facades\Auth;
use \Boot\Support\Facades\Url;
use \Boot\Support\Facades\Router;
use \Boot\Services\Translation;
use \Boot\Support\Facades\Config;
use \Boot\App\Application;
use \Boot\Contracts\Http\Kernel;
use \Boot\Contracts\Debug\ExceptionHandler;
use \Boot\Contracts\Console\Kernel as ConsoleKernel;
use \Boot\System\Http\Request;

//инициализируем сервисы (службы)
// Builder::getInstance()->registerServices();


//новые модули

$module = ['news', 'support', 'feedback', 'job', 'organizations', 'scan', 'portfolio', 'order', 'service', 'cashbox', 'reviews', 'project', 'promotions', 'certificates', 'bank', 'home', 'partner', 'control', 'scada', 'victoria', 'task2', 'selection', 'sales2', 'dev', 'warranty_card', 'users', 'chat', 'away', 'org', 'calculation', 'rept', 'catalog', 'price2'];


$old = true;

if (isset($new) && $new) {
    $old = false;
}



// require_once ROOT . 'boot/capsule.php';

//подключение функций
require_once ROOT . 'lib/php_funcs.php';
require_once ROOT . 'lib/global.php';
if (file_exists(ROOT . 'usr/other/other.functions.php')) {
    require_once ROOT . 'usr/other/other.functions.php';
}

require_once ROOT . 'etc/' . ROUTE_LINK . 'global.config.php';

//установка часового пояса
// if (Config::has('global.timezone')) {
//     date_default_timezone_set(Config::get('global.timezone'));
// }

//установка GZIP
if ($config['gzip'] && !DEBUG) {
    ob_start("ob_gzhandler");
}

// $response = new Response(
//     '',
//     Response::HTTP_OK,
//     ['content-type' => 'text/html']
// );

// $response->headers->setCookie(Cookie::create('foo3', 'bar'));
// $response->send();

//проверка на API
if (isset($api) && $api) {
    $old = false;
    $api = true;
} else {
    $api = false;
}

$app = new Application(
    ROOT ?? dirname(__DIR__)
);

$app->singleton(
    Kernel::class,
    Boot\App\Http\Kernel::class
);

$app->singleton(
    ConsoleKernel::class,
    Boot\App\Console\Kernel::class
);

$app->singleton(
    ExceptionHandler::class,
    Boot\App\Exceptions\Handler::class
);

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

if ($old) {
    //подключение старого ядра    
    require_once ROOT . 'lib/loader_core.php';
} else {
    require_once ROOT . 'boot/db/' . $config['dbType'] . '.db.php';
    require_once ROOT . 'boot/db' . (($config['dbCache'] == 1) ? '_cache' : '') . '.class.php';
  
  
    
   


    if (!isset($coreOnly) || $coreOnly != true) {
        Router::run();
    }
}