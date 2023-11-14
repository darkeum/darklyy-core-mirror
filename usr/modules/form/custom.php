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

	global $db, $config, $core, $tags, $partner_conf, $url, $headTag, $cache;
	$where = ' AND c.lang = \'' . $core->InitLang() . '\'';
	if($category != 'all')
	{
		$catsArr = array_map('trim', explode(',', $category));
		$i = 0;
		foreach($catsArr as $cat)
		{
			$i++;
			if($i == 1) $where .= " AND "; else $where .= " OR ";
			$where .= "cat like '%," . $cat . ",%'";
		}
	}

	if(!empty($notin))
	{
		$notcatsArr = array_map('trim', explode(',', $notin));
		foreach($notcatsArr as $cat)
		{
			$where .= " AND cat NOT LIKE '%," . $cat . ",%'";
		}
	}

	if(($order!='date')&($order!='views')&($order!='votes')&($order!='comments')&($order!='allow_index'))
	{
		$order='date';
	}	
	if(($short!='DESC')&($short!='ASC'))
	{
		$short='DESC';
	}
	
	$core->loadModLang('partner');
	$core->tempModule = 'partner';
	$queryDB = $db->query("SELECT n.*, c.* FROM ".DB_PREFIX."_partner as n LEFT JOIN ".DB_PREFIX."_langs as c on(c.postId=n.id and c.module='partner') WHERE active='1' " . $where . " ORDER BY " . $order . " " . $short . " LIMIT 0, " . $limit . "");
	$custom = '';
	if($db->numRows($queryDB) > 0) 
	{
		$partner = '';
		while($partner = $db->getRow($queryDB)) 
		{
			$tag_list = explode(',', $partner['tags']);
				$tag_count = 0;
				$tags = false;			
				foreach($tag_list as $tag) 
				{
					$tag_count++;
					if($tag_count < ($partner_conf['tags_num']+1)) 
					{
						$tags .= '<a href="partner/tags/' . $tag . '" title="' . $tag . '">' . ($headTag == $tag ? '<strong>' . $tag . '</strong>' : $tag) . '</a>';
					}
				}	
			$catInfo = $partner['cat'] !== ',0,' ? $core->catInfo('partner', $partner['cat']) : '';
			$cat = $partner['cat'] !== ',0,' ? $core->getCat('partner', $partner['cat'], 'short', 3) : '';
			$partner_link = $partner['cat'] !== ',0,' ? 'partner/' . $core->getCat('partner', $partner['cat'], 'development') . '/' : 'partner/';
			$cat_one = $partner['cat'] !== ',0,' ? $core->getCat('partner', $partner['cat'], 'altname', 1) : 'index';
			$short = $core->bbDecode($partner['short'], $partner['id'], true);
			$miniImg = _getCustomImg($short);		
			ob_start();
			$core->tpl->loadFile($template);
			include(loadTag('partner'));	
			$custom .= $core->tpl->return_end();
			unset($tags);
			ob_end_clean();
		}
	}