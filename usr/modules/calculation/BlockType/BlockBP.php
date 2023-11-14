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

use Project\Actions\Calculation\Pump\ApiPumpCalculationAction;
use Project\UI\API\Requests\Calculation\PumpCalculationRequest;

	
class BlockBP extends \Calculation\BlockCalculationLoader{
	
	public $prepareData = array();
	public $dataLibrary = array();

	//конструктор класса
	function __construct(){		
		$this->dataLibrary = new \Calculation\Library\DataLibrary(); 
	}

	public function calculate() {
		$specification = array();
		$i = 1;	
		$gP = ($this->blockData['q']['clearValue']*$this->getCoefficientByBOVType($this->blockData['typeBlock']))/6;	
		$this->prepareData['gP'] = $gP;
		if($this->blockData['id'] != '22') {
			$gPn = ($this->blockData['q']['clearValue']*$this->getCoefficientByBOVType($this->blockData['typeBlock']))/5;
			$gPnAll = $gPn;
			$h = $this->blockData['h'];
			if($this->blockData['id'] == '92') {
				foreach ($this->requestData['blocks']['BP'] as $k => $v) {
					if($v['id'] != '92'){
						$gPnTemp = ($v['q']['clearValue']*$this->getCoefficientByBOVType($v['typeBlock']))/5;
						$gPnAll = $gPnAll + $gPnTemp;
					}	
					if($h < $v['h']) {
						$h = $v['h'];
					}				
				}			
			}
			$hPump = $h + 20 - $this->requestData['mainData']['p2']['clearValue'];
			$pumpSeries = 'MHI';
		
			$data = [
				'pumpSelector'      => 1,
				'q'                 => $gPnAll,
				'h'                 => $hPump,
				'oid'               => 507,
				'manufacture'       => '20',
				'model'             => $pumpSeries,
				'voltage'           => 2,
				'coefficient' 		=> 1,	
			];
			
			// create request object with some data
			$request = PumpCalculationRequest::injectData($data);
			$pc = app(ApiPumpCalculationAction::class)->run($request);	
			$result['cid'] = ($pc['data']['entities'][0]);	
			if($result['cid'] != 0){
				$parametr = array();
				$parametr['id'] = $result['cid'];		
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 2);	
				$i++;				
			} 			
		}
		//подбор расширительного бака
		$tc = new \Calculation\TankCalculation(); 
		$request = array(
			'q' => $this->blockData['q']['clearValue'],
			'h' => $this->blockData['h'],
			't11' => $this->blockData['t11']['clearValue'],
			't21' => $this->blockData['t21']['clearValue'],
			'systemType' => $this->blockData['typeBlock'],
		);
		$tc->setData($request);	
		$tankArray = $tc->tankSelection();	
		foreach ($tankArray as $key => $value) { 
			$parametr = array();
			$parametr['id'] = $value['cid'];		
			$tankResult = \Catalog_v2\Model\Catalog::getCatalog($parametr);
			$tankArray[$key]['attribute'] = $tankResult[0]['attribute'];
			$specification[] = $this->structureSpecification($i, $tankResult, $value['count']);	
		}
		//подбор соленоидного и балансировочного клапана
		$pipe = new \Calculation\pipeCalculation();
		$dn = $pipe->getDnByG($gP);	
		$dnArray = $this->dataLibrary->getData('Diameter')['dn'];	
		$dnKey = array_search($dn, $dnArray); 
		if($dnKey - 2 >= 0){
			$dnTrue = $dnArray[$dnKey - 2];
		} elseif($dnKey - 1 >= 0) {
			$dnTrue = $dnArray[$dnKey - 1];
		} else {
			$dnTrue = $dnArray[0];
		}
		//соленоидный клапан
		$parametr = array();
		$parametr['man'] = '195';
		$parametr['category'] = '353';
		$parametr['attribute'] = array('120' => $dnTrue,
									   '278' => 'Нормально закрыт'									  
									);	
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), '1');	
		$i++;
		//балансировочный клапан
		$parametr = array();
		$parametr['man'] = '13';
		$parametr['category'] = '290';
		$parametr['attribute'] = array('4' => $dnTrue,
									   '292' => 'MSV-B'									  
									);	
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), '1');	
		$i++;	
		//реле давления и кран под него 
		if($this->blockData['id'] != '22') {
			//реле давления
			$parametr = array();
			$parametr['id'] = '2287';		
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 2);	
			$i++;	
			//кран под реле давления
			$parametr = array();
			$parametr['id'] = '112808';		
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 2);	
			$i++;
		}
		//кран на подпитку
		$dn = $pipe->getDnByG($gP);	
		if($dn <= 50) {
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
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), ($this->blockData['id'] != '22') ? 7 : 3);	
		$i++;
		//краны на расширительные баки
		foreach ($tankArray as $key => $value) { 
			//dump($value['attribute']);		
			$parametr = array();
			$parametr['man'] = '6';
			$parametr['category'] = '557';
			$parametr['attribute'] = array('4' => $value['attribute']['250'],
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет',	
									   '291' =>  'Aquasfera Euro');	
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $value['count']);	
		}
		//обратный клапан 
		$dn = $pipe->getDnByG($gP);	
		if($dn <= 50) {			
			$parametr = array();
			$parametr['man'] = '6';
			$parametr['category'] = '289';
			$parametr['sku_like'] = '3003-';
			$parametr['attribute'] = array('4' => $dn);
		} else {					
			$parametr = array();
			$parametr['man'] = '13';
			$parametr['category'] = '289';
			$parametr['attribute'] = array('4' => $dn,
										'10' =>  'Межфланцевое',		
										'37' =>  '120',		
										'44' =>  'Чугун',	
										);				
		}
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), ($this->blockData['id'] == '92') ? 4 : 2);	
		$i++;
		
		if($this->blockData['id'] != '22') {
			//спусник
			$dn = $pipe->getDnByG($gP);	
			$dnTrue = 15;
			if($dn > 50) {	
				$dnTrue = 25;
			}
			$parametr = array();
			$parametr['man'] = '6';
			$parametr['category'] = '557';
			$parametr['attribute'] = array('4' => $dnTrue,
										'195' =>  'ручное (рычаг)',		
										'244' =>  'ВР/ВР',		
										'245' =>  'Нет',	
										'291' =>  'Aquasfera Euro'
										);	
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 2);	
			$i++;	
			//манометры		   
			$specification[] = $this->structureSpecification($i, $this->getManometrByP($this->blockData['h']+10, '18'), 2);	
			$i++;
			$specification[] = $this->structureSpecification($i, $this->getManometrByP($this->requestData['mainData']['p2']['clearValue'], '18'), 2);	
			$i++;
			//кран под манометры
			$parametr = array();
			$parametr['man'] = '7';
			$parametr['category'] = '299';
			$parametr['attribute'] = array('69' =>  'Кран',		
										'156' =>  'G1/2 - М20х1.5');									
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 4);	
			$i++; 
		}	
		$result = array();
		$result['spec'] = $specification;	
		$result['parameter']['maxDn'] = $pipe->getDnByG($gP);		
		$result['parameter']['havePump'] = ($this->blockData['id'] != '22') ? true : false;		
		return $result;
	}
}
