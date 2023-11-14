<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @info_ru     Место под ваши собственные функции
* @info_en     Place for your own functions
*/


function getAttach($matches)
{
	global $core, $db;
	$id = intval($matches[1]);
	
	
	
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE id = '".$id."' AND access = '1' LIMIT 1");	
	if($db->numRows($query) > 0)
	{
		$rows = $db->getRow($query);
		$info = new SplFileInfo($rows['url']);
		$size = formatfilesize(@filesize($rows['url']), true);
		if(mb_strlen($size) > 5)
		{
			$size = mb_substr($size, 0, 5);
		}
		$name = $rows['name'];
		if (isset($matches[2]))
		{
			$name = $matches[2].'.'.$info->getExtension(); 
		}
		return stripslashes('<a href="/download/'.$id.'" target="_blank" class="ui-link document" title="'.$name.'" alt="'.$name.'">
													<i class="document__icon document__icon_'.$info->getExtension().'"></i>
													<div class="document__title document__title__cert">
														<div class="document__name">'.$name.'</div>
														<div class="document__size">'.$size.' (Скачиваний: '.$rows['downloads'].')</div>
													</div>
												</a>');  
		 
    }
	else
	{
		return '';
	}	
}
