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

use Boot\Support\Str;
use Illuminate\Support\Collection;
use Boot\System\Database\Model\Model;
use Boot\System\Settings\Facades\Settings;
use Illuminate\Support\Traits\Localizable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Boot\System\Notifications\Events\NotificationSent;
use Boot\System\Notifications\Events\NotificationSending;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Collection as ModelCollection;

class NotificationSender
{
    use Localizable;

    /**
     * The notification manager instance.
     *
     * @var \Boot\System\Notifications\ChannelManager
     */
    protected $manager;

    /**
     * The Bus dispatcher instance.
     *
     * @var \Illuminate\Contracts\Bus\Dispatcher
     */
    protected $bus;

    /**
     * The event dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The locale to be used when sending notifications.
     *
     * @var string|null
     */
    protected $locale;

    /**
     * Create a new notification sender instance.
     *
     * @param  \Boot\System\Notifications\ChannelManager  $manager
     * @param  \Illuminate\Contracts\Bus\Dispatcher  $bus
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @param  string|null  $locale
     * @return void
     */
    public function __construct($manager, $bus, $events, $locale = null)
    {
        $this->bus = $bus;
        $this->events = $events;
        $this->locale = $locale;
        $this->manager = $manager;
    }

    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param  \Illuminate\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @return void
     */
    public function send($notifiables, $notification)
    {
        $notifiables = $this->formatNotifiables($notifiables);

        if ($notification instanceof ShouldQueue) {
            return $this->queueNotification($notifiables, $notification);
        }

        $this->sendNow($notifiables, $notification);
    }

    /**
     * Send the given notification immediately.
     *
     * @param  \Illuminate\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @param  array|null  $channels
     * @return void
     */
    public function sendNow($notifiables, $notification, array $channels = null)
    {
        $notifiables = $this->formatNotifiables($notifiables);
        $original = clone $notification;
        foreach ($notifiables as $notifiable) {
            if (empty($viaChannels = $channels ?: $notification->via($notifiable))) {
                continue;
            }
            $this->withLocale($this->preferredLocale($notifiable, $notification), function () use ($viaChannels, $notifiable, $original) {
                $notificationId = Str::uuid()->toString();
                foreach ((array) $viaChannels as $channel) {
                    if (!in_array($channel, config('notification.ignore-channel', []))) {
                        $eneble_chanel = Settings::getByUser($notifiable->id, 'notification_' . $channel, true);
                        if ($channel == 'mail') {
                            Settings::getByUser($notifiable->id, 'notification_' . $channel, false);
                        }
                        if ($eneble_chanel) {
                            if (!($notifiable instanceof AnonymousNotifiable && $channel === 'database')) {
                                $this->sendToNotifiable($notifiable, $notificationId, clone $original, $channel);
                            }
                        }
                    }
                }
            });
        }
    }


    /**
     * Get the notifiable's preferred locale for the notification.
     *
     * @param  mixed  $notifiable
     * @param  mixed  $notification
     * @return string|null
     */
    protected function preferredLocale($notifiable, $notification)
    {
        return $notification->locale ?? $this->locale ?? value(function () use ($notifiable) {
            if ($notifiable instanceof HasLocalePreference) {
                return $notifiable->preferredLocale();
            }
        });
    }

    /**
     * Send the given notification to the given notifiable via a channel.
     *
     * @param  mixed  $notifiable
     * @param  string  $id
     * @param  mixed  $notification
     * @param  string  $channel
     * @return void
     */
    protected function sendToNotifiable($notifiable, $id, $notification, $channel)
    {
        if (!$notification->id) {
            $notification->id = $id;
        }

        if (!$this->shouldSendNotification($notifiable, $notification, $channel)) {
            return;
        }

        $response = $this->manager->driver($channel)->send($notifiable, $notification);

        $this->events->dispatch(
            new NotificationSent($notifiable, $notification, $channel, $response)
        );
    }

    /**
     * Determines if the notification can be sent.
     *
     * @param  mixed  $notifiable
     * @param  mixed  $notification
     * @param  string  $channel
     * @return bool
     */
    protected function shouldSendNotification($notifiable, $notification, $channel)
    {
        if (
            method_exists($notification, 'shouldSend') &&
            $notification->shouldSend($notifiable, $channel) === false
        ) {
            return false;
        }

        return $this->events->until(
            new NotificationSending($notifiable, $notification, $channel)
        ) !== false;
    }

    /**
     * Queue the given notification instances.
     *
     * @param  mixed  $notifiables
     * @param  \Boot\System\Notifications\Notification  $notification
     * @return void
     */
    protected function queueNotification($notifiables, $notification)
    {
        $notifiables = $this->formatNotifiables($notifiables);

        $original = clone $notification;

        foreach ($notifiables as $notifiable) {
            $notificationId = Str::uuid()->toString();

            foreach ((array) $original->via($notifiable) as $channel) {
                $notification = clone $original;

                $notification->id = $notificationId;

                if (!is_null($this->locale)) {
                    $notification->locale = $this->locale;
                }

                $queue = $notification->queue;

                if (method_exists($notification, 'viaQueues')) {
                    $queue = $notification->viaQueues()[$channel] ?? null;
                }
                $eneble_chanel = Settings::getByUser($notifiable->id, 'notification_' . $channel, true);
                if ($channel == 'mail') {
                    Settings::getByUser($notifiable->id, 'notification_' . $channel, false);
                }
                if ($eneble_chanel) {
                    $this->bus->dispatch(
                        (new SendQueuedNotifications($notifiable, $notification, [$channel]))
                            ->onConnection($notification->connection)
                            ->onQueue($queue)
                            ->delay(
                                is_array($notification->delay) ?
                                    ($notification->delay[$channel] ?? null)
                                    : $notification->delay
                            )
                            ->through(
                                array_merge(
                                    method_exists($notification, 'middleware') ? $notification->middleware() : [],
                                    $notification->middleware ?? []
                                )
                            )
                    );
                }
            }
        }
    }

    /**
     * Format the notifiables into a Collection / array if necessary.
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
}
