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

use Illuminate\Contracts\Events\Dispatcher;
use Boot\System\Notifications\Notification;
use NotificationChannels\Telegram\Exceptions\CouldNotSendNotification;
use NotificationChannels\Telegram\Telegram;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Telegram\TelegramFile;
use GuzzleHttp\Client as HttpClient;
use Boot\Support\Facades\Config;

class TelegramChannel
{
    /**
     * @var Telegram
     */
    protected $telegram;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * Channel constructor.
     */
    public function __construct()
    {
        $this->telegram = new Telegram(
            Config::get('services.telegram-bot-api')['token'],
            app(HttpClient::class),
            null
        );;
        // $this->dispatcher = $dispatcher;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     *
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification): ?array
    {
        $message = $notification->toTelegram($notifiable);

        if (is_string($message)) {
            $message = TelegramMessage::create($message);
        }

        if ($message->toNotGiven()) {
            $to = $notifiable->routeNotificationFor('telegram', $notification)
                ?? $notifiable->routeNotificationFor(self::class, $notification);

            if (!$to) {
                return null;
            }

            $message->to($to);
        }

        if ($message->hasToken()) {
            $this->telegram->setToken($message->token);
        }

        $params = $message->toArray();

        $sendMethod = str_replace('Telegram', 'send', array_reverse(explode('\\', get_class($message)))[0]);

        try {
            if ($message instanceof TelegramMessage) {
                if ($message->shouldChunk()) {
                    $replyMarkup = $message->getPayloadValue('reply_markup');

                    if ($replyMarkup) {
                        unset($params['reply_markup']);
                    }

                    $messages = $this->chunk($message->getPayloadValue('text'), $message->chunkSize);

                    $payloads = collect($messages)->filter()->map(function ($text) use ($params) {
                        return array_merge($params, ['text' => $text]);
                    });

                    if ($replyMarkup) {
                        $lastMessage = $payloads->pop();
                        $lastMessage['reply_markup'] = $replyMarkup;
                        $payloads->push($lastMessage);
                    }

                    return $payloads->map(function ($payload) {
                        $response = $this->telegram->sendMessage($payload);

                        // To avoid rate limit of one message per second.
                        sleep(1);

                        if ($response) {
                            return json_decode($response->getBody()->getContents(), true);
                        }

                        return $response;
                    })->toArray();
                }

                $response = $this->telegram->sendMessage($params);
            } elseif ($message instanceof TelegramFile) {
                $response = $this->telegram->sendFile($params, $message->type, $message->hasFile());
            } elseif (method_exists($this->telegram, $sendMethod)) {
                $response = $this->telegram->{$sendMethod}($params);
            } else {
                return null;
            }
        } catch (CouldNotSendNotification $exception) {
            // $this->dispatcher->dispatch(new NotificationFailed(
            //     $notifiable,
            //     $notification,
            //     'telegram',
            //     []
            // ));

            throw $exception;
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Chunk the given string into an array of strings.
     */
    public function chunk(string $value, int $limit = 4096): array
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return [$value];
        }

        if ($limit >= 4097) {
            $limit = 4096;
        }

        $output = explode('%#TGMSG#%', wordwrap($value, $limit, '%#TGMSG#%'));

        // Fallback for when the string is too long and wordwrap doesn't cut it.
        if (count($output) <= 1) {
            $output = mb_str_split($value, $limit, 'UTF-8');
        }

        return $output;
    }
}