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

namespace Project\Actions\Calculation\PumpStation;


use Boot\Support\Str;
use Boot\Models\Attach;
use Boot\Support\Carbon;
use Catalog\Models\Catalog;
use Boot\System\Database\DB;
use Object\Model\ObjectModel;
use Boot\Support\Facades\Auth;
use Boot\Support\Facades\File;
use Price\Models\CatalogPrice;
use Boot\Support\Facades\Config;
use Boot\Support\Facades\Storage;
use Boot\Abstracts\Actions\Action;
use Price\Models\CatalogPriceDetail;
use Price\UI\CLI\CatalogPriceDaemon;
use Organizations\Model\Organizations;
use Project\Models\PumpStationAttribute;
use Project\Tasks\Calculation\Pump\PumpGraphTask;
use Project\Tasks\Calculation\PumpStation\PumpStationCalculationTask;
use Project\Tasks\Calculation\PumpStation\PumpStationCollectDataTask;
use Project\UI\API\Requests\Calculation\PumpStationCalculationRequest;


class ApiPumpStationRequestAction extends Action
{

    public $request;
    public $requestId;
    public $requestData;
    public $collectPumpData;
    public $pump;
    public $calcResult;
    public $series;
    public $result;


    /**
     * Обработчик действия
     *
     * @param  mixed $request
     * @return array
     */
    public function run(PumpStationCalculationRequest $request): array
    {
        $this->request = $request;
        $this->requestData = app(PumpStationCollectDataTask::class)->run($request);
        $result = app(PumpStationCalculationTask::class)->run($this->requestData);
        $this->result = $result;
        $this->setPump($request['cid']);

        $graph_multi = app(PumpGraphTask::class)->run([
            'id' => $this->pump,
            'q' => $this->requestData['q_all'],
            'h' => $this->requestData['h'],
            'work_mark' => $this->requestData['work_mark'],
            'pump_switch' => 1,
            'pump_count' => $this->requestData['pump_count'],
            'pump_reserve' => $this->requestData['pump_reserve'],
            'pump_reserve_count' => $this->requestData['pump_reserve_count'],
            'priority' => 1,
            'use_pump_station' => true,
            'direction' =>  '',
        ], false);
        $this->result[0]['info']['charts'] = $graph_multi->getCharts();
        $this->result[0]['info']['points'] = $graph_multi->getPoints();
        $this->result[0]['info']['cid'] = $request['cid'];
        $this->result[0]['info']['series_calculate'] = $this->requestData['series_calculate'];

        // dump($this->requestData);
        // dd($this->result);
        $this->collectPumpData = $result[0]['info']['pump_catalog'];
        $this->calcResult = $result;
        $this->series = $this->requestData['series_calculate'];

        $this->createRequestAfter();

        $res['type'] = 'success';
        $res['aid'] = $this->createCalculationAttach();
        $res['request'] = $this->requestId;
        switch ($this->requestData['series_calculate']) {
            case 'wp':
                $price = $result[0]['price_series_1'];
                break;
            case 'wpb':
                $price = $result[0]['price_series_2'];
                break;
            case 'wpbs':
                $price = $result[0]['price_series_3'];
                break;

            default:
                $price = 0;
                break;
        }

        $this->createCatalog($price, $res['aid']);
        return ['data' => $res];
    }




    public function setPump($cid)
    {
        $this->pump = intval($cid);
        $this->requestData['pump'] = $this->pump;
    }

    public function createRequestAfter($id = null)
    {


        $title = '';
        $request = array();
        $request['uid_dir'] = Auth::id();
        $request['title'] = $title;
        $request['title_user'] = $title;
        $request['request'] = $this->request;
        if (!empty($this->requestData['oid'])) {
            $request['oid'] = $this->requestData['oid'];
        }
        if (!empty($this->requestData['object'])) {
            $request['object'] = $this->requestData['object'];
        }
        $request['request_data'] = $this->requestData;
        $request['result'] = $this->result;
        $request['type'] = '6';
        $request['date'] = date("Y-m-d H:i:s");
        $request['status'] = '3';
        $result = \Request\Models\Request::create($request);
        $this->requestId = $result->id;
        $newTitle = 'Расчет насосного станции №' . $this->requestId;
        \Request\Models\Request::find($result->id)->update(['title' => $newTitle, 'title_user' => $newTitle]);
        return $this->requestId;
    }

    public function getTitle($pump, $series, $type = 'pump')
    {
        $series_names = [
            'wp' => 'Smart-WP',
            'wpb' => 'Smart-WPB',
            'wpbs' => 'Smart-Control-WPB',
        ];
        if ($type == 'pump') {
            $smart_title = [
                'АТРИ-НС',
                $this->requestData['pump_all_count'],
                $pump['spec'][2] ?? $pump['sku'],
            ];
        } elseif ($type == 'series') {
            $smart_title = [
                $series_names[$series],
            ];
        } elseif ($type == 'all') {
            $smart_title = [
                'АТРИ-НС',
                $this->requestData['pump_all_count'],
                $pump['spec'][2] ?? $pump['sku'],
                $series_names[$series],
            ];
        }

        return implode(' ', $smart_title);
    }

    public function createCalculationAttach($id = null)
    {


        $tpl = modules_path('project/Resources/Templates/pumpStation.docx');
        $document =  new \PhpOffice\PhpWord\TemplateProcessor($tpl);
        $title =  $this->getTitle($this->collectPumpData,  $this->series, 'all');


        $module = 'request';
        $directory = 'files' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $this->requestId;
        File::makeDirectory(ROOT . $directory, 0777, true);

        $dirClear = 'files/' . $module . '/' . $this->requestId;
        $dir = ROOT . $dirClear;

        $file_url_graph_multi = $dir . '/graph_multi.png';

        $graph_multi = app(PumpGraphTask::class)->run([
            'id' => $this->pump,
            'q' => $this->requestData['q_all'],
            'h' => $this->requestData['h'],
            'work_mark' => $this->requestData['work_mark'],
            'pump_switch' => 1,
            'pump_count' => $this->requestData['pump_count'],
            'pump_reserve' => $this->requestData['pump_reserve'],
            'pump_reserve_count' => $this->requestData['pump_reserve_count'],
            'priority' => 1,
            'direction' =>  $file_url_graph_multi,
            'frequency' =>  true,
            'use_pump_station' => true,
        ]);

        $document->setImageValue('graph_multi', array('path' => $file_url_graph_multi, 'width' => $graph_multi['width'] * 0.55, 'height' => $graph_multi['height'] * 0.55, 'ratio' => false));

        $attribute = [];
        foreach ($this->collectPumpData['attribute'] as $value) {
            $attribute[$value['id']] = $value['value'];
        }


        //заполнение заданных параметров
        $current_data = [];
        $current_data[] = ['current_name' => 'Производительность',          'current_value' => $this->requestData['q'] . ' м³/ч'];
        $current_data[] = ['current_name' => 'Напор',                       'current_value' => $this->requestData['h'] . ' м'];
        $current_data[] = ['current_name' => 'Перекачиваемая жидкость',     'current_value' => 'Вода 100%'];
        $current_data[] = ['current_name' => 'Т перекачиваемой жидкости',   'current_value' => '20.00 °C'];
        $current_data[] = ['current_name' => 'Плотность',                   'current_value' => '998.20 кг/м³'];
        $current_data[] = ['current_name' => 'Кинематическая вязкость',     'current_value' => '1.00 мм²/с'];
        $document->cloneRowAndSetValues('current_name', $current_data);
        //Заполнение расчетных данных
        $result_data = [];
        $result_data[] = ['result_name' => 'Производительность',            'result_value' => $this->requestData['q']. ' м³/ч'];
        $result_data[] = ['result_name' => 'Напор',                         'result_value' => $this->requestData['h']. ' м'];
        $document->cloneRowAndSetValues('result_name', $result_data);
        //Заполнение данных продукта
        $product_data = [];
        if (isset($attribute[Config::get('project-pump.attributes.pressure')])) {
            $product_data[] = ['product_name' => 'Max. рабочее давление',   'product_value' => $attribute[Config::get('project-pump.attributes.pressure')]];
        }
        if (isset($attribute[Config::get('project-pump.attributes.t')])) {
            $product_data[] = ['product_name' => 'Т перекач. жидкости',     'product_value' => $attribute[Config::get('project-pump.attributes.t')] . ' °C'];
        }
        // if (isset($attribute[Config::get('project-pump.attributes.tMaxEnv')])) {
        //     $product_data[] = ['product_name' => 'Макс. Температура окр. Среды',   'product_value' => $attribute[Config::get('project-pump.attributes.tMaxEnv')]];
        // }
        $document->cloneRowAndSetValues('product_name', $product_data);
        //Заполнение данных мотора
        $motor_data = [];
        if (isset($attribute[Config::get('project-pump.attributes.voltage')])) {
            $motor_data[] = ['motor_name' => 'Подключение к сети',      'motor_value' => $attribute[Config::get('project-pump.attributes.voltage')]];
        }
        if (isset($attribute[Config::get('project-pump.attributes.power')])) {
            $motor_data[] = ['motor_name' => 'Ном. Мощность P2',        'motor_value' => $attribute[Config::get('project-pump.attributes.power')]. ' кВт'];
        }
        if (isset($attribute[Config::get('project-pump.attributes.amperage')])) {
            $motor_data[] = ['motor_name' => 'Потребление тока',        'motor_value' => $attribute[Config::get('project-pump.attributes.amperage')]. ' А'];
        }
        $document->cloneRowAndSetValues('motor_name', $motor_data);
        //Заполнение данных по размерам
        $size_data = [];
        if (isset($attribute[Config::get('project-pump.attributes.dn_in')])) {
            $size_data[] = ['size_name' => 'Патрубок на стороне всаса',         'size_value' => 'ДУ '.$attribute[Config::get('project-pump.attributes.dn_in')]];
        }
        if (isset($attribute[Config::get('project-pump.attributes.dn_out')])) {
            $size_data[] = ['size_name' => 'Патрубок на напорн. стороне',   'size_value' => 'ДУ '.$attribute[Config::get('project-pump.attributes.dn_out')]];
        }
        // if (isset($attribute[Config::get('project-pump.attributes.length')])) {
        //     $size_data[] = ['size_name' => 'Габаритная длина',                  'size_value' => $attribute[Config::get('project-pump.attributes.length')]];
        // }
        $document->cloneRowAndSetValues('size_name', $size_data);
        //Данные материалов
        $material_data = [];
        if (isset($this->collectPumpData['sku'])) {
            $material_data[] = ['material_name' => 'Артикул насоса',   'material_value' => $this->collectPumpData['sku']];
        }
        $document->cloneRowAndSetValues('material_name', $material_data);

        $object_name = 'Не указано';
        if (isset($this->requestData['object'])) {
            $objectModel = ObjectModel::find($this->requestData['object']);
            if (!empty($objectModel)) {
                $object_name = $objectModel->name;
            }
        }

        $title_parameter = 'Рабочие параметры насосной станции ';
        if ($this->requestData['pump_reserve']) {
            $title_parameter .= '(' . $this->requestData['pump_count'] . ' рабочих + ' . $this->requestData['pump_reserve_count'] . ' резервный)';
        } else {
            $title_parameter .= '(' . $this->requestData['pump_count'] . ' рабочих)';
        }
        $attribute_cc = cc($this->collectPumpData['attribute']);
        $power = $attribute_cc->where('id', '137')->first()['value'];
        $power_all = $power * $this->requestData['pump_count'];

        $document->setValue('title', $title);
        $document->setValue('sku',  'AE-NS-' . $this->requestId);
        $document->setValue('project_numb',  $this->requestId);
        $document->setValue('project_name',  'Расчет насосного оборудования №' . $this->requestId);
        $document->setValue('project_date',  Carbon::now()->format('d.m.Y H:i'));
        $document->setValue('object_name',  $object_name);
        $document->setValue('title_parameter',  $title_parameter);

        $document->setValue('dn_in', $attribute[Config::get('project-pump.attributes.dn_in')] ?? '');
        $document->setValue('dn_out', $attribute[Config::get('project-pump.attributes.dn_out')] ?? '');
        $document->setValue('h', $this->requestData['h'] . ' м.в.ст');
        $document->setValue('q', $this->requestData['q_all'] . ' м³/ч');
        $document->setValue('speed', '2 900 1/мин');
        $document->setValue('frequency', '50 Гц');
        $document->setValue('power_all',  $power_all . ' кВт');

        $h2 = '';
        $l1 = '';
        $pic_1 = '';
        $pic_2 = '';
        $pic_3 = '';
        $dir_pic = ROOT . 'usr' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'project' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'Pictures' . DIRECTORY_SEPARATOR;

        switch ($attribute['127']) {
            case 'CDM':
                if (isset(explode('F', $this->collectPumpData['spec'][2])[1])) {
                    $search_psa = 'CDMF-' . explode('-', explode('F', $this->collectPumpData['spec'][2])[1])[0];
                } else {
                    $search_psa = 'CDMF-' . explode('-', explode('M', $this->collectPumpData['spec'][2])[1])[0];
                }

                $psa = PumpStationAttribute::where('cid', $search_psa)->where('diameter_collector', $this->result[0]['info']['dn_kol'])->where('pump_count', $this->requestData['pump_all_count'])->first();


                $l1 = $psa->center_distance ?? '';

                $pic_1 =  $dir_pic . 'v-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-1.png';
                $pic_2 =  $dir_pic . 'v-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-2.png';
                $pic_3 =  $dir_pic . 'v-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-3.png';
                break;
            case 'BL':
                $search_psa = '%BL ' . explode('-', explode('L', $this->collectPumpData['spec'][2])[1])[0] . '%';
                $psa = PumpStationAttribute::where('cid', 'like', $search_psa)->where('diameter_collector', $this->result[0]['info']['dn_kol'])->where('pump_count', $this->requestData['pump_all_count'])->first();

                $l1 = $psa->center_distance ?? '';

                $pic_1 =  $dir_pic . 'v-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-1.png';
                $pic_2 =  $dir_pic . 'v-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-2.png';
                $pic_3 =  $dir_pic . 'v-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-3.png';
                break;
            case 'MVI':
                $model = $this->collectPumpData['spec'][2];
                if (Str::indexOf($model, '/') !== false) {
                    $search_psa = '%' . $model . '%';
                } else {
                    $search_psa = '%' . $model . '%';
                    $prefix = explode('-', $model)[1];

                    $numb = explode(' ', explode('-', $model)[0])[1];
                    if ($prefix == 1) {
                        if ($numb >= 202 &&  $numb <= 220) {
                            $search_psa = '%MVI 202-1...220-1%';
                        } elseif ($numb >= 402 &&  $numb <= 419) {
                            $search_psa = '%MVI 402-1...419-1%';
                        } elseif ($numb >= 802 &&  $numb <= 819) {
                            $search_psa = '%MVI 802-1...819-1%';
                        }
                    } elseif ($prefix == 3) {
                        if ($numb >= 202 &&  $numb <= 220) {
                            $search_psa = '%MVI 202-3...212-3%';
                        } elseif ($numb >= 402 &&  $numb <= 412) {
                            $search_psa = '%MVI 402-3...412-3%';
                        } elseif ($numb >= 802 &&  $numb <= 812) {
                            $search_psa = '%MVI 802-3...812-3%';
                        } elseif ($numb >= 1602 &&  $numb <= 1614) {
                            $search_psa = '%MVI 1602-3...1614-3%';
                        }
                    }
                }
                $psa = PumpStationAttribute::where('cid', 'like', $search_psa)->where('diameter_collector', $this->result[0]['info']['dn_kol'])->where('pump_count', $this->requestData['pump_all_count'])->first();

                $l1 = $psa->center_distance ?? '';
                $h2 = $psa->centerline_vertical ?? '';

                $pic_1 =  $dir_pic . 'v-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-1.png';
                $pic_2 =  $dir_pic . 'v-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-2.png';
                $pic_3 =  $dir_pic . 'v-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-3.png';
                break;
            case 'CHL':
                $str = str_replace('T', '', $this->collectPumpData['spec'][2]);
                $search_psa = '%' .  $str . '%';
                $psa = PumpStationAttribute::where('cid', 'like', $search_psa)->where('diameter_collector', $this->result[0]['info']['dn_kol'])->where('pump_count', $this->requestData['pump_all_count'])->first();

                $l1 = $psa->centerline_horizon ?? '';

                $pic_1 =  $dir_pic . 'g-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-1.png';
                $pic_2 =  $dir_pic . 'g-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-2.png';
                $pic_3 =  $dir_pic . 'g-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-3.png';
                break;
            case 'MHI':
                $model = $this->collectPumpData['spec'][2];
                if (Str::indexOf($model, '/') !== false) {
                    $search_psa = '%' . $model . '%';
                } else {
                    $search_psa = '%' . $model . '%';
                    $prefix = explode('-', $model)[1];
                    $numb = explode(' ', explode('-', $model)[0])[1];
                    if ($numb >= 202 &&  $numb <= 203) {
                        $search_psa = '%MHI 202-1...MHI 203-2%';
                    } elseif ($numb == 204) {
                        $search_psa = '%MHI 204-1...MHI 204-2%';
                    } elseif ($numb >= 402 &&  $numb <= 403) {
                        $search_psa = '%MHI 402-1...MHI 403-2%';
                    }
                }

                $psa = PumpStationAttribute::where('cid', 'like', $search_psa)->where('diameter_collector', $this->result[0]['info']['dn_kol'])->where('pump_count', $this->requestData['pump_all_count'])->first();
                $l1 = $psa->centerline_horizon ?? '';

                $pic_1 =  $dir_pic . 'g-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-1.png';
                $pic_2 =  $dir_pic . 'g-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-2.png';
                $pic_3 =  $dir_pic . 'g-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-3.png';
                break;
            case 'BWJ':
                $search_psa = '%BWJ ' . explode('-', explode('J', $this->collectPumpData['spec'][2])[1])[0] . '%';
                $psa = PumpStationAttribute::where('cid', 'like', $search_psa)->where('diameter_collector', $this->result[0]['info']['dn_kol'])->where('pump_count', $this->requestData['pump_all_count'])->first();

                $l1 = $psa->center_distance ?? '';

                $pic_1 =  $dir_pic . 'g-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-1.png';
                $pic_2 =  $dir_pic . 'g-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-2.png';
                $pic_3 =  $dir_pic . 'g-' . $this->series . '-' . $this->requestData['pump_all_count'] . '-3.png';
                break;
        }

        if (file_exists($pic_1)) {
            $document->setImageValue('pic_1', array('path' => $pic_1, 'width' => 150, 'height' => 185, 'ratio' => false));
        } else {
            $document->setValue('pic_1',   '');
        }
        if (file_exists($pic_2)) {
            $document->setImageValue('pic_2', array('path' => $pic_2, 'width' => 200, 'height' => 185, 'ratio' => false));
        } else {
            $document->setValue('pic_2',   '');
        }
        if (file_exists($pic_3)) {
            $document->setImageValue('pic_3', array('path' => $pic_3, 'width' => 170, 'height' => 185, 'ratio' => false));
        } else {
            $document->setValue('pic_3',   '');
        }

        switch ($this->series) {
            case 'wp':
                $description = 'Насосные установки повышения давления с частотным преобразователем степенью защиты IP65 с функцией контроллера на каждом насосе предназначены для обеспечения требуемого расхода и напора воды в следующих системах:';
                $description_control = 'Регулирование частоты с помощью преобразователя частоты с функцией контроллера (на каждом насосе)';
                break;
            case 'wpb':
                $description = 'Насосные установки повышения давления с частотными преобразователями на каждый насос с функцией контроллера в шкафу, степенью зашиты класса IP 65 предназначены для обеспечения требуемого расхода и напора воды в следующих системах:';
                $description_control = 'Регулирование частоты с помощью преобразователя частоты с функцией контроллера (в шкафу)';
                break;
            case 'wpbs':
                $description = 'Насосные установки повышения давления с частотными преобразователями на каждый насос с функцией контроллера с собственным программным обеспечением в шкафу, степенью зашиты класса IP 65 предназначены для обеспечения требуемого расхода и напора воды в следующих системах:';
                $description_control = 'Регулирование частоты с помощью преобразователя частоты с функцией контроллера (в шкафу)';
                break;
        }



        $document->setValue('l',    ($psa->total_length ?? '-'). ' мм');
        $document->setValue('b',    ($psa->width_collector ?? '-'). ' мм');
        $document->setValue('hh', ($this->requestData['series_calculate'] != 'wp' ? ($psa->height_with_locker ?? '-'). ' мм'  : ($psa->height_with_flap ?? '-'). ' мм'));
        $document->setValue('l1',   $l1. ' мм' ?? '');
        $document->setValue('l2',   ($psa->base_width ?? '-'). ' мм');
        $document->setValue('b1',   ($psa->base_length ?? '-'). ' мм');
        $document->setValue('h1',   ($psa->height_collector ?? '-'). ' мм');

        $document->setValue('dn1',  'ДУ '.($psa->diameter_collector ?? '-'));
        $document->setValue('dn2',  'ДУ '.($psa->diameter_collector ?? '-'));

        $document->setValue('h2',   $h2). ' мм';



        $document->setValue('wight',   $this->result[0]['series'][$this->series]['wight']. ' кг'  ?? '');
        $document->setValue('series',  $this->getTitle($this->collectPumpData,  $this->series, 'series'));

        $document->setValue('description',   $description);
        $document->setValue('description_control',   $description_control);
        $document->setValue('version', Config::get('project-pump.version'));
        $document->setValue('data_base', Carbon::now()->format('d.m.Y'));

        $file_name = '13232';

        $file_url = $dir . '/' . $file_name . '.docx';
        $document->saveAs($file_url);
        // foreach ($temporary as $key => $value) {
        //     Storage::disk('local')->delete($value);
        // }
        //!Вернуть для сервера
        \Boot\Jobs\PdfJob::dispatchSync($file_url, $file_name . '.pdf');
        $request = [
            'name'          => 'Расчет насосной станции №' . $this->requestId,
            //!Вернуть для сервера
            'url'           => $dirClear . '/' . $file_name . '.pdf',
            // 'url'           => $dirClear . '/' . $file_name . '.docx',
            'pid'           => $this->requestId,
            'module'        => 'request',
            'access'        => '1',
        ];
        $attach = Attach::create($request);
        \Request\Model\Request::find($this->requestId)->update(['aid' => $attach->id]);

        return $attach->id;
    }

    /**
     * Сохранение НС в каталоге
     *
     * @param 	string    	$sid ID расчета
     * @param 	string    	$aid ID файла расчета
     * @param 	int|null    $i   Ступень, если есть
     * @return 	int 	  	Возвращает id (cid) номенклатуры в базе
     */
    public function createCatalog($price, $aid, $i = 1)
    {
        $uid = Auth::id();
        $unit = '796';
        $category = '451';
        $man = '1';
        $sku = 'AE-NS-' . $this->requestId;
        $active = '1';
        $active_guest = '0';
        $active_user = '1';
        $title =  $this->getTitle($this->collectPumpData,  $this->series, 'all');
        $short = 'Насосная станция повышения давления';
        switch ($this->series) {
            case 'wp':
                $preview = '/files/media/ns1.png';
                break;
            case 'wpb':
                $preview = '/files/media/ns2.png';
                break;
            case 'wpbs':
                $preview = '/files/media/ns3.png';
                break;
        }

        $attach = Attach::find($aid);
        $attach_id = [];
        if (!empty($attach)) {
            $attach_id[] = $attach->id;
        }
        $specTitle = 'Насосная станция повышения давления ' . $title;


        $specMark = $title;
        $specArray = ["1" => $specTitle, "2" => $specMark, "3" => "АТРИ-ЭНЕРГО"];


        $request = [
            'uid' => $uid,
            'sku' => $sku,
            'unit' => $unit,
            'title' => $specTitle,
            'fulltitle' => $specTitle,
            'short' => $short,
            'category' => $category,
            'man' => $man,
            'active' => $active,
            'active_guest' => $active_guest,
            'active_user' => $active_user,
            'preview' => $preview,
            'attach_id' => $attach_id,
            'spec' => $specArray,
            // 'weight' => $weightArray,
        ];

        $catalog = Catalog::create($request);

        $this->setCatalogPrice(3, $catalog->id, $price, $uid);


        $this->setCatalogPrice(1, $catalog->id, $price, $uid);
        \Request\Model\Request::find($this->requestId)->update(['final_cid' => $catalog->id]);
        return $catalog->id;
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
            'module' => 'request',
            'pid' => $this->requestId,
            'active' => 1,
        ]);
        if (!empty($price)) {
            CatalogPriceDetail::create([
                'price_id'     => $price->id,
                'cid'         => $cid,
                'sum'         => $sum,
            ]);
            CatalogPriceDaemon::key_create();
            return true;
        }
        return false;
    }
}
