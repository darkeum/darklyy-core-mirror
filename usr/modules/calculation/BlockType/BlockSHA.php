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
use \Calculation\Utilities\UtilitiesTool as UtilitiesTool;
use \Calculation\Library\DataLibrary as DataLibrary;
	
class BlockSHA extends \Calculation\BlockCalculationLoader{
	
	public $prepareData = array();
	public $dataLibrary = array();
	//конструктор класса
	function __construct(){		
		$this->ut = new UtilitiesTool();
		$this->dataLibrary = new DataLibrary(); 
	}
	
	public function calculate() {
		$i = 1;
		$count = 0;
		if(isset($this->requestData['blocks']['BOV'])){
			$count += count($this->requestData['blocks']['BOV']);
		}
		if(isset($this->requestData['blocks']['BGV'])){
			$count += count($this->requestData['blocks']['BGV']);
		}
		$parametr = array();		
		if($count == 1){
			$parametr['id'] = '115952';		
				
		} elseif($count == 2) {
			$parametr['id'] = '115953';				
		} else {
			$parametr['id'] = '115954';	
		}
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), '1');	
		$i++;
		$result = array();
		$result['spec'] = $specification;	
		$result['parameter']['maxDn'] = '0';		
		return $result;
	}
}
?>