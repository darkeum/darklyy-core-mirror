<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
 
if (!defined('ACCESS')) {
    header('Location: /');
    exit;
}
require (ROOT.'etc/content.config.php');

function view($secret_key)
{
global $db, $config, $core, $url, $headTag, $content_conf, $lang;
	if($secret_key)
	{
		$query = $db->query("SELECT * FROM " . DB_PREFIX . "_ver WHERE secret_key='" . $db->safesql($secret_key) . "' LIMIT 1");
		$static = $db->getRow($query);
		if($static)
		{	
			if ($static['date_end'] == 0)
			{
				$st = 'выполняется!';
			}	
			else
			{
				$st = 'готов!';
			}
			$text = 'Ваш заказ от '.date('d.m.Y', $static['date']).' '.$st;			
			$ptitle = 'Наряд-заказ №ПР-'.$static['id'];			
			set_title(array($ptitle));					
			$core->tpl->loadFile('verification/verification.view');
			$core->tpl->setVar('TITLE', $ptitle);
			$core->tpl->setVar('TEXT', $text);			
			$core->tpl->end();				
		}
		else
		{
			//	include(ROOT . 'usr/tpl/404.tpl');
		}
	}
	else
	{
		include(ROOT . 'usr/tpl/404.tpl');
	}
}
 
global $lang;
switch(isset($url[1]) ? $url[1] : null) 
{
	default:
		
	break;
	case "view":
		if(isset($url[2])) 
		{
			$secret_key = $url[2];
			view($secret_key);
		}
		else
		{
			location('/');
		}
	break;	
		

}