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
	$core->tpl->setVar('PAGE_TITLE', $page_title);
	$core->tpl->setVar('MANUFACTURE', $manufacture);
	$core->tpl->setVar('CONTENT', $items);
	$core->tpl->setVar('BREAD', $bread);
	$core->tpl->setVar('PAGES', $pages);
	$core->tpl->setVar('INFO', $info);
	$core->tpl->setVar('COMMENTS', '');			
	$core->tpl->sources = if_sets("#\\[catalog_main\\](.*?)\\[/catalog_main\\]#is", $core->tpl->sources, $f_main);
	$core->tpl->sources = if_sets("#\\[catalog_list\\](.*?)\\[/catalog_list\\]#is", $core->tpl->sources, $f_list);
	$core->tpl->sources = if_sets("#\\[catalog_cats\\](.*?)\\[/catalog_cats\\]#is", $core->tpl->sources, $f_cats);
	$core->tpl->sources = if_sets("#\\[catalog_view\\](.*?)\\[/catalog_view\\]#is", $core->tpl->sources, $f_view);