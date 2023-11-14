<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Valve Calculation Task
*/

namespace Project\Tasks\Calculation\Pump;

use Price;
use Boot\Support\Numb;
use Boot\Abstracts\Tasks\Task;
use Boot\Support\Facades\Config;
use Illuminate\Database\Eloquent\Builder;

class PumpCalculationTask extends Task
{

	public $pump;
	public $catalog;
	/**
	 * Обработчик задачи
	 *
	 * @param  mixed $request
	 * @return array
	 */
	public function run($request): array
	{
		$this->requestData = $request;

		$this->pump = $request['cid'] ?? null;
		$this->setSearchType($this->requestData['pumpSelector']);
		$result = [
			'result' => $this->collectPump(false),
			'calc' => $this->calcResult,
			'catalog' => $this->catalog
		];
		return $result;
	}

	/**
	 * Тип поиска: 0 по назначению 1 по модели
	 *
	 * @var int
	 */
	protected $searchType;

	/**
	 * Входные данные расчета
	 *
	 * @var array
	 */
	protected $requestData;

	protected $calcResult;

	/**
	 * Установка типа поиска
	 *
	 * @param  mixed $pumpSelector
	 * @return void
	 */
	public function setSearchType($value)
	{
		$this->searchType = $value;
	}

	/**
	 * Формирование параметров для запроса в каталог
	 *
	 * @return array
	 */
	public function getSearchParams(): array
	{
		$parameter = [];
		$parameter['category'] = Config::get('project-pump.category');
		if ($this->searchType == '0') {
			$parameter['attribute'] = [
				Config::get('project-pump.attributes.model') => (empty($this->requestData['pumpSeries']) ? '0' : $this->requestData['pumpSeries']),
				Config::get('project-pump.attributes.typeBinary') => $this->requestData['typeBinary'],
			];
			if (!empty($this->requestData['voltage'])) {
				$parameter['attribute'][Config::get('project-pump.attributes.voltage')] = $this->requestData['voltage'];
			}
		} elseif ($this->searchType == '1') {
			if (!empty($this->requestData['model'])) {
				$parameter['attribute'][Config::get('project-pump.attributes.model')] = $this->requestData['model'];
			}
		} else {
			$parameter['search_id'] = $this->requestData['pump_search_id'];
		}
		$parameter['man'] = $this->requestData['manufacture'];
		$parameter['hidden'] = 0;
		if ($this->requestData['usePumpStation']) {
			$parameter['attribute'][243] = 'Да';
		}
		return $parameter;
	}

	/**
	 * Проверка насоса по давлению
	 *
	 * @param  array $attribute
	 * @return bool
	 */
	protected function checkPumpPressure($attribute): bool
	{
		if (isset($attribute[Config::get('project-pump.attributes.pressure')])) {
			$p_before = Numb::prepareNumeric($this->requestData['p_before']) ?? 0;
			$p_pump = Numb::prepareNumeric(explode(' ', $attribute[Config::get('project-pump.attributes.pressure')])[0]) * 10;
			// dd($p_before + $this->requestData['h']);
			if ($p_before + Numb::prepareNumeric($this->requestData['h']) <= $p_pump) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Подбор насосов
	 *
	 * @param  bool Необходимость лучшего насоса по стоимости, если true то подберет 
	 *              лучший насос по стоимостеи иначе выведет все подходящие
	 * @return array
	 */
	public function collectPump($selectOne = true)
	{
		$result = [];
		$parameter = [];
		if (empty($this->pump)) {
			$parameter = $this->getSearchParams();
		} else {
			$parameter['id'] = $this->pump;
		}
		// dd($parameter['attribute']);
		// $this->catalog = Config::get('project-pump.models.catalog')::getCatalog($parameter);
		$catalog = Config::get('project-pump.models.catalog')::where('active', 1);

		if (empty($this->pump)) {
			if (isset($parameter['attribute'])) {
				$catalog->withAttributeSave($parameter['attribute']);
			}
			// dd( $catalog->count());
			if (isset($parameter["man"]) && !empty($parameter["man"])) {
				if (is_array($parameter["man"])) {
					$catalog->whereIn("man", $parameter['man']);
				} else {
					$catalog->where("man", $parameter['man']);
				}
			}
			// var_dump($parameter["search_id"]);
			if (isset($parameter["search_id"]) && !empty($parameter["search_id"])) {
				if (is_array($parameter["search_id"])) {
					$catalog->whereIn("id", $parameter['search_id']);
				} else {
					$catalog->where("id", $parameter['search_id']);
				}
			}


			if (isset($parameter["category"])) $catalog->where("category", "LIKE", "%,{$parameter['category']},%");

			// dd( $this->requestData['h']);
			$catalog->whereHas('pump', function (Builder $query) {
				if ($this->requestData['usePumpStation']) {
					$query->where('max_y', '>=',  $this->requestData['h'] + 2);
				} else {
					$query->where('max_y', '>=',  $this->requestData['h']);
				}
				$query->where('max_x', '>=',  $this->requestData['q']);
				$query->where('min_x', '<=',  $this->requestData['q']);
				$query->where('type', 'QH');
			});
		} else {
			$catalog->where('id', $this->pump);
		}
		$this->catalog = $catalog->get();

		if (!empty($this->catalog)) {
			$cidArray = [];
			foreach ($this->catalog as $value) {
				if ($this->checkPumpPressure($value['attribute']) && $this->checkPump($value['id'])) {
					$cidArray[] = $value['id'];
				}
			}
			if (empty($cidArray)) {
				foreach ($this->catalog as $value) {
					if ($this->checkPumpPressure($value['attribute']) && $this->checkPump($value['id'], ['min' => '0', 'max' => '0.2'])) {
						$cidArray[] = $value['id'];
					}
				}
			}

			if ($selectOne) {
				$minPrice = false;
				$xPumpCid = 0;
				if (!empty($cidArray)) {
					foreach ($cidArray as $cid) {
						$price = Price::getPrice($cid);
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
		}
		return $result;
	}

	public function fastCheck()
	{
	}

	public function superMath($x1, $x2, $y1, $y2)
	{
		$a = $this->requestData['h'] / ($this->requestData['q'] * $this->requestData['q']);
		$k = ($y1 - $y2) / ($x1 - $x2);
		$b = $y2 - $k * $x2;
		$aa = $a;
		$bb = -$k;
		$cc = -$b;
		//дискриминант
		$d = ($bb * $bb) - (4 * $aa * $cc);
		$superX = (-$bb + sqrt($d)) / (2 * $aa);
		$superY = $k * $superX + $b;
		return [
			'superX' => $superX,
			'superY' => $superY
		];
	}

	/**
	 * Проверка насоса по его кривой
	 *
	 * @param  int $cid
	 * @param  array $restrictions
	 * @return void
	 */
	public function checkPump($cid, $restrictions = ['min' => '0.2', 'max' => '0.9'])
	{
		$query = Config::get('project-pump.models.pump')::where("cid", $cid)->where("type", 'QH')->orderBy("type_of_instalation")->orderBy("work_line")->get()->toArray();
		$data = [];

		foreach ($query as $p) {
			$nd = [];
			foreach ($p['data'] as $dd) {
				if ($this->requestData['usePumpStation']) {
					$prepare = (array) $dd;
					// dd($prepare );
					if (Numb::prepareNumeric($prepare['H']) - 2 >= 0) {
						$nd[] = [
							'Q' => $prepare['Q'],
							'H' => Numb::prepareNumeric($prepare['H']) - 2,
						];
					}
				} else {
					$nd[]  = (array) $dd;
				}
			}
			$data[] = [
				"data" => $nd,
				"max_y" => $this->requestData['usePumpStation'] ? Numb::prepareNumeric($p['max_y']) - 2 :  $p['max_y'],
				"max_x" => $p['max_x'],
				"min_y" => $this->requestData['usePumpStation'] ? ((Numb::prepareNumeric($p['min_y']) - 2 >= 0) ? Numb::prepareNumeric($p['min_y']) - 2 : 0) : $p['min_y'],
				"min_x" => $p['min_x'],
				"id" => $p['id'],
			];
		}
		$check_distance = 10000;

		// var_dump($data);
		$flag = false;
		if (isset($data) && !empty($data) && $this->requestData['h'] != 0) {

			foreach ($data as $key_line => $line) {
				if (isset($line['data']) && !empty($line['data'])) {
					$xMax = floatval(Numb::commasReplace($line['data'][count($line['data']) - 1]['Q']));
					//быстрая проверка

					if ($this->requestData['pump_wrong'] == 1) {

						$superMathFast = $this->superMath($line['min_x'], $line['max_x'], $line['min_y'], $line['max_y']);
						$superXFast = $superMathFast['superX'];
						$superYFast = $superMathFast['superY'];
						// dump($superMathFast, $line);
						if (!($superXFast >= $line['min_x'] && $superXFast <= $line['max_x'] && $superYFast <= $line['max_y'] && $superYFast >= $line['min_y'])) {
							return false;
						}
					}


					//полная проверка
					foreach ($line['data'] as $key => $value) {
						if (isset($line['data'][$key + 1])) {
							//получаем уравнение прямой по двум точкам из отрезка графика насоса
							//Y=kX+b;

							//h-y q-x
							$x1 = floatval(Numb::commasReplace($value['Q']));
							$y1 = floatval(Numb::commasReplace($value['H']));
							$x2 = floatval(Numb::commasReplace($line['data'][$key + 1]['Q']));
							$y2 = floatval(Numb::commasReplace($line['data'][$key + 1]['H']));
							if ($x1 - $x2 == 0) {
								continue;
							}
							$superMath = $this->superMath($x1, $x2, $y1, $y2);
							$superX = $superMath['superX'];
							$superY = $superMath['superY'];

							$distance = sqrt(pow($superX - $this->requestData['q'], 2) + pow($superY - $this->requestData['h'], 2));

							// if($cid=='6856'){
							// 	dd('Y='.$aa.'X2+'.$bb.'x+'.$cc);
							// }
							// dump($cid.': superX:'.$superX.' superY:'.$superY);
							if ($this->requestData['pump_wrong'] == 1) {
								//проверка пересечения линейных графиков и параболы
								if ($superX >= $x1 && $superX <= $x2 && $superY <= $y1 && $superY >= $y2) {
									//проверка точки по критериям
									if (($superX > $this->requestData['q']) && ($superY > $this->requestData['h'] * $this->requestData['coefficient']) && ($restrictions['min'] * $xMax < $superX) && ($superX < $restrictions['max'] * $xMax) && ($superY / $this->requestData['h']) < 2.5) {
										//проверка на ближайшую рабочею линию
										if ($distance < $check_distance) {
											$check_distance = $distance;
											$priority = $key_line;
											$flag = true;
											$this->calcResult[$cid]    = [
												'superX' => $superX,
												'superY' => $superY,
												'superLine' => $line['id'],
												'priority' => $priority,
											];
										}
									}
								}
							} else {
								if ($distance < $check_distance) {
									$check_distance = $distance;
									$priority = $key_line;
									$flag = true;
								}
								$this->calcResult[$cid]    = [
									'superX' => $superX,
									'superY' => $superY,
									'superLine' => $line['id'],
									'priority' => $priority,
								];
							}
						}
					}
				}
			}
		}
		return $flag;
	}
}
