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
global $db;
	//$price = get_price($catalog['price']);
	$pp1 = "по запросу";
	$pp2 = "0";
	
	
	
	
	$l_price = isset($catalog["sum"]) ? $catalog["sum"] : 0;
	if(!empty($l_price) && $l_price != 0){
		$pp1 = number_format($l_price,2,'.',' ')." ₽";
		$pp2 = $l_price;
	}
	
	$price = [
		"is" => $pp1,
		"RUB" => $pp2,
	];

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
		$quantity = '<span class="order-in-stock">в наличии</span>';
	}
	else
	{
		$quantity = '<span class="order-under">под заказ</span>';
	}	

$attribute = json_decode($catalog['attribute'], true);
$spec = json_decode($catalog['spec'], true);
$weight = json_decode($catalog['weight'], true);

$publish_urls = json_decode($catalog['publish_urls'], true);

$attribute_all = '<table class="shop_attributes">';
if(!empty($attribute)){
	foreach($attribute as $k => $v)
	{
		if(isset($atribute_list[$k])&&!empty($v))
		{
				$attribute_all .= '<tr><th>'.$atribute_list[$k].'</th><td><p>'.$v.'</p></td></tr>';
		}
	}
}
$attribute_all .= '</table>';	

$spec_list[1] = 'Наименование (спецификация)';
$spec_list[2] = 'Марка (спецификация)';
$spec_list[3] = 'Производитель (спецификация)';

$weight_list[1] = 'Вес';
$weight_list[2] = 'Длина';
$weight_list[3] = 'Объем';
$weight_list[4] = 'Площадь';

$weight_list_unit[1] = 'кг.';
$weight_list_unit[2] = 'мм.';
$weight_list_unit[3] = 'м2.';
$weight_list_unit[4] = 'м3.';


$spec_all = '<table class="shop_attributes">';
if(isset($spec))
{
foreach($spec as $k => $v)
{
	if(isset($spec_list[$k])&&!empty($v))
	{
		$vh = html_entity_decode(htmlspecialchars_decode($v, ENT_QUOTES), ENT_QUOTES);
		// var_dump($vh);
		$vh = preg_replace('/&lt;br&gt;/i', '<br>', $vh);
		$spec_all .= '<tr><th>'.$spec_list[$k].'</th><td><p>'.$vh.'</p></td></tr>';
	}
}
}

$ves = 'не указано';

if (isset($weight))
{
//	print_r($weight);	
foreach($weight as $k => $v)
{
	if(isset($weight_list[$k])&&!empty($v))
	{
		if($k == '1')
		{
			$ves = $v;
		}
		//print_r($k);
		$spec_all .= '<tr><th>'.$weight_list[$k].'</th><td><p>'.$v.' '.$weight_list_unit[$k].'</p></td></tr>';
	}
}
}

$spec_all .= '</table>';	
	
	$view_360 = '';
	if (isset($publish_urls['view_360']))
	{
		$view_360 = '<div class="item-thumbnail-product">
			<div class="thumbnail-wrapper">
			<a data-fancybox data-type="iframe" data-src="/catalog_v2/iframe_360/'.$catalog['id'].'" href="javascript:;">

			<img width="112" height="112" src="usr/tpl/dev2/assest/images/icon/view360.jpg" class="slick-current" alt="" srcset="usr/tpl/dev2/assest/images/icon/view360.jpg 112w" sizes="(max-width: 112px) 100vw, 112px" />		
</a>			

</div>
		</div>';
	}
	$weight_str = 'Не указано';
	if(isset($weight[1])&&!empty($weight[1]))
	{
		$weight_str = $weight[1];
	}
	$manufacture_id = isset($catalog['manufacture_id']) ? $catalog['manufacture_id'] : '';
	$manufacture_id_1c = isset($catalog['manufacture_id_1c']) ? $catalog['manufacture_id_1c'] : '';
	// $man_image = '/files/manufacture/'.$manufacture_id.'.jpg';
	$man_image = empty($catalog["m_preview"]) ? 'files/manufacture/'.$manufacture_id.'.jpg' : str_replace("https://atri-energo.ru/", "", $catalog["m_preview"]);
	// dd($man_image);
	// var_dump(file_exists($man_image));
	$core->tpl->setVar('TITLE', stripcslashes($catalog['title']));
	$core->tpl->setVar('TITLE_ENTITY', htmlentities(stripcslashes($catalog['title'])));
	$core->tpl->setVar('SKU', $sku);
	$core->tpl->setVar('VES', $ves);
	$core->tpl->setVar('VIEW_360', $view_360);
	$core->tpl->setVar('ID', $catalog['id']);
	$unit_sname = isset($catalog['unit_sname']) ? $catalog['unit_sname'] : '';
	$core->tpl->setVar('UNIT', $unit_sname);
	$core->tpl->setVar('MAN', $man);
	$core->tpl->setVar('ATTRIBUTE_ALL', $attribute_all);
	$core->tpl->setVar('SPEC_ALL', $spec_all);
	$core->tpl->setVar('STOCK', $quantity);
	$core->tpl->setVar('WEIGHT_STR', $weight_str);
	
	$core->tpl->setVar('MANUFACTURE_IMG', (file_exists($man_image) ? '<img src="'.$man_image.'" class="order-logo-img" alt="" />' : ''));
	$core->tpl->setVar('MANUFACTURE_URL', '/catalog/man/'.$manufacture_id_1c);
	$core->tpl->setVar('PRICE', $price['is']);
	$core->tpl->setVar('PRICE_RUB', $price['RUB']);

	$core->tpl->setVar('SHORT', $short);
	$core->tpl->setVar('FULL', '<div id="full-' . $catalog['id'] . '">' . $core->bbDecode($catalog['full'], $catalog['id'], true) . '</div>');
	$core->tpl->setVar('CATEGORY', $cat);
	$core->tpl->setVar('CAT_ONE', $cat_one);
	$core->tpl->setVar('ALTNAME', $catalog['altname']);
	$core->tpl->setVar('ICON', isset($catInfo['icon']) ? $core->getCatImg($catalog_link, $catInfo['icon'], $catInfo['title']) : '');
	// $core->tpl->setVar('AUTHOR', '<a href="profile/' . $catalog['author'] . '" title="' . _PAGE . ': ' . $catalog['author'] . '">' . $catalog['author'] . '</a>');
	$core->tpl->setVar('AUTHOR', '');
	$core->tpl->setVar('VIEWS', $catalog['views']);
	$core->tpl->setVar('PREVIEW', (empty($catalog['preview']) ? '/files/no_photo.jpg' : $catalog['preview']));
	// $core->tpl->setVar('COMMENTS', $catalog['comments']);
	$core->tpl->setVar('COMMENTS', '');
	$ajaxLoadClass=false;
	if (isset($atribute['ajax']))
	{
		$ajaxLoadClass=$atribute['ajax'];
	}
	$core->tpl->setVar('AJAX_LOAD', ($ajaxLoadClass ? 'ajax-load ' : '' ));
	
	
    $core->tpl->setVar('CAT_TYPE_LIST', (!empty($_COOKIE['type-conclusion-list']) ? $_COOKIE['type-conclusion-list'] : '' ));
    $core->tpl->setVar('CAT_TYPE_TABLE', (!empty($_COOKIE['type-conclusion-table']) ? $_COOKIE['type-conclusion-table'] : '' ));
    $core->tpl->setVar('CAT_TYPE_LIST_ACTIVE', '');


    $core->tpl->setVar('CAT_TYPE_LIST_ACTIVE', (!empty($_COOKIE['type-conclusion-list']) ? 'active' : ''));
    $core->tpl->setVar('CAT_TYPE_TABLE_ACTIVE', (!empty($_COOKIE['type-conclusion-table']) ? 'active' : ''));
    $core->tpl->setVar('CAT_TYPE_GRID_ACTIVE', (empty($_COOKIE['type-conclusion-list']) && empty($_COOKIE['type-conclusion-table']) ? 'active' : ''));


	$core->tpl->setVar('FULL_LINK', $catalog_link . $catalog['altname'] . ".html");	
	$miniImg = _getCustomImg($short);
	
	$sum_original = isset($catalog["sum_original"]) ? $catalog["sum_original"] : 0;
	$sum_original = round($sum_original, 2);
	$sum_original_view = empty($sum_original) ? "По запросу" : number_format($sum_original,2,'.',' ')." ₽";
	$sum = isset($catalog["sum"]) ? round($catalog["sum"], 2) : 0;
	$core->tpl->sources = preg_replace_callback("#\\[sum_original](.*?)\\[/sum_original]#is", function($matches) use($sum_original, $sum)
				{
					if($sum != $sum_original)
					{
						return stripslashes($matches[1]);	
					}
				}, $core->tpl->sources);
	$core->tpl->setVar('PRICE_ORIGINAL_RUB', $sum_original_view);
	
	
	
	
	
	
	
	
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
		
	// $catalog_date = $catalog['date'];
	// $core->tpl->sources = preg_replace_callback("#\\{%MYDATE:(.*?)%\\}#is",  
	// 	function($match) use ($catalog_date)
	// 	{
	// 		return date($match[1], $catalog_date);
	// 	},$core->tpl->sources);	
		
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
	
	
	if(false)
	{
		$catId = explode(',', $catalog['cat']);
		$core->tpl->setVar('BREADCUMB', $core->getCat('catalog', ($catId[1] != 0) ? $catId[1] : '', 'breadcrumb', 1));
	}	
	
	$core->tpl->sources = preg_replace(array_keys($array_replace), array_values($array_replace), $core->tpl->sources);
	$core->tpl->sources = preg_replace("#\\{%IMG:(.*?):(.*?)%\\}#is", (!empty($miniImg[(int)"\${1}"]) ? $miniImg[(int)"\${1}"] : "\${2}") , $core->tpl->sources);
	$core->tpl->sources = preg_replace("#\\{%IMG:(.*?)%\\}#is",  (!empty($miniImg[(int)"\${1}"]) ? $miniImg[(int)"\${1}"] : ''), $core->tpl->sources);
	// $core->tpl->setVar('DATE', formatDate($catalog['date']));
	$core->tpl->setVar('ID', $catalog['id']);
	// $core->tpl->setVar('RATING', $catalog['allow_rating'] ? draw_rating($catalog['id'], 'catalog', $catalog['score'], $catalog['votes']) : '');
	// $core->tpl->setVar('EDIT', ($core->auth->isModer||$core->auth->isAdmin)  ? '<a href="catalog/edit/'.$catalog['id'].'">'._EDIT.'</a>' : '');
	
	$core->tpl->sources = preg_replace(array_keys($array_replace), array_values($array_replace), $core->tpl->sources);