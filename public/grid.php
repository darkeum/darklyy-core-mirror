<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description File for grid load
*/

//подключаем основные константы 
require dirname(__FILE__) . '/../define.php';
//маркер чистой загрузки без роутинга и шаблонизатора
$coreOnly = true;
//подключаем и инициализируем загрузчик
require ROOT . 'lib/loader_old.php';
//подключаем систему grid
header('Access-Control-Allow-Origin: *');
$grid_name = $_GET['jqgrid'];
$grid_path = $_GET['patch'];
$grid_module = $_GET['module'];
$core->loadModLangADM($grid_module);
loadGrid($grid_name = 'jqSimple', '', array(), $grid_path);