<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\CLI\Commands;

use Boot\Models\Users;
use Scada\Models\Scada;
use Boot\Support\Carbon;
use Scada\Models\ScadaTags;
use Scada\Models\ScadaCrash;
use Scada\Models\ScadaUsers;
use Boot\Support\Facades\Config;
use Scada\Notifications\CrashScada;
use Boot\Support\Facades\Notification;
use Scada\Notifications\ErrorConnection;
use Scada\Notifications\SuccessConnection;
use Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class ScadaCrashCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'scada:crash';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Уведомления об аварии';

    /**
     * Получение списка пользователи
     *
     * @param  int $id
     * @return void
     */
    public function getUsers($id)
    {
        $users = [];
        $scada_users = ScadaUsers::where('scada_id', $id)->get();
        $scada_user_list = [];
        foreach ($scada_users as $value_u) {
            $scada_user_list[] = $value_u->uid;
        }
        if (!empty($scada_user_list)) {
            $users = Users::whereIn('id', $scada_user_list)->get();
        } else {
            $users = Users::whereIn('id', Config::get('scada.admin'))->get();
        }
        return $users;
    }


    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $scada = Scada::where('status', 1)->get();
        foreach ($scada as $value) {
            // if ($value->notifiable == 1) {
            //     if ($value->polling_at <= Carbon::now()->subMinutes(7)) {
            //         if ($value->notification_flag == 0) {
            //             Scada::find($value->id)->update(['notification_flag' => 1, 'connection' => 0]);
            //             if ($value->id != 5) {
            //                 Notification::send($this->getUsers($value->id), new ErrorConnection($value));
            //                 ScadaCrash::create([
            //                     'scada_id' => $value->id,
            //                     'tags_id' => null,
            //                     'type' => 'connection',
            //                     'description' =>  'Отсутствует подключение к объекту диспетчеризации #' . $value->id,
            //                 ]);
            //             }
            //         }
            //     } else {
            //         if ($value->notification_flag == 1) {
            //             Scada::find($value->id)->update(['notification_flag' => 0, 'connection' => 1]);
            //             if ($value->id != 5) {
            //                 Notification::send($this->getUsers($value->id), new SuccessConnection($value));
            //             }
            //         }
            //     }
            // }
            $scada_tags = ScadaTags::where('scada_id', $value->id)->where('status', 1)->where('notifiable', 1)->get();
            foreach ($scada_tags as $tag) {
                if ($tag->last_value == 1 && $tag->notification_flag == 0) {
                    Notification::send($this->getUsers($value->id), new CrashScada($value, $tag));
                    ScadaCrash::create([
                        'scada_id' => $value->id,
                        'tags_id' => $tag->id,
                        'type' => 'crash',
                        'description' => 'Обнаружена авария: ' . $tag->title,
                    ]);
                    ScadaTags::find($tag->id)->update(['notification_flag' => 1]);
                } elseif ($tag->last_value == 0 && $tag->notification_flag == 1) {
                    ScadaTags::find($tag->id)->update(['notification_flag' => 0]);
                }
            }
        }
    }
}
