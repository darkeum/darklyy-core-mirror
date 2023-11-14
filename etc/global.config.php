<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


global $config;
$config = array();
$config['url'] = env('APP_URL', '');
$config['name'] = env('APP_NAME', '');
$config['description'] = "Основная деятельность ООО «Атри-Энерго» связана с поставкой, монтажом и обслуживанием теплообменного оборудования.";
$config['slogan'] = "Энергия в кубе";
$config['keywords'] = "ключевые, слова, сайта";
$config['divider'] = " - ";
$config['charset'] = "utf-8";
$config['mainModule'] = "feed";
$config['lang'] = "ru";
$config['uniqKey'] = "xeznwgup";
$config['timezone'] = "Asia/Baku";

$config['tpl_change'] = "0";
$config['smartphone'] = "0";
$config['dbType'] = "mysqli";
$config['imageEffect'] = "shadowbox";
$config['support_mail'] = "atri-energo@yandex.ru";
$config['gzip'] = "1";
$config['off'] = "0";
$config['off_text'] = "Сайт закрыт.<br /> Ведутся профилактические работы.";
$config['cache'] = "1";
$config['dbCache'] = "0";
$config['mod_rewrite'] = "1";
$config['comments'] = "1";
$config['plugin'] = "0";
$config['tpl'] = 'dev2';
