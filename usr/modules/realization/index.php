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
    require (ROOT.'etc/content.config.php');
    
    function view($secret_key)
    {
        global $db, $config, $core, $url, $headTag, $content_conf, $lang;
        if($secret_key)
        {
            $query = $db->query("SELECT * FROM " . DB_PREFIX . "_sales WHERE md5='" . $db->safesql($secret_key) . "' LIMIT 1");
            $static = $db->getRow($query);
            if($static)
            {	
                /*
                    if ($static['date_end'] == 0)
                    {
                    $st = 'выполняется!';
                    }	
                    else
                    {
                    $st = 'готов!';
                    }
                */
                //$text = 'Ваш заказ от '.$static['date'].' '.$st;	
                $text = '<div class="table-responsive"> 
				
                <table class="table table-striped" id="tablesForm">
                <thead>
                <tr>
                <th class="w40"><span class="pd-l-sm"></span>№</th>
                <th style="width: 200px;">Номенклатура</th>
                <th  style="width: 70px;">Артикул</th>
                <th  style="width: 70px;" class="w50 text-center">Кол-во</th>	
                <th class="w150">Цена</th>
                <th class="w200">Сумма</th>
                <th class="w150">Наличие</th>	
                <th class="w150">Дата поставки</th>							
                </tr>
                </thead>
                <tbody id="tov_list">';
                $query1 = $db->query("SELECT s.*, d.title, d.quantity, st.count_now, s_o.date_end FROM ".DB_PREFIX."_sales_detail as s LEFT JOIN ".DB_PREFIX."_catalog AS d on(d.id=s.cid) LEFT JOIN ".DB_PREFIX."_storage_detail AS st on (st.sale_detail_id=s.id) LEFT JOIN ".DB_PREFIX."_supply_detail AS sdt on (sdt.sdid=s.id) LEFT JOIN ".DB_PREFIX."_supply_order_detail AS sdto on (sdto.supply_d = s.id) LEFT JOIN ".DB_PREFIX."_supply_order AS s_o on (s_o.id = sdto.supply_o) WHERE s.sales_id = '".$static['id']."' AND s.status != '0'");
                $i = 1;
                $total = 0;
                while($content = $db->getRow($query1)){
                    if($content['discount'] == 0){
                        $price = $content['s_price'];
                        } else {
                        $price = $content['s_price'] - $content['s_price'] * $content['discount'] / 100;
                    }
					$st =  '<font color="red">Нет</font>';
					if ($content['quantity'] >= $content['count'])
					{$st = '<font color="green">Да</font>';
                    }
                    $text .= '<tr><td data-toggle="tooltip" title="' . $content['id'] . '"><span class="pd-l-sm"></span>' . $i . '</td>
                    <td class="w100"><a href="/catalog_v2/view/'.$content['cid'].'" target="_blank">' . $content['s_name'] . '</a></td>
                    <td class="w50">' . $content['s_sku'] . '</td>
                    <td class="text-center">' . $content['count'] . '</td>											
                    <td>' . number_format($price,2,'.',' '). ' руб.</td>		
                    <td>' . number_format($content['total'],2,'.',' '). ' руб.</td>
                    <td>'.$st.'</td>
                    <td>Неизвестно</td></tr>';
                    $i++; 
                    $total = $content['total'] + $total;
                }
                $total = $static['total'] - $static['nds'];
                $text .= '</tbody> 
				<tfoot>
				<tr><td colspan="5" class="text-right" ><b>Итого:</b></td><td colspan="3" class="text-left">'.number_format($total,2,'.',' ').' руб.</td></tr>
				<tr><td colspan="5" class="text-right" style="border-top: 0px solid #ddd;"><b>В т.ч. НДС (18%): </b></td><td style="border-top: 0px solid #ddd;" colspan="3" class="text-left">'.number_format($static['nds'],2,'.',' ').' руб.</td></tr>
				<tr><td colspan="5" class="text-right" style="border-top: 0px solid #ddd;"><b>Итого с НДС: </b></td><td style="border-top: 0px solid #ddd;" colspan="3" class="text-left">'.number_format($static['total'],2,'.',' ').' руб.</td></tr>
				</tfoot>
				</table> 
                </div>';
                $score = config('app.url').'/sales/pdfCreate/'.$secret_key;
                $ptitle = 'Заказ №PN-'.$static['id'];			
                set_title(array($ptitle));					
                $core->tpl->loadFile('sales/sales.view');
                $core->tpl->setVar('TITLE', $ptitle);
                $core->tpl->setVar('TEXT', $text);	
                $core->tpl->setVar('SCORE', $score);
                $core->tpl->end();				
            }
            else
            {
                //	include(ROOT . 'usr/tpl/404.tpl');
            }
        }
        else
        {
            include(ROOT . 'usr/tpl/404.tpl');
        }
    }
    
    function pdfCreate($secret_key){
        global $db, $config, $core, $url, $headTag, $content_conf, $lang;
        if($secret_key){
            $query = $db->query("SELECT s.*, o.name as name, o.inn as inn, o.kpp as kpp FROM " . DB_PREFIX . "_sales as s LEFT JOIN ".DB_PREFIX."_organizations as o on(s.oid = o.id) WHERE md5='" . $db->safesql($secret_key) . "' LIMIT 1");
            $static = $db->getRow($query);
            if($static)
            {	
                require_once(ROOT.'usr/plugins/fpdf/tfpdf.php');
                $pdf = new tFPDF(); 
                $pdf->AddPage(); 
                $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
                $pdf->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.ttf',true);
                //SET
                $textsize = 9;
                $topMargin = 5;
                $leftMargin = 4;
                $rightMargin = 3;
                $lineSpacing = 4.75;
                $width = 210 - $leftMargin - $rightMargin;
                $posX = $pdf->GetX();
                $posY = $pdf->GetY();
                $pdf->SetFont('DejaVu', '', $textsize);
                
                $pdf->Image(ROOT.'files/sales/fpdf/lo.png', 10, 20, -250);
                $pdf->Ln(25);
                $pdf->Cell(86, 4, "УДМУРТСКОЕ ОТДЕЛЕНИЕ №8618 ПАО СБЕРБАНК Г.", "LTR", 0, "L");
                $pdf->Cell(12, 4, "БИК", 1, 0, "L");
                $pdf->Cell(0, 4, "049401601", "LTR", 1, "L");
                $pdf->Cell(86, 4, "ИЖЕВСК", "LR", 0, "L");
                $pdf->Cell(12, 4, "Сч. №", "LTR", 0, "L");
                $pdf->Cell(0, 4, "30101810400000000601", "LR", 1, "L");
                $pdf->SetFont('DejaVu', '', 7);
                $pdf->Cell(86, 4, "Банк получателя", "LBR", 0, "L");
                $pdf->Cell(12, 4, "", "LBR", 0, "L");
                $pdf->Cell(0, 4, "", "LBR", 1, "L");
                $pdf->SetFont('DejaVu', '', $textsize);
                $pdf->Cell(43, 4, "ИНН 1840013705", 1, 0, "L");
                $pdf->Cell(43, 4, "КПП 184001001", 1, 0, "L");
                $pdf->Cell(12, 4, "Сч. №", 1, 0, "L");
                $pdf->Cell(0, 4, "40702810868000002201", 1, 1, "L");
                $pdf->Cell(86, 4, 'ООО "Атри-Энерго"', "LTR", 0, "L");
                $pdf->SetFont('DejaVu', '', 6);
                $pdf->Cell(12, 4, "Вид оп.", 1, 0, "L");
                $pdf->Cell(14, 4, "", "LR", 0, "L");
                $pdf->Cell(14, 4, "Срок плат.", 1, 0, "L");
                $pdf->Cell(0, 4, "", "LR", 1, "L");
                $pdf->Cell(86, 4, "", "LR", 0, "L");
                $pdf->Cell(12, 4, "Наз. пл.", 1, 0, "L");
                $pdf->Cell(14, 4, "", "LR", 0, "L");
                $pdf->Cell(14, 4, "Очер. плат.", 1, 0, "L");
                $pdf->Cell(0, 4, "5", "LR", 1, "L");
                $pdf->Cell(86, 4, "Получатель", "LBR", 0, "L");
                $pdf->Cell(12, 4, "Код", 1, 0, "L");
                $pdf->Cell(14, 4, "", "LBR", 0, "L");
                $pdf->Cell(14, 4, "Рез. поле", 1, 0, "L");
                $pdf->Cell(0, 4, "", "LBR", 1, "L");
                
                $pdf->SetFont('DejaVu', 'B', 12);
                $pdf->Ln(8);
                $pdf->Cell(0, 4, "Счет на оплату №PN-".$static['id']." от ".$static['date']." г.", 0, 1, "L");
                $pdf->Ln(4);
                $posX = $pdf->GetPageWidth();
                $posY = $pdf->GetY();
                $pdf->SetLineWidth(0.5);
                $pdf->Line(10, $posY, $posX-10, $posY);
                $pdf->SetLineWidth(0.2);
                $pdf->SetFont('DejaVu', '', 9);
                $pdf->Ln(4);
                $pdf->Cell(20, 8, "Поставщик:", 0, 0, "L");
                $pdf->SetFont('DejaVu', 'B', 9);
                $pdf->MultiCell(0, 4, 'ООО "Атри-Энерго", ИНН 1840013705, КПП 184001001, 426053, Удмуртская Респ, Ижевск г, Салютовская ул, дом № 5, помещение 10, тел.: (3412) 908 123', 0, "L");
                $pdf->SetFont('DejaVu', '', 9);
                $pdf->Ln(4);
                $pdf->Cell(20, 4, "Покупатель:", 0, 0, "L");
                $pdf->SetFont('DejaVu', 'B', 9);
                $pdf->MultiCell(0, 4, $static['name'].', ИНН '.$static['inn'].', КПП '.$static['kpp'], 0, "L");
                $pdf->Ln(4);
                $pdf->SetFont('DejaVu', 'B', 9);
                $pdf->Cell(10, 10, "№", 1, 0, 'C'); 
                $pdf->Cell(50, 10, "Номенклатура", 1, 0, 'C'); 
                $pdf->Cell(20, 10, "Артикул", 1, 0, 'C'); 
                $pdf->Cell(20, 10, "Кол-во", 1, 0, 'C'); 
                $pdf->Cell(20, 10, "Цена", 1, 0, 'C'); 
                $pdf->Cell(20, 10, "Сумма", 1, 0, 'C'); 
                $pdf->Cell(20, 10, "Наличие", 1, 0, 'C'); 
                $pdf->Cell(30, 10, "Дата поставки", 1, 1, 'C');
                
                $pdf->SetFont('DejaVu', '', 6); 
                $pdf->SetLineWidth(0.2); 
                $fill = false; 
                $query1 = $db->query("SELECT s.*, d.title, d.quantity, st.count_now, s_o.date_end FROM ".DB_PREFIX."_sales_detail as s LEFT JOIN ".DB_PREFIX."_catalog AS d on(d.id=s.cid) LEFT JOIN ".DB_PREFIX."_storage_detail AS st on (st.sale_detail_id=s.id) LEFT JOIN ".DB_PREFIX."_supply_detail AS sdt on (sdt.sdid=s.id) LEFT JOIN ".DB_PREFIX."_supply_order_detail AS sdto on (sdto.supply_d = s.id) LEFT JOIN ".DB_PREFIX."_supply_order AS s_o on (s_o.id = sdto.supply_o) WHERE s.sales_id = '".$static['id']."' AND s.status != '0'");
                $i = 1;
                while($content = $db->getRow($query1)){
                    if($content['discount'] == 0){
                        $price = $content['s_price'];
                        } else {
                        $price = $content['s_price'] - $content['s_price'] * $content['discount'] / 100;
                    }
                    
                    $st =  'Нет';
					if ($content['quantity'] >= $content['count']) $st = 'Да';
                    $cont = htmlspecialchars_decode($content['s_name']);
                    $res = $cont;
                    $flag = false;
                    
                    $posX = $pdf->GetX();
                    $posY = $pdf->GetY();
                    $pdf->MultiCell(10, 12, $i, 1, 'L');
                    $posX += 10;
                    $pdf->SetXY($posX, $posY);
                    $pdf->MultiCell(50, 4, $res, "LTR", 'L'); 
                    $posX += 50;
                    $pdf->SetXY($posX, $posY);
                    $pdf->Cell(20, 12, $content['s_sku'], 1, 0, 'C'); 
                    $posX += 20;
                    $pdf->SetXY($posX, $posY);
                    $pdf->Cell(20, 12, $content['count'], 1, 0, 'C'); 
                    $posX += 20;
                    $pdf->SetXY($posX, $posY);
                    $pdf->Cell(20, 12, number_format($price,2,'.',' '). ' руб', 1, 0, 'C'); 
                    $posX += 20;
                    $pdf->SetXY($posX, $posY);
                    $pdf->Cell(20, 12, number_format($content['total'],2,'.',' '). ' руб', 1, 0, 'C'); 
                    $posX += 20;
                    $pdf->SetXY($posX, $posY);
                    $pdf->Cell(20, 12, $st, 1, 0, 'C'); 
                    $posX += 20;
                    $pdf->SetXY($posX, $posY);
                    $pdf->Cell(30, 12, 'Неизвестно', 1, 1, 'C'); 
                    $i++; 
                }
                $pdf->SetXY($posX-150, $posY);
                $pdf->Cell(50, 12, '', "B", 1, 'C'); 
                
                $pdf->Ln(4);
                $pdf->SetFont('DejaVu', 'B', 9);
                $pdf->Cell(100, 4, "", 0, 0);
                $pdf->Cell(20, 4, "Итого: ", 0, 0, "R");
                $pdf->Cell(30, 4, number_format($static['total'] - $static['nds'],2,'.',' ').' руб', 0, 1, "R");
                $pdf->Cell(100, 4, "", 0, 0);
                $pdf->Cell(20, 4, "В т.ч. НДС (18%): ", 0, 0, "R");
                $pdf->Cell(30, 4, number_format($static['nds'],2,'.',' ').' руб', 0, 1, "R");
                $pdf->Cell(100, 4, "", 0, 0);
                $pdf->Cell(20, 4, "Итого с НДС: ", 0, 0, "R");
                $pdf->Cell(30, 4, number_format($static['total'],2,'.',' ').' руб', 0, 1, "R");
                $pdf->SetFont('DejaVu', '', 9);
                $pdf->Cell(0, 4, "Всего наименований ".($i-1).", на сумму ".number_format($static['total'],2,'.',' ')." руб.", 0, 1, "L");
                $pdf->SetFont('DejaVu', 'B', 9);
                $total = num2str($static['total']);
                $total = str_replace('( ', '', $total);
                $total = str_replace(' )', '', $total);
                $total = mb_strtoupper(mb_substr($total, 0, 1)).mb_substr($total, 1);
                $pdf->Cell(0, 4, $total, 0, 1, "L");
                $pdf->Ln(4);
                $pdf->Cell(0, 4, "Внимание! Счет на оплату действителен в течении 5 дней!", 0, 1, "L");
                $pdf->Ln(4);
                $posX = $pdf->GetPageWidth();
                $posY = $pdf->GetY();
                $pdf->SetLineWidth(0.5);
                $pdf->Line(10, $posY, $posX-10, $posY);
                $pdf->SetLineWidth(0.2);
                $pdf->Ln(4);
                
                $pdf->SetFont('DejaVu', 'B', 9);
                $pdf->Cell(30, 4, "Руководитель", 0, 0, "L");
                $pdf->Cell(60, 4, "", "B", 0, "L");
                $pdf->Cell(10, 4, "", 0, 0, "L");
                $pdf->Cell(60, 4, "Некопыткин А. В.", "B", 1, "C");
                $pdf->SetFont('DejaVu', '', 6);
                $pdf->Cell(30, 4, "", 0, 0, "L");
                $pdf->Cell(60, 4, "подпись", "", 0, "C");
                $pdf->Cell(10, 4, "", 0, 0, "L");
                $pdf->Cell(60, 4, "расшифровка подписи", 0, 1, "C");
                
                $pdf->SetFont('DejaVu', 'B', 9);
                $pdf->Cell(30, 4, "Бухгалтер", 0, 0, "L");
                $pdf->Cell(60, 4, "", "B", 0, "L");
                $pdf->Cell(10, 4, "", 0, 0, "L");
                $pdf->Cell(60, 4, "Емерхонова Л. И.", "B", 1, "C");
                $pdf->SetFont('DejaVu', '', 6);
                $pdf->Cell(30, 4, "", 0, 0, "L");
                $pdf->Cell(60, 4, "подпись", "", 0, "C");
                $pdf->Cell(10, 4, "", 0, 0, "L");
                $pdf->Cell(60, 4, "расшифровка подписи", 0, 1, "C");
                
                $pdf->SetFont('DejaVu', 'B', 9);
                $pdf->Cell(30, 4, "Менеджер", 0, 0, "L");
                $pdf->Cell(60, 4, "", "B", 0, "L");
                $pdf->Cell(10, 4, "", 0, 0, "L");
                $pdf->Cell(60, 4, "", "B", 1, "C");
                $pdf->SetFont('DejaVu', '', 6);
                $pdf->Cell(30, 4, "", 0, 0, "L");
                $pdf->Cell(60, 4, "подпись", "", 0, "C");
                $pdf->Cell(10, 4, "", 0, 0, "L");
                $pdf->Cell(60, 4, "расшифровка подписи", 0, 1, "C");
                
                //save
                $filename=ROOT.'files/sales/fpdf/'.$secret_key.'.pdf';
                $pdf->Output($filename,'F');
                $file_url= 'files/sales/fpdf/'.$secret_key.'.pdf';
                header('Location: https://atri-energo.ru/'.$file_url);
                
            }
        }
    }
    
    global $lang;
    switch(isset($url[1]) ? $url[1] : null) 
    {
        default:
		
        break;
        case "view":
		if(isset($url[2])) 
		{
			$secret_key = $url[2];
			view($secret_key);
        }
		else
		{
			location('/');
        }
        break;	
        case 'pdfCreate':
        if(isset($url[2])) 
		{
			$secret_key = $url[2];
			pdfCreate($secret_key);
        }
		else
		{
			location('/');
        }
        break;
        
    }                