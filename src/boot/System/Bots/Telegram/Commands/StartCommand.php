<?php

namespace Boot\System\Bots\Telegram\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use \Boot\Models\UsersToken;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected string $name = "start";

    /**
     * @var string Command Description
     */
    protected string $description = "Start Command to get you started";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $telegram = $this->getTelegram();
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $this->replyWithMessage(['text' => 'Добрый день, вас приветствует бот '.(env('APP_NAME') == 'AE' ? 'АТРИ-ЭНЕРГО' : 'ПРИС').', здесь вы можете узнать состояние вашего заказа, прошла ли оплата по счету, посмотреть ваши объекты и еще много других вещей. Теперь вам нужно привязать ваш аккаунт на сайте компании '.(env('APP_NAME') == 'AE' ? 'АТРИ-ЭНЕРГО' : 'ПРИС').' к вашу Telegram. Для этого напишите в ответном сообщение вашу почту указанную на сайте. Если вы еще не зарегистрированы, то обязательно сделайте это!']);
    }
}
