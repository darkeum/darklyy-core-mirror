<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Heat Calculation class
*/

namespace Calculation;

use Pto\Model\Pto;
use Boot\Support\Numb;
use GuzzleHttp\Client;
use \Boot\Models\Attach;
use Illuminate\Support\Str;
use \Catalog_v2\Model\Catalog;
use \Boot\Support\Facades\Auth;
use \Price\Models\CatalogPrice;
use \Calculation\Model\HeatSolution;
use Price\UI\CLI\CatalogPriceDaemon;
use \Price\Models\CatalogPriceDetail;
use \GuzzleHttp\Exception\RequestException;
use \Calculation\Library\HeatLibrary as HeatLibrary;
use \Calculation\Utilities\UtilitiesTool as UtilitiesTool;

class HeatCalculation
{

	//данные
	public $requestData = array();
	public $requestID = null;
	public $heatID = null;
	public $solutionArray = array();
	public $solutionData = array();
	public $solutionID = null;
	public $objectName = null;


	public $oppID = null;
	public $calcID = array();
	public $rNumb = array();
	public $ptoID = array();
	public $ptoUrl = array();

	public $cidArray = array();
	//данные авторизации
	// public $username = 'vaneka97@ya.ru';
	// public $password = '73652459';
	public $username = 'lay@atri-energo.ru';
	public $password = 'j7Auwx';
	//данные клиента
	public $client;
	public $cookieSession;
	public $token;
	public $ptoTypeUse = 0;
	public $logClient = array();

	//конструктор класса
	function __construct()
	{
		$this->UtilitiesTool = new UtilitiesTool();
		$this->HeatLibrary = new HeatLibrary();
	}


	public function getHeatOrder($request, $type = 'main')
	{
		if ($type == 'main') {
			if (Auth::isUser()) {
				$uid = Auth::user();
			} else {
				$uid = null;
			}
			$this->getFormRequestData($request);
			if ($this->requestData['ptoTypeUse'] == 'ГВС двухступенчатая') {
				$this->ptoTypeUse = 1;
			}
			$result = array();
			if (!empty($uid) && $request['type'] != 'hand') {
				$this->initClient();
				if ($this->initAuth()) {
					$this->createRequst();
					$result = $this->createHeatSolution();
				} else {
					$result['error'][] = 'Ошибка авторизации!';
				}
			} else {
				$this->createRequstHand();
				$this->sendNotif();
				$result = array();
				$result['type'] = 'success';
				$result['typeRequst'] = 'hand';
				$result['requestID'] = $this->requestID;
			}
		} elseif ($type == 'app') {

			$this->getFormRequestData($request, '2');
			if ($this->requestData['ptoTypeUse'] == 'ГВС двухступенчатая') {
				$this->ptoTypeUse = 1;
			}
			$result = array();
			$this->initClient();
			if ($this->initAuth()) {
				$this->createHeat();
				$solution = array();
				$resultHeat = $this->createHeatSolution();
				if ($resultHeat['type'] == 'success') {
					$heat = \Calculation\Model\Heat::find($this->heatID);
					$solutionQuery = \Calculation\Model\HeatSolution::where(array('heat_id' => $this->heatID))->get()->toArray();
					if ($heat->heatData['ptoTypeUse'] == 'ГВС двухступенчатая') {
						foreach ($solutionQuery as $k => $v) {
							$solution[$v['solution_id']]['solutions'][] = $v;
						}
						foreach ($solution as $k => $v) {
							$title = '';
							$price = 0;
							$deliveryWeeks = 0;
							foreach ($v['solutions'] as $kk => $vv) {
								$title .= $vv['solution'] . ' ';
								$price += $vv['price'];
								$deliveryWeeks = $vv['deliveryWeeks'];
								$detailed = $vv['detailed'];
								$solution[$k]['title'][$kk] = $vv['solution'];
							}
							$solution[$k]['solution'] = $title;
							$solution[$k]['price'] = $price * 1.2;
							$solution[$k]['detailed'] = $detailed;
							$solution[$k]['deliveryWeeks'] = $deliveryWeeks;
						}
					} else {
						$solution = $solutionQuery;
					}
					// $solution = $solutionQuery;

					if (!empty($solution)) {

						$result['entities'] = $solution;
						$result['type'] = 'success';
					} else {
						$result['type'] = 'empty';
					}
				} else {
					$result = $resultHeat;
				}
			} else {
				$result['error'][] = 'Ошибка авторизации!';
			}
		} else {
			$result['error'][] = 'Произошла ошибка, свяжитесь с администрацией!';
		}

		return $result;
	}




	//стандартизируем данные
	public function getFormRequestData($request, $type = 'auto')
	{
		if ($type == 'auto') {
			$correctType = isset($request['typeData']) ? $request['typeData'] : '1';
		} else {
			$correctType = $type;
		}
		switch ($correctType) {
			default:
			case "1":
				$prepareRequest = array(
					//данные расчета
					'ptoTypeUse' 				=> $this->HeatLibrary->prepareAim(isset($request['t_prim']) ? $request['t_prim'] : 'Отопление'),
					'environmentCold' 			=> $this->HeatLibrary->prepareEnvironment(isset($request['t_typesreda']) ? $request['t_typesreda'] : 'Вода'),
					'environmentHot' 			=> $this->HeatLibrary->prepareEnvironment(isset($request['t_typesr']) ? $request['t_typesr'] : 'Вода'),
					'flowDirection' 			=> $this->HeatLibrary->prepareFlowDirection('Противоток'),
					'gMeasure' 					=> $this->HeatLibrary->prepareGMeasure('т/ч'),
					'GVS1_Cold_flangeType' 		=> $this->HeatLibrary->prepareFlange(isset($request['GVS1_Cold_flangeType']) ? $request['GVS1_Cold_flangeType'] : 'Черные'),
					'GVS1_Hot_flangeType' 		=> $this->HeatLibrary->prepareFlange(isset($request['GVS1_Hot_flangeType']) ? $request['GVS1_Hot_flangeType'] : 'Черные'),
					'gasketMaterial' 			=> $this->HeatLibrary->prepareGasketMaterial(isset($request['gasketMaterial']) ? $request['gasketMaterial'] : 'EPDM'),
					'goal' 						=> $this->HeatLibrary->prepareAim(isset($request['t_prim']) ? $request['t_prim'] : 'Отопление'),
					'GVS1_Cold_plugType' 		=> $this->HeatLibrary->prepareFlange(isset($request['GVS1_Cold_plugType']) ? $request['GVS1_Cold_plugType'] : 'Черные'),
					'GVS1_Hot_plugType' 		=> $this->HeatLibrary->prepareFlange(isset($request['GVS1_Hot_plugType']) ? $request['GVS1_Hot_plugType'] : 'Черные'),
					'plateMaterial_standart' 	=> $this->HeatLibrary->preparePlateMaterial(isset($request['plateMaterial_standart']) ? $request['plateMaterial_standart'] : 'AISI316'),
					'pressureGage' 				=> $this->HeatLibrary->prepareRMeasure('м.вод.ст.'),
					'plugAssortmentCold' 		=> $this->HeatLibrary->prepareFlange(isset($request['plugAssortmentHot']) ? $request['plugAssortmentHot'] : 'Черные'),
					'plugAssortmentHot' 		=> $this->HeatLibrary->prepareFlange(isset($request['plugAssortmentHot']) ? $request['plugAssortmentHot'] : 'Черные'),
					'ptoType' 					=> 'Разборные ПТО',
					'QMeasure' 					=> $this->HeatLibrary->prepareQMeasure(isset($request['t_edizm']) ? $request['t_edizm'] : 'Гкал/ч'),
					'QMeasure2' 				=> $this->HeatLibrary->prepareQMeasure(isset($request['t_edizm_2']) ? $request['t_edizm_2'] : 'Гкал/ч'),
					'GcoMeasure' 				=> $this->HeatLibrary->prepareGMeasure(isset($request['t_edizm_3']) ? $request['t_edizm_3'] : 'т/ч'),
					'P' 						=> $this->HeatLibrary->preparePressure(isset($request['t_raschdavl']) ? $request['t_raschdavl'] : '16 атм'),
					'T' 						=> $this->UtilitiesTool->prepareNumericArray($request, 'T', true, '150'),
					'thickness_standart' 		=> $this->HeatLibrary->preparePlateThickIndex(isset($request['thickness_standart']) ? $request['thickness_standart'] : 'Авто'),
					'G1' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_gre_mass', true),
					'G2' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_nagr_mass', true),
					'Q' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_nagr', true),
					'Q1' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_nagr_1', true),
					'Q2' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_nagr_2', true),
					'R1' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_gre_mvst', true),
					'R12' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_gre_mvst_2', true),
					'R2' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_nagr_mvst', true),
					'R22' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_nagr_mvst_2', true),
					'T1' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_gre_temper_in', true),
					'T2' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_gre_temper_out', true),
					'T3' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_nagr_temper_in', true),
					'T4' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_nagr_temper_out', true),
					'T12' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('T12', 'T11', 't_t1'), true),
					'T22' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('T22', 'T21', 't_t2'), true),
					'X' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_zapas', true),
					'X2' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_zapas_2', true),
					'Q3' 						=> $this->UtilitiesTool->prepareNumericArray($request, 't_nagr_22', true),
					'Gco_2' 					=> $this->UtilitiesTool->prepareNumericArray($request, 't_rashod', true),
					'Gc' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('Gc'), true),
					'Tc' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('Tc'), true),
					'PercentHot' 				=> $this->UtilitiesTool->prepareNumericArray($request, array('percentHot'), true, 0),
					'PercentCold' 				=> $this->UtilitiesTool->prepareNumericArray($request, array('percentCold'), true, 0),
					'Tco' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('Tco'), true),
					'qBalanceAuto' 				=> (isset($request['t_nagr_auto']) ? intval($request['t_nagr_auto']) : '0'),
					'flan' 						=> (isset($request['t_flan']) ? (($request['t_flan'] == true) ? '1' : '0') : '0'),
					'allowChange' 				=> (isset($request['t_optimizator']) ? (($request['t_optimizator'] == true) ? '1' : '0') : '0'),
					'mono' 						=> (isset($request['t_type']) ? ((intval($request['t_type']) == '1') ? '1' : '0') : '0'),
					'pto_2' 					=> (isset($request['t_type']) ? ((intval($request['t_type']) == '2') ? '1' : '0') : '0'),
					'IncludeFlanges' 			=> (isset($request['addFlangesCost']) ? (($request['addFlangesCost'] === true || $request['addFlangesCost'] === 'true') ? '1' : '0') : (isset($request['t_flan']) ? (($request['t_flan'] === true || $request['t_flan'] === 'true') ? '1' : '0') : '0')),
				);
				break;
			case "2":

				$prepareRequest = array(
					//данные расчета
					'ptoTypeUse' 				=> $this->HeatLibrary->prepareAim(isset($request['ptoTypeUse']) ? $request['ptoTypeUse'] : 'Отопление'),
					'environmentCold' 			=> $this->HeatLibrary->prepareEnvironment(isset($request['environmentCold']) ? $request['environmentCold'] : 'Вода'),
					'environmentHot' 			=> $this->HeatLibrary->prepareEnvironment(isset($request['environmentHot']) ? $request['environmentHot'] : 'Вода'),
					'flowDirection' 			=> $this->HeatLibrary->prepareFlowDirection('Противоток'),
					'gMeasure' 					=> $this->HeatLibrary->prepareGMeasure(($request['gMeasure'] ?? $request['t_edizm_3'] ?? 'т/ч')),
					'GVS1_Cold_flangeType' 		=> $this->HeatLibrary->prepareFlange(isset($request['GVS1_Cold_flangeType']) ? $request['GVS1_Cold_flangeType'] : 'Черные'),
					'GVS1_Hot_flangeType' 		=> $this->HeatLibrary->prepareFlange(isset($request['GVS1_Hot_flangeType']) ? $request['GVS1_Hot_flangeType'] : 'Черные'),
					'gasketMaterial' 			=> $this->HeatLibrary->prepareGasketMaterial(isset($request['gasketMaterial']) ? $request['gasketMaterial'] : 'EPDM'),
					'goal' 						=> $this->HeatLibrary->prepareAim(isset($request['ptoTypeUse']) ? $request['ptoTypeUse'] : 'Отопление'),
					'GVS1_Cold_plugType' 		=> $this->HeatLibrary->prepareFlange(isset($request['GVS1_Cold_plugType']) ? $request['GVS1_Cold_plugType'] : 'Черные'),
					'GVS1_Hot_plugType' 		=> $this->HeatLibrary->prepareFlange(isset($request['GVS1_Hot_plugType']) ? $request['GVS1_Hot_plugType'] : 'Черные'),
					'plateMaterial_standart' 	=> $this->HeatLibrary->preparePlateMaterial(isset($request['plateMaterial_standart']) ? $request['plateMaterial_standart'] : 'AISI316'),
					'pressureGage' 				=> $this->HeatLibrary->prepareRMeasure($request['pressureGage'] ?? 'м.вод.ст.'),
					'plugAssortmentCold' 		=> $this->HeatLibrary->prepareFlange(isset($request['plugAssortmentHot']) ? $request['plugAssortmentHot'] : 'Черные'),
					'plugAssortmentHot' 		=> $this->HeatLibrary->prepareFlange(isset($request['plugAssortmentHot']) ? $request['plugAssortmentHot'] : 'Черные'),
					'ptoType' 					=> 'Разборные ПТО',
					'QMeasure' 					=> $this->HeatLibrary->prepareQMeasure($request['QMeasure'] ?? $request['t_edizm'] ?? 'Гкал/ч'),
					'QMeasure2' 				=> $this->HeatLibrary->prepareQMeasure($request['QMeasure'] ?? $request['t_edizm_2'] ?? 'Гкал/ч'),
					'GcoMeasure' 				=> $this->HeatLibrary->prepareGMeasure($request['GcoMeasure'] ?? $request['t_edizm_3'] ?? 'т/ч'),
					'P' 						=> $this->HeatLibrary->preparePressure(isset($request['P']) ? $request['P'] : '16 атм'),
					'T' 						=> $this->UtilitiesTool->prepareNumericArray($request, 'T', true, '150'),
					'thickness_standart' 		=> $this->HeatLibrary->preparePlateThickIndex(isset($request['thickness_standart']) ? $request['thickness_standart'] : 'Авто'),
					'G1' 						=> $this->UtilitiesTool->prepareNumericArray($request, 'G1', true),
					'G2' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('G2', 'Ggvs'), true),
					'Q' 						=> $this->UtilitiesTool->prepareNumericArray($request, 'Q', true),
					'Q_1' 						=> $this->UtilitiesTool->prepareNumericArray($request, 'Q_1', true),
					'Q_2' 						=> $this->UtilitiesTool->prepareNumericArray($request, 'Q_2', true),
					'R1' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('R1', 'step1Rh', 't_gre_mvst'), true),
					'R12' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('R12', 'step2Rh', 't_gre_mvst_2'), true),
					'R2' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('R2', 'step1Rc', 't_nagr_mvst'), true),
					'R22' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('R22', 'step2Rc', 't_nagr_mvst_2'), true),
					'T1' 						=> $this->UtilitiesTool->prepareNumericArray($request, 'T1', true),
					'T2' 						=> $this->UtilitiesTool->prepareNumericArray($request, 'T2', true),
					'T3' 						=> $this->UtilitiesTool->prepareNumericArray($request, 'T3', true),
					'T4' 						=> $this->UtilitiesTool->prepareNumericArray($request, 'T4', true),
					'T12' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('T12', 'T11', 't_t1'), true),
					'T22' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('T22', 'T21', 't_t2'), true),
					'X' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('X', 'step1X'), true),
					'X2' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('X2', 'step2X'), true),
					'Q3' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('Q3', 'Q1', 't_nagr_22'), true),
					'Gco_2' 					=> $this->UtilitiesTool->prepareNumericArray($request, array('Gco_2', 'Gco', 't_rashod'), true),
					'Gc' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('Gc'), true),
					'Tc' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('Tc'), true),
					'Tco' 						=> $this->UtilitiesTool->prepareNumericArray($request, array('Tco'), true),
					'PercentHot' 				=> $this->UtilitiesTool->prepareNumericArray($request, array('percentHot'), true, 0),
					'PercentCold' 				=> $this->UtilitiesTool->prepareNumericArray($request, array('percentCold'), true, 0),
					// 'qBalanceAuto' 				=> (isset($request['t_nagr_auto']) ? (($request['t_nagr_auto'] == true) ? '1' : '0') : '1'),
					'qBalanceAuto' 				=> (isset($request['t_nagr_auto']) ? (($request['t_nagr_auto'] === true || $request['t_nagr_auto'] === 'true') ? '1' : '0') : '1'),
					// 'flan' 						=> (isset($request['t_flan']) ? intval($request['t_flan']) : '0'),
					'flan' 						=> (isset($request['t_flan']) ? (($request['t_flan'] === true || $request['t_flan'] === 'true') ? '1' : '0') : '0'),
					// 'allowChange' 				=> $request['t_optimizator'] ?? $request['allowChange'] ?? '0',
					// addFlangesCost
					'allowChange' 				=> (isset($request['t_optimizator']) ? (($request['t_optimizator'] === true || $request['t_optimizator'] === 'true') ? '1' : '0') : '0'),
					'IncludeFlanges' 			=> (isset($request['addFlangesCost']) ? (($request['addFlangesCost'] === true || $request['addFlangesCost'] === 'true') ? '1' : '0') : (isset($request['t_flan']) ? (($request['t_flan'] === true || $request['t_flan'] === 'true') ? '1' : '0') : '0')),
					'mono' 						=> (isset($request['t_type']) ? ((intval($request['t_type']) == '1') ? '1' : '0') : '0'),
					'pto_2' 					=> (isset($request['t_type']) ? ((intval($request['t_type']) == '2') ? '1' : '0') : '0')
				);
				break;
		}
		// var_dump($request['ptoTypeUse']);
		// exit;


		//подставляем объект
		$object_name = (!empty($request['object_name']) ? $request['object_name'] : '');
		// $object_id = (!empty($request['object_id']) ? $request['object_id'] : '');
		// $object_id = ((!empty($request['object_id']) && $request['object_id'] != 'undefined') ? $request['object_id'] : '');
		$object_id = ((!empty($request['object']) && $request['object'] != 'undefined') ? $request['object'] : '');
		if (!empty($object_id)) {
			$object = \Object\Model\ObjectModel::find($object_id);
			if (!empty($object)) {
				$object_name = $object->name;
				$object_id = $object->id;
			}
		} else {
			if (Auth::isUser()) {
				$uid = Auth::id();
				$userObject = \DB::table("users_object as uo")
					->leftJoin("object as o", "uo.object", "=", "o.id")
					->select("o.id", "o.name", "o.address", "o.coords", "uo.choose")
					->where("uo.uid", "=", $uid)->get();
				if (!empty($userObject)) {
					foreach ($userObject as $key => $value) {
						$object_name = $value->name;
						$object_id = $value->id;
						if ($value->choose) {
							break;
						}
					}
				}
			}
		}

		//подставляем организацию
		$oid_id = ((!empty($request['t_oid']) && $request['t_oid'] != 'undefined') ? $request['t_oid'] : '');
		$oid_name = (!empty($request['org_name']) ? $request['org_name'] : '');
		$oid_inn = (!empty($request['org_inn']) ? $request['org_inn'] : '');
		if (!empty($request['t_oid'])) {
			$oid = \Organizations\Model\Organizations::find($request['t_oid']);
			if (!empty($oid)) {
				$oid_name = $oid->name;
				$oid_inn = $oid->inn;
				$oid_id = $oid->id;
			}
		} else {
			if (Auth::isUser()) {
				$uid = Auth::id();
				$userOid = \DB::table("users_org as uo")
					->leftJoin("organizations as o", "uo.oid", "=", "o.id")
					->select("o.id", "o.name", "o.inn", "uo.choose")
					->where("uo.uid", "=", $uid)->get();
				if (!empty($userOid)) {
					foreach ($userOid as $key => $value) {
						$oid_name = $value->name;
						$oid_inn = $value->inn;
						$oid_id = $value->id;
						if ($value->choose) {
							break;
						}
					}
				}
			}
		}


		$prepareRequestExt = array(
			//данные организации
			'oid' 						=> $oid_id,
			'oid_name' 					=> $oid_name,
			'oid_inn' 					=> $oid_inn,
			//данные объекта
			'object_id' 				=> $object_id,
			'object_name' 				=> $object_name,
			//данные пользователя
			'uid_name' 					=> (!empty($request['name']) ? $request['name'] : ''),
			'uid_phone' 				=> (!empty($request['phone']) ? $request['phone'] : ''),
			'uid_mail' 					=> (!empty($request['email']) ? $request['email'] : ''),
			//комментарий и файл
			'message'				 	=> (!empty($request['message']) ? $request['message'] : ''),
			'files' 					=> (!empty($request['t_file']) ? $request['t_file'] : ''),
		);
		$this->requestData = array_merge($prepareRequest, $prepareRequestExt);
		// var_dump($this->requestData);
		// exit;

	}

	public function getRequsetDataFromArray($request)
	{
		$this->requestData = $request;
	}

	//инициализация клиента
	public function initClient()
	{
		$this->client = new Client([
			'base_uri' => '',
			'verify'  => false,
			'allow_redirects' => true,
			'cookies' => true,
			'headers' => [
				'User-Agent'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36',
				'Accept'       => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Upgrade-Insecure-Requests' => '1',
				'Origin' => 'https://ridan.ru/selection-pto',
			]
		]);
	}

	//получение токена
	private function getToken($data)
	{
		$token = stristr($data, '<meta name="csrf-token" content="');
		$token = str_replace('<meta name="csrf-token" content="', "", $token);
		$token = stristr($token, '">', true);
		return $token;
	}

	//получение токена со страницы
	private function getTokenPage($page = 'https://ridan.ru/configurator-pto')
	{
		$getFormPage = $this->client->request('GET', $page, [
			'headers' => [
				'Cookie' => $this->cookieSession,
			],
		]);
		$bodyFormPage = $getFormPage->getBody()->getContents();
		$token = $this->getToken($bodyFormPage);
		return $token;
	}

	//авторизация
	public function initAuth()
	{
		//Получаем токен для авторизации
		$token = $this->getTokenPage('https://ridan.ru/');
		//Проходим авторизацию
		$post_data = array(
			'email' 	=> $this->username,
			'password' 	=> $this->password,
			'_token' 	=> $token
		);
		$getAuthPage  = $this->client->request('POST', 'https://ridan.ru/login', [
			'headers' => array(
				'Cookie' 					=> $this->cookieSession,
				'User-Agent'   				=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36',
				'Accept'       				=> 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
				'Content-Type' 				=> 'application/x-www-form-urlencoded; charset=UTF-8',
				'Upgrade-Insecure-Requests' => '1',
				'X-CSRF-TOKEN' 				=> $token,
			),
			'form_params' 	=> $post_data,
			'debug' 		=> false
		], $post_data);
		$bodyAuthPage = $getAuthPage->getBody()->getContents();
		if (Str::isJson($bodyAuthPage)) {
			return true;
		}
		return false;
	}

	public function getObjects()
	{
		if (!empty($this->requestData['object_id'])) {
			$objectData = get_object_data($this->requestData['object_id']);
			if (!empty($objectData['name'])) {
				if (isset($objectData['address']) && !empty($objectData['address'])) {
					$orderName = $objectData['name'] . ' (' . $objectData['address'] . ')';
				} else {
					$orderName = $objectData['name'];
				}
			} else {
				$orderName = 'Новый объект для расчета';
			}
		} else {
			$orderName = 'Новый объект для расчета';
		}
		$this->objectName = $orderName;
		//получаем токен
		$getFormPage = $this->client->request('GET', 'https://ridan.ru/configurator-pto', [
			'headers' => [
				'Cookie' => $this->cookieSession,
			],
		]);
		$bodyFormPage = $getFormPage->getBody()->getContents();
		$token = $this->getToken($bodyFormPage);
		//Проверяем объект
		$getResultPage = $this->client->request('GET', 'https://ridan.ru/modal?route=search/suggest&type=address&relatedFields[]=ProjectObject[Name]&term=' . $orderName, [
			'headers' => [
				'Cookie' => $this->cookieSession,
				'Content-Type' => 'application/json; charset=UTF-8',
				'X-CSRF-TOKEN' => $token,
			],
			'debug' => false
		]);
		$bodyResultPage = $getResultPage->getBody()->getContents();
		// dd($bodyResultPage);
		// exit;
		$resultArray = json_decode($bodyResultPage, true);
		$this->logClient['getObjects'] = $resultArray;
		return $resultArray;
	}

	//создание объекта
	public function createObject()
	{
		$orderName = $this->objectName;
		$jsonPost = json_decode('{"obj":"{\"OrderId\":null,\"XmlParams\":{\"OrderName\":\"' . $orderName . '\"},\"_mode\":\"update,EmptyCart\"}","objectid":613}', true);
		$getResultPage = $this->client->request('POST', 'https://rucoecom.danfoss.com/OneCRM/Objects/Perform', [
			'headers' => [
				'Cookie' => $this->cookieSession,
				'Content-Type' => 'application/json; charset=UTF-8',
			],
			'json' => $jsonPost,
			'debug' => false
		]);
		$bodyResultPage = $getResultPage->getBody()->getContents();
		$resultArray = json_decode($bodyResultPage, true);
		$this->logClient['createObject'] = $resultArray;
		return $resultArray['Order']['0']['OrderId'];
	}

	//выбор объекта
	public function selectObject()
	{
		$objects = $this->getObjects();
		if (isset($objects['orders'])) {
			$this->oppID = $objects['orders']['0']['OrderId'];
		} else {
			$this->oppID = $this->createObject();
		}
		return $this->oppID;
	}

	//подготовка данных для отправки формы
	private function prepareFormData($token)
	{
		$post_data = array(
			'CalculationType' 	=> 'standard',
			'Aim' 					=> $this->HeatLibrary->prepareAim($this->requestData['ptoTypeUse'], 'key'),
			'Q' 					=> $this->requestData['Q'],
			'QMeasureID' 			=> $this->HeatLibrary->prepareQMeasure($this->requestData['QMeasure'], 'key'),

			'EnvironmentHotId' 		=> $this->HeatLibrary->prepareEnvironment($this->requestData['environmentHot'], 'key'),
			'EnvironmentColdId' 	=> $this->HeatLibrary->prepareEnvironment($this->requestData['environmentCold'], 'key'),

			'PercentHot' 			=> $this->requestData['PercentHot'],
			'PercentCold' 			=> $this->requestData['PercentCold'],

			'T1' 					=> $this->requestData['T1'],
			'T2' 					=> $this->requestData['T2'],
			'T3' 					=> $this->requestData['T3'],
			'T4' 					=> $this->requestData['T4'],

			'G1' 					=> $this->requestData['G1'],
			'G2' 					=> $this->requestData['G2'],
			'GMeasureID' 			=> $this->HeatLibrary->prepareGMeasure($this->requestData['GcoMeasure'], 'key'),

			'R1' 					=> $this->requestData['R1'],
			'R2' 					=> $this->requestData['R2'],
			'RMeasureID' 			=> $this->HeatLibrary->prepareRMeasure($this->requestData['pressureGage'], 'key'),

			'IncludeFlanges' 		=> $this->requestData['IncludeFlanges'],

			'FlangeAssortmentHot' 	=> $this->HeatLibrary->prepareFlange($this->requestData['GVS1_Hot_flangeType'], 'key'),
			'FlangeAssortmentCold' 	=> $this->HeatLibrary->prepareFlange($this->requestData['GVS1_Cold_flangeType'], 'key'),

			'IncludePlugs' 		    => 0,
			'PlugAssortmentHot'		=> $this->HeatLibrary->preparePlugAssortment($this->requestData['plugAssortmentHot'], 'key'),
			'PlugAssortmentCold' 	=> $this->HeatLibrary->preparePlugAssortment($this->requestData['plugAssortmentCold'], 'key'),

			'T' 					=> $this->requestData['T'],
			'P' 					=> $this->requestData['P'],

			'GasketMaterialMaxTemp' => 150,
			'GasketMaterialId' 		=> $this->HeatLibrary->prepareGasketMaterial($this->requestData['gasketMaterial'], 'key'),

			'MaxT' 					=> '160',

			'PlateMaterialId' 		=> $this->HeatLibrary->preparePlateMaterial($this->requestData['plateMaterial_standart'], 'key'),
			'PlateThickness' 		=> $this->HeatLibrary->preparePlateThickIndex($this->requestData['thickness_standart'], 'name'),
			'PlateThickIndex' 		=> $this->HeatLibrary->preparePlateThickIndex($this->requestData['thickness_standart'], 'key'),

			'X' 					=> $this->requestData['X'],
			'PtoType' 				=> '1',

			'FlowDirection' 		=> '0',
			// 'ModelType'				=> 6,


			// 'IncludeFlanges' 		=> '0',


			'_token' 				=> $token,
			'names[PercentCold]' 	=> 'Процентное содержание ХВС',
			'names[PercentHot]' 	=> 'Процентное содержание ГВС',


		);
		return $post_data;
	}


	//подготовка данных для отправки формы
	private function prepareFormDataGVS($token)
	{
		$post_data = [
			'AllowToChangeData' 	=> (($this->requestData['allowChange'] == '1') ? '1' : '0'),
			'FlangeColdId' 			=> $this->HeatLibrary->prepareFlange($this->requestData['GVS1_Cold_flangeType'], 'key'),
			'FlangeHotId' 			=> $this->HeatLibrary->prepareFlange($this->requestData['GVS1_Hot_flangeType'], 'key'),
			'GMeasureId' 			=> $this->HeatLibrary->prepareGMeasure($this->requestData['GcoMeasure'], 'key'),
			'Gc' 					=> $this->requestData['Gc'],
			'Gco' 					=> $this->requestData['Gco_2'],
			'Ggvs' 					=> $this->requestData['G2'],
			'IncludeFlanges' 		=> $this->requestData['IncludeFlanges'],
			'Mono'				 	=> (($this->requestData['mono'] == '1') ? 'on' : ''),
			'Separate'				=> (($this->requestData['pto_2'] == '1') ? 'on' : ''),
			'P' 					=> $this->requestData['P'],

			'PlugAssortmentColdId' 	=> $this->HeatLibrary->preparePlugAssortment($this->requestData['GVS1_Cold_flangeType'], 'key'),
			'PlugAssortmentHotId' 	=> $this->HeatLibrary->preparePlugAssortment($this->requestData['GVS1_Hot_flangeType'], 'key'),

			'Q' 					=> $this->requestData['Q'],
			'Q1' 					=> $this->requestData['Q3'],
			'QMeasure' 				=> $this->HeatLibrary->prepareQMeasure($this->requestData['QMeasure2'], 'key'),
			'QMeasureId' 			=> $this->HeatLibrary->prepareQMeasure($this->requestData['QMeasure'], 'key'),
			'QSplit' 				=> (($this->requestData['qBalanceAuto'] == '1') ? 'AUTO' : 'MANUAL'),
			'Q_1' 					=> $this->UtilitiesTool->prepareNumericArray($this->requestData, 'Q_1', true),
			'Q_2' 					=> $this->UtilitiesTool->prepareNumericArray($this->requestData, 'Q_2', true),
			'RMeasureId' 			=> $this->HeatLibrary->prepareRMeasure($this->requestData['pressureGage'], 'key'),
			'SolutionType' 			=> (($this->requestData['mono'] == '1') ? 'Mono' : 'Separate'),
			'T' 					=> '150',
			'T1' 					=> $this->requestData['T1'],
			'T11' 					=> $this->requestData['T12'],
			'T2' 					=> $this->requestData['T2'],
			'T21' 					=> $this->requestData['T22'],
			'T3' 					=> $this->requestData['T3'],
			'T4' 					=> $this->requestData['T4'],
			'Tc' 					=> $this->requestData['Tc'],
			'Tco' 					=> $this->requestData['Tco'],
			'_token' 				=> $token,
			'autoSplitPressureDrop' => '0',
			'quickSettings' => '1',
			'step[PtoIndex]' => '0',
			'step[ModelType]' => '5',

			'step1[PtoIndex]' 		=> '1',
			'step1[Rc]' 			=> Numb::prepareNumeric($this->requestData['R2']),
			'step1[Rh]' 			=> Numb::prepareNumeric($this->requestData['R1']),
			'step1[X]' 				=> Numb::prepareNumeric($this->requestData['X']),
			'step1[ModelType]' 		=> 8,

			'step2[PtoIndex]'		=> '1',
			'step2[Rc]'				=> Numb::prepareNumeric($this->requestData['R22']),
			'step2[Rh]'				=> Numb::prepareNumeric($this->requestData['R12']),
			'step2[X]' 				=> Numb::prepareNumeric($this->requestData['X2']),
			'step2[ModelType]' 		=> 8,
			'range-1' 				=> 30,
			'scheme-2' 				=> (($this->requestData['mono'] == '1') ? 'Mono' : 'Separate'),
			'scheme-3' 				=> (($this->requestData['mono'] == '1') ? 'Mono' : 'Separate'),
			'GasketMaterialId' 		=> $this->HeatLibrary->prepareGasketMaterial($this->requestData['gasketMaterial'], 'key'),
			'GasketMaterialMaxTemp' 		=> 150,
			'PlateMaterialId' 		=> $this->HeatLibrary->preparePlateMaterial($this->requestData['plateMaterial_standart'], 'key'),
			'PlateThickIndex' 		=> $this->HeatLibrary->preparePlateThickIndex($this->requestData['thickness_standart'], 'key'),
			'G1' 					=> $this->requestData['G1'],
		];
		// dd($post_data);
		return $post_data;
	}


	//получение данных расчета
	public function getFormResult($calctype = '0', $token = null)
	{
		if (empty($token)) {
			$token = $this->getTokenPage();
		}
		//Отправляем форму для получения результатов
		if ($calctype == '0') {
			$post_data = $this->prepareFormData($token);
			$post_url = 'https://ridan.ru/instruments/configurator-pto/standard/search';
		} else {
			$post_data = $this->prepareFormDataGVS($token);
			$post_url = 'https://ridan.ru/instruments/configurator-pto/gvs2/store';
		}
		// print_r($post_data);
		// exit;
		// dd($post_data);
		try {
			$getFormDataPage = $this->client->request('POST', $post_url, [
				'headers' => [
					'Cookie' => $this->cookieSession,
					'X-CSRF-TOKEN' => $token,
					'accept' => 'application/json, text/javascript, */*; q=0.01',
				],
				'form_params' => $post_data,
				'debug' => false
			]);
			$bodyFormDataPage = $getFormDataPage->getBody()->getContents();
			//  print_r($bodyFormDataPage);
			// exit;
			// dd($bodyFormDataPage);
			$calcModelArray = json_decode($bodyFormDataPage, true);

			if ($calctype == '0') {
				$calcModelArray = $this->compatibilityResult($calcModelArray);
			} else {
				$calcModelArray = $this->compatibilityResultGVS($calcModelArray);
				// print_r(json_encode($calcModelArray, JSON_UNESCAPED_UNICODE));
				// exit;
			}
		} catch (RequestException $e) {

			$error = $this->prepareCalulationError($e->getResponse()->getBody()->getContents());
			$calcModelArray['error'] = $error;
		}
		return $calcModelArray;
	}



	public function prepareCalulationError($error)
	{
		$errorArray = json_decode($error, true);
		$errorArrayCorrect = array();
		if (isset($errorArray['errors'])) {
			foreach ($errorArray['errors'] as $key => $value) {
				switch ($key) {
					default:
						$errorArrayCorrect[$key] = $this->UtilitiesTool->trimShy($value);
						break;
				}
			}
		} else {
			$errorArrayCorrect = $errorArray;
		}
		return $errorArrayCorrect;
	}


	//создание расчета теплообменника
	public function createHeat()
	{
		global $core;
		$uid = Auth::id();
		if (empty($uid)) {
			$uid = '79';
		}
		$requset = array(
			'uid' => $uid,
			'heatData' => $this->requestData,
			'status' => '1',
		);
		$heat = \Calculation\Model\Heat::create($requset);
		$this->heatID = $heat->id;
		return $this->heatID;
	}


	public function prepareError($data)
	{
		$prepareError = $data;
		if (is_array($prepareError)) {
			if (isset($prepareError['step1.Rh'])) {
				$prepareError['step1Rh'] = $prepareError['step1.Rh'];
				unset($prepareError['step1.Rh']);
			}
			if (isset($prepareError['step2.Rh'])) {
				$prepareError['step2Rh'] = $prepareError['step2.Rh'];
				unset($prepareError['step2.Rh']);
			}
			if (isset($prepareError['step1.Rc'])) {
				$prepareError['step1Rc'] = $prepareError['step1.Rc'];
				unset($prepareError['step1.Rc']);
			}
			if (isset($prepareError['step2.Rc'])) {
				$prepareError['step2Rc'] = $prepareError['step2.Rc'];
				unset($prepareError['step2.Rc']);
			}
			if (isset($prepareError['step1.X'])) {
				$prepareError['step1X'] = $prepareError['step1.X'];
				unset($prepareError['step1.X']);
			}
			if (isset($prepareError['step2.X'])) {
				$prepareError['step2X'] = $prepareError['step2.X'];
				unset($prepareError['step2.X']);
			}
		}
		return $prepareError;
	}

	public function compatibilityResult($calcModelArray)
	{

		$result = $calcModelArray['result'];
		if (isset($calcModelArray['message'])) {
			$data['message'] = $calcModelArray['message'];
		}
		//поиск request
		$request = Str::betweenFirst($result, '<input name="CorrectedCalculationRequest" type="hidden" value="', '">');
		$data['replace']['.request'] = json_decode(htmlspecialchars_decode($request), true);
		//поиск solution
		$data['count'] = Str::substrCount($result, 'action="/instruments/configurator-pto/standard/popup-save-calculation"');
		$tmp = $result;
		for ($i = 0; $i < $data['count']; $i++) {
			$tmp = stristr($tmp, 'action="/instruments/configurator-pto/standard/popup-save-calculation');
			$tmp = mb_substr($tmp, mb_strlen('action="/instruments/configurator-pto/standard/popup-save-calculation'));
			$data['replace']['.response']['data']['Solutions'][] = json_decode(htmlspecialchars_decode(Str::betweenFirst($tmp, '<input name="Solution" type="hidden" value="', '">')), true);
		}
		return $data;
	}

	public function compatibilityResultGVS($calcModelArray)
	{

		$result = $calcModelArray['result'];
		//поиск request
		// $request = Str::betweenFirst($result, '<input name="Solution" type="hidden" value="', '">');
		// dd(json_decode(htmlspecialchars_decode($request), true));
		// $data['replace']['.request'] = json_decode(htmlspecialchars_decode($request), true);
		//поиск solution
		$data['count'] = Str::substrCount($result, 'action="/instruments/configurator-pto/gvs2/popup-save-solution"');
		$tmp = $result;
		for ($i = 0; $i < $data['count']; $i++) {
			if ($i == 0) {
				$data['replace']['.response']['data']['Solutions'][] = json_decode(htmlspecialchars_decode(Str::betweenFirst($tmp, '<input name="Solution" type="hidden" value="', '">')), true);
			} else {
				$tmp = stristr($tmp, 'action="/instruments/configurator-pto/gvs2/popup-save-solution"');
				$tmp = mb_substr($tmp, mb_strlen('action="/instruments/configurator-pto/gvs2/popup-save-solution"'));
				$data['replace']['.response']['data']['Solutions'][] = json_decode(htmlspecialchars_decode(Str::betweenFirst($tmp, '<input name="Solution" type="hidden" value="', '">')), true);
			}
		}
		// dd($data);
		return $data;
	}



	//создание расчета теплообменника
	public function createHeatSolution()
	{
		$data = $this->getFormResult($this->ptoTypeUse);
		if (isset($data['error'])) {
			$result = array();
			$result['type'] = 'error';
			$result['typeRequst'] = 'auto';
			$result['error'] = $this->prepareError($data['error']);
			$result['requestID'] = '';
		} elseif (isset($data['message']) && !empty($data['message'])) {
			$result = array();
			$result['type'] = 'error';
			$result['typeRequst'] = 'auto';
			$result['error'] = $data['message']['html'];
			$result['requestID'] = '';
		} else {
			if ($this->ptoTypeUse == 0) {
				// dd($data);
				// print_r($data);
				// exit;
				$solution = $data['replace']['.response'];
				if (isset($solution['data']['Solutions'])) {
					\Calculation\Model\Heat::find($this->heatID)->update(array('request' => $data['replace']['.request']));
					foreach ($solution['data']['Solutions'] as $key => $value) {
						$requset = array();
						$requset['heat_id'] = $this->heatID;
						$requset['solution_id'] = $key;
						$requset['solution'] = $value['Solution'];
						$requset['price'] = $value['Price'];
						$requset['deliveryWeeks'] = $value['DeliveryWeeks'];
						$requset['effectiveSurface'] = $value['EffectiveSurface'];
						$requset['weight'] = $value['NetWeight'];
						$requset['height'] = $value['PtoHeight'];
						$requset['width'] = $value['PtoWidth'];
						$requset['length'] = $value['PtoLength'];
						$requset['status'] = '1';
						$requset['detailed'] = array(
							'DisplaySolutionTemplate' => 'SolutionGVS1',
							'lbl_Q' => $value['Q'],
							'lbl_ddlQMeasure' => 'Ккал/ч',
							'lbl_X' => $value['X'],
							'lbl_FZ' => $value['XX'],
							'lbl_TransfetFactor' => $value['HeatTransfetFactor'] . ' / ' . $value['CalcTransferRate'],
							'lbl_TransfertFactorMeasure' => 'ккал/(м²ч.С)',
							'lbl_T' => $value['T'],
							'lbl_P' => $value['P'],
							'lbl_EnvironmentMediaHot' => $this->HeatLibrary->prepareEnvironment($value['EnvironmentHotId'], 'name'),
							'lbl_EnvironmentMediaCold' => $this->HeatLibrary->prepareEnvironment($value['EnvironmentColdId'], 'name'),
							'lbl_G1' => $value['G1'],
							'lbl_G2' => $value['G2'],
							'lbl_GMEasure' => 'т/ч',
							'lbl_T1_3' => $value['T1'] . ' -> ' . $value['T3'],
							'lbl_T2_4' => $value['T2'] . ' -> ' . $value['T4'],
							'lbl_R1_R1S' => round($value['PressureDropInHotPorts'], 2) . ' / ' . round($value['R1'] - $value['PressureDropInHotPorts']),
							'lbl_R2_R2S' => round($value['PressureDropInColdPorts'], 2) . ' / ' . round($value['R2'] - $value['PressureDropInColdPorts']),
							'lbl_PressureGage' => 'м.вод.ст.',
							'lbl_VelocityHot' => $value['VelocityInPortHot'] . ' / ' . $value['VelocityInChannelHot'],
							'lbl_VelocityCold' => $value['VelocityInPortCold'] . ' / ' . $value['VelocityInChannelCold'],
							'lbl_VelocityMeasure' => 'м/с',
							'lbl_NetWeight' => $value['NetWeight'],
							'lbl_GasketMaterial' => $this->HeatLibrary->prepareGasketMaterial($value['GasketMaterialId'], 'name'),
							'lbl_CommonInfo1_2' => $value['PlateMaterialId'] . ' ' . $value['PlateThickness'] . ' мм.',
							'lbl_ConnectionHot' => $value['ConnectionHot'] . '(' . $this->HeatLibrary->prepareFlange($value['FlangeBwnGasketHId'], 'name') . ')',
							'lbl_ConnectionCold' => $value['ConnectionCold'] . '(' . $this->HeatLibrary->prepareFlange($value['FlangeBwnGasketCId'], 'name') . ')',
							'lbl_PortSurfaceH' => (empty($value['PortSurfaceH'])) ? '---' : $value['PortSurfaceH'],
							'lbl_PortSurfaceC' => (empty($value['PortSurfaceC'])) ? '---' : $value['PortSurfaceH'],
							'lbl_MaxPlateCount' => $value['MaxPlateCount'],
							'lbl_RO1' => $value['RO1'],
							'lbl_RO2' => $value['RO2'],
							'lbl_ROMeasure' => 'т/м3',
							'lbl_C1' => $value['C1'],
							'lbl_C2' => $value['C2'],
							'lbl_CMeasure' => 'кДж/(кг*К)',
							'lbl_LA1' => $value['LA1'],
							'lbl_LA2' => $value['LA2'],
							'lbl_LAMeasure' => 'Вт/(м*К)',
							'lbl_AverageM1' => $value['AverageM1'],
							'lbl_AverageM2' => $value['AverageM2'],
							'lbl_AverageMMeasure' => 'Па*с',
							"lbl_UsePTOSize" 				=> $value['UsePTOSize'],
							"lbl_PTOType" 					=> $value['PTOType'],

						);
						$requset['response'] = $value;
						$requset['result'][] = [
							'SolutionType' => 'GVS1',
							'UsePTOSize' => $value['UsePTOSize'],
							'PTOType' => $value['PTOType'],
							'dn' => [
								'value' => $value['DnHot'],
								'measure' => 'мм.',
								'description' => 'Диаметр',
							],
							'dnHot' => [
								'value' => $value['DnHot'],
								'measure' => 'мм.',
								'description' => 'Диаметр портов (гор.)',
							],
							'dnCold' => [
								'value' => $value['DnCold'],
								'measure' => 'мм.',
								'description' => 'Диаметр портов (хол.)',
							],
							'q' => [
								'value' => $value['Q'],
								'measure' => 'ккал/ч',
								'description' => 'Тепловая нагрузка',
							],
							't' => [
								'value' => $value['T'],
								'measure' => '℃',
								'description' => 'Максимальная температура',
							],
							'effectiveSurface' => [
								'value' => $value['EffectiveSurface'],
								'measure' => 'м²',
								'description' => 'Площадь теплопередающей поверхности',
							],
							'p' => [
								'value' => $value['P'],
								'measure' => 'бар',
								'description' => 'Рабочее давление(рама)',
							],
							'platesNum' => [
								'value' => $value['PlatesNum'],
								'measure' => 'шт',
								'description' => 'Число пластин',
							],
							'netWeight' => [
								'value' => $value['NetWeight'],
								'measure' => 'кг',
								'description' => 'Вес нетто',
							],
							'ptoHeight' => [
								'value' => $value['PtoHeight'],
								'measure' => 'мм',
								'description' => 'Высота ТО',
							],
							'ptoWidth' => [
								'value' => $value['PtoWidth'],
								'measure' => 'мм',
								'description' => 'Ширина ТО',
							],
							'frameLength' => [
								'value' => $value['PtoLength'],
								'measure' => 'мм',
								'description' => 'Длина рамы',
							],
							'price' => [
								'value' => $value['Price'],
								'currencyName' => 'EUR',
							],
						];

						\Calculation\Model\HeatSolution::create($requset);
					}
				}
			} else {
				$solution = $data['replace']['.response'];
				// dd($data['replace']['.response']);
				// dd($solution['data'][0]['body'][0]['netWeight']['value']);
				// exit;
				if (isset($data['replace']['.request'])) {
					\Calculation\Model\Heat::find($this->heatID)->update(array('request' => $data['replace']['.request']));
				}
				foreach ($solution['data']['Solutions'] as $key => $value) {
					if (isset($value['body'])) {
						$ptoCount = count($value['body']);
					} else {
						$ptoCount = 0;
					}
					if ($ptoCount > 1) {
						$stepFirst  = $value['body'][0];
						$stepSecond = $value['body'][1];
						foreach ($value['body'] as $bodyKey => $bodyValue) {
							$requset = array();
							$requset['heat_id'] = $this->heatID;
							$requset['solution_id'] = $key;
							$requset['solution'] = $bodyValue['solutionName']['value'] ?: $bodyValue['varPack']['name'];
							$requset['price'] = $bodyValue['price']['value'];
							$requset['deliveryWeeks'] = $value['deliveryWeeks'];
							$requset['effectiveSurface'] = $bodyValue['effectiveSurface']['value'];
							$requset['weight'] = $bodyValue['netWeight']['value'];
							$requset['height'] = $bodyValue['ptoHeight']['value'];
							$requset['width'] = $bodyValue['ptoWidth']['value'];
							$requset['length'] = $bodyValue['frameLength']['value'];
							$requset['status'] = '1';

							foreach ($value['body'] as $bk => $bv) {
								$requset['result'][$bk] = [
									'SolutionType' => $value['solutionType'],
									'UsePTOSize' => $bv['UsePTOSize']['value'],
									'PTOType' => $bv['PTOType']['value'],
									'dn' => [
										'value' => $bv['dnHot']['value'],
										'measure' => $bv['dnHot']['measure'],
										'description' => 'Диаметр',
									],
									'dnHot' => [
										'value' => $bv['dnHot']['value'],
										'measure' => $bv['dnHot']['measure'],
										'description' => $bv['dnHot']['_fieldDescription'],
									],
									'dnCold' => [
										'value' => $bv['dnCold']['value'],
										'measure' => $bv['dnCold']['measure'],
										'description' => $bv['dnCold']['_fieldDescription'],
									],
									'q' => [
										'value' => $bv['q']['value'],
										'measure' => $bv['q']['measure'],
										'description' => $bv['q']['_fieldDescription'],
									],
									't' => [
										'value' => $bv['t']['value'],
										'measure' => $bv['t']['measure'],
										'description' => $bv['t']['_fieldDescription'],
									],
									'effectiveSurface' => [
										'value' => $bv['effectiveSurface']['value'],
										'measure' => $bv['effectiveSurface']['measure'],
										'description' => $bv['effectiveSurface']['_fieldDescription'],
									],
									'p' => [
										'value' => $bv['p']['value'],
										'measure' => $bv['p']['measure'],
										'description' => $bv['p']['_fieldDescription'],
									],
									'platesNum' => [
										'value' => $bv['platesNum']['value'],
										'measure' => $bv['platesNum']['measure'],
										'description' => $bv['platesNum']['_fieldDescription'],
									],
									'netWeight' => [
										'value' => $bv['netWeight']['value'],
										'measure' => $bv['netWeight']['measure'],
										'description' => $bv['netWeight']['_fieldDescription'],
									],
									'ptoHeight' => [
										'value' => $bv['ptoHeight']['value'],
										'measure' => $bv['ptoHeight']['measure'],
										'description' => $bv['ptoHeight']['_fieldDescription'],
									],
									'ptoWidth' => [
										'value' => $bv['ptoWidth']['value'],
										'measure' => $bv['ptoWidth']['measure'],
										'description' => $bv['ptoWidth']['_fieldDescription'],
									],
									'frameLength' => [
										'value' => $bv['frameLength']['value'],
										'measure' => $bv['frameLength']['measure'],
										'description' => $bv['frameLength']['_fieldDescription'],
									],
									'price' => [
										'value' => $bv['price']['value'],
										'currencyName' => $bv['price']['currencyName'],
									],
								];
							}
							$requset['detailed'] = array(
								"DisplaySolutionTemplate" 		=> "SolutionGVS2",
								"lbl1_G1" 						=> $stepFirst['g1']['value'],
								"lbl1_G2" 						=> $stepFirst['g2']['value'],
								"lbl2_G1" 						=> $stepSecond['g1']['value'],
								"lbl2_G2" 						=> $stepSecond['g2']['value'],
								"lGmeasureId" 					=> $stepFirst['g1']['measure'],
								"lbl1_T1" 						=> $stepFirst['t1']['value'],
								"lbl1_T3" 						=> $stepFirst['t3']['value'],
								"lbl2_T1" 						=> $stepSecond['t1']['value'],
								"lbl2_T3" 						=> $stepSecond['t3']['value'],
								"lbl1_T2" 						=> $stepFirst['t2']['value'],
								"lbl1_T4" 						=> $stepFirst['t4']['value'],
								"lbl2_T2" 						=> $stepSecond['t2']['value'],
								"lbl2_T4" 						=> $stepSecond['t4']['value'],
								"lbl1_R1" 						=> $stepFirst['R1']['value'],
								"lbl1_R2" 						=> $stepFirst['R2']['value'],
								"lbl2_R1" 						=> $stepSecond['R1']['value'],
								"lbl2_R2" 						=> $stepSecond['R2']['value'],
								"lRmeasureId" 					=> $stepFirst['R1']['measure'],
								"lbl1_VelocityInPortHot" 		=> $stepFirst['velocityInPortHot']['value'],
								"lbl1_VelocityInPortCold" 		=> $stepFirst['velocityInPortCold']['value'],
								"lbl2_VelocityInPortHot" 		=> $stepSecond['velocityInPortHot']['value'],
								"lbl2_VelocityInPortCold" 		=> $stepSecond['velocityInPortCold']['value'],
								"lbl1_Q" 						=> $stepFirst['q']['value'],
								"lbl2_Q" 						=> $stepSecond['q']['value'],
								"lQMeasureValue" 				=> $stepFirst['q']['measure'],
								"lbl1_X" 						=> $stepFirst['x']['value'],
								"lbl2_X" 						=> $stepSecond['x']['value'],
								"lbl1_FZ" 						=> $stepFirst['pollutionFactorHot']['value'] + $stepFirst['pollutionFactorCold']['value'],
								"lbl2_FZ" 						=> $stepSecond['pollutionFactorHot']['value'] + $stepSecond['pollutionFactorCold']['value'],
								"lbl1_TransferFactor"			=> $stepFirst['heatTransfetFactor']['value'] . ' / ' . $stepFirst['calcTransferRate']['value'],
								"lbl2_TransferFactor"			=> $stepSecond['heatTransfetFactor']['value'] . ' / ' . $stepSecond['calcTransferRate']['value'],
								"lbl1_EffectiveSurface" 		=> $stepFirst['effectiveSurface']['value'],
								"lbl2_EffectiveSurface" 		=> $stepSecond['effectiveSurface']['value'],
								"lbl1_PlateComponent" 			=> $stepFirst['Arrangement']['value'],
								"lbl2_PlateComponent" 			=> $stepSecond['Arrangement']['value'],
								"lbl1_AssemblingHotChanels" 	=> $stepFirst['AssemblingHotChanels']['value'],
								"lbl1_AssemblingColdChanels" 	=> $stepFirst['AssemblingColdChanels']['value'],
								"lbl2_AssemblingHotChanels" 	=> $stepSecond['AssemblingHotChanels']['value'],
								"lbl2_AssemblingColdChanels" 	=> $stepSecond['AssemblingColdChanels']['value'],
								"lbl1_PlaneMaterial" 			=> $stepFirst['PlateMaterial']['value'] . ' ' . $stepFirst['PlateThickness']['value'] . ' мм.',
								"lbl1_GasketMaterialId" 		=> $stepFirst['GasketMaterial']['value'],
								"lbl1_Pressure" 				=> "16/23",
								"lbl1_Temperature" 				=> "150",
								"lbl2_PlaneMaterial" 			=> $stepSecond['PlateMaterial']['value'] . ' ' . $stepSecond['PlateThickness']['value'] . ' мм.',
								"lbl2_GasketMaterialId" 		=> $stepSecond['GasketMaterial']['value'],
								"lbl2_Pressure" 				=> "16/23",
								"lbl2_Temperature" 				=> "150",
								"lbl1_ConnectionHot" 			=> $stepFirst['HotSideConnections']['name'],
								"lbl2_ConnectionHot" 			=> $stepSecond['HotSideConnections']['name'],
								"lbl1_ConnectionCold" 			=> $stepFirst['ColdSideConnections']['name'],
								"lbl2_ConnectionCold" 			=> $stepSecond['ColdSideConnections']['name'],
								"lbl2_PortSurfaceHot" 			=> "---",
								"lbl1_PortSurfaceHot" 			=> "---",
								"lbl2_PortSurfaceCold" 			=> "---",
								"lbl1_PortSurfaceCold" 			=> "---",
								"lbl1_pollutionFactorCold" 		=> $stepFirst['pollutionFactorCold']['value'],
								"lbl1_pollutionFactorHot" 		=> $stepFirst['pollutionFactorHot']['value'],
								"lbl2_pollutionFactorCold" 		=> $stepSecond['pollutionFactorCold']['value'],
								"lbl2_pollutionFactorHot" 		=> $stepSecond['pollutionFactorHot']['value'],
								"lbl1_AssemblingColdChanels" 	=> $stepFirst['AssemblingColdChanels']['value'],
								"lbl1_AssemblingHotChanels" 	=> $stepFirst['AssemblingHotChanels']['value'],
								"lbl2_AssemblingColdChanels" 	=> $stepSecond['AssemblingColdChanels']['value'],
								"lbl2_AssemblingHotChanels" 	=> $stepSecond['AssemblingHotChanels']['value'],
								"lbl1_platesNum" 				=> $stepFirst['platesNum']['value'],
								"lbl2_platesNum" 				=> $stepSecond['platesNum']['value'],
								"lbl1_Arrangement" 				=> $stepFirst['Arrangement']['value'],
								"lbl2_Arrangement" 				=> $stepSecond['Arrangement']['value'],
								"solutionTypeCaption" 			=> ($value['solutionType'] == 'GVS2MONO') ? "моноблок" : "раздельные ступени",
								"lbl1_UsePTOSize" 				=> $stepFirst['UsePTOSize']['value'],
								"lbl2_UsePTOSize" 				=> $stepSecond['UsePTOSize']['value'],
								"lbl1_PTOType" 					=> $stepFirst['PTOType']['value'],
								"lbl2_PTOType" 					=> $stepSecond['PTOType']['value'],
							);
							$requset['response'] = $value;
							\Calculation\Model\HeatSolution::create($requset);
						}
					} else {
					}
				}
			}
			$result = array();
			$result['type'] = 'success';
			$result['typeRequst'] = 'auto';
			$result['requestID'] = $this->requestID;
		}
		$solution = \Calculation\Model\HeatSolution::where('heat_id', $this->heatID)->get()->toArray();

		$this->solutionArray = $solution;

		return $result;
	}

	private function checkVar($var)
	{
		return (!empty($var) ? $var : 'Не указано');
	}


	protected function createRequstInfo()
	{
		//подготовка информации в текстовом виде
		$request = [];
		$request[] = [
			'type' => 'title',
			'content' => 'Информация о заявке',
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'Применение ПТО',
			'value' => $this->checkVar($this->requestData['ptoTypeUse']),
		];
		$request[] = [
			'type' => 'title',
			'content' => 'Греющая сторона',
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'Температура среды на входе в ПТО',
			'value' => $this->checkVar($this->requestData['T1']),
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'Температура среды на выходе из ПТО',
			'value' => $this->checkVar($this->requestData['T2']),
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'Массовый расход среды (т/ч)',
			'value' => $this->checkVar($this->requestData['G1']),
		];
		if ($this->ptoTypeUse == 0) {
			$request[] = [
				'type' => 'parametr',
				'content' => 'Потери напора (м.в.ст.)',
				'value' => $this->checkVar($this->checkVar($this->requestData['R1'])),
			];
		} else {
			$request[] = [
				'type' => 'parametr',
				'content' => 'Потери напора (м.в.ст.) 1 ступень',
				'value' => $this->checkVar($this->checkVar($this->requestData['R1'])),
			];
			$request[] = [
				'type' => 'parametr',
				'content' => 'Потери напора (м.в.ст.) 2 ступень',
				'value' => $this->checkVar($this->checkVar($this->requestData['R12'])),
			];
		}
		$request[] = [
			'type' => 'parametr',
			'content' => 'Тип греющей среды',
			'value' => $this->checkVar($this->requestData['environmentHot']),
		];
		$request[] = [
			'type' => 'title',
			'content' => 'Нагреваемая сторона',
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'Температура среды на входе в ПТО',
			'value' => $this->checkVar($this->requestData['T4']),
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'Температура среды на выходе из ПТО',
			'value' => $this->checkVar($this->requestData['T3']),
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'Массовый расход среды (т/ч)',
			'value' => $this->checkVar($this->requestData['G2']),
		];
		if ($this->ptoTypeUse == 0) {
			$request[] = [
				'type' => 'parametr',
				'content' => 'Потери напора (м.в.ст.)',
				'value' => $this->checkVar($this->checkVar($this->requestData['R2'])),
			];
		} else {
			$request[] = [
				'type' => 'parametr',
				'content' => 'Потери напора (м.в.ст.) 1 ступень',
				'value' => $this->checkVar($this->checkVar($this->requestData['R2'])),
			];
			$request[] = [
				'type' => 'parametr',
				'content' => 'Потери напора (м.в.ст.) 2 ступень',
				'value' => $this->checkVar($this->checkVar($this->requestData['R22'])),
			];
		}
		$request[] = [
			'type' => 'parametr',
			'content' => 'Тип греющей среды',
			'value' => $this->checkVar($this->requestData['environmentCold']),
		];
		$request[] = [
			'type' => 'title',
			'content' => 'Основная информация',
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'Тепловая нагрузка',
			'value' => $this->checkVar($this->requestData['Q']),
			'sub' =>  $this->checkVar($this->requestData['QMeasure']),
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'Расчетное давление',
			'value' => $this->checkVar($this->requestData['P']),
		];
		if ($this->ptoTypeUse == 0) {
			$request[] = [
				'type' => 'parametr',
				'content' => 'Запас площади поверхности (в процентах)',
				'value' => $this->checkVar($this->requestData['X']),
			];
		} else {
			$request[] = [
				'type' => 'parametr',
				'content' => 'Запас площади поверхности (в процентах) 1 ступень',
				'value' => $this->checkVar($this->requestData['X']),
			];
			$request[] = [
				'type' => 'parametr',
				'content' => 'Запас площади поверхности (в процентах) 2 ступень',
				'value' => $this->checkVar($this->requestData['X2']),
			];
		}
		if ($this->ptoTypeUse == 1) {
			$request[] = [
				'type' => 'title',
				'content' => 'Дополнительная информация',
			];
			$request[] = [
				'type' => 'parametr',
				'content' => 'Расход теплосетевой воды',
				'value' => $this->checkVar($this->requestData['Gco_2']),
			];
			$request[] = [
				'type' => 'parametr',
				'content' => 'Разбить расход автоматически',
				'value' => ($this->requestData['qBalanceAuto'] == 1 ? '<font color="green">Да</font>' : '<font color="red">Нет</font><br>'),
			];
			if ($this->requestData['qBalanceAuto'] == 0) {
				$request[] = [
					'type' => 'parametr',
					'content' => 'Расход (в процентах) 1 ступень',
					'value' => $this->checkVar($this->requestData['Q1']),
				];
				$request[] = [
					'type' => 'parametr',
					'content' => 'Расход (в процентах) 2 ступень',
					'value' => $this->checkVar($this->requestData['Q2']),
				];
			}
			$request[] = [
				'type' => 'parametr',
				'content' => 'Моноблок или 2 раздельных ПТО',
				'value' => (($this->requestData['mono'] == '1') ? 'Моноблок' : '2 раздельных ПТО'),
			];
			$request[] = [
				'type' => 'parametr',
				'content' => 'Включить в стоимость ответные фланцы',
				'value' => (($this->requestData['flan'] == '1') ? '<font color="green">Да</font>' : '<font color="red">Нет</font>'),
			];
			$request[] = [
				'type' => 'parametr',
				'content' => 'Разрешить программе изменять начальные данные, чтобы находить более дешевые варианты',
				'value' => (($this->requestData['allowChange'] == '1') ? '<font color="green">Да</font>' : '<font color="red">Нет</font>'),
			];
		}
		$request[] = [
			'type' => 'title',
			'content' => 'Информация о заказчике',
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'ФИО заказчика',
			'value' => $this->checkVar($this->requestData['uid_name']),
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'Телефон заказчика',
			'value' => $this->checkVar($this->requestData['uid_phone']),
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'Email заказчика',
			'value' => $this->checkVar($this->requestData['uid_mail']),
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'Объект',
			'value' => $this->checkVar($this->requestData['object_name']),
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'Организация',
			'value' => $this->checkVar($this->requestData['oid_name']),
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'ИНН организация',
			'value' => $this->checkVar($this->requestData['oid_inn']),
		];
		$request[] = [
			'type' => 'title',
			'content' => 'Сообщение',
		];
		$request[] = [
			'type' => 'parametr',
			'content' => 'Сообщение пользователя',
			'value' => $this->checkVar($this->requestData['message']),
		];


		$requestComplete = '';
		foreach ($request as $key => $value) {
			if ($value['type'] == 'title') {
				$requestComplete .= '<h6>' . $value['content'] . ':</h6>';
			}
			if ($value['type'] == 'parametr') {
				$requestComplete .= '<b>' . $value['content'] . ': </b> ' . $value['value'] . ' ' . (isset($value['sub']) ? $value['sub'] : '') . '<br>';
			}
		}
		return $requestComplete;
	}

	//создание заявки на расчет
	public function createRequstHand()
	{
		if (Auth::isUser()) {
			$uid = Auth::id();
		} else {
			$uid = 0;
		}
		$title = 'Заявка на расчет теплообменника';
		$request = array();
		$request['uid_dir'] = $uid;
		$request['title'] = $title;
		$request['title_user'] = $title;
		$request['request'] = $this->createRequstInfo();
		$request['request_data'] = '';
		$request['type'] = '3';
		$request['pid'] = '0';
		$request['date'] = date("Y-m-d H:i:s");
		$request['status'] = '1';
		$result = \Request\Model\Request::create($request);
		$this->requestID = $result->id;
		return $this->requestID;
	}



	public function createRequst()
	{
		global $core;
		$uid = Auth::id();
		// if(empty())
		// dump($this->heatID);

		// dd($heat);
		$pid = $this->heatID;
		if (!empty($id)) {
			$pid = intval($id);
		}
		$title = 'Расчет теплообменника №' . implode(', ', $this->rNumb);
		$request = array();
		$request['uid_dir'] = $uid;
		$request['title'] = $title;
		$request['title_user'] = $title;
		$request['request'] = $this->createRequstInfo();
		if (!empty($this->requestData['oid'])) {
			$request['oid'] = $this->requestData['oid'];
		}
		if (!empty($this->requestData['object_id'])) {
			$request['object'] = $this->requestData['object_id'];
		}
		if (isset($this->heatID)) {
			$heat = \Calculation\Model\Heat::find($this->heatID)->toArray();
			$request['request_data'] = $heat['heatData'];
		} else {
			$request['request_data'] = '';
		}

		$request['type'] = '3';
		$request['pid'] = $pid;
		$request['date'] = date("Y-m-d H:i:s");
		$request['status'] = '3';
		$result = \Request\Model\Request::create($request);
		$this->requestID = $result->id;
		return $this->requestID;
	}

	public function createRequstAfter($id = null)
	{

		global $core;
		$uid = Auth::check() ? Auth::id() : 79;
		$heat = \Calculation\Model\Heat::find($this->heatID)->toArray();
		// dd($heat);
		$pid = $this->heatID;
		if (!empty($id)) {
			$pid = intval($id);
		}
		$title = 'Расчет теплообменника №' . implode(', ', $this->rNumb);
		$request = array();
		$request['uid_dir'] = $uid;
		$request['title'] = $title;
		$request['title_user'] = $title;
		$request['request'] = $this->createRequstInfo();
		if (!empty($this->requestData['oid'])) {
			$request['oid'] = $this->requestData['oid'];
		}
		if (!empty($this->requestData['object_id'])) {
			$request['object'] = $this->requestData['object_id'];
		}
		$request['request_data'] = $heat['heatData'];
		$request['type'] = '3';
		$request['pid'] = $pid;
		$request['date'] = \Carbon\Carbon::now();
		$request['status'] = '3';
		$result = \Request\Model\Request::create($request);
		$this->requestID = $result->id;
		return $this->requestID;
	}


	/**
	 * Получаем данных решеняи из базы данных по ID решению
	 *
	 * @param 	int  		$solutionID ID решения
	 * @return 	array|bool  Возвращает данные решения, если не найдено то false
	 */
	public function getSolutionData($solutionID)
	{
		$solution = HeatSolution::with('heat')->find($solutionID);
		if (!empty($solution)) {
			$heatData = $solution->heat->heatData;
			if ($solution->heat->heatData['ptoTypeUse'] == 'ГВС двухступенчатая') {
				$this->ptoTypeUse = 1;
			}
			$this->getRequsetDataFromArray($heatData);
			$this->heatID = $solution->heat_id;
			$this->solutionID = $solution->id;
			$this->solutionData = $solution->toArray();
			return $this->solutionData;
		}
		return false;
	}

	/**
	 * Получаем ID решения из базы по ID теплообменника и номеру, также id решения заносится в $this->solutionID
	 *
	 * @param 	int  $heatID ID теплообменика
	 * @param 	int  $solutionNumb номер решения
	 * @return 	int|bool	Возвращает ID решения, если не найдено то false
	 */
	public function getSolutionIDByHeat($heatID, $solutionNumb)
	{
		$solution = HeatSolution::where('heat_id', $heatID)->where('solution_id', $solutionNumb)->first();
		if (!empty($solution)) {
			$this->solutionID = $solution->id;
			return $this->solutionID;
		}
		return false;
	}

	/**
	 * Сохранение расчета на https://ridan.ru/ и получение его номера
	 *
	 * @return 	array 	Возвращает номер сохраненного расчета
	 */
	public function saveSolution()
	{
		$token = $this->getTokenPage();
		$data = $this->getFormResult($this->ptoTypeUse, $token);
		$request = [];
		$modalRequest = [];

		if ($this->ptoTypeUse == '0') {
			$solution = $data['replace']['.response'];
			$url = 'https://ridan.ru/instruments/configurator-pto/standard/multi-save-calculation';
			$request['forms[0][CorrectedCalculationRequest]'] = json_encode($data['replace']['.request'], JSON_UNESCAPED_UNICODE);
			$request['forms[0][Solution]'] = json_encode($this->solutionData['response'], JSON_UNESCAPED_UNICODE);
			// $request['ProjectObject[Address]'] = '';
			$request['forms[0][Comment]'] = $this->requestData['ptoTypeUse'];
			// $request['ProjectObject[Comment]'] = '';
			$request['ProjectObject[Name]'] = (!empty($this->requestData['object_name'])) ? ee($this->requestData['object_name'])  : 'Без объекта';
			// $request['ProjectObject[Number]'] = '';
			// $urlModal = 'https://ridan.ru/modal?route=pto/popupSaveCalculation';
			// $modalRequest['form'][0]['CorrectedCalculationRequest'] = $request['CorrectedCalculationRequest'];
			// $modalRequest['form'][0]['Solution'] = $request['Solution'];
		} else {
			$url = 'https://ridan.ru/instruments/configurator-pto/gvs2/multi-save-solution';
			$sol = $this->solutionData['response'];
			$request['forms[0][SolutionId]'] = $sol['id'];
			// $request['ProjectObjectInput[Address]'] = '';
			$request['forms[0][Comment]'] = $this->requestData['ptoTypeUse'];
			// $request['ProjectObjectInput[Comment]'] = '';
			$request['ProjectObjectInput[Name]'] = (!empty($this->requestData['object_name'])) ? ee($this->requestData['object_name'])  : 'Без объекта';
			// $request['ProjectObjectInput[Number]'] = '';
		}
		//$request['coord'] = '';

		// print_r($this->getContent('POST', $urlModal, $modalRequest, $token));
		$response = json_decode($this->getContent('POST', $url, $request, $token), true);
		// var_dump($response);
		// var_dump($this->getContent('POST', $url, $request, $token));
		// dd('ffff');
		// exit;
		// $rNumb = stristr($response['result'], ' Номер Вашего расчета: ');
		// $rNumb = str_replace('Номер Вашего расчета: ', "", $response['result']['title']);
		$rNumb = str_replace("<p> Номера ваших расчетов: </p>\r\n    <p> ", "", $response['result']['html']);
		// var_dump($rNumb);
		$rNumb = stristr($rNumb, ' </p>', true);
		// var_dump($rNumb);
		// dd($rNumb);
		// $rNumb = stristr($rNumb, ' </div>', true);
		// $rNumb = str_replace(' ', '', $rNumb);
		// $rNumb = preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/", '', $rNumb);
		// print_r($rNumb);

		$this->rNumb[] = trim($rNumb);
		if ($this->ptoTypeUse != '0') {
			if ($this->requestData['pto_2'] == '1') {
				$this->rNumb[] = 'w' . (intval(preg_replace("/[^,.0-9]/", '', trim($rNumb))) + 1);
			}
		}
		return $rNumb;
	}

	/**
	 * Получение ID расчета по HTML данным из поля calculationId
	 *
	 * @param 	string  $contentd Строка для поска
	 * @return 	array 	Возвращает ID расчета, если не найден вернет false
	 */
	protected function getCalculationId($content)
	{
		$calculationId = stristr($content, '<input type="hidden" name="calculationId" value="');
		$calculationId = str_replace('<input type="hidden" name="calculationId" value="', "", $calculationId);
		$calculationId = stristr($calculationId, '">', true);
		if (!empty($calculationId)) {
			return $calculationId;
		}
		return false;
	}

	protected function getCalculationIdSecondStep($content)
	{
		$calculationId = stristr($content, 'name="calculationId" value="');
		$calculationId = stristr($calculationId, '<input type="hidden" name="calculationId" value="');
		$calculationId = str_replace('<input type="hidden" name="calculationId" value="', "", $calculationId);
		$calculationId = stristr($calculationId, '">', true);
		if (!empty($calculationId)) {
			return $calculationId;
		}
		return false;
	}

	/**
	 * Получение ID расчетов по его номеру с https://ridan.ru/
	 *
	 * @return 	array 	Возвращает массив с ID расчетов, если двеступечатая схема
	 * 					с раздельными ступенями, добавит в список расчетов, вторую ступень
	 */
	public function getSolution()
	{
		$token = $this->getTokenPage();
		$searchUrl = 'https://ridan.ru/api/v1/cabinet/calculations/pto/search?filter%5Bdate%5D%5Bfrom%5D=' . date('d.m.y') . '&filter%5Bdate%5D%5Bto%5D=' . date('d.m.y') . '&filter%5Bnumber%5D=' . $this->rNumb[0] . '&filter%5Bobject%5D=&filter%5BauthorId%5D=&filter%5Bitin%5D=1840013705&sort%5Bdate%5D=DESC&';
		$searchResult = json_decode($this->getContent('GET', $searchUrl, [], $token), true);
		foreach ($searchResult['data']['items'][0]['bodyParts'] as $key => $value) {
			$this->ptoUrl[] = $value['files'][0]['url'];
		}
		// print_r($this->ptoUrl);
		// $listUrl = 'https://ridan.ru/calculations-pto/list?_token=' . $token . '&date[from]=' . date('d.m.Y') . '&date[to]=' . date('d.m.Y') . '&numbers=&projectObject=&createdBy=any&organisation[itin]=1840013705&pageNumber=1&perPage=10';
		// print_r($listUrl);
		// $list = $this->getContent('GET', $listUrl, [], $token);
		// if ($this->ptoTypeUse == '0') {
		// 	$url = 'https://ridan.ru/calculations-pto/show?number=' . trim($this->rNumb[0]) . '&archived=0&step=1';
		// 	$response = json_decode($this->getContent('GET', $url, [], false), true);
		// 	// print_r($this->getContent('GET', $url, [], false));
		// 	// exit;
		// 	$this->ptoID[] = $this->getCalculationId($response['data']);
		// } else {
		// 	for ($i = 1; $i <= 2; $i++) {
		// 		$url = 'https://ridan.ru/calculations-pto/show?number=' . trim($this->rNumb[0]) . '&archived=0&step=' . $i . '';
		// 		// print_r($url);
		// 		$response = json_decode($this->getContent('GET', $url, [], false), true);
		// 		$calculationId = $this->getCalculationId($response['data']);
		// 		if ($i == 1) {
		// 			$this->ptoID[] = $calculationId;
		// 		} elseif ($i == 2 && Str::substrCount($response['data'], 'calculationId') != 1) {
		// 			$calculationId = $this->getCalculationIdSecondStep($response['data']);
		// 			$pto = stristr($response['data'], '<input name="Number" type="hidden" value="');
		// 			$pto = str_replace('<input name="Number" type="hidden" value="', "", $pto);
		// 			$pto = stristr($pto, '">', true);
		// 			$this->rNumb[] = trim($pto);
		// 			$this->ptoID[] = $calculationId;
		// 		}
		// 	}
		// }
		// dd($this->rNumb);
		// print_r($this->rNumb);
		// print_r($this->ptoID);
		// exit;
		// exit;
		return $this->ptoUrl;
	}


	/**
	 * Получение данных по заданным параметрам
	 *
	 * @param 	string    			$method Метод запроса (GET|POST)
	 * @param 	string    			$url Ссылка на которую нажно отправить запрос
	 * @param 	array     			$form Передаваемые данные
	 * @param 	string|bool|null    $token Токен для запроса, если null будет создан автоматически, если false, не будет подставлятся
	 * @param 	array     			$params Дополнительные параметры запроса
	 * @return 	int 	  			Возвращает полученные данные
	 */
	protected function getContent($method, $url, $form = [], $token = null, $params = [])
	{
		$headers = [
			'Cookie' => $this->cookieSession,
			'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
		];
		$headers['accept'] = 'application/json, text/javascript, */*; q=0.01';
		if (isset($params['accept'])) {
			$headers['accept'] = $params['accept'];
		}
		if ($token !== false) {
			if (empty($token)) {
				$token = $this->getTokenPage();
			}
			$form['_token']	= $token;
			$headers['X-CSRF-TOKEN'] = $token;
		}
		try {
			$getResult = $this->client->request($method, $url, [
				'headers' => $headers,
				'form_params' => $form,
				'debug' => false
			]);
			$result = $getResult->getBody()->getContents();
		} catch (RequestException $e) {
			$result = $e->getResponse()->getBody()->getContents();
		}
		return $result;
	}

	/**
	 * Получение файла расчета с https://ridan.ru/
	 *
	 * @param 	string    $title Название расчета
	 * @param 	string    $url Ссылка на расчет
	 * @return 	array 	  Возвращает массив id (aid) расчетов в базе
	 */
	public function getSolutionAttache()
	{
		$aid = array();
		$cid = array();
		$i = 0;
		$token = $this->getTokenPage();
		// var_dump($this->rNumb);
		// exit;
		foreach ($this->ptoUrl as $key => $value) {
			$pdfCode = $this->getContent('GET', 'https://ridan.ru/' . $value, [], $token);

			if (!file_exists(ROOT . 'files/calc/')) {
				mkdir(ROOT . 'files/calc/', 0777, true);
			}
			$file = 'files/calc/' . $this->rNumb[$key] . '.pdf';
			$fh = fopen(ROOT . '/' . $file, 'w');
			fwrite($fh, $pdfCode);
			fclose($fh);
			$aid[] = $this->saveAttache($this->rNumb[$key], $file);
			$cid[] = $this->createCatalog($this->solutionID, $aid[$i], $i);
			$i++;
		}
		HeatSolution::find($this->solutionID)->update(['aid' => $aid, 'cid' => $cid]);
		$this->cidArray = $cid;
		return $aid;
	}

	/**
	 * Сохранение расчета в базе
	 *
	 * @param 	string    $title Название расчета
	 * @param 	string    $url Ссылка на расчет
	 * @return 	int 	  Возвращает id (aid) расчета в базе
	 */
	protected function saveAttache($title, $url)
	{
		$requset = [
			'name' 		=> $title,
			'url' 		=> $url,
			'pid'	=> $this->heatID,
			'module'		=> 'heat',
			'access'	=> '1',
		];
		$attach = Attach::create($requset);
		return $attach->id;
	}

	/**
	 * Сохранение расчитанного теплообменника в каталоге
	 *
	 * @param 	string    	$sid ID расчета
	 * @param 	string    	$aid ID файла расчета
	 * @param 	int|null    $i   Ступень, если есть
	 * @return 	int 	  	Возвращает id (cid) номенклатуры в базе
	 */
	public function createCatalog($sid, $aid, $i = 1)
	{
		$heatSolution = HeatSolution::with('heat')->find($sid);
		if (!empty($heatSolution)) {
			$uid = $heatSolution->heat->uid;
			$result = $heatSolution->result[$i];
			$unit = '796';
			$category = '451';
			$man = '146';
			$sku = $this->rNumb[$i];
			$active = '1';
			$active_guest = '0';
			$active_user = '1';
			$title = 'Аппарат теплообменный разборный ' . $result['PTOType'] . $result['UsePTOSize'];
			$short = 'Аппарат теплообменный пластинчатый разборный ' . $result['PTOType'] . $result['UsePTOSize'] . ' - это функциональное и уникальное в своем роде устройство, предназначенное для обмена жидкостями и газами в системах теплоснабжения.';
			$pto = Pto::where('ptoTags', 'like', '%"' . $result['UsePTOSize'] . '"%')->get();
			$preview = '/files/cats/Ridan.jpg';
			if (isset($pto[0])) {
				$preview = (!empty($pto[0]['img_3d'])) ? $pto[0]['img_3d'] : $pto[0]['img_2d'];
			}
			$attach = Attach::find($aid);
			$attach_id = [];
			if (!empty($attach)) {
				$attach_id[] = $attach->id;
			}
			$specTitle = 'Аппарат теплообменный разборный';
			$specTitle .= ' ' . $heatSolution->heat->heatData['ptoTypeUse'];
			if ($result['SolutionType'] == "GVS2MONO") {
				$specTitle .= ' моноблок';
				$result2 = $heatSolution->result[$i + 1];
				$q1 = $this->UtilitiesTool->prepareNumeric($result['q']['value']) / 1000000;
				$q2 = $this->UtilitiesTool->prepareNumeric($result2['q']['value']) / 1000000;
				$q =  number_format(($q1 + $q2), 4, ',', ' ');
				$s1 = $this->UtilitiesTool->prepareNumeric($result['effectiveSurface']['value']);
				$s2 = $this->UtilitiesTool->prepareNumeric($result2['effectiveSurface']['value']);
				$s =  number_format(($s1 + $s2), 2, ',', ' ');
				$n1 = $result['platesNum']['value'];
				$n2 = $result2['platesNum']['value'];
				$n = $n1 + $n2;

				// $q = number_format($this->UtilitiesTool->prepareNumeric($result['q']['value']) / 1000000, 4, ',', ' ');
				// $s = number_format($this->UtilitiesTool->prepareNumeric($result['effectiveSurface']['value']), 2, ',', ' ');
				// $n = $result['platesNum']['value'];
			} elseif ($result['SolutionType'] == "GVS2SEPARATED") {
				$specTitle .= ' ' . ($i + 1) . ' ст.';
				$q = number_format($this->UtilitiesTool->prepareNumeric($result['q']['value']) / 1000000, 4, ',', ' ');
				$s = number_format($this->UtilitiesTool->prepareNumeric($result['effectiveSurface']['value']), 2, ',', ' ');
				$n = $result['platesNum']['value'];
			} else {
				$q = number_format($this->UtilitiesTool->prepareNumeric($result['q']['value']) / 1000000, 4, ',', ' ');
				$s = number_format($this->UtilitiesTool->prepareNumeric($result['effectiveSurface']['value']), 2, ',', ' ');
				$n = $result['platesNum']['value'];
			}
			$specTitle .= ' Ду ' . $result['dn']['value'] . ' ' . $result['dn']['measure'] . ';';
			$specTitle .= ' Q=' . $q . ' Гкал/ч;';
			$specTitle .= ' S=' . $s . ' ' . $result['effectiveSurface']['measure'] . ';';
			$specTitle .= ' n=' . $n . ' ' . $result['platesNum']['measure'] . ';';
			$specTitle .= ' Ру ' . number_format($this->UtilitiesTool->prepareNumeric($result['p']['value']) / 10, 2, ',', ' ') . ' МПа;';
			$specTitle .= ' Tmax=' . $result['t']['value'] . ' ' . $result['t']['measure'] . ';';

			$specMark = $result['PTOType'] . $result['UsePTOSize'];
			$specArray = ["1" => $specTitle, "2" => $specMark, "3" => ""];
			$weight = $result['netWeight']['value'];
			$length = $result['frameLength']['value'];
			$width 	= $result['ptoWidth']['value'];
			$height = $result['ptoHeight']['value'];
			$wS = ($length * $height * $width);
			$wP = ($length * $width);
			$weightArray = ["1" => $weight, "2" => $length, "3" => $wS, "4" => $wP, "5" => "1"];

			$request = [
				'uid' => $uid,
				'sku' => $sku,
				'unit' => $unit,
				'title' => $title,
				'fulltitle' => $title,
				'short' => $short,
				'category' => $category,
				'man' => $man,
				'active' => $active,
				'active_guest' => $active_guest,
				'active_user' => $active_user,
				'preview' => $preview,
				'attach_id' => $attach_id,
				'spec' => $specArray,
				'weight' => $weightArray,
			];

			$catalog = Catalog::create($request);

			$nds_proc = \Price::getNds("3");

			$price = ($result['price']['value']) / 100 * ($nds_proc + 100);
			$price = round($price, 2);

			$this->setCatalogPrice(10, $catalog->id, $price, $uid);

			$price = ($result['price']['value'] * 0.5) / 100 * ($nds_proc + 100);
			$price = round($price, 2);

			$this->setCatalogPrice(15, $catalog->id, $price, $uid);

			return $catalog->id;
		}
	}

	/**
	 * Добавление прайс-листа
	 *
	 * @param 	int    $price_type Тип прайс-листа 1-закуп, 2 евро, 3 прайс
	 * @param 	int    $cid ID (cid) номенклатуры
	 * @param 	float  $sum Прайс-лист
	 * @param 	int    $uid ID отвественнго
	 * @return 	int    Возвращает true? если прайс лист создан успешно, иначен false
	 */
	protected function setCatalogPrice($price_type, $cid, $sum, $uid)
	{
		$price = CatalogPrice::create([
			'price_type' => $price_type,
			'uid' => $uid,
			'module' => 'heat',
			'pid' => $this->heatID,
			'active' => 1,
		]);
		if (!empty($price)) {
			CatalogPriceDetail::create([
				'price_id' 	=> $price->id,
				'cid' 		=> $cid,
				'sum' 		=> $sum,
			]);
			CatalogPriceDaemon::key_create();
			return true;
		}
		return false;
	}


	public function sendNotif()
	{
		$designers = getDesignersRequest();
		$attr = [];
		$attr['uid'] = $designers;
		$attr['title'] = 'Новая заявка на расчет теплообменника';
		$attr['text'] = "Новая заявка на расчет теплообменника";
		$attr['icon'] = config('app.url') . '/files/push/tasks_ok.png';
		$attr['url'] = config('app.url') . '/administration/module/request/view/' . $this->requestID;
		$attr['pNotif'] = true;
		$attr['pPush'] = true;
		sendAllNotif($attr);
	}
}
