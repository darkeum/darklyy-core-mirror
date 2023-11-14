<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Calculation\BlockType;
use \Calculation\Library\DataLibrary as DataLibrary;
use Project\Actions\Calculation\Pump\ApiPumpCalculationAction;
use Project\UI\API\Requests\Calculation\PumpCalculationRequest;
	
class BlockBOV extends \Calculation\BlockCalculationLoader{
	
	public $prepareData = array();
	public $dataLibrary = array();
	//конструктор класса
	function __construct(){	
		$this->dataLibrary = new DataLibrary(); 	
	}

	//заполняем данные для подбора теплообменника
	public function prepareDataToHeatCalculation(){
		parent::prepareDataToHeatCalculation();
		switch($this->blockData['teploPr']) {
			default:
			case "1":
				$q = $this->blockData['q']['clearValue'];
			break;
			case "2":
				$q = round(($this->blockData['q']['clearValue']*0.75), 6);
			break;
			case "3":
				$q = round(($this->blockData['q']['clearValue']*0.5), 6);
			break;				
		}
		if($this->blockData['teplo'] == 1) {
			$q = $this->blockData['q']['clearValue'];
		}
		$this->heatData['Q'] = $q;
		$this->heatData['R1'] = '2';
		if($this->requestData['mainData']['p1']['clearValue']- $this->requestData['mainData']['p2']['clearValue'] < 7){
			$this->heatData['R1'] = '1';
		}		
		$this->heatData['R2'] = '2';		
		$this->heatData['T1'] = $this->requestData['mainData']['t1']['clearValue'];
		$this->heatData['T2'] = $this->requestData['mainData']['t2']['clearValue'];
		$this->heatData['T3'] = $this->blockData['t21']['clearValue'];
		$this->heatData['T4'] = $this->blockData['t11']['clearValue'];
		if($this->heatData['T2'] - $this->heatData['T3'] <=5) {
			$this->heatData['T2'] = $this->heatData['T3'] + 5;
		}
		if($this->blockData['typeBlock'] == '2') {
			$this->heatData['ptoTypeUse'] = 'Вентиляция';	
		}elseif($this->blockData['typeBlock'] == '3') {
			$this->heatData['ptoTypeUse'] = 'Технология';	
		} else {
			$this->heatData['ptoTypeUse'] = 'Отопление';	
		}
		
	}

	public function calculate() {
		$i = 1;
		//получаем данные теплообменника(ов)	
		// if($this->blockData['bov_type'] == 'Независимая') {
			// dump($this->blockData);
			// exit;
		if($this->blockData['bov_type'] == 'Независимая') {
			dump($this->blockData['bov_type']);
			$cidArray = $this->getHeatCalculation();
			// $cidArray = $this->getHeatCalculationLocalTemp();
			$this->prepareData['GTO1'] = $this->prepareNumeric($this->heatDataComplete['detailed']['lbl_G1']);
			$this->prepareData['GTO11'] = $this->prepareNumeric($this->heatDataComplete['detailed']['lbl_G2']);
			switch($this->blockData['teploPr']) {
				default:
				case "1":
					$this->prepareData['G1'] = 	$this->prepareData['GTO1'];
					$this->prepareData['G11'] = $this->prepareData['GTO11'];
				break;
				case "2":
					$this->prepareData['G1'] = 	round(($this->prepareData['GTO1']/0.75), 6);
					$this->prepareData['G11'] = round(($this->prepareData['GTO11']/0.75), 6);
				break;
				case "3":
					$this->prepareData['G1'] = 	round(($this->prepareData['GTO1']/0.5), 6);
					$this->prepareData['G11'] = round(($this->prepareData['GTO11']/0.5), 6);
				break;				
			}

			$this->prepareData['dPto'] = $this->prepareNumeric(explode(' / ', $this->heatDataComplete['detailed']['lbl_R2_R2S'])[0]);
			$specification = array();		
			foreach ($cidArray as $k => $v) {		
				$parametr = array();
				$parametr['id'] = $v;		
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $this->blockData['teplo']);	
				$i++;		
			}
		} else {
			$this->prepareData['G1'] = round($this->blockData['q']['clearValue']*1000/($this->requestData['mainData']['t1']['clearValue']-$this->requestData['mainData']['t2']['clearValue']), 1);
			$this->prepareData['G11'] = round($this->blockData['q']['clearValue']*1000/( $this->blockData['t11']['clearValue'] - $this->blockData['t21']['clearValue']), 1);
			$this->prepareData['dPto'] = 0;
		}	
		
		//подбираем насосное оборудование
		$pumpSeriesArray = array();
	
		$pumpCount = '1';
		if($this->blockData['pch'] == '0') {
			if($this->blockData['nasos_type'] == 'Сдвоенный') {
				$pumpSeriesArray[] = 'TOP-SD';
				$pumpSeriesArray[] = 'DPL';
			} else {
				$pumpSeriesArray[] = 'TOP-S';
				$pumpSeriesArray[] = 'IPL';
				$pumpCount = '2';
			}
		} else {
			if($this->blockData['nasos_type'] == 'Сдвоенный') {
				$pumpSeriesArray[] = 'YONOS MAXO-D';
				$pumpSeriesArray[] = 'DPL';
			} else {
				$pumpSeriesArray[] = 'YONOS MAXO';
				$pumpSeriesArray[] = 'IPL';
				$pumpCount = '2';
			}
		}
		$noPH = false;	
		foreach ($pumpSeriesArray as $k => $v) {
			$pumpU = '3~ 400 V / 50 Hz';
			if(($v == 'YONOS MAXO-D') || ($v == 'YONOS MAXO')) {
				$pumpU = '1~ 230 V / 50 Hz';
			}
			$data = [
				'pumpSelector'      => 1,
				'q'                 => $this->prepareData['G11']*1.1,
				'h'                 => $this->blockData['dp']['clearValue']+$this->prepareData['dPto']+2,
				'oid'               => 507,
				'manufacture'       => '20',
				'model'             => $v,
				'voltage'           => $pumpU,
			];
			
			// create request object with some data
			$request = PumpCalculationRequest::injectData($data);
			$pc = app(ApiPumpCalculationAction::class)->run($request);	
			$result['cid'] = ($pc['data']['entities'][0]);
			if($result['cid'] != 0){
				$parametr = array();
				$parametr['id'] = $result['cid'];		
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $pumpCount);	
				$i++;
				if(($v == 'YONOS MAXO-D') || ($v == 'YONOS MAXO')) {
					$noPH = true;
				}
				break;
			}
			
		}	
		//подбор регулирующего клапана	
		$parametr = array();
		if($this->blockData['bov_type'] == 'Зависимая') {		
			$shArray = array('70','71','72','73');	
			if(in_array($this->blockData['id'], $shArray)) {
				$parametr = array();
				$parametr['man'] = '13';
				$parametr['category'] = '562';
				$parametr['attribute'] = array(
					'10' => 'Фланцевое',
					'28' => '130',
					'40' => '16',
					'44' => 'Чугун',	
					'112' => '3 ходовой',
				);	
			}
		}	
		$klapanData = $this->calulationValve($this->prepareData['G1'], $parametr);	
		$klapanDataAtrr = $klapanData[0]['attribute'];		
		$specification[] = $this->structureSpecification($i, $klapanData, '1');	
		$i++;
		//подбор электропривода
		$parametr = array();
		if($klapanDataAtrr['4'] <= 50){
			$parametr['id'] = '2319';		
		}else{
			$parametr['id'] = '22106';		
		}				
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), '1');	
		$i++;
		//подбор шкафа
		// $specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), '1');	
		// $i++;
		//датчик температуры наружного воздуха
		$parametr = array();
		$parametr['id'] = '32202';		
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), '1');	
		$i++;		
		//датчик температуры погружной
		$parametr = array();
		$parametr['id'] = '18481';		
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), '2');	
		$i++;
		//датчики давления для преобразователя частоты (ПЧ)
		if($this->blockData['pch'] == '1'){
			if($noPH) {
				$countDDPch = 0;
			} else {
				$countDDPch = 2;
				$parametr = array();
				if($this->blockData['h']+10 >= 80){
					$parametr['id'] = '34304';		
				} else {
					$parametr['id'] = '34303';		
				}
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $countDDPch);	
			}			
		} else {
			$countDDPch = 0;
		}
		if($this->blockData['bov_type'] == 'Независимая') {
		//реле давления подпитки
			$parametr = array();
			$parametr['id'] = '33844';		
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), '1');	
			$i++;	
		}
		//реле давления насоса
		$parametr = array();
		$parametr['id'] = '2287';		
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $pumpCount);	
		$i++;		
		//кран под реле давления
		$parametr = array();
		$parametr['id'] = '112808';		
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $pumpCount+$countDDPch);	
		$i++;
		//подключаем класс для работы с диаметрами
		$pipe = new \Calculation\pipeCalculation();
		//подбор крана T11	
		$dn = $pipe->getDnByG($this->prepareData['G11']);
		if($dn < 50){
			$parametr = array();
			$parametr['man'] = '6';
			$parametr['category'] = '557';
			$parametr['attribute'] = array('4' => $dn,
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет',	
									   '291' =>  'Aquasfera Euro'
									);	
		} else {
			$parametr = array();
			$parametr['man'] = '134';
			$parametr['category'] = '556';
			$parametr['attribute'] = array(
				'4' => $dn,
				'44' => 'Сталь',
				'157' => '200',
				'195' => 'ручное (рычаг)',
				'196' => 'неполнопроходной',	
				'273' => 'Фланцевый',
			);				
		}	
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), '2');	
		$i++;	
		//подбор крана T1		
		$dn = $pipe->getDnByG($this->prepareData['G1']);	
		$parametr = array();
		$parametr['man'] = '134';
		$parametr['category'] = '556';
		$parametr['attribute'] = array(
			'4' => $dn,
			'44' => 'Сталь',
			'157' => '200',
			'195' => 'ручное (рычаг)',
			'196' => 'неполнопроходной',	
			'273' => 'Фланцевый',
		);		
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 2);	
		$i++;
		if($this->blockData['bov_type'] == 'Независимая') {
			//подбор крана T1 (на теплообменник)								
			if($this->blockData['teplo'] == 2)	{	
				$dn = $pipe->getDnByG($this->prepareData['GTO1']);
				$parametr = array();
				$parametr['man'] = '134';
				$parametr['category'] = '556';
				$parametr['attribute'] = array(
					'4' => $dn,
					'44' => 'Сталь',
					'157' => '200',
					'195' => 'ручное (рычаг)',
					'196' => 'неполнопроходной',	
					'273' => 'Фланцевый',
				);	
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 4);	
				$i++;
			}			
			//подбор крана на подпитку	
			$gP = ($this->blockData['q']['clearValue']*11.7)/6;			
			$dn = $pipe->getDnByG($gP);	
			if($dn < 50) {
				$parametr = array();
				$parametr['man'] = '6';
				$parametr['category'] = '557';
				$parametr['attribute'] = array('4' => $dn,
										'195' =>  'ручное (рычаг)',		
										'244' =>  'ВР/ВР',		
										'245' =>  'Нет',	
										'291' =>  'Aquasfera Euro');	
			} else {
				$parametr = array();
				$parametr['man'] = '146';
				$parametr['category'] = '288';
				$parametr['attribute'] = array('4' => $dn,
											'37' =>  '120',		
											'49' =>  'Чугун',		
											'223' =>  'Рукоятка');	
				
			}
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), '1');	
			$i++;
		}	
		//спусники
		$parametr = array();
		$parametr['man'] = '6';
		$parametr['category'] = '557';
		$parametr['attribute'] = array('4' => '25',
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет',	
									   '291' =>  'Aquasfera Euro'
									);
		$spCountPump = 0;							
		if($pumpCount == 2)	{
			$spCountPump = 2;
		}	
		if($this->blockData['bov_type'] == 'Независимая') {	
			$spCount = $this->blockData['teplo']*4+$spCountPump;
		} else {
			$spCount = $spCountPump+2;
		}
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $spCount);	
		$i++;
		//затворы на Т11 на нагреваемой стороне
		$dn = $pipe->getDnByG($this->prepareData['G11']);
		if($dn < 50) {
			$parametr = array();
			$parametr['man'] = '6';
			$parametr['category'] = '557';
			$parametr['attribute'] = array('4' => $dn,
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет',	
									   '291' =>  'Aquasfera Euro');	
		} else {
			$parametr = array();
			$parametr['man'] = '146';
			$parametr['category'] = '288';
			$parametr['attribute'] = array('4' => $dn,
										   '37' =>  '120',		
										   '49' =>  'Чугун',		
										   '223' =>  'Рукоятка');	
			
		}
		$ztCountPump = 1;							
		if($pumpCount == 2)	{
			$ztCountPump = 4;
		}						
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $ztCountPump);	
		$i++;
		//затвор на перемычке
		if($this->blockData['bov_type'] == 'Зависимая') {
			$dn = $pipe->getDnByG($this->prepareData['G11']);
			$dnTrue = $dn;
			if($dn >= 40){
				$dnArray = $this->dataLibrary->getData('Diameter')['dn'];	
				// dump($this->dataLibrary);
				// dump($dnArray);
				// exit;
				$dnKey = array_search($dn, $dnArray); 
				if($dnKey - 1 >= 0){
					$dnTrue = $dnArray[$dnKey - 1];
				}  else {
					$dnTrue = $dnArray[0];
				}
			}
			if($dnTrue < 50) {
				$parametr = array();
				$parametr['man'] = '6';
				$parametr['category'] = '557';
				$parametr['attribute'] = array('4' => $dnTrue,
										'195' =>  'ручное (рычаг)',		
										'244' =>  'ВР/ВР',		
										'245' =>  'Нет',	
										'291' =>  'Aquasfera Euro');	
			} else {
				$parametr = array();
				$parametr['man'] = '146';
				$parametr['category'] = '288';
				$parametr['attribute'] = array('4' => $dnTrue,
											'37' =>  '120',		
											'49' =>  'Чугун',		
											'223' =>  'Рукоятка');	
				
			}					
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
			$i++;
		}	
		if($this->blockData['bov_type'] == 'Независимая') {
			//затворы на Т11 на нагреваемой стороне (у теплообменников)
			if($this->blockData['teplo'] == 2)	{				
				$dn = $pipe->getDnByG($this->prepareData['GTO11']);
				if($dn < 50) {
					$parametr = array();
					$parametr['man'] = '6';
					$parametr['category'] = '557';
					$parametr['attribute'] = array('4' => $dn,
											'195' =>  'ручное (рычаг)',		
											'244' =>  'ВР/ВР',		
											'245' =>  'Нет',	
											'291' =>  'Aquasfera Euro');	
				} else {
					$parametr = array();
					$parametr['man'] = '146';
					$parametr['category'] = '288';
					$parametr['attribute'] = array('4' => $dn,
												'37' =>  '120',		
												'49' =>  'Чугун',		
												'223' =>  'Рукоятка');	
				}
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 4);	
				$i++;
			}
		}
		//фильтр сетчатый и обратный клапан на T11
		$dn = $pipe->getDnByG($this->prepareData['G11']);
		if($dn < 50) {
			//фильтр сетчатый на T11 при dn  < 50
			$parametr = array();
			$parametr['man'] = '6';
			$parametr['category'] = '284';
			$parametr['attribute'] = array('4' => $dn,
										'10' =>  'Муфтовое',		
										'44' =>  'Латунь',		
										'41' =>  'Сетчатый',	
										);
								
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
			$i++;
			//обратный клапан на T11	при dn  < 50		
			if($pumpCount == 2){
				$parametr = array();
				$parametr['man'] = '6';
				$parametr['category'] = '289';
				$parametr['sku_like'] = '3003-';
				$parametr['attribute'] = array('4' => $dn);
									
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $pumpCount);	
				$i++;
			}

		} else {
			//фильтр сетчатый на T11
			$parametr = array();
			$parametr['man'] = '43';
			$parametr['category'] = '284';
			$parametr['attribute'] = array('4' => $dn,
										'28' =>  '300',		
										'44' =>  'Чугун',		
										'41' =>  'Сетчатый с магнитной вставкой',	
										);
								
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
			$i++;
			//обратный клапан на T11			
			if($pumpCount == 2){
				$parametr = array();
				$parametr['man'] = '13';
				$parametr['category'] = '289';
				$parametr['attribute'] = array('4' => $dn,
											'10' =>  'Межфланцевое',		
											'37' =>  '120',		
											'44' =>  'Чугун',	
											);									
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $pumpCount);	
				$i++;
			}
		}
		//обратный клапан на перемычке
		if($this->blockData['bov_type'] == 'Зависимая') {
			$dn = $pipe->getDnByG($this->prepareData['G11']);
			$dnTrue = $dn;
			if($dn >= 40){
				$dnArray = $this->dataLibrary->getData('Diameter')['dn'];	
				$dnKey = array_search($dn, $dnArray); 
				if($dnKey - 1 >= 0){
					$dnTrue = $dnArray[$dnKey - 1];
				}  else {
					$dnTrue = $dnArray[0];
				}
			}
			if($dnTrue < 50) {
				$parametr = array();
				$parametr['man'] = '6';
				$parametr['category'] = '289';
				$parametr['sku_like'] = '3003-';
				$parametr['attribute'] = array('4' => $dn);
									
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
				$i++;
			} else {
				$parametr = array();
				$parametr['man'] = '13';
				$parametr['category'] = '289';
				$parametr['attribute'] = array('4' => $dn,
											'10' =>  'Межфланцевое',		
											'37' =>  '120',		
											'44' =>  'Чугун',	
											);									
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
				$i++;
			}					
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
			$i++;
		}	
		//предохранительный клапан
		$dn = $pipe->getDnByG($this->prepareData['G11']);
		$parametr = array();
		$parametr['man'] = '88';
		$parametr['category'] = '312';		
		if($this->blockData['h'] <= 40){
			$pSr = 6;
		}elseif($this->blockData['h'] <= 60){
			$pSr = 8;
		}else{
			$pSr = 10;
		}
		if($dn <= 100) {
			$parametr['attribute'] = array('85' => $pSr,
										   '340' => '25',		
										   '86' => 'Prescor B');		
		} else {
			$parametr['attribute'] = array('340' => '32',
										   '85' => $pSr,
									       '86' => 'Prescor S 700');	
		}
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
		$i++;
		//манометр Т1	
		$mnCount = 1;
		$mCountAll = 0;
		if($this->blockData['bov_type'] == 'Независимая') {
			$mnCount = 1+$this->blockData['teplo'];
		}	
		$mCountAll += $mnCount;				   
		$specification[] = $this->structureSpecification($i, $this->getManometrByP($this->requestData['mainData']['p1']['clearValue'], '18'), $mnCount);	
		$i++;
		//манометр Т2	
		$mnCount = 1;
		if($this->blockData['bov_type'] == 'Независимая') {
			$mnCount = 1+$this->blockData['teplo'];		
		}	
		$mCountAll += $mnCount;		
		$specification[] = $this->structureSpecification($i, $this->getManometrByP($this->requestData['mainData']['p2']['clearValue'], '18'), $mnCount);	
		$i++;
		//манометр Т11
		$mnCount = 1;
		if($this->blockData['bov_type'] == 'Независимая') {	
			$mnCount = 1;		
			if($pumpCount == 2){
				$mnCount = 5;
			}
			$mnCount += $this->blockData['teplo']*2;
			$mCountAll += $mnCount;
		} else {
			$mnCount = 2;		
			if($pumpCount == 2){
				$mnCount = 6;
			}
		}
		$mCountAll += $mnCount;				
		$specification[] = $this->structureSpecification($i, $this->getManometrByP($this->blockData['h']+10, '18'), $mnCount);	
		$i++;
		//кран на манометр
		$parametr = array();
		$parametr['man'] = '7';
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' =>  'Кран',		
									   '156' =>  'G1/2 - М20х1.5');									
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $mCountAll);	
		$i++;
		//термометр Т1
		$tCount = 1;
		$dn = $pipe->getDnByG($this->prepareData['G1']);
		if($this->blockData['bov_type'] == 'Независимая') {	
			$tCount = $this->blockData['teplo'];
			$dn = $pipe->getDnByG($this->prepareData['GTO1']);
		}		
		$specification[] = $this->structureSpecification($i, $this->getTermometrByTAndDn($this->requestData['mainData']['t1']['clearValue'], $dn, '18'), $tCount);	
		$i++;
		//термометр Т2
		$tCount = 1;
		$dn = $pipe->getDnByG($this->prepareData['G1']);
		if($this->blockData['bov_type'] == 'Независимая') {	
			$tCount = $this->blockData['teplo'];
			$dn = $pipe->getDnByG($this->prepareData['GTO1']);
		}
		$specification[] = $this->structureSpecification($i, $this->getTermometrByTAndDn($this->requestData['mainData']['t2']['clearValue'], $dn, '18'), $tCount);	
		$i++;
		//термометр Т11
		$tCount = 1;
		$dn = $pipe->getDnByG($this->prepareData['G11']);
		if($this->blockData['bov_type'] == 'Независимая') {	
			$tCount = $this->blockData['teplo']*2;
			$dn = $pipe->getDnByG($this->prepareData['GTO11']);
		}
		$specification[] = $this->structureSpecification($i, $this->getTermometrByTAndDn($this->blockData['t11']['clearValue'], $dn, '18'), $tCount);	
		$i++;
		$result = array();
		$result['spec'] = $specification;	
		$result['parameter']['maxDn'] = $pipe->getDnByG($this->prepareData['G11']);		
		$result['parameter']['bov_type'] = $this->blockData['bov_type'];		
		return $result;
	}
}
