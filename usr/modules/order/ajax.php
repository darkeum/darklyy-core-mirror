<?php
global $db, $allowFuncs, $url, $core;

if (isset($allowFuncs)) {
	$allowFuncs = array_merge($allowFuncs, ['orderPartner', 'orderPump', 'createVer', "send_in_verification"]);
}

function orderPartner()
{
	global $db, $allowFuncs, $url, $core;
	$error_output = '';
	$success_output = '';
	$type = 'success';
	if (!empty($_POST['name']) && !empty($_POST['phone']) && !empty($_POST['message'])) {

		$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
		$recaptcha_secret = '6LfddtUZAAAAAOJmTgbOOIec5WaFIOnIrOqBFk7I';
		$recaptcha_response = $_POST['recaptcha_response'];
		$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
		$recaptcha = json_decode($recaptcha);
		$dataArray = array();
		$dataArray['type'] = 'partner';
		$dataArray['status'] = '1';
		if ($recaptcha->success == true && $recaptcha->score >= 0.5 && $recaptcha->action == 'contact') {
			if ($core->auth->user_id != '0') {
				$dataArray['surname'] = $core->auth->user_info['surname'];
				$dataArray['name'] = $core->auth->user_info['name'];
				$dataArray['ochestvo'] = $core->auth->user_info['ochestvo'];
				$dataArray['uid'] = $core->auth->user_id;
				$dataArray['phone'] = $core->auth->user_info['phone'];
			} else {
				$dadata = new \Dadata\DadataClient('ddeafb9ff43e79578243d8f031f6e9d8b047ca0e', 'e03a8f8ceaa114f8d8e0e09af92a0f3b069f1d40');
				$response = $dadata->clean("name", filter($_POST['name']));
				if ($response['qc'] == '0') {
					$dataArray['surname'] = $response['surname'];
					$dataArray['name'] = $response['name'];
					$dataArray['ochestvo'] = $response['patronymic'];
					$dataArray['fio_source'] = $response;
					$responsePhone = $dadata->clean("phone", filter($_POST['phone']));
					if ($responsePhone['qc'] != '2') {
						$dataArray['phone'] = $responsePhone['phone'];
						$dataArray['phone_source'] = $responsePhone;
					} else {
						$type = 'error';
						$error_output = "Пожалуйста укажите реальный номер телефон";
					}
				} else {
					$type = 'error';
					$error_output = "Пожалуйста укажите реальное имя";
				}
			}
			$dataArray['message'] = filter($_POST['message']);
			if ($type == 'success') {
				$order = new \Order\Models\Order;
				$order->create($dataArray);
				$success_output = 'Ваше сообщение успешно отправлено';
			}
		} else {
			$error_output = "Вы не прошли проверку на робота, попробуйте ещё раз! Если ошибка сохраняется позвоните нам по телефону +7(3412)908-123 или напишите нам на электронную почту info@atri-energo.ru";
			$type = 'error';
		}
	} else {
		$error_output = "Пожалуйста заполните все обязательные поля!";
		$type = 'error';
	}
	$output = array(
		'type'		=>	$type,
		'error'     =>  $error_output,
		'success'   =>  $success_output
	);
	echo json_encode($output);
}

function orderPump()
{
	global $db, $allowFuncs, $url, $core;
	$error_output = '';
	$success_output = '';
	$type = 'success';
	if (!empty($_POST['name']) && !empty($_POST['phone']) && !empty($_POST['email'])) {

		$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
		$recaptcha_secret = '6LfddtUZAAAAAOJmTgbOOIec5WaFIOnIrOqBFk7I';
		$recaptcha_response = $_POST['recaptcha_response'];
		$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
		$recaptcha = json_decode($recaptcha);
		$dataArray = array();
		$dataArray['type'] = 'pump';
		$dataArray['status'] = '1';
		if ($recaptcha->success == true && $recaptcha->score >= 0.5 && $recaptcha->action == 'contact') {

			if ($core->auth->user_id != '0') {
				$dataArray['surname'] = $core->auth->user_info['surname'];
				$dataArray['name'] = $core->auth->user_info['name'];
				$dataArray['ochestvo'] = $core->auth->user_info['ochestvo'];
				$dataArray['uid'] = $core->auth->user_id;
				$dataArray['phone'] = $core->auth->user_info['phone'];
			} else {
				$dadata = new \Dadata\DadataClient('ddeafb9ff43e79578243d8f031f6e9d8b047ca0e', 'e03a8f8ceaa114f8d8e0e09af92a0f3b069f1d40');
				$response = $dadata->clean("name", filter($_POST['name']));
				if ($response['qc'] == '0') {
					$dataArray['surname'] = $response['surname'];
					$dataArray['name'] = $response['name'];
					$dataArray['ochestvo'] = $response['patronymic'];
					$dataArray['fio_source'] = $response;
					$responsePhone = $dadata->clean("phone", filter($_POST['phone']));
					if ($responsePhone['qc'] != '2') {
						$dataArray['phone'] = $responsePhone['phone'];
						$dataArray['phone_source'] = $responsePhone;
					} else {
						$type = 'error';
						$error_output = "Пожалуйста укажите реальный номер телефон";
					}
				} else {
					$type = 'error';
					$error_output = "Пожалуйста укажите реальное имя";
				}
			}
			$dataArray['message'] = !empty($_POST['message']) ? filter($_POST['message']) : '';
			$dataArray['request_source'] = $_POST;
			if ($type == 'success') {
				$order = new \Order\Models\Order;
				$orderId = $order->add($dataArray);
				$dataArrayPump = array();
				$dataArrayPump['order_id'] = $orderId;
				$dataArrayPump['pressure'] = filter($_POST['pressure']);
				$dataArrayPump['qPump'] = filter($_POST['qPump']);
				$dataArrayPump['r1'] = intval($_POST['r1']);
				$dataArrayPump['r2'] = intval($_POST['r2']);
				$dataArrayPump['type'] = intval($_POST['type']);
				$orderPump = new \Order\Models\OrderPump;
				$orderPump->add($dataArrayPump);
				$success_output = 'Ваше сообщение успешно отправлено';
			}
		} else {
			$error_output = "Вы не прошли проверку на робота, попробуйте ещё раз! Если ошибка сохраняется позвоните нам по телефону +7(3412)908-123 или напишите нам на электронную почту info@atri-energo.ru";
			$type = 'error';
		}
	} else {
		$error_output = "Пожалуйста заполните все обязательные поля!";
		$type = 'error';
	}
	$output = array(
		'type'		=>	$type,
		'error'     =>  $error_output,
		'success'   =>  $success_output
	);
	echo json_encode($output);
}
