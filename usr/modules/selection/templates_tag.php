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
	$core->tpl->setVar('TITLE', $partner['title']);
	$core->tpl->setVar('SHORT', stripslashes($short));
	$full = (empty($partner['full']) ? $short : $partner['full']);
	$core->tpl->setVar('FULL', '<div id="full-' . $partner['id'] . '">' . stripslashes($core->bbDecode($full, $partner['id'], true)). '</div>');
	$core->tpl->setVar('CATEGORY', $cat);
	$core->tpl->setVar('CAT_ONE', $cat_one);
	$core->tpl->setVar('ALTNAME', $partner['altname']);
	$core->tpl->setVar('ICON', isset($catInfo['icon']) ? $core->getCatImg($partner_link, $catInfo['icon'], $catInfo['title']) : '');
	$core->tpl->setVar('AUTHOR', '<a href="profile/' . $partner['author'] . '" title="' . _PAGE . ': ' . $partner['author'] . '">' . $partner['author'] . '</a>');
	$core->tpl->setVar('VIEWS', $partner['views']);
	$core->tpl->setVar('PREVIEW', $partner['preview']);
	$core->tpl->setVar('COMMENTS', $partner['comments']);
	$core->tpl->setVar('TAGS', mb_substr($tags, 0, -2));
	if ($partner['page'])
	{
		$ch_link =  $partner_link . $partner['altname'] . ".html";
		$target = '_self';
	}
	elseif (!empty($partner['site']))
	{
		$ch_link = $partner['site'];
		$target = '_blank';
	}
	else
	{
		$ch_link = '#';
		$target = '_self';
	}
	$site = (empty($partner['site']) ? false : $partner['site']);
	$core->tpl->setVar('CHANGEABLE_LINK', $ch_link);	
	$core->tpl->setVar('TARGET', $target);	
	$core->tpl->setVar('SITE', $partner['site']);	
	$core->tpl->setVar('FULL_LINK', $partner_link . $partner['altname'] . ".html");	
	$miniImg = _getCustomImg($short);
	$core->tpl->sources = if_sets("#\\[tags\\](.*?)\\[/tags\\]#is", $core->tpl->sources, $partner['tags']);
	$core->tpl->sources = if_sets("#\\[preview\\](.*?)\\[/preview\\]#is", $core->tpl->sources, $partner['preview']);
	$core->tpl->sources = if_sets("#\\[category\\](.*?)\\[/category\\]#is", $core->tpl->sources, $cat);
	$core->tpl->sources = if_sets("#\\[mini_img\\](.*?)\\[/mini_img\\]#is", $core->tpl->sources, (!empty($miniImg[0]) ? true : ''));
	$altname = $partner["altname"];
	$ppage = $partner['page'];
	$core->tpl->sources = preg_replace_callback("#\\[more\\](.*?)\\[/more\\]#is",  
		function($match) use ($partner_link, $altname, $ppage)
		{
			if ($ppage)
			{
				return format_link($match[1], $partner_link . $altname . '.html');
			}
			else
			{
				return $match[1];
			}
		},$core->tpl->sources);
	$core->tpl->sources = preg_replace_callback("#\\[more_link\\](.*?)\\[/more_link\\]#is",  
		function($match) use ($partner_link, $altname, $ppage)
		{
			if ($ppage)
			{
				return format_link($match[1], $partner_link . $altname . '.html');
			}
			else
			{
				return false;
			}
		},$core->tpl->sources);
	$core->tpl->sources = preg_replace_callback("#\\[site\\](.*?)\\[/site\\]#is",  
		function($match) use ($site)
		{
			if ($site)
			{
				return '<a href="'.$site.'" target="_blank">'.$match[1].'</a>';
			}
			else
			{
				return false;
			}
		},$core->tpl->sources);
		
	$partner_date = $partner['date'];
	$core->tpl->sources = preg_replace_callback("#\\{%MYDATE:(.*?)%\\}#is",  
		function($match) use ($partner_date)
		{
			return date($match[1], $partner_date);
		},$core->tpl->sources);	
		
	$partner_title = $partner['title'];
	$core->tpl->sources = preg_replace_callback("#\\{%TITLE:(.*?)%\\}#is",  
		function($match) use ($partner_title)
		{
			return short($match[1], $partner_title);
		},$core->tpl->sources);	
		
	$core->tpl->sources = preg_replace_callback("#\\{%SHORT:(.*?)%\\}#is",  
		function($match) use ($short)
		{
			return short($match[1], stripslashes(processText($short)));
		},$core->tpl->sources);	
	
	$array_replace = array(		
		"#\\[edit\\](.*?)\\[/edit\\]#is" => (($core->auth->isModer||$core->auth->isAdmin)  ? "\${1}" : ''),		
		"#\\[img:([0-9]*?)\\]#is" => (!empty($miniImg[0]) ? '<img src="' . $miniImg[0] . '" border="0" width="\\1" />' : ''),	
	);
	
	if(!empty($partner['fields']) && $partner['fields'] != 'N;')
	{
		$fields = unserialize($partner['fields']);
		foreach($fields as $xId => $xData)
		{
			if(!empty($xData[1]))
			{
		$array_replace["#\\[xfield_value:" . $xId . "\\]#is"] = $xData[1];
			}
		}
	}
	
	$partner_fields = $partner['fields'];
	$core->tpl->sources = preg_replace_callback("#\\[xfield:([0-9]*?)\\](.*?)\\[/xfield:([0-9]*?)\\]#is",  
		function($match) use ($partner_fields)
		{
			return ifFields($partner_fields, $match[1], $match[2]);
		},$core->tpl->sources);
		
	if($partner_conf['showBreadcumb'] == '1')
	{
		$catId = explode(',', $partner['cat']);
		$core->tpl->setVar('BREADCUMB', $core->getCat('partner', ($catId[1] != 0) ? $catId[1] : '', 'breadcrumb', 1));
	}	
	
	$core->tpl->sources = preg_replace(array_keys($array_replace), array_values($array_replace), $core->tpl->sources);
	$core->tpl->sources = preg_replace("#\\{%IMG:(.*?):(.*?)%\\}#is", (!empty($miniImg[(int)"\${1}"]) ? $miniImg[(int)"\${1}"] : "\${2}") , $core->tpl->sources);
	$core->tpl->sources = preg_replace("#\\{%IMG:(.*?)%\\}#is",  (!empty($miniImg[(int)"\${1}"]) ? $miniImg[(int)"\${1}"] : ''), $core->tpl->sources);
	$core->tpl->setVar('DATE', formatDate($partner['date']));
	$core->tpl->setVar('ID', $partner['id']);
	$core->tpl->setVar('RATING', $partner['allow_rating'] ? draw_rating($partner['id'], 'partner', $partner['score'], $partner['votes']) : '');
	$core->tpl->setVar('EDIT', ($core->auth->isModer||$core->auth->isAdmin)  ? '<a href="partner/edit/'.$partner['id'].'">'._EDIT.'</a>' : '');
	
	$related_cache = $cache->do_get('related_'.$partner['id']);
	if(empty($related_cache) && $partner_conf['related_partner'] > 0)
	{
		$body_text = $partner['title'] . strip_tags(stripslashes(" " . (!empty($partner['full']) ? $partner['full'] : $partner['short'])));
		if(!empty($body_text))
		{
			$rel_query = $db->query("SELECT n.*, l.* FROM ".DB_PREFIX."_partner AS n LEFT JOIN ".DB_PREFIX."_langs as l on(l.postId=n.id and l.module='partner') WHERE MATCH (`title`, `short`, `full`) AGAINST ('+(" . $db->safesql($body_text) . ")' IN BOOLEAN MODE) AND n.id != " . $partner['id'] . " LIMIT ".$partner_conf['related_partner'], true);
			$related_cache = '';
			if($db->numRows($rel_query) > 0)
			{
				while($related = $db->getRow($rel_query)) 
				{
			$rel_link = $related['cat'] !== ',0,' ? 'partner/' . $core->getCat('partner', $related['cat'], 'development') . '/' : 'partner/';
			$related_cache .= '<li><a href="'.$rel_link . $related['altname'] . '.html">'.$related['title'].' (' . formatdate($related['date']) . ')</a></li>';
				}
			}
			
			$cache->do_put('related_'.$partner['id'], $related_cache, 3600);
		}
	}
	$core->tpl->setVar('RELATED', $related_cache);
	$array_replace["#\\[related\\](.*?)\\[/related\\]#is"] = (!empty($related_cache) ? '\\1' : '');
	$core->tpl->sources = preg_replace(array_keys($array_replace), array_values($array_replace), $core->tpl->sources);