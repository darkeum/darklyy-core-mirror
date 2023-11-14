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

namespace Project\Tasks\Calculation\PumpStation;

use Boot\Support\Str;
use Boot\Support\Numb;
use Catalog\Models\Catalog;
use Boot\Abstracts\Tasks\Task;
use Boot\Support\Facades\Cache;
use Project\Traits\Calculation\PipeTrait;
use Project\Actions\Calculation\Pump\ApiPumpCalculationAction;
use Project\UI\API\Requests\Calculation\PumpCalculationRequest;

class PumpStationCalculationTask extends Task
{
	use PipeTrait;

	protected $request;
	protected $series_index = [
		1 => 'wp',
		2 => 'wpb',
		3 => 'wpbs',
	];

	/**
	 * Обработчик задачи
	 *
	 * @param  mixed $request
	 * @return array
	 */
	public function run($request): array
	{
		$pump_type = [];
		foreach (explode(',', $request['type_use']) as $type_use_v) {
			foreach (explode(',', $request['type']) as $type_v) {
				if ($type_use_v == 1) {
					if ($type_v == 1) {
						$pump_type[6] = 6;
					} elseif ($type_v == 2) {
						$pump_type[7] = 7;
					}
				}
			}
		}

		$pump_type_str = implode(',', $pump_type);

		// dd($request);
		$data = [
			'pumpSelector' => 0,
			'q',
			'h' => $request['h'],
			'oid',
			'object_id',
			'manufacture' => $request['manufacture'],
			'typeBinary' => 1,
			'type' => $pump_type_str,
			'usePumpStation' => 1,
			'frequency' => $request['frequency'],
			'voltage' => '0',
			'coefficient' => $request['coefficient'],
			'pump_switch' => 1,
			'pump_count' => $request['pump_count'],
			'pump_reserve' => $request['pump_reserve'],
			'pump_reserve_count' => $request['pump_reserve_count'],
			'viscosity' => $request['viscosity'],
			'density' => $request['density'],
			't' => $request['t'],
			'work_mark' =>  $request['work_mark'],
			'pump_wrong' => $request['pump_wrong'] ? 'true' : 'false',
			'q_all' => $request['q_all'],
			'p_before' => $request['p_before'],
		];

		if (!empty($request['cid'])) {
			$data['cid'] = $request['cid'];
		}
		// dd($data);
		//
		$this->request = $request;
		$request['pump_all_count'] = $data['pump_count'] + $data['pump_reserve_count'];

		//create request object with some data

		if (!empty($request['cid'])) {
			$request_pump = PumpCalculationRequest::injectData($data);
			$pumps_result = app(ApiPumpCalculationAction::class)->run($request_pump);
			$pumps = $pumps_result['data']['entities'];
		} else {
			$pumps = Cache::rememberForever('pump.station.pump.' . $request['h'] . '.' . $request['p_before'] . '.' . '.' . $request['q_all'] . '.' . implode('.', $request['manufacture']) . '.' . $pump_type_str . '.' . $data['pump_count'] . '.' . $data['pump_reserve_count'] . '.' . $data['pump_wrong'] . '.' . $data['coefficient'], function () use ($data) {
				$request_pump = PumpCalculationRequest::injectData($data);
				$pumps_result = app(ApiPumpCalculationAction::class)->run($request_pump);
				$pumps = $pumps_result['data']['entities'];
				return $pumps;
			});
		}







		$mmi = 0;
		$result = [];
		$series_array = $this->getSeries($request['series_calculate'] ?? null);


		foreach ($pumps as $value) {
			$series_result = [];
			foreach ($series_array as $series) {
				$this->wight = 0;
				$mmi++;
				$attribute = cc($value['attribute']);
				$dn_in = $attribute->where('id', '130')->first()['value'];
				$dn_out = $attribute->where('id', '131')->first()['value'];
				$connection = $attribute->where('id', '10')->first()['value'];
				$pump_type = $attribute->where('id', '358')->first()['value'];
				$pump_model = $attribute->where('id', '127')->first()['value'];

				$p_before = Numb::prepareNumeric($request['p_before']);
				$p_before_with_h = $p_before + Numb::prepareNumeric($request['h']);

				$i = 0;
				$station_specification = [];

				//Насосы
				$i++;
				$parameter = [];
				$parameter['id'] = $value['cid'];
				$station_specification[] = $this->structureSpecification($i, Catalog::getCatalog($parameter), $request['pump_all_count']);

				//Кран или затвор на впуск и на выпуск
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.tap.in.' . $dn_in, function () use ($dn_in) {
					return Catalog::getCatalog($this->getTap($dn_in));
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, $request['pump_all_count'] * 2);


				//кран под бак
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.tap.tank.' . $p_before_with_h, function () use ($p_before_with_h) {
					$parameter = [];
					$parameter['man'] = 6;
					$parameter['category'] = '557';
					$parameter['attribute'] = array(
						'4' => $p_before_with_h <= 95 ? '20' : '25',
						'195' => 'ручное (бабочка)',
						'244' => 'ВР/ВР',
						'291' => 'Aquasfera Euro'
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 1);


				//обратный клапан на выходе
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.valve.in.' . $dn_in, function () use ($dn_in) {
					$parameter = [];
					$parameter['man'] = ($dn_in <= 50) ? 66 : 12;
					$parameter['category'] = '289';
					$parameter['attribute'] = array(
						'4' => $dn_in,
						'10' => ($dn_in <= 50) ? 'Муфтовое' : 'Межфланцевое',
						'37' => ($dn_in <= 50) ? '130' : '120',
						'44' => ($dn_in <= 50) ? 'Латунь никелированная' : 'Чугун',
						'50' => ($dn_in <= 50) ? 'Пружинный' : 'Двухстворчатый',
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, $request['pump_all_count']);

				//бак
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.tank.' . $p_before_with_h, function () use ($p_before_with_h) {
					$parameter = [];
					$parameter['man'] = 29;
					$parameter['category'] = '475';
					$parameter['attribute'] = array(
						'40'  => $p_before_with_h <= 95 ? '10' : '16',
						'221' => 8,
						'250' => $p_before_with_h <= 95 ? '20' : '25',
						'255' => 'Наружная резьба'
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 1);

				//реле давления
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.pressure.switches', function () {
					$parameter = [];
					$parameter['man'] = 18;
					$parameter['category'] = '319';
					$parameter['attribute'] = array(
						'92' => 'От -0.7 до 6',
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 1);

				//датчик давления
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.pressure.meter.' . $p_before_with_h, function () use ($p_before_with_h) {
					$parameter = [];
					$parameter['man'] = 272;
					$parameter['category'] = '280';
					$parameter['attribute'] = array(
						'47' => '4-20 мА',
						'156' => 'G1/4',
						'178' => $p_before_with_h <= 80 ? '1.0' : ($p_before_with_h > 140 ? '2.5' : '1.6'),
					);
					return Catalog::getCatalog($parameter);
				});
				$count_d = $series == 3 ? 1 : 2;
				$station_specification[] = $this->structureSpecification($i, $catalog_result, $count_d);

				//манометр на выход
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.manometer.in' . $p_before_with_h, function () use ($p_before_with_h) {
					$parameter = [];
					$parameter['man'] = 18;
					$parameter['category'] = '297';
					$parameter['attribute'] = array(
						'154' => $p_before_with_h <= 80 ? '0-1.0' : ($p_before_with_h > 140 ? '0-2.5' : '0-1.6'),
						'156' => 'М20×1.5',
						'246' => 'Модель 2 (М2)',
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 1);

				//манометр на вход
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.manometer.out.' . $p_before, function () use ($p_before) {
					$parameter = [];
					$parameter['man'] = 18;
					$parameter['category'] = '297';
					$parameter['attribute'] = array(
						'154' => $p_before > 80 ? '0-1.6' : '0-1.0',
						'156' => 'М20×1.5',
						'246' => 'Модель 2 (М2)',
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 1);

				//кран под манометр
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.tap.manometer', function () {
					$parameter = [];
					$parameter['man'] = 1;
					$parameter['category'] = '299';
					$parameter['attribute'] = array(
						'4' => '15',
						'69' => 'Кран под манометр',
						'156' => 'G1/2 - М20х1.5',
						'206' => 'внутренняя резьба',
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 2);

				//кран под датчик давления и реле
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.tap.pressure', function () {
					$parameter = [];
					$parameter['man'] = 66;
					$parameter['category'] = '557';
					$parameter['attribute'] = array(
						'4' => '15',
						'245' => 'Со спусником и воздухоотводчиком',
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 1 + ($series == 3 ? 1 : 2));


				if ($dn_in <= 50) {
					//ниппель между краном и обратным клапаном
					$count_n = $request['pump_all_count'] * 2;
					if ($connection == 'Фланцевое') {
						$count_n = $request['pump_all_count'];
					} else {
						if ($dn_in == $dn_out) {
							$count_n = $request['pump_all_count'] * 3;
						}
					}
					$i++;
					$catalog_result = Cache::rememberForever('pump.station.nipple.tap.' . $dn_in, function () use ($dn_in) {
						$parameter = [];
						$parameter['man'] = 6;
						$parameter['category'] = '346';
						$parameter['attribute'] = array(
							'4' => $dn_in,
							'98' => $dn_in,
						);
						return Catalog::getCatalog($parameter);
					});
					$station_specification[] = $this->structureSpecification($i, $catalog_result, $count_n);
					if ($connection == 'Фланцевое') {
						if ($dn_in != $dn_out) {
							//переход на выходе насоса
							$i++;
							$catalog_result = Cache::rememberForever('pump.station.transition.pump.' . $dn_in . '.' . $dn_out, function () use ($dn_in, $dn_out) {
								$parameter = [];
								$parameter['man'] = 1;
								$parameter['category'] = '334';
								$parameter['attribute'] = array(
									'4' => $dn_in,
									'98' => $dn_out,
									'296' => 'Концентрический',
								);
								return Catalog::getCatalog($parameter);
							});
							$station_specification[] = $this->structureSpecification($i, $catalog_result, $request['pump_all_count']);
						}
					} else {
						//ниппель на выходе насоса
						$i++;
						$catalog_result = Cache::rememberForever('pump.station.nipple.pump.' . $dn_in . '.' . $dn_out, function () use ($dn_in, $dn_out) {
							$parameter = [];
							$parameter['man'] = 6;
							$parameter['category'] = '346';
							$parameter['attribute'] = array(
								'4' => $dn_in,
								'98' => $dn_out,
							);
							return Catalog::getCatalog($parameter);
						});
						$station_specification[] = $this->structureSpecification($i, $catalog_result, $request['pump_all_count']);
					}
				}
				if ($count_d > 1) {
					//Ниппель на датчик давления
					$i++;
					$catalog_result = Cache::rememberForever('pump.station.nipple.pressure', function () {
						$parameter = [];
						$parameter['man'] = 6;
						$parameter['category'] = '346';
						$parameter['attribute'] = array(
							'4' => 15,
							'98' => 15,
						);
						return Catalog::getCatalog($parameter);
					});
					$station_specification[] = $this->structureSpecification($i, $catalog_result, 1);

					//тройник на датчик давления
					$i++;
					$catalog_result = Cache::rememberForever('pump.station.tee.pressure', function () {
						$parameter = [];
						$parameter['man'] = 6;
						$parameter['category'] = '349';
						$parameter['attribute'] = array(
							'4' => 15,
						);
						return Catalog::getCatalog($parameter);
					});
					$station_specification[] = $this->structureSpecification($i, $catalog_result, 1);

					//угольник на датчик давления
					$i++;
					$catalog_result = Cache::rememberForever('pump.station.square.pressure', function () {
						$parameter = [];
						$parameter['man'] = 6;
						$parameter['category'] = '350';
						$parameter['attribute'] = array(
							'4' => 15,
							'114' => 'НР',
						);
						return Catalog::getCatalog($parameter);
					});
					$station_specification[] = $this->structureSpecification($i, $catalog_result, 1);

					//удлинитель на датчик давления
					$i++;
					$catalog_result = Cache::rememberForever('pump.station.extension.pressure', function () {
						$parameter = [];
						$parameter['id'] = 179782;
						return Catalog::getCatalog($parameter);
					});
					$station_specification[] = $this->structureSpecification($i, $catalog_result, 1);
				}




				//резьба на КИПиА
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.thread.control', function () {
					$parameter = [];
					$parameter['man'] = 1;
					$parameter['category'] = '336';
					$parameter['attribute'] = array(
						'4' => 15,
						'360' => 'Нержавеющая сталь AISI 304',
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 4);
				// $station_specification[] = $this->structureSpecification($i, $catalog_result, $count_d + 2 + 1);

				//резьба на расширительный бак
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.thread.tank.' . $p_before_with_h, function () use ($p_before_with_h) {
					$parameter = [];
					$parameter['man'] = 1;
					$parameter['category'] = '336';
					$parameter['attribute'] = array(
						'4' => $p_before_with_h <= 95 ? '20' : '25',
						'360' => 'Нержавеющая сталь AISI 304',
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 1);

				if ($dn_in <= 50) {
					$count_r = $request['pump_all_count'] * 2;
					if ($connection == 'Фланцевое') {
						$count_r = $count_r * 2;
					}
					//резьба на арматуру
					$i++;
					$catalog_result = Cache::rememberForever('pump.station.thread.fittings.' . $dn_in, function () use ($dn_in) {
						$parameter = [];
						$parameter['man'] = 1;
						$parameter['category'] = '336';
						$parameter['attribute'] = array(
							'4' => $dn_in,
							'360' => 'Нержавеющая сталь AISI 304',
						);
						return Catalog::getCatalog($parameter);
					});
					$station_specification[] = $this->structureSpecification($i, $catalog_result, $count_r);
				}

				if ($dn_in <= 50) {
					//Американка на арматуру
					$i++;
					$catalog_result = Cache::rememberForever('pump.station.american.fittings.' . $dn_in, function () use ($dn_in) {
						$parameter = [];
						$parameter['man'] = 6;
						$parameter['category'] = '348';
						$parameter['attribute'] = array(
							'4' => $dn_in,
							'114' => 'ВР/НР',
							'115' => 'Прямой',
						);
						return Catalog::getCatalog($parameter);
					});
					$station_specification[] = $this->structureSpecification($i, $catalog_result, $request['pump_all_count'] * 2);
				}

				//Футорка на датчики и рэле
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.futura.sensors', function () {
					$parameter = [];
					$parameter['man'] = 6;
					$parameter['category'] = '351';
					$parameter['attribute'] = array(
						'4' => '15',
						'98' => '8',
						'114' => 'ВР/НР',
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 1 + $count_d);




				//расчет диаметра коллектора
				$dn_kol = $this->getDnByG($request['q_all']);

				//Труба коллектор
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.pipe.collector.' . $dn_kol, function () use ($dn_kol) {
					$parameter = [];
					$parameter['man'] = 1;
					$parameter['category'] = '520';
					$parameter['attribute'] = array(
						'4' => $dn_kol,
						'110' => ($dn_kol >= 80) ? '3.0' : '2.0', //!решить вопрос Андрей + Костя = л
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, round((120 * 4 + 600 * ($request['pump_all_count'] - 1)) / 1000));

				//фланец на коллектор
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.flange.collector.' . $dn_kol, function () use ($dn_kol) {
					$parameter = [];
					$parameter['man'] = 1;
					$parameter['category'] = '340';
					$parameter['attribute'] = array(
						'4' => $dn_kol,
						'40' => '16',
						'111' => ($dn_kol >= 125) ? 'Плоский' : 'Пресс-фланец', //!узнать у Никкиты пичугина
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 4);

				//Отбортовка на фланцы
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.flange.flanging.' . $dn_kol, function () use ($dn_kol) {
					$parameter = [];
					$parameter['man'] = 1;
					$parameter['category'] = '340';
					$parameter['attribute'] = array(
						'4' => $dn_kol,
						'110' => '2.0',
						'111' => 'Отбортовка',
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 4);


				//фланцы арматуры
				if ($dn_in > 50) {
					$i++;
					$catalog_result = Cache::rememberForever('pump.station.flange.fittings.' . $dn_in, function () use ($dn_in) {
						$parameter = [];
						$parameter['man'] = 1;
						$parameter['category'] = '340';
						$parameter['attribute'] = array(
							'4' => $dn_in,
							'40' => '16',
							'111' => 'Плоский',
						);
						return Catalog::getCatalog($parameter);
					});
					$station_specification[] = $this->structureSpecification($i, $catalog_result, 6 * $request['pump_all_count']);
				}

				//фланцы насоса вход
				if ($connection == 'Фланцевое') {
					$i++;
					$catalog_result = Cache::rememberForever('pump.station.flange.pump.in.' . $dn_in, function () use ($dn_in) {
						$parameter = [];
						$parameter['man'] = 1;
						$parameter['category'] = '340';
						$parameter['attribute'] = array(
							'4' => $dn_in,
							'40' => '16',
							'111' => 'Плоский',
						);
						return Catalog::getCatalog($parameter);
					});
					$station_specification[] = $this->structureSpecification($i, $catalog_result, ($dn_in == $dn_out) ? $request['pump_all_count'] * 2 : $request['pump_all_count']);
					if ($dn_in != $dn_out) {
						//фланцы насоса выход
						$i++;
						$catalog_result = Cache::rememberForever('pump.station.flange.pump.out.' . $dn_out, function () use ($dn_out) {
							$parameter = [];
							$parameter['man'] = 1;
							$parameter['category'] = '340';
							$parameter['attribute'] = array(
								'4' => $dn_out,
								'40' => '16',
								'111' => 'Плоский',
							);
							return Catalog::getCatalog($parameter);
						});
						$station_specification[] = $this->structureSpecification($i, $catalog_result, $request['pump_all_count']);
					}
				}

				//заглушки
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.flange.plugs.' . $dn_kol, function () use ($dn_kol) {
					$parameter = [];
					$parameter['category'] = '329';
					$parameter['attribute'] = array(
						'4' => $dn_kol,
						// '109' => 'Фланцевая',
						'109' => ($dn_kol >= 100) ? 'Фланцевая' : 'Пресс-фланцевая',
						'360' => 'Сталь',
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 2);

				//Лист стальной
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.steel.sheet', function () use ($request, $value, $pump_model) {
					$parameter = [];
					if ($request['pump_all_count'] <= 2) {
						$parameter['id'] = 188592;
					} else {
						$model = $value['spec'][2];
						$model = explode('-', str_replace("F", "", $model))[0];
						$model_array = ['CDM1', 'CDM3', 'CDM5'];
						if ($pump_model == 'CDM' && !in_array($model, $model_array)) {
							$parameter['id'] = 190947;
						} else {
							$parameter['id'] = 186228;
						}
					}
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, $request['pump_all_count'] > 3 ? 2 : 1);

				//Труба на стойки
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.pipe.rack', function () {
					$parameter = [];
					$parameter['id'] = 27824;
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, $series == 1 ? 1.5 : 3);

				//Хомут на коллектор
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.clamp.collector.' . $dn_kol, function () use ($dn_kol) {
					$parameter = [];
					$parameter['man'] = 200;
					$parameter['category'] = '359';
					$parameter['attribute'] = array(
						'4' => $dn_kol,
						'266' => 'Трубный',
					);
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, $pump_type == 'Вертикальный' ? 4 : 2);

				//Виброопора
				$count_vo = 4;
				if ($pump_type == 'Вертикальный') {
					$count_vo += 4;
				}
				if ($request['pump_all_count'] > 3) {
					$count_vo += 2;
				}
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.vibration.first', function () {
					$parameter = [];
					$parameter['id'] = 41129;
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, $count_vo);

				if ($pump_type == 'Вертикальный') {
					$i++;
					$catalog_result = Cache::rememberForever('pump.station.vibration.second', function () {
						$parameter = [];
						$parameter['id'] = 168002;
						return Catalog::getCatalog($parameter);
					});
					$station_specification[] = $this->structureSpecification($i, $catalog_result, 4);
				}

				//Табличка информационная
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.info', function () {
					$parameter = [];
					$parameter['id'] = 41102;
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 1);

				//Работа
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.work', function () {
					$parameter = [];
					$parameter['id'] = 193771;
					return Catalog::getCatalog($parameter);
				});
				$station_specification[] = $this->structureSpecification($i, $catalog_result, 1);

				$electric = $this->getElectric($attribute, $series);


				$series_result[$this->series_index[$series]] = [
					'title' => $this->getTitle($value, $series, 'series'),
					'station' => $station_specification,
					'station_price' => $this->getPriceBySpec($station_specification),
					'electric' => $electric,
					'electric_price' => $this->getPriceBySpec($electric),
					'wight' => $this->wight * 1.05,
				];
				$this->wight = 0;
			}

			$price_result =  $this->getTotalPrice($series_array, $dn_kol, $request['pump_all_count'], $series_result);


			$attribute = [];
			foreach ($value['attribute'] as $vvv) {
				$attribute[$vvv['id']] = $vvv['value'];
			}
			$a_result = [132, 237, 133, 137, 320, 130, 131];
			$attributeComplete = [];
			$attributeComplete[] = [
				'id' => 9990,
				'name' => 'Производительность',
				'value' =>  $request['q'],
			];
			$attributeComplete[] = [
				'id' => 9990,
				'name' => 'Напор',
				'value' =>  $request['h'] - 2,
			];
			$attributeComplete[] = [
				'id' => 9990,
				'name' => 'Перекачиваемая жидкость',
				'value' =>  'Вода 100%',
			];
			$attributeComplete[] = [
				'id' => 9990,
				'name' => 'Плотность',
				'value' =>  '998.20 кг/м³',
			];
			$attributeComplete[] = [
				'id' => 9990,
				'name' => 'Плотность',
				'value' =>  '1.0 мм²/сек',
			];
			foreach ($attribute as $k => $v) {
				$atrr = \Catalog_v2\Model\CatalogAtribute::find($k);
				if (!empty($atrr)) {
					if (in_array($atrr->id, $a_result))
						$attributeComplete[] = [
							'id' => $atrr->id,
							'name' => $atrr->title,
							'value' =>  $v,
						];
				}
			}
			$attributeComplete[] = [
				'id' => 9991,
				'name' => 'Артикул насоса',
				'value' =>  $value['sku'],
			];


			$result[] = [
				'info' => [
					'pump' => $value['cid'],
					'title' => $this->getTitle($value, $series),
					'pump_catalog' => $value,
					'q_all' => $request['q_all'],
					'h' => $request['h'],
					'work_mark' => $request['work_mark'],
					'pump_count' => $request['pump_count'],
					'pump_reserve' => $request['pump_reserve'],
					'pump_reserve_count' => $request['pump_reserve_count'],
					'attribute' => $attributeComplete,
					'dn_kol' => $dn_kol,
				],
				'series' => $series_result,
				'price_series_1' => $price_result[1] ?? null,
				'price_series_2' => $price_result[2] ?? null,
				'price_series_3' => $price_result[3] ?? null,
			];
		}

		return $result;
	}

	public function getTotalPrice($series_array, $dn_kol, $pump_all_count, $series_result)
	{
		$price_result = [];
		foreach ($series_array as $series) {

			$work = $this->getWorkByDn($dn_kol, $pump_all_count, $series);
			$second = 0;
			if ($series == 2) {
				$second = 20000;
			}
			$price_result[$series] = 2.2 * ($second + ($work * 0.7 * 3000) + ($series_result[$this->series_index[$series]]['station_price']['allPrice'] + $series_result[$this->series_index[$series]]['electric_price']['allPrice']));
		}
		return $price_result;
	}

	public function getWorkByDn($dn, $pump_count, $series)
	{
		$correct_series = $series == 1 ? 1 : 2;
		$result[2][1] = 55;
		$result[2][2] = 55;
		$result[3][1] = 60;
		$result[3][2] = 60;
		$result[4][1] = 70;
		$result[4][2] = 70;
		$result[5][1] = 80;
		$result[5][2] = 80;
		$result[6][1] = 80;
		$result[6][2] = 80;
		switch ($dn) {
			case '32':
				$result[2][1] = 26;
				$result[2][2] = 32;
				$result[3][1] = 29;
				$result[3][2] = 35;
				$result[4][1] = 33;
				$result[4][2] = 39;
				break;
			case '40':
				$result[2][1] = 28;
				$result[2][2] = 34;
				$result[3][1] = 31;
				$result[3][2] = 37;
				$result[4][1] = 36;
				$result[4][2] = 42;
				break;

			case '50':
				$result[2][1] = 30;
				$result[2][2] = 36;
				$result[3][1] = 34;
				$result[3][2] = 40;
				$result[4][1] = 39;
				$result[4][2] = 45;
				break;

			case '65':
				$result[2][1] = 32;
				$result[2][2] = 38;
				$result[3][1] = 36;
				$result[3][2] = 42;
				$result[4][1] = 41;
				$result[4][2] = 47;
				break;
			case '80':
				$result[2][1] = 35;
				$result[2][2] = 41;
				$result[3][1] = 40;
				$result[3][2] = 46;
				$result[4][1] = 45;
				$result[4][2] = 51;
				break;

			case '100':
				$result[2][1] = 39;
				$result[2][2] = 45;
				$result[3][1] = 44;
				$result[3][2] = 50;
				$result[4][1] = 49;
				$result[4][2] = 55;
				break;

			case '125':
				$result[2][1] = 43;
				$result[2][2] = 49;
				$result[3][1] = 48;
				$result[3][2] = 54;
				$result[4][1] = 53;
				$result[4][2] = 59;
				break;

			case '150':
				$result[2][1] = 48;
				$result[2][2] = 54;
				$result[3][1] = 55;
				$result[3][2] = 60;
				$result[4][1] = 59;
				$result[4][2] = 65;
				break;

			case '200':
				$result[2][1] = 51;
				$result[2][2] = 57;
				$result[3][1] = 58;
				$result[3][2] = 63;
				$result[4][1] = 62;
				$result[4][2] = 68;
				break;
		}
		return $result[$pump_count][$correct_series] ?? 60;
	}

	public function getSeries($series_calculate = null)
	{
		if (!empty($series_calculate)) {
			if ($series_calculate == 'wp') {
				$series_array = [1];
			} elseif ($series_calculate == 'wpb') {
				$series_array = [2];
			} else {
				$series_array = [3];
			}
		} else {
			$series_array = [1, 2, 3];
		}
		return $series_array;
	}

	public function getPriceBySpec($result, $clearCache = false)
	{
		$cidArray = [];
		$countNull = 0;
		$allPrice = 0;
		$price = [];
		foreach ($result as $kSpec => $vSpec) {
			if (!empty($vSpec['tm_cid'])) {
				$cidArray[] = $vSpec['tm_cid'];
				if ($clearCache) {
					Cache::forget('pump.station.price.day.' . $vSpec['tm_cid']);
				}
				$price[$vSpec['tm_cid']] = Cache::remember(
					'pump.station.price.day.' . $vSpec['tm_cid'],
					now()->addDay(1),
					function () use ($vSpec) {
						$p = \Price::getPrice($vSpec['tm_cid']);
						if($p->price_purchase_ == 0){
							$attr = [];
							$attr['uid'] = [79];
							$attr['title'] = 'При подборе обнаружена 0 цена ('. $vSpec['tm_cid'].')';
							$attr['text'] = 'При подборе обнаружена 0 цена на товар '.$vSpec['tm_title'].' ('. $vSpec['tm_cid'].')';;
							$attr['icon'] = config('app.url') . '/files/push/new_storage.png';
							$attr['url'] =  route('admin.price.settings.edit', ['id' => $vSpec['tm_cid']]);
							$attr['pNotif'] = true;
							$attr['pPush'] = true;
							$attr['module'] = "price";
							$attr['pid'] = $vSpec['tm_cid'];
							sendAllNotif($attr);
						}
						return $p->price_purchase_;
					}
				);

			}
		}
		if (!empty($cidArray)) {
			foreach ($price as $keyPrice => $valuePrice) {
				if (empty($valuePrice)) {
					$countNull++;
				}
			}
			foreach ($result as $kSpec => $vSpec) {
				if (is_array($vSpec)) {
					if (isset($price[$vSpec['tm_cid']])) {
						$allPrice += $price[$vSpec['tm_cid']] * $vSpec['tm_count'];
					}
				}
			}
		}
		return [
			'countNull' => $countNull,
			'priceArray' => $price,
			'allPrice' => $allPrice
		];
	}


	public function getTitle($pump, $series, $type = 'pump')
	{
		$series_names = [
			1 => 'Smart-WP',
			2 => 'Smart-WPB',
			3 => 'Smart-Control-WPB',
		];
		if ($type == 'pump') {
			$smart_title = [
				'АТРИ-НС',
				$this->request['pump_all_count'],
				$pump['spec'][2] ?? $pump['sku'],
			];
		} elseif ($type == 'series') {
			$smart_title = [
				$series_names[$series],
			];
		} elseif ($type == 'all') {
			$smart_title = [
				'АТРИ-НС',
				$this->request['pump_all_count'],
				$pump['spec'][2] ?? $pump['sku'],
				$series_names[$series],
			];
		}

		return implode(' ', $smart_title);
	}

	protected function getElectric($attribute, $series)
	{
		// $amperage = $attribute->where('id', '320')->first()['value'];
		$power = Numb::prepareNumeric($attribute->where('id', '137')->first()['value']);
		$pump_all_count = $this->request['pump_all_count'];
		$pump_type = $attribute->where('id', '358')->first()['value'];
		$electric_specification = [];
		if ($series == 1) {
			$i = 0;

			//частотник на насосы
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.frequency.' . $power, function () use ($power) {
				$parameter = [];
				$parameter['man'] = 272;
				$parameter['category'] = '531';
				$parameter['attribute'] = array(
					'64' => 'IP65',
				);
				$frequency = Catalog::getCatalog($parameter);
				foreach ($frequency as $key => $value) {
					if ($value['attribute_save']['240'] >= $power) {
						return [$value];
					}
				}
				return null;
			});
			if (!empty($catalog_result)) {
				$electric_specification[] = $this->structureSpecification($i, $catalog_result, $pump_all_count);
			}

			//автомат
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.switch.20', function () {
				$parameter = [];
				$parameter['man'] = 96;
				$parameter['category'] = '584';
				$parameter['attribute'] = array(
					'306' => '20',
					'307' => 'C',
					'308' => '3',
					'362' => 'DZ47-60',
					'363' => '4.5',
				);
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//бокс
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.boxing', function () {
				$parameter = [];
				$parameter['id'] = 168050;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//кабель
			if ($pump_type != 'Вертикальный') {
				$i++;
				$parameter = [];
				$parameter['id'] = 177622;
				$electric_specification[] = $this->structureSpecification($i, Catalog::getCatalog($parameter), $pump_all_count * 1.5);
			}
			$i++;
			$parameter = [];
			$parameter['id'] = 21224;
			$electric_specification[] = $this->structureSpecification($i, Catalog::getCatalog($parameter), 2);
			$i++;
			$parameter = [];
			$parameter['id'] = 36251;
			$electric_specification[] = $this->structureSpecification($i, Catalog::getCatalog($parameter), $pump_all_count * 0.4);
			if ($power <= 2.2) {
				$i++;
				$parameter = [];
				$parameter['id'] = 40402;
				$electric_specification[] = $this->structureSpecification($i, Catalog::getCatalog($parameter), 1.5 + $pump_all_count * 2);
			} else {
				$i++;
				$parameter = [];
				$parameter['id'] = 40402;
				$electric_specification[] = $this->structureSpecification($i, Catalog::getCatalog($parameter), 1.5 + $pump_all_count * 0.5);
				$i++;
				$parameter = [];
				$parameter['id'] = 177576;
				$electric_specification[] = $this->structureSpecification($i, Catalog::getCatalog($parameter), $pump_all_count * 1.2);
			}
			$i++;
			$parameter = [];
			$parameter['id'] = 39565;
			$electric_specification[] = $this->structureSpecification($i, Catalog::getCatalog($parameter), 5 + $pump_all_count * 2);
			$i++;
			$parameter = [];
			$parameter['id'] = 14249;
			$electric_specification[] = $this->structureSpecification($i, Catalog::getCatalog($parameter), 3);
			$i++;
			$parameter = [];
			$parameter['id'] = 182590;
			$electric_specification[] = $this->structureSpecification($i, Catalog::getCatalog($parameter), 1 + $pump_all_count * 0.2);
		} elseif ($series == 2 || $series == 3) {
			$i = 0;

			//шкаф
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.locker.' . $pump_all_count, function () use ($pump_all_count) {
				$parameter = [];
				if ($pump_all_count <= 3) {
					$parameter['id'] = 16198;
				} else {
					$parameter['id'] = 13586;
				}
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//контроллер для 3 серии
			if ($series == 3) {
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.control', function () {
					$parameter = [];
					$parameter['id'] = 185825;
					return Catalog::getCatalog($parameter);
				});
				$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);
			}

			//частотник в шкаф
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.x_frequency.' . $power, function () use ($power, $series) {
				$parameter = [];
				$parameter['man'] = $series == 3 ? 278 : 272;
				$parameter['category'] = '531';
				$parameter['attribute'] = array(
					'64' => 'IP20',
					'336' => $series == 3 ? 'VF-51' : 'PD ES',
				);
				$frequency = Catalog::getCatalog($parameter);
				foreach ($frequency as $key => $value) {
					if ($value['attribute_save']['240'] >= $power) {
						return [$value];
					}
				}
				return null;
			});
			if (!empty($catalog_result)) {
				$electric_specification[] = $this->structureSpecification($i, $catalog_result, $pump_all_count);
			}

			//автомат на ввод
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.switch.in.' . $power, function () use ($power) {
				$parameter = [];
				$parameter['man'] = 96;
				$parameter['category'] = '584';
				$parameter['attribute'] = array(
					'306' => $power > 1.5 ? '16' : '10',
					'308' => '3',
					'362' => 'NXB-63',
					'363' => '4.5',
				);
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//автомат на частотник
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.switch.frequency', function () {
				$parameter = [];
				$parameter['man'] = 96;
				$parameter['category'] = '584';
				$parameter['attribute'] = array(
					'306' => '6',
					'308' => '3',
					'362' => 'NXB-63',
					'363' => '4.5',
				);
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, $pump_all_count);

			//автомат на вентилятор
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.switch.cooling', function () {
				$parameter = [];
				$parameter['man'] = 96;
				$parameter['category'] = '584';
				$parameter['attribute'] = array(
					'306' => '2',
					'308' => '1',
					'362' => 'NXB-63',
					'363' => '4.5',
				);
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//Термостат
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.thermostat', function () {
				$parameter = [];
				$parameter['id'] = 168049;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//Вентилятор
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.cool', function () {
				$parameter = [];
				$parameter['id'] = 90900;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//Решетка вентилятор
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.cool.grid', function () {
				$parameter = [];
				$parameter['id'] = 182431;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//Индентор сети
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.light.power', function () {
				$parameter = [];
				$parameter['id'] = 171255;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//клемма на сеть
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.terminal.power', function () {
				$parameter = [];
				$parameter['id'] = 182920;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//клемма на сеть крышка
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.terminal.power.cover', function () {
				$parameter = [];
				$parameter['id'] = 182921;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//клемма управляющая
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.terminal.power.control', function () {
				$parameter = [];
				$parameter['id'] = 182514;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 8 + 3 * $pump_all_count);

			//клемма водная
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.terminal.power.control.in', function () {
				$parameter = [];
				$parameter['id'] = 183064;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 4);

			//Зажим наборный
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.terminal.clamps', function () {
				$parameter = [];
				$parameter['id'] = 185450;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 8 + 3 * $pump_all_count);

			//клемма управляющая крышка
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.terminal.power.control.cover', function () {
				$parameter = [];
				$parameter['id'] = 182515;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1 + $pump_all_count);

			//шина нулевая
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.terminal.tire.zero', function () {
				$parameter = [];
				$parameter['id'] = 182142;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//короб большой
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.box.big', function () {
				$parameter = [];
				$parameter['id'] = 36614;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result,  $pump_all_count <= 3 ? 1 : 2);

			//короб маленький
			// $i++;
			// $catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.box.small', function () {
			// 	$parameter = [];
			// 	$parameter['id'] = 36368;
			// 	return Catalog::getCatalog($parameter);
			// });
			// $electric_specification[] = $this->structureSpecification($i, $catalog_result,  $pump_all_count <= 3 ? 0.5 : 1);

			//ограничитель на DIN рейку
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.din.limiter', function () {
				$parameter = [];
				$parameter['id'] = 182434;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result,  7);

			//DIN рейка
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.din', function () {
				$parameter = [];
				$parameter['id'] = 182409;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, $pump_all_count <= 3 ? 1 : 1.5);

			//Сальник PG 19
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.pg.19', function () {
				$parameter = [];
				$parameter['id'] = 32544;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result,  $pump_all_count <= 3 ? 2 : 1);

			//Сальник PG 20
			if ($pump_all_count > 3) {
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.pg.20', function () {
					$parameter = [];
					$parameter['id'] = 32544;
					return Catalog::getCatalog($parameter);
				});
				$electric_specification[] = $this->structureSpecification($i, $catalog_result,  1);
			}

			//нейлоновая муфта
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.clutch', function () {
				$parameter = [];
				$parameter['id'] = 179932;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result,  3 + $pump_all_count);

			//клемма соединительная
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.connecting.terminal', function () {
				$parameter = [];
				$parameter['id'] = 185842;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result,   $pump_all_count <= 3 ? 0.2 : 0.4);

			//провод LS на ввод
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.wire.in', function () use ($pump_all_count) {
				$parameter = [];
				$parameter['id'] = $pump_all_count <= 3 ? 196630 : 138748;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result,   2);

			//провод на обвязку насосов
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.wire.pump', function () {
				$parameter = [];
				$parameter['id'] = 196634;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result,   $pump_all_count * 3);

			//провод на обвязку всего
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.wire.all', function () {
				$parameter = [];
				$parameter['id'] = 196650;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result,  $series == 2 ? 20 : 40);

			//провод синий
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.wire.blue', function () {
				$parameter = [];
				$parameter['id'] = 194454;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result,  5);

			//провод желтый
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.wire.yellow', function () {
				$parameter = [];
				$parameter['id'] = 196652;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result,  1);

			if ($series == 3) {
				//Переключатель на дверь
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.switch.work', function () {
					$parameter = [];
					$parameter['id'] = 109246;
					return Catalog::getCatalog($parameter);
				});
				$electric_specification[] = $this->structureSpecification($i, $catalog_result,  $pump_all_count);

				//Индикатор работы
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.light.work', function () {
					$parameter = [];
					$parameter['id'] = 171257;
					return Catalog::getCatalog($parameter);
				});
				$electric_specification[] = $this->structureSpecification($i, $catalog_result,  $pump_all_count);

				//Индикатор аварии
				$i++;
				$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.light.error', function () {
					$parameter = [];
					$parameter['id'] = 171256;
					return Catalog::getCatalog($parameter);
				});
				$electric_specification[] = $this->structureSpecification($i, $catalog_result,  $pump_all_count);
			}

			//Наконечник 1 0,5-8
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.end.1.05', function () {
				$parameter = [];
				$parameter['id'] = 194450;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//Наконечник 2 0,5-8
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.end.2.05', function () {
				$parameter = [];
				$parameter['id'] = 30593;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//Наконечник 1 1,5-8
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.end.1.15', function () {
				$parameter = [];
				$parameter['id'] = 194451;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);

			//Наконечник 1 2,5-8
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.end.1.25', function () {
				$parameter = [];
				$parameter['id'] = 196653;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 0.08);

			//Наконечник кольцо 1,5
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.end.ring', function () {
				$parameter = [];
				$parameter['id'] = 194452;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result,  $pump_all_count * 2);

			//маркер для кабеля
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.marker', function () {
				$parameter = [];
				$parameter['id'] = 36716;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result,  0.5);

			//монтажная база для провода на дверь
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.installer.base', function () {
				$parameter = [];
				$parameter['id'] = 194453;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result,  5);

			//стяжка для провода на дверь
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.installer.coupler', function () {
				$parameter = [];
				$parameter['id'] = 182800;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result,  0.05);

			//маркировка для клем 2,5
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.marker.terminal.25', function () {
				$parameter = [];
				$parameter['id'] = 182516;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 2);

			//маркировка для клем 4
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.marker.terminal.4', function () {
				$parameter = [];
				$parameter['id'] = 158797;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 2);


			//печатная продукция
			$i++;
			$catalog_result = Cache::rememberForever('pump.station.electric.' . $series . '.print', function () {
				$parameter = [];
				$parameter['id'] = 166228;
				return Catalog::getCatalog($parameter);
			});
			$electric_specification[] = $this->structureSpecification($i, $catalog_result, 1);
		}
		return $electric_specification;

		// dump('$amperage');
		// dump($amperage);
		// dump('$power');
		// dump($power);
		// dump('$pump_all_count');
		// dump($pump_all_count);
		// dump('$series');
		// dump($series);
		// dd($electric_specification);
	}

	protected function getTap($dn)
	{
		if ($dn <= 50) {
			$parameter = [];
			$parameter['man'] = 6;
			$parameter['category'] = '557';
			$parameter['attribute'] = array(
				'4' => $dn,
				'195' => 'ручное (рычаг)',
				'244' => 'ВР/ВР',
				'291' => 'Aquasfera Euro'
			);
			// dd($parameter);
		} else {
			$parameter = [];
			$parameter['man'] = 12;
			$parameter['category'] = '288';
			$parameter['attribute'] = array(
				'4' => $dn,
				'37' => '120',
				'49' => 'Чугун'
			);
		}
		return $parameter;
	}

	public $wight;

	public function structureSpecification($i, $catalog, $count, $old = false)
	{

		if (!empty($catalog)) {
			$catalog = $catalog[0];
			$sku = $catalog['sku'];
			$unit =  get_unit('data', $catalog['unit'], 'sname');
			$name = $catalog['title'];
			$model = ' ';
			$manufacter = '';
			// if (!empty($catalog['man'])) {
			// 	$query = $db->query("SELECT * FROM JMY_catalog_manufacture WHERE id = '" . $catalog['man'] . "' LIMIT 1");
			// 	if ($db->numRows($query) > 0) {
			// 		$manData = $db->getRow($query);
			// 		$manufacter = $manData['name'];
			// 	}
			// }
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

				$this->wight = $this->wight + Numb::prepareNumeric($weight_good) * $count;
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
			return   array(
				'tm_cid'        	=> '',
				'tm_aid'        	=> '',
				'tm_n'        		=> $i,
				'tm_title' 			=> '',
				'tm_model' 			=> '',
				'tm_sku' 			=> '',
				'tm_manufacter' 	=> '',
				'tm_unit' 			=> '',
				'tm_count' 			=> '',
				'tm_weight' 		=> '',
			);
		}
	}
}
