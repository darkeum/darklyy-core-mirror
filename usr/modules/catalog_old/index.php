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
session_start(); 
function main($cat = null) 
{
global $db, $config, $core, $tags, $url, $headTag, $cache, $lang;
	$parrent_flag = false;
	$cat_u = $cat;
	$numb = 30;
	//вывод главной каталога
	if(!$cat) 
	{
		$cat_parrent = $db->query("SELECT id as cid, name, fulltitle, description, keywords, altname FROM ".DB_PREFIX."_categories WHERE parent_id='0' AND module = 'catalog' AND status='1' ORDER BY name ASC");
		if($db->numRows($cat_parrent) > 0)
		{
			$parrent_flag = true;
			
		}
		else
		{
			$parrent_flag = false;
		}
		$f_main = 'yes';
		set_title(array($lang['catalog']));
		$page_title = $lang['catalog'];
		$where = ' AND n.allow_index != \'1\'';
		$link = '';
		$core->tpl->uniqTag = 'main';
		$queryM = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_manufacture ORDER BY sum DESC LIMIT 8");
		$manufacture = '';
		if($db->numRows($queryM) > 0) 
		{
			while($catalogM = $db->getRow($queryM)) 
			{
				$core->tpl->loadFile('catalog/catalog.manufacture');
				$core->tpl->setVar('NAME', $catalogM['name']);
				$core->tpl->setVar('MAN_ID', $catalogM['id_1c']);
				$core->tpl->setVar('IMG', '/files/manufacture/'.$catalogM['id'].'.jpg');
				$core->tpl->setVar('SUM', $catalogM['sum']);					
				$manufacture .= $core->tpl->return_end();
			}
		}	
	} 
	elseif($cat == 'tag')
	{
		$blockCL = true;
		set_title(array($lang['catalog'], $lang['tag'], $url[2]));
		$page_title = $lang['tag'] . ': '.$url[2];
		$core->tpl->title($lang['tag'] . ': '.$url[2]);
		$where = 'AND tags regexp \'[[:<:]](' . $db->safesql($url[2]) . ')[[:>:]]\'';
		$link = '/tags/' . $url[2];		
		$core->tpl->uniqTag = 'cat';
	}
	//вывод производителей
	elseif($cat == 'man')
	{
		$f_main = '';
		$queryMAN = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_manufacture WHERE id_1c = '" . $db->safesql($url[2]) . "'");	
		$resMAN = $db->getRow($queryMAN);	
		$blockCL = true;
		set_title(array($lang['catalog'], 'Производители', $resMAN['name']));
		$bread = '<li><a href="/catalog/manufacture">Производители</a></li>';
		$page_title = $resMAN['name'];
		$where = 'AND manufacture = \''.$db->safesql($url[2]).'\'';
		$link = '/man/' . $url[2];		
		$core->tpl->uniqTag = 'cat';
	}
	//вывод поиска
	elseif($cat == 'search')
	{
		$core->tpl->uniqTag = 'cat';
		if (!empty($_POST['referal']))
		{
		
			$stext = filter($_POST['referal']);				
			if ($_POST['s_type'] == 2)
			{
				$where = "AND sku like '%" . $stext . "%'";
			}
			elseif ($_POST['s_type'] == 3)
			{
				$where = "AND title like '%" . $stext . "%'";
			}
			elseif ($_POST['s_type'] == 4)
			{
				$where = "AND short like '%" . $stext . "%'";
			}
			else
			{
				$where = "AND (sku like '%" . $stext . "%' OR title like '%" . $stext . "%' OR short like '%" . $stext . "%')";
			}

		}
		
	}
	else
	{
		$f_main = '';
		$cat = mjsEnd($url);
		$altname = filter($cat, 'a');
		$cat_query = $db->query("SELECT id as cid, name, fulltitle, description, keywords, full FROM ".DB_PREFIX."_categories WHERE altname='" . $db->safesql($altname) . "'");
		if($db->numRows($cat_query) == 0)
		{
			location();
		}		
		$cat_info = $db->getRow($cat_query);
		$cat_full = '';
		if(!empty($cat_info['full']))
		{			
			$cat_full = '
			<style> .text-cat p {margin-bottom: 0rem; font-size: 14px;    color: #000;}</style>
			<div style="padding-left: 40px;   padding-right: 40px;" class="row text-cat">'.$cat_info['full'].'</div>';
		}
		if(!empty($cat_info['fulltitle']))
		{
			set_title(array($cat_info['fulltitle']));
			$page_title = $cat_info['fulltitle'];
		}
		else
		{
			set_title(array($cat_info['name']));
			$page_title = $cat_info['name'];
		}
		if(!empty($cat_info['keywords']))
		{
			$core->tpl->keywords = $cat_info['keywords'];
		}
		if(!empty($cat_info['description']))
		{
			$core->tpl->description = $cat_info['description'];
		}		
		$core->tpl->uniqTag = 'cat';
		$core->tpl->feed_link = 'cat/' . $cat_info['cid'];		
		if($cat != 'search')
		{
			$where = "AND category like '%," . $cat_info['cid'] . ",%'";
		}
		$cat_ids = getcache('categories');
		$cat_pod = false;
		foreach($cat_ids as $cid => $val) 
		{
			foreach($val as $pid => $name) 
			{
				if($pid == $cat_info['cid']) $cat_pod .= ','.$cid;
			}
		}		
		$link = '/' . $core->getCat('catalog', $cat_info['cid'], 'development');
		$cat_parrent = $db->query("SELECT id as cid, name, fulltitle, description, keywords, altname FROM ".DB_PREFIX."_categories WHERE parent_id='".$cat_info['cid']."' AND module = 'catalog' AND status='1' ORDER BY name ASC");
		if($db->numRows($cat_parrent) > 0)
		{
			$parrent_flag = true;
			
		}
		else
		{
			$parrent_flag = false;
		}
		
	}
	// $where .= ' AND n.date <= \'' . time() . '\'';	
	$page = init_page();
	$cut = ($page-1)*$numb;	
	
	if($parrent_flag)
	{
		while($parrent = $db->getRow($cat_parrent)) 
		{
			$core->tpl->loadFile('catalog/catalog.category');
			$core->tpl->setVar('NAME', $parrent['name']);
			$core->tpl->setVar('CAT_URL', $url[1].'/'.$parrent['altname']);
			$core->tpl->setVar('IMG', '/files/cats/'.$parrent['cid'].'.jpg'); 
			$core->tpl->setVar('SUM', $parrent['sum']);
			$items .= $core->tpl->return_end();
		}
		$f_list = '';
		$f_cats = 'yes';
		$core->tpl->loadFile('catalog/catalog.carcase');
		include('carcase_tag.php');
		$core->tpl->end();
	}
	else
	{	
		
		// foreach ($_POST as $key => $value)
		// {
			
		// 	if ($key != 'sort' && $key != 'stock' && $cat != 'search')
		// 	{
		// 		$where_filter .= " AND JSON_VALUE(`fields` , '$.".$key."')  IN ('".implode("', '", $value)."') ";
		// 	}
		// 	else if($key == 'stock')
		// 	{				
		// 		$_SESSION['stock'] = $value;				
		// 	}
			
		// }
		if (isset($_SESSION['stock']))
		{
			if ($_SESSION['stock'] == 'is')
			{
				$where_filter .= " AND `quantity` > 0 ";
				$s_is = 'class="active"';
			}
			else if ($_SESSION['stock'] == 'no')
			{
				$where_filter .= " AND `quantity` <= 0";
				$s_no = 'class="active"';
			}
			else
			{
				$s_all = 'class="active"';
			}
		}
		else
		{
			$s_all = 'class="active"';
		}
	
		$query = $db->query("SELECT n.*, u.unit, u.sname as unit_sname, m.name as manufacture_name, m.id as manufacture_id FROM `".DB_PREFIX."_catalog` as n LEFT JOIN `".DB_PREFIX."_catalog_unit` as u on(u.unit=n.unit) LEFT JOIN ".DB_PREFIX."_catalog_manufacture as m on(m.id = n.man) WHERE active='1' " . $where . $where_filter. " ORDER BY title ASC LIMIT " . $cut . ", " . $numb . "");	
		if($db->numRows($query) > 0) 
		{	
			$catalog ='';
			$items = '';
			while($catalog = $db->getRow($query)) 
			{
				$tag_list = explode(', ', $catalog['tags']);
				$tag_count = 0;
				$tags = false;			
				foreach($tag_list as $tag) 
				{
					$tag_count++;
					if($tag_count < 6) 
					{
						$tags .= '<a href="catalog/tags/' . $tag . '" title="' . $tag . '">' . ($headTag == $tag ? '<strong>' . $tag . '</strong>' : $tag) . '</a>, ';
					}
				}
				
				
				$catInfo = $catalog['cat'] !== ',0,' ? $core->catInfo('catalog', $catalog['cat']) : '';
				$cat = $catalog['cat'] !== ',0,' ? $core->getCat('catalog', $catalog['cat'], 'short', 3) : '';
				$catalog_link = $catalog['cat'] !== ',0,' ? 'catalog/' : 'catalog/';
				$cat_one = $catalog['cat'] !== ',0,' ? $core->getCat('catalog', $catalog['cat'], 'altname', 1) : 'index';
				$short = $core->bbDecode($catalog['short'], $catalog['id'], true);					
				$core->tpl->loadFile('catalog/catalog.'.(is_array($core->tpl->uniqTag) ? $core->tpl->uniqTag[0] : (empty($core->tpl->uniqTag) ? 'main' : $core->tpl->uniqTag)));	
				include(loadTag('catalog'));		
				$items .= $core->tpl->return_end();						
				unset($tags);
			}		
			list($all) = $db->fetchRow($db->query("SELECT count(n.id) FROM ".DB_PREFIX."_catalog as n WHERE active='1' " . $where . $where_filter));
			$queryM = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_manufacture ORDER BY sum DESC LIMIT 8");
			$manufacture = '';
			if($db->numRows($queryM) > 0) 
			{
				while($catalogM = $db->getRow($queryM)) 
				{
					$core->tpl->loadFile('catalog/catalog.manufacture');
					$core->tpl->setVar('NAME', $catalogM['name']);
					$core->tpl->setVar('MAN_ID', $catalogM['id_1c']);
					$core->tpl->setVar('IMG', '/files/manufacture/'.$catalogM['id'].'.jpg');
					$core->tpl->setVar('SUM', $catalogM['sum']);					
					$manufacture .= $core->tpl->return_end();
				}
			}
			$f_list = 'yes';
			$f_cats = '';			
			$info = $core->tpl->info('Внимание! Данный раздел находится в разработке и возможно не точное отображение информации. Для получения актуальных данных обратиться к нашим менеджерам по телефону +7 (3412) 908-123 и мы обязательно поможем Вам!', 'warning-2', false, false, false, false, false, true);	
			
		} 		
		else 
		{
			$f_list = '';
			$f_cats = '';
			$info = $core->tpl->info($lang['catalog_empty'], 'info-2', false, false, false, false, false, true);
		}
		$fields_array = array();
			$query = $db->query("SELECT n.quantity FROM ".DB_PREFIX."_catalog as n WHERE active='1' " . $where . " ORDER BY title ASC");	
			if($db->numRows($query) > 0) 
			{	
				$numb_is = 0;
				$numb_no = 0;
				while($fff = $db->getRow($query)) 
				{
					if ($fff['quantity'] > 0)
					{
						$numb_is++;
					}
					else
					{
						$numb_no++;
					}					
					// $fields = json_decode($fff['fields'], true);
					// foreach ($fields as $key => $value)
					// {
						
					// 	$fields_array[$key] = $key;
					// }					
				}
				$numb_all = $numb_is+$numb_no;
			}
			$queryXF = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_xfields ORDER BY name ASC");	
			$xfield_array = array();
			if($db->numRows($queryXF) > 0)
			{
				while($xf = $db->getRow($queryXF))
				{	
					$xfield_array[$xf['id_1c']]['id'] = $xf['id'];
					$xfield_array[$xf['id_1c']]['name'] = $xf['name'];
				}
			}
			$queryXFV = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_xfields_values ORDER BY value ASC");	
			$xfield_value_array = array();
			if($db->numRows($queryXFV) > 0)
			{
				while($xfv = $db->getRow($queryXFV))
				{	
					$xfield_value_array[$xfv['id_1c']] = $xfv['value'];
				}
			}
			$xfl = '';
			foreach ($fields as $key => $value)
			{
				$filter .='
					<dt>
                      <span class="category-filter">'. $xfield_array[$key]['name'].'</span>
                      <a href="javascript:void(0)" class="clear" style="display:none">
                        Clear
                      </a>
                    </dt>
                  
                  <dd>
                    <ol>';
                      
                        
						   $queryXFV = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_xfields_values WHERE xfield = '".$xfield_array[$key]['id']."' ORDER BY value ASC");	
				$xfield_value_array = array();
				if($db->numRows($queryXFV) > 0)
				{
					while($xfv = $db->getRow($queryXFV))
					{	
						$filter .= '<div class="form-row">
					<label>
						<input class="checkbox" type="checkbox" name="'.$key.'[]" value="'.$xfv['id_1c'].'" >
						<span class="checkbox-custom"></span>
						<span class="label">'.$xfv['value'].'</span>
					</label>
				</div>';
					}
				} 
						  
						$filter .= '</ol>
					  </dd>';
			}
			$filter .=	'<input type="submit" class="action_button" style="width:100%;  font-size: 14px;  margin-bottom: 0px;" value="Показать">	';	
		$pages = '';		
		$pages .= $core->tpl->pages($page, $numb, $all, 'catalog' . $link . '/{page}', false, true);			
		$core->tpl->loadFile('catalog/catalog.carcase');
		include('carcase_tag.php');
		$core->tpl->end();
	}
}

function manufacture()
{
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang;
	$queryM = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_manufacture ORDER BY name ASC");
	$manufacture = '';
	if($db->numRows($queryM) > 0) 
	{
		while($catalogM = $db->getRow($queryM)) 
		{
			$core->tpl->loadFile('catalog/catalog.manufacture');
			$core->tpl->setVar('NAME', $catalogM['name']);
			$core->tpl->setVar('MAN_ID', $catalogM['id_1c']);
			$core->tpl->setVar('IMG', '/files/manufacture/'.$catalogM['id'].'.jpg');
			$core->tpl->setVar('SUM', $catalogM['sum']);				
			$manufacture .= $core->tpl->return_end();
		}
	}
	$page_title = "Производители";
	$core->tpl->loadFile('catalog/catalog.carcase');
	$f_main = 'yes';
	$f_list = '';
	$f_cats = '';
	include('carcase_tag.php');
	$core->tpl->end();
}



function cart()
{
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang;	
	$info = $core->tpl->info('Внимание! Данный раздел находится в разработке и возможно не точное отображение информации. Для получения актуальных данных обратиться к нашим менеджерам по телефону +7 (3412) 908-123 и мы обязательно поможем Вам!', 'warning-2', false, false, false, false, false, true);
	include_once('cart.php');
	$items = cart_interface();
	$page_title = "Корзина";
	$core->tpl->loadFile('catalog/catalog.carcase');
	$f_cart = 'yes';
	$f_list = '';
	$f_cats = '';
	include('carcase_tag.php');
	$core->tpl->end();
}


function order()
{
	
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang;
	$basket = htmlspecialchars($_COOKIE["basket"]);
	$tovar_array = explode( ',', $basket);	
	foreach ($tovar_array as $key => $value)
	{
		$info_array = explode( ':', $value);	
		$ids_str .= "'".$info_array[0]."', ";
		
	}
	$info = $core->tpl->info('Внимание! Данный раздел находится в разработке и возможно не точное отображение информации. Для получения актуальных данных обратиться к нашим менеджерам по телефону +7 (3412) 908-123 и мы обязательно поможем Вам!', 'warning-2', false, false, false, false, false, true);
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id IN(".substr($ids_str, 0, -2).") ORDER BY title ASC");	
	$items = '';
	if($db->numRows($query) > 0) 
	{
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
			$core->tpl->loadFile('catalog/catalog.cart');
			include(loadTag('catalog'));						
			$items .= $core->tpl->return_end();
			
		}
	}
	$page_title = "Корзина";
	$core->tpl->loadFile('catalog/catalog.carcase');
	$f_cart = 'yes';
	$f_list = '';
	$f_cats = '';
	include('carcase_tag.php');
	$core->tpl->end();
}

function specification($sid = 0)
{	
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang;
	$info = $core->tpl->info('Внимание! Данный раздел находится в разработке и возможно не точное отображение информации. Для получения актуальных данных обратиться к нашим менеджерам по телефону +7 (3412) 908-123 и мы обязательно поможем Вам!', 'warning-2', false, false, false, false, false, true);
	if ($sid == 0)
	{
		if (isset($_SESSION['products']) && !empty($_SESSION['products']))
		{		
			foreach ($_SESSION['products'] as $key => $value) 
			{	
				$ids_str .= "'".$key."', ";
				
			}			
			$query = $db->query("SELECT n.*,  u.unit, u.sname as unit_sname, m.id_1c, m.name as manufacture_name, m.id as manufacture_id  FROM ".DB_PREFIX."_catalog as n LEFT JOIN ".DB_PREFIX."_catalog_unit as u on(u.unit=n.unit) LEFT JOIN ".DB_PREFIX."_catalog_manufacture as m on(m.id_1c = n.manufacture) WHERE n.id IN(".substr($ids_str, 0, -2).") ORDER BY title ASC");
			$items = '';
			$spec_data = array(
				'num' => array(),
				'sku' => array(),
				'unit' => array(),
				'name' => array(),
				'sum' => array(),
				'model' => array(),
				'manufacter' => array()
			);
			if($db->numRows($query) > 0) 
			{
				$i = 0;
				while($catalog = $db->getRow($query)) 
				{	
					$i++;
					$sku = $catalog['sku'];
					$unit = $catalog['unit_sname'];
					$name = $catalog['title'];
					$model = ' ';
					$manufacter = (!empty($catalog['manufacture_name']) ? $catalog['manufacture_name'] : ' ');
					foreach (json_decode($catalog['fields']) as $key => $value)
					{
						if ($key == '48ebbf1d-3bbd-11e8-a468-704d7b330583')
						{
							$name = $value;
						}
						if ($key == '52e71120-3bbd-11e8-a468-704d7b330583')
						{
							$model = $value;
						}
						if ($key == '5d85a200-3bbd-11e8-a468-704d7b330583')
						{
							$manufacter = $value;
						}
					}				
					array_push($spec_data['num'], $i);
					array_push($spec_data['sku'], $sku);
					array_push($spec_data['unit'], $unit);
					array_push($spec_data['name'], $name);
					array_push($spec_data['sum'], $_SESSION['products'][$catalog['id']]['count']);
					array_push($spec_data['model'], $model);
					array_push($spec_data['manufacter'], $manufacter);
					
				}
			}
			require_once(ROOT.'usr/plugins/PHPWord.php');

			$PHPWord = new PHPWord();

			$document = $PHPWord->loadTemplate(ROOT.'/files/tpl/specification.docx');

			$Month_rus = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
			
			/*
			$document->setValue('{day}', date('d'));
			$document->setValue('{month}', $Month_rus[ date( "n" ) - 1 ]);
			$document->setValue('{year}', date('Y'));
			
			$document->setValue('{order}', $order_id);
			
			$document->setValue('{worker}', $user_data["surname"] ." ". $user_data["name"] ." ". $user_data["ochestvo"]);
			
			$document->setValue('{e_id}', $order_data["elevator_id"]);
			$document->setValue('{e_address}', $elevator_data["address"]);*/
			
			
			
			$document->cloneRow('spec', $spec_data);		
			$uid = $core->auth->user_info['id'];
			$document->save('files/user/'.$uid .'_spec.docx');
			$items = '<a href="files/user/'.$uid .'_spec.docx" target="_blank" > Скачать </a>';
			//unset($_COOKIE['basket']);
			header('Location: https://atri-energo.ru/files/user/'.$uid .'_spec.docx');
		}
		else
		{
			$info = $core->tpl->info('Внимание! Корзина пуста, чтобы составить спецификацию необходимо заполнить корзину необходимыми товарами!', 'error-2', false, false, false, false, false, true);
		}
		
	}
		
	$page_title = "Корзина";
	$core->tpl->loadFile('catalog/catalog.carcase');
	$f_cart = 'yes';
	$f_list = '';
	$f_cats = '';
	include('carcase_tag.php');
	$core->tpl->end();
}

function calc_create($procent = 0)
{	
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang;
	$info = $core->tpl->info('Внимание! Данный раздел находится в разработке и возможно не точное отображение информации. Для получения актуальных данных обратиться к нашим менеджерам по телефону +7 (3412) 908-123 и мы обязательно поможем Вам!', 'warning-2', false, false, false, false, false, true);
	
		if (isset($_SESSION['products']) && !empty($_SESSION['products']))
		{		
			foreach ($_SESSION['products'] as $key => $value) 
			{	
				$ids_str .= "'".$key."', ";
				
			}			
			$query = $db->query("SELECT n.*,  u.unit, u.sname as unit_sname, m.id_1c, m.name as manufacture_name, m.id as manufacture_id  FROM ".DB_PREFIX."_catalog as n LEFT JOIN ".DB_PREFIX."_catalog_unit as u on(u.unit=n.unit) LEFT JOIN ".DB_PREFIX."_catalog_manufacture as m on(m.id_1c = n.manufacture) WHERE n.id IN(".substr($ids_str, 0, -2).") ORDER BY title ASC");
			$items = '';
			$spec_data = array(
				'num' => array(),
				'sku' => array(),				
				'name' => array(),
				'price' => array(),
				'price_sum' => array(),
				'sum' => array()
			);
			if (!empty($_POST['pr']))
			{
				if ($_POST['pr']>100)
				{
					$pr =intval($_POST['pr']);
				}
				else
				{
					$pr =100 - intval($_POST['pr']);
				}
			
			}
			else
			{
			$pr = 100;
			}
			if($db->numRows($query) > 0) 
			{
				$i = 0;
				while($catalog = $db->getRow($query)) 
				{	
					$i++;
					$sku = $catalog['sku'];
					$unit = $catalog['unit_sname'];
					$name = $catalog['title'];
					$model = ' ';
					$manufacter = (!empty($catalog['manufacture_name']) ? $catalog['manufacture_name'] : ' ');
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
					$sum_rub = ($sum_rub+$_SESSION['products'][$catalog['id']]['count']*$price_rub)/100*$pr;	
					
					array_push($spec_data['num'], $i);
					array_push($spec_data['sku'], $sku);					
					array_push($spec_data['name'], $name);
					array_push($spec_data['price'], $price_rub/100*$pr.' руб.');
					array_push($spec_data['price_sum'], $price_rub*$_SESSION['products'][$catalog['id']]['count']/100*$pr.' руб.');
					array_push($spec_data['sum'], $_SESSION['products'][$catalog['id']]['count']);
				
					
				}
			}
			require_once(ROOT.'usr/plugins/PHPWord.php');

			$PHPWord = new PHPWord();

			$document = $PHPWord->loadTemplate(ROOT.'/files/tpl/calc_tpl.docx');

			$Month_rus = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
			$document->setValue('{total}', $sum_rub.' руб.');
						
			
			
			$document->cloneRow('spec', $spec_data);		
			$uid = $core->auth->user_info['id'];
			$document->save('files/user/'.$uid .'_calc.docx');
			$items = '<a href="files/user/'.$uid .'_calc.docx" target="_blank" > Скачать </a>';
			header('Location: https://atri-energo.ru/files/user/'.$uid .'_calc.docx');
		}
		else
		{
			$info = $core->tpl->info('Внимание! Корзина пуста, чтобы составить спецификацию необходимо заполнить корзину необходимыми товарами!', 'error-2', false, false, false, false, false, true);
		}
		

}

function view($tran = false)
{
global $db, $config, $core, $tags, $catalog_conf, $url, $headTag, $cache;

		$where = 'altname';
		if($tran)
		{
			$translate = str_replace(array('.html', '.htm'), array('', ''), empty($tran) ? $url[2] : filter($tran, 'a'));
		}
		else
		{
			$translate = str_replace(array('.html', '.htm'), array('', ''), $url[2]);
		}		
		if(is_numeric(empty($tran) ? $url[2] : $tran)) $where = 'id';				
		$query = $db->query("SELECT n.*,  u.unit, u.sname as unit_sname, m.id_1c, m.name as manufacture_name, m.id as manufacture_id, m.id_1c as manufacture_id_1c  FROM ".DB_PREFIX."_catalog as n LEFT JOIN ".DB_PREFIX."_catalog_unit as u on(u.unit=n.unit) LEFT JOIN ".DB_PREFIX."_catalog_manufacture as m on(m.id_1c = n.manufacture) WHERE n." . $where . "='" . $db->safesql($translate) . "' ");
		if($db->numRows($query) == 0)
		{
			location();
		}		
		$catalog = $db->getRow($query);
		$textFull = $catalog['full'];
		$pageContent = init_page('break');
		$pageBreaks = explode('[pagebreak]', $textFull);
		if(!isset($pageBreaks[$pageContent-1])) $pageContent = 1;
		$pagesBr = count($pageBreaks);
		$breakNav = '';
		$textFull = $pageBreaks[$pageContent-1];
		$textShort = $catalog['short'];
		$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET views = views+1 WHERE " . $where . "='" . $db->safesql($translate) . "' LIMIT 1 ;");
		$tag_list = explode(',', $catalog['tags']);
		$tag_count = 0;
		$tags = false;		
		if(!empty($catalog['keywords']))
		{
			$core->tpl->keywords =$catalog['keywords'];
		}
		if(!empty($catalog['description']))
		{
			$core->tpl->description = $catalog['description'];
		}
		foreach($tag_list as $tag) 
		{
			$tag_count++;
			$tag = trim($tag);
			if($tag_count < ($catalog_conf['tags_num']+1)) 
			{
				$tags .= '<a href="catalog/tags/'.urlencode($tag).'" title="' . $tag . '">'.$tag.'</a>, ';
			}
		}		
		if($catalog_conf['tagIll'])
		{
			$tagFormat = '';
			
			foreach($tag_list as $ttag)
			{
				$ttag = trim($ttag);
				$tagFormat .= "'" . $ttag  . "' => '" . str_replace('{tag}', $ttag, $catalog_conf['illFormat'])  . "', ";
			}
			eval('$tagFormated = array(' . $tagFormat . ');');
			$textFull = str_ireplace(array_keys($tagFormated), array_values($tagFormated), $textFull);
			$textShort = str_ireplace(array_keys($tagFormated), array_values($tagFormated), $textShort);		
		}		
		$cat_id = str_replace(',', '', $catalog['cat']);
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_categories WHERE id = '" . $cat_id . "'");	
		$cat_for = $db->getRow($query);		
	
		if($pagesBr > 1 && $pageBreaks[1] !== '')
		{
			if(isset($pageBreaks[$pageContent-2]))
			{
				$breakNav .= "<a href=\"" . $catalog_link . 'break/'. ($pageContent-1) . '/' . $catalog['altname'] . ".html\" title=\"Назад\">" . _PREVIOUS_PAGE . "</a>";
			}			
			if(isset($pageBreaks[$pageContent-2]) && isset($pageBreaks[$pageContent])) $breakNav .= ' | ';			
			if(isset($pageBreaks[$pageContent]))
			{
				$breakNav .= "<a href=\"" . $catalog_link . 'break/'. ($pageContent+1) . '/' . $catalog['altname'] . ".html\" title=\"" . _NEXT_PAGE . "\">" . _NEXT_PAGE . "</a>";
			}
		}	
		$core->tpl->uniqTag = 'view';
		$ptitle = stripcslashes($catalog['title']);
		if(!empty($catalog['fulltitle']))
		{
			$ptitle =stripcslashes($catalog['fulltitle']);
		}
		$page_title = $ptitle;
		set_title(array($catalog['name'], $ptitle));
		$catInfo = $catalog['cat'] !== ',0,' ? $core->catInfo('catalog', $catalog['cat']) : '';
		$cat = $catalog['cat'] !== ',0,' ? $core->getCat('catalog', $catalog['cat'], 'short', 3) : '';
		$catalog_link = $catalog['cat'] !== ',0,' ? 'catalog/' . $core->getCat('catalog', $catalog['cat'], 'development') . '/' : 'catalog/';
		$cat_one = $catalog['cat'] !== ',0,' ? $core->getCat('catalog', $catalog['cat'], 'altname', 1) : 'index';
		$short = $core->bbDecode($textShort, $catalog['id'], true);
		$miniImg = _getCustomImg($short);
		$core->tpl->loadFile('catalog/catalog.'.(is_array($core->tpl->uniqTag) ? $core->tpl->uniqTag[0] : (empty($core->tpl->uniqTag) ? 'main' : $core->tpl->uniqTag)));
		$queryXF = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_xfields ORDER BY name ASC");	
		$xfield_array = array();
		if($db->numRows($queryXF) > 0)
		{
			while($xf = $db->getRow($queryXF))
			{	
				$xfield_array[$xf['id_1c']] = $xf['name'];
			}
		}
		$queryXFV = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_xfields_values ORDER BY value ASC");	
		$xfield_value_array = array();
		if($db->numRows($queryXFV) > 0)
		{
			while($xfv = $db->getRow($queryXFV))
			{	
				$xfield_value_array[$xfv['id_1c']] = $xfv['value'];
			}
		}
		$xfl = '';
		foreach (json_decode($catalog['fields']) as $key => $value)
		{
			if ($key != '22dc8222-d435-11e7-a44a-704d7b330583' && $key != 'd8cd7b43-162e-11e8-a456-704d7b330583' && $key != 'f79da02b-162e-11e8-a456-704d7b330583')
			{
			$xfl .= $xfield_array[$key].': '.$xfield_value_array[$value].'<br>';
			}
		}
			$bb = new bb;
		include(loadTag('catalog'));
		$cert = '[attach=174]';
		$core->tpl->setVar('XF', $xfl);
		$core->tpl->setVar('CERT', parseCert($catalog['id']));
		$items = $core->tpl->return_end();		
		$pages = '';		
		$core->tpl->loadFile('catalog/catalog.carcase');
		$f_main = '';
		$f_list = '';
		$f_cats = '';
		$f_view = 'yes';
		include('carcase_tag.php');
		$core->tpl->end();
}

function load_tpl($file)
	{
		global $core, $config;
		$loadDefault = 'usr/tpl/default/' . $file . $core->tpl->ext;
		$loadTheme = 'usr/tpl/'.$config['tpl'].'/' . $file . $core->tpl->ext;
		if (!isset($loadTheme)) 
		{
			$text = file_get_contents(ROOT . $loadDefault);		
		}
		else
		{
			$text = file_get_contents(ROOT . $loadTheme);		
		}
		return $text;
	}
	
function parseCert($pubId)
{
	global $core, $db, $lang;	
	$pubId = intval($pubId);
	$q = $db->query("SELECT * FROM `" . DB_PREFIX . "_cert_relation` WHERE `cid`='" . $pubId . "'");		
	if($db->numRows($q) > 0) 
	{
		$first = load_tpl('attach_cert');
		$stat = $first;
		$position=strpos($stat,'[static]');
		$stat=substr($stat,$position);
		$position=strpos($stat,'[/static]');
		$stat=substr($stat,0,$position);
		$stat = preg_replace( "#\\[static]#is", '', $stat);				
		$first = preg_replace( "#\\[static](.*?)\\[/static]#is", '', $first);				
		while($rows = $db->getRow($q))
		{				
			$query = $db->query("SELECT r.*, t.print FROM ".DB_PREFIX."_cert as r LEFT JOIN ".DB_PREFIX."_cert_type as t on (r.type = t.id) WHERE r.id = '" . $rows['cert_id'] . "'");		
			$cat = $db->getRow($query);		
			$queryA = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE id = '" . $cat['aid'] . "'");		
			$catA = $db->getRow($queryA);			
			$replace = $first;
			$info = new SplFileInfo($catA['url']);
			$replace = str_replace('{%EXT%}', $info->getExtension(), $replace);
			$replace = str_replace('{%NUMB%}', $catA['downloads'], $replace);
			$replace = str_replace('{%SIZE%}', formatfilesize(@filesize($catA['url']), true), $replace);
			$replace = str_replace('{%ID%}', $catA['id'], $replace);
			$replace = str_replace('{%FDATE%}', formatdate($catA['date'], true), $replace);
			$replace = str_replace('{%NAME%}', str_replace('{NUMB}', $cat['numb'], $cat['print']), $replace);						
			$text .= $replace;
		}
	}		
	return stripslashes($text);
}
	

function revision() 
{
		global $core, $db, $core, $config, $catalog_conf, $user;
	$query = $db->query("SELECT n.*,  u.unit, u.sname as unit_sname, m.id_1c, m.name as manufacture_name, m.id as manufacture_id, m.id_1c as manufacture_id_1c FROM ".DB_PREFIX."_catalog as n LEFT JOIN ".DB_PREFIX."_catalog_unit as u on(u.unit=n.unit) LEFT JOIN ".DB_PREFIX."_catalog_manufacture as m on(m.id_1c = n.manufacture) WHERE n.cat IN(',179,',',180,',',181,',',182,',',183,',',184,',',185,',',186,')");	
	$table = ' <table id="table2excel" class="table table-striped">
                    <thead>
						<tr>
							<th><span class="pd-l-sm"></span>#</th>
							<th>Артикул</th>
							<th>Наименование</th>
							<th>Остаток</th>
							<th>Цена</th>								
						</tr>
                    </thead>
                    <tbody>';
	if($db->numRows($query) > 0) 
	{	
		while($catalog = $db->getRow($query)) 
		{
			$table .=  '<tr>
					<td>'.$catalog['id'].'</td> 
					<td> '.$catalog['sku'].' </td> 
					<td>'.$catalog['title'].'</td> 
					<td>'.$catalog['quantity'].'</td>
						<td>'.$catalog['barcode'].'</td>
				</tr>';
		}
	}
	$table .= ' </tbody>
	</table>';
	$table .= '
	
  ';
			$core->tpl->loadFile('catalog/catalog.table');
			$core->tpl->setVar('TABLE', $table);
			$core->tpl->end();	
}


function cats_id() 
{
		global $core, $db, $core, $config, $catalog_conf, $user;
	$query = $db->query("SELECT id ,name  FROM ".DB_PREFIX."_catalog_cats WHERE parent_id != '0'");		
	$table = ' <table id="table2excel" class="table table-striped">
                    <thead>
						<tr>
							<th><span class="pd-l-sm"></span>#</th>
						
							<th>Наименование</th>								
						</tr>
                    </thead>
                    <tbody>';
	if($db->numRows($query) > 0) 
	{	
		while($catalog = $db->getRow($query)) 
		{
			$table .=  '<tr>
					<td>'.$catalog['id'].'</td> 
				
					<td>'.$catalog['name'].'</td> 
				</tr>';
		}
	}
	$table .= ' </tbody>
	</table>';
	$table .= '
	
  ';
			$core->tpl->loadFile('catalog/catalog.table');
			$core->tpl->setVar('TABLE', $table);
			$core->tpl->end();	
}

function sync() 
{
	global $core, $db, $url, $lang;

	require_once(ROOT.'sync.php');
	$msg = '';
	$author = 'admin';
	$ttime = time();
	$date = !empty($_POST['date']) ? filter($_POST['date']) : $ttime;	
	$info = '';
	//очистка таблиц
	//$db->query("DELETE FROM `JMY_langs` WHERE  module='catalog'");
	//$db->query("TRUNCATE JMY_catalog");
	//$db->query("TRUNCATE JMY_catalog_cats");
	//$db->query("DELETE FROM `JMY_categories` WHERE `module` = 'catalog'	");
	//$db->query("TRUNCATE JMY_catalog_manufacture");
	
	//импорт категорий
	$queryCats = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_cats");	
	$cats_array = array();
	if($db->numRows($queryCats) > 0)
	{
		while($resultCats = $db->getRow($queryCats))
		{	
			$cats_array[$resultCats['id_1c']] = $resultCats['cid'];
		}
	}	
	if(file_exists(ROOT . 'tmp/cache/categories.cache'))
		{
			@unlink(ROOT . 'tmp/cache/categories.cache');
		}
	
	foreach ($arr_category as $items_cats)
	{		
				
		if(!isset($cats_array[$items_cats->id]))
		{
			$catsParent['cid'] = 0;	
			if (!empty($items_cats->parent))
			{
				$queryCatsParent = $db->query("SELECT cid FROM ".DB_PREFIX."_catalog_cats WHERE id_1c = '".$items_cats->parent."' LIMIT 1");
				$catsParent = $db->getRow($queryCatsParent);
			}			
			$name = filter($items_cats->name);
			$altname = rand(1, 999).'-'.translit($name);
			$db->query("INSERT INTO `" . DB_PREFIX . "_categories` (`name`, `altname`, `parent_id`, `module`) VALUES ('" . $db->safesql($name) . "', '" . $altname . "', '" . $catsParent['cid'] . "', 'catalog');");			
			$db->query("INSERT INTO `" . DB_PREFIX . "_catalog_cats` (`id_1c`, `cid`) VALUES ('" . $items_cats->id . "', '" . $db->insert_id() . "');");
			$info .= str_replace(array('[name]', '[altname]'), array($name, $altname), $lang['cats_fast_save']);
		}		
	}
	//чистим
	//$db->query("TRUNCATE JMY_catalog_xfields");	
	//$db->query("TRUNCATE JMY_catalog_xfields_values");	
	//импорт дополнительных реквизитов и их значений
	$info = '';
	$queryItemsxf = $db->query("SELECT id, id_1c FROM ".DB_PREFIX."_catalog_xfields");
	$xf_array = array();
	if($db->numRows($queryItemsxf) > 0)
	{
		while($xf = $db->getRow($queryItemsxf))
		{	
			$xf_array[$xf['id_1c']] = $xf['id'];
		}
	}
	$queryItemsxf_v = $db->query("SELECT id, id_1c FROM ".DB_PREFIX."_catalog_xfields_values");
	$xf_v_array = array();
	if($db->numRows($queryItemsxf_v) > 0)
	{
		while($xf_v = $db->getRow($queryItemsxf_v))
		{	
			$xf_v_array[$xf_v['id_1c']] = $xf_v['id'];
		}
	}
	
	foreach ($arr_property as $items_property)
	{
		switch($items_property->type) 
		{
			default:	
				$ptype = 0;
			break;	
			case "Справочник":
				$ptype = 1;
			break;
			case "Строка":
				$ptype = 2;
			break;
			case "Число":
				$ptype = 3;
			break;			
		}		
		if (isset($xf_array[$items_property->id]))
		{
			//$db->query("INSERT INTO `" . DB_PREFIX . "_catalog_xfields` (`id_1c`, `name`, `active`) VALUES ('" . $items_property->id . "', '" . $db->safesql($items_property->name) . "', '0');");
			echo str_replace(array('[name]'), array($items_property->name), 'Реквизит [name] обновлен!<br>');
		}
		else
		{
			$db->query("INSERT INTO `" . DB_PREFIX . "_catalog_xfields` (`id_1c`, `name`, `type`, `active`) VALUES ('" . $items_property->id . "', '" . $db->safesql($items_property->name) . "', '" . $db->safesql($ptype) . "', '0');");
			echo str_replace(array('[name]'), array($items_property->name), 'Реквизит [name] добавлен!<br>');
		}
		
		if (!empty($items_property->values))
		{
			foreach($items_property->values as $key => $value) 				
			{
				if (isset($xf_v_array[$key]))
				{
					//$db->query("INSERT INTO `" . DB_PREFIX . "_catalog_xfields` (`id_1c`, `name`, `active`) VALUES ('" . $items_property->id . "', '" . $db->safesql($items_property->name) . "', '0');");
					echo str_replace(array('[name]'), array($items_property->name), 'Реквизит [name] обновлен!<br>');
				}
				else
				{
					$queryP = $db->query("SELECT id FROM ".DB_PREFIX."_catalog_xfields WHERE id_1c = '".$items_property->id."' LIMIT 1");
					$property = $db->getRow($queryP);
					$db->query("INSERT INTO `" . DB_PREFIX . "_catalog_xfields_values` (`id_1c`, `xfield`, `value`) VALUES ('" . $key . "', '" . $property['id'] . "', '".$value."');");
					echo str_replace(array('[name]', '[xfield]'), array($value, $items_property->name), '+ Значение [name] для реквизта [xfield] добавлено!<br>');			
				}	
						
			}
		}			
	}	
	$i = 0;
	//импорт товаров остатков и цен	
	$queryItems = $db->query("SELECT id, id_1c FROM ".DB_PREFIX."_catalog");
	$item_array = array();
	if($db->numRows($queryItems) > 0)
	{
		while($org = $db->getRow($queryItems))
		{	
			$item_array[$org['id_1c']] = $org['id'];
		}
	}
	foreach ($arr as $items )
	{
		$i++;
		if (!empty($items->name))
		{
			$price = $items->price;
			$properties = array();
			$active = 0;	
			$allow_actual = 0;
			$preview = !empty($items->images) ? '/files/import/'.$items->images : false;	
			$manufacture = !empty($items->manufacture_id) ? $items->manufacture_id : false;			
			$sku = !empty($items->sku) ? $items->sku : false;			
			$description = !empty($items->description) ? $items->description : false;
			$unit = !empty($items->unit) ? $items->unit : false;
			if (!empty($items->properties))
			{
				$properties = $items->properties;		
				if (!empty($properties['a2fb1c2b-871a-11e7-a43e-704d7b330583']))
				{
					$active = (($properties['a2fb1c2b-871a-11e7-a43e-704d7b330583'] == 'Да') ? '1' : '0');
					unset($properties['a2fb1c2b-871a-11e7-a43e-704d7b330583']);
				}		
				if (!empty($properties['0c5ab4a8-871b-11e7-a43e-704d7b330583']))
				{
					$allow_actual = (($properties['0c5ab4a8-871b-11e7-a43e-704d7b330583'] == 'Да') ? '1' : '0');
					unset($properties['0c5ab4a8-871b-11e7-a43e-704d7b330583']);
				}
			}
			if (!empty($items->manufacture_id))
			{
				$queryItems = $db->query("SELECT id FROM ".DB_PREFIX."_catalog_manufacture WHERE id_1c = '".$items->manufacture_id."' LIMIT 1");
				if($db->numRows($queryItems) > 0)
				{
					$update = '';
				}
				else
				{
					$insert = $db->query("INSERT INTO `" . DB_PREFIX . "_catalog_manufacture` ( `id` , `id_1c`, `name`) VALUES (NULL, '" . $items->manufacture_id . "', '" . $items->manufacture_name . "');");
				}
			}			
			if(isset($item_array[$items->id]))
			{
				$short = '';
				if ($description != false)
				{
					$short = " `short` = '".$description."', ";
				}
				$preview_upd = '';
				if ($preview != false)
				{
					$preview_upd = " `preview` = '" . $preview . "', ";
				}
				
				$update = $db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `fields` = '" . json_encode($properties, JSON_UNESCAPED_UNICODE) . "', `title` = '" . $db->safesql(processText($items->name)) . "', `fulltitle` = '" . $db->safesql(processText($items->name)) . "', ".$short.$preview_upd."  `quantity` = '" . $items->quantity . "', `barcode` = '" . $items->barcode . "', `active` = '" . $active . "', `price` = '" . json_encode($price, JSON_UNESCAPED_UNICODE) . "', `sku` = '" . $sku . "',  `cat` = '," . $cats_array[$items->categories[0]] . ",', `manufacture` = '" . $manufacture . "', `unit` = '" . $unit . "' WHERE `id` = '" . $item_array[$items->id] . "' LIMIT 1 ;");						
				echo  $items->name.': обновлён!<br>';
			}
			else
			{
				$ntitle = filter(trim(htmlspecialchars_decode($items->name, ENT_QUOTES)), 'title');
				$ftitle = filter(trim(htmlspecialchars_decode($items->name, ENT_QUOTES)), 'title');	
				$insert = $db->query("INSERT INTO `" . DB_PREFIX . "_catalog` ( `id` , `id_1c`, `sku`, `cat`, `unit`, `title`, `fulltitle`, `short`, `author` , `date`, `price`, `quantity`, `altname`, `barcode`, `preview`, `manufacture`, `allow_comments` , `allow_rating` , `allow_index` , `allow_actual` ,`score` , `votes` , `views` , `comments`, `fields`, `groups` , `fixed` , `active` ) VALUES (NULL, '" . $items->id . "', '" . $sku . "', '," . $cats_array[$items->categories[0]] . ",', '" . $unit . "', '" . $db->safesql(processText($ntitle))  . "', '" . $db->safesql(processText($ntitle))  . "', '" . $description . "', '" . $author . "', '" . $date . "', '" . json_encode($price, JSON_UNESCAPED_UNICODE) . "', '" . $items->quantity . "', '" . rand(100, 999).$db->safesql(translit($items->name)) . "', '" . $items->barcode . "', '" . $preview . "', '" . $manufacture . "', '1', '1', '0', '0', '".$allow_actual."', '0', '0', '0', '".json_encode($properties, JSON_UNESCAPED_UNICODE)."', ',0,', '0', '".$active."');");
				if (!empty($items->manufacture_id))
				{
					$db->query("UPDATE `" . DB_PREFIX . "_catalog_manufacture` SET sum=sum+1 WHERE `id_1c` ='" . $items->manufacture_id . "' LIMIT 1", true);
				}
				if($insert) 
				{
					$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id_1c = '" . $items->id . "'");
					$catalog = $db->getRow($query);							
					$dir = ROOT.'files/catalog/'.$catalog['id'];
					if (!file_exists($dir))
					{
						mkdir($dir, 0777);
					}		
				}	
				
				echo $items->name.': добавлен!<br>';				
			}
		}
		if ($i % 50 == 0)
			usleep(100);
		
		
		
	}
	
}

function sync_m()
{
	global $core, $db;
	$queryM = $db->query("SELECT manufacture FROM ".DB_PREFIX."_catalog WHERE manufacture <> '' and active=1");
	$manufacture = array();
	if($db->numRows($queryM) > 0) 
	{
		while($catalogM = $db->getRow($queryM)) 
		{			
			$manufacture[$catalogM['manufacture']] = $manufacture[$catalogM['manufacture']] + 1;
		}
	}
	foreach($manufacture as $key => $value) 
	{
		$update = $db->query("UPDATE `" . DB_PREFIX . "_catalog_manufacture` SET `sum` = '" . $value . "' WHERE `id_1c` = '" . $key . "' LIMIT 1 ;");
	}	
}

switch(isset($url[1]) ? $url[1] : null) 
{
	default:	
		errorLoad();
		// if(eregStrt('.htm', mjsEnd($url)))
		// {
		// 	view(mjsEnd($url));
		// }
		// else
		// {
		// 	main(isset($url[1]) && $url[1] != 'page' ? $url[1] : false);
		// }
	break;	
	
	// case "cat":
	// 	main($url[2]);
	// break;
	
	// case "specification":
	// 	specification();
	// break;
	
	// case "search":
	// 	main('search');
	// break;
	
	// case "order":
	// 	order();
	// break;
	
	// case "date":
	// 	main('date');
	// break;
	
	// case "view":
	// 	view();
	// break;
		
	// case "sync":
	// 	$uid = $core->auth->user_info['id'];
	// 	if ($uid == '1')
	// 	{
	// 		sync();
	// 		sync_m();
	// 	}
	// 	else
	// 	{
	// 		location('/');
	// 	}
	// break;
		
	// case "sync_m":
	// 	sync_m();
	// break;
		
	// case "manufacture":
	// 	manufacture();
	// break;
		
	// case "cart":
	// 	cart();		
	// break;
	
	// case "calc":
	// 	calc_create();		
	// break;
		
	// case "revision":
	// 	revision();
	// break;
		
	// case "catsid":
	// 	cats_id();
	// break;
	
	// case "ajax":
	// 	include_once('cart.php');
	// 	switch(isset($url[2]) ? $url[2] : null) 
	// 	{
	// 		default:
	// 			if (empty($_SESSION['products'])) {
	// 				echo 'Ваша корзина пуста';
	// 			}
	// 			else {
	// 				echo 'Товаров в корзине '.$_SESSION['products_incart'].' на сумму '.$_SESSION['cart_coast'];
	// 			}
	// 			unset($_SESSION['products_incart']);
	// 			unset($_SESSION['cart_coast']);
	// 			unset($_SESSION['products']);
				
	// 		break;	
	
	// 		case "add_to_cart":
	// 			add_to_cart($_POST['product_id']);
	// 		break;
			
	// 		case "update_cart":
	// 			echo 'Корзина: <span class="icon cart" aria-hidden="true"></span><span class="price"><span data-of="totalPrice"><span id="totalPrice">'.$_SESSION['cart_coast'].'</span> руб.</span></span><span class="badge" data-toggle="visible" data-of="totalCount" style=""><span id="totalGoods">'.$_SESSION['products_incart'].'</span></span>';
	// 			exit;
	// 		break;
			
	// 		case "update_product_count":
	// 			update_product_count($_POST['product_id'], $_POST['count']);
	// 			exit;
	// 		break;
			
	// 		case "remove_from_cart":
	// 			remove_from_cart($_POST['product_id']);
	// 			exit;
	// 		break;
			
	// 		case "cart_interface":
	// 			echo cart_interface();
	// 			exit;
	// 		break;
			
	// 		case "ddd":
	// 			$q = $db->query("SELECT id FROM `" . DB_PREFIX . "_catalog` WHERE  `title` LIKE '%Бобышка%'");		
	// 			if($db->numRows($q) > 0) 
	// 			{
	// 				while($rows = $db->getRow($q))
	// 				{
	// 					$insert = $db->query("INSERT INTO `" . DB_PREFIX . "_cert_relation` ( `id` , `cid`, `cert_id`, `status`) VALUES (NULL, '" . $rows['id'] . "', '16', '1');");
	// 				}
	// 			}	
	// 		break;
			
			
			
	// 	}
	// break;
	
	
	
	// case "tags":
	// 	if(isset($url[2])) 
	// 	{
	// 		main('tag');
	// 	}
	// 	else
	// 	{
	// 		set_title(array(_catalog, _TAGS));			
	// 		$tag_query = $db->query("SELECT tag FROM " . DB_PREFIX . "_tags");
	// 		while($tag = $db->getRow($tag_query)) 
	// 		{
	// 			$tags[] = $tag['tag'];
	// 		}
	// 		$tag_cloud = new TagsCloud;
	// 		$tags_list = $tag_cloud->get_cloud($tags); 
	// 		$core->tpl->title(_TAGS_ALL . ': ' . count($tags_list));
	// 		$core->tpl->open();			
	// 		echo '<div align="center">' . "\n";			
	// 		foreach ($tags_list as $tag) 
	// 		{
	// 			echo $tag.' ';
	// 		}			
	// 		echo '</div>' . "\n";
	// 		$core->tpl->close();
	// 	}
	// 	break;
		
		

		
}

