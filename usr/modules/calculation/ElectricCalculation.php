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
use PhpOffice\PhpWord\Element\Field;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use mikehaertl\wkhtmlto\Pdf;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use Amenadiel\JpGraph\Util;
use \Boot\System\Database\DB;
use \Boot\Models\Attach;
use \Catalog_v2\Model\Catalog;

class ElectricCalculation
{

	//данные
	public $requestData = array();
	public $ut;
	public $result = array();
	public $collectPumpData = array();
	public $calcResult = array();
	public $pump = null;
	public $requestId = null;
	public $file = null;
	public $uid = 0;
	public $controller = [];

	//конструктор класса
	function __construct()
	{
		//подключаем класс с инструментами для работы
		$this->ut = new UtilitiesTool();
	}


	/**
	 * Сбор и стандартизация данных
	 *
	 * @param 	array    $request входные данные
	 */
	public function setData($request)
	{
		$systemCount = 0;
		$hasFrequency = false;
		$hasRegulatorAnalog = false;
		$hasRegulator3 = false;
		foreach ($request as $key => $value) {
			if ($key == 'bov' || $key == 'bgv') {
				foreach ($value as $k => $v) {
					if ($v['active'] == 1) {
						$systemCount++;
						if ($v['frequency'] == 1) {
							$hasFrequency = true;
						}
						if ($v['regulator'] == 1) {
							$hasRegulatorAnalog = true;
						} else {
							$hasRegulator3 = true;
						}
						$v['system'] = $key;
						$system[] = $v;
					}
				}
			}
		}
		$this->requestData = [
			'systemCount' => $systemCount,
			'hasFrequency' => $hasFrequency,
			'hasRegulatorAnalog' => $hasRegulatorAnalog,
			'hasRegulator3' => $hasRegulator3,
			'controller' => $request['general']['controller'],
			'system' => $system,
		];
	}

	public function calc()
	{
		$result = [];
		$controller = $this->chooseController();
		foreach ($controller['controller'] as $key => $value) {
			$result[] = $value['catalog'];
		}

		dd($this->chooseControllerKey());
		return $result;
	}

	public function calcVisual()
	{
		$i = 1;
		$calc = $this->calc();
		$specification = [];
		foreach ($calc as $key => $value) {
			$specification[] = $this->structureSpecification($i, $value, '1');
			$i++;
		}
		return $specification;
	}

	public function req($n, $key, $xCount)
	{
		// if ($n >= 100) {
		// 	return false; 
		// }
		// if(!empty(array_filter($xCount))){

		// }

		// return $n * factorial($n - 1); // здесь происходит повторный вызов функции 
	}

	public function minus($array1, $array2)
	{
		$res = array();
		for ($i = 0; $i < count($array1); $i++) {
			if ($array1[$i] - $array2[$i] < 0) {
				return $array1;
			}
			$res[$i] = $array1[$i] - $array2[$i];
		}
		$flag = true;
		for ($i = 0; $i < count($array1) - 3; $i++) {
			if ($array1[$i] - $array2[$i] > 0) {
				$flag = false;
			}
		}
		if ($flag && ($res[7] != 0 || $res[7] != 0 || $res[7] != 0)) {
			return $array1;
		} else {
			return $res;
		}
	}


	public function chooseControllerKey()
	{
		$count = [];
		$count['bov1'] = 0;
		$count['bov2'] = 0;
		$count['bgv1'] = 0;
		$count['bgv2'] = 0;
		$count['bpn'] = 0;
		$count['bp0'] = 0;
		$count['bp1'] = 0;
		$count['bp2'] = 0;
		foreach ($this->requestData['system'] as $key => $value) {
			if ($value['system'] == 'bov') {
				if ($value['pump'] == '1') {
					$count['bov1']++;
				} else {
					$count['bov2']++;
				}
				if ($value['type'] == '1') {
					$count['bp' . $value['bp_pump']]++;
				} else {
					$count['bpn']++;
				}
			} else {
				if ($value['pump'] == '1') {
					$count['bgv1']++;
				} else {
					$count['bgv2']++;
				}
			}
		}
		$xCount = [];
		// foreach ($count as $key => $value) {
		// 	$xCount[] = $value;
		// }
		$xCount[] = $count['bov1'];
		$xCount[] = $count['bov2'];
		$xCount[] = $count['bgv1'];
		$xCount[] = $count['bgv2'];
		$xCount[] = $count['bpn'];
		$xCount[] = $count['bp0'];
		$xCount[] = $count['bp1'];
		$xCount[] = $count['bp2'];

		dump($xCount);

		$key = [];
		$key = array(
			'A260.A' => array('2', '0', '0', '0', '1', '0', '0', '0'),
			'A361.A' => array('0', '2', '0', '0', '1', '0', '0', '0'),
			'A361.B' => array('0', '2', '0', '0', '0', '1', '0', '0'),
			'A361.C' => array('0', '2', '0', '0', '0', '0', '1', '0'),
			'A361.D' => array('0', '2', '0', '0', '0', '0', '0', '1'),
			'A217.A' => array('0', '0', '1', '0', '1', '0', '0', '0'),
			'A230.A' => array('1', '0', '0', '0', '1', '0', '0', '0'),
			'A231.A' => array('0', '1', '0', '0', '1', '0', '0', '0'),
			'A231.B' => array('0', '1', '0', '0', '0', '1', '0', '0'),
			'A231.C' => array('0', '1', '0', '0', '0', '0', '1', '0'),
			'A266.A' => array('1', '0', '1', '0', '1', '0', '0', '0'),
			'A317.A' => array('0', '0', '1', '0', '1', '0', '0', '0'),

			'A368.A' => array('0', '1', '0', '1', '1', '0', '0', '0'),
			'A368.B' => array('0', '1', '0', '1', '0', '1', '0', '0'),
			'A368.C' => array('0', '1', '0', '1', '0', '0', '1', '0'),
			'A368.D' => array('0', '1', '0', '1', '0', '0', '0', '1'),
			'A368.E' => array('0', '1', '0', '0', '1', '0', '0', '0'),
			'A368.F' => array('0', '1', '0', '0', '0', '1', '0', '0'),
			'A368.G' => array('0', '1', '0', '0', '0', '0', '1', '0'),			
			'A368.K' => array('0', '1', '0', '0', '0', '0', '0', '1'),

			'A376.A' => array('2', '0', '1', '0', '1', '0', '0', '0'),
			'A390.A' => array('3', '0', '0', '0', '1', '0', '0', '0'),
		);

		$preset = [
			'X1' => ['0', '3', '0', '1', '0', '0', '3', '0'],
		];


		$i = 0;
		$res = [];
		$xKey = $key;
		$min = 100;
		foreach ($key as $kk => $vv) {
			$r = $this->calcKey($xKey, $xCount, 0, [], 0, $kk);
			// dump($r);
			if (isset($r['i']) && $r['i']  <= $min) {
				$min = $r['i'];
				$res = $r;
			}
		}
		dump($res);
		// return $result;
		return $res;
	}

	public function calcKey($key, $xCount, $i, $res, $xl, $k)
	{
		$xTemp = [];
		$temp = [];
		$xKey = $key;
		if ($k != false) {
			do {
				$xTemp = $temp;
				if (empty($temp)) {
					$temp = $this->minus($xCount, $key[$k]);
				} else {
					$temp = $this->minus($temp, $key[$k]);
				}
				if ($temp != $xCount && $xTemp != $temp) {
					$i++;
					$res[] = $k;
					// dump('F ' . $k);
				}
			} while ($temp != $xCount && $xTemp != $temp);
		}
		foreach ($key as $kk => $vv) {
			do {
				$xTemp = $temp;
				if (empty($temp)) {
					$temp = $this->minus($xCount, $vv);
				} else {
					$temp = $this->minus($temp, $vv);
				}
				if ($temp != $xCount && $xTemp != $temp) {
					$i++;
					$res[] = $kk;
					// dump('S ' . $kk);
				}
			} while ($temp != $xCount && $xTemp != $temp);
			unset($xKey[$kk]);

			if (empty(array_filter($temp)) || empty($key)) {
				return [
					'xKey' => $xKey,
					'xCount' => $xCount,
					'i' => $i,
					'res' => $res,
				];
			} else {
				$xl++;
				return  $this->calcKey($xKey, $temp, $i, $res, $xl, false);
				// $colQlect[] =  $this->calcKey($xKey, $temp, $i, $res, $xl, false);
			}
		}
		// return $collect;	
	}

	public function chooseControllerDanfoss()
	{
		//считаем количество контуров
		$count = ['bov1' => 0, 'bov2' => 0, 'bgv1' => 0, 'bgv2' => 0, 'bpn' => 0, 'bp0' => 0, 'bp1' => 0, 'bp2' => 0];
		foreach ($this->requestData['system'] as $value) {
			if ($value['system'] == 'bov') {
				if ($value['pump'] == '1') {
					$count['bov1']++;
				} else {
					$count['bov2']++;
				}
				if ($value['type'] == '1') {
					$count['bp' . $value['bp_pump']]++;
				} else {
					$count['bpn']++;
				}
			} else {
				if ($value['pump'] == '1') {
					$count['bgv1']++;
				} else {
					$count['bgv2']++;
				}
			}
		}
		$xCount = [];
		foreach ($count as $value) {
			$xCount[] = $value;
		}
		$result = [];
		if($this->requestData['hasRegulatorAnalog'] && $this->requestData['system'][0]['system'] == 'bov'){
			$result['controller'][] = ['id' => '38679', 'model' => 'TCM HE'];
		} elseif($this->requestData['systemCount'] == 1 && $this->requestData['hasRegulatorAnalog'] && $this->requestData['system'][0]['system'] != 'bov'){
			$result['controller'][] = ['id' => '40538',	'model' => 'TCM DHW'];
		} elseif($this->requestData['systemCount'] == 2 && $this->requestData['hasRegulatorAnalog']){
			$bov = 0;
			$bgv = 0;
			foreach ($this->requestData['system'] as $value) {
				if($value == 'bov') {
					$bov++;
				} else {
					$bgv++;
				}
			}
			$result['controller'][] = ['id' => '40538',	'model' => 'TCM DHW'];
		}

	}	
	
	
	public function chooseController()
	{
		if($this->requestData['controller'] == 'danfoss'){
			$this->chooseControllerDanfoss();
		}
		// $result[$this->requestData['controller']];
		$result = [];
		$result['segnetics']['controller'][] = [
			'id' => '24607',
			'model' => 'SMH4',
		];
		$result['danfoss']['controller'] = [];
		switch ($this->requestData['systemCount']) {
			default:
			case "1":
				if (!$this->requestData['hasFrequency']) {
					if ($this->requestData['system'][0]['system'] == 'bov') {
						if ($this->requestData['system'][0]['type'] == '2') {
							if ($this->requestData['hasRegulatorAnalog']) {
								//TCM HE
								$result['danfoss']['controller'][] =  [
									'id' => '38679',
									'model' => 'TCM HE',
								];
							} else {
								//ECL 210
								$result['danfoss']['controller'][] = [
									'id' => '2398',
									'model' => 'ECL 210',
								];
							}
						} else {
							if ($this->requestData['hasRegulatorAnalog']) {
								//TCM HE
								$result['danfoss']['controller'][] =  [
									'id' => '38679',
									'model' => 'TCM HE',
								];
							} else {
								//ECL 310
								$result['danfoss']['controller'][] =  [
									'id' => '2390',
									'model' => 'ECL 310',
								];
							}
						}
					} else {
						//TCM DHW
						$result['danfoss']['controller'][] = [
							'id' => '40538',
							'model' => 'TCM DHW',
						];
					}
				}
				break;
			case "2":
				if ($this->requestData['hasRegulatorAnalog']) {
					//TCM DHW
					$result['danfoss']['controller'][] = [
						'id' => '40538',
						'model' => 'TCM DHW',
					];
				} else {
					//ECL 310
					$result['danfoss']['controller'][] =  [
						'id' => '2390',
						'model' => 'ECL 310',
					];
				}
				break;
			case "3":
				if (!$this->requestData['hasRegulatorAnalog']) {
					//ECL 310 + ECL 210
					$result['danfoss']['controller'][] =  [
						'id' => '2390',
						'model' => 'ECL 310',
					];
					$result['danfoss']['controller'][] = [
						'id' => '2398',
						'model' => 'ECL 210',
					];
				}
				break;
			case "4":
				if (!$this->requestData['hasRegulatorAnalog']) {
					//2 ECL 310
					$result['danfoss']['controller'][] =  [
						'id' => '2390',
						'model' => 'ECL 310',
					];
					$result['danfoss']['controller'][] = [
						'id' => '2390',
						'model' => 'ECL 310',
					];
				}
				break;
		}
		$cidArray = [];
		foreach ($result as $key => $value) {
			foreach ($value['controller'] as $k => $v) {
				// dump( $v);
				$r =  Catalog::getCatalog(['id' => $v['id']]);
				// dump($r);
				$cidArray[] = $r[0]['id'];
				$result[$key]['controller'][$k]['catalog'] = $r;
				$result[$key]['controller'][$k]['cid'] = $r[0]['id'];
				$result[$key]['controller'][$k]['title'] = $r[0]['title'];
			}
		}

		// $xKey['']
		$minPrice = false;
		foreach ($result as $key => $value) {
			$total = 0;
			foreach ($value['controller'] as $k => $v) {
				$price = \Price::getPrice($v['cid']);
				$price_list = empty($price->price_list) ? 0 : $price->price_list;
				$total += $price_list;
				$result[$key]['controller'][$k]['price'] = $price_list;
			}
			$result[$key]['total'] = $total;
			if (($minPrice == false) || (($minPrice > $total) && ($total != 0))) {
				$minPrice = $total;
				$xKey = $key;
			}
		}
		$result['auto'] = $result[$xKey];
		// dd($xKey);
		$this->controller = $result[$this->requestData['controller']];
		return $result[$this->requestData['controller']];
	}


	public function structureSpecification($i, $catalog, $count)
	{
		global $db;
		if (!empty($catalog)) {
			$catalog = $catalog[0];
			// dd($catalog);
			$sku = $catalog['sku'];
			$unit =  get_unit('data', $catalog['unit'], 'sname');
			$name = $catalog['title'];
			$model = ' ';
			$manufacter = '';
			if (!empty($catalog['man'])) {
				$query = $db->query("SELECT * FROM JMY_catalog_manufacture WHERE id = '" . $catalog['man'] . "' LIMIT 1");
				if ($db->numRows($query) > 0) {
					$manData = $db->getRow($query);
					$manufacter = $manData['name'];
				}
			}
			$spec_array = $catalog['spec'];
			$weight	 = $catalog['weight'];
			if (!empty($spec_array['1'])) {
				$name = $spec_array['1'];
			}
			if (!empty($spec_array['2'])) {
				$model = str_replace("&lt;br&gt;", "<w:br/>", $spec_array['2']);
			}
			if (!empty($spec_array['3'])) {
				$manufacter = $spec_array['3'];
			}
			$weight_good = '';
			if (!empty($weight['1'])) {
				$weight_good = $weight['1'];
			}
			$aid = 0;
			$attache = $catalog['attach_id'];
			if (!empty($attache)) {
				$aid = $attache[0];
			}
			$order_data_spec_tm =  array(
				'tm_cid'        	=> $catalog['id'],
				'tm_aid'        	=> $aid,
				'tm_n'        		=> $i,
				'tm_title' 			=> $name,
				'tm_model' 			=> $model,
				'tm_sku' 			=> $sku,
				'tm_manufacter' 	=> $manufacter,
				'tm_unit' 			=> $unit,
				'tm_count' 			=> $count,
				'tm_weight' 		=> $weight_good,
			);
			return $order_data_spec_tm;
		} else {
			return false;
		}
	}


}
