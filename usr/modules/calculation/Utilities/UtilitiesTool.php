<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Calculation\Utilities;
	
class UtilitiesTool{

	//конструктор класса
	function __construct(){		

	}

	//замена запятых на точку
	public function commasReplace($text) {
		return str_replace(',','.',$text);	
	}
	
	//замена точек на запятые
	public function dotsReplace($text) {
		return str_replace('.',',',$text);	
	}

	//подготовка числа к расчету
	public function prepareNumeric($numeric, $dotsReplace = false){
		$result = floatval($this->commasReplace($numeric));
		if($dotsReplace){
			$result = $this->dotsReplace($result);
		}
		return $result;
	}

	public function prepareNumericArray($request, $key, $dotsReplace = false, $empty = ''){
		$keyArray = array();
		if(!is_array($key)){
			$keyArray[] = $key;
		} else {
			$keyArray = $key;
		}
		$result = $empty;
		foreach ($keyArray as $k => $v) {
			if(isset($request[$v]) && !empty($request[$v])){
				$result = $this->prepareNumeric($request[$v], $dotsReplace);
			}
			
		}
		return $result;
	}

	public function trimShy($text){
		return str_replace('&shy;','',$text);	
	}


	
}