<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Sync\UI\CLI\Commands;

use Boot\Support\Carbon;
use Sync\Sync;
use Reestr\Models\Reestr;
use Sync\Models\SyncBase;
use Supply\Model\Receipts;
use Catalog\Models\Catalog;
use Sync\Actions\SyncAction;
use Sync\Models\SyncRelation;
use Kily\Tools1C\OData\Client;
use Storage\Model\StockTransfer;
use Realization\Model\Realization;
use Boot\System\Core\Abstracts\Commands\ConsoleCommand;
use CreateChartAccounts;
use Orgs\Models\ChartAccounts;

class SyncHelperCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'sync:helper
                            {base? : Идентификатор базы программы для синхронизации}
                            {--force : Принудительная работа с игнорированием ключевого файла}
                            {--once : Выполнить команду однократно}
                            {--host= : Имя хоста}';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Синхронизировать данные со внешними программами';

    public $commandOutput;
    public $sectionError;

    protected function getBase($base)
    {
        if (!empty($base)) {
            if (!empty($baseReturn = SyncBase::find($base))) {
                return [$baseReturn];
            } else {
                $this->error('Не найдена указанная программа для синхронизации');
            }
        } else {
            if (!empty($baseReturn = SyncBase::where('status', 1)->get())) {
                return $baseReturn;
            } else {
                $this->error('Отсутствуют программы для синхронизации');
            }
        }
    }




    //обработчик типа номенклатуры
    public function syncChart()
    {
        $typeList = array();
        $typeRest = $this->Client->{'ChartOfAccounts_Хозрасчетный'}->get()->values();
        // dd($typeRest);
        $typeGet = \Catalog_v2\Model\CatalogTypes::all()->toArray();
        foreach ($typeGet as $tValue) {
            $dataKey = array_search($tValue['title'], array_column($typeRest, 'Description'));
            if ($dataKey === false) {
                dump($tValue);
                $sync_unit = $this->Client->{'Catalog_ВидыНоменклатуры'}->create([
                    'Description' => $tValue['title'],
                    'Услуга' => (($tValue['service'] != 1) ? false : true),
                ]);

                // dd($sync_unit);
                // dd($typeRest);
                $syncRef = $sync_unit->getLastId();
                $this->setSyncRelation($tValue['id'], 'catalog_types', $syncRef, $this->syncBaseKey);
                $typeList[$tValue['id']]['Ref_Key'] = $syncRef;
                $typeList[$tValue['id']]['service'] = $tValue['service'];
            } else {
                $typeList[$tValue['id']]['Ref_Key'] = $typeRest[$dataKey]['Ref_Key'];
                $typeList[$tValue['id']]['service'] = $tValue['service'];
            }
        }
        $this->typeList = $typeList;
        return $typeList;
    }

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $sync_base = SyncBase::first();

        $client = new Client($sync_base->host, [
            'auth' => [
                $sync_base->user,
                $sync_base->pass
            ],
            'timeout' => 300,
        ]);
        $sync = new Sync();
        // $chart = $client->{'ChartOfAccounts_Хозрасчетный'}->get()->values();
        // $chart_result = ChartAccounts::all();
        // foreach ($chart_result as $key => $value) {
        //     $dataKey = array_search($value->code, array_column($chart, 'Code'));
        //     $sr = SyncRelation::where('id_1c', $chart[$dataKey]['Ref_Key'])->first();
        //     dump($value->id);
        //     dump($value->code);
        //     dump($chart[$dataKey]['Ref_Key']);
        //     if($chart[$dataKey]['Ref_Key'] != $sr->id_1c){
        //         dd('error');
        //     }

            // dd($value['Ref_Key']);
            // dd($value['Code']);
            // dd($value['Description']);
            // $response = [
            //     'code' => $value['Code'],
            //     'title' => $value['Description'],
            //     'status' => 1
            // ];
            // // dump($response);
            // $chart_result = ChartAccounts::create($response);
            // $response_relation = [
            //     'id_1c' => $value['Ref_Key'],
            //     'module' => 'chart',
            //     'pid' => $chart_result->id,
            //     'sync_base' => 1,
            //     'date' => Carbon::now(),
            // ];
            // SyncRelation::create($response_relation);
            // $sync->syncRegister('chart', $chart_result->id, 1, 1);
        // }
        // dd('');


        // $catalog = Catalog::all();
        // $sync = new Sync();
        // foreach ($catalog as $key => $value) {
        //     $sync->syncRegister('catalog_v2', $value->id, 1);
        // }
        // dd($sync_base);
        // foreach ($sync_base as $key => $value) {
        //     $sb[$value->org] = $value->id;
        // }

        // $catalog = Receipts::all();
        // $sync = new Sync();
        // foreach ($catalog as $key => $value) {
        //     $sync->syncRegister('receipts', $value->id, 1, 1);
        // }
        // dd($sync_base);



        foreach ($sync_base as $key => $value) {
            $sb[$value->org] = $value->id;
        }
        $data = SyncRelation::where('sync_base', 0)->where("pid", 507)->get();
        dd($data);
        $ct = [];
        foreach ($data  as $key => $value) {
            switch ($value->module) {
                case 'stock_transfer':
                    $transfer = StockTransfer::find($value->pid);
                    SyncRelation::find($value->id)->update(['sync_base' => $sb[$transfer->org]]);
                    dump($value->pid, $sb[$transfer->org]);
                    break;

                case 'realization':
                case 'realization_bill':
                    $transfer = Realization::find($value->pid);
                    SyncRelation::find($value->id)->update(['sync_base' => $sb[$transfer->org]]);
                    dump($value->pid, $sb[$transfer->org]);
                    break;


                case 'catalog_v2':
                    $ct[$value->pid][] = $value->id;
                    // $transfer = Realization::find($value->pid);
                    // SyncRelation::find($value->id)->update(['sync_base' => $sb[$transfer->org]]);
                    // dump($value->pid, $sb[$transfer->org]);
                    break;

                case 'organizations':
                    $oid[$value->pid][] = $value->id;
                    // $transfer = Realization::find($value->pid);
                    // SyncRelation::find($value->id)->update(['sync_base' => $sb[$transfer->org]]);
                    // dump($value->pid, $sb[$transfer->org]);
                    break;

                case 'reestr':
                    // $reestr[$value->pid][] = $value->id;
                    $reestr = Reestr::find($value->pid);
                    SyncRelation::find($value->id)->update(['sync_base' => $sb[$reestr->org]]);
                    dump($value->pid, $sb[$reestr->org]);
                    break;

                case 'receipts':
                case 'receipts_bill':
                    $reestr = Receipts::find($value->pid);
                    SyncRelation::find($value->id)->update(['sync_base' => $sb[$reestr->org]]);
                    dump($value->pid, $sb[$reestr->org]);
                    break;

                default:
                    # code...
                    break;
            }
        }
        dump($oid);
    }
}
