<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Project\Tasks\Calculation\PumpStation;

use Boot\Support\Numb;
use Boot\Abstracts\Tasks\Task;
use Project\UI\API\Requests\Calculation\PumpStationCalculationRequest;

class PumpStationCollectDataTask extends Task
{
	/**
	 * run
	 *
	 * @param  mixed $request
	 * @return array
	 */
	public function run(PumpStationCalculationRequest $request): array
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
			'type_use',
			'frequency',
			'voltage',
			'series',
			'pump_reserve_count',
			'coefficient' => 5,
			'p' => ['6 bar', '10 bar', '6/10 bar', '12', '12 bar', '16 bar', '25 bar', '25/40 bar'],
			'cid' => null,
			'series_calculate' => null,
			'pump_switch' => 0,
			'pump_count' => 1,
			'pump_reserve' => 0,
			'viscosity' => 1.001,
			'density' => 998.2,
			't' => 1,
			'work_mark' => 'A1',
			'pump_wrong' => 1,
			'q_all',
			'p_max',
			'p_before' => 0,
		]);
		$pump_all_count = $sanitizedData['pump_count'];
		if($sanitizedData['pump_reserve'] == 'true'){
			$pump_all_count += $sanitizedData['pump_reserve_count'] ?? 0;
		}
		$sanitizedData['pump_all_count'] = $pump_all_count;
		$sanitizedData['q'] = $sanitizedData['q_all'] / $sanitizedData['pump_count'];

		$sanitizedData['manufacture'] = is_array($sanitizedData['manufacture']) ? $sanitizedData['manufacture'] : (($sanitizedData['manufacture'] != 0) ? explode(',', $sanitizedData['manufacture']) : []);
		$sanitizedData['pump_reserve'] = ($sanitizedData['pump_reserve'] == 'true') ? 1 : 0;
		$sanitizedData['pump_wrong'] = ($sanitizedData['pump_wrong'] == 'true') ? 1 : 0;
		// $sanitizedData['h'] +=2;
		$sanitizedData['series'] = explode(',', $sanitizedData['series']);

		$sanitizedData['frequency'] = 2;

// print_r($sanitizedData);
// exit;
		return $sanitizedData;
	}
}
