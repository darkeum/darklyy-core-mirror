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

use Catalog_v2\Model\CatalogManufacture;

abstract class BlockCalculationLoader
{

	//данные
	protected $requestData = array();
	protected $blockData = array();
	protected $key = false;
	public $heatData = array();
	public $heatDataComplete = array();
	public $specArray = array();




	//заполнение данных
	public function setRequestData($request)
	{
		$this->requestData = $request;
	}

	//
	public function unsetRequestData()
	{
		unset($this->requestData);
	}

	//ключ блока
	public function setKey($k)
	{
		$this->key = $k;
	}

	//данные блока
	public function setBlockData($v)
	{
		$this->blockData = $v;
	}

	//данные блока
	public function setSpecItem($data)
	{
		$this->specArray[] = array();
	}


	//подготовка данных для подсчёта теплообменника
	public function prepareDataToHeatCalculation()
	{
		$this->heatData = array(

			'X' => '15',
			//'Q' => $this->blockData['q']['clearValue'],
			// 'T1' => (!empty($request['t_gre_temper_in']) ? $this->dotsReplace(floatval($this->commasReplace($request['t_gre_temper_in']))) : ''),
			// 'T2' => (!empty($request['t_gre_temper_out']) ? $this->dotsReplace(floatval($this->commasReplace($request['t_gre_temper_out']))) : ''),
			// 'T3' => (!empty($request['t_nagr_temper_in']) ? $this->dotsReplace(floatval($this->commasReplace($request['t_nagr_temper_in']))) : ''),
			// 'T4' => (!empty($request['t_nagr_temper_out']) ? $this->dotsReplace(floatval($this->commasReplace($request['t_nagr_temper_out']))) : ''),	
			// 'T12' => (!empty($request['t_t1']) ? $this->dotsReplace(floatval($this->commasReplace($request['t_t1']))) : ''),		
			// 'T22' => (!empty($request['t_t2']) ? $this->dotsReplace(floatval($this->commasReplace($request['t_t2']))) : ''),
			// 'X2' => (!empty($request['t_zapas_2']) ? $this->dotsReplace(floatval($this->commasReplace($request['t_zapas_2']))) : ''),		
			// 'R1' => '2',
			// 'R2' => '2',	
			// 'R12' => (!empty($request['t_gre_mvst_2']) ? $this->dotsReplace(floatval($this->commasReplace($request['t_gre_mvst_2']))) : ''),		
			// 'R22' => (!empty($request['t_nagr_mvst_2']) ? $this->dotsReplace(floatval($this->commasReplace($request['t_nagr_mvst_2']))) : ''),
			// 'qBalanceAuto' => (isset($request['t_nagr_auto']) ? intval($request['t_nagr_auto']) : '0'),
			// 'flan' => (isset($request['t_flan']) ? intval($request['t_flan']) : '0'),
			// 'allowChange' => (isset($request['t_optimizator']) ? intval($request['t_optimizator']) : '0'),
			// 'mono' => (isset($request['t_type']) ? ((intval($request['t_type']) == '1') ? '1' : '0') : '1'),
			// 'pto_2' => (isset($request['t_type']) ? ((intval($request['t_type']) == '2') ? '1' : '0') : '0'),
			// 'Q3' => (!empty($request['t_nagr_22']) ? $this->dotsReplace(floatval($this->commasReplace($request['t_nagr_22']))) : ''),
			// 'Gco_2' => (!empty($request['t_rashod']) ? $this->dotsReplace(floatval($this->commasReplace($request['t_rashod']))) : ''),
			// 'ptoTypeUse' => $request['t_prim'],
			// 'message' => (!empty($request['t_mess']) ? filter($request['t_mess']) : ''),
			'oid' => '507',
			'oid_name' => 'ООО «АТРИ-ЭНЕРГО»',
			'oid_inn' => '1840013705',
			'object_id' => '2419',
			'object_name' => 'Aquatherm 2021',
			'uid_name' => '',
			'uid_phone' => '',
			'uid_mail' => '',
		);
	}

	public function sortPrice($a, $b)
	{
		if ($a['price'] == $b['price']) return 0;
		return ($a['price'] > $b['price']) ? 1 : -1;
	}



	public function getHeatCalculation()
	{
		$this->prepareDataToHeatCalculation();
		$hc = new \Calculation\HeatCalculation();
		$hc->getFormRequestData(array());
		$hc->requestData = array_merge($hc->requestData, $this->heatData);
		// dump($hc->requestData);
		if ($this->heatData['ptoTypeUse'] == 'ГВС двухступенчатая') {
			$hc->ptoTypeUse = 1;
		}
		// dump('gggg');
		$hc->initClient();
		$hc->initAuth();
		// $hc->selectObject();

		$hc->createHeat();
		// dump('qaaa');	
		// $hc->createRequst();
		// dump('wefwef');	
		$resultHeat = $hc->createHeatSolution();
		// dump('eertergh');	
		// dump($resultHeat);

		$solutionID = '0';

		// dump($hc->solutionArray);

		//отсекаем буквенные варианты 
		$xSolution = [];
		foreach ($hc->solutionArray as $k => $v) {
			if ($v['solution'][5] == '-' || $v['solution'][5] == '-') {
				// $solutionID = $v['id'];
				// $this->heatDataComplete = $v;
				$xSolution[] = $v;
			}
			// dump($v['solution']);
		}
		if (!empty($xSolution)) {
			//сортируем массив по цене
			usort($xSolution, array($this, 'sortPrice'));
			$minPrice = $xSolution[0]['price'];
			$minSize = preg_replace('/[^0-9]/', '', $xSolution[0]['result'][0]['UsePTOSize']);
			$minSolution = $xSolution[0];
			$minKey = 0;
			//dump($minPrice.'-minprice');
			//dump($minSize.'-minSize');
			// dump($minSolution);
			foreach ($xSolution as $k => $v) {
				// dump('qqqqqq');	
				if ($k != 0) {
					// dump('wwwwww');
					$size = preg_replace('/[^0-9]/', '', $v['result'][0]['UsePTOSize']);
					if ($minSize == '14') {
						// dump('xxxx');
						if ($size == '19') {
							// dump($v['price']);
							if ($minPrice * 1.1 >= $v['price']) {
								$minPrice = $v['price'];
								$minSize = $size;
								$minSolution = $xSolution[$k];
								$minKey = 0;
							}
						} else {
							if ($size == '14') {
								if ($minPrice * 1.1 >= $v['price']) {
									$minPrice = $v['price'];
									$minSize = $size;
									$minSolution = $xSolution[$k];
									$minKey = 0;
								}
							}
						}
					}
				} else {
					if ($minSize == '19') {
						break;
					}
				}
			}
			$solutionID = $minSolution['id'];
			$this->heatDataComplete = $minSolution;
		} else {
			$solutionID = 0;
			$this->heatDataComplete = $hc->solutionArray[0];
		}
		dump($this->heatDataComplete['solution']);

		// dd($minSolution['solution']);		//cjhnbhetv
		//if
		// exit;
		// if(empty($this->heatDataComplete)){

		// }



		dump('ffff');
		// exit;
		//throw new \Exception("Configuration file not found.");
		//dump($this->heatDataComplete);
		//dump($hc);
		// $hc->setSolutionID($solutionID);
		dump($hc->heatID);
		dump($solutionID);
		// dump($hc->getSolutionIDByHeat($hc->heatID, $solutionID));
		$hc->getSolutionData($solutionID);
		// $hc->initClient();
		// $hc->initAuth();	
		$hc->saveSolution();
		$hc->getSolution();
		$file = $hc->getSolutionAttache();
		$hc->createRequstAfter($solutionID);

		// dump($hc->getSolutionData($solutionID));
		// dump('fef');
		// $hc->saveSolution();
		// dump('awd');	
		// $hc->getSolution();	
		// dump('qqqq');
		// $hc->setComment();
		// $hc->getSolutionAttache();	
		// dump('trjj');
		// dd($hc->cidArray); 
		return $hc->cidArray;
	}

	//замена запятых на точку
	private function commasReplace($text)
	{
		return str_replace(',', '.', $text);
	}

	//подготовка числа к расчету
	public function prepareNumeric($numeric)
	{
		return floatval($this->commasReplace($numeric));
	}

	public function getHeatCalculationLocalTemp()
	{

		$this->heatDataComplete = array(
			'id' => '0',
			'solution' => 'HH-19-16/2-37-TKTM42 (Ду 65 мм)',
			'price' => '2823.9105224609',
			'deliveryWeeks' => '1',
			'EffectiveSurface' => '7.77',
			'Weight' => '260',
			'Height' => '960',
			'Width' => '395',
			'Length' => '630',
			'detailed' => array(
				'DisplaySolutionTemplate' => 'SolutionGVS1',
				'lbl_Q' => '0,6000',
				'lbl_dumplQMeasure' => 'Гкал/ч',
				'lbl_X' => '15,4',
				'lbl_FZ' => '35,56',
				'lbl_TransfetFactor' => '3703 / 4272',
				'lbl_TransfertFactorMeasure' => 'ккал/(м2 ч С)',
				'lbl_T' => '150',
				'lbl_P' => '16',
				'lbl_EnvironmentMediaHot' => 'Вода',
				'lbl_EnvironmentMediaCold' => 'Вода',
				'lbl_G1' => '7,91',
				'lbl_G2' => '1,93',
				'lbl_GMEasure' => 'т/ч',
				'lbl_T1_3' => '150,0 -> 75,0',
				'lbl_T2_4' => '95,0 <- 70,0',
				'lbl_R1_R1S' => '0,26 / 0,03',
				'lbl_R2_R2S' => '1,89 / 0,27',
				'lbl_PressureGage' => 'м.вод.ст.',
				'lbl_VelocityHot' => '0,7 / 0,29',
				'lbl_VelocityCold' => '2,06 / 0,76',
				'lbl_VelocityMeasure' => 'м/с',
				'lbl_NetWeight' => '260',
				'lbl_GasketMaterial' => 'EPDM',
				'lbl_CommonInfo1_2' => 'AISI316 0,5 мм',
				'lbl_ConnectionHot' => 'Фланцы (Черные) Ду 65; ',
				'lbl_ConnectionCold' => 'Фланцы (Черные) Ду 65; ',
				'lbl_PortSurfaceH' => '---',
				'lbl_PortSurfaceC' => '---',
				'lbl_MaxPlateCount' => '55',
				'lbl_RO1' => '949,13',
				'lbl_RO2' => '970,54',
				'lbl_ROMeasure' => 'кг/м3',
				'lbl_C1' => '4234,47',
				'lbl_C2' => '4198,08',
				'lbl_CMeasure' => 'Дж/(кг*К)',
				'lbl_LA1' => '0,68',
				'lbl_LA2' => '0,67',
				'lbl_LAMeasure' => 'Вт/(м*К)',
				'lbl_AverageM1' => '2,463559 e-01',
				'lbl_AverageM2' => '3,486729 e-01',
				'lbl_AverageMMeasure' => 'Па*с',
			),
		);
		return  array('0' => '115561');
	}

	public function getHeatCalculationGVSLocalTemp()
	{

		$this->heatDataComplete = array(
			'id' => '0',
			'solution' => '1-ая ст.: HH-19-16/1-32-TKTL16 (Ду 65 мм) 2-ая ст.: HH-19-16/1-35-TKTL76 (Ду 65 мм)',
			'price' => '4575.122281799',
			'deliveryWeeks' => '1',
			'EffectiveSurface' => '7,326/6,66',
			'Weight' => '300',
			'Height' => '960',
			'Width' => '395',
			'Length' => '730',
			'detailed' => array(
				'DisplaySolutionTemplate' => 'SolutionGVS2',
				'lbl1_G1' => '15,88',
				'lbl1_G2' => '6,64',
				'lbl2_G1' => '7,97',
				'lbl2_G2' => '6,64',
				'lGmeasureId' => 'т/ч',
				'lbl1_T1' => '45,0',
				'lbl1_T3' => '5,0',
				'lbl2_T1' => '70,0',
				'lbl2_T3' => '38,6',
				'lbl1_T2' => '31,0',
				'lbl1_T4' => '38,6',
				'lbl2_T2' => '48,0',
				'lbl2_T4' => '65,0',
				'lbl1_R1' => '1,26',
				'lbl1_R2' => '0,22',
				'lbl2_R1' => '0,70',
				'lbl2_R2' => '0,49',
				'lRmeasureId' => 'м.вод.ст.',
				'lbl1_VelocityInPortHot' => '1,34',
				'lbl1_VelocityInPortCold' => '0,56',
				'lbl2_VelocityInPortHot' => '0,68',
				'lbl2_VelocityInPortCold' => '0,56',
				'lbl1_Q' => '0,224000 (56%)',
				'lbl2_Q' => '0,176000 (44%)',
				'lQMeasureValue' => 'Гкал/ч',
				'lbl1_X' => '16,93',
				'lbl2_X' => '15,71',
				'lbl1_FZ' => '59,52',
				'lbl2_FZ' => '38,95',
				'lbl1_TransferFactor' => '2406/2814',
				'lbl2_TransferFactor' => '3447/3988',
				'lbl1_EffectiveSurface' => '6,66',
				'lbl2_EffectiveSurface' => '7,33',
				'lbl1_PlateComponent' => '32-TKTL16',
				'lbl2_PlateComponent' => '35-TKTL76',
				'lbl1_AssemblingHotChanels' => '1 x 15 + 0 x 0',
				'lbl1_AssemblingColdChanels' => '1 x 16 + 0 x 0',
				'lbl2_AssemblingHotChanels' => '1 x 17 + 0 x 0',
				'lbl2_AssemblingColdChanels' => '1 x 17 + 0 x 0',
				'lbl1_PlaneMaterial' => '0,5 мм AISI316',
				'lbl1_GasketMaterialId' => 'EPDM',
				'lbl1_Pressure' => '16/23',
				'lbl1_Temperature' => '150',
				'lbl1_ConnectionHot' => 'Фланцы (Черные) Ду 65; ',
				'lbl2_ConnectionHot' => 'Фланцы (Черные) Ду 65; ',
				'lbl1_ConnectionCold' => 'Фланцы (Черные) Ду 65; ',
				'lbl2_ConnectionCold' => 'Фланцы (Черные) Ду 65; ',
				'lbl2_PortSurfaceHot' => '---',
				'lbl1_PortSurfaceHot' => '---',
				'lbl2_PortSurfaceCold' => '---',
				'lbl1_PortSurfaceCold' => '---',
				'solutionTypeCaption' => 'моноблок',
			)
		);
		return  array('0' => '115700');
	}

	public function structureSpecification($i, $catalog, $count)
	{
		global $db;
		if (!empty($catalog)) {
			$catalog = $catalog[0];
			$sku = $catalog['sku'];
			$unit =  get_unit('data', $catalog['unit'], 'sname');
			$name = $catalog['title'];
			$model = ' ';
			$manufacter = '';
			if (!empty($catalog['man'])) {
				$manufacture_item = CatalogManufacture::find($catalog['man']);
				if (!empty($manufacture_item)) {
					$manufacter = $manufacture_item->name;
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
			// dd($i);
			return false;
		}
	}


	//подбор регулирующего клапана
	public function calulationValve($g, $parametr = array())
	{
		$dPcl = 3;
		if ($this->requestData['mainData']['p1']['clearValue'] - $this->requestData['mainData']['p2']['clearValue'] < 7) {
			$dPcl = 1.5;
		}
		$kvs = $g / sqrt(0.1 * $dPcl);
		if (empty($parametr)) {
			$parametr = array();
			$parametr['man'] = '13';
			$parametr['category'] = '562';
			$parametr['attribute'] = array(
				'10' => 'Фланцевое',
				'28' => '200',
				'40' => '25',
				'44' => 'Чугун',
				'112' => '2 ходовой',
				// '272' => 'Регулятор температуры «с электроприводом»',
				// '282' => 'AMV(E) 15(ES), 25(SU, SD), 35, 85, 86, AMV 323, 423, 523, AME 655, 658SU(SD)',
				'283' => 'Седельный',
			);
		}
		dump($parametr);
		$klapanList = \Catalog_v2\Model\Catalog::getCatalog($parametr);
		// dd($klapanList );
		$kTemp = 10000;
		$kTempId = '0';
		$kTempIdKey = '0';
		if(!empty($klapanList)){
			
		}
		foreach ($klapanList as $k => $v) {
			$atrr = $v['attribute'];
			$klapanKvs = $this->commasReplace($atrr['51']);
			if ($kTemp >= ($klapanKvs - $kvs) && ($klapanKvs - $kvs) >= 0) {
				$kTemp = $klapanKvs - $kvs;
				$kTempId = $v['id'];
				$kTempIdKey = $k;
			}
		}
		$kCatalog = array();
		$kCatalog[0] = $klapanList[$kTempIdKey];
		return $kCatalog;
	}

	public function getManometrByP($p, $man)
	{
		if ($p > 80) {
			$diapozon = '0-1.6';
		} else {
			$diapozon = '0-1.0';
		}
		$parametr = array();
		$parametr['man'] = $man;
		$parametr['category'] = '297';
		$parametr['attribute'] = array(
			'58' => 'Стандартное исполнение',
			'154' =>  $diapozon,
			'156' =>  'М20×1.5'
		);
		if ($man == '18') {
			$parametr['attribute']['246'] = 'Модель 2 (М2)';
		}
		//dump($parametr);	
		return  \Catalog_v2\Model\Catalog::getCatalog($parametr);
	}

	public function getTermometrByTAndDn($t, $dn, $man)
	{
		$td = 'От 0 до 120';
		if ($t > 110) {
			$td = 'От 0 до 160';
		}
		$pogr = '64';
		if ($dn < 50) {
			$pogr = '46';
		}
		if ($dn > 100) {
			$pogr = '100';
		}
		$parametr = array();
		$parametr['man'] = $man;
		$parametr['category'] = '298';
		$parametr['attribute'] = array(
			'62' =>  'Механический',
			'63' =>  '2.5',
			'89' =>  $td,
			'152' =>  'Осевое',
			'158' =>  $pogr
		);
		return  \Catalog_v2\Model\Catalog::getCatalog($parametr);
	}

	public function getCoefficientByBOVType($typeBlock)
	{
		switch ($typeBlock) {
			default:
			case "1":
				$coefficient = 11.7;
				break;
			case "2":
				$coefficient = 8.6;
				break;
			case "3":
				$coefficient = 30;
				break;
		}
		return $coefficient;
	}
}
