<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2020 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
 
if (!defined('ACCESS')) {
    header('Location: /');
    exit;
}
require (ROOT.'etc/content.config.php');

function dotsReplase($value)
{
	 return (!empty($value) ? str_replace('.', ',', $value) : 0);
}

global $lang;

switch(isset($url[1]) ? $url[1] : null) 
{
	default:
		
	break;
	
	case "view":
		require_once ROOT . '/vendor/autoload.php';
		require_once(ROOT.'usr/modules/request/admin/sub/metering.php');	
        $id = $url[2];
        $metering = new metering($id);
        $metering->init();
		//$metering->showAllParametr();
        $metering->printOut();
		echo '<pre>';
		print_r( $metering->getSpecificationTM());		
	exit;	

	
	case "metering_generate":
		require_once ROOT . '/vendor/autoload.php';
		require_once(ROOT.'usr/modules/request/admin/sub/metering.php');	
        $id = $url[2];
        $metering = new metering($id);
        $metering->init();
		//$metering->showAllParametr();
        $metering->printOut();
		//echo '<pre>';
		//print_r( $metering->getSpecificationTM());		
	exit;	
		
    break;
	case "metering_generate_scheme":
		require_once ROOT . '/vendor/autoload.php';
		require_once(ROOT.'usr/modules/request/admin/sub/metering.php');	
        $id = intval($url[2]);
		$type = intval($url[3]);
        $metering = new metering($id);
        $metering->init();
        $metering->getScheme($type);
		exit;
    break;
	case "metering_generate_debug":
		require_once ROOT . '/vendor/autoload.php';
		require_once(ROOT.'usr/modules/request/admin/sub/metering.php');	
        $id = $url[2];
        $metering = new metering($id);
        $metering->init();
		$metering->showAllParametr();
        //$metering->printOut();
		echo '<pre>';
		print_r( $metering->getSpecificationTM());	
		exit;
	break;
	case "last":
		$query = $db->query("SELECT * FROM JMY_doc ORDER BY id DESC LIMIT 1");
		$r = $db->getRow($query);
		
		location(config('app.url').'/administration/module/doc/print/doc/'.$r['id']);
		exit;
	break;	
	case "success":
	if(isset($url[2], $url[3])) 
	{
		$id = $url[2];
		$secret_key = $url[3];
		$success = true;
		confirm($id, $secret_key, $success);
	}
	else
	{
		location('/');
	}
break;

}