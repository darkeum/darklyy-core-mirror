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

function compare()
{
	global $db, $allowFuncs, $url, $core;	
	//setcookie("compare", '1,2,3,40667,2136,2133,2138,1165,40566,555,40565,2139,2140,2137,2135,2134,556,2132,563,1282,40567,557,1263,44038,21359');
	if(isset($_COOKIE['compare']))
	{		
		$ids = '';
		$id = $_COOKIE['compare'];		
		$id_array = explode(',',$id);
		foreach($id_array as $k => $v) 
		{
			$id_array[$k] = intval($v);
		}	
		$ids = implode(',',$id_array);
		
		$atribute_query = $db->query("SELECT id,title FROM ".DB_PREFIX."_catalog_atribute ORDER BY position ASC");
		if($db->numRows($atribute_query) > 0)
		{
			while($at_list = $db->getRow($atribute_query)) 
			{
				$atribute_list[$at_list['id']] = $at_list['title'];		
			}
		}
		if(!empty($ids)&&($ids != ''))
		{
			$return_array = array();
			$query = $db->query("SELECT n.*,  u.unit, u.sname as unit_sname, m.id_1c, m.name as manufacture_name, m.id as manufacture_id, m.id_1c as manufacture_id_1c  FROM ".DB_PREFIX."_catalog as n LEFT JOIN ".DB_PREFIX."_catalog_unit as u on(u.unit=n.unit) LEFT JOIN ".DB_PREFIX."_catalog_manufacture as m on(m.id = n.man) WHERE n.id IN(" . $db->safesql($ids) . ")");
			if($db->numRows($query) > 0) 
			{
				while($catalog = $db->getRow($query)) 
				{
					$pArrayData = \Price::getPrice($catalog['id']);
					$pp1 = number_format($pArrayData->price_list_,2,'.',' ')." ₽";
					$category_array = explode(',', $catalog['category']);
					$category = $category_array[1];				
					$return_array[$category]['items'][$catalog['id']]['id'] = $catalog['id'];
					$return_array[$category]['items'][$catalog['id']]['title'] = $catalog['title'];
					$return_array[$category]['items'][$catalog['id']]['sku'] = $catalog['sku'];	
					$return_array[$category]['items'][$catalog['id']]['category'] = $category;	
					$return_array[$category]['items'][$catalog['id']]['preview'] = $catalog['preview'];	
					$return_array[$category]['items'][$catalog['id']]['url'] = '/catalog_v2/'.$catalog['altname'].'.html';	
					$return_array[$category]['items'][$catalog['id']]['price'] = $pp1;				
					$return_array[$category]['attribute'] = array();
					$attribute_array[$category][$catalog['id']] = json_decode($catalog['attribute'], true);
					
				} 
			}
			foreach($attribute_array as $k => $v) 
			{	
				$attribute_pre[$k]['attribute'] = array();
				foreach($v as $kk => $vv) 
				{
					if(is_array($vv))
					{
						$attribute_pre[$k]['attribute'] = $attribute_pre[$k]['attribute'] + $vv;
					}
				}		
				$category_query = $db->query("SELECT position_filter FROM ".DB_PREFIX."_categories WHERE id = '".$k."' LIMIT 1");
				$category_result = $db->getRow($category_query);
				//echo $category_result['position_filter'];
				$position_filter = json_decode($category_result['position_filter'], true);			
				if(!isset($position_filter))
				{
					foreach($attribute_pre[$k]['attribute'] as $kk => $vv) 
					{
						$position_filter[] = $kk;
					}
				}	
				foreach($position_filter as $kk => $vv) 
				{
					if(isset($attribute_pre[$k]['attribute'][$vv]))
					{
						$attribute_pre[$k]['attribute_complete'][] = array('id' => $vv, 'title' => $atribute_list[$vv]);
					}
				}
				$return_array[$k]['attribute'] = $attribute_pre[$k]['attribute_complete'];
				foreach($return_array[$k]['items'] as $kk => $vv) 
				{	
					$attribute = array();
					foreach($return_array[$k]['attribute'] as $kkk => $vvv) 
					{ 
						if(isset($attribute_array[$k][$kk][$vvv['id']]))
						{
							$attribute[] = array('id' => $vvv['id'], 'value' => $attribute_array[$k][$kk][$vvv['id']], 'title' => $atribute_list[$vvv['id']]);
						}	
						else
						{
							$attribute[] = array('id' => $vvv['id'], 'value' => '-', 'title' => $atribute_list[$vvv['id']]);
						}
					}						
					$return_array[$k]['items'][$kk]['attribute'] = $attribute;	
				}
			}	
			$items = '';
			$cats = '';
			$cat_count = 0;
			$tpl_attribute_group ='';
			foreach($return_array as $k => $v) 
			{
				$cat_count++;
				$i = 0;
				$catInfo = $core->catInfo('catalog_v2', ','.$k.',');				
				foreach($v['items'] as $key => $value) 
				{
					$i++;
					$core->tpl->loadFile('catalog_v2/catalog.compare.detail');		
					$core->tpl->setVar('ID', $value['id']);
					$core->tpl->setVar('ACTIVE', (($cat_count == 1) ? 'active' : ''));
					$core->tpl->setVar('TITLE', $value['title']);
					$core->tpl->setVar('SKU', $value['sku']);
					$core->tpl->setVar('PREVIEW', $value['preview']);
					$core->tpl->setVar('CATEGORY', $value['category']);
					$core->tpl->setVar('CATEGORY_ID', $k);
					$core->tpl->setVar('PRICE', $value['price']);
					$core->tpl->setVar('URL', $value['url']);
					$core->tpl->setVar('CATEGORY_URL', '/catalog_v2/'.$catInfo['altname']);
					$core->tpl->setVar('CATEGORY_NAME', $catInfo['title']);
					
					
					$items .= $core->tpl->return_end();				
					
					
				}
				foreach($v['attribute'] as $key => $value) 
				{	
					$tpl_attribute = '';
					$value_temp = '';
					$flag = true;
					foreach($v['items'] as $kk => $vv) 
					{	
						if(empty($value_temp))
						{
							$value_temp = $vv['attribute'][$key]['value'];
						}
						$core->tpl->loadFile('catalog_v2/catalog.compare.attribute');	
						if ($value_temp != $vv['attribute'][$key]['value'])
						{
							$flag = false;
						}
						$core->tpl->setVar('ATRIBUTE_VALUE', $vv['attribute'][$key]['value']);
						$tpl_attribute .= $core->tpl->return_end();						
					}	
					$core->tpl->loadFile('catalog_v2/catalog.compare.attribute.group');		
					$core->tpl->setVar('ATRIBUTE_ITEMS', $tpl_attribute);
					$core->tpl->setVar('ATRIBUTE_DIF', ($flag) ? '' : 'different');
					$core->tpl->setVar('ATRIBUTE_TITLE', $value['title']);
					$core->tpl->setVar('ATRIBUTE_ACTIVE', (($cat_count == 1) ? 'active' : ''));
					$core->tpl->setVar('ATRIBUTE_CAT_ID', $k);
					$tpl_attribute_group .= $core->tpl->return_end();	
				}
				
				$cats .= '<span onclick="compareTab(this)" '.(($cat_count == 1) ? 'class="active"' : '').' data-id="'.$k.'">'.$catInfo['title'].'<span>'.$i.'</span></span>';	
				
			}			
			$core->tpl->loadFile('catalog_v2/catalog.compare');
			$core->tpl->setVar('CONTENT', $items);
			$core->tpl->setVar('ATTRIBUTE', $tpl_attribute_group);
			$core->tpl->setVar('CATS', $cats);
			$core->tpl->end();
		}
		else
		{
			$core->tpl->loadFile('catalog_v2/catalog.compare.empty');
			$core->tpl->end();
		}
	}	
	else
	{
		$core->tpl->loadFile('catalog_v2/catalog.compare.empty');
		$core->tpl->end();
	}
}