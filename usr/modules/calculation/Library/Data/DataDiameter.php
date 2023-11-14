<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Calculation\Library\Data;
	
class DataDiameter{

	public $sprav = array();

	public $dataLibrary = array();
	//конструктор класса
	function __construct(){		

	}

	//получения имя класса где хранится справочные данные
	public function getDataLibrary(){
       //возможные внутренние диаметры 
		$this->sprav['dn'] = array(15,20,25,32,40,50,65,80,100,125,150,200,250,300);
		//наружные диаметры в соответствии с внутренними диаметрами
		$this->sprav['dn_n'] = array(
			'15' => '21.3',
			'20' => '25',
			'25' => '32',
			'32' => '38',
			'40' => '45',
			'50' => '57',
			'65' => '76',
			'80' => '89',
			'100' => '108',
			'125' => '133',
			'150' => '159',
			'200' => '219',
			'250' => '273',
			'300' => '325'
		);
		return $this->sprav;
    }
}