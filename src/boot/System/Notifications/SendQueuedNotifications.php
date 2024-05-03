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

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SendQueuedNotifications implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Уведомляемые субъекты, которые должны получить уведомление.
     *
     * @var \Illuminate\Support\Collection
     */
    public $notifiables;

    /**
     * Уведомление для отправки.
     *
     * @var \Boot\System\Notifications\Notification
     */
    public $notification;

    /**
     * Все каналы для отправки уведомления.
     *
     * @var array
     */
    public $channels;

    /**
     * Сколько раз можно попытаться выполнить задание.
     *
     * @var int
     */
    public $tries;

    /**
     * Количество секунд, в течение которых задание может выполняться до истечения времени ожидания.
     *
     * @var int
     */
    public $timeout;

    /**
     *Указывает, должно ли задание быть зашифровано.
     *
     * @var bool
     */
    public $shouldBeEncrypted = false;

    /**
     * Создания нового экземпляра задания.
     *
     * @param  \Boot\System\Notifications\Notifiable|\Illuminate\Support\Collection  $notifiables
     * @param  \Boot\System\Notifications\Notification  $notification
     * @param  array|null  $channels
     * @return void
     */
    public function __construct($notifiables, $notification, array $channels = null)
    {
        $this->channels = $channels;
        $this->notification = $notification;
        $this->notifiables = $this->wrapNotifiables($notifiables);
        $this->tries = property_exists($notification, 'tries') ? $notification->tries : null;
        $this->timeout = property_exists($notification, 'timeout') ? $notification->timeout : null;
        $this->afterCommit = property_exists($notification, 'afterCommit') ? $notification->afterCommit : null;
        $this->shouldBeEncrypted = $notification instanceof ShouldBeEncrypted;
    }

    /**
     * Оберните уведомляемые объекты в коллекцию.
     *
     * @param  \Boot\Syeytem\Notifications\Notifiable|\Illuminate\Support\Collection  $notifiables
     * @return \Illuminate\Support\Collection
     */
    protected function wrapNotifiables($notifiables)
    {
        if ($notifiables instanceof Collection) {
            return $notifiables;
        } elseif ($notifiables instanceof Model) {
            return EloquentCollection::wrap($notifiables);
        }

        return Collection::wrap($notifiables);
    }

    /**
     * Отправить уведомления.
     *
     * @param  \Boot\System\Notifications\ChannelManager  $manager
     * @return void
     */
    public function handle(ChannelManager $manager)
    {
        $manager->sendNow($this->notifiables, $this->notification, $this->channels);
    }

    /**
     * Получить отображаемое имя задания в очереди.
     *
     * @return string
     */
    public function displayName()
    {
        return get_class($this->notification);
    }

    /**
     * Вызовите неудачный метод в экземпляре уведомления.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function failed($e)
    {
        if (method_exists($this->notification, 'failed')) {
            $this->notification->failed($e);
        }
    }

    /**
     * Получите количество секунд до того, как будет доступно выпущенное уведомление.
     *
     * @return mixed
     */
    public function backoff()
    {
        if (! method_exists($this->notification, 'backoff') && ! isset($this->notification->backoff)) {
            return;
        }

        return $this->notification->backoff ?? $this->notification->backoff();
    }

    /**
     * Получите срок действия уведомления.
     *
     * @return mixed
     */
    public function retryUntil()
    {
        if (! method_exists($this->notification, 'retryUntil') && ! isset($this->notification->retryUntil)) {
            return;
        }

        return $this->notification->retryUntil ?? $this->notification->retryUntil();
    }

    /**
     * Подготовьте экземпляр к клонированию.
     *
     * @return void
     */
    public function __clone()
    {
        $this->notifiables = clone $this->notifiables;
        $this->notification = clone $this->notification;
    }
}
