<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Project\Actions\Calculation\Pump;


use Boot\Support\Numb;
use Boot\Support\Facades\Config;
use Boot\Abstracts\Actions\Action;
use Project\Tasks\Calculation\Pump\PumpCalculationTask;
use Project\Tasks\Calculation\Pump\PumpCollectDataTask;
use Project\UI\API\Requests\Calculation\PumpCalculationRequest;


class ApiPumpCalculationAction extends Action
{

    /**
     * Обработчик действия
     *
     * @param  mixed $request
     * @return array
     */
    public function run(PumpCalculationRequest $request): array
    {
        $requestData = app(PumpCollectDataTask::class)->run($request);
        $result = app(PumpCalculationTask::class)->run($requestData);
        return ['data' => $this->getResult($result, $requestData)];
    }

    public function getResult($collection, $requestData)
    {
        $entities = [];
        if (!empty($collection['result'])) {
            foreach ($collection['result'] as $value) {
                $catalog = Config::get('project-pump.models.catalog')::find($value);
                $attribute =  $catalog->attribute;
                $chart = [];
                $xArray = [];
                $query = Config::get('project-pump.models.pump')::where("cid", $value)->where("type", 'QH')->orderBy("type_of_instalation")->orderBy("work_line")->get();
                foreach ($query as $p) {
                    $d = $p->data;
                    $chart_data = [];
                    $max_h = 0;
                    $max_q = 0;
                    foreach ($d as $k => $v) {
                        if ($requestData['usePumpStation']) {
                            if (Numb::prepareNumeric($v->H) - 2 >= 0) {
                                if (Numb::prepareNumeric($v->H) > $max_h) {
                                    $max_h = Numb::prepareNumeric($v->H);
                                }
                                if (Numb::prepareNumeric($v->Q) > $max_q) {
                                    $max_q = Numb::prepareNumeric($v->Q);
                                }
                                $chart_data[] = ['x' => Numb::prepareNumeric($v->Q), 'y' => Numb::prepareNumeric($v->H) - 2];
                            }
                        } else {
                            if (Numb::prepareNumeric($v->H) > $max_h) {
                                $max_h = Numb::prepareNumeric($v->H);
                            }
                            if (Numb::prepareNumeric($v->Q) > $max_q) {
                                $max_q = Numb::prepareNumeric($v->Q);
                            }
                            $chart_data[] = ['x' => Numb::prepareNumeric($v->Q), 'y' => Numb::prepareNumeric($v->H)];
                        }
                    }
                    $chart[] = $chart_data;
                }
                if ($requestData['usePumpStation']) {
                    $max_h = Numb::prepareNumeric($v->H) - 2;
                }




                $calcChart = [];
                for ($i = 0; $i <= $max_h + 2; $i += 0.3) {
                    $p = $requestData['h'] / ($requestData['q'] * $requestData['q']);
                    $x = sqrt($i / $p);
                    $calcChart[] = ['x' => $x, 'y' => $i];
                }
                $calcI = 1;
                if ($max_q > 15) {
                    $calcI = round($max_q / 15);
                }
                for ($i = 0; $i <= $max_q + $calcI; $i += $calcI) {
                    $xArray[] = $i;
                }
                sort($xArray, SORT_NUMERIC);
                $xArray = array_unique($xArray);
                $xx = [];
                foreach ($xArray as $kk => $vv) {
                    $xx[] = $vv;
                }
                $attributeComplete = [];
                foreach ($attribute as $k => $v) {
                    $att = Config::get('project-pump.models.attribute')::find($k);
                    $attributeComplete[] = [
                        'id' => $att->id,
                        'name' => $att->name,
                        'value' =>  $v,
                    ];
                }
                $spec =  $catalog->spec;
                $weight = $catalog->weight;

                $price = \Price::getPrice($catalog->id);
                $price_list = empty($price->price_list) ? 'По запросу' : $price->price_list;
                $storage = get_ndr(["cid" => $catalog->id, "stock_id" => 1, "returnType" => "array"]);
                $man_stock = \Catalog\Model\CatalogManufactureStock::where('cid', $catalog->id)->first();
                $man_stock_date = null;
                $man_stock_correct = 'По запросу';
                if (!empty($man_stock) && $man_stock->count > 0) {
                    $man_stock_correct = $man_stock->count . ' шт.';
                    $man_stock_date = $man_stock->date;
                }
                $storage_correct = (isset($storage['d']) && $storage['d'] > 0) ?  $storage['d'] . ' шт.' : 'Под заказ';
                $entities[] = [
                    'cid' => $catalog->id,
                    'sku' => $catalog->sku,
                    'title' => $catalog->title,
                    'preview' => $catalog->preview,
                    'description' => $catalog->short,
                    'price' => $price_list,
                    'attribute' => $attributeComplete,
                    'spec' => $spec,
                    'weight' => $weight,
                    'charts' => $chart,
                    'man' => 'Wilo',
                    'point' => ['x' => $requestData['q'], 'y' => $requestData['h']],
                    'calcPoint' => ['x' => $collection['calc'][$catalog->id]['superX'], 'y' => $collection['calc'][$catalog->id]['superY']],
                    'cross' => ['x' => $collection['calc'][$catalog->id]['superX'], 'y' => $collection['calc'][$catalog->id]['superY']],
                    'calcChart' => $calcChart,
                    'labelX' => $xx,
                    'superChart' => $collection['calc'][$catalog->id]['superLine'],
                    'delta' => round((($collection['calc'][$catalog->id]['superX'] - $requestData['q']) / $requestData['q']) * 100, 2),
                    'storage' => $storage_correct,
                    'man_stock' => $man_stock_correct,
                    'man_stock_date' => $man_stock_date,
                ];
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
