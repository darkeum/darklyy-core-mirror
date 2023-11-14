<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Notifications\UI\CLI\Commands;

use Boot\Support\Facades\Config;
use \Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class NotificationsCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'notifications:viber';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Уведомления Viber';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        while(1) {
            try{
                //проверка файла ключа
                if(\Boot\Models\NotifDaemon::key_check()) {			
                    \Boot\Models\NotifDaemon::key_delete();
                    //поиск записей для отправки
                    foreach(\Boot\Models\NotifDaemon::with("notif")->whereHas("notif", function($query){
                        $query->where("platform", "viber");
                    })->whereNull("deleted_at")->get() as $item){
                        $attr = [];
                        $attr['uid'] = $item->notif->uid;
                        $attr['text'] = $item->notif->text;
                        $attr['url'] = Config::get('app.url').'/administration/module/notif/redirect/'.$item->notif->id;	
                        $this->sendViberDemon($attr);
                        $item->delete();
                    }
                }
                // Время сна Демона между итерациями (зависит от потребностей системы)
                sleep(20); 
            } catch(\Exception $e){
                setLog('Error', '0', 'daemon', $e->getMessage());
                dump($e->getMessage());
            }
        }
    }

    public function sendViberDemon($attr){
        foreach(\Boot\Models\UsersToken::where("platform", "viber")->where("status", 1)->where("uid", $attr['uid'])->get() as $item){
            $Viber = new \vibers();
            $Viber->message_post(
                $item->token,
                [
                    'name' => 'АТРИ-БОТ', 
                    'avatar' => config('app.url').'/files/avatars/bot.png' 
                ],
                $attr['text'].' '.(empty($attr['url']) ? '' : $attr['url'])
            );	
        }
    }
}