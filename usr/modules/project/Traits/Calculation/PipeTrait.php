<?php

/**
 * @name        JMY CMS
 * @link        http://jmy.su/
 * @copyright   Copyright (C) 2012-2021 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */

namespace Project\Traits\Calculation;

trait PipeTrait
{
	//данные
	public $library_pipe = [];

	//заполнение табличных данных
	public function getLibraryPipeData()
	{
		//возможные внутренние диаметры 
		$this->library_pipe['dn'] = array(15, 20, 25, 32, 40, 50, 65, 80, 100, 125, 150, 200, 250, 300);
		//наружные диаметры в соответствии с внутренними диаметрами
		$this->library_pipe['dn_n'] = array(
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
		//толщина трубы в соответствии с внутренними диаметрами
		$this->library_pipe['tolshina'] =  array(
			'15' => '2.8',
			'20' => '2.8',
			'25' => '3.2',
			'32' => '3.2',
			'40' => '3.5',
			'50' => '3.5',
			'65' => '3.5',
			'80' => '4',
			'100' => '4',
			'125' => '4.5',
			'150' => '4.5',
			'200' => '6',
			'250' => '7',
			'300' => '8'
		);
		//толщина бесшовной трубы в соответствии с внутренними диаметрами
		$this->library_pipe['tolshina_bezshov'] =  array(
			'15' => '2.5',
			'20' => '2.5',
			'25' => '3',
			'32' => '3',
			'40' => '3.5',
			'50' => '3.5',
			'65' => '3.5',
			'80' => '5',
			'100' => '4',
			'125' => '5',
			'150' => '6',
			'200' => '6',
			'250' => '7',
			'300' => '8'
		);
	}

	//расчет диаметра трубы от расхода
	public function getDnByG($g)
	{
		if (empty($this->library_pipe)) {
			$this->getLibraryPipeData();
		}
		$dn = 15;
		$ksh = 0.5; //коофицент широховатости
		$kg = 958.4; //плотность			
		foreach ($this->library_pipe['dn'] as $k => $v) {
			$da = 1 / pow((1.14 + 2 * log10(($v / 1000) / ($ksh / 1000))), 2);
			$h = 0.00638 * $da * pow($g, 2) / (pow($v / 1000, 5) * $kg);
			if ($h > 4 && $h < 18) {
				$dn = $v;
				break;
			}
		}
		return $dn;
	}
}
