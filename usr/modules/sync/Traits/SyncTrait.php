<?php

/**
 * @name        JMY CMS
 * @link        http://jmy.su/
 * @copyright   Copyright (C) 2012-2022 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */

namespace Sync\Traits;

use Carbon\Carbon;
use Boot\Support\Str;
use Sync\Models\SyncBase;
use Boot\Models\Relations;
use GuzzleHttp\Psr7\Message;
use Sync\Models\SyncRegister;
use Sync\Models\SyncRelation;
use Kily\Tools1C\OData\Client;
use Sync\UI\CLI\Commands\SyncCommand;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Orgs\Models\ChartAccounts;
use Symfony\Component\Console\Helper\ProgressBar;

trait SyncTrait
{

    use SyncErrors;

    //данные
    protected $syncData = array();
    protected $syncDataPrepare = array();
    protected $syncDataComplete = array();
    protected $syncBaseKey = false;
    // protected $syncModule = false;
    // protected $syncModuleModel = false;
    // protected $syncModuleTable = false;
    // protected $syncRestLink = false;
    protected $isRegister = true;
    public SyncCommand $console;

    /**
     * Инициализация клиента обмена
     *
     * @return void
     */
    public function initClient($base)
    {
        if (!empty($this->console->host)) {
            $host = $this->console->host;
        } else {
            $host = $base->host;
        }
        $this->Client = new Client($host, [
            'auth' => [
                $base->user,
                $base->pass
            ],
            'timeout' => 300,
        ]);
    }

    //заполнение данных
    public function setSyncData($request)
    {
        $this->syncData = $request;
    }

    //устанавливаем ид базы синхронизации
    public function setSyncBase($base)
    {
        $this->syncBaseKey = $base;
    }

    //устанавливаем ид базы синхронизации
    public function setRegister($bool)
    {
        $this->isRegister = $bool;
    }

    public function run(SyncCommand $console, SyncBase $base, array $value, $register = true, $method = ['add', 'update'])
    {
        $this->base = $base;
        $this->console = $console;
        $this->initClient($base);
        $this->setSyncData($value);
        $this->setRegister($register);
        $this->getItemsBase();
        $this->checkRelation();
        $this->checkRelationRest();
        $this->customCheck();
        $this->viewResult();
        $this->customSync();
        $this->syncAdapter($method);
        return $this->syncDataComplete;
    }



    /**
     * getItemsBase
     *
     * @return void
     */
    public function getItemsBase()
    {
        $chunk = array_chunk($this->syncData, 50, true);
        $section =  $this->console->commandOutput->section();
        $progressBar = new ProgressBar($section);
        $progressBar->setFormat(
            "%current%/%max% [%bar%] %percent:3s%% %message%"
        );
        $progressBar->setMessage('Извлечение данных из базы', 'message');
        $progressBar->start(count($chunk));
        foreach ($chunk as $chunkValue) {
            $items = $this->model::whereIn('id', $chunkValue)->get()->toArray();
            foreach ($chunkValue as $k => $v) {
                $dataKey = array_search($v, array_column($items, 'id'));
                if ($dataKey === false) {;
                    SyncRegister::find($k)->update(['status' => 3]);
                    unset($this->syncData[$k]);
                    $this->syncError('Элемент с регистрационным номером ' . $k . ' не найден в базе данных.');
                } else {
                    $this->syncDataPrepare[$k]['pid'] = $v;
                    $this->syncDataPrepare[$k]['data'] = $items[$dataKey];
                }
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        $section->clear();
    }

    public function customCheck()
    {
    }
    public function customSync()
    {
    }

    public function setRelationForBroken($syncData, $pid = null)
    {
    }


    /**
     * checkRelation
     *
     * @param  mixed $syncDataSpecial
     * @return void
     */
    public function checkRelation($syncDataSpecial = array())
    {
        $chunk = array_chunk($this->syncData, 50, true);
        $section =  $this->console->commandOutput->section();
        $progressBar = new ProgressBar($section);
        $progressBar->setFormat(
            "%current%/%max% [%bar%] %percent:3s%% %message%"
        );
        $progressBar->setMessage('Проверка наличия связей внутри базы', 'message');
        $progressBar->start(count($chunk));
        foreach ($chunk as $chunkValue) {
            $items = SyncRelation::where("module", $this->syncModule)
                ->where("sync_base", $this->base->id)
                ->whereIn("pid", $chunkValue)
                ->where("id_1c", "!=", "")
                ->where("status", 1)
                ->get()
                ->toArray();
            $result = [];
            foreach ($this->syncData as $k => $v) {
                $dataKey = array_search($v, array_column($items, 'pid'));
                if ($dataKey === false) {
                    $result['add'][$v]['register'] = $k;
                    $result['add'][$v]['pid'] = $this->syncDataPrepare[$k]['pid'];
                    $result['add'][$v]['data'] = $this->syncDataPrepare[$k]['data'];
                    $result['add'][$v]['relation'] = false;
                } else {
                    $result['update'][$v]['register'] = $k;
                    $result['update'][$v]['pid'] = $this->syncDataPrepare[$k]['pid'];
                    $result['update'][$v]['data'] = $this->syncDataPrepare[$k]['data'];
                    $result['update'][$v]['relation'] = $items[$dataKey]['id_1c'];
                }
            }
            $this->syncDataComplete = array_merge($this->syncDataComplete, $result);;
            $progressBar->advance();
        }
        $progressBar->finish();
        $section->clear();
    }

    /**
     * checkRelationRest
     *
     * @return void
     */
    public function checkRelationRest()
    {
        $data_check = array();
        if (isset($this->syncDataComplete['update'])) {
            $chunk = array_chunk($this->syncDataComplete['update'], 50, true);
            $section =  $this->console->commandOutput->section();
            $progressBar = new ProgressBar($section);
            $progressBar->setFormat(
                "%current%/%max% [%bar%] %percent:3s%% %message%"
            );
            $progressBar->setMessage('Проверка наличия связей по программе', 'message');
            $progressBar->start(count($chunk));
            foreach ($chunk as $key => $value) {
                $filter = [];
                foreach ($value as $k => $v) {
                    $filter[] = "Ref_Key eq guid'" . $v['relation'] . "'";
                }
                $dataTemp = $this->Client->{$this->syncRestLink}->filter(implode(' or ', $filter))->get()->values();
                $data_check = array_merge($data_check, $dataTemp);
                $progressBar->advance();
            }
            foreach ($this->syncDataComplete['update'] as $k => $v) {
                $dataKey = array_search($v['relation'], array_column($data_check, 'Ref_Key'));
                if ($dataKey === false) {
                    $this->syncDataComplete['add'][$k]['register'] = $v['register'];
                    $this->syncDataComplete['add'][$k]['pid'] = $v['pid'];
                    $this->syncDataComplete['add'][$k]['data'] = $v['data'];
                    $this->syncDataComplete['add'][$k]['relation'] = false;
                    $this->deleteSyncRelation($v['relation'], $this->base->id);
                    unset($this->syncDataComplete['update'][$k]);
                    $this->console->newLine(1);
                    $this->console->error('Элемент с регистрационным номером ' . $k . ' не найден в программе синхронизации');
                }
            }
            $progressBar->finish();
            $section->clear();
        }
    }


    /**
     * viewResult
     *
     * @return void
     */
    public function  viewResult()
    {
        // $this->console->info('Данные для синхронизации после проверок:');
        // $this->console->newLine(1);
        // $table['add'] = [
        //     'name' => 'Добавление',
        //     'count' => isset($this->syncDataComplete['add']) ? count($this->syncDataComplete['add']) : 0
        // ];
        // $table['update'] = [
        //     'name' => 'Изменение',
        //     'count' => isset($this->syncDataComplete['update']) ? count($this->syncDataComplete['update']) : 0,
        // ];
        // $this->console->table(['Действие', 'Количество'], $table);
        // $this->console->newLine(1);
    }











    //отметка об успешной выгрузке
    public function setIsSync($id)
    {
        $request = array(
            'status' => '2',
            'date_sync' => date('Y-m-d H:i:s'),
        );
        SyncRegister::find($id)->update($request);
    }

    //создание элемента
    public function restAdd($syncData, $pid, $register)
    {

        $this->Client->{$this->syncRestLink}->id('');

        try {
            $data_catalog = $this->Client->{$this->syncRestLink}->create($syncData);
            if (!empty($data_catalog)) {
                $sync_catalog_ref = $data_catalog->getLastId();
                $this->setSyncRelation($pid, $this->syncModule, $sync_catalog_ref, $this->base->id);
                if ($this->isRegister) {
                    $this->setIsSync($register);
                }
                return $sync_catalog_ref;
            } else {
                if ($this->isRegister) {
                    SyncRegister::find($register)->update(['status' => 3]);
                }
                $this->setRelationForBroken($syncData, $pid);
                var_dump(json_encode($syncData, JSON_UNESCAPED_UNICODE));
                // var_dump(json_encode($syncData));
                exit;
                // $this->syncError($this->Client->getHttpErrorMessage());
                // $this->syncError($this->Client->getErrorMessage());
                // $this->syncError($this->Client->getResponse());
                $this->syncError('Ошибка добавления ' . $this->syncRestLink . ' {' . $pid . '}');
            }
        } catch (RequestException $e) {
            if ($this->isRegister) {
                SyncRegister::find($register)->update(['status' => 3]);
            }
            dump(Message::toString($e->getResponse()));
            return null;
        }
    }


    //обновление элемента
    public function restUpdate($syncData, $relation, $register)
    {
        $this->Client->{$this->syncRestLink}->id('');
        try {
            $data_catalog = $this->Client->{$this->syncRestLink}->update($relation, $syncData);
            // dd($data_catalog);
            if ($this->isRegister) {
                $this->setIsSync($register);
            }
        } catch (RequestException $e) {
            if ($this->isRegister) {
                SyncRegister::find($register)->update(['status' => 3]);
            }
            dump(Message::toString($e->getResponse()));
            return null;
        }
        // dump($data_catalog);	
        // dump($syncData);
    }

    public function deleteSyncRelation($ref_key, $sync_base)
    {
        $item = SyncRelation::where("id_1c", $ref_key)->where("sync_base", $sync_base)->first();
        if (!empty($item)) {
            $item->delete();
        }
    }

    //создание или обновление ключа синхронизации
    public function setSyncRelation($id, $module, $ref_key, $sync_base)
    {
        $syncRelation = SyncRelation::firstOrCreate(
            [
                'module' => $module,
                'pid' => $id,
                'sync_base' => $sync_base,
            ],
            [
                'id_1c' => $ref_key,
                'date' => Carbon::now(),
                'status' => 1,
            ]
        );
        return $syncRelation->id_1c;
    }

    public function getStaticRelation($request)
    {
        $refKeyArray = array();
        $syncRelation = SyncRelation::where("sync_base", $this->base->id)->where(function ($query) use ($request) {
            foreach ($request as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $subV) {
                        $query->orWhere(function ($query) use ($k, $subV) {
                            $query->where("module", $k)->where("pid", $subV);
                        });
                    }
                } else {
                    $query->orWhere(function ($query) use ($k, $v) {
                        $query->where("module", $k)->where("pid", $v);
                    });
                }
            }
        });

        $items = $syncRelation->get()->toArray();
        if (!empty($items)) {
            foreach ($items as $k => $v) {
                $refKeyArray[$v['module']][$v['pid']] = $v['id_1c'];
            }
        }
        return $refKeyArray;
    }

    public function getChart($request)
    {
        $charts = ChartAccounts::whereIn('code', $request)->get();
        $charts_ref = [];
        foreach ($charts as $key => $value) {
            $relation = SyncRelation::where("sync_base", $this->base->id)->where("pid", $value->id)->where("module", 'chart')->first();
            $charts_ref[$value->code] = $relation->id_1c;
        }

        return $charts_ref;
    }

    public function prepareDate($date)
    {
        $datePrepare = date('Y-m-d H:i:s', strtotime($date));
        $dateArray = explode(' ', $datePrepare);
        $result = $dateArray['0'] . 'T' . $dateArray['1'];
        return $result;
    }

    public function getReestr($id)
    {
        $relations = Relations::where('module_in', 'reestr')->where('module_out', 'sales')->where('pid_out',  $id)->first();
        $reestr_id = false;
        if (!empty($relations)) {
            $reestr_id = $relations->pid_in;
        }
        return $reestr_id;
    }

    public function getSyncSpecial($request)
    {
        $result = [];
        foreach ($request as $k => $v) {
            $task = $this->getTaskClassName($k);
            if (class_exists($task)) {
                $result[$k]    = [];
                $resultTemp = app($task)->run($this->console, $this->base, $v, false, ['add']);

                foreach ($resultTemp as $cValue) {
                    $result[$k] = array_merge($result[$k], $cValue);
                }
            }
        }
        return $result;
    }


    /**
     * Получение имени класса задачи
     *
     * @param  string $r
     * @return string
     */
    protected function getTaskClassName($name)
    {
        return "\Sync\Tasks\\" . Str::ucfirst($name) . 'Task';
    }
}
