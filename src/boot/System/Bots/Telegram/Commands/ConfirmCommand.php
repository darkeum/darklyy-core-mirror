<?php

namespace Boot\System\Bots\Telegram\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use \Boot\Models\UsersToken;
use Telegram\Bot\Keyboard\Keyboard;

class ConfirmCommand extends Command
{
    /**
     * @var string Имя команды
     */
    protected $name = "confirm";

    /**
     * @var string Паттерн аргументов
     */
    protected $pattern = "{id}";

    /**
     * @var string Описание команды
     */
    protected $description = "Подтверждение действий";


    /**
     * Исполнитель команды
     *
     * @return void
     */
    public function handle()
    {
        $telegram = $this->getTelegram();
        $argument = $this->getArguments();
        if (!empty($argument['id'])) {
            $id = intval($argument['id']);
            $electronicConfirmation = \Boot\Models\ElectronicConfirmation::find($id);
            if(!empty($electronicConfirmation)){
                $updates = $telegram->getWebhookUpdate();
                $chatId = $updates->getMessage()->getChat()->getId();
                $botId = $telegram->getMe()->getId();
                $usersToken = UsersToken::where('platform', 'telegram')->where('device_id', $botId)->where('status', 1)->where('token', $chatId)->first();
                $uid =  $usersToken->uid;
                if($electronicConfirmation->uid == $uid){
                    if($electronicConfirmation->used == 0 && $electronicConfirmation->active == 0){
                        \Boot\Models\ElectronicConfirmation::find($id)->update(["active" => 1]);
                        $text = 'Действие подтверждено!';
                    } else {
                        $text = 'Данное действие уже было подтверждено!';
                    }
                  
                } else {
                    $text = 'У вас нет доступа к данному действию!';
                }
            } else {
                $text = 'Запись не найдена';
            }
        } else {
            $text = 'Не указан id подтверждения';
        }
        $this->replyWithMessage([
            'text' => $text
        ]);   
       
    }
}
