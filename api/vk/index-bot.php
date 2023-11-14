<?php


function getVerCode($length = 6) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
	
	define('CALLBACK_API_EVENT_CONFIRMATION', 'confirmation');
	define('CALLBACK_API_EVENT_MESSAGE_NEW', 'message_new');
	
	require_once 'config.php';
	require_once 'global.php';
	
	require_once 'api/vk_api.php';
	require_once 'api/yandex_api.php';
	
	require_once 'bot/bot.php';
	
	if (!isset($_REQUEST)) {
		exit;
	}
	
	callback_handleEvent();
	
	function callback_handleEvent() {
		$event = _callback_getEvent();
		
		try {
			switch ($event['type']) {
				//Подтверждение сервера
				case CALLBACK_API_EVENT_CONFIRMATION:
				_callback_handleConfirmation();
				break;
				
				//Получение нового сообщения
				case CALLBACK_API_EVENT_MESSAGE_NEW:
				_callback_handleMessageNew($event['object']);
				break;
				
				default:
				_callback_response('Unsupported event');
				break;
			}
			} catch (Exception $e) {
			log_error($e);
		}
		
		_callback_okResponse();
	}
	
	function _callback_getEvent() {
		return json_decode(file_get_contents('php://input'), true);
	}
	
	function _callback_handleConfirmation() {
		_callback_response(CALLBACK_API_CONFIRMATION_TOKEN);
	}
	
	function _callback_handleMessageNew($data) {
		global $core, $db, $config;
		$client_id = $data['user_id'];
		$message = $data['body'];
		$send = '';
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_users_token WHERE platform = 'vk' AND token = '".$client_id."' LIMIT 1");	
		if($db->numRows($query) <= 0)
		{
			$users_get_response = vkApi_usersGet($client_id);
			$user = array_pop($users_get_response);
			$msg = "Добрый день, {$user['first_name']}!<br> Вас приветствует бот АТРИ-ЭНЕРГО, здесь вы можете узнать состояние вашего заказа, прошла ли оплата по счету, посмотреть ваши объекты и еще много других вещей. Теперь вам нужно привязать ваш аккаунт на сайте компании АТРИ-ЭНЕРГО к аккаунту Вконтакте. Для этого напишите в ответном сообщение вашу почту указанную на сайте. Если вы еще не зарегистрированы, то обязательно сделайте это!";			
			$db->query("INSERT INTO `" . DB_PREFIX . "_users_token` ( `id` , `uid`, `token`,  `platform`, `device_id`, `date`, `veritification_code`, `status`)  VALUES (NULL, NULL, '".$client_id."', 'vk', '0', '".date('Y-m-d H:i:s')."', '".getVerCode()."', '0');");	
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_users_token WHERE platform = 'vk' AND token = '".$client_id."' LIMIT 1");	
			$result = $db->getRow($query);
		}
		else
		{
			$result = $db->getRow($query);
		}			
		if (empty($result['uid']))
		{
			if(filter_var($message, FILTER_VALIDATE_EMAIL))
			{
				$query_user = $db->query("SELECT * FROM ".DB_PREFIX."_users WHERE email = '".$db->safesql($message)."' AND active != '0' LIMIT 1");
				if($db->numRows($query_user) <= 0)
				{
					$msg = 'Мы не нашли пользователя с такой электронной почтой или он еще не активен. Пожалуйста укажите правильную почту или зарегистрируйтесь на сайте.';
				}
				else
				{
					$result_user = $db->getRow($query_user);
					$user_name = $result_user['name'].' '.$result_user['ochestvo'];						
					$regMsg = 'Здравствуйте, <b>' . $user_name . '</b>!' . "<br /><br />";
					$regMsg .= 'Вы получили это письмо, так как поступил запрос на привязку вконтакте к вашему аккаунту ' . $config['name'] . "<br />";
					$regMsg .= 'Если этот запрос поступил не от Вас, просто проигнорируйте это письмо и удалите его. В случае повторения данного запроса обратить за помощью к администрации сайта.' . "<br /><br />";
					$regMsg .= 'Чтобы завершить процесс привязки вконтакте, напишите в чате данный код: ' . $result['veritification_code'] ."<br />";
					$regMsg .= '---' . "<br />";
					$regMsg .= '<i>С уважением, администрация ' . $config['name'] . "</i><br />";
					sendMail($result_user['email'], 'Подтверждение привязки вконтакте к вашему аккаунту' . $config['name'], $regMsg);
					$msg = $user_name.', мы отправили на вашу почту проверочной код, пожалуйста напишите его в ответном сообщении!';
					$db->query("UPDATE `" . DB_PREFIX . "_users_token` SET `uid` = '".$result_user['id']."' WHERE platform = 'vk' AND token = '".$client_id."';");
				}					
			}
			else
			{
				$msg = 'Укажите ваш E-mail. Например bot@atri-energo.ru';
			}
		}
		else
		{
			$typу = "text";
			if ($result['status'] == '0')
			{
				if($result['veritification_code'] == $message)
				{
					$msg = 'Спасибо, вы успешно привязали вконтакте к вашему аккаунту! Для получения списка команд напишите: "Помощь"';
					$db->query("UPDATE `" . DB_PREFIX . "_users_token` SET `status` = '1' WHERE platform = 'vk' AND token = '".$client_id."';");
				}
				else
				{
					if($message == 'Отправить еще раз')
					{
						$query_user = $db->query("SELECT * FROM ".DB_PREFIX."_users WHERE id = '".$result['uid']."' AND active != '0' LIMIT 1");
						if($db->numRows($query_user) <= 0)
						{
							$msg = 'Что-то пошло не так!';
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
							$msg = $user_name.', мы отправили на вашу почту проверочной код, пожалуйста напишите его в ответном сообщении!';
						}			
					}
					else
					{
						$msg = 'Вы ввели неверный проверочный код, если вам не пришло сообщение на почту напишите в ответ: "Отправить еще раз"';	
					}
				}
			}
			else
			{
				switch (mb_strtoupper($message)) {
					default:
					$msg = 'Неизвестная команда. Для получения списка команд напишите: "Помощь"';
					break;
					case 'ПОМОЩЬ':
					$msg = 'К сожалению на данный момент я могу только показать вам контакты нашей компании для этого напишите "Контакты", но не смотря на это вы будете получать все важные уведомления. Со временем я стану более умным и смогу выполнять больше команд.';
					break;
					case 'КОНТАКТЫ':
					$msg = 'Наши Контакты: Адрес: г. Ижевск, ул. Салютовская, 5а, E-mail: atri-energo@yandex.ru, Телефон:+7 (3412) 908-123';
					break;	
					case 'ТЕСТ':
					$type = 'url';
					$msg = 'Наши Контакты: Адрес: г. Ижевск, ул. Салютовская, 5а, E-mail: atri-energo@yandex.ru, Телефон:+7 (3412) 908-123';
					break;	
					
				}
			}
		}
		vkApi_messagesSend($client_id, $msg);
		_callback_okResponse();
	}
	
	function _callback_okResponse() {
		_callback_response('ok');
	}
	
	function _callback_response($data) {
		echo $data;
		exit();
	}
	
	
