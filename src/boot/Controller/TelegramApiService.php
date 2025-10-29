<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Telegram Api Controller
*/

namespace Boot\Controller;

use \Boot\Abstracts\Controllers\ApiController;
use \Boot\System\Notifications\Channels\Telegram\TelegramApi;
use Telegram\Bot\Keyboard\Keyboard;
use \Boot\Support\Facades\Config;
use \Boot\Models\Users;
use \Boot\Models\UsersToken;
use Illuminate\Support\Facades\Mail;


class TelegramApiService extends ApiController
{

    public function getUpdates()
    {
        $telegram = new TelegramApi(Config::get('services.telegram-bot-api.token'));

        $updates = $telegram->getWebhookUpdate();
        $chatId = $updates->getMessage()->getChat()->getId();
        $botId = $telegram->getMe()->getId();
        $userToken = UsersToken::firstOrCreate(
            [
                'token' =>  $chatId,
                'platform' =>  'telegram',
                'device_id' => $botId,

            ],
            [
                'uid' => null,
                'status' => 0,
                'veritification_code' => $this->getVerCode(),
            ]
        );
        if ($userToken->wasRecentlyCreated === true) {
            $telegram->addCommands([
                \Boot\System\Bots\Telegram\Commands\StartCommand::class,
            ]);
            $telegram->commandsHandler(true);
        } else {
            $sendMessage = '';
            $reply_markup = null;
            $message = $updates->getMessage()->getText();
            if (empty($userToken->uid) || $userToken->status == 0) {
                if (empty($userToken->uid)) {
                    if (filter_var($message, FILTER_VALIDATE_EMAIL)) {
                        $user = Users::where('email', $message)->first();
                        if (!empty($user)) {
                            $user_name = $user->name;
                            $sendMessage = $user_name . ', мы отправили на вашу почту проверочной код, пожалуйста напишите его в ответном сообщении!';
                            try {
                                Mail::to($user->email)->send(new \Boot\Mails\TelegramRegister($userToken->veritification_code));
                            } catch (\Throwable $th) {

                            }
                            // Mail::to($user->email)->send(new \Boot\Mails\TelegramRegister($userToken->veritification_code));
                            $userToken->update([
                                'uid' => $user->id,
                                'status' => 0
                            ]);
                        } else {
                            $sendMessage = 'Мы не нашли пользователя с такой электронной почтой или он еще не активен. Пожалуйста укажите правильную почту или зарегистрируйтесь на сайте.';
                        }
                    } else {
                        $sendMessage = 'Укажите ваш E-mail. Например bot@example.com';
                    }
                } else {
                    if ($userToken->veritification_code == $message) {
                        $sendMessage = 'Спасибо, вы успешно привязали Telegram к вашему аккаунту!';
                        $userToken->update([
                            'status' => 1
                        ]);
                    } else {
                        $sendMessage = 'Вы ввели неверный проверочный код, введите код еще раз.';
                        $reply_markup = Keyboard::make();
                        $reply_markup->inline();
                        $reply_markup->row(
                            Keyboard::inlineButton([
                                'text' => 'Отправить код повторно',
                                'callback_data' => 'send_code '
                            ]),
                            Keyboard::inlineButton([
                                'text' => 'Изменить почту',
                                'callback_data' => 'change_email '
                            ])
                        );
                    }
                }
                if ($updates->detectType() == 'callback_query') {
                    $telegram->addCommands([
                        \Telegram\Bot\Commands\HelpCommand::class,
                        \Boot\System\Bots\Telegram\Commands\ChangeEmailCommand::class,
                        \Boot\System\Bots\Telegram\Commands\SendEmailCodeCommand::class,
                    ]);
                    $telegram->commandsHandler(true);
                    if ($updates->detectType() == 'callback_query') {
                        $callbackData = $updates->callbackQuery->data;
                        $command = strstr($callbackData, ' ', true);
                        $up =  $telegram->getWebhookUpdateCallback(true, $updates);
                        $telegram->triggerCommand($command, $up);
                    }
                } else {
                    $telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => $sendMessage,
                        'reply_markup' => $reply_markup
                    ]);
                }
            } else {
                $telegram->addCommands(config('bots.telegram.commands', []));
                $telegram->commandsHandler(true);
                if ($updates->detectType() == 'callback_query') {
                    $callbackData = $updates->callbackQuery->data;
                    $command = strstr($callbackData, ' ', true);
                    $up =  $telegram->getWebhookUpdateCallback(true, $updates);
                    $telegram->triggerCommand($command, $up);
                }
            }
        }
    }

    public function getVerCode($length = 6)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
