<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Notifications\Channels\Telegram;

use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update as UpdateObject;
use Telegram\Bot\Events\UpdateWasReceived;

class TelegramApi extends Api
{
    public function getWebhookUpdateCallback($shouldEmitEvent = true,  UpdateObject $update): UpdateObject
    {
        $callbackData = $update->callbackQuery->data;
        $rowBody = [
            'update_id' => $update->update_id,
            'message' => $update->getMessage(),
        ];
        $rowBody['message']['text'] =  '/' . $callbackData;
        $rowBody['message']['entities'] = ['offset' => 0, 'length' =>  strlen($callbackData), 'type' => "bot_command"];
        $rowBody['message']['reply_markup'] = null;
       
        $body = $rowBody;

        $update = new UpdateObject($body);

        if ($shouldEmitEvent) {
            $this->emitEvent(new UpdateWasReceived($update, $this));
        }

        return $update;
    }
}