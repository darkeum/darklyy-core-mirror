<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

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

use PhpOffice\PhpWord\Element\Field;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use mikehaertl\wkhtmlto\Pdf;

function printSelection($id, $type = null, $print = true)
{
	$model = \Request\Model\Request::class;
	$data = $model::select(array('request.*', 'organizations.name as oid_name', 'object.name as object_name'));			
	$data->leftJoin('organizations', 'request.oid', '=', 'organizations.id');
	$data->leftJoin('object', 'request.object', '=', 'object.id');
	$data->where('request.pid', '=', $id);
	$data->where('request.type', '=', '2');
	$requestData = $data->get()->toArray()[0];	
	$tpl = ROOT.'/files/tpl/selection/kp.docx';
	$document =  new \PhpOffice\PhpWord\TemplateProcessor($tpl);

	$block =  new \Api\App\Method\Adapter\Request\Block($requestData);
	$blockData = $block->getAdapterData();

	$pdf = new Pdf(config('app.url').'/selection/scheme/333');
	$options = array(
		'no-outline',           // option without argument
		'encoding' => 'UTF-8',  // option with argument
		'orientation' => 'Landscape',  // option with argument
		'page-size' => 'A3',  // option with argument
		'user-style-sheet' => ROOT.'/pdf.css',
	);
	$pdf->setOptions($options); 
	if (!$pdf->saveAs(ROOT.'/page.pdf')) {
		$error = $pdf->getError();
		dd( $error);
		// ... handle error here
	}
	$im = new Imagick();
	$im->setResolution(300,300);
	$im->readimage(ROOT.'page.pdf[0]'); 
	$im->setImageFormat('jpeg');    
	$im->writeImage('thumb.jpg'); 
	$im->clear(); 
	$im->destroy();

	$document->setImageValue('scheme', array('path' => ROOT.'/thumb.jpg', 'width' => 970, 'height' => 685, 'ratio' => false));
						
	$document->setValue('numb', $blockData['id']);  
	$document->setValue('date', $blockData['dateOnly']);  
	$document->setValue('object', $blockData['object_name']);        
	$document->setValue('t1_value', $blockData['general']['t1']['value']);           
	$document->setValue('t1_type', $blockData['general']['t1']['type']); 
	$document->setValue('t2_value', $blockData['general']['t2']['value']);           
	$document->setValue('t2_type', $blockData['general']['t2']['type']); 
	$document->setValue('p1_value', $blockData['general']['p1']['value']);           
	$document->setValue('p1_type', $blockData['general']['p1']['type']);   
	$document->setValue('p2_value', $blockData['general']['p2']['value']);           
	$document->setValue('p2_type', $blockData['general']['p2']['type']);             
	$document->setValue('h_value', $blockData['general']['h']['value']);             
	$document->setValue('h_type', $blockData['general']['h']['type']);   
	
	
	$cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
	$cellVCentered = array('valign' => 'center');
	$cellVCenteredAndVertical = array('valign' => 'center', 'textDirection' => \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR);
	$textStyleTitle = array('name' => 'Calibri', 'size' => '10', 'allCaps' => true, 'bold' => true);
	$textStyle = array('name' => 'Calibri', 'size' => '10');

	$table = new Table(array('borderSize' => 12, 'borderColor' => 'green', 'width' => 6000, 'unit' => TblWidth::PERCENT));
	$table->addRow(900);
	$table->addCell(121, $cellVCenteredAndVertical)->addText('Поз.',  $textStyleTitle, $cellHCentered);
	$table->addCell(496, $cellVCentered)->addText('Обозначение',  $textStyleTitle, $cellHCentered);
	$table->addCell(854, $cellVCentered)->addText('Наименование',  $textStyleTitle, $cellHCentered);
	$table->addCell(121, $cellVCenteredAndVertical)->addText('Кол.',  $textStyleTitle, $cellHCentered);
	$table->addCell(350, $cellVCentered)->addText('Примеч.',  $textStyleTitle, $cellHCentered);
	$order_data = array();
	$i = 0;
	foreach ($blockData['entities'] as $key => $value) {
		$i++;
		$order_data[]  = array(
			'n' => $i,
			'sku' =>  $value['type'].'-'.$blockData['id'].'-1',
			'name' => $value['nameShort'],
			's' => '1',
			'u' => 'шт',
			'price' => $value['price'],
			'total' => $value['price'],
			'time' => '4-8 недель',

		); 
		foreach ($value['spec'] as $sKey => $sValue) {
			$table->addRow();
			$table->addCell(121)->addText($i.'.'.$sValue['tm_n'], $textStyle);
			$table->addCell(496)->addText($sValue['tm_model'], $textStyle);
			$table->addCell(854)->addText($sValue['tm_title'], $textStyle);
			$table->addCell(121)->addText($sValue['tm_count'], $textStyle);
			$table->addCell(350)->addText($sValue['tm_manufacter'], $textStyle);
		}
	}

	
	// $phpWord = new PhpOffice\PhpWord\Element\Section(1);
	// $section = $phpWord->addSection();
// Adding Text element to the Section having font styled by default...
// $section->addText(
//     '"Learn from yesterday, live for today, hope for tomorrow. '
//         . 'The important thing is not to stop questioning." '
//         . '(Albert Einstein)'
// );
// // $section->setValue($table);
// $section->addText(
//     '"The greatest accomplishment is not in never falling, '
//         . 'but in rising again after you fall." '
//         . '(Vince Lombardi)'
// );


	$document->setComplexBlock('table', $table);
	   
	$document->cloneBlock('block_name', 3, true, true);  
	$document->cloneRowAndSetValues('n', $order_data);
	           
	$module = 'selection';
	
		$dir = ROOT.'files/'.$module.'/'.$id;
		if (!file_exists($dir))
		{
			mkdir($dir, 0777);						
		}		

	$file_name = $id;
	$file_url= ROOT.'files/'.$module.'/'.$id.'/'.$file_name.'.docx';
	$file_url_pdf= ROOT.'files/'.$module.'/'.$id.'/'.$file_name.'.pdf';
	$document->saveAs($file_url);	
	
			\Boot\Jobs\PdfJob::dispatchSync($file_url, $file_name . '.pdf');
			$pdf=new PDF_AutoPrint();
			$pageCount = $pdf->setSourceFile($file_url_pdf);
			for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
				$tplIdx = $pdf->importPage($pageNo);        
				$pdf->addPage();
				$pdf->useTemplate($tplIdx, null, null, null, null, true);
			}
			$pdf->AutoPrint(true);
		$pdf->Output($file_name.".pdf", "I");
		unlink($file_url);
		unlink($file_url_pdf);
		exit;
	// header('Location: '.$file_url_output);
	
}