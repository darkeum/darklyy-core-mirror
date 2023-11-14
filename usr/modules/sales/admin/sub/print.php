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
require_once(ROOT."/vendor/autoload.php");
require_once(ROOT.'usr/plugins/PHPWord.php');

function print_sales($id, $type = null, $print = true){
	\Sales\Support\SalesPrintSupport::print_sales($id, $type, $_GET);
}

function universal_print($request){
	Sales\Support\SalesPrintSupport::universal_print($request);
}

function doc_create()
{	
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang, $adminTpl;	
	$uid = $core->auth->user_info['id'];		
	$id = intval($_POST['id']);
	$org_id = intval($_POST['org']);
	$date = strtotime($_POST['date_doc']);	
	//$date_stamp = datetostamp($_POST['date_doc']);
	if (!empty($id))
	{
		$query_static = $db->query("SELECT * FROM ".DB_PREFIX."_sales WHERE id = '" . $id . "'");
		$order = $db->getRow($query_static);
		$pr = isset($_POST['pr']) ? intval($_POST['pr']) : 0;
		parseCertById($order['oid']);		
		$query_bank = $db->query("SELECT * FROM ".DB_PREFIX."_organizations_bank WHERE oid = '".$order['oid']."' LIMIT 1");		
		$query_org = $db->query("SELECT * FROM ".DB_PREFIX."_organizations WHERE id = '".$order['oid']."' LIMIT 1");		
		$org = $db->getRow($query_org);
		
		
		if($db->numRows($query_bank) > 0 && isset($org['inn'], $org['dey'])) 
		{
			//$query = $db->query("SELECT pid_in FROM ".DB_PREFIX."_relations WHERE module_in = 'reestr' AND module_out = 'sales' AND pid_out = '".$id."' LIMIT 1");		
			//if(($db->numRows($query) <= 0)||(!empty($_POST['numb']))) 
			//{				
				if (!empty($_POST['numb']))
				{
					$numb = intval($_POST['numb']);
				}
				else
				{
					$query_n = $db->query("SELECT numb FROM ".DB_PREFIX."_reestr WHERE org = '".$org_id."' ORDER BY id DESC LIMIT 1");		
					$rn = $db->getRow($query_n);
					$numb = $rn['numb']+1;
					if($db->query("INSERT INTO `" . DB_PREFIX . "_reestr` ( `id` , `type` , `numb` ,  `uid` , `oid` , `org` , `date` , `date_view` , `date_create` , `status`) VALUES (NULL, '5', '" . $numb . "', '" . $uid . "', '" . $order['oid'] . "', '".$org_id."', '" . $date . "', '" . date('Y-m-d', $date) . "', '" . time() . "', '1');"))
					{
						$rid = $db->insert_id();
						$db->query("INSERT INTO `" . DB_PREFIX . "_relations` ( `id` , `module_out` , `pid_out` ,  `module_in` , `pid_in` , `date` , `uid` , `status`) VALUES (NULL, 'sales', '" . $id . "', 'reestr', '" . $rid . "', '". time() ."', '" . $uid . "', '1');");
					}
				}							
					
					// require_once(ROOT.'usr/plugins/PHPWord.php');
					$path = ROOT."usr/modules/sales/resources/template/";
					$PHPWord = new PHPWord();
					if($_POST['org'] == 1)
					{
						$document = $PHPWord->loadTemplate($path.'sales_doc/sale_doc_1.docx');
					} 
					elseif($_POST['org'] == 5)
					{
						$document = $PHPWord->loadTemplate($path.'sales_doc/sale_doc_5.docx');
					} 
					else
					{
						$document = $PHPWord->loadTemplate($path.'sales_doc/sale_doc_2.docx');
					}
					$Month_rus = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
					$bank = $db->getRow($query_bank);				
							switch($org['post']) {
								default:
									$post=$org['post'];
								break;				
								case "Директор":
									$post='директора';
								break;
								case "Генеральный директор":
									$post='генерального директора';
								break;
							}
							$dey = $org['dey'];

							$result_d = getDirectorDetail($org, ["oid" => $order['oid']]);
							
							$order_data = array(
									'n' => array(),
									'sku' => array(),
									'name' => array(),
									's' => array(),
									'u' => array(),
									'price' => array(),
									'price_nds' => array()
								);	
							$query = $db->query("SELECT * FROM ".DB_PREFIX."_sales_detail WHERE sales_id = '".$id."' AND status != '0' AND del = '0'");		
							if($db->numRows($query) > 0) 
							{
								$i = 0;
								while($catalog = $db->getRow($query)) 
								{	
									$i++;
									$stable_name = $catalog['s_name'];
									$stable_sku = $catalog['s_sku'];
									$stable_unit = $catalog['s_unit'];
									$sum = $catalog['count'];
									$price = round(($catalog['total']/ $catalog['count']), 2);
									$total_nds = $catalog['total'];			
									array_push($order_data['n'], $i);
									array_push($order_data['sku'], $stable_sku);
									array_push($order_data['name'], $stable_name);
									array_push($order_data['s'], $sum);
									array_push($order_data['u'],  get_unit('data', $stable_unit));
									array_push($order_data['price'], number_format($price,2,'.',' '));
									array_push($order_data['price_nds'], number_format($total_nds,2,'.',' '));
								}
							}
						
							$result_s = mb_substr($result_d->name, 0, 1) . '. ';
							$result_s .= mb_substr($result_d->patronymic, 0, 1) . '. ';
							$result_s .=  $result_d->surname;
								
							if ($pr <= 0)
							{
								$pay = 'Покупатель вносит сумму в размере 100% в течении 30 рабочих дней со дня отгрузки.';
							}
							elseif(($pr > 0)&&($pr < 100))
							{
								$pr_o = 100 - $pr;
								$pay = 'Покупатель вносит предоплату в размере '.$pr.'% в течение 3 (Трех) рабочих дней с момента подписания настоящей спецификации, оставшуюся сумму в размере '.$pr_o.'% в течении 30 рабочих дней со дня отгрузки.';
							}
							else
							{
								$pay = 'Покупатель вносит предоплату в размере 100% в течение 3 (Трёх) рабочих дней с момента подписания настоящей спецификации.';
							}				
							$document->setValue('{doc_numb}', $numb);
							$document->setValue('{post}', $post);
							if($org['type'] == "INDIVIDUAL"){
								$document->setValue('{post_i}', "Индивидуальный предприниматель");
							} else {
								$document->setValue('{post_i}', $org['post']);
							}
							$document->setValue('{director}', $result_d->result_genitive);
							$document->setValue('{director_short}', $result_s);
							$document->setValue('{dey}', $dey);
							$document->setValue('{pay}', $pay);
							
							$document->setValue('{day}', date('d', $date));
							$document->setValue('{month}', $Month_rus[ date( "n", $date) - 1 ]);
							$document->setValue('{year}', date('Y', $date));
							
							$document->setValue('{org_name}', $org['name']);
							$document->setValue('{org_inn}', $org['inn']);
							$document->setValue('{org_address}', $org['yaddress']);
							if($org['type'] == "INDIVIDUAL"){
								$document->setValue('{org_kpp}', "");
							} else {
								$document->setValue('{org_kpp}', "КПП: ".$org['kpp']);
							}

							$document->setValue('{org_bank}', $bank['name_short']);
							$document->setValue('{org_bik}', $bank['bik']);
							$document->setValue('{org_rs}', $bank['rschet']);
							$document->setValue('{org_ks}', $bank['kschet']);
							$document->cloneRow('s', $order_data);
							$document->setValue('{total}', number_format($order['total'],2,'.',' '));
							$document->setValue('{nds}',  number_format($order['nds'],2,'.',' '));
							$document->setValue('{total_nds}', number_format($order['total'],2,'.',' '));
							$document->setValue('{total_rus}', '('.mb_ucfirst(substr(num2str($order['total']), 1)));
							
							$dir = ROOT.'files/sales/'.$id;
							if (!file_exists($dir))
							{
								mkdir($dir, 0777, true);						
							}
							$file_url= 'files/sales/'.$id.'/8-'.$numb.'-'.time().'.docx';
							$document->save($file_url);	

							header('Location: https://atri-energo.ru/'.$file_url);
		
		}
		else
		{
			$adminTpl->admin_head('Ошибка создания договора!');
			echo '<div id="content" class="animated fadeIn">';	
			$adminTpl->info('Для составления договора у контрагента заполнены не все поля!', 'error', null, $lang['error'], 'Редактировать контрагента', ADMIN.'/module/organizations/edit/'.$order['oid']);
			echo '</div>';	
			$adminTpl->admin_foot();
		}			
	}		
}

function bank_query_result($id){
	global $db;
	$query_bank = $db->query("SELECT * FROM `".DB_PREFIX."_organizations_bank` WHERE oid = '".$id."' LIMIT 1");
	$bank = $db->getRow($query_bank);
	return $bank;
} 

function org_query_result($id){
	global $db;
	$query_org = $db->query("SELECT * FROM `".DB_PREFIX."_organizations` WHERE id = '".$id."' LIMIT 1");		
	$org = $db->getRow($query_org);
	return $org;
}  

function contract_create($request){
	return \Sales\Support\SalesReestrSupport::contract_create($request);
}

function spec_create($request){
	return \Sales\Support\SalesReestrSupport::spec_create($request);
}

function save_file_attach($request, $document, $prefix){
	return \Sales\Support\SalesReestrSupport::save_file_attach($request, $document, $prefix);
}

function get_contract($request){
	echo \Sales\Support\SalesReestrSupport::get_contract($request);
}

function get_spec($request){
	echo \Sales\Support\SalesReestrSupport::get_spec($request);
}

