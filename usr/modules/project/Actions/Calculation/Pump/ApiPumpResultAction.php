<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Valve Calculation Api Action 
*/

namespace Project\Actions\Calculation\Pump;


use Boot\Support\Numb;
use Boot\Models\Attach;
use Boot\Support\Carbon;
use Boot\System\Database\DB;
use Object\Model\ObjectModel;
use Boot\Support\Facades\Auth;
use Boot\Support\Facades\File;
use Boot\Support\Facades\Config;
use Boot\Support\Facades\Storage;
use Boot\Abstracts\Actions\Action;
use Catalog_v2\Model\CatalogManufacture;
use Organizations\Model\Organizations;
use Project\Tasks\Calculation\Pump\PumpGraphTask;
use Project\Tasks\Calculation\Pump\PumpCalculationTask;
use Project\Tasks\Calculation\Pump\PumpCollectDataTask;
use Project\UI\API\Requests\Calculation\PumpCalculationRequest;


class ApiPumpResultAction extends Action
{
    /**
     * Обработчик действия
     *
     * @param  mixed $request
     * @return array
     */
    public function run(PumpCalculationRequest $request): array
    {
        $this->requestData = app(PumpCollectDataTask::class)->run($request);
      
        $result = app(PumpCalculationTask::class)->run($this->requestData);
        // var_dump($result );
        $this->collectPumpData = $result['catalog'];
        $this->calcResult = $result['calc'];
        $this->setPump($this->requestData['cid']);
        return  $this->getResult();
    }

    public function setPump($cid)
    {
        $this->pump = intval($cid);
        $this->requestData['pump'] = $this->pump;
    }


    public $pump;
    public $requestData;
    public $collectPumpData;
    public $calcResult;

    public function getResult(): array
    {
        $result = [];
        $entities = [];
        if (!empty($this->calcResult)) {
         
            foreach ($this->collectPumpData as $key => $value) {                
                if (array_key_exists($value->id, $this->calcResult)) {
                    $attribute = $value->attribute;
                    $chart = [];
                    $xArray = [];

                    // $xArray[] = $this->requestData['q'];
                    // $xArray[] = $this->calcResult[$value['id']]['superX'];
                    $query = \Catalog_v2\Model\Pump::where("cid", $value->id)->orderBy("type_of_instalation")->orderBy("work_line")->get();
                    foreach ($query as $p) {
                        $d = $p->data;
                        $chart_data = [];
                        $max_h = 0;
                        $max_q = 0;
                        foreach ($d as $k => $v) {
                            if (Numb::prepareNumeric($v->H) > $max_h) {
                                $max_h = Numb::prepareNumeric($v->H);
                            }
                            if (Numb::prepareNumeric($v->Q) > $max_q) {
                                $max_q = Numb::prepareNumeric($v->Q);
                            }
                            $chart_data[] = ['x' => Numb::prepareNumeric($v->Q), 'y' => Numb::prepareNumeric($v->H)];
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

                    // $attribute = [];
                    // var_dump($value->attribute);
                    // foreach ($value->attribute as $value_a) {
                    //     $attribute[$value_a['id']] = $value_a['value'];
                    // }
                    $attributeComplete = [];
                    foreach ($attribute as $k => $v) {
                        $atrr = \Catalog_v2\Model\CatalogAtribute::find($k);
                        if(!empty($atrr)){
                            $attributeComplete[] = [
                                'id' => $atrr->id,
                                'name' => $atrr->title,
                                'value' =>  $v,
                            ];
                        }
                        
                    }
                    $spec =$value->spec;
                    $weight = $value->weight;

                    $price = \Price::getPrice($value->id);
                    $price_list = empty($price->price_list) ? 'По запросу' : $price->price_list;
                    $manufacture = CatalogManufacture::find($value['man']);
                    $manufacture_title = 'Не известно';
                    if($manufacture){
                        $manufacture_title = $manufacture->name;
                    }
                    $entities[] = [
                        'cid' => $value->id,
                        'sku' => $value->sku,
                        'title' => $value->title,
                        'preview' => Config::get('app.url') . (empty($value->preview) ? '/media/no-photo.jpg' : $value->preview),
                        'description' => $value->short,
                        'price' => $price_list,
                        'attribute' => $attributeComplete,
                        'spec' => $spec,
                        'weight' => $weight,
                        'charts' => $chart,
                        'man' => $manufacture_title,
                        'point' => ['x' => $this->requestData['q'], 'y' => $this->requestData['h']],
                        'calcPoint' => ['x' => $this->calcResult[$value->id]['superX'], 'y' => $this->calcResult[$value->id]['superY']],
                        'cross' => ['x' => $this->calcResult[$value->id]['superX'], 'y' => $this->calcResult[$value->id]['superY']],
                        'calcChart' => $calcChart,
                        'labelX' => $xx,
                        'superChart' => $this->calcResult[$value->id]['superLine'],
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
}
