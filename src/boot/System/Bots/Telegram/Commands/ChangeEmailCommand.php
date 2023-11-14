<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description ChangeEmail Command
*/

namespace Boot\System\Bots\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use \Boot\Models\UsersToken;

class ChangeEmailCommand extends Command
{
    /**
     * @var string Имя команды
     */
    protected $name = "change_email";

    /**
     * @var string Описание команды
     */
    protected $description = "Изменение Email при регистрации";

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
        $usersToken = UsersToken::where('platform', 'telegram')->where('device_id', $botId)->where('status', 0)->where('token', $chatId)->first();
        if(!empty($usersToken)){
            $usersToken->update(['uid' => null]);
            $text = 'Укажите новый Email';
        } else {
            $text = 'Вы уже зарегистрированы!';
        }
        $this->replyWithMessage([
            'text' => $text
        ]);   
    }
}
