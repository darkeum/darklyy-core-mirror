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


function print_sales($id, $type = null, $trigger = false)
{	
	require_once(ROOT."/vendor/autoload.php");
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang, $adminTpl;
	if($core->auth->user_id == '1й1')
	{	
		if(!empty($id))
		{
			$request = array();
			$request['realization_id'] = $id;			
			
			$rp = new \Realization\RealizationPrint($request);
			
			$rp->printStart();
			
		}	
		dd($rp);			
		exit;			
	}
	
	
	
	
	
	
	
	if (!empty($id))
	{			
		$uid = $core->auth->user_info['id'];		
		$query_static = $db->query("SELECT r.*, s.numb as s_numb FROM `".DB_PREFIX."_realization` as r LEFT JOIN `".DB_PREFIX."_sales` as s ON(r.sales_id = s.id) WHERE r.id = '" . $id . "'");
		$order = $db->getRow($query_static);			
		$date = strtotime($order['date']);
		$sales_id = $order['sales_id'];
		//$query_bank = $db->query("SELECT * FROM ".DB_PREFIX."_bank_detail WHERE module = 'sales' AND date < '".date('Y-m-d', $date)."' AND pid LIKE '%\"" . $sales_id . "\"%'");
		//echo "SELECT * FROM ".DB_PREFIX."_bank_detail WHERE module = 'sales' AND date < '".date('Y-m-d', $date)."' AND pid LIKE '%\"" . $sales_id . "\"%'";
		//exit;
		
		$format = isset($_GET["format"]) ? $_GET["format"] : "PDF";

		$numb = (!empty($order['numb']) ? $order['numb'] : module_perfix('realization', $id));
		$pr = isset($_POST['pr']) ? intval($_POST['pr']) : 0;            
        $query_oid = $db->query("SELECT * FROM ".DB_PREFIX."_organizations WHERE id = '".$order['oid']."' LIMIT 1");		
		$oid = $db->getRow($query_oid);
		$query_org = $db->query("SELECT org.nds, oid.* FROM ".DB_PREFIX."_org as org LEFT JOIN  ".DB_PREFIX."_organizations as oid ON(oid.id = org.oid) WHERE org.id = '".$order['org']."' LIMIT 1");
		$org = $db->getRow($query_org);
		
		$org_nds = \Price::getNds($org['nds']);
		$org_nds_id = $org_nds;
		if ($org_nds != '0')
		{
			$org_nds_view = $org_nds.'%';
			$st = '1';
		}
		else
		{
			$org_nds_view = 'Без НДС';
			$st = '2';
		}


		$release = 'release_19082017';
		if($date >= strtotime('02.04.2021')){
			$release = 'release_02042021';
		}

		$path = ROOT."/usr/modules/realization/resources/templates/{$release}/";
		
		if(isset($oid['inn']))
		{
			$orientation = 'P';
			switch($type) 
			{
				default:
					$tpl = $path.'UPD_print.docx';
					$tpl_prepare = $path.'UPD_print_prepare.docx';
					$orientation = 'L';
				break;				
				case "upd":
					$tpl = $path.'UPD_print.docx';
					$tpl_prepare = $path.'UPD_print_prepare.docx';
					$orientation = 'L';
				break;	
				case "upd_multi":
					$tpl = $path.'UPD_multi_print.docx';
					$tpl_prepare = $path.'UPD_multi_prepare.docx';
					$orientation = 'L';
				break;	
				case "upd_multi_one":
					$tpl = $path.'UPD_multi_one_print.docx';
					$tpl_prepare = $path.'UPD_multi_one_prepare.docx';
					$orientation = 'L';
				break;	
				case "rashod":
					$tpl = $path.'rashod.docx';
					$tpl_prepare = $path.'rashod_prepare.docx';
					$orientation = 'P';
				break;	
				case "rashod_price":
					$tpl = $path.'rashod_price.docx';
					$tpl_prepare = $path.'rashod_price_prepare.docx';
					$orientation = 'P';
				break;
				
			}
			copy($tpl, $tpl_prepare);
			
			
			
			$Month_rus = getMonthRus();	
			
			$zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
			$zip->open($tpl_prepare);
			$fileToModify = "word/header1.xml";
			$header = $zip->getFromName($fileToModify);
			//print_r($header);
			//exit;
			$header = str_replace("{DOC_NUMBER}", $numb, $header);
			$header = str_replace("{DAY}", date('d', $date), $header);
			$header = str_replace("{MONTH}", $Month_rus[ date( "n", $date) - 1 ], $header);
			$header = str_replace("{YEAR}", date('Y', $date), $header);		
			$zip->deleteName($fileToModify);
			$zip->addFromString($fileToModify, $header);
			$return =$zip->close();
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		
			
			
			
			require_once(ROOT.'usr/plugins/PHPWord.php');
			$PHPWord = new PHPWord();
			$document = $PHPWord->loadTemplate($tpl_prepare);	
			
			
						 
			$order_data = array(
						'I' => array(),
						'SKU' => array(),
						'NAME' => array(),
						'CT' => array(),
						'YCT' => array(),
						'UN' => array(),
						'YUN' => array(),
						'U' => array(),
						'CD' => array(),
						'N_SUM' => array(),
						'AK' => array(),
						'PRICE' => array(),
						'PRICE_T' => array(),
						'N' => array(),
						'S' => array(),
						'SN' => array(),
						'T' => array(),
						'TOTAL' => array()
			);	
			if($trigger)
			{
				$order_data_end = $order_data;
			}

			$query = $db->query("SELECT * FROM ".DB_PREFIX."_realization_detail WHERE rid = '".$id."' AND status != '0' ORDER BY position, id");		
			if($db->numRows($query) > 0) 
			{
				$i = 0;
				$total_all = 0;
				$nds_all = 0;
				$total_nds_all = 0;
				while($catalog = $db->getRow($query)) 
				{	
					$i++;
					$stable_name = html_entity_decode($catalog['s_name'], ENT_QUOTES);
					$stable_name = str_replace("&", "&amp;", $stable_name);
					$stable_sku = $catalog['s_sku'];
					$stable_unit = (empty($catalog['s_unit']) ?  '796' : $catalog['s_unit']);
					$stable_unit = ($catalog['s_unit'] == 'null' ?  '796' : $catalog['s_unit']);
					$stable_unit_code = get_unit('data', $stable_unit);
					$count = $catalog['count'];
					
					
					$total_nds = $catalog['total'];	
					
					
					// if ($org_nds != '0')
					// {
					// 	$nds = round(($total_nds/120*20), 2);
					// 	$total = $total_nds - $nds;
					// 	$total_all = $total_all + round($total, 2);
						
					// 	$price = round(($catalog['total']/ $catalog['count']), 2);
					// 	$price = round($price-($price/120*20), 2);
					// }
					// else
					// {
					// 	$price = round(($catalog['total']/ $catalog['count']), 2);
					// 	$total = $total_nds;
					// 	$total_all = $total_all + round($total, 2);
					// 	$nds = 0;
					// }
					
					$dp = ["price" => $catalog["s_price"], "count" => $catalog["count"], "discount" => $catalog["discount"], "nds_type" => $catalog["nds_type"]];
					$price_data = \Price::get_all($dp);
					$dp["count"] = 1;
					$price_data2 = \Price::get_all($dp);

					$price = $price_data["discount_price"] - $price_data2["nds"];
					$total_nds = $price_data["total"];
					$total = $price_data["total"] - $price_data["nds"];
					$nds = $price_data["nds"];
					$total_all = $total_all + $total;
					
					$nds_all = $nds_all + $nds;
					$total_nds_all = $total_nds_all + $total_nds;
					array_push($order_data['I'], $i);
					array_push($order_data['SKU'], $stable_sku);
					array_push($order_data['NAME'], $stable_name);
					array_push($order_data['CT'], $count);
					array_push($order_data['YCT'], $count);
					array_push($order_data['CD'], '--');
					array_push($order_data['S'], '--');
					array_push($order_data['SN'], '--');
					array_push($order_data['T'], '--'); 
					array_push($order_data['AK'], 'без акциза');
					array_push($order_data['UN'], $stable_unit_code);
					array_push($order_data['YUN'], $stable_unit_code);
					array_push($order_data['U'], $stable_unit);
					array_push($order_data['N'], $org_nds_view);
					array_push($order_data['PRICE'], number_format($price,2,'.',' '));
					array_push($order_data['PRICE_T'], number_format($total,2,'.',' '));
					array_push($order_data['N_SUM'], (($org_nds_id != '0') ? number_format($nds,2,'.',' ') : 'без НДС'));
					array_push($order_data['TOTAL'], number_format($total_nds,2,'.',' '));
				}
				//echo '<pre>';
				if($trigger)
				{
					foreach($order_data as $key => $value)
					{
						array_push($order_data_end[$key], array_pop($value));
						$order_data[$key] = $value;
						
					}					
					//print_r($order_data);
					//echo '<br>ffff<br>';
					//print_r($order_data_end);
					//exit;
				}
			}								
			$document->setValue('{TYPE_DOC}', 'Реализация товаров');        
			$document->setValue('{S}', $st);    
			$document->setValue('{DOC_NUMB}', $numb);                  
			$document->setValue('{COUNT}',$i);
			$document->setValue('{DAY}', date('d', $date));
			$document->setValue('{MONTH}', $Month_rus[ date( "n", $date) - 1 ]);
			$document->setValue('{YEAR}', date('Y', $date));					
			$document->setValue('{OID_NAME}', $oid['name']);
			$document->setValue('{OID_INN}', $oid['inn']);
			$document->setValue('{OID_ADDRESS}', $oid['yaddress']);
			$document->setValue('{ORG_NAME}', $org['name']);
			$document->setValue('{ORG_INN}', $org['inn']);
			$document->setValue('{ORG_ADDRESS}', $org['yaddress']);
			$document->setValue('{CURRENCY}', 'Российский рубль, 643');
			$document->setValue('{PRICE_TOTAL}', number_format($total_all,2,'.',' '));
			$document->setValue('{NDS_T}', ($org_nds_id != '0') ? number_format($nds_all,2,'.',' ') : 'без НДС');
			$document->setValue('{TOTAL}', number_format($total_nds_all,2,'.',' '));
			
			$query_stock = $db->query("SELECT name FROM ".DB_PREFIX."_stock WHERE id = '".$order['stock_id']."' LIMIT 1");		
			$stock = $db->getRow($query_stock);
			$stock_text = $stock['name'];
			$document->setValue('{STOCK}', $stock_text);    
				
			if(empty($order['sales_id']))
			{
				$base_text = '--';
			}
			else
			{
				$query_sales = $db->query("SELECT * FROM ".DB_PREFIX."_sales WHERE id = '".$order['sales_id']."' LIMIT 1");		
				$sales = $db->getRow($query_sales);
				$base_text = 'Заказ клиента №'.$order['s_numb'].' от '.date('d.m.Y', strtotime($sales['date']));
			}
			$document->setValue('{BASE}', $base_text);
            if($oid['type'] == "INDIVIDUAL")
			{
				$document->setValue('{OID_KPP}', "");
			} 
			else 
			{
				$document->setValue('{OID_KPP}', "/".$oid['kpp'].',');
			}
			if($org['type'] == "INDIVIDUAL")
			{
				$document->setValue('{ORG_KPP}', "");
			} 
			else 
			{
				$document->setValue('{ORG_KPP}', "/".$org['kpp'].',');
			}
			
			$u_head = '';
			$u_buch = '';
			$u_ship = '';
			$u_ship_post = '';
			
			
			if (!empty($order['print_info']))
			{
				$print_info = json_decode($order['print_info'], true);
				//var_dump($print_info);
				//exit;
				$u_head = $print_info['sign']['leader']['data'];
				$u_buch = $print_info['sign']['buch']['data'];
				$u_ship = $print_info['sign']['ship']['data'];
				$u_ship_post = $print_info['sign']['ship']['post'];
			}
			$document->setValue('{U_HEAD}', $u_head);
			$document->setValue('{U_BUCH}', $u_buch);
			$document->setValue('{U_SHIPPED}', $u_ship);
			$document->setValue('{U_SHIPPED_POST}', $u_ship_post);
			$sd_date = date("d.m.Y", strtotime($order["date"]));
			$document->setValue('{SHIPPING_DOC}', "№ п/п 1-{$i} №{$numb} от {$sd_date} г.");
			
			$document->cloneRow('S', $order_data);	
			if($trigger)
			{
				$document->cloneRow('L', $order_data_end);	
			}
			$dir = ROOT.'files/realization/'.$id;
			if (!file_exists($dir))
			{
				mkdir($dir, 0777, true);						
			}
			$order_date = date("d-m-Y", strtotime($order["date"]));
			$file_name = $type.'_'.$order["numb"].'_'.$order_date;
			$file_url= ROOT.'files/realization/'.$id.'/'.$file_name.'.docx';
			$file_url_pdf= ROOT.'files/realization/'.$id.'/'.$file_name.'.pdf';
			$document->save($file_url);			
			unlink($tpl_prepare);
			
			if(($type == 'upd')||($type == 'upd_multi')||($type == 'upd_multi_one'))
			{	
				/*
				$zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
				$zip->open($file_url);
				preg_match("/\<Pages>(.*)\<\/Pages\>/", $zip->getFromName("docProps/app.xml"), $var);
				print_r($zip->getFromName("docProps/app.xml"));
				exit;
				*/
					
				\Boot\Jobs\PdfJob::dispatchSync($file_url, $file_name.".pdf");
				$fp = fopen($file_url_pdf, 'r');
				if ($fp) {
					$count = 0;
					while(!feof($fp)) {
						$line = fgets($fp,255);
						if (preg_match('|/Count [0-9]+|', $line, $matches)){
							preg_match('|[0-9]+|', $matches[0], $matches2);
							if ($count < $matches2[0]) {
								$count = trim($matches2[0]); 
							}
						} 
					}
					fclose($fp);   
				}
				//echo $type;
				//echo $count;
				//exit;
				if(($count == 1)||$trigger||(($count > 1)&&($i==1)))
				{
					if ($count%10 == 1)
					{
						$count = $count.' листе';
					}
					else
					{
						$count = $count.' листах';
					}
					if(($i==1)&&(!$trigger))
					{
						//$count = '1 листе';
						unlink($file_url);
						unlink($file_url_pdf);
						print_sales($id, $type = 'upd_multi_one', true);
						exit;
					}
					$PHPWord = new PHPWord();
					$document = $PHPWord->loadTemplate($file_url);	
					$document->setValue('{PAGE}',$count);
					$document->save($file_url);
				}
				else
				{
					unlink($file_url);
					unlink($file_url_pdf);
					print_sales($id, $type = 'upd_multi', true);
					exit;
				}
			}
			
			if($format === "PDF"){
				\Boot\Jobs\PdfJob::dispatchSync($file_url, $file_name.".pdf");
				$pdf=new PDF_AutoPrint();
				$pageCount = $pdf->setSourceFile($file_url_pdf);
				for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
					$tplIdx = $pdf->importPage($pageNo);        
					$pdf->addPage($orientation);
					$pdf->useTemplate($tplIdx, null, null, null, null, true);
				}
				$pdf->AutoPrint(true);
				$pdf->Output($file_name.'.pdf', "I");
				unlink($file_url);
				unlink($file_url_pdf);
			}
				
			header('Location: /files/realization/'.$id.'/'.$file_name.'.docx');
			
			
		}
		else
		{
			
		}	
	}
}


