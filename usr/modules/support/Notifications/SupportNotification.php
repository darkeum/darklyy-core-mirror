<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Support\Notifications;

use \Illuminate\Bus\Queueable;
use \Boot\Support\Facades\Config;
use \Boot\System\Notifications\Notification;
use \Illuminate\Contracts\Queue\ShouldQueue;
use \NotificationChannels\Telegram\TelegramMessage;
use \Boot\System\Notifications\Messages\MailMessage;
use \Boot\System\Notifications\Messages\ViberMessage;

class SupportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $support = '';
    public $text = '';
    public $transferData = '';
    public $confirmationId = '';
    public $confirmationCode = '';

    /**
     * Создание экземпляра сообщения
     *
     * @return void
     */
    public function __construct($support)
    {
        $this->support = $support;
        $this->setText();
    }

    public function setText()
    {
        $this->text = "";
    }

    /**
     * Указание каналов рассылки
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['telegram', 'viber'];
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
            $support_url = Config::get('app.url') . '/administration/module/support/' . $this->support->id;
            return TelegramMessage::create()
                ->to($token)
                ->content($this->text)
                ->button('Перейти в заявку',  $support_url);
        } else {
            return TelegramMessage::create();
        }
    }

    /**
     * Получение уведомления на почту
     *
     * @param  mixed  $notifiable
     * @return \Boot\System\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', '')
            ->line('Thank you for using our application!');
    }

    /**
     * Получение уведомления в Viber
     *
     * @param  mixed $notifiable
     * @return \Boot\System\Notifications\Messages\ViberMessage
     */
    public function toViber($notifiable)
    {
        $userToken = $notifiable->token->where('platform', 'viber')->first()->token;
        if (!empty($userToken)) {
            $token = $userToken->token;
            $support_url = Config::get('app.url') . '/administration/module/support/' . $this->support->id;
            return ViberMessage::create()
                ->to($token)
                ->content($this->text . ' (' . $support_url . ')');
        } else {
            return ViberMessage::create();
        }
    }

    /**
     * Получение уведомления в массиве
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
