<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Notifications;

use Illuminate\Queue\SerializesModels;

/**
 * Не обязательно обвяленные методы и переменные
 * @method object toViber()
 * @method object toTelegram()
 * @method object toMail()
 * @method object viaQueues()
 * @method object middleware()
 * @method object failed()
 * @method object backoff()
 * @method object retryUntil()
 * @property any $queue
 * @property any $connection
 * @property any $delay
 * @property any $middleware
 * @property any $backoff
 * @property any $tries
 * @property any $timeout
 * @property any $afterCommit
 * @property any $retryUntil
 */

class Notification
{
    use SerializesModels;

    /**
     * Уникальный идентификатор уведомления.
     *
     * @var string
     */
    public $id;

    /**
     * Языковой стандарт, который будет использоваться при отправке уведомления.
     *
     * @var string|null
     */
    public $locale;

    /**
     * Получите каналы, на которых должно транслироваться мероприятие.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    /**
     * Установите языковой стандарт для отправки этого уведомления.
     *
     * @param  string  $locale
     * @return $this
     */
    public function locale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function via($notifiable)
    {
        return [];
    }
}
