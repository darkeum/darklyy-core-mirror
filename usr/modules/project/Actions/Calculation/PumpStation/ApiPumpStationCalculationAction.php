<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Project\Actions\Calculation\PumpStation;


use Catalog\Models\Catalog;
use Boot\Abstracts\Actions\Action;
use Project\Tasks\Calculation\Pump\PumpGraphTask;
use Project\Tasks\Calculation\PumpStation\PumpStationCalculationTask;
use Project\Tasks\Calculation\PumpStation\PumpStationCollectDataTask;
use Project\UI\API\Requests\Calculation\PumpStationCalculationRequest;


class ApiPumpStationCalculationAction extends Action
{
    /**
     * Обработчик действия
     *
     * @param  mixed $request
     * @return array
     */
    public function run(PumpStationCalculationRequest $request): array
    {
        $requestData = app(PumpStationCollectDataTask::class)->run($request);
        $result = app(PumpStationCalculationTask::class)->run($requestData);
        return ['data' => $this->getResult($result, $requestData)];
    }

    public function getResult($collection, $requestData)
    {       
        $entities = [];
        if (!empty($collection)) {
            foreach ($collection as $value) {
                $graph_multi = app(PumpGraphTask::class)->run([
                    'id' => $value['info']['pump'],
                    'q' => $value['info']['q_all'],
                    'h' => $value['info']['h'],
                    'work_mark' => $value['info']['work_mark'],
                    'pump_switch' => 1,
                    'pump_count' => $value['info']['pump_count'],
                    'pump_reserve' => $value['info']['pump_reserve'],
                    'pump_reserve_count' => $value['info']['pump_reserve_count'],
                    'priority' => 1,
                    'use_pump_station' => 1,
                    'direction' =>  '',
                ], false);
                $pints = $graph_multi->getPoints();
                // dd($pints);             
                $delta =  round((($pints['super_mark']['QH']['x'] - $requestData['q_all'])/$requestData['q_all'])*100, 2);
                $entities[] = [
                    'sku' => '',
                    'cid' => $value['info']['pump'],
                    'attribute' => $value['info']['attribute'],
                    'title' => $value['info']['title'],
                    'preview' => '',
                    'price_series_1' => $value['price_series_1'],
                    'price_series_2' => $value['price_series_2'],
                    'price_series_3' => $value['price_series_3'],
                    'price' => 0,
                    'delta' => $delta,
                    'man_stock' => 0,
                    'storage' => 0,                   
                    'description' => '',
                    'series' => $value['series'],
                    'charts' => $graph_multi->getCharts(),
                    'points' => $pints,
                ];
                // var_dump($graph_multi->getCharts());
                // dd($entities);
            }
            $result['entities'] = $entities;
            $result['type'] = 'success';
        } else {
            $result['entities'] = $entities;
            $result['type'] = 'empty';
        }
        return  $result;
    }
}
