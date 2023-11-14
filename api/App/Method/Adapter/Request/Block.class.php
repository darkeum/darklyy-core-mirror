<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2020 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/


namespace Api\App\Method\Adapter\Request;

use \Selection\Controller\Request as SelectionRequest;
	
class Block {		
	protected $requestData = array();

	public function __construct($requestData){
		$this->requestData = $requestData;
		$this->SelectionRequest = new SelectionRequest(); 
	}

	public function getAdapterData(){		
		$this->SelectionRequest->setRequestId($this->requestData['pid']);
		$this->SelectionRequest->getSelectionRequest();
		$this->SelectionRequest->getCalculationData();
		$blockData = $this->SelectionRequest->getInputData();
		$blockRequestData = $this->SelectionRequest->BlockCalculation->requestData;
		$total = 0;
		$blockCount = 0;
		$h = 0;
		$i = 0;
		$blockRequest = array();
		$entities = $blockData;
		foreach ($blockData as $key => $value) {	
			unset($entities[$key]['price']);
			unset($entities[$key]['inputData']);
			$entities[$key]['price'] = $value['price']['allPrice'];
			$total = $total+$value['price']['allPrice'];	
			$blockCount++;
			if($value['type'] == 'BOV') {			
				$blockRequest[$i]['blockName'] = $value['inputData']['typeBlock']['value'];
				$blockRequest[$i]['t1'] = array(
					'title' => 'Температура подающего трубопровода Т11',
					'value' => $value['inputData']['t11']['value'],
					'type' => $value['inputData']['t11']['sub'],
				);
				$blockRequest[$i]['t2'] = array(
					'title' => 'Температура подающего трубопровода Т21',
					'value' => $value['inputData']['t21']['value'],
					'type' => $value['inputData']['t21']['sub'],
				);
				$q = $value['inputData']['q']['value'];
				$blockRequest[$i]['q'] = array(
					'title' => 'Тепловая нагрузка в МВт (Гкал/ч)',
					'value' => $q.' ('.round($q*1.163,2).')',
					'type' => 'МВт (Гкал/ч)',
				);
				$h = $value['inputData']['h']['value'];
				$i++;
				
			}
			if($value['type'] == 'BGV') {			
				$blockRequest[$i]['blockName'] = 'ГВС';
				$blockRequest[$i]['t1'] = array(
					'title' => 'Температура горячей воды Т3',
					'value' => $value['inputData']['t3']['value'],
					'type' => $value['inputData']['t3']['sub'],
				);
				$blockRequest[$i]['t2'] = array(
					'title' => 'Температура холодной воды Тв1',
					'value' => $value['inputData']['tb1']['value'],
					'type' => $value['inputData']['tb1']['sub'],
				);
				$q = $value['inputData']['q']['value'];
				$blockRequest[$i]['q'] = array(
					'title' => 'Тепловая нагрузка в МВт (Гкал/ч)',
					'value' => $q.' ('.round($q*1.163,2).')',
					'type' => 'МВт (Гкал/ч)',
				);
				$i++;
			}
			$entities[$key]['id'] = $key+1;			
		}
		$total = round($total, 2);
		// dd($blockData);
		$result = array();
		$result['id'] = $this->requestData['id'];
		$result['pid'] = $this->requestData['pid'];
		$result['type'] = $this->requestData['type'];	
		$result['object_name'] = $this->requestData['object_name'];	
		$result['fullTitle'] = 'Расчет блочного теплового пункта АТРИ-БТП №'.$this->requestData['id'];
		$result['description'] = 'Блочный индивидуальный тепловой пункт <b>АТРИ-БТП</b> поставляется в полной заводской готовности к монтажу на объекте. <br> <b>АТРИ-БТП</b> соответствует требованиям ТР ТС 004/2011 "О безопасности низковольтного оборудования", ТР ТС 020/2011 "Электромагнитная совместимость технических средств", ТР ТС 010/2011 "О безопасности машин и	оборудования".';
		$result['preview'] = config('app.url').'/files/media/block.png';
		$result['total'] = number_format($total,2,',',' ');
		$result['date'] = $this->requestData['date'];
		$result['dateView'] = date('d.m.Y H:i:s', strtotime($this->requestData['date']));
		$result['dateOnly'] = date('d.m.Y', strtotime($this->requestData['date']));
		$result['brand'] = 'АТРИ-ЭНЕРГО';
		$result['numb'] = $this->requestData['id'];
		$result['warranty'] = '24 месяца';
		$result['production'] = '4-8 недель';
		$result['blockCount'] = $blockCount.' шт.';
		$result['scheme'] = config('app.url').'/selection/scheme/'.$this->requestData['id'];
		$result['print'] = config('app.url').'/selection/print/'.$this->requestData['pid'];
		$result['general']['t1']['value'] = $blockRequestData['mainData']['t1']['value'];
		$result['general']['t1']['type']  = $blockRequestData['mainData']['t1']['type'];
		$result['general']['t2']['value'] = $blockRequestData['mainData']['t2']['value'];
		$result['general']['t2']['type']  = $blockRequestData['mainData']['t2']['type'];
		$result['general']['p1']['value'] = $blockRequestData['mainData']['p1']['value'];
		$result['general']['p1']['type']  = $blockRequestData['mainData']['p1']['type'];
		$result['general']['p2']['value'] = $blockRequestData['mainData']['p2']['value'];
		$result['general']['p2']['type']  = $blockRequestData['mainData']['p2']['type'];
		$result['general']['h']['value']  =	$h;
		$result['general']['h']['type']   = 'м.';
		$result['blocks'] = $blockRequest;
		
		$result['entities'] = $entities;
		$result['totalCount'] = $blockCount;

		return $result;
	}
	
	public function collectHeatRequest(){
		
	}	 

		


}	
	
?>