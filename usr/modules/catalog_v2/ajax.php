<?php
global $db, $allowFuncs, $url, $core;

if (isset($allowFuncs))
{
$allowFuncs = array_merge($allowFuncs, ['getCatalogInfoById']);
}

function getCatalogInfoById()
{
	global $db, $allowFuncs, $url, $core;	
	if(isset($_GET['id']))
	{
		//$ids = explode(',', $_GET['id']);
		$ids = $_GET['id'];	
		$atribute_query = $db->query("SELECT id,title FROM ".DB_PREFIX."_catalog_atribute ORDER BY position ASC");
		if($db->numRows($atribute_query) > 0)
		{
			while($at_list = $db->getRow($atribute_query)) 
			{
				$atribute_list[$at_list['id']] = $at_list['title'];		
			}
		}
		$return_array = array();
		$query = $db->query("SELECT n.*,  u.unit, u.sname as unit_sname, m.id_1c, m.name as manufacture_name, m.id as manufacture_id, m.id_1c as manufacture_id_1c  FROM ".DB_PREFIX."_catalog as n LEFT JOIN ".DB_PREFIX."_catalog_unit as u on(u.unit=n.unit) LEFT JOIN ".DB_PREFIX."_catalog_manufacture as m on(m.id_1c = n.manufacture) WHERE n.id IN(" . $db->safesql($ids) . ")");
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
				$attribute_pre[$k]['attribute'] = $attribute_pre[$k]['attribute'] + $vv;
			}		
			$category_query = $db->query("SELECT position_filter FROM ".DB_PREFIX."_categories WHERE id = '".$k."' LIMIT 1");
			$category_result = $db->getRow($category_query);
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
				}						
				$return_array[$k]['items'][$kk]['attribute'] = $attribute;	
			}
		}		
		print_r($return_array);		
	}	
}