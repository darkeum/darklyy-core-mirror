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
	
class DiffuserCalculation{	
	
	//данные
	public $requestData = array();
	public $resultData = array();
	
	//конструктор класса
	function __construct(){	
		//подключаем класс с инструментами для работы
		$this->UtilitiesTool = new UtilitiesTool();
	}
	//сбор входных данных
	public function setData($request){
		$this->requestData = array(	
			'dn1' => $this->UtilitiesTool->prepareNumeric($request['dn1']),
			'dn2' => $this->UtilitiesTool->prepareNumeric($request['dn2']),			
			'type' => 'Без покрытия',		
			'construct' => 'Концентрический'		
		);			
	}

	//подбор расширительного бака
	public function diffuserSelection() {
		if($this->requestData['dn1'] == $this->requestData['dn2']) {
			$this->resultData = array();
			return false;			
		} else {	
			$parametr = array();
			$parametr['category'] = '334';	
			$parametr['attribute']['108'] = $this->requestData['type'];	
			$parametr['attribute']['296'] = $this->requestData['construct'];	
			$return_array =array();
			$rtan = 99999;
			$dList = \Catalog_v2\Model\Catalog::getCatalog($parametr);
			if(!empty($dList)) {	
				foreach ($dList as $key => $value) {	
					$attribute = $value['attribute'];
					if(!isset($attribute['4']) || !isset($attribute['248'])){
						continue;
					}					
					if(($attribute['4'] == $this->requestData['dn1'])&&($attribute['98'] == $this->requestData['dn2'])){
						$return_array[] = $value;
						$this->resultData = $return_array;
						return($return_array);
					} elseif(($attribute['4'] == $this->requestData['dn1']))	{
						if(!empty($attribute['248'])){
							$tan1 = rad2deg(atan($attribute['248']/(($this->requestData['dn1']-$attribute['98'])/2)));
							foreach ($dList as $keySecond => $valueSecond) {	
								$attributeSecond = $valueSecond['attribute'];
								if(!isset($attributeSecond['4']) || !isset($attributeSecond['248'])){
									continue;
								}	
								if(($attributeSecond['4'] == $attribute['98'])&&($attributeSecond['98'] == $this->requestData['dn2']))
								{
									$tan2 = rad2deg(atan($attributeSecond['248']/(($attribute['98']-$this->requestData['dn2'])/2)));
									if($rtan >= abs($tan1-$tan2)) {
										$rtan = abs($tan1-$tan2);
										$pr1 = $value;
										$pr2 = $valueSecond;
									}
								}
							}
						} else {
							dd($value['id']);
						}
						
						
					}					
				}
				$return_array[] = $pr1;
				$return_array[] = $pr2;
				$this->resultData = $return_array;
				return($return_array);
			} else {
				$this->resultData = array();
				return false;
			}			
		}
	}

	public function getDiffuserL() {
		$l = 0;
		//dd($this->resultData);
		foreach ($this->resultData as $k => $v) {	
			$attribute = $v['attribute'];			
			$l = $l + $attribute['248'];
		}
		return $l;
	}
}
?>