<?php

use \Boot\App\Application;
use \Boot\Contracts\Http\Kernel;
use \Boot\Contracts\Debug\ExceptionHandler;
use \Boot\Contracts\Console\Kernel as ConsoleKernel;

$reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
// Путь к папке vendor/composer, поднимаемся на 2 уровня вверх до корня проекта
$rootPath = dirname($reflection->getFileName(), 3);

require_once $rootPath . '/define.php';

// define('TIMER', microtime(1));
// define('ROOT', dirname(__FILE__) . '/');

// dd(dirname(__DIR__));
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

return $app;