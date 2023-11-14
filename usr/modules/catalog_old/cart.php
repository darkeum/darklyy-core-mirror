<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
    header('Location: /');
    exit;
}

function add_to_cart($product_id, $count=1) {
	global $core, $db;
	//unset($_SESSION['products'][$product_id]);
	//провер¤ем, не был ли добавлен товар в корзину ранее:
	if (!empty($_SESSION['products'][$product_id])) {
		//увеличиваем количество на единицу, если товар уже добавлен:
		$_SESSION['products'][$product_id]['count']++;
	}
	else {
		//создаем пустой массив, на вс¤кий случай, можно и без него.
		$_SESSION['products'][$product_id]=array();
		//извлекаем цену товара из базы данных:
		$q="SELECT price FROM JMY_catalog WHERE id='$product_id'";
		//$q="SELECT price FROM JMY_catalog WHERE id=15";
		//разбиваем результат запроса в массив:
		$add_product=$db->fetchRow($db->query($q));
		//print_r($add_product);
		$price = json_decode($add_product['price'], true);
		if ($price["1bb285e9-a8f3-11e7-a442-704d7b330583"]['value'] != 0)
		{
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
			$price_rub = number_format($euro*$price["ee22d123-37ac-11e8-a463-704d7b330583"]['value'],2,'.','');
		}
		else
		{
			$price_rub = 0;
		}
		//print_r($price_rub);		
		//можно и так, если не умеем работать с объектами:
		//$add_product=mysql_fetch_assoc(mysql_query($q));
		//добавл¤ем товар в корзину:
		$_SESSION['products'][$product_id]['coast']=$price_rub;
		$_SESSION['products'][$product_id]['count']=$count;
	}
	/*
	вызываем функции дл¤ подсчета стоимости корзины и количества товаров.
	здесь ¤ оп¤ть внесу изменение, будем считать не количество товаров в корзине,
	а количество самих товаров, т.е. если в корзине 5 единиц товара 1, и 2 Ц товара 2, 
	то товаров в корзине Ц 2, а не 7.
	» еще один момент, лучше всего объединить в одну функцию подсчет суммы 	корзины и количества товаров в ней: update_cart()
	*/
	update_cart();
}
/**
 * перезагружаем корзину
 */
function update_cart() {
	//количество товаров в корзине считаем как количество элементов в массиве 	//$_SESSION['products'] с помощью стандартной функции PHP count():
	$_SESSION['products_incart']=count($_SESSION['products']);
	//сначала обнулим стоимость:
	$_SESSION['cart_coast']=0;
	//стоимость корзины (перемножаем цены на количество и складываем):
	foreach ($_SESSION['products'] as $key=>$value) {
		$_SESSION['cart_coast']+=$_SESSION['products'][$key]['coast']* $_SESSION['products'][$key]['count'];
	}
}
/**
* принимает те же параметры, что и функци¤ add_to_cart()
*/
function update_product_count($product_id, $count) {
	$_SESSION['products'][$product_id]['count']=$count;
	//вызываем update_cart() чтобы пересчитать стоимость.
	update_cart();
}
/**
 * удалить из корзины
*/
function remove_from_cart($product_id) {
	unset($_SESSION['products'][$product_id]);
	update_cart();
}


function cart_interface()
{
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang;
	if (isset($_SESSION['products']) && !empty($_SESSION['products']))
	{		
		foreach ($_SESSION['products'] as $key => $value) 
		{	
			$ids_str .= "'".$key."', ";
			
		}	
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id IN(".substr($ids_str, 0, -2).") ORDER BY title ASC");	
		$items = '';
		if($db->numRows($query) > 0) 
		{
			$sum_rub = 0;
			while($catalog = $db->getRow($query)) 
			{
				$price = json_decode($catalog['price'], true);
				if ($price["1bb285e9-a8f3-11e7-a442-704d7b330583"]['value'] != 0)
				{
					$price_is = $price["1bb285e9-a8f3-11e7-a442-704d7b330583"]['value'].' РУБ.';
					$price_rub = $price["1bb285e9-a8f3-11e7-a442-704d7b330583"]['value'];
				}
				else if ($price["ee22d123-37ac-11e8-a463-704d7b330583"]['value'] != 0)
				{
					$price_is = $price["ee22d123-37ac-11e8-a463-704d7b330583"]['value'].' EUR.';;
					$price_rub = $price["ee22d123-37ac-11e8-a463-704d7b330583"]['value'];
				}
				else
				{
					$price_is = 'по запросу';
					$price_rub = 0;
				}
				$sum_rub = $sum_rub+$_SESSION['products'][$catalog['id']]['count']*$price_rub;
				$core->tpl->loadFile('catalog/catalog.cart');
				$core->tpl->setVar('CART_COUNT', $_SESSION['products'][$catalog['id']]['count']);
				$core->tpl->setVar('CART_SUM', $_SESSION['products'][$catalog['id']]['count']*$price_rub.' РУБ.');
				$core->tpl->setVar('CART_ID', $catalog['id']);
				include(loadTag('catalog'));						
				$items .= $core->tpl->return_end();
				
			}
			$items .= '<tr class="cart__row border-bottom line1 cart-flex border-top">
				  <td colspan="4" class="text-right">
				   <b>Итого:</b>
				  </td>
				  <td colspan="2" class="text-right">
				   <b>'.$sum_rub.' РУБ.</b>
				  </td>
				 
				</tr>';
		}
	}
	$items .= '<tr class="cart__row border-bottom line1 cart-flex border-top">
				  <td colspan="6" class="text-left">
				   <b>Ваша корзина пуста.</b>
				  </td>
				 
				 
				</tr>';
	return $items;
}