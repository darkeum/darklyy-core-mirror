<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Boot\Abstracts\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;
use Boot\System\Notifications\Messages\ViberMessage;

class SuccessConnection extends Notification implements ShouldQueue
{

    use Queueable;

    protected $scada = null;
    protected $message = null;

    public function __construct($scada)
    {
        $this->scada = $scada;
        $this->message = 'Подключение к объекту диспетчеризации #' . $this->scada->id . ' (' . $this->scada->title . ') восстановлено!';
    }

    public function toArray($notifiable)
    {
        return [
            // ...
        ];
    }

    /**
     * Указание каналов рассылки
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['viber', 'telegram'];
    }

    /**
     * Получение уведомления в Viber
     *
     * @param  mixed $notifiable
     * @return \Boot\System\Notifications\Messages\ViberMessage
     */
    public function toViber($notifiable)
    {
        $token = $notifiable->token->where('platform', 'viber')->first()->token;
        if (!empty($token)) {
            return ViberMessage::create()->to($token)
                ->content($this->message);
        } else {
            return ViberMessage::create();
        }
    }

    /**
     * Получение уведомления в Telegram
     *
     * @param  mixed $notifiable
     * @return void
     */
    public function toTelegram($notifiable)
    {
        $userToken = $notifiable->token->where('platform', 'telegram')->first();
        if (!empty($userToken)) {
           
            $token = $userToken->token;
            return TelegramMessage::create()
                ->to($token)
                ->content($this->message);
        } else {
            return TelegramMessage::create();
        }
    }
}
