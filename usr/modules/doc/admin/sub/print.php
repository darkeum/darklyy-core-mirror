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


class PDF_JavaScript extends \setasign\Fpdi\Fpdi {

    var $javascript;
    var $n_js;

    function IncludeJS($script) {
        $this->javascript=$script;
    }

    function _putjavascript() {
        $this->_newobj();
        $this->n_js=$this->n;
        $this->_out('<<');
        $this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
        $this->_out('>>');
        $this->_out('endobj');
        $this->_newobj();
        $this->_out('<<');
        $this->_out('/S /JavaScript');
        $this->_out('/JS '.$this->_textstring($this->javascript));
        $this->_out('>>');
        $this->_out('endobj');
    }

    function _putresources() {
        parent::_putresources();
        if (!empty($this->javascript)) {
            $this->_putjavascript();
        }
    }

    function _putcatalog() {
        parent::_putcatalog();
        if (!empty($this->javascript)) {
            $this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
        }
    }
}

class PDF_AutoPrint extends PDF_JavaScript
{
    function AutoPrint($dialog=false)
    {
        //Open the print dialog or start printing immediately on the standard printer
        $param=($dialog ? 'true' : 'false');
        $script="print($param);";
        $this->IncludeJS($script);
    }

    function AutoPrintToPrinter($server, $printer, $dialog=false)
    {
        $script = "document.contentWindow.print();";
        $this->IncludeJS($script);
    }
}

function dotsReplase($value)
{
	 return (!empty($value) ? str_replace(',', '.', $value) : 0);
}

function print_atridoc($id, $type = null, $print = true)
{
	require_once(ROOT."/vendor/autoload.php");
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang, $adminTpl;
	
	if (!empty($id))
	{			
		$uid = $core->auth->user_info['id'];	
		$query_static = $db->query("SELECT * FROM JMY_doc WHERE id = '".$id."' LIMIT 1");
		
		
		$order = $db->getRow($query_static);			
		$date = strtotime($order['date']);
		//$org = getOrgInfo($order['org']);	
		$numb = module_perfix('sales', $id);
		$pr = isset($_POST['pr']) ? intval($_POST['pr']) : 0;            
       // $query_org = $db->query("SELECT * FROM ".DB_PREFIX."_organizations WHERE id = '".$order['oid']."' LIMIT 1");		
		//$oid = $db->getRow($query_org);
	//	echo $order['org']; 
		if(isset($id))
		{
			switch($type) 
			{
				default:
					$tpl = ROOT.'/files/tpl/doc/act.docx';
				break;				
				case "doc":
					$tpl = ROOT.'/files/tpl/doc/act.docx';
				break;					
			}
			$document =  new \PhpOffice\PhpWord\TemplateProcessor($tpl);			
			$Month_rus = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];				
			$order_data = array();
			$query_object = $db->query("SELECT * FROM JMY_object WHERE id = '".$order['capital_construction']."' LIMIT 1");		
			$object = $db->getRow($query_object);	
			$faceTypeArray = getFaceType();
			$faceTypeUserArray = getFaceType('sclon');
			$test_data = json_decode($order['test_data']);
			
			$document->setValue('object', $object['name']);           
			$document->setValue('object_address', $object['address']);    
			foreach (json_decode($order['responsible_persons']) as $key => $val)
			{
				$query_org = $db->query("SELECT * FROM ".DB_PREFIX."_organizations WHERE id = '".$val->org."' LIMIT 1");	
				$oid = $db->getRow($query_org);
				
				$query_user_org = $db->query("SELECT * FROM ".DB_PREFIX."_users_org WHERE oid = '".$val->org."' AND uid ='".$val->user."'  LIMIT 1");				
				$user_org = $db->getRow($query_user_org);
				
				$order_data[] =  array(
						'oid_name'        	=> $oid['name'].' ОГРН:'.$oid['ogrn'].', ИНН:'.$oid['inn'],
						'oid_type'     		=> $faceTypeArray[$val->face_type],	
						'oid_address'     	=> $oid['yaddress']					
							
					);
				$user_data[] =  array(
						'u_name'        	=> shortUserName($val->user),
						'u_type'     		=> $faceTypeUserArray[$val->face_type],	
						'u_post'     		=> $user_org['post'],					
						'u_writ'     		=> $user_org['writ']					
							
					);
				$user_data2[] =  array(
						'user_type'     	=> $faceTypeUserArray[$val->face_type],	
						'user_name'        	=> shortUserName($val->user),						
						'user_post'     	=> $user_org['post'],
						'user_date'     	=> $val->{'date'},
						'user_sign'     	=> md5(time())	
					);
			}
			$document->cloneRowAndSetValues('oid_name', $order_data);
			$document->cloneRowAndSetValues('u_type', $user_data);
			$document->cloneRowAndSetValues('user_type', $user_data2);
			$document->setValue('id', $order['id']);   
			$document->setValue('project', $order['project']);   
			$document->setValue('d', date('d', $date));
			$document->setValue('month', $Month_rus[ date( "n", $date) - 1 ]);
			$document->setValue('year', date('Y', $date));	
			$document->setValue('p', isset($test_data->pressure) ? $test_data->pressure : '0');	
			$document->setValue('p10', isset($test_data->pressure) ? dotsReplase($test_data->pressure)*10 : '0');	
			$document->setValue('time', isset($test_data->test_time) ? $test_data->test_time : '0');
			$document->setValue('dp', isset($test_data->pressure_drop) ? $test_data->pressure_drop :'0');
			$document->setValue('dp10', isset($test_data->pressure_drop) ? dotsReplase($test_data->pressure_drop)*10 : '0');
			$document->setValue('commission_decision', isset($test_data->commission_decision) ? : '' );	
			/*
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_sales_detail WHERE sales_id = '".$id."' AND status != '0' ORDER BY position, id ASC");		
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
					$date_post = (!empty($catalog['date_post']) ? date("d.m.Y", strtotime($catalog['date_post'])) : '');
					$order_data[] =  array(
						'n'        	=> $i,
						'sku' 		=> $stable_sku,
						'name'      => $stable_name,
						's'     	=> $sum,
						'u'     	=> $stable_unit,
						'price'     => number_format($price,2,'.',' '),
						'price_nds' => number_format($total_nds,2,'.',' '),
						'time'     	=> $date_post,
					);
				}
			}								
			$document->setValue('doc_numb', $numb);                  
			$document->setValue('count',$i);
			$document->setValue('day', date('d', $date));
			$document->setValue('month', $Month_rus[ date( "n", $date) - 1 ]);
			$document->setValue('year', date('Y', $date));				
			$document->setValue('oid_name', $oid['name']);
			$document->setValue('oid_inn', $oid['inn']);
			$document->setValue('oid_address', $oid['yaddress']);
			$document->setValue('org_name', $org['organizations']['name']);
			$document->setValue('org_inn', $org['organizations']['inn']);
			$document->setValue('org_address', $org['organizations']['yaddress']);
			$document->setValue('org_bank_address', $org['organizations_bank']['address']);
			$document->setValue('org_bank_name', $org['organizations_bank']['name']);
			$document->setValue('org_bank_bik', $org['organizations_bank']['bik']);
			$document->setValue('org_bank_ks', $org['organizations_bank']['kschet']);
			$document->setValue('org_bank_rs', $org['organizations_bank']['rschet']);
			$document->setImageValue('logo',  array('path' => ROOT.$org['org']['logo'], 'width' => '76mm', 'height' => '16mm'));		
			if ($org['org']['nds'] == '0')
			{
				$nds_includes = 'Без налога (НДС):';
				$total_with_nds = '';
				$total_with_nds_sum = '';
				$nds_sum = '-';			
			}
			else
			{
				$nds_includes = 'В том числе НДС ('.getNds($org['org']['nds']).'%):';
				$total_with_nds = 'Итого с НДС';
				$total_with_nds_sum = number_format($order['total'],2,'.',' ');
				$nds_sum = number_format($order['nds'],2,'.',' ');
			}
			if($type == 'bill_sign')
			{
				$document->setImageValue('sign',  array('path' => ROOT.$org['org']['bill_sign'], 'width' => '190mm', 'height' => '54mm'));
			}
			$document->setValue('nds_includes', $nds_includes);
			$document->setValue('total_with_nds', $total_with_nds);
			$document->setValue('total_with_nds_sum', $total_with_nds_sum);
				
            if($oid['type'] == "INDIVIDUAL")
			{
				$document->setValue('oid_kpp', "");
			} 
			else 
			{
				$document->setValue('oid_kpp', "КПП: ".$oid['kpp'].',');
			}
			if($org['organizations']['type'] == "INDIVIDUAL")
			{
				$document->setValue('org_kpp', "");
				$document->setValue('org_kpp_only', "");
			} 
			else 
			{
				$document->setValue('org_kpp', "КПП: ".$org['organizations']['kpp'].',');
				$document->setValue('org_kpp_only', $org['organizations']['kpp']);
			}
			$manager_name = '';
			$manager_phone = '';
			$manager_email = '';
			if(!empty($order['uid']))
			{
				$query_user = $db->query("SELECT * FROM ".DB_PREFIX."_users WHERE id = '" . $order['uid'] . "'");
				$user = $db->getRow($query_user);
				$result_s = mb_substr($user['name'], 0, 1) . '. ';
				$result_s .= mb_substr($user['ochestvo'], 0, 1) . '. ';
				$result_s .=  $user['surname'];
				$manager_name = $result_s;
				$manager_email = $user['email'];
				$manager_phone = $user['phone'];
			}
			
			$document->setValue('org_director', shortUserName($org['org']['director']));
			$document->setValue('org_accountant', shortUserName($org['org']['accountant']));
			$document->setValue('manager', $manager_name);
			$document->setValue('manager_email', $manager_email);
			$document->setValue('manager_phone', $manager_phone);
			$document->cloneRowAndSetValues('n', $order_data);
			$document->setValue('total', number_format($order['total'],2,'.',' '));
			$document->setValue('nds',  $nds_sum);
			$document->setValue('total_nds', number_format($order['total'],2,'.',' '));					
			$total_rus = str_replace("( ", "", num2str($order['total']));
			$total_rus = str_replace(" )", "", $total_rus);
			$document->setValue('total_rus', mb_ucfirst($total_rus));
			*/
			$dir = ROOT.'files/doc/'.$id;
			if (!file_exists($dir))
			{
				mkdir($dir, 0777);						
			}
			$file_url= ROOT.'files/sales/'.$id.'/'.$type.'_print.docx';
			$file_url_pdf= ROOT.'files/sales/'.$id.'/'.$type.'_print.pdf';
			$document->saveAs($file_url);	
			//header('Location: /files/sales/'.$id.'/'.$type.'_print.docx');
		}
		else
		{
				
		}	
	}
	
	if ($print)
	{
		\Boot\Jobs\PdfJob::dispatchSync($file_url, $type.'_print.pdf');
		$pdf=new PDF_AutoPrint();
		$pageCount = $pdf->setSourceFile($file_url_pdf);
		for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
			$tplIdx = $pdf->importPage($pageNo);        
			$pdf->addPage();
			$pdf->useTemplate($tplIdx, null, null, null, null, true);
		}
		$pdf->AutoPrint(true);
		$pdf->Output();
		unlink($file_url);
		unlink($file_url_pdf);
	}
	else
	{
		
	}
	
}