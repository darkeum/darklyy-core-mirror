<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Requests\UI\WEB\Controllers;

use Requests\Models\Requests;
use Boot\Controller\AbstractController;
use Project\Tasks\Calculation\PumpStation\PumpStationCalculationTask;

class RequestsUpdateController extends AbstractController
{
    static function price($id)
    {
        $request = Requests::findOrFail($id);

        if ($request->type == 6) {
            $result = $request->result[0];
            foreach ($result['series'] as $key => $value) {
                $series_array = app(PumpStationCalculationTask::class)->getSeries($key);
                $result['series'][$key]['station_price'] = app(PumpStationCalculationTask::class)->getPriceBySpec($value['station'], true);
                $result['series'][$key]['electric_price'] = app(PumpStationCalculationTask::class)->getPriceBySpec($value['electric'], true);
            }
            $price_result = app(PumpStationCalculationTask::class)->getTotalPrice($series_array,  $result['info']['dn_kol'],  $result['info']['pump_count'] + $result['info']['pump_reserve_count'], $result['series']);
            $result['price_series_1'] = $price_result[1] ?? null;
            $result['price_series_2'] = $price_result[2] ?? null;
            $result['price_series_3'] = $price_result[3] ?? null;
            $request->update(['result' => [$result]]);
            return  back();
        } else {
            abort(404);
        }

    }
}
