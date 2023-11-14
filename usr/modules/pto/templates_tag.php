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


/*
	$preview_array = json_decode($news['preview'], true);
	$core->tpl->setVar('TITLE', $news['title']);
	$core->tpl->setVar('CATEGORY', (!empty($cat) ? $cat : 'Без категории'));
	$core->tpl->setVar('CAT_ONE', $cat_one);
	$core->tpl->setVar('ALTNAME', $news['altname']);
	$core->tpl->setVar('TEXT', $news['text']);
	$core->tpl->setVar('NUMB', $news['numb']);
	$core->tpl->setVar('ORG_NAME', $news['org_name']);
	$core->tpl->setVar('DATE_START', date('d.m.Y', strtotime($news['date_start'])));
	$date_end = date('d.m.Y', strtotime($news['date_end']));
	
	$core->tpl->setVar('DATE_END', ($date_end == '01.01.1970') ? 'бессрочный' :  $date_end);
	
	
	$core->tpl->setVar('ICON', isset($catInfo['icon']) ? $core->getCatImg($news_link, $catInfo['icon'], $catInfo['title']) : '');

	$core->tpl->setVar('PREVIEW', $preview_array['full_webp']);
	$core->tpl->setVar('PREVIEW_FULL', $preview_array['full']);
	$core->tpl->setVar('PREVIEW_THUMB_WEBP', $preview_array['thumb_webp']);
	$core->tpl->setVar('PREVIEW_WIDTH', (($preview_array['size']['orientation'] == '0') ? '214' : '149'));
	$core->tpl->setVar('PDF', $news['pdf']);
	$core->tpl->setVar('FULL_LINK', $news_link . $news['altname'] . ".html");	
	
	$core->tpl->sources = if_sets("#\\[preview\\](.*?)\\[/preview\\]#is", $core->tpl->sources, $news['preview']);
	$core->tpl->sources = if_sets("#\\[category\\](.*?)\\[/category\\]#is", $core->tpl->sources, $cat);
	$core->tpl->sources = if_sets("#\\[mini_img\\](.*?)\\[/mini_img\\]#is", $core->tpl->sources, (!empty($miniImg[0]) ? true : ''));
	$altname = $news["altname"];
	$core->tpl->sources = preg_replace_callback("#\\[more\\](.*?)\\[/more\\]#is",  
		function($match) use ($news_link, $altname)
		{
			return format_link($match[1], $news_link . $altname . '.html');
		},$core->tpl->sources);
		
	$news_date = $news['date'];
	$core->tpl->sources = preg_replace_callback("#\\{%MYDATE:(.*?)%\\}#is",  
		function($match) use ($news_date)
		{
			return date($match[1], strtotime($news_date));
		},$core->tpl->sources);	
		
	$news_title = $news['title'];
	$core->tpl->sources = preg_replace_callback("#\\{%TITLE:(.*?)%\\}#is",  
		function($match) use ($news_title)
		{
			return short($match[1], $news_title);
		},$core->tpl->sources);	
		
	$text = $news['text'];
	$core->tpl->sources = preg_replace_callback("#\\{%TEXT:(.*?)%\\}#is",  
		function($match) use ($text)
		{
			return short($match[1], stripslashes(processText($text)));
		},$core->tpl->sources);	
	
	$array_replace = array(		
		"#\\[edit\\](.*?)\\[/edit\\]#is" => (($core->auth->isModer||$core->auth->isAdmin)  ? "\${1}" : ''),		
		"#\\[img:([0-9]*?)\\]#is" => (!empty($miniImg[0]) ? '<img src="' . $miniImg[0] . '" border="0" width="\\1" />' : ''),	
	);
	

	

		
	if($cert_conf['showBreadcumb'] == '1')
	{
		$catId = explode(',', $news['category']);
	//	$core->tpl->setVar('BREADCUMB', $core->getCat('certificates', ($catId[1] != 0) ? $catId[1] : '', 'breadcrumb', 1));
	}	
	
	$core->tpl->sources = preg_replace(array_keys($array_replace), array_values($array_replace), $core->tpl->sources);
	$core->tpl->sources = preg_replace("#\\{%IMG:(.*?):(.*?)%\\}#is", (!empty($miniImg[(int)"\${1}"]) ? $miniImg[(int)"\${1}"] : "\${2}") , $core->tpl->sources);
	$core->tpl->sources = preg_replace("#\\{%IMG:(.*?)%\\}#is",  (!empty($miniImg[(int)"\${1}"]) ? $miniImg[(int)"\${1}"] : ''), $core->tpl->sources);
	$core->tpl->setVar('DATE', formatDate(strtotime($news['date'])));
	$core->tpl->setVar('ID', $news['id']);
	$core->tpl->setVar('EDIT', ($core->auth->isModer||$core->auth->isAdmin)  ? '<a href="certificates/edit/'.$news['id'].'">'._EDIT.'</a>' : '');
	

	
	$core->tpl->sources = preg_replace(array_keys($array_replace), array_values($array_replace), $core->tpl->sources);*/