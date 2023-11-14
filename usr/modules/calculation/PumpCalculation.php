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

use \Boot\Models\Attach;
use Amenadiel\JpGraph\Plot;
use Amenadiel\JpGraph\Util;
use Amenadiel\JpGraph\Graph;
use mikehaertl\wkhtmlto\Pdf;
use \Boot\System\Database\DB;
use Boot\Support\Facades\Auth;
use Boot\Support\Facades\Config;
use PhpOffice\PhpWord\Element\Field;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use \Calculation\Utilities\UtilitiesTool as UtilitiesTool;

class PumpCalculation
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
	public $searchType = 0;

	//конструктор класса
	function __construct()
	{
		//подключаем класс с инструментами для работы
		$this->ut = new UtilitiesTool();
	}

	/**
	 * Стандартизация данных по вольтажу насоса
	 *
	 * @param 	string    	$u Данные по вольтажу
	 * @return 	string   	Возвращает стандартизированный вариант вольтажа (по умолчанию 1~ 230 V / 50 Hz)
	 */
	public function getVoltage($u)
	{
		switch ($u) {
			default:
			case "0":
			case "1~ 230 V / 50 Hz":
				$result = '1~ 230 V / 50 Hz';
				break;
			case "1":
			case "3~ 400 V / 50 Hz":
				$result = '3~ 400 V / 50 Hz';
				break;
		}
		return $result;
	}

	/**
	 * Получение марок насоса по назначению и наличию частотного преобразователя
	 *
	 * @param 	string   $pumpUse Назначение насоса
	 * @param 	bool     $frequency наличие частотного преобразователя 
	 * @return 	array    Возвращает найденную модель насоса, если нечего не найдено вернет false
	 */
	public function getPumpSeries($pumpUse, $frequency)
	{
		switch ($pumpUse) {
			default:
			case "0":
				$result[1] = ['IPL', 'DPL', 'TOP-S', 'TOP-SD', 'STAR-RS', 'IL', 'DL', 'BL'];
				$result[0] = ['YONOS MAXO', 'YONOS MAXO-D', 'YONOS PICO', 'YONOS PICO-D', 'STRATOS PICO', 'STRATOS PICO-D', 'STRATOS MAXO', 'STRATOS MAXO-D'];
				break;
			case "1":
				$result[1] = ['STAR-Z'];
				$result[0] = ['YONOS MAXO-Z', 'STRATOS PICO-Z', 'STRATOS MAXO-Z'];
				break;
			case "2":
				$result[1] = ['MHI', 'MHIL', 'MEDANA CH1-L', 'MEDANA CH1-LC'];
				$result[0] = [];
				break;
			case "3":
				$result[1] = ['MHI', 'MHIL', 'Helix V', 'MEDANA CH1-L', 'MEDANA CH1-LC', 'MEDANA CV1-L', 'MVI'];
				$result[0] = [];
				break;
			case "4":
				$result[1] = ['BL'];
				$result[0] = [];
				break;
		}
		return $result[$frequency];
	}

	public function setSearchType()
	{
		$this->searchType = $this->requestData['pumpSelector'];
	}

	public function getPumpModel($key)
	{
		$modelList = ["BL",	"DL", "DPL", "Helix V", "IL", "IPL", "MEDANA CH1-L", "MEDANA CH1-LC", "MEDANA CV1-L", "MHI", "MHIL", "MVI", "STAR-RS", "STAR-Z", "STRATOS MAXO", "STRATOS MAXO-D", "STRATOS MAXO-Z", "STRATOS PICO", "STRATOS PICO-D", "STRATOS PICO-Z", "TOP-S", "TOP-SD", "YONOS MAXO", "YONOS MAXO-D", "YONOS MAXO-Z", "YONOS PICO", "YONOS PICO-D"];
		if (isset($modelList[$key])) {
			return $modelList[$key];
		}
	}

	public function getSearchParams()
	{
		// print_r($this->searchType);
		$parameter = [];
		if ($this->searchType == '0') {
			// print_r($this->searchType);
			$parameter['category'] = '363';
			$parameter['attribute'] = array(
				'127' => (empty($this->requestData['pumpSeries']) ? '0' : $this->requestData['pumpSeries']),
				'133' => $this->requestData['u'],
				'138' => $this->requestData['typeBinary'],
			);
		} else {
			$parameter['category'] = '363';
			$parameter['attribute'] = array(
				'127' => $this->requestData['model'],
			);
		}
		return $parameter;
	}

	/**
	 * Стандартизация данных по вольтажу насоса
	 *
	 * @param 	string    	$u Данные по вольтажу
	 * @return 	string   	Возвращает стандартизированный вариант вольтажа (по умолчанию 1~ 230 V / 50 Hz)
	 */
	public function getPumpUse($pumpUse)
	{
		switch ($pumpUse) {
			default:
			case "0":
			case "Отопление":
				$result = '0';
				break;
			case "1":
			case "Циркуляция ГВС":
				$result = '1';
				break;
			case "2":
			case "Подпитка отопления":
				$result = '2';
				break;
			case "3":
			case "Повысительный ХВС / ГВС":
				$result = '4';
				break;
			case "4":
			case "Пожарные насосы":
				$result = '4';
				break;
		}
		return $result;
	}



	/**
	 * Стандартизация данных по вольтажу насоса
	 *
	 * @param 	string    	$u Данные по вольтажу
	 * @return 	string   	Возвращает стандартизированный вариант вольтажа (по умолчанию 1~ 230 V / 50 Hz)
	 */
	public function getBinaryType($value)
	{
		switch ($value) {
			default:
			case "0":
			case "Одинарный":
				$result = 'Одинарный';
				break;
			case "1":
			case "Сдвоенный":
				$result = 'Сдвоенный';
				break;
		}
		return $result;
	}

	/**
	 * Сбор и стандартизация данных
	 *
	 * @param 	array    $request входные данные
	 */
	public function setData($request)
	{
		$pumpUse =  $this->getPumpUse($request['pumpUse']);
		$frequency = $request['frequency'];
		if (empty($request['pumpSeries'])) {
			$request['pumpSeries'] = $this->getPumpSeries($pumpUse, $frequency);
		}
		$this->requestData = array(
			'pumpSeries' => $request['pumpSeries'],
			'h' => $this->ut->prepareNumeric($request['h']),
			'q' => $this->ut->prepareNumeric($request['q']),
			'u' => $this->getVoltage($request['u']),
			'p' => isset($request['p']) ? $request['p'] : array('10 bar', '16 bar'),
			'coefficient' => isset($request['coefficient']) ? $this->ut->prepareNumeric($request['coefficient']) : '1.05',
			'oid' => isset($request['t_oid']) ? intval($request['t_oid']) : '',
			'object' => isset($request['object_id']) ? intval($request['object_id']) : '',
			'manufacture' => isset($request['manufacture']) ? intval($request['manufacture']) : '',
			'model' => isset($request['model']) ? $this->getPumpModel($request['model']) : '',
			'typeBinary' => isset($request['soloOrBinary']) ? $this->getBinaryType($request['soloOrBinary']) : '',
			'pumpSelector' => isset($request['pumpSelector']) ? intval($request['pumpSelector']) : '0',
		);
	}

	//подбор насосов
	public function collectPump($selectOne = true)
	{
		//ищем заданные насосы.
		$parametr = array();
		if (empty($this->pump)) {

			$parametr = $this->getSearchParams();
			// print_r($parametr);
		} else {
			$parametr['id'] = $this->pump;
		}
		$r =  \Catalog_v2\Model\Catalog::getCatalog($parametr);

		$this->collectPumpData = $r;
		//получем список насосов прошедших проверку		
		$cidArray = array();
		foreach ($r as $key => $value) {
			// dump($value);
			$attribute = $value['attribute'];
			// print_r($attribute['132']);	
			// exit;
			if (isset($attribute['132'])) {
				if (in_array($attribute['132'], $this->requestData['p'])) {
					// dump($value['id']);
					if ($this->checkPump($value['id'])) {
						$cidArray[] = $value['id'];
					}
				}
			}
		}
		// print_r($cidArray);	
		// exit;
		if (empty($cidArray)) {
			foreach ($r as $key => $value) {
				if (isset($attribute['132'])) {
					if (in_array($attribute['132'], $this->requestData['p'])) {
						if ($this->checkPump($value['id'], array('min' => '0', 'max' => '0.2'))) {
							$cidArray[] = $value['id'];
						}
					}
				}
			}
		}
		// print_r($cidArray);	
		if ($selectOne) {
			//выбираем лучший насос по стоимости
			$minPrice = false;
			$xPumpCid = 0;
			if (!empty($cidArray)) {
				foreach ($cidArray as $cid) {
					$price = \Price::getPrice($cid);
					$price_list = empty($price->price_list) ? 0 : $price->price_list;
					if (($minPrice == false) || ($minPrice > $price_list)) {
						$minPrice = $price_list;
						$xPumpCid = $cid;
					}
				}
			}
			$result = array(
				'cid' => $xPumpCid,
			);
		} else {
			$result = $cidArray;
		}
		$this->result = $result;
		return $result;
	}


	public function getResult()
	{
		global $db;
		$result = [];
		$entities = [];
		if (!empty($this->result)) {

			foreach ($this->collectPumpData as $key => $value) {
				if (in_array($value['id'], $this->result)) {
					$attribute = $value['attribute'];
					$chart = [];
					$xArray = [];

					// $xArray[] = $this->requestData['q'];
					// $xArray[] = $this->calcResult[$value['id']]['superX'];
					$query = \Catalog_v2\Model\Pump::where("cid", $value['id'])->orderBy("type_of_instalation")->orderBy("work_line")->get();
					foreach($query as $p){
						$d = $p->data;
						$chart_data = [];
						$max_h = 0;
						$max_q = 0;
						foreach ($d as $k => $v) {
							if ($this->ut->prepareNumeric($v->H) > $max_h) {
								$max_h = $this->ut->prepareNumeric($v->H);
							}
							if ($this->ut->prepareNumeric($v->Q) > $max_q) {
								$max_q = $this->ut->prepareNumeric($v->Q);
							}
							$chart_data[] = ['x' => $this->ut->prepareNumeric($v->Q), 'y' => $this->ut->prepareNumeric($v->H)];
							// $xArray[] = $this->ut->prepareNumeric($v['Q']);
						}

						// print_r($d);
						$chart[] = $chart_data;
					}
					$calcChart = [];

					for ($i = 0; $i <= $max_h + 2; $i += 0.3) {
						$p = $this->requestData['h'] / ($this->requestData['q'] * $this->requestData['q']);
						$x = sqrt($i / $p);
						$calcChart[] = ['x' => $x, 'y' => $i];
					}
					$calcI = 1;
					if ($max_q > 15) {
						$calcI = round($max_q / 15);
						// print_r($calcI.' ');
						// exit;
					}

					// y= (7/4.5^2)*x^2
					for ($i = 0; $i <= $max_q + $calcI; $i += $calcI) {
						$xArray[] = $i;
					}
					// $xArray = [0,1,2,3,4,5,6,7,8,10];
					sort($xArray, SORT_NUMERIC);
					$xArray = array_unique($xArray);
					$xx = [];
					foreach ($xArray as $kk => $vv) {
						$xx[] = $vv;
					}
					$attributeComplete = [];
					foreach ($attribute as $k => $v) {
						$atrr = \Catalog_v2\Model\CatalogAtribute::find($k);
						$attributeComplete[] = [
							'id' => $atrr->id,
							'name' => $atrr->title,
							'value' =>  $v,
						];
					}
					$spec = $value['spec'];
					$weight = $value['weight'];

					$price = \Price::getPrice($value['id']);
					$price_list = empty($price->price_list) ? 0 : $price->price_list;

					$entities[] = [
						'cid' => $value['id'],
						'sku' => $value['sku'],
						'title' => $value['title'],
						'preview' => Config::get('app.url').$value['preview'],
						'description' => $value['short'],
						'price' => $price_list,
						'attribute' => $attributeComplete,
						'spec' => $spec,
						'weight' => $weight,
						'charts' => $chart,
						'man' => 'Wilo',
						'point' => ['x' => $this->requestData['q'], 'y' => $this->requestData['h']],
						'calcPoint' => ['x' => $this->calcResult[$value['id']]['superX'], 'y' => $this->calcResult[$value['id']]['superY']],
						'cross' => ['x' => $this->calcResult[$value['id']]['superX'], 'y' => $this->calcResult[$value['id']]['superY']],
						'calcChart' => $calcChart,
						'labelX' => $xx,
						'superChart' => $this->calcResult[$value['id']]['superLine'],
					];
				}
			}
			// print_r($price);
			$result['entities'] = $entities;
			$result['type'] = 'success';
		} else {
			$result['entities'] = $entities;
			$result['type'] = 'empty';
		}
		return  $result;
	}

	//проверка насоса по графику
	public function checkPump($cid, $restrictions = array('min' => '0.2', 'max' => '0.7'))
	{
		$query = \Catalog_v2\Model\Pump::where("cid", $cid)->where("priority", 1)->orderBy("type_of_instalation")->orderBy("work_line")->get()->toArray();
		$data = [];

		foreach($query as $p){
			$nd = [];
			foreach($p['data'] as $dd){
				$nd[] = (array) $dd;
			}
			$data[] = [
				"data" => $nd,
				"id" => $p['id'],
			];
		}
		// var_dump($data);
		$flag = false;
		if (isset($data) && !empty($data) && $this->requestData['h'] != 0) {

			$ccccc = 0;
			if (isset($data[$ccccc]['data']) && !empty($data[$ccccc]['data'])) {
				$xMax = floatval($this->ut->commasReplace($data[$ccccc]['data'][count($data[$ccccc]['data']) - 1]['Q']));
				$a = $this->requestData['h'] / ($this->requestData['q'] * $this->requestData['q']);
				foreach ($data[$ccccc]['data'] as $key => $value) {
					if (isset($data[$ccccc]['data'][$key + 1])) {
						//получаем уравнение прямой по двум точкам из отрезка графика насоса
						//Y=kX+b;

						//h-y q-x
						$x1 = floatval($this->ut->commasReplace($value['Q']));
						$y1 = floatval($this->ut->commasReplace($value['H']));
						$x2 = floatval($this->ut->commasReplace($data[$ccccc]['data'][$key + 1]['Q']));
						$y2 = floatval($this->ut->commasReplace($data[$ccccc]['data'][$key + 1]['H']));
						$k = ($y1 - $y2) / ($x1 - $x2);
						$b = $y2 - $k * $x2;
						// if($cid=='6856'){
						// 	dump('Y='.$k.'X+'.$b);
						// }

						$aa = $a;
						$bb = -$k;
						$cc = -$b;
						//дискриминант
						$d = ($bb * $bb) - (4 * $aa * $cc);
						$superX = (-$bb + sqrt($d)) / (2 * $aa);
						$superY = $k * $superX + $b;
						// if($cid=='6856'){
						// 	dd('Y='.$aa.'X2+'.$bb.'x+'.$cc);
						// }
						// dump($cid.': superX:'.$superX.' superY:'.$superY);
						//проверка пересечения линейных графиков и параболы
						if ($superX >= $x1 && $superX <= $x2 && $superY <= $y1 && $superY >= $y2) {
							//проверка точки по критериям
							if (($superX > $this->requestData['q'] ) && ($superY > $this->requestData['h'] * $this->requestData['coefficient']) && ($restrictions['min'] * $xMax < $superX) && ($superX < $restrictions['max'] * $xMax)) {
								// if(true) {
								$flag = true;
								$this->calcResult[$cid]	= [
									'superX' => $superX,
									'superY' => $superY,
									'superLine' => $data[$ccccc]['id'],
								];
							}
						}
					}
				}
			}
		}
		return $flag;
	}


	public function setPump($cid)
	{
		$this->pump = intval($cid);
		$this->requestData['pump'] = $this->pump;
	}

	public function createRequestAfter($id = null)
	{
		global $core;

		$title = '';
		$request = array();
		$request['uid_dir'] = Auth::id();
		$request['title'] = $title;
		$request['title_user'] = $title;
		$request['request'] = '';
		if (!empty($this->requestData['oid'])) {
			$request['oid'] = $this->requestData['oid'];
		}
		if (!empty($this->requestData['object'])) {
			$request['object'] = $this->requestData['object'];
		}
		$request['request_data'] = $this->requestData;
		$request['type'] = '5';
		$request['date'] = date("Y-m-d H:i:s");
		$request['status'] = '3';
		$result = \Request\Model\Request::create($request);
		$this->requestId = $result->id;
		$newTitle = 'Расчет насосного оборудования №' . $this->requestId;
		\Request\Model\Request::find($result->id)->update(['title' => $newTitle, 'title_user' => $newTitle]);
		return $this->requestId;
	}

	public function createCalculationAttach($id = null)
	{
		$model = \Request\Model\Request::class;
		$data = $model::select(array('request.*', 'organizations.name as oid_name', 'object.name as object_name'));
		$data->leftJoin('organizations', 'request.oid', '=', 'organizations.id');
		$data->leftJoin('object', 'request.object', '=', 'object.id');
		$data->where('request.pid', '=', '352');
		$data->where('request.type', '=', '2');
		$requestData = $data->get()->toArray()[0];
		$tpl = ROOT . '/files/tpl/calculation/pump.docx';
		$document =  new \PhpOffice\PhpWord\TemplateProcessor($tpl);

		$pumpData = $this->collectPumpData[0];
		$spec = $pumpData['spec'];
		$attribute = $pumpData['attribute'];
		$weight = $pumpData['weight'];
		$title = $spec[1] . ' ' . $spec[2];

		$query = \Catalog_v2\Model\Pump::where("cid", $this->pump)->orderBy("type_of_instalation")->orderBy("work_line")->get();

		
		foreach($query as $p){
			$nd = [];
			foreach($p['data'] as $dd){
				$nd[] = (array) $dd;
			}
			$d = $nd;
			
			$chart_data = [];
			$max_h = 0;
			$max_q = 0;
			foreach ($d as $k => $v) {
				if ($this->ut->prepareNumeric($v['H']) > $max_h) {
					$max_h = $this->ut->prepareNumeric($v['H']);
				}
				if ($this->ut->prepareNumeric($v['Q']) > $max_q) {
					$max_q = $this->ut->prepareNumeric($v['Q']);
				}
				// $chart_data[] = ['x' => $this->ut->prepareNumeric($v['Q']), 'y' => $this->ut->prepareNumeric($v['H'])];
				$chart_data['x'][] = $this->ut->prepareNumeric($v['Q']);
				$chart_data['y'][] = $this->ut->prepareNumeric($v['H']);
			}
			$chart_data['key'] = $p['id'];
			$chart_data['type'] = 'chart';
			$chart[] = $chart_data;
		}
		$chart_data = [];
		for ($i = 0; $i <= $max_h + 2; $i += 0.3) {
			$p = $this->requestData['h'] / ($this->requestData['q'] * $this->requestData['q']);
			$x = sqrt($i / $p);
			$chart_data['x'][] = $x;
			$chart_data['y'][] = $i;
		}
		$chart_data['key'] = 0;
		$chart_data['type'] = 'calc';
		$chart[] = $chart_data;

		$__width  = 640;
		$__height = 480;
		$g  = new Graph\Graph($__width, $__height);
		$g->SetMargin(30, 20, 40, 30);
		$g->title->Set('Рабочее поле');
		$g->title->SetFont(FF_ARIAL, FS_NORMAL, 12);
		// $g->subtitle->Set('(Control points shown in red)');
		$g->subtitle->SetColor('darkred');
		$g->SetMarginColor('lightblue');
		// We need a linlin scale since we provide both
		// x and y coordinates for the data points.
		$g->SetScale('linlin');

		// We want 1 decimal for the X-label
		$g->xaxis->SetLabelFormat('%1.1f');
		foreach ($chart as $gk => $gv) {
			$splot = new Plot\ScatterPlot($gv['y'], $gv['x']);

			// $splot->value ->Show();  
			// $splot->value->SetFormat('(%0.0f)');  


			$spline = new Util\Spline($gv['x'], $gv['y']);
			list($newx, $newy) = $spline->Get(50);
			$lplot = new Plot\LinePlot($newy, $newx);
			if ($this->calcResult[$this->pump]['superLine'] == $gv['key']) {
				$lplot->SetColor('red');
				// $splot->mark->SetFillColor('green@0.3');
				// $splot->mark->SetColor('green@0.5');	
			} else {
				$lplot->SetColor('navy');
				$splot->mark->SetFillColor('green@0.3');
				// $splot->mark->SetColor('green@0.5');	
			}
			if ($gv['type'] == 'calc') {
				$lplot->SetColor('black');
			}
			$splot->mark->Hide();
			// print_r($gv['key']);
			$g->Add($lplot);
			$g->Add($splot);
		}
		// $g->InitFrame();
		// 		$shape = new Graph\Shape($g, $scale);
		// $shape->SetColor('black');

		// // Add a black line
		// $shape->SetColor('black');
		// $shape->Line(0, 0, 20, 20);

		$module = 'request';
		$dirClear = 'files/' . $module . '/' . $this->requestId;
		$dir = ROOT . $dirClear;
		if (!file_exists($dir)) {
			mkdir($dir, 0777);
		}
		$file_url_grpah = $dir . '/graph.png';
		// $g->Stroke();
		// exit;
		$g->Stroke($file_url_grpah);



		$document->setImageValue('graph', array('path' => $dir . '/graph.png', 'width' => 377, 'height' => 264, 'ratio' => false));



		$cert = DB::table('cert_relation')
			->leftJoin('cert', 'cert.id', '=', 'cert_relation.cert_id')
			->leftJoin('attach', 'cert.aid', '=', 'attach.id')
			->where('cert_relation.cid', $this->pump)
			->whereIn('cert.type', [12, 13])
			->get()
			->toArray();


		if (!empty($cert)) {
			foreach ($cert as $k => $v) {
				if ($v->type == 12) {
					$document->setImageValue('connect', array('path' => ROOT . $v->url, 'width' => 175, 'height' => 130, 'ratio' => false));
				} elseif (count($cert) == 1) {
					$document->setValue('connect', '');
				}
				if ($v->type == 13) {
					$document->setImageValue('schema', array('path' => ROOT . $v->url, 'width' => 300, 'height' => 180, 'ratio' => false));
				} elseif (count($cert) == 1) {
					$document->setValue('schema', '');
				}
			}
		}
		$document->setValue('h', $this->requestData['h']);
		$document->setValue('q', $this->requestData['q']);
		$document->setValue('liquid', 'Вода 100%');
		$document->setValue('liquid_t', '20.00');
		$document->setValue('liquid_r', '998.20');
		$document->setValue('liquid_k', '1.00');
		$document->setValue('calc_h', round($this->calcResult[$this->pump]['superY'], 2));
		$document->setValue('calc_q', round($this->calcResult[$this->pump]['superX'], 2));
		$document->setValue('title', $title);
		$document->setValue('sku', $pumpData['sku']);
		$document->setValue('weight', $weight[1]);
		$document->setImageValue('graph', array('path' => $dir . '/graph.png', 'width' => 377, 'height' => 264, 'ratio' => false));


		$file_name = '13232';

		$file_url = $dir . '/' . $file_name . '.docx';
		$file_url_pdf = $dir . '/' . $file_name . '.pdf';
		$document->saveAs($file_url);


		\Boot\Jobs\PdfJob::dispatchSync($file_url, $file_name . '.pdf');

		$requset = [
			'name' 		=> 'Расчет насосного оборудования №' . $this->requestId,
			'url' 		=> $dirClear . '/' . $file_name . '.pdf',
			'pid'	=> $this->requestId,
			'module'		=> 'request',
			'access'	=> '1',
		];
		$attach = Attach::create($requset);

		return $attach->id;
	}
}
