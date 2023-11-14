<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Capsule loader
*/

use \Boot\Support\Facades\Config;
use Illuminate\Database\Capsule\Manager as Capsule;
// use \Boot\Services\Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

$capsule = new Capsule;
$connection = Config::get('database.connections');
foreach ($connection as $k => $v) {
    $name = Config::get('database.default') == $k ? "default" : $k;
    $capsule->addConnection([
        'driver'    => $v['driver'],
        'host'      => $v['host'],
        'port'      => $v['port'],
        'database'  => $v['database'],
        'username'  => $v['username'],
        'password'  => $v['password'],
        'charset'   => $v['charset'],
        'collation' => $v['collation'] ?? '',
        'prefix'    => $v['prefix'] ?? '',
    ], $name);
}

$capsule->setEventDispatcher(new Dispatcher(new Container));

$capsule->setAsGlobal();

$capsule->bootEloquent();
