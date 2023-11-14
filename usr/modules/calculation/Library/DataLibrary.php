<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Calculation\Library;
	
class DataLibrary{

	public $dataLibrary = array();
	//конструктор класса
	function __construct(){		

	}

	//получения имя класса где хранится справочные данные
	public function getDataClassName($r){
        return "\Calculation\Library\Data\Data".$r;
    }

	//получения имя класса где хранится справочные данные
	public function getData($sparv) {
		$dataClass = $this->getDataClassName($sparv);			
		if(class_exists($dataClass)){
			$data = new $dataClass();
			$dataLibrary[$sparv] = $data->getDataLibrary();
			return $dataLibrary[$sparv];
		} else {
			return false;
		}
	}
	
	
}