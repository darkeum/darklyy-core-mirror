<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Project\Tasks\Calculation\Pump;

use Boot\Support\Numb;
use Boot\Abstracts\Tasks\Task;
use Project\UI\API\Requests\Calculation\PumpCalculationRequest;

class PumpCollectDataTask extends Task
{
	/**
	 * run
	 *
	 * @param  mixed $request
	 * @return array
	 */
	public function run(PumpCalculationRequest $request): array
	{
		$sanitizedData = $request->sanitizeInput([
			'pumpSelector',
			'q',
			'h',
			'oid',
			'object',
			'manufacture',
			'model',
			'typeBinary',
			'type',
			'usePumpStation' => 0,
			'frequency',
			'voltage',
			'coefficient' => 5,
			'p' => ['2 bar', '3 bar', '4 bar', '5 bar', '6 bar', '7 bar', '8 bar', '9 bar', '10 bar', '6/10 bar', '12', '12 bar', '16 bar', '20 bar', '25 bar', '25/40 bar'],
			'cid' => null,
			'pump_switch' => 0,
			'pump_count' => 1,
			'pump_reserve' => 0,
			'pump_reserve_count' => 1,
			'pump_search_id',
			'viscosity' => 1.001,
			'density' => 998.2,
			't' => 1,
			'work_mark' => 'A1',
			'pump_wrong' => 1,
			'q_all',
			'p_before' => 0,
		]);
		if ($sanitizedData['pumpSelector'] == 0) {
			$sanitizedData['typeBinary'] = $this->getBinaryType($sanitizedData['typeBinary']);
			$sanitizedData['voltage'] = $this->getVoltage($sanitizedData['voltage']);
			$sanitizedData['pumpSeries'] = $this->getPumpSeries($sanitizedData['type'], $sanitizedData['frequency']);
		} elseif ($sanitizedData['pumpSelector'] == 1) {
			$sanitizedData['model'] = is_array($sanitizedData['model']) ? $sanitizedData['model'] : (($sanitizedData['model'] != 0) ? explode(',', $sanitizedData['model']) : []);
		} else {
			$sanitizedData['pump_search_id'] = is_array($sanitizedData['pump_search_id']) ? $sanitizedData['pump_search_id'] : (($sanitizedData['pump_search_id'] != 0) ? explode(',', $sanitizedData['pump_search_id']) : []);
		}
		if ($sanitizedData['pump_switch'] == 1) {
			$sanitizedData['q'] = $sanitizedData['q_all'] / $sanitizedData['pump_count'];
		}

		$sanitizedData['manufacture'] = is_array($sanitizedData['manufacture']) ? $sanitizedData['manufacture'] : (($sanitizedData['manufacture'] != 0) ? explode(',', $sanitizedData['manufacture']) : []);

		$sanitizedData['coefficient'] = (Numb::prepareNumeric($sanitizedData['coefficient']) + 100) / 100;
		$sanitizedData['pump_reserve'] = ($sanitizedData['pump_reserve'] == 'true') ? 1 : 0;
		$sanitizedData['pump_wrong'] = ($sanitizedData['pump_wrong'] == 'true') ? 1 : 0;
		return $sanitizedData;
	}

	/**
	 * getBinaryType
	 *
	 * @param  mixed $value
	 * @return void
	 */
	protected function getBinaryType($value)
	{
		if(!is_array($value)){
			$value = explode(',', $value);
		}

		$result = [];
		foreach ($value as $v) {
			switch ($v) {
				default:
				case "1":
				case "Одинарный":
					$result[] = 'Одинарный';
					break;
				case "2":
				case "Сдвоенный":
					$result[] = 'Сдвоенный';
					break;
				case "0":
					break;
			}
		}
		return $result;
	}

	/**
	 * getVoltage
	 *
	 * @param  mixed $u
	 * @return void
	 */
	protected function getVoltage($value)
	{
		if(!is_array($value)){
			$value = explode(',', $value);
		}

		$result = [];
		foreach ($value as $u) {
			switch ($u) {
				default:
				case "1":
				case "1~ 230 V / 50 Hz":
					$result = ['1~ 230 V / 50 Hz', '3~ 220-380 V / 50 Hz'];
					break;
				case "2":
				case "3~ 400 V / 50 Hz":
					$result = ['3~ 380 V / 50 Hz', '3~ 400 V / 50 Hz', '3~ 220-380 V / 50 Hz'];
					break;
				case "0":
					break;
			}
		}
		return $result;
	}

	/**
	 * Получение марок насоса по назначению и наличию частотного преобразователя
	 *
	 * @param 	string   $pumpUse Назначение насоса
	 * @param 	array|string     $frequency наличие частотного преобразователя
	 * @return 	array    Возвращает найденную модель насоса, если нечего не найдено вернет false
	 */
	protected function getPumpSeries($pumpUse, $frequency)
	{
		$result = [];
		$result_full = [
			1 => [],
			2 => [],
		];

		foreach (explode(',', $pumpUse) as $u) {
			switch ($u) {
				default:
				case "1":
					$result[2] = ['IPL', 'DPL', 'TOP-S', 'TOP-SD', 'STAR-RS', 'IL', 'DL', 'BL', 'NES', 'TD', 'GHN', 'GHNDbasic', 'GHNMDbasic', 'GHNMbasic', 'GHNbasic'];
					$result[1] = ['YONOS MAXO', 'YONOS MAXO-D', 'YONOS PICO', 'YONOS PICO-D', 'STRATOS PICO', 'STRATOS PICO-D', 'STRATOS MAXO', 'STRATOS MAXO-D', 'ECL 2 POLES', 'ECL 4 POLES', 'ECLD 2 POLES', 'NMT', 'NMT MAX', 'NMT MAX C', 'NMT MAX II', 'NMT MAX II C', 'NMT MAX II S', 'NMT MINI', 'NMT MINI ER', 'NMT MINI PLUS', 'NMT MINI PRO', 'NMT MINI PWM', 'NMT PLUS', 'NMT PLUS ER', 'NMT PLUS COMFORT', 'NMT PLUS ER', 'NMT PLUS PWM H', 'NMT PLUS PWM S', 'NMT SMART', 'NMT SMART C', 'NMT SMART S', 'NMTD MAX', 'NMTD MAX C', 'NMTD MAX II', 'NMTD MAX II C', 'NMTD MAX II S', 'NMTD PLUS', 'NMTD PLUS ER', 'NMTD SMART', 'NMTD SMART C', 'NMTD SMART S'];
					break;
				case "2":
					$result[2] = ['STAR-Z', 'BL', 'GHN', 'GHND', 'GHNDbasic', 'GHNM', 'GHNMDbasic', 'GHNMbasic', 'SAN', 'SAN ECO', 'SANMBASIC', 'SANbasic'];
					$result[1] = ['YONOS MAXO-Z', 'STRATOS PICO-Z', 'STRATOS MAXO-Z', 'NMT SAN MAX', 'NMT SAN MAX C', 'NMT SAN MAX II', 'NMT SAN MAX II C', 'NMT SAN MAX II S', 'NMT SAN PLUS ER', 'NMT SAN SMART', 'NMT SAN SMART C', 'NMT SAN SMART S'];
					break;
				case "3":
					$result[2] = ['MHI', 'MHIL', 'MEDANA CH1-L', 'MEDANA CH1-LC', 'CHL', 'CDL', 'NES'];
					$result[1] = [];
					break;
				case "4":
					$result[2] = ['MHI', 'MHIL', 'Helix V', 'MEDANA CH1-L', 'MEDANA CH1-LC', 'MEDANA CV1-L', 'MVI', 'CHL', 'CDL', 'NES', 'BL', 'BWJ', 'CLD 2 POLES', 'CLD 4 POLES'];
					$result[1] = ['ECL 2 POLES', 'ECL 4 POLES', 'ECLD 2 POLES'];
					break;
				case "5":
					$result[2] = ['BL', 'CDL', 'NES', 'CDM'];
					$result[1] = [];
					break;
					//Для насосных станций
				case "6":
					$result[2] = ['CDM', 'MVI', 'BL'];
					$result[1] = [];
					break;
				case "7":
					$result[2] = ['CHL', 'BWJ', 'MHI'];
					$result[1] = [];
					break;
				case "0":
					break;
			}
			$result_full[1] = array_merge($result_full[1], $result[1]);
			$result_full[2] = array_merge($result_full[2], $result[2]);
		}
		if (empty($frequency)) {
			return array_merge($result_full[1], $result_full[2]);
		} else {
			$result_prepare = [];
			foreach (explode(',', $frequency) as $value) {
				$result_prepare = array_merge($result_prepare, $result_full[$value]);
			}
			return $result_prepare;
		}
	}
}
