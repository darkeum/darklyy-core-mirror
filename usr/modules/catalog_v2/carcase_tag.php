<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
    header('Location: /');
    exit;
}	

$stypex = 1;
if (isset($_POST['s_type']))
{
	$stypex = intval($_POST['s_type']);
}
$stype_list = '<option value="1" '.($stypex == '1' ? 'selected="selected"' : '').'>Искать по всем полям</option>
							<option value="2" '.($stypex == '2' ? 'selected="selected"' : '').'>Искать по артикулу</option>
							<option value="3" '.($stypex == '3' ? 'selected="selected"' : '').'>Искать по наименованию</option>
							<option value="4" '.($stypex == '4' ? 'selected="selected"' : '').'>Искать по описанию</option>';

							if (empty($_SESSION['products'])) {
	$small_cart = 'Ваша корзина пуста';
}
else {
	$small_cart = 'Корзина: <span class="icon cart" aria-hidden="true"></span><span class="price"><span data-of="totalPrice"><span id="totalPrice">'.$_SESSION['cart_coast'].'</span> руб.</span></span><span class="badge" data-toggle="visible" data-of="totalCount" style=""><span id="totalGoods">'.$_SESSION['products_incart'].'</span></span>';
}
	$core->tpl->setVar('SMALL_CART', $small_cart);
	$core->tpl->setVar('PAGE_TITLE', $page_title);
	$core->tpl->setVar('MANUFACTURE',(isset($manufacture) ? $manufacture : ''));
	$core->tpl->setVar('CONTENT', $items);
	$core->tpl->setVar('BREAD', (isset($bread) ? $bread : ''));
	$core->tpl->setVar('PAGES', (isset($pages) ? $pages : ''));
	$core->tpl->setVar('INFO', 'ertert');
	$core->tpl->setVar('CAT_FULL', (isset($cat_full) ? $cat_full : ''));
	$core->tpl->setVar('FILTER', (isset($filter) ? $filter : ''));
	$core->tpl->setVar('FILTER_V2', (isset($filter_v2) ? $filter_v2 : ''));
	$core->tpl->setVar('NUMB_IS', (isset($numb_is) ? $numb_is : ''));
	$core->tpl->setVar('NUMB_NO', (isset($numb_no) ? $numb_no : ''));
	$core->tpl->setVar('NUMB_ALL',(isset($numb_all) ? $numb_all : ''));
	$core->tpl->setVar('SEARCH_TEXT', (isset($stext) ? $stext : ''));
	$core->tpl->setVar('SEARCH_TYPE', (isset($stype_list) ? $stype_list : ''));
	$core->tpl->setVar('BREADCRUMBS', $breadcrumbs);
	$core->tpl->setVar('MAIN_URI', $_GET["url"]);
	
	$core->tpl->setVar('S_ALL', (isset($s_all) ? $s_all : ''));
	$core->tpl->setVar('S_NO', (isset($s_no) ? $s_no : ''));
	$core->tpl->setVar('S_IS', (isset($s_is) ? $s_is : ''));
	
	$core->tpl->setVar('COMMENTS', '');			
	$core->tpl->sources = if_sets("#\\[catalog_main\\](.*?)\\[/catalog_main\\]#is", $core->tpl->sources, (isset($f_main) ? $f_main : ''));
	$core->tpl->sources = if_sets("#\\[catalog_cart\\](.*?)\\[/catalog_cart\\]#is", $core->tpl->sources, (isset($f_cart) ? $f_cart : ''));
	$core->tpl->sources = if_sets("#\\[catalog_list\\](.*?)\\[/catalog_list\\]#is", $core->tpl->sources, (isset($f_list) ? $f_list : ''));
	$core->tpl->sources = if_sets("#\\[catalog_cats\\](.*?)\\[/catalog_cats\\]#is", $core->tpl->sources, (isset($f_cats) ? $f_cats : ''));
	$core->tpl->sources = if_sets("#\\[catalog_view\\](.*?)\\[/catalog_view\\]#is", $core->tpl->sources, (isset($f_view) ? $f_view : ''));