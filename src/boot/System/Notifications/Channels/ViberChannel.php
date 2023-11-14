<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/


namespace Boot\System\Notifications\Channels;

use \Boot\System\Notifications\Notification;
use \Boot\Support\Facades\Config;
use \Viber\Bot;
use \Viber\Api\Sender;
use \Viber\Client;

class ViberChannel
{
    /**
     * @var Viber
     */
    protected $viber;

    /**
     * @var Sender
     */
    private $sender; 
    
    /**
     * @var Client
     */
    private $client;
      
    /**
     * Конструктор канала уведомлений
     *
     * @return void
     */
    public function __construct()
    {
        $config = Config::get('services.viber-bot-api');
        $this->viber = new Bot(['token' => $config['token']]);
        $this->sender = new Sender([
            'name' => $config['name'] ?? 'Bot',
            'avatar' => $config['avatar'] ?? null
        ]);
        $this->client = $this->viber->getClient();
    }

    /**
     * Отправка полученного сообщения
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * 
     */
    public function send($notifiable, Notification $notification): ?array
    {
        if(method_exists($notification, 'toViber')){
            $message = $notification->toViber($notifiable);
            $params = $message->toArray();
            return  $this->client->sendMessage(
                (new \Viber\Api\Message\Text())
                ->setSender( $this->sender)
                ->setReceiver($params['chat_id'])
                ->setText($params['text'])
            )->getData();
        } 
    }
}
