<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description Websocket class
*/

//подключаем основные константы 
require dirname(__FILE__) . '/../define.php';
header('Access-Control-Allow-Origin: *');
//маркер чистой загрузки без роутинга и шаблонизатора
$coreOnly = true;
//подключаем и инициализируем загрузчик
require ROOT . 'lib/loader.php';


use Workerman\Worker;

//список клиентов
$client = [];

// создаём ws-сервер, к которому будут подключаться все наши пользователи
$ws_worker = new Worker("websocket://atri-energo.ru:4444");
$ws_worker->name = 'pusher';
// создаём обработчик, который будет выполняться при запуске ws-сервера
$ws_worker->onWorkerStart = function () use (&$users, &$task, &$client) {
	// создаём локальный tcp-сервер, чтобы отправлять на него сообщения
	$inner_tcp_worker = new Worker("tcp://atri-energo.ru:1234");
	$inner_tcp_worker->name = 'publisher';
	// создаём обработчик сообщений, который будет срабатывать,
	// когда на локальный tcp-сокет приходит сообщение
	$inner_tcp_worker->onMessage = function ($connection, $data) use (&$client) {

		$data = json_decode($data, true);
		dump($data);
		if (isset($data['message_type'])) {
			$message_type = $data['message_type'];
			switch ($message_type) {
				case 'scada':
					if (isset($client[$message_type]) && isset($client[$message_type][$data['scada_id']])) {
						foreach ($client[$message_type][$data['scada_id']] as $users) {
							foreach ($users as $wc) {
								$wc->send(json_encode($data['message'], JSON_UNESCAPED_UNICODE));
							}
						}
					}
					break;

				case 'chat':
					if (isset($data['message']) && isset($client[$message_type]) && isset($data['user'])) {
						$message = $data['message'];
						foreach ($data['user'] as $value) {
							if(isset($client[$message_type][$value])){
								foreach ($client[$message_type][$value] as $conn) {
									$conn->send($message);
								}
							}
						}
					}
					break;
			}
		}
	};
	$inner_tcp_worker->listen();
};

$ws_worker->onConnect = function ($connection) use (&$client) {
	$connection->onWebSocketConnect = function ($connection) use (&$client) {
		global $core, $db;
		if (isset($_GET['token']) && !empty($_GET['token'])) {
			$token = $_GET['token'];
			$q_str_token =  "SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users_token` WHERE token = '" . $db->safesql($token) . "' AND platform = 'app' AND status = '1'";
			$qq_token = $db->query($q_str_token);
			if ($db->numRows($qq_token) > 0) {
				$userInfo = $db->getRow($qq_token);
				$check = true;
				$core->auth->initUser($userInfo['uid']);
				if (isset($_GET['wss_type'])) {
					$wss_type = $_GET['wss_type'];
					switch ($wss_type) {
						case 'scada':
							$scada_id = $_GET['scada_id'];
							$client[$wss_type][$scada_id][$userInfo['uid']][] = $connection;
							break;

						case 'chat':							
							$client[$wss_type][$userInfo['uid']][] = $connection;
							break;
					}
				} 
			} else {
				$check = false;
			}
		} else {
			$client['guest']['connections'][] = $connection;
		}	
	};
};

$ws_worker->onClose = function ($connection) use (&$client) {
	foreach ($client as $key => $value) {
		switch ($key) {
			case 'scada':
				foreach ($value as $key_type => $value_type) {
					foreach ($value_type as $key_user => $value_user) {		
						$conn = array_search($connection, $value_user);	
						if ($conn !== false) {
							unset($client[$key][$key_type][$key_user][$conn]);
							if(empty($client[$key][$key_type][$key_user])){
								unset($client[$key][$key_type][$key_user]);
							}
							if(empty($client[$key][$key_type])){
								unset($client[$key][$key_type]);
							}
						}
					}
				}
				break;
			case "chat":		
				foreach ($value as $key_user => $value_user) {		
					$conn = array_search($connection, $value_user);	
					if ($conn !== false) {
						unset($client[$key][$key_user][$conn]);
						if(empty($client[$key][$key_user])){
							unset($client[$key][$key_user]);
						}
					}	
				}
							
				break;
		}
	}	
};

Worker::runAll();
