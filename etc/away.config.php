<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


global $away_conf;
$away_conf = array();
$away_conf['keywords'] = "histore, история, история изменений, версия, JMY CMS,  roadmap";
$away_conf['description'] = "История версий JMY CMS с описаний изменений, от первой до текущей версии системы.";
$away_conf['redirect_info'] = "0";
$away_conf['redirect_time'] = "5";

