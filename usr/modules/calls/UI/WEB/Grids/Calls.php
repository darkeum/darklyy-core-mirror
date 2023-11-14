<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander
*/

namespace Calls\UI\WEB\Grids;

use Boot\Models\UsersPhoneid;
use Boot\Support\Carbon;
use Boot\Support\Facades\DB;
use Boot\Support\Facades\Auth;
use Boot\System\Grid\GridModern;
use DateInterval;
use DateTime;
use loophp\collection\Collection;
use Requests\Models\Request;

class Calls extends GridModern
{
    /**
     * Пространство имен
     *
     * @var string
     */
    protected $namespace = 'calls';

    private \Illuminate\Support\Collection $users;

    private \Illuminate\Support\Collection $calls;

    /**
     * Инициализация grid
     *
     * @return void
     */
    protected function init()
    {
        //Заголовок таблицы
        $this->title = __('calls::lang.admin.list');

        //Опции таблицы (в данном случае сортировка)
        $this->options = [
            'sort' => [
                'name' => 'id',
                'order' => 'DESC',
            ]
        ];

        //Опции панели навигации
        $this->nav = array(
            'export' => true,
            'export.detail' => [
                'excel' => true,
                'pdf' => true,
                'print' => true,
                'copy' => true,
                'csv' => false,
            ],
        );

        //Подключение к необходимой таблице
        $this->db = DB::table('calls as n');
        //if(auth()->id() == 1092) $this->db->where('id', 1);

        // Получение активных пользователей
        $this->users = DB::table('users')->where('active', true)->get();

        //Получаем список номеров
        $this->calls = DB::table('calls')->select('id', 'src', 'dst')->get();



        //Настройка колонок
        $this->cols = [

            'recordingfile' => [
                'title' => 'file',
                'db' => 'n.recordingfile',
                'visible' => false,
            ],

            'duration' => [
                'title' => 'duration',
                'db' => 'n.duration',
                'visible' => false,
            ],

            'file' => [
                'title' => 'filePath',
                'db' => 'n.file',
                'visible' => false,
            ],

            'id' => [
                'title' => '#',
                'db' => 'n.id',
                'width' => '1%',
                'className' => 'text-center',
                'icon' => 'featherhash',
            ],

            # Вызывающий абонемент
            'src' => [
              'title' => __('calls::lang.grid.src_title'),
                'db' => 'n.src',
                //'width' => '50%',
                'className' => 'text-center',
                //'render' => 'text',
                //'popover' => 'left',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getPhoneShortName('src'), 'Все')
                ],
            ],

            # Вызываемый абонемент
            'dst' => [
                'title' => __('calls::lang.grid.dst_title'),
                'db' => 'n.dst',
                'className' => 'text-center',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getPhoneShortName('dst'), 'Все')
                ],
            ],

            # Время звонка
            'calldate' => [
                'title' => __('calls::lang.grid.calldate_title'),
                'db' => 'n.calldate',
                'className' => 'text-center',
                'type' => 'date_range',
                'format_date' => "d.m.Y H:i",
                //'options' => $this->dateRangeSearch(),
            ],

            # Длительность звонка
            'billsec' => [
                'title' => __('calls::lang.grid.duration_title'),
                'db' => 'n.billsec',
                'className' => 'text-center'
            ],

            'waiting' => [
                'title' => 'Время ожидания',
                'db' => 'n.billsec',
                'className' => 'text-center'
            ],

            # Статус доступности аудио файла
            'status' => [
                'title' => trans('calls::lang.grid.status'),
                'db' => 'n.status',
                'className' => 'text-center',
                'encode' => false,
                'replace' => $this->getStatus(),
            ],

            # Действия
            'action' => [
                'type' => 'actions',
                'width' => '1%',
                'className' => 'text-center',
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search' => false,
                'excel_hidden' => true,

                // query sql
                'search_op' => false,
            ]
        ];
        $this->render_filter_toolbar = true;
    }

    /**
     * Подстановка и замена элементов в строке (подмена ячеек)
     *
     * @param array $row
     * @return array
     * @throws \Exception
     */
    protected function parseRow($row): array
    {
        $row['src'] = view('calls::manager.calls.grid.src', ['name' => $this->getShortUserName($row['src'])])->render();

        $row['dst'] = view('calls::manager.calls.grid.dst', ['name' => $this->getShortUserName($row['dst'])])->render();

        $row['calldate'] = date('d.m.Y H:i', $row['calldate']);

        $row['action'] = view('calls::manager.calls.grid.action', [
            'row' => $row,
            'show' => router("admin.calls.redirect", $row['id']),
            'media' => 'http://api.izhnet.ru/amocrm/asterisk/83/?GETFILE=',
        ])->render();

        $row['waiting'] = Carbon::parse((int) $row['duration'] - (int) $row['billsec'])->format('i минут, s секунд');

        $row['billsec'] = Carbon::parse((int) $row['billsec'])->format('i минут, s секунд');
        return $row;
    }

    /**
     * Получение короткого ФИО
     *
     * @param string $phone
     * @return string
     */
    private function getShortUserName(string $phone): string
    {
        $user = $this->users->filter(function($item) use ($phone) {
            if(str($phone)->startsWith('8') && str($phone)->length() == 11) {
                $phone = str($phone)->replaceFirst('8', '+7')->value();
            }
            return $item->phone == $phone;
        })->first();

        return $user ? user_short_name($user): $phone;
    }

    /**
     * Получаем список имен для src и dst
     *
     * @param string $groupBy
     * @return array
     */
    private function getPhoneShortName(string $groupBy): array
    {
        $prepareCalls = $this->calls->groupBy($groupBy)->transform(function ($call) {
            return $call->first();
        });

        return $prepareCalls->transform(function ($src) {
           $phone = $this->users->filter(function ($user) use ($src) {
               $phone = $src->src;
               if(str($phone)->startsWith('8') && str($phone)->length() == 11) {
                   $phone = str($phone)->replaceFirst('8', '+7')->value();
               }
                return $user->phone == $phone;
            })->first();

           return ($phone) ? user_short_name($phone) : $src->src;
        })->toArray();
    }

    private function getStatus(): array
    {
        return [
            0 => "<span class='d-block badge bg-warning rounded-pill' data-toggle='tooltip'>Не доступен</span>",
            1 => "<span class='d-block badge bg-success rounded-pill' data-toggle='tooltip'>Доступен</span>"
        ];
    }
}