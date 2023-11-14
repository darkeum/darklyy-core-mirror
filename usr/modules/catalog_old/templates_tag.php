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
	$price = json_decode($catalog['price'], true);

	if ($price["1bb285e9-a8f3-11e7-a442-704d7b330583"]['value'] != 0)
	{
		$price_is = number_format($price["1bb285e9-a8f3-11e7-a442-704d7b330583"]['value'], 0, '.', ' ').' РУБ.';
		$price_rub = $price["1bb285e9-a8f3-11e7-a442-704d7b330583"]['value'];
	}
	else if ($price["ee22d123-37ac-11e8-a463-704d7b330583"]['value'] != 0)
	{
		$content_cbrf = get_cbrf(); 
		$pattern_cbrf = "#<Valute ID=\"([^\"]+)[^>]+>[^>]+>([^<]+)[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)[^>]+>[^>]+>([^<]+)#i"; 
		preg_match_all($pattern_cbrf, $content_cbrf, $out, PREG_SET_ORDER); 
		$dollar = ""; 
		$euro = ""; 
		foreach($out as $cur) 
		{ 
			if($cur[2] == 840) $dollar = str_replace(",",".",$cur[4]); 
			if($cur[2] == 978) $euro   = str_replace(",",".",$cur[4]); 
		} 	  
		
		$price_is =  number_format($euro*$price["ee22d123-37ac-11e8-a463-704d7b330583"]['value'],2,'.',' ').' РУБ.';;
		$price_rub = number_format($euro*$price["ee22d123-37ac-11e8-a463-704d7b330583"]['value'],2,'.','');
	}
	else
	{
		$price_is = 'по запросу';
		$price_rub = 0;
	}
	if(!empty($catalog['manufacture_name']))
	{
		$man = $catalog['manufacture_name'];
	}
	else
	{
		$man = 'по запросу';
	}
	$sku = !empty($catalog['sku']) ? $catalog['sku'] : 'по запросу';	
	if(!empty($catalog['quantity']))
	{
		$quantity = 'в наличии';
	}
Else
{
	$quantity = 'под заказ';
}	


	
	
	$core->tpl->setVar('TITLE', stripcslashes($catalog['title']));
	$core->tpl->setVar('SKU', $sku);
	
	$core->tpl->setVar('ID', $catalog['id']);
	$core->tpl->setVar('UNIT', $catalog['unit_sname']);
	$core->tpl->setVar('MAN', $man);
	$core->tpl->setVar('STOCK', $quantity);
	$core->tpl->setVar('MANUFACTURE_IMG', '/files/manufacture/'.$catalog['manufacture_id'].'.jpg');
	$core->tpl->setVar('MANUFACTURE_URL', '/catalog/man/'.$catalog['manufacture_id_1c']);
	$core->tpl->setVar('PRICE', $price_is);
	$core->tpl->setVar('PRICE_RUB', $price_rub);
	$core->tpl->setVar('SHORT', $short);
	$core->tpl->setVar('FULL', '<div id="full-' . $catalog['id'] . '">' . $core->bbDecode($catalog['full'], $catalog['id'], true) . '</div>');
	$core->tpl->setVar('CATEGORY', $cat);
	$core->tpl->setVar('CAT_ONE', $cat_one);
	$core->tpl->setVar('ALTNAME', $catalog['altname']);
	$core->tpl->setVar('ICON', isset($catInfo['icon']) ? $core->getCatImg($catalog_link, $catInfo['icon'], $catInfo['title']) : '');
	$core->tpl->setVar('AUTHOR', '<a href="profile/' . $catalog['author'] . '" title="' . _PAGE . ': ' . $catalog['author'] . '">' . $catalog['author'] . '</a>');
	$core->tpl->setVar('VIEWS', $catalog['views']);
	$core->tpl->setVar('PREVIEW', (empty($catalog['preview']) ? '/files/no_photo.jpg' : $catalog['preview']));
	$core->tpl->setVar('COMMENTS', $catalog['comments']);
	$core->tpl->setVar('TAGS', mb_substr($tags, 0, -2));
	$core->tpl->setVar('FULL_LINK', $catalog_link . $catalog['altname'] . ".html");	
	$miniImg = _getCustomImg($short);
	$core->tpl->sources = if_sets("#\\[tags\\](.*?)\\[/tags\\]#is", $core->tpl->sources, $catalog['tags']);
	$core->tpl->sources = if_sets("#\\[preview\\](.*?)\\[/preview\\]#is", $core->tpl->sources, $catalog['preview']);
	$core->tpl->sources = if_sets("#\\[category\\](.*?)\\[/category\\]#is", $core->tpl->sources, $cat);
	$core->tpl->sources = if_sets("#\\[mini_img\\](.*?)\\[/mini_img\\]#is", $core->tpl->sources, (!empty($miniImg[0]) ? true : ''));
	$altname = $catalog["altname"];
	$core->tpl->sources = preg_replace_callback("#\\[more\\](.*?)\\[/more\\]#is",  
		function($match) use ($catalog_link, $altname)
		{
			return format_link($match[1], $catalog_link . $altname . '.html');
		},$core->tpl->sources);
		
	$catalog_date = $catalog['date'];
	$core->tpl->sources = preg_replace_callback("#\\{%MYDATE:(.*?)%\\}#is",  
		function($match) use ($catalog_date)
		{
			return date($match[1], $catalog_date);
		},$core->tpl->sources);	
		
	$catalog_title = $catalog['title'];
	$core->tpl->sources = preg_replace_callback("#\\{%TITLE:(.*?)%\\}#is",  
		function($match) use ($catalog_title)
		{
			return short($match[1], $catalog_title);
		},$core->tpl->sources);	
		
	$core->tpl->sources = preg_replace_callback("#\\{%SHORT:(.*?)%\\}#is",  
		function($match) use ($short)
		{
			// return short($match[1], processText($short));
			return short($match[1], $short); // Убираем \ перед кавычками
		},$core->tpl->sources);	
	
	$array_replace = array(		
		"#\\[edit\\](.*?)\\[/edit\\]#is" => (($core->auth->isModer||$core->auth->isAdmin)  ? "\${1}" : ''),		
		"#\\[img:([0-9]*?)\\]#is" => (!empty($miniImg[0]) ? '<img src="' . $miniImg[0] . '" border="0" width="\\1" />' : ''),	
	);
	
	if(!empty($catalog['fields']) && $catalog['fields'] != 'N;')
	{
		$fields = unserialize($catalog['fields']);
		foreach($fields as $xId => $xData)
		{
			if(!empty($xData[1]))
			{
		$array_replace["#\\[xfield_value:" . $xId . "\\]#is"] = $xData[1];
			}
		}
	}
	
	$catalog_fields = $catalog['fields'];
	$core->tpl->sources = preg_replace_callback("#\\[xfield:([0-9]*?)\\](.*?)\\[/xfield:([0-9]*?)\\]#is",  
		function($match) use ($catalog_fields)
		{
			return ifFields($catalog_fields, $match[1], $match[2]);
		},$core->tpl->sources);
		
	if(false)
	{
		$catId = explode(',', $catalog['cat']);
		$core->tpl->setVar('BREADCUMB', $core->getCat('catalog', ($catId[1] != 0) ? $catId[1] : '', 'breadcrumb', 1));
	}	
	
	$core->tpl->sources = preg_replace(array_keys($array_replace), array_values($array_replace), $core->tpl->sources);
	$core->tpl->sources = preg_replace("#\\{%IMG:(.*?):(.*?)%\\}#is", (!empty($miniImg[(int)"\${1}"]) ? $miniImg[(int)"\${1}"] : "\${2}") , $core->tpl->sources);
	$core->tpl->sources = preg_replace("#\\{%IMG:(.*?)%\\}#is",  (!empty($miniImg[(int)"\${1}"]) ? $miniImg[(int)"\${1}"] : ''), $core->tpl->sources);
	$core->tpl->setVar('DATE', formatDate($catalog['date']));
	$core->tpl->setVar('ID', $catalog['id']);
	$core->tpl->setVar('RATING', $catalog['allow_rating'] ? draw_rating($catalog['id'], 'catalog', $catalog['score'], $catalog['votes']) : '');
	$core->tpl->setVar('EDIT', ($core->auth->isModer||$core->auth->isAdmin)  ? '<a href="catalog/edit/'.$catalog['id'].'">'._EDIT.'</a>' : '');
	/*
	$related_cache = $cache->do_get('related_'.$catalog['id']);
	if(empty($related_cache) && 5 > 0)
	{
		$body_text = $catalog['title'] . strip_tags(stripslashes(" " . (!empty($catalog['full']) ? $catalog['full'] : $catalog['short'])));
		if(!empty($body_text))
		{
			$rel_query = $db->query("SELECT n.*, l.* FROM ".DB_PREFIX."_catalog AS n LEFT JOIN ".DB_PREFIX."_langs as l on(l.postId=n.id and l.module='catalog') WHERE MATCH (`title`, `short`, `full`) AGAINST ('+(" . $db->safesql($body_text) . ")' IN BOOLEAN MODE) AND n.id != " . $catalog['id'] . " LIMIT 5", true);
			$related_cache = '';
			if($db->numRows($rel_query) > 0)
			{
				while($related = $db->getRow($rel_query)) 
				{
			$rel_link = $related['cat'] !== ',0,' ? 'catalog/' . $core->getCat('catalog', $related['cat'], 'development') . '/' : 'catalog/';
			$related_cache .= '<li><a href="'.$rel_link . $related['altname'] . '.html">'.$related['title'].' (' . formatdate($related['date']) . ')</a></li>';
				}
			}
			
			$cache->do_put('related_'.$catalog['id'], $related_cache, 3600);
		}
	}
	$core->tpl->setVar('RELATED', $related_cache);
	$array_replace["#\\[related\\](.*?)\\[/related\\]#is"] = (!empty($related_cache) ? '\\1' : '');
	*/
	$core->tpl->sources = preg_replace(array_keys($array_replace), array_values($array_replace), $core->tpl->sources);