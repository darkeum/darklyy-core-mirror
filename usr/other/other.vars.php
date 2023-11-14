<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @info_ru     Место под ваши собственные функции для шаблонов
* @info_en     Place for your own functions for templates
*/

global $core, $db, $cats;



$search_cat = '';
if(empty($core->catArray))
{
	$core->catArray = getcache('categories');
}
$parse = isset($core->catArray['catalog_v2']) ? $core->catArray['catalog_v2'] : null;
$parse_old = isset($core->catArray['catalog']) ? $core->catArray['catalog'] : null; 
if (!empty($parse)){
	foreach($parse as $info)
	{
		if (($info['parent'] == '0')&&($info['hidden']== '0'))
		{
			$search_cat .= '<option value="'.$info['altname'].'" >'.$info['title'].'</option>';
		}
		//echo $info['hidden'];
	}
	
}

$date = date("d/m/Y"); 
$date_file = date("dmY"); 
$file_cache = ROOT.'/tmp/cache/menu_'.$date_file.'.cache';
if (!file_exists($file_cache))
{
	$menu = get_menu_main($parse, 0, 0, 9, true);	
	$files = glob(ROOT.'/tmp/cache/menu_*.cache');
	foreach($files as $file)
	{ 
		if(is_file($file))
		unlink($file); 
	}		
	file_put_contents($file_cache, $menu);		
}	
else
{
	$menu = file_get_contents($file_cache);		
}
$file_cache_all = ROOT.'/tmp/cache/menu_all_'.$date_file.'.cache';
if (!file_exists($file_cache_all))
{
	$menu_all = get_menu_main($parse, 0, 0);	
	$files = glob(ROOT.'/tmp/cache/menu_all_*.cache');
	foreach($files as $file)
	{ 
		if(is_file($file))
		unlink($file); 
	}		
	file_put_contents($file_cache_all, $menu_all);		
}	
else
{
	$menu_all = file_get_contents($file_cache_all);		
}




	//
	$this->setVar('MAIN_MENU',  $menu);
	$this->setVar('MAIN_MENU_ALL',  $menu_all);
	$this->setVar('CHOOSE_ORG',  chooseOrg());
	$this->setVar('CHOOSE_OBJECT',  chooseObject());
	//$this->setVar('MENU_V2',  get_menu($parse, 0));
	
	
	$this->setVar('SEARCH_CAT',  $search_cat);
//	$this->setVar('MENU',  get_tree_main($parse_old, 0));
//	$this->setVar('MENU_MAIN',  get_tree_main($parse_old, 0, 0, 9));

	$this->setVar('UNAME',  isset($core->auth->user_info['name']) ? $core->auth->user_info['name'] : '');
	$this->setVar('USNAME',  isset($core->auth->user_info['surname']) ? $core->auth->user_info['surname'] : '');
	
//	$this->setVar('CATEGORY_MAIN',  get_category_main(8));
	$this->setVar('CATEGORY_MAIN_HOME',  get_category_main_new(8));
	$this->setVar('OPEN_GRAPH',  '<meta name="og:title" content="'.$core->tpl->title_now.'">
		<meta name="og:description" content="'.$core->tpl->description.'">
		<meta name="og:url" content="https://atri-energo.ru/">
		<meta name="og:site_name" content="ООО «АТРИ-ЭНЕРГО»">
		<meta name="og:locale" content="ru_RU">
		<meta property="og:image" content="'.$core->tpl->preview.'">
		<meta name="og:type" content="website">');
	$this->setVar('SCHEMA',  '');