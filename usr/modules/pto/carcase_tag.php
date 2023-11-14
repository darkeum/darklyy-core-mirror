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
	$core->tpl->setVar('CONTENT', $items);
	$core->tpl->setVar('BREADCRUMBS', $breadcrumbs);
	$core->tpl->setVar('PAGES', $pages);
	$core->tpl->setVar('INFO', $info);
	$core->tpl->setVar('COMMENTS_VIEW', $comments);			