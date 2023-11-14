<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description ElectronicSignTransfer Notification
*/

namespace App\Notifications;

use \Illuminate\Bus\Queueable;
use \Illuminate\Contracts\Queue\ShouldQueue;
use \Boot\System\Notifications\Messages\MailMessage;
use \Boot\System\Notifications\Messages\ViberMessage;
use \Boot\System\Notifications\Notification;
use \NotificationChannels\Telegram\TelegramMessage;
use \Boot\Support\Facades\Config;
use Notifications\Models\Notifications;


class DefaultNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $notification_id = null;
    public $uid = null;
    public $text = null;
    public $url_correct = null;

    /**
     * Создание экземпляра сообщения
     *
     * @return void
     */
    public function __construct($id)
    {
        $item = Notifications::find($id);
        if ($item) {
            $this->uid = $item->uid;
            $this->text = $item->text;
            $this->url_correct = Config::get('app.url') . '/administration/module/notif/redirect/' . $id;
        }
    }

    /**
     * Указание каналов рассылки
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['telegram'];
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
            $url = $this->url_correct;            
            return TelegramMessage::create()
                ->to($token)
                ->content("$this->text")
                ->button('Открыть',  $url);
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
        $confirmationUrl = Config::get('app.url') . '/administration/module/storage/transfer/activeElectronicSign/' . $this->confirmationId;
        $token = $notifiable->token->where('platform', 'viber')->first()->token;
        return ViberMessage::create()->to($token)
            ->content("Код подтверждения перемещения №{$this->transferData['numb']}\nКод подтерждения: $this->confirmationCode\n$confirmationUrl");
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
