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

function print_storage($id, $type = null, $print = true){
	$data = print_storage_file($id, $type);
	$file_url = $data["file_url"];
	$file_url_pdf = $data["file_url_pdf"];
	//exit;
	if ($print){
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
}

function print_storage_file($id, $type, $request = []){
	global $db, $core;
	require_once(ROOT."/vendor/autoload.php");
	require_once(ROOT.'/boot/sub_classes/phpqrcode/qrlib.php');  
	if (!empty($id)){			
		$uid = $core->auth->user_info['id'];				
		$order = get_query_order($id);	
		$date = strtotime($order['date']);
		$numb = empty($order["numb"]) ? module_perfix('sales', $id) : $order["numb"];
		$pr = isset($_POST['pr']) ? intval($_POST['pr']) : 0;
		$order['oid'] = '507';
        $query_org = $db->query("SELECT * FROM ".DB_PREFIX."_organizations WHERE id = '".$order['oid']."' LIMIT 1");		
		$org = $db->getRow($query_org);
		if(isset($org['inn']))
		{
			switch($type) 
			{
				default:
					$tpl = ROOT.'/usr/modules/storage/Resources/Templates/Transfer.docx';
				break;				
				case "storage":
					$tpl = ROOT.'/usr/modules/storage/Resources/Templates/Transfer.docx';
				break;	
			}
			$document =  new \PhpOffice\PhpWord\TemplateProcessor($tpl);					 
			$Month_rus = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];				
			$order_data = array();	
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_stock_transfer_detail WHERE transfer_id = '".$id."' AND status != '0' ORDER BY id ASC");		
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
					$date_post = (!empty($catalog['date_post']) ? date("d.m.Y", strtotime($catalog['date_post'])) : '');
					$order_data[] =  array(
							'n'        	=> $i,
							'sku' 		=> $stable_sku,
							'name'      => $stable_name,
							's'     	=> $sum,
							'u'     	=> get_unit('data', $stable_unit),
							'time'     	=> $date_post,
						);
				
				}
			}								
			$document->setValue('doc_numb', $numb);                  
			$document->setValue('object', (!empty($order['objname']) ? $order['objname'] : 'Не указано'));                  
			$document->setValue('sales', (empty($order['s_id']) ? "" : 'Заказ: '.$order["s_numb"]));                  
			$document->setValue('count',$i);
			$document->setValue('day', date('d', $date));
			$document->setValue('month', $Month_rus[ date( "n", $date) - 1 ]);
			$document->setValue('year', date('Y', $date));					
			$document->setValue('org_name', $org['name']);
			$document->setValue('org_inn', $org['inn']);
			$document->setValue('org_address', $org['yaddress']);
            if($org['type'] == "INDIVIDUAL")
			{
				$document->setValue('{org_kpp}', "");
			} 
			else 
			{
				$document->setValue('{org_kpp}', "КПП: ".$org['kpp'].',');
			}

			// $manager_name = '';
			// $manager_phone = '';
			// $manager_email = '';

			// if(!empty($order['uid']))
			// {
			// 	$query_user = $db->query("SELECT * FROM ".DB_PREFIX."_users WHERE id = '" . $order['uid'] . "'");
			// 	$user = $db->getRow($query_user);
			// 	$result_s = mb_substr($user['name'], 0, 1) . '. ';
			// 	$result_s .= mb_substr($user['ochestvo'], 0, 1) . '. ';
			// 	$result_s .=  $user['surname'];
			// 	$manager_name = $result_s;
			// 	$manager_email = $user['email'];
			// 	$manager_phone = $user['phone'];
			// }
			$query_stock_in = $db->query("SELECT name FROM ".DB_PREFIX."_stock WHERE id = '".$order['stock_in']."' LIMIT 1");		
			$stock_in = $db->getRow($query_stock_in);
			$query_stock_out = $db->query("SELECT name FROM ".DB_PREFIX."_stock WHERE id = '".$order['stock_out']."' LIMIT 1");		
			$stock_out = $db->getRow($query_stock_out);
			// $document->setValue('manager', $manager_name);
			// $document->setValue('manager_email', $manager_email);
			// $document->setValue('manager_phone', $manager_phone);
			$uid_gave = shortUserName($order["uid_gave"]);
			$uid_accepted = shortUserName($order["uid_accepted"]);
			$document->setValue('uid_gave', $uid_gave);
			$document->setValue('uid_accepted', $uid_accepted);
			$document->setValue('stock_in', $stock_in['name']);
			$document->setValue('stock_out', $stock_out['name']);

			$uid_gave_sign = empty($request["uid_gave_sign"]) ? "                            ": $request["uid_gave_sign"];
			$uid_accepted_sign = empty($request["uid_accepted_sign"]) ? "                            ": $request["uid_accepted_sign"];
			$document->setValue('uid_gave_sign', $uid_gave_sign);
			$document->setValue('uid_accepted_sign', $uid_accepted_sign);
			$dir = ROOT.'files/transfer/'.$id;
			if (!file_exists($dir))
			{
				mkdir($dir, 0777);						
			}
			$file_url_qrcode= ROOT.'files/transfer/'.$id.'/'.$type.'_qrcode.png';
			QRcode::png("https://atri-energo.ru/administration/module/storage/transfer/detail/".$id, $file_url_qrcode, "L", 0, 0);
			
			$document->setImageValue('qrcode',  array('path' => $file_url_qrcode, 'width' => '32mm', 'height' => '32mm'));
			$document->cloneRowAndSetValues('n', $order_data);				
			
			$clear_file_url = 'files/transfer/'.$id.'/'.$type.'_print.docx';
			$file_url= ROOT.$clear_file_url;
			$file_url_pdf= ROOT.'files/transfer/'.$id.'/'.$type.'_print.pdf';
			$document->saveAs($file_url);	

			return [
				"clear_file_url" => $clear_file_url,
				"file_url" => $file_url,
				"file_url_pdf" => $file_url_pdf,
			];
		}
	}
}

function print_detail($id, $format, $type, $combine){
	require_once(ROOT."/vendor/autoload.php");
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang, $adminTpl;

	$queryO  = $db->query("SELECT s.*, o.name FROM ".DB_PREFIX."_storage as s LEFT JOIN ".DB_PREFIX."_sales as sl on (s.sales_id=sl.id) LEFT JOIN ".DB_PREFIX."_organizations as o on (sl.oid=o.id) WHERE s.id = '".$id."'");	
	$order = $db->getRow($queryO);	

	$sort_checked_type = isset($_COOKIE["storage_detail_sort"]) ? $_COOKIE["storage_detail_sort"] : "s.id";
	$query = $db->query("SELECT s.*, c.* FROM ".DB_PREFIX."_storage_detail as s LEFT JOIN ".DB_PREFIX."_catalog as c on(s.cid=c.id) WHERE s.storage_id = '".$id."' AND s.status != '0' ORDER BY ".$sort_checked_type);	
	$order_data = [];
	$key_sync = [];
	if($db->numRows($query) > 0)
	{	
		$n = 0;
		$counter = -1;
		while($content = $db->getRow($query)) 
		{
			$append = true;
			$n++;
			$counter++;
			
			if($combine && array_key_exists($content['id'], $key_sync)){
				//$order_data[$key_sync[$content['id']]]["quantity"] += $content['quantity'];
				//$order_data[$key_sync[$content['id']]]["quantity_real"] += $content['count_now'];
				$append = false;
			}
			$price = \Price::getPrice($content['cid']);
			if($append){
				$order_data[] = [
					"n" => $n,
					"cid" => $content['id'],
					"sku" => $content['sku'],
					"title" => $content['title'],
					"quantity" => $content['quantity'] ?? '0',
					"quantity_real" => $content['count_now'],
					"unit" => $content['unit'],
					"price" =>  $price->price_purchase_,
					"total" =>  $price->price_purchase_*$content['count_now'],
				];
			}

			if(!array_key_exists($content['id'], $key_sync)){
				$key_sync[$content['id']] = $counter;
			}
		}
	}

	$data["type"] = $type;
	$data["id"] = $order["id"];
	$data["date"] = $order["date"];
	$data["order"] = $order_data;
	$data["format"] = $format;

	print_fubric($id, $data);
}

function print_assembly($id, $format, $type, $combine){
	require_once(ROOT."/vendor/autoload.php");
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang, $adminTpl;

	$query = $db->query("SELECT sa.*, s.numb as s_numb, s.title as s_title, obj.name as obj_name, o.name as o_name
	FROM `".DB_PREFIX."_storage_assembly` as sa 
	LEFT JOIN `".DB_PREFIX."_sales` as s ON(s.id = sa.sales_id) 
	LEFT JOIN `".DB_PREFIX."_object` as obj ON(s.object = obj.id) 
	LEFT JOIN `".DB_PREFIX."_organizations` as o ON(s.oid = o.id) 
	WHERE sa.id = '".$id."' LIMIT 1");
	$order = $db->getRow($query);
	
	$order_data = [];
	$key_sync = [];
	// $query = $db->query("SELECT sad.count, sd.* 
	// FROM `".DB_PREFIX."_storage_assembly_detail` as sad 
	// LEFT JOIN `".DB_PREFIX."_sales_detail` as sd ON(sd.id = sad.sdid) 
	// WHERE sad.said = '".$id."'");	
	$query = getQueryDetail($id);
	if($db->numRows($query) > 0){
		$n = 0;
		$counter = -1;
		while($content = $db->getRow($query)) {
			$append = true;
			$n++;
			$counter++;
			
			if($combine && array_key_exists($content['cid'], $key_sync)){
				$order_data[$key_sync[$content['cid']]]["quantity"] += $content['count'];
				$append = false;
			}

			if($append){
				$ndr = get_ndr(["cid" => $content["cid"], "stock_id" => "1", "module" => "sales", "pid" => "", "returnType" => "array"]);
				$order_data[] = [
					"n" => $n,
					"cid" => $content['cid'],
					"sku" => $content['s_sku'],
					"title" => html_entity_decode($content['s_name'], ENT_QUOTES),
					"quantity" => $content['sadcount_save'],
					"ndr" => $ndr["n"]." / ".$ndr["d"]." / ".$ndr["r"],
				];
			}

			if(!array_key_exists($content['cid'], $key_sync)){
				$key_sync[$content['cid']] = $counter;
			}
		}
	}

	$data["type"] = $type;
	$data["id"] = $order["id"];
	$data["date"] = $order["date"];
	$data["user"] = shortUserName($order["uid"]);
	$data["order"] = $order_data;
	$data["format"] = $format;
	$data["s_numb"] = $order["s_numb"];
	$data["s_title"] = $order["s_title"];
	$data["object"] = isset($order["obj_name"]) ? $order["obj_name"] : "";
	$data["oid"] = isset($order["o_name"]) ? $order["o_name"] : "";

	print_fubric($id, $data);
}

function print_fubric($id, $data){
	switch($data["type"]){
		default:
		case "bill":
			$tpl = ROOT.'/usr/modules/storage/Resources/Templates/Bill.docx';
		break;		
		case "assembly":
			$tpl = ROOT.'/usr/modules/storage/Resources/Templates/Assembly.docx';
		break;
	}

	$document =  new \PhpOffice\PhpWord\TemplateProcessor($tpl);		
	$document->setValue('numb', $data["id"]);
	$document->setValue('stock_name', "Оптовый");
	$document->setValue('user', $data["user"] ?? 'null');
	$document->setValue('date', date("d.m.Y H:i:s", strtotime($data["date"])));
	$document->setValue('s_numb', $data["s_numb"] ?? 'Б/Н');
	$document->setValue('s_title', $data["s_title"] ?? 'Б/Н');
	$object = empty($data["object"]) ? "Не указан" : $data["object"];
	$document->setValue('object', $object);
	$oid = empty($data["oid"]) ? "Не указан" : $data["oid"];
	$document->setValue('oid', $oid);
	$document->cloneRowAndSetValues('n', $data["order"]);

	$file_url = 'files/storage/'.$id.'/'.$id.'_print.docx';
	$file_url_pdf = 'files/storage/'.$id.'/'.$id.'_print.pdf';
	$dir = ROOT.'files/storage/'.$id;
	if (!file_exists($dir)){
		mkdir($dir, 0777, true);						
	}
	$document->saveAs(ROOT.$file_url);
	if($data["format"] === 'pdf')
	{
		\Boot\Jobs\PdfJob::dispatchSync(ROOT.$file_url, $id.'_print.pdf');
		header('Location: /'.$file_url_pdf);
		exit;
	}
	header('Location: /'.$file_url);
}