<?php 

//подключаем основные константы 
$rootdir = str_replace("usr\other\daemon", "", __DIR__);
$rootdir = str_replace("usr/other/daemon", "",$rootdir);

require $rootdir.'define.php';

//маркер чистой загрузки без роутинга и шаблонизатора
$coreOnly = true;
//подключаем и инициализируем загрузчик
require ROOT . 'lib/loader.php';