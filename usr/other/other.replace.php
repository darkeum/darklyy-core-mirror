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
$this->sources = preg_replace_callback( "#\\[order:(.+?):(.+?)](.*?)\\[/order]#is", "orderShow", $this->sources);
$this->sources = preg_replace_callback( "#\\[knowledge:(.+?):(.*?)]#is", "getKnowledgeTooltip", $this->sources);
$this->sources = preg_replace_callback( "#\\[attach:(.+?)]#is", "getAttach", $this->sources);
// $this->sources = preg_replace_callback( "#\\[favorite:(.+?)](.*?)\\[/favorite]#is", "getFavoriteButton", $this->sources);
$this->sources = preg_replace_callback( "#\\[catalog_cats:(.+?)](.*?)\\[/catalog_cats]#is", "catalogCatsShow", $this->sources);
$this->sources = preg_replace_callback( "#\\[catalog_cats_open:(.+?)](.*?)\\[/catalog_cats_open]#is", "catalogCatsOpenShow", $this->sources);
$this->sources = preg_replace_callback( "#\\[enqueue:(.+?):(.+?)\\]#is", "enqueueFile", $this->sources);
$this->sources = preg_replace_callback( "#\\[menu_active:(.*?):(.+?)](.*?)\\[/menu_active]#is", function($matches) use ($url)
		{
			$catArray = explode(',', $matches[1]);
			$tag = $matches[2]; 
			$content = $matches[3];	
			if(empty($url[1]))
			{	
				$url[1] = 'index';
			}
			if(!empty(array_intersect($url, $catArray))) 
			{
				if($tag == 1) return stripslashes($content);
			}
			else
			{
				if($tag == 0) return stripslashes($content);
			}
		},$this->sources);	
		
$showMenu = $this->showMenu;

$this->sources = preg_replace_callback( "#\\[showMenu](.*?)\\[/showMenu]#is", function($matches) use ($showMenu)
		{
			$content = $matches[1];	
			if($showMenu) 
			{
				return stripslashes($content);
			}
		},$this->sources);	
		
		
$openMenu = $this->openMenu;
$this->sources = preg_replace_callback( "#\\[openMenu](.*?)\\[/openMenu]#is", function($matches) use ($openMenu)
		{
			$content = $matches[1];	
			if($openMenu) 
			{
				return stripslashes($content);
			}
		},$this->sources);	
