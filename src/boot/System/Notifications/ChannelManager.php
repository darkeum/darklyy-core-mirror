<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace  Boot\System\Notifications;

use Boot\Support\Carbon;
use Boot\Support\Manager;
use \InvalidArgumentException;
use Notifications\Models\Notifications;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Bus\Dispatcher as Bus;
use Illuminate\Contracts\Notifications\Factory as FactoryContract;
use Boot\System\Notifications\Exceptions\MisingParametrNotification;
use Illuminate\Contracts\Notifications\Dispatcher as DispatcherContract;
use Illuminate\Database\Eloquent\Collection as ModelCollection;
use Illuminate\Support\Collection;
use Boot\System\Database\Model\Model;
use Boot\System\Settings\Facades\Settings;

class ChannelManager extends Manager implements DispatcherContract, FactoryContract
{
    /**
     * Канал уведомлений по умолчанию
     *
     * @var string
     */
    protected $defaultChannel = 'mail';

    /**
     * Языковой стандарт, используемый при отправке уведомлений.
     *
     * @var string|null
     */
    protected $locale;

    /**
     * При необходимости отформатируйте уведомления в коллекцию/массив.
     *
     * @param  mixed  $notifiables
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    protected function formatNotifiables($notifiables)
    {
        if (!$notifiables instanceof Collection && !is_array($notifiables)) {
            return $notifiables instanceof Model
                ? new ModelCollection([$notifiables]) : [$notifiables];
        }

        return $notifiables;
    }

    /**
     * Проверка на отсутсвие необходимых параметров
     *
     * @param  $notification
     * @param  string Имя параметра
     *
     * @throws  MisingParametrNotification
     * @return bool
     */
    protected function checkNotificationParametrs($notification, string $parametr): bool
    {
        if (empty($notification->$parametr)) {
            throw MisingParametrNotification::mising("Отсутствует необходимы параметр для уведомления `{$parametr}`");
        }
        return true;
    }

    /**
     * Создание записи об уведомлении в базе данных
     *
     * @param  mixed $notifiable
     * @param  mixed $notification
     * @return void
     */
    protected function createDatabeseNotification($notifiables, $notification, array $requred_parametr = ['title', 'text'])
    {
        if (isset($notification->database_notification) && $notification->database_notification) {
            $notifiables = $this->formatNotifiables($notifiables);
            foreach ($notifiables as $notifiable) {
                foreach ($requred_parametr as $value) {
                    $this->checkNotificationParametrs($notification, $value);
                }
                if (empty($notification->icon)) {
                    $notification->icon = config('app.url') . '/files/push/notification.png';
                }
                if (empty($notification->module)) {
                    $notification->module = 'other';
                }
                if (empty($notification->module_id)) {
                    $notification->module_id = null;
                }

                $notification_database = Notifications::create([
                    "uid" => $notifiable->id,
                    "url" => $notification->url ?? null,
                    "text" => $notification->text,
                    "title" => $notification->title,
                    "icon" => $notification->icon,
                    "date" => Carbon::now(),
                    "status" => 1,
                    "module" => $notification->module,
                    "pid" =>  $notification->module_id,
                ]);
                $notification->url_correct = route('admin.notifications.redirect', ['id' => $notification_database->id]);
                $notification->database_notification = false;
            }
        }
        return $notification;
    }

    /**
     * Отправка уведомления указанным пользователям.
     *
     * @param  \Illuminate\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @return void
     */
    public function send($notifiables, $notification)
    {
        $notification = $this->createDatabeseNotification($notifiables, $notification);
        (new NotificationSender(
            $this,
            $this->container->make(Bus::class),
            $this->container->make(Dispatcher::class),
            $this->locale
        )
        )->send($notifiables, $notification);
    }

    /**
     * Отправка уведомления немедленно.
     *
     * @param  \Illuminate\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @param  array|null  $channels
     * @return void
     */
    public function sendNow($notifiables, $notification, array $channels = null)
    {
        $notification = $this->createDatabeseNotification($notifiables, $notification);
        foreach ($channels as $value) {
            $eneble_chanel = Settings::get('notification_' . $value, true);
            if ($value == 'mail') {
                Settings::get('notification_' . $value, false);
            }
            if ($eneble_chanel) {
                (
                    new NotificationSender(
                        $this,
                        $this->container->make(Bus::class),
                        $this->container->make(Dispatcher::class),
                        $this->locale
                    )
                )->sendNow($notifiables, $notification, $channels);
            }
        }
    }

    /**
     * Получите экземпляр канала.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function channel($name = null)
    {
        return $this->driver($name);
    }

    /**
     * Создание экземпляра драйвера базы данных.
     *
     * @return \Boot\System\Notifications\Channels\DatabaseChannel
     */
    protected function createDatabaseDriver()
    {
        return $this->container->make(Channels\DatabaseChannel::class);
    }

    /**
     * Создайте экземпляр драйвера Broadcast передачи.
     *
     * @return \Boot\System\Notifications\Channels\BroadcastChannel
     */
    protected function createBroadcastDriver()
    {
        return $this->container->make(Channels\BroadcastChannel::class);
    }

    /**
     * Создание экземпляра драйвера E-mail.
     *
     * @return \Boot\System\Notifications\Channels\MailChannel
     */
    protected function createMailDriver()
    {
        return $this->container->make(Channels\MailChannel::class);
    }

    /**
     * Создание нового экземпляра драйвера.
     *
     * @param  string  $driver
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        try {
            return parent::createDriver($driver);
        } catch (InvalidArgumentException $e) {
            if (class_exists($driver)) {
                return $this->container->make($driver);
            }

            throw $e;
        }
    }

    /**
     * Получение имени драйвера канала по умолчанию.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->defaultChannel;
    }

    /**
     * Получение имени драйвера канала по умолчанию.
     *
     * @return string
     */
    public function deliversVia()
    {
        return $this->getDefaultDriver();
    }

    /**
     * Установка имени драйвера канала по умолчанию.
     *
     * @param  string  $channel
     * @return void
     */
    public function deliverVia($channel)
    {
        $this->defaultChannel = $channel;
    }

    /**
     * Устанеовка языка уведомления
     *
     * @param  string  $locale
     * @return $this
     */
    public function locale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
