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


use Boot\Models\Attach;
use Boot\Support\Carbon;
use Boot\System\Database\DB;
use Object\Model\ObjectModel;
use Boot\Support\Facades\Auth;
use Boot\Support\Facades\File;
use Boot\Support\Facades\Config;
use Boot\Support\Facades\Storage;
use Boot\Abstracts\Actions\Action;
use Organizations\Model\Organizations;
use Project\Tasks\Calculation\Pump\PumpGraphTask;
use Project\Tasks\Calculation\Pump\PumpCalculationTask;
use Project\Tasks\Calculation\Pump\PumpCollectDataTask;
use Project\UI\API\Requests\Calculation\PumpCalculationRequest;


class ApiPumpRequestAction extends Action
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
        $this->collectPumpData = $result['catalog'];
        $this->calcResult = $result['calc'];
        $this->setPump($this->requestData['cid']);
        $this->createRequestAfter();

        $res['type'] = 'success';
        $res['aid'] = $this->createCalculationAttach();
        $res['request'] = $this->requestId;
        return ['data' => $res];
    }
    public $pump;
    public $requestId;
    public $requestData;
    public $collectPumpData;
    public $calcResult;



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
        if ($this->requestData['pump_switch'] == 1) {
            $tpl = modules_path('project/Resources/Templates/pump_multi.docx');
        } else {
            $tpl = modules_path('project/Resources/Templates/pump.docx');
        }

        $document =  new \PhpOffice\PhpWord\TemplateProcessor($tpl);
        $pumpData = $this->collectPumpData[0];
        $spec = $pumpData['spec'];
        $attribute = $pumpData['attribute'];
        $weight = $pumpData['weight'];
        if (isset($spec[1], $spec[2])) {
            $title = $spec[1] . ' ' . $spec[2];
        } else {
            $title = $pumpData['title'];
        }
        $title = $pumpData['title'];




        $module = 'request';
        $directory = 'files' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $this->requestId;
        File::makeDirectory(ROOT . $directory, 0777, true);

        $dirClear = 'files/' . $module . '/' . $this->requestId;
        $dir = ROOT . $dirClear;

        $file_url_graph = $dir . '/graph.png';
        $file_url_graph_multi = $dir . '/graph_multi.png';


        $graph = app(PumpGraphTask::class)->run([
            'id' => $this->pump,
            'q' => $this->requestData['q'],
            'h' => $this->requestData['h'],
            'work_mark' => $this->requestData['work_mark'],
            'priority' => 1,
            'direction' =>  $file_url_graph,
        ]);

      



        $document->setImageValue('graph', array('path' => $file_url_graph, 'width' => $graph['width'] * 0.55, 'height' => $graph['height'] * 0.55, 'ratio' => false));

        if ($this->requestData['pump_switch'] == 1) {
            $graph_multi = app(PumpGraphTask::class)->run([
                'id' => $this->pump,
                'q' => $this->requestData['q_all'],
                'h' => $this->requestData['h'],
                'work_mark' => $this->requestData['work_mark'],
                'pump_switch' => $this->requestData['pump_switch'],
                'pump_count' => $this->requestData['pump_count'],
                'pump_reserve' => $this->requestData['pump_reserve'],
                'pump_reserve_count' => $this->requestData['pump_reserve_count'],
                'priority' => 1,
                'direction' =>  $file_url_graph_multi,
            ]);
            $document->setImageValue('graph_multi', array('path' => $file_url_graph_multi, 'width' => $graph_multi['width'], 'height' => $graph_multi['height'], 'ratio' => false));
        }


        $cert = DB::table('cert_relation')
            ->leftJoin('cert', 'cert.id', '=', 'cert_relation.cert_id')
            ->leftJoin('attach', 'cert.aid', '=', 'attach.id')
            ->where('cert_relation.cid', $this->pump)
            ->whereIn('cert.type', [12, 13])
            ->get()
            ->toArray();
        // var_dump($cert);
        $temporary = [];
        $connectEmpty = true;
        $schemaEmpty = true;
        if (!empty($cert)) {
            foreach ($cert as $v) {
                // var_dump($v->url);
                // var_dump(Storage::exists($v->url));
                if (Storage::exists($v->url)) {

                    $temporary[] = $name = $directory . DIRECTORY_SEPARATOR . basename($v->url);
                    Storage::disk('local')->put($name, Storage::get($v->url), 'public');
                    if ($v->type == 12) {
                        $document->setImageValue('connect', array('path' =>  Storage::disk('local')->path($name), 'width' => 175, 'height' => 130, 'ratio' => false));
                        $connectEmpty = false;
                    } else {
                        $document->setImageValue('schema', array('path' => Storage::disk('local')->path($name), 'width' => 300, 'height' => 180, 'ratio' => false));
                        $schemaEmpty = false;
                    }
                }
            }
        }
        if ($connectEmpty) {
            $document->setValue('connect', '');
        }
        if ($schemaEmpty) {
            $document->setValue('schema', '');
        }



        //заполнение заданных параметров
        $current_data = [];
        $current_data[] = ['current_name' => 'Производительность',          'current_value' => $this->requestData['q'] . ' m³/h'];
        $current_data[] = ['current_name' => 'Напор',                       'current_value' => $this->requestData['h'] . 'm'];
        $current_data[] = ['current_name' => 'Перекачиваемая жидкость',     'current_value' => 'Вода 100%'];
        $current_data[] = ['current_name' => 'Т перекачиваемой жидкости',   'current_value' => '20.00 °C'];
        $current_data[] = ['current_name' => 'Плотность',                   'current_value' => '998.20 kg/m³'];
        $current_data[] = ['current_name' => 'Кинематическая вязкость',     'current_value' => '1.00 mm²/s'];
        $document->cloneRowAndSetValues('current_name', $current_data);
        //Заполнение расчетных данных
        $result_data = [];
        $result_data[] = ['result_name' => 'Производительность',            'result_value' => round($this->calcResult[$this->pump]['superX'], 2)];
        $result_data[] = ['result_name' => 'Напор',                         'result_value' => round($this->calcResult[$this->pump]['superY'], 2)];
        $document->cloneRowAndSetValues('result_name', $result_data);
        //Заполнение данных продукта
        $product_data = [];
        if (isset($attribute[Config::get('project-pump.attributes.pressure')])) {
            $product_data[] = ['product_name' => 'Max. рабочее давление',   'product_value' => $attribute[Config::get('project-pump.attributes.pressure')]];
        }
        if (isset($attribute[Config::get('project-pump.attributes.t')])) {
            $product_data[] = ['product_name' => 'Т перекач. жидкости',     'product_value' => $attribute[Config::get('project-pump.attributes.t')]];
        }
        if (isset($attribute[Config::get('project-pump.attributes.tMaxEnv')])) {
            $product_data[] = ['product_name' => 'Макс. Температура окр. Среды',   'product_value' => $attribute[Config::get('project-pump.attributes.tMaxEnv')]];
        }
        $document->cloneRowAndSetValues('product_name', $product_data);
        //Заполнение данных мотора
        $motor_data = [];
        if (isset($attribute[Config::get('project-pump.attributes.voltage')])) {
            $motor_data[] = ['motor_name' => 'Подключение к сети',      'motor_value' => $attribute[Config::get('project-pump.attributes.voltage')]];
        }
        if (isset($attribute[Config::get('project-pump.attributes.power')])) {
            $motor_data[] = ['motor_name' => 'Ном. Мощность P2',        'motor_value' => $attribute[Config::get('project-pump.attributes.power')]];
        }
        if (isset($attribute[Config::get('project-pump.attributes.amperage')])) {
            $motor_data[] = ['motor_name' => 'Потребление тока',        'motor_value' => $attribute[Config::get('project-pump.attributes.amperage')]];
        }
        $document->cloneRowAndSetValues('motor_name', $motor_data);
        //Заполнение данных по размерам
        $size_data = [];
        if (isset($attribute[Config::get('project-pump.attributes.dn_in')])) {
            $size_data[] = ['size_name' => 'Патрубок на стороне всас.',         'size_value' => $attribute[Config::get('project-pump.attributes.dn_in')]];
        }
        if (isset($attribute[Config::get('project-pump.attributes.dn_out')])) {
            $size_data[] = ['size_name' => 'Патрубок на напорн. стороне DNd',   'size_value' => $attribute[Config::get('project-pump.attributes.dn_out')]];
        }
        if (isset($attribute[Config::get('project-pump.attributes.length')])) {
            $size_data[] = ['size_name' => 'Габаритная длина',                  'size_value' => $attribute[Config::get('project-pump.attributes.length')]];
        }
        $document->cloneRowAndSetValues('size_name', $size_data);
        //Данные материалов
        $material_data = [];
        if (isset($attribute[Config::get('project-pump.attributes.frame')])) {
            $material_data[] = ['material_name' => 'Корпус насоса',   'material_value' => $attribute[Config::get('project-pump.attributes.frame')]];
        }
        if (isset($attribute[Config::get('project-pump.attributes.wheel')])) {
            $material_data[] = ['material_name' => 'Рабочее колесо',   'material_value' => $attribute[Config::get('project-pump.attributes.wheel')]];
        }
        if (isset($attribute[Config::get('project-pump.attributes.shaft')])) {
            $material_data[] = ['material_name' => 'Вал',   'material_value' => $attribute[Config::get('project-pump.attributes.shaft')]];
        }
        if (isset($attribute[Config::get('project-pump.attributes.bearing')])) {
            $material_data[] = ['material_name' => 'Материал подшип.',   'material_value' => $attribute[Config::get('project-pump.attributes.bearing')]];
        }
        $document->cloneRowAndSetValues('material_name', $material_data);

        $object_name = 'Не указано';
        if (isset($this->requestData['object'])) {
            $objectModel = ObjectModel::find($this->requestData['object']);
            if (!empty($objectModel)) {
                $object_name = $objectModel->name;
            }
        }

        $oid_name = 'Не указано';
        if (isset($this->requestData['oid'])) {
            $oidModel = Organizations::find($this->requestData['oid']);
            if (!empty($oidModel)) {
                $oid_name = $oidModel->name;
            }
        }
        if ($this->requestData['pump_switch'] == 1) {
                $document->setValue('dn_in', $attribute[Config::get('project-pump.attributes.dn_in')] ?? '');
                $document->setValue('dn_out', $attribute[Config::get('project-pump.attributes.dn_out')] ?? '');
                $document->setValue('h', $this->requestData['h'].' m');
                $document->setValue('q', $this->requestData['q_all'].' m³/h');
                $document->setValue('speed', '');
                $document->setValue('frequency', '50 Hz');
        }

        $document->setValue('title', $title);
        $document->setValue('project_numb',  $this->requestId);
        $document->setValue('project_name',  'Расчет насосного оборудования №' . $this->requestId);
        $document->setValue('project_date',  Carbon::now()->format('d.m.Y H:i'));
        $document->setValue('object_name',  $object_name);
        $document->setValue('oid_name',  $oid_name);
        $document->setValue('email',  Auth::user()->email);
        $document->setValue('sku', $pumpData['sku']);
        $document->setValue('weight', $weight[1]);
        $document->setValue('version', Config::get('project-pump.version'));
        $document->setValue('data_base', Carbon::now()->format('d.m.Y'));
        $document->setImageValue('graph', array('path' => $dir . '/graph.png', 'width' => 377, 'height' => 264, 'ratio' => false));
        if (Config::has('project-pump.manufacture_logo.' . $pumpData['man'])) {
            $pump_logo = modules_path('project/Resources/Templates/Logo/' . Config::get('project-pump.manufacture_logo.' . $pumpData['man']));
            $document->setImageValue('pump_logo', array('path' => $pump_logo, 'width' => 125, 'height' => 53, 'ratio' => false));
        } else {
            $document->setValue('pump_logo', '');
        }



        $file_name = '13232';

        $file_url = $dir . '/' . $file_name . '.docx';
        $document->saveAs($file_url);
        // foreach ($temporary as $key => $value) {
        //     Storage::disk('local')->delete($value);
        // }
        \Boot\Jobs\PdfJob::dispatchSync($file_url, $file_name . '.pdf');
        $request = [
            'name'          => 'Расчет насосного оборудования №' . $this->requestId,
            'url'           => $dirClear . '/' . $file_name . '.pdf',
            'pid'           => $this->requestId,
            'module'        => 'request',
            'access'        => '1',
        ];
        $attach = Attach::create($request);
        \Request\Model\Request::find($this->requestId)->update(['aid' => $attach->id]);

        return $attach->id;
    }
}
