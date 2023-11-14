<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Calculation;

use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

class BlockCalculation{
	
	
	//данные
	public $requestData = array();
	public $resultData = array();
	public $requestId = false;
	
	//конструктор класса
	function __construct(){		
		
	}

	//получение данных через &_POST
	public function getRequsetDataFromPost(){
		$this->getRequsetData($_POST);

	}	
	
	//получение данных через SQL
	public function getRequsetDataFromSQL($id, $getResult = false){
		$r = \Selection\Model\SelectionRequest::find($id)->toArray();
		if(!empty($r)){
			$this->requestId = $id;
			$this->requestData = json_decode($r['requestData'], true);
			if($getResult) {
				$this->resultData = json_decode($r['resultData'], true);	
			}
			return true;
		} else {
			return false;
		}	
	}

	//замена запятых на точку
	private function commasReplace($text) {
		return str_replace(',','.',$text);	
	}
	
	//замена точек на запятые
	private function dotsReplace($text) {
		return str_replace('.',',',$text);	
	}


	//подготовка числа к расчету
	private function prepareNumeric($numeric){
		return floatval($this->commasReplace($numeric));
	}

 	//собираем единиц измерений давления
	private function preparePressureType($type){
		switch($type) {
			default:
			case "МПа":
				$result = '1';
			break;
			case "кгс/см²":
				$result = '2';
			break;
			case "м.в.ст":
			case "м.в.ст.":
				$result = '3';
			break;			
		}
		return $result;
	}

	//конвертируем единицы измерений давления
	private function preparePressure($value, $type){
		$valueClear = $this->prepareNumeric($value);
		$typeClear = $this->preparePressureType($type);
		switch($typeClear) {
			default:
			case "1":
				$result = $valueClear*101.97162;
			break;
			case "2":
				$result = $valueClear*10;
			break;
			case "3":
				$result = $valueClear;
			break;			
		}
		return $result;
	}

 	//собираем единиц измерений нагрузки
	private function prepareMeasureType($type){
		$data = [
			1 => "Гкал/ч",
			2 => "Ккал/ч",
			3 => "МВт",
			4 => "Вт",
		];
		$key = array_search($type , $data);
		return $key ? $key : 1;	
	}

	//конвертируем единицы измерений нагрузки
	private function prepareMeasure($value, $type){
		$valueClear = $this->prepareNumeric($value);
		$typeClear = $this->prepareMeasureType($type);
		switch($typeClear) {
			default:
			case "1":
				$result = $valueClear;
			break;
			case "2":
				$result = $valueClear/1000000;
			break;
			case "3":
				$result = $valueClear*0.8598452;
			break;	
			case "4":
				$result = $valueClear*pow(8.5984522, -1);;
			break;			
		}
		return round($result, 6);
	}

	//стандартизируем данные
	private function getRequsetData($request){
		$this->requestId = intval($request['iUrl']);
		$iData = json_decode($request['iData'], true);
		$iDataGeneral = json_decode($request['iDataGeneral'], true);
		$blockData = explode('||', $request['postData']);		
		$requestDataPrepare = array();
		$requestDataPrepare['mainData'] = array(			
			't1' => array(
				'value' => $this->prepareNumeric($iData['iData']['t1']),
				'type' => '℃',
				'typeKey' => 1,
				'clearValue' => $this->prepareNumeric($iData['iData']['t1']),
				'clearType' => '℃',
				'clearTypeKey' => 1,
			),	
			't2' => array(
				'value' => $this->prepareNumeric($iData['iData']['t2']),
				'type' => '℃',
				'typeKey' => 1,
				'clearValue' => $this->prepareNumeric($iData['iData']['t2']),
				'clearType' => '℃',
				'clearTypeKey' => 1,
			),	
			'p1' => array(
				'value' => $iDataGeneral['iDataGeneral']['p1'],
				'type' => $iDataGeneral['iDataGeneral']['p1Type'],
				'typeKey' => $this->preparePressureType($iDataGeneral['iDataGeneral']['p1Type']),
				'clearValue' => $this->preparePressure($iDataGeneral['iDataGeneral']['p1'], $iDataGeneral['iDataGeneral']['p1Type']),
				'clearType' => 'м.в.ст.',
				'clearTypeKey' => 3,
			),		
			'p2' => array(
				'value' => $iDataGeneral['iDataGeneral']['p2'],
				'type' => $iDataGeneral['iDataGeneral']['p2Type'],
				'typeKey' => $this->preparePressureType($iDataGeneral['iDataGeneral']['p2Type']),
				'clearValue' => $this->preparePressure($iDataGeneral['iDataGeneral']['p2'], $iDataGeneral['iDataGeneral']['p2Type']),
				'clearType' => 'м.в.ст.',
				'clearTypeKey' => 3,
			),	
		);
		$requestDataPrepare['mainData']['blocks'] = array();
		foreach ($blockData as $key => $value) {			
			$valueArray = json_decode(substr($value, 3), true);
			$blockType = array_key_first($valueArray);
			$blockDataArray = array();
			switch($blockType) {				
				case "block_vvoda":
					$blockTypePrepare = 'BV';
					$blockDataArray = array(
						'id' => $valueArray[$blockType]['id'],
						'q' => array(
							'value' => $valueArray[$blockType]['q'],
							'type' => $valueArray[$blockType]['qType'],
							'typeKey' => $this->prepareMeasureType($valueArray[$blockType]['qType']),
							'clearValue' => $this->prepareMeasure($valueArray[$blockType]['q'], $this->prepareMeasureType($valueArray[$blockType]['qType'])),
							'clearType' => 'Гкал/ч',
							'clearTypeKey' => 1,
						),	
						'uzel_ucheta' => ($valueArray[$blockType]['uzel_ucheta'] == 'false' ? false : true),
						'regul_dav_t1' => ($valueArray[$blockType]['regul_dav_t1'] == 'false' ? false : true),
						'regul_dav_t2' => ($valueArray[$blockType]['regul_dav_t2'] == 'false' ? false : true),
						'bal_klapan' => ($valueArray[$blockType]['bal_klapan'] == 'false' ? false : true),
						'shkafPodpitka' => $valueArray[$blockType]['shkafPodpitka'],
						'shkafDopObor' => $valueArray[$blockType]['shkafDopObor'],
						'skafDi' => $valueArray[$blockType]['skafDi'],
						'message' => $valueArray[$blockType]['message'],
						'skafDo' => $valueArray[$blockType]['skafDo'],
					);	
					dump($blockDataArray);	 			
				break;
				case "block_otopleniya":
					// dump('dumpdump');
					// exit;
					$blockTypePrepare = 'BOV';
					//dump($valueArray[$blockType]);
					$blockDataArray = array(						
						'id' => $valueArray[$blockType]['id'],
						'q' => array(
							'value' => $valueArray[$blockType]['q'],
							'type' => $valueArray[$blockType]['qType'],
							'typeKey' => $this->prepareMeasureType($valueArray[$blockType]['qType']),
							'clearValue' => $this->prepareMeasure($valueArray[$blockType]['q'], $this->prepareMeasureType($valueArray[$blockType]['qType'])),
							'clearType' => 'Гкал/ч',
							'clearTypeKey' => 1,
						),	
						'kontr' => $valueArray[$blockType]['kontr'],
						'regKlapan' => $valueArray[$blockType]['regKlapan'],
						't11' => array(
							'value' => $valueArray[$blockType]['t11'],
							'type' => '℃',
							'typeKey' => 1,
							'clearValue' => $this->prepareNumeric($valueArray[$blockType]['t11']),
							'clearType' => '℃',
							'clearTypeKey' => 1,
						),	
						't21' => array(
							'value' => $valueArray[$blockType]['t21'],
							'type' => '℃',
							'typeKey' => 1,
							'clearValue' => $this->prepareNumeric($valueArray[$blockType]['t21']),
							'clearType' => '℃',
							'clearTypeKey' => 1,
						),	
						'dp' => array(
							'value' => $valueArray[$blockType]['dp'],
							'type' => $valueArray[$blockType]['dpType'],
							'typeKey' => $this->preparePressureType($valueArray[$blockType]['dpType']),
							'clearValue' => $this->preparePressure($valueArray[$blockType]['dp'],  $valueArray[$blockType]['dpType']),
							'clearType' => 'м.в.ст.',
							'clearTypeKey' => 3,
						),							
						'shkafAvtomatiky' => $valueArray[$blockType]['shkafAvtomatiky'],
						'bov_type' => $valueArray[$blockType]['bov_type'],
						// 'bov_type' => 'Зависимая', 
						'nasos_type' => $valueArray[$blockType]['nasos_type'],
						'ras_nasos' => $valueArray[$blockType]['ras_nasos'],
						'teplo' => $valueArray[$blockType]['teplo'],
						'reg_t' => $valueArray[$blockType]['reg_t'],
						'typeBlock' => $valueArray[$blockType]['typeBlock'],
						'typeBlock' => $valueArray[$blockType]['typeBlock'],
						'pch' => $valueArray[$blockType]['pch'],
						'bvNasos' => $valueArray[$blockType]['bvNasos'],
						'message' => $valueArray[$blockType]['message'],
						'h' => $valueArray[$blockType]['h'],
						//1 - 100/100%
						//2 - 75/75%
						//3 - 50/50%
						'teploPr' => $valueArray[$blockType]['teploPr'],
					);
					$dataForBlockBP = array();
					$dataForBlockBP['q'] = $blockDataArray['q'];
					$dataForBlockBP['h'] = $blockDataArray['h'];
					$dataForBlockBP['typeBlock'] = $blockDataArray['typeBlock'];
					$dataForBlockBP['t11'] = $blockDataArray['t11'];
					$dataForBlockBP['t21'] = $blockDataArray['t21'];
				break;
				case "block_podpitky":
					$blockTypePrepare = 'BP';
					$blockDataArray = array(
						'id' => $valueArray[$blockType]['id'],
						'povNasos' => $valueArray[$blockType]['povNasos'],
						'klapanSol' => $valueArray[$blockType]['klapanSol'],
						'rasshirBak' => $valueArray[$blockType]['rasshirBak'],
						'message' => $valueArray[$blockType]['message'],
						'nasosStan' => $valueArray[$blockType]['nasosStan'],
						'q' => $dataForBlockBP['q'],
						'h' => $dataForBlockBP['h'],
						'typeBlock' => $dataForBlockBP['typeBlock'],					
						't11' => $dataForBlockBP['t11'],					
						't21' => $dataForBlockBP['t21'],					
					);
				break;			
				case "block_gvs":
					$blockTypePrepare = 'BGV';
					$blockDataArray = array(
						'id' => $valueArray[$blockType]['id'], 
						'q' => array(
							'value' => $valueArray[$blockType]['qGVS'],
							'type' => $valueArray[$blockType]['qSelGVS'],
							'typeKey' => $this->prepareMeasureType($valueArray[$blockType]['qSelGVS']),
							'clearValue' => $this->prepareMeasure($valueArray[$blockType]['qGVS'], $this->prepareMeasureType($valueArray[$blockType]['qSelGVS'])),
							'clearType' => 'Гкал/ч',
							'clearTypeKey' => 1,
						),
						't1' => array(
							'value' => $valueArray[$blockType]['GVSt1'],
							'type' => '℃',
							'typeKey' => 1,
							'clearValue' => $this->prepareNumeric($valueArray[$blockType]['GVSt1']),
							'clearType' => '℃',
							'clearTypeKey' => 1,
						),	
						't2' => array(
							'value' => $valueArray[$blockType]['GVSt2'],
							'type' => '℃',
							'typeKey' => 1,
							'clearValue' => $this->prepareNumeric($valueArray[$blockType]['GVSt2']),
							'clearType' => '℃',
							'clearTypeKey' => 1,
						),	
						't3' => array(
							'value' => $valueArray[$blockType]['GVSt3'],
							'type' => '℃',
							'typeKey' => 1,
							'clearValue' => $this->prepareNumeric($valueArray[$blockType]['GVSt3']),
							'clearType' => '℃',
							'clearTypeKey' => 1,
						),	
						't4' => array(
							'value' => $valueArray[$blockType]['GVStv1'],
							'type' => '℃',
							'typeKey' => 1,
							'clearValue' => $this->prepareNumeric($valueArray[$blockType]['GVStv1']),
							'clearType' => '℃',
							'clearTypeKey' => 1,
						),
						'gt3' => array(
							'value' => $valueArray[$blockType]['GVSgt1'],
							'type' => 'м³/ч',
							'typeKey' => 1,
							'clearValue' => $this->prepareNumeric($valueArray[$blockType]['GVSgt1']),
							'clearType' => 'м³/ч',
							'clearTypeKey' => 1,
						),	
						'pb1' => array(
							'value' => $valueArray[$blockType]['pb1'],
							'type' => $valueArray[$blockType]['pb1SelGVS'],
							'typeKey' => $this->preparePressureType($valueArray[$blockType]['pb1SelGVS']),
							'clearValue' => $this->preparePressure($valueArray[$blockType]['pb1'],  $valueArray[$blockType]['pb1SelGVS']),
							'clearType' => 'м.в.ст.',
							'clearTypeKey' => 3,
						),	
						'kontrGVS' => $valueArray[$blockType]['kontrGVS'],
						'regKlapanGVS' => $valueArray[$blockType]['regKlapanGVS'],
						'typeNag' => $valueArray[$blockType]['typeNag'],
						'shemaPodkl' => $valueArray[$blockType]['shemaPodkl'],
						'shkafAvtomatikyGVS' => $valueArray[$blockType]['shkafAvtomatikyGVS'],
						'message' => $valueArray[$blockType]['message'],
						'dhodDBlockBOV' => $valueArray[$blockType]['dhodDBlockBOV'],
						'gvs_type' => $valueArray[$blockType]['gvs_type'],
						'nasos_type' => $valueArray[$blockType]['nasos_type'],
						'teplo' => $valueArray[$blockType]['teplo'],
						'reg_t' => $valueArray[$blockType]['reg_t'],
						//1 - 100/100%
						//2 - 75/75%
						//3 - 50/50%
						'teploPr' => $valueArray[$blockType]['teploPr'],
					
					);
				break;	
			}
			$requestDataPrepare['blocks'][$blockTypePrepare][] = $blockDataArray;
			
		}
		$requestDataPrepare['GBOVSum'] = 0;
		
		$this->requestData = $requestDataPrepare;
	}

	//сохранение данных 
	public function saveDataRequest(){	
		global $core;
		//$requestDataJson = json_encode($this->requestData, JSON_UNESCAPED_UNICODE);	
		$sr = \Selection\Model\SelectionRequest::find($this->requestId);
		$sr->update(array('requestData' => $this->requestData, 'daemon' => '0'));	
		$solution = $sr->toArray();
		$request = \Request\Model\Request::class;
		// $request::where(array(
		// 					array('pid', '=', $this->requestId),
		// 					array('type', '=', '2'),
		// 				))->get()->toArray();
		// $count = $request::where('pid', $this->requestId)->where("type", 2)->count();
		dump($this->requestId);
		dump('Бред');
		$request::firstOrCreate(
			['pid' => $this->requestId, 'type' => 2],
			[
				'uid_dir' => $core->auth->user_info['id'], 
				'title' => 'Расчет БТП №'.$this->requestId, 
				'title_user' => 'Расчет БТП №'.$this->requestId, 
				'request' => json_encode($this->requestData, JSON_UNESCAPED_UNICODE), 
				'oid' => $solution['oid'], 
				'object' => $solution['object'], 
				'org' => 1, 
				'date' => date('Y-m-d H:i:s'),
				'status' => '3',
			]
		);
		
						
		return $this->requestId;		
	}

	//сохранение данных 
	public function checkData(){	
		//$requestDataJson = json_encode($this->requestData, JSON_UNESCAPED_UNICODE);	
		$r = \Selection\Model\SelectionRequest::find($this->requestId)->toArray();	
		dump($r['requestData']);
		$result = true;	
		if(!empty($r)) {
			// if(json_encode($this->requestData, JSON_UNESCAPED_UNICODE) == $r['requestData']) {
			if(json_decode($r['requestData'], true) == $this->requestData) {
				$result = false;
			}
		}
		return $result;		
	}

	//сохранение результата
	public function saveResultData(){
		// dd($this->requestId);
		\Selection\Model\SelectionRequest::find($this->requestId)->update(array('resultData' => $this->resultData, 'daemon' => '1'));	
		//$resultDataJson = json_encode($this->resultData, JSON_UNESCAPED_UNICODE);			
		//$db->query("UPDATE ".DB_PREFIX."_selection_request SET `resultData` = '".$resultDataJson."', `daemon` = '1' WHERE id = '".$this->requestId."' LIMIT 1");
		return $this->requestId;		
	}


    public function getBlocksClassName($r){
        return "\Calculation\BlockType\Block".$r;
	}
	
	public function getPriceBySpec($result){
		$cidArray = array();
		$countNull = 0;
		$allPrice = 0;
		$price = [];
		foreach($result as $kSpec => $vSpec) {	
			if(!empty($vSpec['tm_cid'])){
				$cidArray[] = $vSpec['tm_cid'];	
				$p = \Price::getPrice($vSpec['tm_cid']);
				$price[ $vSpec['tm_cid'] ] = $p->price_purchase_;
			}
																
		}
		if(!empty($cidArray)){		
			foreach ($price as $keyPrice => $valuePrice){ 
				if(empty($valuePrice)){					
					$countNull++;
				}
			}
			foreach($result as $kSpec => $vSpec) {	
				if(is_array($vSpec)) {
					if(isset($price[$vSpec['tm_cid']])){
						$allPrice += $price[$vSpec['tm_cid']] * $vSpec['tm_count'];
					}
				}
				
			}
			
		}
		return array(
				'countNull' => $countNull,
				'priceArray' => $price,
				'allPrice' => $allPrice	
		);
	}
 
	public function calculationBlocks() {
		//dump($this->requestData);
		$flagBV = false;
		$resultArray = array();
		foreach ($this->requestData['blocks'] as $key => $value) {
			if($key != 'BV'){
				$blockClass = $this->getBlocksClassName($key);			
				if(class_exists($blockClass)){
					foreach ($value as $k => $v) {	
						$block = new $blockClass();
						$block->setRequestData($this->requestData);
						$block->setKey($k);
						$block->setBlockData($v);
						// try {
							$ansi = new \Bramus\Ansi\Ansi();
							$ansi->color(array(SGR::COLOR_FG_GREEN, SGR::COLOR_BG_BLACK));
							$ansi->text('Подбираю блок '.$key)->lf();
							$ansi->text('Данные блока')->lf();
							$ansi->text(json_encode($v, JSON_UNESCAPED_UNICODE))->lf();   
							$result = $block->calculate();			
						// } catch (\Exception $e) {
							// dump($e->getMessage());

							// return $e->getMessage();
						// }						
						if(isset($block->prepareData) && $key == 'BOV'){
							$this->requestData['GBOVSum'] += $block->prepareData['G1'];
						}
						$block->unsetRequestData();
						$this->requestData['blocks'][$key][$k]['calcData'] = $block;
						//простановка номенклатуры
						$resultArray['blocks'][$key][$k]['spec'] = $result['spec'];
						//простановка параметров
						$resultArray['blocks'][$key][$k]['parameter'] = $result['parameter'];
						//входные данные
						$resultArray['blocks'][$key][$k]['blockData'] = $v;
						//простановка цен						
						$resultArray['blocks'][$key][$k]['price'] = $this->getPriceBySpec($result['spec']);
						// dump($result);
					}			
				}
			} else {
				$flagBV= true;
			}
		}
		if($flagBV) {
			$blockClass = $this->getBlocksClassName('BV');			
			if(class_exists($blockClass)){
				foreach ($this->requestData['blocks']['BV'] as $k => $v) {	
					$block = new $blockClass();
					$block->setRequestData($this->requestData);
					$block->setKey($k);
					$block->setBlockData($v);
					$result = $block->calculate();	
					//простановка номенклатуры
					$resultArray['blocks']['BV'][$k]['spec'] = $result['spec'];
					//простановка параметров
					$resultArray['blocks']['BV'][$k]['parameter'] = $result['parameter'];
					//входные данные
					$resultArray['blocks']['BV'][$k]['blockData'] = $v;
					//простановка цен		
					$resultArray['blocks']['BV'][$k]['price'] = $this->getPriceBySpec($result['spec']);	
					if($v['uzel_ucheta'] != false) {
						$blockClass = $this->getBlocksClassName('SHU');	
						if(class_exists($blockClass)){
							$block = new $blockClass();
							$block->setRequestData($this->requestData);
							$block->setKey($k);
							$block->setBlockData($v);
							$result = $block->calculate();
							//простановка номенклатуры
							$resultArray['blocks']['SHU'][0]['spec'] = $result['spec'];
							//простановка параметров
							$resultArray['blocks']['SHU'][0]['parameter'] = $result['parameter'];
							//входные данные
							$resultArray['blocks']['SHU'][0]['blockData'] = $v;
							//простановка цен		
							$resultArray['blocks']['SHU'][0]['price'] = $this->getPriceBySpec($result['spec']);		
						}	
					}
					//dump($result);
				}			
			}
		}
		$blockClass = $this->getBlocksClassName('SHA');	
		if(class_exists($blockClass)){
			$block = new $blockClass();
			$block->setRequestData($this->requestData);
			$block->setKey($k);
			$block->setBlockData($v);
			$result = $block->calculate();
			//простановка номенклатуры
			$resultArray['blocks']['SHA'][0]['spec'] = $result['spec'];
			//простановка параметров
			$resultArray['blocks']['SHA'][0]['parameter'] = $result['parameter'];
			//входные данные
			$resultArray['blocks']['SHA'][0]['blockData'] = $v;
			//простановка цен		
			$resultArray['blocks']['SHA'][0]['price'] = $this->getPriceBySpec($result['spec']);			
		}	

		$this->resultData = $resultArray;
		return $this->resultData;
	}

	public function getWorkByParameter($type, $parameter){
		$workArray = array();
		switch($type) {			
			case "BV":
				$workArray = array(
					'25' => 39.6,		
					'32' => 41.8,		
					'40' => 44,		
					'50' => 45.6,		
					'65' => 48,		
					'80' => 50.16,		
					'100' => 52.8,		
					'125' => 64.6,		
					'150' => 68,		
				);							
			break;
			case "BOV":
				if($parameter['bov_type'] == 'Независимая') {
					$workArray = array(
						'25' => 48.24,		
						'32' => 50.92,		
						'40' => 53.6,		
						'50' => 55.48,		
						'65' => 58.4,		
						'80' => 57,		
						'100' => 60,		
						'125' => 72.2,		
						'150' => 76,		
					);		
				} else {
					$workArray = array(
						'25' => 44.64,		
						'32' => 47.12,		
						'40' => 49.6,		
						'50' => 50.92,		
						'65' => 53.6,		
						'80' => 51.68,		
						'100' => 54.4,		
						'125' => 69.16,		
						'150' => 72.8,		
					);	
				}										
			break;
			case "BGV":
				if($parameter['gvs_type'] == 'Смешанная') {
					$workArray = array(
						'25' => 53.28,		
						'32' => 56.24,		
						'40' => 59.2,		
						'50' => 60.04,		
						'65' => 63.2,		
						'80' => 61.56,		
						'100' => 64.8,		
						'125' => 76,		
						'150' => 80,		
					);		
				} else {
					$workArray = array(
						'25' => 48.24,		
						'32' => 50.92,		
						'40' => 53.6,		
						'50' => 55.48,		
						'65' => 58.4,		
						'80' => 57,		
						'100' => 60,		
						'125' => 72.2,		
						'150' => 76,		
					);	
				}										
			break;
			case "BP":
				if($parameter['havePump']) {
					$workArray = array(
						'25' => 34.56,		
						'32' => 36.48,		
						'40' => 38.4,		
						'50' => 41.04,		
						'65' => 43.2,		
						'80' => 43.2,		
						'100' => 43.2,		
						'125' => 43.2,		
						'150' => 43.2,		
					);		
				} else {
					$workArray = array(
						'25' => 14.4,		
						'32' => 15.2,		
						'40' => 16,		
						'50' => 19,		
						'65' => 20,		
						'80' => 20,		
						'100' => 20,		
						'125' => 20,		
						'150' => 20,		
					);	
				}										
			break;			
		}
		foreach ($workArray as $k => $v) {	
			if($k >= $parameter['maxDn']) {
				$work = $v;
				break;
			}
		}
		return $work;		
	}

	public function prepareResult() {
		$blocksArray = array();
		//формируем список блоков в правильном порядке
		if(isset($this->resultData['blocks']['SHA'])) {
			foreach ($this->resultData['blocks']['SHA'] as $k => $v) {	
				$blocksArray[] = array(
					'name' => 'Шкаф автоматики (SHA-'.$this->requestId.'-'.($k+101).')',
					'nameShort' => 'Шкаф автоматики',
					'img' => config('app.url').'/files/cats/222.jpg',
					'spec' => $v['spec'],
					'price' => $v['price'],
					'parameter' => $v['parameter'],
					'type' => 'SHA',

				);
			}
		}
		if(isset($this->resultData['blocks']['SHU'])) {
			foreach ($this->resultData['blocks']['SHU'] as $k => $v) {	
				$blocksArray[] = array(
					'name' => 'Шкаф узла учета (SHU-'.$this->requestId.'-'.($k+101).')',
					'nameShort' => 'Шкаф узла учета',
					'img' => config('app.url').'/files/cats/222.jpg',
					'spec' => $v['spec'],
					'price' => $v['price'],
					'parameter' => $v['parameter'],
					'blockData' => $v['blockData'],
					'type' => 'SHU',

				);
			}
		}
		if(isset($this->resultData['blocks']['BV'])) {
			foreach ($this->resultData['blocks']['BV'] as $k => $v) {	
				$blocksArray[] = array(
					'name' => 'Блок ввода (BV-'.$this->requestId.'-'.($k+101).')',
					'nameShort' => 'Блок ввода',
					'img' => config('app.url').'/usr/tpl/dev2/assest/images/1/block_vvoda.jpg',
					'spec' => $v['spec'],
					'price' => $v['price'],
					'parameter' => $v['parameter'],
					'blockData' => $v['blockData'],
					'type' => 'BV',

				);
			}
		}
		if(isset($this->resultData['blocks']['BOV'])) {
			foreach ($this->resultData['blocks']['BOV'] as $k => $v) {	
				$blocksArray[] = array(
					'name' => 'Блок отопления/вентиляции (BOV-'.$this->requestId.'-'.($k+101).')',
					'nameShort' => 'Блок отопления/вентиляции',
					'img' => config('app.url').'/usr/tpl/dev2/assest/images/1/bov.jpg',
					'spec' => $v['spec'],
					'price' => $v['price'],
					'parameter' => $v['parameter'],
					'blockData' => $v['blockData'],
					'type' => 'BOV',

				);
				if(isset($this->resultData['blocks']['BP'][$k])){
					$blocksArray[] = array(
						'name' => 'Блок подпитки (BP-'.$this->requestId.'-'.($k+101).')',
						'nameShort' => 'Блок подпитки',
						'img' => config('app.url').'/usr/tpl/dev2/assest/images/1/bp.jpg',
						'spec' => $this->resultData['blocks']['BP'][$k]['spec'],
						'price' => $this->resultData['blocks']['BP'][$k]['price'],
						'parameter' => $this->resultData['blocks']['BP'][$k]['parameter'],
						'blockData' => $this->resultData['blocks']['BP'][$k]['blockData'],
						'type' => 'BP',

					);
				}
			}
		}
		if(isset($this->resultData['blocks']['BGV'])) {
			foreach ($this->resultData['blocks']['BGV'] as $k => $v) {	
				$blocksArray[] = array(
					'name' => 'Блок горячего водоснабжения (BGV-'.$this->requestId.'-'.($k+101).')',
					'nameShort' => 'Блок горячего водоснабжения',
					'img' => config('app.url').'/usr/tpl/dev2/assest/images/1/block_gvs.jpg',
					'spec' => $v['spec'],
					'price' => $v['price'],
					'parameter' => $v['parameter'],
					'blockData' => $v['blockData'],
					'type' => 'BGV',
				);
			}
		}	
		//убираем повторяющиеся позиции		
		foreach ($blocksArray as $key => $value) {	
			$specPreapre = array();
			$ii = 0;
			foreach ($value['spec'] as $sKey => $sValue) {
			if(isset($sValue['tm_cid'])){
				if(array_key_exists($sValue['tm_cid'], $specPreapre)) {
					$sValue['tm_count'] = $specPreapre[$sValue['tm_cid']]['tm_count'] + $sValue['tm_count'];
					$sValue['tm_n'] = $specPreapre[$sValue['tm_cid']]['tm_n'];
					$specPreapre[$sValue['tm_cid']] = $sValue;
				} else {
					$ii++;
					$url = config('app.url').'/catalog_v2/fast_view_my/'.$sValue['tm_cid'];
					if($sValue['tm_aid'] != '0') {
						$url = config('app.url').'/download/'.$sValue['tm_aid'];
					}
					$sValue['tm_n'] = $ii;		
					$sValue['tm_url'] = $url;		
					$specPreapre[$sValue['tm_cid']] = $sValue;
				}	
			}
				
			}			
			$blocksArray[$key]['spec'] = array_values($specPreapre);
		}
		return $blocksArray;
	}

	public function getResultTemplate() {
		$blocksArray = $this->prepareResult();
	//	dump($this->resultData);
		//вывод результата 
		$template = '<div style="background: #e7e7e7;">
		<div class="titleRas"><h1>Результаты расчетов</h1></div>
		<div>';
		foreach ($blocksArray as $key => $value) {	
			$work = 0;
			$workCco = 1500;
			$cco = 2;
			if($value['type'] == 'SHU' || $value['type'] == 'SHA'){
				$cco = 2;
			} else {				
				$work = $this->getWorkByParameter($value['type'], $value['parameter']);					
			}
			$template .= '<div class="aspect-tab">
		 					<input id="item-'.$key.'" type="checkbox" class="aspect-input" name="aspect">
		 	 				<label for="item-'.$key.'" class="aspect-label"></label>
								<div class="aspect-content">
									<div class="aspect-info">
										<img src="'.$value['img'].'" alt=""  height="48" style="vertical-align: midumple;"/>
										<span class="aspect-name"><strong>'.$value['name'].'</strong></span>
									</div>
									<div class="aspect-stat">
										<div class="all-opinions">											
											<span>Стоимость: </span>
											<span style="font-size: 18px; font-weight: bold;">'.number_format(($value['price']['allPrice']+$work*$workCco)*$cco,2,',',' ').' руб.</span>
										</div>
										<div>
											
										</div>
									</div>
								</div>
								<div class="aspect-tab-content">
										
													<table>
														<tr>
															<th>#</th>
															<th>Наименование</th>
															<th>Модель</th>
															<th>Артикул</th>
															<th>Производитель</th>
															<th>Ед. изм.</th>
															<th>Кол-во</th>
															<th>Вес</th>
														
															<th>Просмотр</th>
														</tr>';
														$i = 0;
														$total = 0;
														//dump($value);
													//	dump($value['price']['priceArray']);
														foreach ($value['spec'] as $sKey => $sValue) {
															$i++;
															$url = config('app.url').'/catalog_v2/fast_view_my/'.$sValue['tm_cid'];
															if($sValue['tm_aid'] != '0') {
																$url = config('app.url').'/download/'.$sValue['tm_aid'];
															}
															$price = 0;
															if(is_array($sValue)){															
																if(isset($value['price']['priceArray'][$sValue['tm_cid']])){
																	// dd($value['price']['priceArray']);
																	$price = $value['price']['priceArray'][$sValue['tm_cid']];
																}
																$total = $total+$price*$sValue['tm_count'];
																$template .= '<tr>
																				<td>'.$i.'</td>
																				<td>'.ee($sValue['tm_title']).'</td>
																				<td>'.ee($sValue['tm_model']).'</td>
																				<td>'.$sValue['tm_sku'].'</td>
																				<td>'.$sValue['tm_manufacter'].'</td>
																				<td>'.$sValue['tm_unit'].'</td>
																				<td>'.$sValue['tm_count'].'</td>
																				<td>'.$sValue['tm_weight'].'</td>

																				<td><a data-fancybox data-type="iframe" data-width="2048" data-src="'.$url.'" href="javascript:;" 		class="btnClassic">Каталог</a></td>
																			</tr>';
															}
														}	
													// 	$template .= '<tr>
													// 	<td></td>
													// 	<td></td>
													// 	<td></td>
													// 	<td></td>
													// 	<td></td>
													// 	<td></td>
													// 	<td></td>
													// 	<td></td>
													// 	<td></td>
													// 	<td>'.	$total .'</td>
													// 	<td></td>
													// </tr>';
									$template .= '</table>
											
								</div>
							</div>';

		}
		
		$request = \Request\Model\Request::where('pid', $this->requestId)->where('type', 2)->first();;
		$template .= '
		<style>
		a:hover {
			text-decoration: none !important;
		}
		</style>
		<div class="modalBottom"><button type="button" class="btn btn-dark" onclick="$.arcticmodal(\'close\');">Отмена</button>
					<a href="https://lk.atri-energo.ru/e-commerce/design/calculations/'.$request->id.'/calculation" target="_blank" class="btn btn-danger">Перейти в личный кабинет</a></div>';


		
		 $template .= '</div></div>';
		return $template;
	}
	
}
