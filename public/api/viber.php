<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

//подключаем основные константы 
require '../../define.php';
header('Access-Control-Allow-Origin: *');
//маркер чистой загрузки без роутинга и шаблонизатора
$coreOnly = true;
//подключаем и инициализируем загрузчик
require ROOT . 'lib/loader.php';

use Viber\Bot;
use Viber\Api\Sender;

$apiKey = '491c0a3663e7d14d-a7c84835f8b1809f-21bfe84ad2fa640a';

function getVerCode($length = 6) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$botSender = new Sender([
    'name' => 'АТРИ-БОТ',
    'avatar' => config('app.url').'/files/avatars/bot.png',
]);

try {
	$log = '';
    $bot = new Bot(['token' => $apiKey]);
    $bot
		->onConversation(function ($event) use ($bot, $botSender) {
        return (new \Viber\Api\Message\Text())
            ->setSender($botSender)
            ->setText("Добрый день, вас приветствует бот АТРИ-ЭНЕРГО, здесь вы можете узнать состояние вашего заказа, прошла ли оплата по счету, посмотреть ваши объекты и еще много других вещей. Теперь вам нужно привязать ваш аккаунт на сайте компании АТРИ-ЭНЕРГО к вашу вайберу. Для этого напишите в ответном сообщение вашу почту указанную на сайте. Если вы еще не зарегистрированы, то обязательно сделайте это!");
		})
		->onSubscribe(function ($event) use ($bot, $botSender, $log) {
            // $log->info('onSubscribe handler');
            $this->getClient()->sendMessage(
                (new \Viber\Api\Message\Text())
                    ->setSender($botSender)
                    ->setText('Спасибо что подписались! ')
            );
        })
		->onText('|.*|s', function ($event) use ($bot, $botSender) {	
			global $core, $db, $config;
			$client_id = $event->getSender()->getId();
			$message = $event->getMessage()->getText();
			$send = '';
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_users_token WHERE platform = 'viber' AND token = '".$client_id."' LIMIT 1");	
			if($db->numRows($query) <= 0)
			{
				$db->query("INSERT INTO `" . DB_PREFIX . "_users_token` ( `id` , `uid`, `token`,  `platform`, `device_id`, `date`, `veritification_code`, `status`)  VALUES (NULL, '-1', '".$client_id."', 'viber', '0', '".date('Y-m-d H:i:s')."', '".getVerCode()."', '0');");	
				$query = $db->query("SELECT * FROM ".DB_PREFIX."_users_token WHERE platform = 'viber' AND token = '".$client_id."' LIMIT 1");	
				$result = $db->getRow($query);
			}
			else
			{
				$result = $db->getRow($query);
			}
			if ($result['uid'] == '-1')
			{
				if(filter_var($message, FILTER_VALIDATE_EMAIL))
				{
					$query_user = $db->query("SELECT * FROM ".DB_PREFIX."_users WHERE email = '".$db->safesql($message)."' AND active != '0' LIMIT 1");
					if($db->numRows($query_user) <= 0)
					{
						$send = 'Мы не нашли пользователя с такой электронной почтой или он еще не активен. Пожалуйста укажите правильную почту или зарегистрируйтесь на сайте.';
					}
					else
					{
						$result_user = $db->getRow($query_user);
						$user_name = $result_user['name'].' '.$result_user['ochestvo'];						
						$regMsg = 'Здравствуйте, <b>' . $user_name . '</b>!' . "<br /><br />";
						$regMsg .= 'Вы получили это письмо, так как поступил запрос на привязку viber к вашему аккаунту ' . $config['name'] . "<br />";
						$regMsg .= 'Если этот запрос поступил не от Вас, просто проигнорируйте это письмо и удалите его. В случае повторения данного запроса обратить за помощью к администрации сайта.' . "<br /><br />";
						$regMsg .= 'Чтобы завершить процесс привязки viber, напишите в чате данный код: ' . $result['veritification_code'] ."<br />";
						$regMsg .= '---' . "<br />";
						$regMsg .= '<i>С уважением, администрация ' . $config['name'] . "</i><br />";
						sendMail($result_user['email'], 'Подтверждение привязки viber к вашему аккаунту' . $config['name'], $regMsg);
						$send = $user_name.', мы отправили на вашу почту проверочной код, пожалуйста напишите его в ответном сообщении!';
						$db->query("UPDATE `" . DB_PREFIX . "_users_token` SET `uid` = '".$result_user['id']."' WHERE platform = 'viber' AND token = '".$client_id."';");
					}					
				}
				else
				{
					$send = 'Укажите ваш E-mail. Например bot@atri-energo.ru';
				}
			}
			else
			{
				$type = "text";
				if ($result['status'] == '0')
				{
					if($result['veritification_code'] == $message)
					{
						$send = 'Спасибо, вы успешно привязали viber к вашему аккаунту! Для получения списка команд напишите: "Помощь"';
						$db->query("UPDATE `" . DB_PREFIX . "_users_token` SET `status` = '1' WHERE platform = 'viber' AND token = '".$client_id."';");
					}
					else
					{
						if($message == 'Отправить еще раз')
						{
							$query_user = $db->query("SELECT * FROM ".DB_PREFIX."_users WHERE id = '".$result['uid']."' AND active != '0' LIMIT 1");
							if($db->numRows($query_user) <= 0)
							{
								$send = 'Что-то пошло не так!';
							}
							else
							{
								$result_user = $db->getRow($query_user);
								$user_name = $result_user['name'].' '.$result_user['ochestvo'];						
								$regMsg = 'Здравствуйте, <b>' . $user_name . '</b>!' . "<br /><br />";
								$regMsg .= 'Вы получили это письмо, так как поступил запрос на привязку viber к вашему аккаунту ' . $config['name'] . "<br />";
								$regMsg .= 'Если этот запрос поступил не от Вас, просто проигнорируйте это письмо и удалите его. В случае повторения данного запроса обратить за помощью к администрации сайта.' . "<br /><br />";
								$regMsg .= 'Чтобы завершить процесс привязки viber, напишите в чате данный код: ' . $result['veritification_code'] ."<br />";
								$regMsg .= '---' . "<br />";
								$regMsg .= '<i>С уважением, администрация ' . $config['name'] . "</i><br />";
								sendMail($result_user['email'], 'Подтверждение привязки viber к вашему аккаунту' . $config['name'], $regMsg);
								$send = $user_name.', мы отправили на вашу почту проверочной код, пожалуйста напишите его в ответном сообщении!';
							}			
						}
						else
						{
							$send = 'Вы ввели неверный проверочный код, если вам не пришло сообщение на почту напишите в ответ: "Отправить еще раз"';	
						}
					}
				}
				else
				{
					switch (mb_strtoupper($message)) {
						default:
							
							if(strpos($message, config('app.url').'/') === false)
							{
								$send = 'Неизвестная команда. Для получения списка команд напишите: "Помощь"';
							}
							else
							{
								$send = '';
								$type = 'none';
							}
						break;
						case 'ПОМОЩЬ':
							$send = 'К сожалению на данный момент я могу только показать вам контакты нашей компании для этого напишите "Контакты", но не смотря на это вы будете получать все важные уведомления. Со временем я стану более умным и смогу выполнять больше команд.';
						break;
						case 'КОНТАКТЫ':
							$send = 'Наши Контакты: Адрес: г. Ижевск, ул. Салютовская, 5а, E-mail: atri-energo@yandex.ru, Телефон:+7 (3412) 908-123';
						break;	
						case 'ТЕСТ':
							$type = 'url';
							$send = 'Наши Контакты: Адрес: г. Ижевск, ул. Салютовская, 5а, E-mail: atri-energo@yandex.ru, Телефон:+7 (3412) 908-123';
						break;	
						
					}
				}
			}
			$receiverId = $event->getSender()->getId();
			switch ($type) {
				default:
					$bot->getClient()->sendMessage(
						(new \Viber\Api\Message\Text())
						->setSender($botSender)
						->setReceiver($event->getSender()->getId())
						->setText($send)
					);
				break;
				case 'none':
				break;
				case 'url':
				$bot->getClient()->sendMessage(
                          (new \Viber\Api\Message\CarouselContent())
                            ->setSender($botSender)
                            ->setReceiver($receiverId)
                            ->setButtonsGroupColumns(6)
                            ->setButtonsGroupRows(6)
                            ->setBgColor('#FFFFFF')
                            ->setButtons([
                                (new \Viber\Api\Keyboard\Button())
                                    ->setColumns(6)
                                    ->setRows(3)
                                    ->setActionType('open-url')
                                    ->setActionBody('https://www.google.com')
                                    ->setImage('https://i.vimeocdn.com/portrait/58832_300x300'),
                                (new \Viber\Api\Keyboard\Button())
                                    ->setColumns(6)
                                    ->setRows(2)
                                    ->setActionType('reply')
                                    ->setActionBody('https://www.google.com')
                                    ->setText('<span style="color: #ffffff; ">Buy</span>')
                                    ->setTextSize("large")
                                    ->setTextVAlign("middle")
                                    ->setTextHAlign("middle")
                                    ->setImage('https://user-images.githubusercontent.com/3382565/31753411-0be75dfc-b456-11e7-9eea-b976d21fcc53.png'),
								(new \Viber\Api\Keyboard\Button())
                                    ->setColumns(6)
                                    ->setRows(1)
                                    ->setActionType('reply')
                                    ->setActionBody('https://www.google.com')
                                    ->setText('<span style="color: #ffffff; ">Buy</span>')
                                    ->setTextSize("large")
                                    ->setTextVAlign("middle")
                                    ->setTextHAlign("middle")
                                    ->setImage('https://user-images.githubusercontent.com/3382565/31753411-0be75dfc-b456-11e7-9eea-b976d21fcc53.png')
                            ])
                    );
                    break;
				break;
			}
			
			
    })
    ->run();	
} catch (Exception $e) {
    // todo - log exceptions
	// $bot->getClient()->sendMessage(
	// 	(new \Viber\Api\Message\Text())
	// 	->setSender($botSender)
	// 	->setReceiver($event->getSender()->getId())
	// 	->setText($e->getMessage())
	// );
	
}