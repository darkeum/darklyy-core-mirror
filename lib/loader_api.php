<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
*/

//подключаем автоматический загрузчик классов

if (isset($_SERVER['REQUEST_URI'])) {
	$urlArray = explode('/', $_SERVER['REQUEST_URI']);
	if ($urlArray[1] == 'api') {
		$type = $urlArray[2];
		switch ($type) {
			default:
				$api = true;
				$api_version = preg_replace("/[^0-9]/", '', $urlArray[2]);
				$api_services = false;
				if ($urlArray[2] == 'services') {
					$api_services = true;
				}
				//подключаем и инициализируем загрузчик
				// require ROOT . 'lib/loader_core.php';
				break;
			case "app":
				
				$coreOnly = true;
				// $api = true;
				//подключаем и инициализируем загрузчик
                // require ROOT . 'lib/loader_core.php';
				$appApi = new \Api\App\AppMain();
				// header('Access-Control-Allow-Origin: *');
				// header("Content-Type: application/json");
				echo $appApi->getResult();
				break;
		}
	} else {
		if (isset($_GET['type'])) {
			$coreOnly = true;
			//подключаем и инициализируем загрузчик
			// require ROOT . 'lib/loader.php';
			$type = $_GET['type'];
			switch ($type) {
				default:
					$text = json_decode($text, true);
					break;
				case "app":
					require_once ROOT . 'api/app/index.php';
					$appApi = new appApi();
					
					echo $appApi->getResult();
					break;
			}
		}
	}
}