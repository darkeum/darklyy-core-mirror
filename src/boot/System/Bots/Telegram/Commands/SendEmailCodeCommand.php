<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description SendEmailCode Command
*/

namespace Boot\System\Bots\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use \Boot\Models\UsersToken;
use \Boot\Models\Users;
use Illuminate\Support\Facades\Mail;

class SendEmailCodeCommand extends Command
{
    /**
     * @var string Имя команды
     */
    protected $name = "send_code";

    /**
     * @var string Описание команды
     */
    protected $description = "Повторная отправка кода";

    /**
     * Исполнитель команды
     *
     * @return void
     */
    public function handle()
    {
        $telegram = $this->getTelegram();
        $updates = $telegram->getWebhookUpdate();
        $chatId = $updates->getMessage()->getChat()->getId();
        $botId = $telegram->getMe()->getId();
        $usersToken = UsersToken::where('platform', 'telegram')->where('device_id', $botId)->where('status', 0)->where('token', $chatId)->whereNotNull('uid')->first();
        if(!empty($usersToken)){
            $user = Users::find($usersToken->uid);
            Mail::to($user->email)->send(new \Boot\Mails\TelegramRegister($usersToken->veritification_code));  
            $text = 'Код выслан повторно!';
        } else {
            $text = 'Вы уже зарегистрированы!';
        }
        $this->replyWithMessage([
            'text' => $text
        ]);   
    }
}
