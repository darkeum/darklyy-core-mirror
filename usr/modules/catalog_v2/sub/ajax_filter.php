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

function ajax_filter($atribute = array()) 
{
	global $core, $db;
	$uid = $core->auth->user_info['id'] ?? 0;
	$ajax_filter = array();
	$ajax_filter_text = '';
	$category = $atribute['category'];
	$where = '';
	if (isset($_GET))
	{
		$prepare_filter = prepare_filter($_GET);	
	}
	$where_user = $core->auth->isUser ? " AND active_user = '1'" : " AND active_guest = '1'";
	
	$manufacture_names = json_decode(get_manufacture('json'), true);
	$atribute_query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_atribute WHERE (cat LIKE '%,".$category.",%') AND NOT (hidden LIKE '%,".$category.",%' AND hidden IS NOT NULL)  ORDER BY position ASC");
	$atribute_value_query = $db->query("SELECT attribute, man FROM ".DB_PREFIX."_catalog WHERE active='1'  AND hidden = '0' AND category LIKE '%".$category."%'".$where_user);
	$atribute_value_query_filter = $db->query("SELECT attribute, man FROM ".DB_PREFIX."_catalog WHERE active='1' AND hidden = '0' AND category LIKE '%".$category."%' ".$prepare_filter['where'].$where_user);
	$attribute_data = [];
	//проход производителям
	$ajax_filter['man']['title'] = 'Производитель';
	$ajax_filter['man']['value'] = array();	
	$ajax_filter['man']['value_is'] = array();	
	//проход по категориям 
	if($db->numRows($atribute_query) > 0)
	{
		while($atribute_list = $db->getRow($atribute_query)) 
		{
			$ajax_filter[$atribute_list['id']]['title'] = $atribute_list['title'];
			$ajax_filter[$atribute_list['id']]['value'] = array();	
			$ajax_filter[$atribute_list['id']]['value_is'] = array();
			$ajax_filter[$atribute_list['id']]['data'] = $atribute_list;
		}
	}
	//проход по товарам
	if($db->numRows($atribute_value_query) > 0)
	{
		while($atribute_value_list = $db->getRow($atribute_value_query)) 
		{
			if(isset($ajax_filter['man']))
			{
				if (!in_array($atribute_value_list['man'], $ajax_filter['man']['value'])&&!empty($atribute_value_list['man'])) 
				{
					$ajax_filter['man']['value'][] = $atribute_value_list['man'];			
				}	
			}
			$attribute_value_array_one = json_decode($atribute_value_list['attribute'], true);
			if (isset($attribute_value_array_one) && is_array($attribute_value_array_one))
			{
				foreach($attribute_value_array_one as $k => $v)
				{
					// dd($attribute_value_array_one);
					if(isset($ajax_filter[$k]))
					{
						if (!in_array($v, $ajax_filter[$k]["value"])&&!empty($v)) 
						{
							$ajax_filter[$k]['value'][] = $v;
						}	
					}
				}
			}
		}
	}
	// dd($ajax_filter);

	//проход по отфильтрованным товарам
	if($db->numRows($atribute_value_query_filter) > 0)
	{
		while($atribute_value_list_filter = $db->getRow($atribute_value_query_filter)) 
		{
			
			if(isset($ajax_filter['man']))
			{
				if (!in_array($atribute_value_list_filter['man'], $ajax_filter['man']['value_is'])&&!empty($atribute_value_list_filter['man'])) 
				{
					$ajax_filter['man']['value_is'][] = $atribute_value_list_filter['man'];			
				}	
			}
			$attribute_value_filter_array_one = json_decode($atribute_value_list_filter['attribute'], true);
			if (isset($attribute_value_filter_array_one) && is_array($attribute_value_filter_array_one))
			{
				foreach($attribute_value_filter_array_one as $k => $v)
				{
					if(isset($ajax_filter[$k]))
					{
				//		if(isset($ajax_filter[$k]['value_is']))
				//		{
							if (!in_array($v, $ajax_filter[$k]['value_is'])&&!empty($v)) 
							{
								$ajax_filter[$k]['value_is'][] = $v;			
							}	
					//	}
					}
				}
			}
		}
		// dump($ajax_filter);
	}
	$i = 0;
    $ind = 0;
	$category_query = $db->query("SELECT position_filter FROM ".DB_PREFIX."_categories WHERE id = '".$category."' LIMIT 1");
	$category_result = $db->getRow($category_query);
	$position_filter = json_decode($category_result['position_filter'], true);
	$ajax_filter_2 = $ajax_filter;
	if(!empty($position_filter))
	{
		$ajax_filter_prepare = array();
		$ajax_filter_prepare['man'] = $ajax_filter_2['man'];
		unset($ajax_filter_2['man']);
		foreach($position_filter as $k => $v)
		{
			if(isset($ajax_filter_2[$v]))
			{
				$ajax_filter_prepare[$v] = $ajax_filter_2[$v];	
				unset($ajax_filter_2[$v]);
			}
		}	
		foreach($ajax_filter_2 as $k => $v)
		{
			$ajax_filter_prepare[$k] = $ajax_filter[$k];	
		}
		$ajax_filter = $ajax_filter_prepare;
		
	}
	// dd($ajax_filter);
	foreach($ajax_filter as $k => $v){
		$checked_filter = ($ind < 2 ? ' block-filter-title-active' : '');
		foreach($v['value'] as $kk => $vv)
		{
			if(in_array('ch_'.$k.$vv, $prepare_filter['key_array'])) 
			{				
				$checked_filter = ' block-filter-title-active block-filter-title-hover';				
			} 
		}
		$ajax_filter_text .= '<div class="block-filter '.$checked_filter.'"><span onclick="filterTitle(this)" class="block-filter-title">'. $v['title'] .'</span><div class="filterConDiv">';
		//asort($v['value']);
		if(isset($v["data"]) && $v["data"]["type"] == 1){
			$list = explode(",", $v["data"]["value"]);
			$new_value = [];
			foreach($v["value"] as $v1){
				$v1k = array_search($v1, $list);
				if($v1k !== false){
					$new_value[ $v1k ] = $v1;
				}
			}
			ksort($new_value);
			$v['value'] = $new_value;
		} else {
			uasort($v['value'], 'cmp');
		}
		
		foreach($v['value'] as $kk => $vv)
		{
			$smart_class = '';
			$checked = '';
			$disabled = '';
			$smart_class ='';
			if(in_array('ch_'.$k.$vv, $prepare_filter['key_array'])) 
			{
				$smart_class = 'smart-filter-checkbox-active';
				$checked = 'checked';				
			} 
			// dump($v);
			if(isset($v['value_is']) && !in_array($vv, $v['value_is']) && ($k != "man")) 
			{
				$disabled = 'disabled';
				$smart_class = 'smart-filter-checkbox-disabled';
				//$smart_class = 'smart-filter-checkbox-active';
				//$checked = 'checked';	
				
			}
			$i++;
			if ($k == 'man')
			{
				$ajax_filter_text .= '<label id="id_'.$k.'_'.$i.'" class="smart-filter-checkbox '.$smart_class.'"><input data-label="id_'.$k.'_'.$i.'" type="checkbox" value="'.$vv.'" class="no-icheck checkbox_filter" name="ch_'.$k.'[]" '.$checked.$disabled.'><div class="checkbox__text">'.$manufacture_names[$vv]['name'].'</label></div></label>';
			}
			else
			{		
				$fix_name = str_replace("/", "fraction", $vv);	
				$ajax_filter_text .= '<label id="id_'.$k.'_'.$i.'" class="smart-filter-checkbox '.$smart_class.'"><input data-label="id_'.$k.'_'.$i.'" type="checkbox" value="'.$fix_name.'" class="no-icheck checkbox_filter" name="ch_'.$k.'[]" '.$checked.$disabled.'><div class="checkbox__text">'.$vv.'</label></div></label>';
			
			}
		}
		$ajax_filter_text .= '</div></div>';

		$ind++;

	}
	$page = empty($_GET["page"]) ? 1 : $_GET["page"];
	$sort = empty($_GET["sort"]) ? 'n.title' : $_GET["sort"];
	$sort_type = empty($_GET["sort_type"]) ? 'ASC' : $_GET["sort_type"];
	$ajax_filter_text .= '<input type="hidden" value="'.$category.'" name="category" />';
	$ajax_filter_text .= '<input type="hidden" value="'.$page.'" name="page" id="inp_page">';
	$ajax_filter_text .= '<input type="hidden" value="'.$sort.'" name="sort" id="sort">';
	$ajax_filter_text .= '<input type="hidden" value="'.$sort_type.'" name="sort_type" id="sort_type">';
	return $ajax_filter_text;
}

function cmp($a, $b){
	$a = explode(" ", $a);
	$a = $a[0];
	$b = explode(" ", $b);
	$b = $b[0];
	
	if ( $a == $b ) return 0;
  elseif ( $a > $b ) return 1;
  elseif ( $a < $b ) return -1;
}

function short_filter($a, $b){
	
	print_r($a);
	$a = explode(" ", $a);
	$a = $a[0];
	$b = explode(" ", $b);
	$b = $b[0];
	
	if ( $a == $b ) return 0;
  elseif ( $a > $b ) return 1;
  elseif ( $a < $b ) return -1;
}

function ajax_filter_list($atribute = array()) 
{	
	global $core, $db;
	$uid = $core->auth->user_info['id'] ?? 0;
	$atribute['page'] = empty($atribute["page"]) ? 1 : $atribute["page"];
	$save_page = $atribute['page'];
	$atribute['page'] -= 1;
	$atribute['count'] = empty($atribute["count"]) ? 60 : $atribute["count"];
	$atribute['ajax'] = empty($atribute["ajax"]) ? false : true;
	$atribute['sort'] = empty($atribute["sort"]) ? 'n.title' : $atribute["sort"];
	$atribute['sort_type'] = empty($atribute["sort_type"]) ? 'ASC' : $atribute["sort_type"];
	$atribute['category'] = empty($atribute["category"]) ? ',1,' : $atribute["category"];

	$result = [
		"main" => "",
		"pagination" => "",
	];
	if($atribute['ajax']){
		// $ajax_filter_text = '';
	} else {
		$cat_type_list = !empty($_COOKIE['type-conclusion-list']) ? $_COOKIE['type-conclusion-list'] : '';
		$cat_type_table = !empty($_COOKIE['type-conclusion-table']) ? $_COOKIE['type-conclusion-table'] : '';

		$result["main"] .= '<div id="ajax_filter_list_load" style="display:none;"></div>';
		$result["main"] .= '<ul class="products-loop  grid clearfix type-conclusion '.$cat_type_list.$cat_type_table.'" id="ajax_filter_list">';
	}

	if(isset($_GET)){
		$prepare_filter = prepare_filter($_GET);
	}
	

	$atribute["where"] = "n.active='1' AND n.category LIKE '%".$atribute['category']."%' ".$prepare_filter['where'];
	
	if($uid == 0){
		$atribute["no_discount"] = true;
	}
	

	$att = $atribute;
	$att["no_price"] = true;
	$pages = 0;
	$qh2 = get_query_catalog($att);
	unset($qh2["LIMIT"]);
	$qs = implode(" ", $qh2);
	$cq = $db->query($qs);
	$pages = $db->numRows($cq);
	$pages_all = ceil($pages / $atribute['count']);
	
	$qh = get_query_catalog($atribute);
	$qq = implode(" ", $qh);

	$catalog_query = $db->query($qq);
	//проход по товарам
	if($db->numRows($catalog_query) > 0)
	{
		while($catalog = $db->getRow($catalog_query)) 
		{		
			$catInfo = $catalog['category'] !== ',0,' ? $core->catInfo('catalog_v2', $catalog['category']) : '';
			$cat = $catalog['category'] !== ',0,' ? $core->getCat('catalog_v2', $catalog['category'], 'short', 3) : '';
			$catalog_link = $catalog['category'] !== ',0,' ? 'catalog_v2/' : 'catalog_v2/';
			$cat_one = $catalog['category'] !== ',0,' ? $core->getCat('catalog_v2', $catalog['category'], 'altname', 1) : 'index';
			$short = $core->bbDecode($catalog['short'], $catalog['id'], true);
			$core->tpl->loadFile('catalog_v2/catalog.cat');
			include(loadTag('catalog_v2'));
			$result["main"] .= $core->tpl->return_end();
		}
	}	

	if($atribute['ajax']){
		// $ajax_filter_text .= '';
	} else {
		$result["main"] .= '</ul>';
	}

	if($pages_all > 0 && $pages > $atribute['count']){
		$class_stop_name = 'pagination-widget__page_cna';
		if($save_page == 1){
			$first_func = '';
			$prev_func = '';
			$class_stop = $class_stop_name;
		} else {
			$first_func = 'a_page(1);';
			$prev_func = 'a_page(paginator(\'prev\'));';
			$class_stop = '';
		}
		$li = '<li class="pagination-widget__page pagination-widget__page_first '.$class_stop.'" onclick="'.$first_func.'"><i class="fas fa-angle-double-left"></i></li>';
		$li .= '<li class="pagination-widget__page pagination-widget__page_prev '.$class_stop.'" onclick="'.$prev_func.'"><i class="fas fa-angle-left"></i></li>';
		for($i=1;$i<=$pages_all;$i++){
			$display = true;
			if($save_page < 6){
				if($i > 8){
					$display = false;
				}
			} else if($pages_all - 5 < $save_page){
				if($i <= $pages_all - 8){
					$display = false;
				}
			} else {
				if($save_page - 5 >= $i || $save_page + 4 <= $i){
					$display = false;
				}
			}
			if($display){
				$li .= '<li id="pagination-widget__page_'.$i.'"
				class="pagination-widget__page '.($save_page == $i ? 'pagination-widget__page_active' : '').'" 
				onclick="a_page('.$i.');">'.$i.'</li>';
			}
		}
		if($save_page == $pages_all){
			$next_func = '';
			$last_func = '';
			$class_stop = $class_stop_name;
		} else {
			$next_func = 'a_page(paginator(\'next\'));';
			$last_func = 'a_page('.$pages_all.');';
			$class_stop = '';
		}
		$li .= '<li class="pagination-widget__page pagination-widget__page_next '.$class_stop.'" onclick="'.$next_func.'"><i class="fas fa-angle-right"></i></li>';
		$li .= '<li class="pagination-widget__page pagination-widget__page_last '.$class_stop.'" onclick="'.$last_func.'"><i class="fas fa-angle-double-right"></i></li>';
		if($pages_all == $save_page){
			$btn = '';
		} else {
			$btn = '<button class="pagination-widget__show-more-btn">Показать ещё</button>';
		}
		
		$ul = '<ul class="pagination-widget__pages">'.$li.'</ul>';
		if($atribute['ajax']){
			$result["pagination"] .= $btn.$ul;
		} else {
			$result["pagination"] .= '<div id="products-list-pagination" class="pagination-widget">'.$btn.$ul.'</div>';
		}
	}

	if($atribute['ajax']){
		return json_encode($result, JSON_UNESCAPED_UNICODE);
	} else {
		return implode("", $result);
	}
}

function prepare_filter($data)
{
	global $core;
	$uid = $core->auth->user_info['id'] ?? 0;
	$return = array ();
	$key_array = array();
	$where = '';
	$ch_man = [];
	//echo '<pre>';
	//print_r($data);
	foreach($data as $k => $v)
	{
		if($k != 'category' && $k != 'ajax' && $k !='ch_man' && $k != 'page' && $k != 'count' && $k != 'sort' && $k != 'sort_type' && $k != 'url')
		{			
			$key = substr($k, 3);			
			if(is_array($v))
			{
				foreach($v as $kk => $vv)
				{
					$f_vv = $data[$k][$kk] = str_replace("fraction", "/", $vv);
					$key_array[] = $k.$f_vv;
				}
				$value = implode("', '", $data[$k]);	 
			}
			else
			{
				$f_v = $data[$k] = str_replace("fraction", "/", $v);
				$value = $f_v;
				$key_array[] = $k.$f_v;
			}
			$where .= "AND JSON_VALUE(`attribute` , '$.".$key."') IN ('".$value."') ";
		}
		if($k == 'ch_man')
		{
			foreach($v as $ch){
				$ch_man[] = "man = '{$ch}'";
				// $where .= "AND man = '".$ch."' ";		
				$key_array[] = $k.$ch;
			}	
        }
	}	
	if(count($ch_man) > 0){
		$where .= " AND (".implode(" OR ", $ch_man).")";
	}
	
	$return['where'] = $where;
	$return['key_array'] = $key_array;
	return $return;
}

