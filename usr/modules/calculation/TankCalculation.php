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
use \Calculation\Utilities\UtilitiesTool as UtilitiesTool;
	
class TankCalculation{	
	
	//данные
	public $requestData = array();
	public $ut;
	
	//конструктор класса
	function __construct(){	
		//подключаем класс с инструментами для работы
		$this->ut = new UtilitiesTool();
	}

	public function getСoefficientBySystemType($type){
		switch($type) {
			default:
			case "1":
			case "Отопление":
				$result = 11.7;
			break;
			case "2":
			case "Вентиляция":
				$result = 8.6;
			break;
			case "3":
			case "Технология":
				$result = 30;
			break;				
		}
		return $result;
	}
	
	//сбор входных данных
	public function setData($request){
		$this->requestData = array(	
			'q' => $this->ut->prepareNumeric($request['q']),
			'h' => $this->ut->prepareNumeric($request['h']),
			't11' => $this->ut->prepareNumeric($request['t11']),
			't21' => $this->ut->prepareNumeric($request['t21']),
			'coefficien' => $this->getСoefficientBySystemType($request['systemType']),
		);			
	}

	//подбор коэффициент расширения о температуры
	public function getExpansionRatioByT($t){
		$spravArray = array(
				'40'  => 	'0.75',
				'50'  => 	'1.18',
				'60'  => 	'1.68',
				'70'  => 	'2.25',
				'80'  => 	'2.89',
				'90'  => 	'3.58',
				'100' => 	'4.34',
				'110' => 	'5.16');
		foreach ($spravArray as $key => $value) { 
			if($key - $t >= 0){
				return $value;
			}
		}
	}
	//подбор расширительного бака
	public function tankSelection() {
		//подсчет объема системы
		$systemVolume = $this->requestData['q']*$this->requestData['coefficien']*1.2;
		//получаем коэффициент расширения
		$tSr = ($this->requestData['t11'] + $this->requestData['t21'])/2;
		$expansionRatio = $this->getExpansionRatioByT($tSr);
		//давление статики
		$pWork = $this->requestData['h'] + 5;
		//давление срабатывания клапана
		if($this->requestData['h'] <= 40){
			$pSr = 60;
		}elseif($this->requestData['h'] <= 60){
			$pSr = 80;
		}else{
			$pSr = 100;
		}
		//объем баков		
		$tankVolume = (10*$systemVolume*$expansionRatio)/(1-($pWork+10)/($pSr+10));
		//подбор баков
		if($tankVolume <= 100){
			$parametr = array();
			$parametr['man'] = '29';
			$parametr['category'] = '475';
			$parametr['attribute'] = array(		
				'140' => 'WAV'	
			);
			$tankList = array();
			$tankArray = \Catalog_v2\Model\Catalog::getCatalog($parametr);
			foreach ($tankArray as $key => $value) { 
				$attribute = json_decode($value['attribute'], true);			
				$tankList[$key] = $attribute['221'];							
			}
			asort($tankList);
			$result = array();
			foreach ($tankList as $key => $value) { 
				if($value - $tankVolume >= 0){
					$result[] = array(
						'cid' => $tankArray[$key]['id'],
						'count' => 1,
						'value' => $value,
					);	
					break;
				}
			}
		} else {
			$tankVolumeClear = 	ceil($tankVolume/100)*100;
			$parametr = array();
			$parametr['man'] = '29';
			$parametr['category'] = '474';
			$parametr['attribute'] = array(		
				'140' => 'Wester WRV',		
			);
			$tankList = array();
			$tankArray = \Catalog_v2\Model\Catalog::getCatalog($parametr);
			foreach ($tankArray as $key => $value) { 
				$attribute = $value['attribute'];
				if($attribute['221'] >=100 && $attribute['221'] <= 500) {					
					$tankList[$key] = $attribute['221'];					
				}				
			}
			arsort($tankList);
			$tankListCompl = array();
			foreach ($tankList as $key => $value) { 
				$count = intval($tankVolumeClear/$value);
				$tankVolumeClear -= ($count*$value);
				$tankListCompl[] = array(
					'id' => $key,
					'value' => $value,
					'count' => $count
				);
			}
			$result = array();
			foreach ($tankListCompl as $key => $value) { 
				if($value['count'] > 0){
					if($value['value'] == '100'){
						$parametr = array();
						$parametr['man'] = '29';
						$parametr['category'] = '475';
						$parametr['attribute'] = array(		
							'140' => 'Wester WAV',		
							'221' => '100',		
						);
						$tankResult = \Catalog_v2\Model\Catalog::getCatalog($parametr);
						$result[] = array(
							'cid' => $tankResult[0]['id'],
							'count' => $value['count'],
							'value' => $value['value'],
						);
					} else {
						$result[] = array(
							'cid' => $tankArray[$value['id']]['id'],
							'count' => $value['count'],
							'value' => $value['value'],

						);
					}					
				}
			}
		}		
		return $result;	
	}
}
?>