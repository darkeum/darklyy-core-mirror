<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

use \Boot\App\Application;
use \Boot\Contracts\Http\Kernel;
use \Boot\Contracts\Debug\ExceptionHandler;
use \Boot\Contracts\Console\Kernel as ConsoleKernel;
use \Boot\System\Http\Request;

//подключение функций
require_once ROOT . 'lib/php_funcs.php';
require_once ROOT . 'lib/global.php';
if (file_exists(ROOT . 'usr/other/other.functions.php')) {
    require_once ROOT . 'usr/other/other.functions.php';
}

require_once ROOT . 'etc/' . ROUTE_LINK . 'global.config.php';

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

$kernel->terminate($request, $response);
