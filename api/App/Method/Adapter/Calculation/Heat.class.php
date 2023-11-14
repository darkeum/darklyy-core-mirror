<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2020 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/


namespace Api\App\Method\Adapter\Calculation;
	
class Heat {		
	protected $requestData = array();

	public function __construct(){
	}
	
	public function getCalculation(){

		global $core;

		// dd($core->auth);
		$hc = new \Calculation\HeatCalculation();
		$result = $hc->getHeatOrder($_POST, 'app');
		return $result;
	}	

	public function saveCalculation(){

		global $core;
		$id = intval($_GET['solution']);
		$hc = new \Calculation\HeatCalculation();
		$hc->getSolutionData($id);
		$hc->initClient();
		$hc->initAuth();	
		$hc->saveSolution();	
		$hc->getSolution();	
		$file = $hc->getSolutionAttache();
		$hc->createRequstAfter($id);		
		$result = array();
		$result['type'] = 'success';
		$result['aid'] = $file;		
		$result['request'] = $hc->requestID;		
		$result['request'] = $hc->requestID;		
		return $result;
	}	

		


}	
	
?>