<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2020 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
 
if (!defined('ACCESS')) {
    header('Location: /');
    exit;
}

loadConfig('certificates');

function view2()
{
	global $db, $config, $core, $tags, $cert_conf, $url, $headTag, $cache;
	//Хлебные крошки
	$core->tpl->breadcrumbs[0] = array('name' => 'Главная', 'url' => '/', 'active' => '0');
	$core->tpl->breadcrumbs[1] = array('name' => 'Каталог', 'url' => '/catalog_v2', 'active' => '0');
	$core->tpl->breadcrumbs[2] = array('name' => 'Теплообменное оборудование', 'url' => '/catalog_v2/teploobmennoe_oborudovanie', 'active' => '0');	
	if(!isset($url[1]))
	{
		$core->tpl->breadcrumbs[3] = array('name' => 'Типоразмер теплообменников', 'url' => '/pto', 'active' => '1');		
		//список теплообменников
		$ptoList = '';
		$ptoListBannerArray = array();	
		$ptoArray = \Pto\Model\Pto::where("status", 1)->get()->toArray();
		foreach($ptoArray as $value) {
			$core->tpl->loadFile('pto/pto.list');
			$core->tpl->setVar('PTO_TITLE', $value['title']);
			$core->tpl->setVar('PTO_DN', $value['dn']);
			$core->tpl->setVar('PTO_SP', $value['sp']);
			$core->tpl->setVar('PTO_ST', $value['st']);
			$core->tpl->setVar('PTO_URL', '/pto/'.$value['altname'].'.html');
			$ptoList .= $core->tpl->return_end();
			$name = mb_strtoupper(str_replace('НН№', '', $value['title']));
			$name = str_replace('М', 'M', $name);
			$name = str_replace('Е', 'E', $name);				
			$ptoListBannerArray[] = array(
				'name' => $name,
				'link' => $config['url'].'/pto/'.$value['altname'].'.html',
			);
		}
		$page_title = 'Типоразмер теплообменников';
		$core->tpl->loadFile('pto/pto.main');	
		$core->tpl->setVar('PTO_LIST', $ptoList);
		$core->tpl->setVar('PTO_LIST_JSON', json_encode($ptoListBannerArray, JSON_UNESCAPED_UNICODE));
		$items = $core->tpl->return_end();			
		$breadcrumbs = $core->tpl->get_breadcrumbs();
		$pages = '';
		$comments = '';		
		$info = '';
		$core->tpl->loadFile('pto/pto.carcase');
		include('carcase_tag.php');
		$core->tpl->end();
		
	}
	else
	{
		
		$translate = str_replace(array('.html', '.htm'), array('', ''), $url[1]);
		$query = $db->query("SELECT p.*, m.name as man_name FROM ".DB_PREFIX."_pto as p LEFT JOIN ".DB_PREFIX."_catalog_manufacture AS m ON (p.man_id = m.id) WHERE p.altname = '". $db->safesql($translate) . "' LIMIT 1");
		if($db->numRows($query) == 0)
		{
			errorLoad(); 
		}
		else
		{
			$core->tpl->breadcrumbs[3] = array('name' => 'Типоразмер теплообменников', 'url' => '/pto', 'active' => '0');
			$pto = $db->getRow($query);	
			$page_title = 'Теплообменник пластинчатый разборный '.$pto['title'];
			$core->tpl->breadcrumbs[4] = array('name' => $page_title, 'url' => '/pto/'.$pto['altname'].'.html', 'active' => '1');
			if(!empty($pto['keywords']))
			{
				$core->tpl->keywords =$pto['keywords'];
			}
			if(!empty($pto['description']))
			{
				$core->tpl->description = $pto['description'];
			}
			if(!empty($pto['preview']))
			{
				$preview_array = json_decode($pto['preview'], true);
				$core->tpl->preview = $config['url'].$preview_array['full'];
			}
			$core->tpl->loadFile('pto/pto.view');	
			$core->tpl->setVar('TITLE', $pto['title']);
			$core->tpl->setVar('IMG_2D', $pto['img_2d']);
			$core->tpl->setVar('IMG_3D', $pto['img_3d']);
			//параметры пто
			$core->tpl->setVar('PTO_MAN', $pto['man_name']);
			$core->tpl->setVar('PTO_T_FROM', $pto['t_from']);
			$core->tpl->setVar('PTO_T_TO', $pto['t_to']);
			$core->tpl->setVar('PTO_Q_MAX', $pto['q_max']);
			$core->tpl->setVar('PTO_P_MAX', $pto['p_max']);
			//материал прокладок
			$gt = \Pto\Model\Pto::getGasketType();
			$gt_array = json_decode($pto['gasket_type'], true);
			$gt_list_array = array();
			foreach($gt_array as $key => $value) {
				$gt_list_array[] = $gt[$value];
			}
			$core->tpl->setVar('PTO_GASKET_TYPE', implode(", ", $gt_list_array));
			//материал пластин 
			$pt = \Pto\Model\Pto::getPlatesType();
			$pt_array = json_decode($pto['plates_type'], true);
			$pt_list_array = array();
			foreach($pt_array as $key => $value) {
				$pt_list_array[] = $pt[$value];
			}
			$core->tpl->setVar('PTO_PLATES_TYPE', implode(", ", $pt_list_array));
			//теплоноситель
			$coolant = \Pto\Model\Pto::getCoolant();
			$coolant_array = json_decode($pto['coolant'], true);
			$coolant_list_array = array();
			foreach($coolant_array as $key => $value) {
				$coolant_list_array[] = $coolant[$value];
			}
			$core->tpl->setVar('PTO_COOLANT', implode(", ", $coolant_list_array));
			//основной тип пластин
			$main_plates = \Pto\Model\Pto::getPlatesType();
			$main_plates_array = json_decode($pto['main_plates'], true);
			$main_plates_list_array = array();
			foreach($main_plates_array as $key => $value) {
				$main_plates_list_array[] = $main_plates[$value];
			}
			$core->tpl->setVar('PTO_MAIN_PLATES', implode(", ", $main_plates_list_array));
			//промежуточный тип пластин
			$other_plates = \Pto\Model\Pto::getOtherPlates();
			$other_plates_array = json_decode($pto['other_plates'], true);
			$other_plates_list_array = array();
			foreach($other_plates_array as $key => $value) {
				$other_plates_list_array[] = $other_plates[$value];
			}
			$core->tpl->setVar('PTO_OTHER_PLATES', implode(", ", $other_plates_list_array));
			$core->tpl->setVar('PTO_SP', $pto['sp']);
			$core->tpl->setVar('PTO_ST', $pto['st']);
			$core->tpl->setVar('PTO_WARRANTY', $pto['warranty']);
			$pViewArray = \Pto\Model\PtoView::where("pto_id", $pto['id'])->get()->toArray();
			$plates_min = 10000;
			$plates_max = 0;
			$items_cad = '';
			$items_view = '';
			$rp = 0;
			$pViewSetting = array();
			if(!empty($pViewArray))
			{
				foreach($pViewArray as $key => $value) {
					if(isset($pViewSetting[$value['p']])){
						$pViewSetting[$value['p']]++;
					}
					else {
						$pViewSetting[$value['p']] = 1;
					}						
				}				
				foreach($pViewArray as $key => $value) {
					if($plates_min > $value['plates_min'])
					{
						$plates_min = $value['plates_min'];
					}
					if($plates_max < $value['plates_max'])
					{
						$plates_max = $value['plates_max'];
					}
					if(($value['cad_2d'] != '1')||($value['cad_3d'] != '1')||($value['cad_revit'] != '1')) {
						$items_cad .= '<div class="row"><div class="col-12"><h6 class="h2">Чертежи и модели для ПТО '.$pto['title'].', рама №'.$value['numb'].' (Расчетное давление '.$value['p'].' МПа) </h6></div></div><div class="document-group__documents">';
						if($value['cad_2d'] != '1') {										
							$items_cad .= getAttach(array('1' => $value['cad_2d'], '2' => '2D чертеж ПТО '.$pto['title'].', рама №'.$value['numb'].' ('.$value['p'].' МПа)'));
						}
						if($value['cad_3d'] != '1') {										
							$items_cad .= getAttach(array('1' => $value['cad_3d'], '2' => '3D модель ПТО '.$pto['title'].', рама №'.$value['numb'].' ('.$value['p'].' МПа)'));
						}
						if($value['cad_revit'] != '1') {										
							$items_cad .= getAttach(array('1' => $value['cad_revit'], '2' => 'BIM Revit ПТО '.$pto['title'].', рама №'.$value['numb'].' ('.$value['p'].' МПа)'));
						}
						$items_cad .='</div>';
					}
					if($rp != $value['p'])
					{
						$items_view .= '<tr><td colspan="9">Расчетное давление '.$value['p'].' МПа ('. ($value['p']*10) .' кгс/см<sup>2</sup>)</td></tr>'; 
						$rp = $value['p'];
						$items_view .= '<tr>
											<td rowspan="'.$pViewSetting[$value['p']].'">'.$pto['title'].'</td>
											<td>'.$value['numb'].'</td>
											<td>'.$value['l'].'</td>
											<td>'.$value['l1'].'</td>
											<td>'.$value['l2'].'</td>
											<td rowspan="'.$pViewSetting[$value['p']].'">'.$value['hairpin_type'].'</td>
											<td rowspan="'.$pViewSetting[$value['p']].'">'.$value['hairpin_count'].'</td>
											<td>'.$value['plates_min'].'-'.$value['plates_max'].'</td>
											<td>'.$value['weight'].'</td>
										</tr>'; 
						
					}
					else
					{
						$items_view .= '<tr>											
											<td>'.$value['numb'].'</td>
											<td>'.$value['l'].'</td>
											<td>'.$value['l1'].'</td>
											<td>'.$value['l2'].'</td>
											<td>'.$value['plates_min'].'-'.$value['plates_max'].'</td>
											<td>'.$value['weight'].'</td>
										</tr>'; 
					}
					
				
				}
			}
			$core->tpl->setVar('PTO_PLATES_MIN', $plates_min);
			$core->tpl->setVar('PTO_PLATES_MAX', $plates_max);
			$core->tpl->setVar('PTO_CAD', $items_cad);
			$core->tpl->setVar('PTO_VIEW', $items_view);
			$items = $core->tpl->return_end();				
			$pages = '';
			$comments = '';
			
			$info = '';
			$breadcrumbs = $core->tpl->get_breadcrumbs();
			//$pages .= $core->tpl->pages($page, $numb, $all, 'catalog' . $link . '/{page}', false, true);	
			$core->tpl->loadFile('pto/pto.carcase');
			include('carcase_tag.php');
			$core->tpl->end();
			
		}
	}
}

switch(isset($url[1]) ? $url[1] : null) 
{
	default:	
		view2();
	break;			
}