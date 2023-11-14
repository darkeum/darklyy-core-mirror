<?php
    use Workerman\Worker;
//echo dirname(__FILE__);
	require_once("/var/www/izhclient0077/data/www/atri-energo.ru/vendor/autoload.php");
    
   // массив для связи соединения пользователя и необходимого нам параметра
$users = [];
$task = [];

// создаём ws-сервер, к которому будут подключаться все наши пользователи
$ws_worker = new Worker("websocket://atri-energo.ru:4444");
// создаём обработчик, который будет выполняться при запуске ws-сервера
$ws_worker->onWorkerStart = function() use (&$users, &$task)
{
    // создаём локальный tcp-сервер, чтобы отправлять на него сообщения из кода нашего сайта
    $inner_tcp_worker = new Worker("tcp://atri-energo.ru:1234");
    // создаём обработчик сообщений, который будет срабатывать,
    // когда на локальный tcp-сокет приходит сообщение
    $inner_tcp_worker->onMessage = function($connection, $data) use (&$users, &$task) {
	
        $data = json_decode($data, true);
		if(isset($data['module']))
		{			
			print_r($data['module']);
			//print_r($data);
			if (is_array($data['user']))
			{
				foreach($data['user'] as $key => $val)
				{	
					print_r('Сообщение отправлено: '.$data['module'].'_'.$val);
					if (isset($users[$data['module'].'_'.$val]))
					{
						$webconnection = $users[$data['module'].'_'.$val];
						$webconnection->send($data['message']);
						print_r('Сообщение отправлено: '.$data['module'].'_'.$val);
					}
				} 
			}
			else
			{
				//print_r('Сообщение отправлено: '.$data['module'].'_'.$data['user'].' : '.$users[$data['module'].'_'.$data['user']]);
				if (isset($users[$data['module'].'_'.$data['user']])) {
					$webconnection = $users[$data['module'].'_'.$data['user']];
					$webconnection->send($data['message']);
				//	print_r('Сообщение отправлено: '.$data['module'].'_'.$data['user'].' : '.$users[$data['module'].'_'.$data['user']]);
				}
			}
		}
		else
		{
			print_r($data);				
			//print_r( $data );
			if (is_array($data['user']))
			{
				foreach($data['user'] as $key => $val)
				{
						print_r($val);
					if (isset($users[$val]))
					{
						$webconnection = $users[$val];
						$webconnection->send($data['message']);
					}
					print_r($data['message']);
				} 
			}
			else
			{
				// отправляем сообщение пользователю по userId
				if (isset($users[$data['user']])) {
					$webconnection = $users[$data['user']];
					$webconnection->send($data['message']);
				}
			}
		}
    };
    $inner_tcp_worker->listen();
};

$ws_worker->onConnect = function($connection) use (&$users, &$task)
{
    $connection->onWebSocketConnect = function($connection) use (&$users, &$task)
    {
		if(isset($_GET['module']))
		{
			$users[$_GET['module'].'_'.$_GET['user']] = $connection;
			print_r('Пользователь авторизован: '.$_GET['module'].'_'.$_GET['user']);
		}
		else
		{
			// при подключении нового пользователя сохраняем get-параметр, который же сами и передали со страницы сайта
			$users[$_GET['user'].'_'.$_GET['task']] = $connection;
			// вместо get-параметра можно также использовать параметр из cookie, например $_COOKIE['PHPSESSID']
		}
    };
};

$ws_worker->onClose = function($connection) use(&$users, &$task)
{
    // удаляем параметр при отключении пользователя
    $user = array_search($connection, $users);
    unset($users[$user]);	
};

// Run worker
Worker::runAll();