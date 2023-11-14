<?php

use Boot\Support\Carbon;

/**
 * @name        JMY CORE
 * @link        https://jmy.su/
 * @copyright   Copyright (C) 2012-2019 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 * @info_ru     Место под ваши собственные функции
 * @info_en     Place for your own functions
 */


function sendAllNotif($attr)
{
	//uid, title, text, icon, url, pNotif, pPush, pMail, pSms
	global $core, $config;
	foreach ($attr as $key => $val) {
		if (empty($attr[$key])) $attr[$key] = false;
	}

	switch ($attr['title']) {
		case 'task':
			$attr['title'] = 'Новая задача';
			break;
	}
	$uid = $attr['uid'];
	$id_notif = "";
	$uid = is_array($uid) ? $uid : [$uid];
	if (count($uid) > 0) {
		foreach ($uid as $val) {
			$attr['uid'] = $val;
			$id_notif = sendNotif($attr);
			if (isset($attr['url'])) {
				$old_url = $attr['url'];
				$attr['url'] = $config['url'] . '/administration/module/notif/redirect/' . $id_notif;
			}
			sendPush($attr);
			// sendViber($attr);
			sendVk($attr);
			sendApp($attr);
			if ($val) {
				$user = \Boot\Models\Users::find($val);
				if(!empty($user)){
					$user->notify(new \App\Notifications\DefaultNotification($id_notif));
				}
				
			}
			\Boot\Models\NotifDaemon::create(["notif_id" => $id_notif, "platform" => "viber"]);
			if (isset($attr['url'])) {
				$attr['url'] = $old_url;
			}
		}
	}

	// if(!is_array($uid)){
	// 	if(!empty($uid)){
	// 		$id_notif = sendNotif($attr);
	// 		if (isset($attr['url'])){
	// 			$attr['url'] = $config['url'].'/administration/module/notif/redirect/'.$id_notif;
	// 		} 
	// 		sendPush($attr);
	// 		// sendViber($attr);
	// 		// sendVk($attr);
	// 		sendApp($attr);
	// 		$notifDaemon->add(["notif_id" => $id_notif, "platform" => "viber"]);
	// 		//return $nid;
	// 	}
	// } else {

	// }

	return $id_notif;
}

function sendNotif($attr)
{
	$attr["url"] = str_replace("&amp;", "&", $attr["url"]);

	$module = null;
	$pid = null;
	if (array_key_exists('module', $attr) && !empty($attr["module"])) {
		$module = $attr["module"];
	}
	if (array_key_exists('pid', $attr) && !empty($attr["pid"])) {
		$pid = $attr["pid"];
	}

	$notif = \Notifications\Models\Notifications::create([
		"uid" => $attr['uid'],
		"url" => $attr['url'],
		"text" => $attr['text'],
		"title" => $attr['title'],
		"icon" => $attr['icon'],
		"date" => Carbon::now(),
		"status" => 1,
		"module" => $module,
		"pid" => $pid,
	]);
	return $notif->id;
}

function sendPush($attr)
{
	$url = 'https://fcm.googleapis.com/fcm/send';
	$YOUR_API_KEY = 'AAAAt3MK9JI:APA91bHQHQvxelv0URCO2AtaJ4KDBHPxJ55EL5uwAy9X1PALlCoLv_4JMLqStQAI50Xhb_O2Fh6b64at3qm5z6pVJzIt_8TFSTRSCdE36c2bu9pp5ORLIC2OCnxDrmIFdfDbn2XLnWxY';
	//sprintf($attr['text']),
	$request_body = [
		'data' => [
			'title' => $attr['title'],
			'body' => $attr['text'],
			'icon' => $attr['icon'],
			'click_action' => $attr['url'],
		],
		"registration_ids"  => getClientId($attr['uid'])
	];
	$request_headers = [
		'Content-Type: application/json',
		'Authorization: key=' . $YOUR_API_KEY,
	];
	if (!empty(getClientId($attr['uid']))) {
		//print_r($request_body);
		//exit;
		$fields = json_encode($request_body);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$response = curl_exec($ch);
		//print_r($fields);
		//print_r($fields);
		//print_r($response);
		curl_close($ch);
	}
	return true;
}

function sendApp($attr)
{
	$url = 'https://fcm.googleapis.com/fcm/send';
	$YOUR_API_KEY = 'AAAA12wwQe0:APA91bEQMYmnNjhQcwKuuU8LdjPrR1wsdftkEBylT3OiAlaAt_bWGyPa6trK0Ld-TGRZtlTKXfi0h7_Ap3gJzJ__rFR21n4whnmqqhl0QyB1PWVHLCpTqx_Kr1IkT8nS15_SloqSV4be';
	//sprintf($attr['text'])
	$request_body = [
		'notification' => [
			'title' => $attr['title'],
			'text' => $attr['text'],
		],
		"registration_ids"  => getAppClientId($attr['uid']),
	];
	$request_headers = [
		'Content-Type: application/json',
		'Authorization: key=' . $YOUR_API_KEY,
	];
	// print_r($request_body);
	$fields = json_encode($request_body);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$response = curl_exec($ch);
	curl_close($ch);
	return true;
}


use Viber\Bot;
use Viber\Api\Sender;

function getQueryUsersToken($uid)
{
	return \Boot\Models\UsersToken::where("status", 1)->where("uid", $uid);
}

function sendViber($attr)
{
	$result_user = getQueryUsersToken($attr['uid']);
	$result_user = $result_user->where("platform", "viber")->first();
	if (!empty($result_user)) {
		$result_user = $result_user->toArray();
		$Viber = new vibers();
		if ($attr['uid'] == '1') {
			$apiKey = '491c0a3663e7d14d-a7c84835f8b1809f-21bfe84ad2fa640a';
			$bot = new Bot(['token' => $apiKey]);
			$botSender = new Sender([
				'name' => 'АТРИ-БОТ',
				'avatar' => config('app.url').'/files/avatars/bot.png',
			]);
			$receiverId = $result_user['token'];
			$client = $bot->getClient();
			$client->sendMessage(
				(new \Viber\Api\Message\CarouselContent())
					->setSender($botSender)
					->setReceiver($receiverId)
					->setButtonsGroupColumns(6)
					->setButtonsGroupRows(6)
					->setBgColor('#8f8f90')
					->setButtons([
						(new \Viber\Api\Keyboard\Button())
							->setColumns(6)
							->setRows(3)
							->setActionType('open-url')
							->setTextSize('large')
							->setTextHAlign('left')
							->setSilent(true)
							->setActionBody($attr['url'])
							->setText('<span style="color: #000; ">' . $attr['text'] . '</span>'),
						(new \Viber\Api\Keyboard\Button())
							->setColumns(6)
							->setRows(2)
							->setActionType('open-url')
							->setSilent(true)
							->setActionBody($attr['url'])
							->setImage(config('app.url').'/files/bot/btnViber.png'),
					])
			);
		} else {
			$Viber->message_post(
				$result_user['token'],
				[
					'name' => 'АТРИ-БОТ',
					'avatar' => config('app.url').'/files/avatars/bot.png'
				],
				$attr['text'] . ' ' . (empty($attr['url']) ? '' : $attr['url'])
			);
		}
	}
	return true;
}

function sendVk($attr)
{
	$result_user = getQueryUsersToken($attr['uid']);
	$result_user = $result_user->where("platform", "vk")->first();
	if (!empty($result_user)) {
		$result_user = $result_user->toArray();
		require_once ROOT . '/api/vk/api/vk_api.php';
		vkApi_messagesSend($result_user['token'], $attr['text'] . ' ' . (isset($attr['url']) ? $attr['url'] : ''));
	}
	return true;
}

function sendSms($attr)
{
	$result_user = \Boot\Models\Users::find($attr['uid']);
	if (!empty($result_user)) {
		$result_user = $result_user->toArray();
		if (!empty($result_user['phone'])) {
			$smsru = new SMSRU('40316a89-2477-4d94-6592-5b59379386d7');
			$data = new stdClass();
			$data->to = $result_user['phone'];
			$data->text = $attr['text'] . ' ' . (isset($attr['url']) ? $attr['url'] : '');
			$sms = $smsru->send_one($data);
		}
	}
	return true;
}

function sendEmail($attr)
{
	$result_user = \Boot\Models\Users::find($attr['uid']);
	if (!empty($result_user)) {
		$result_user = $result_user->toArray();
		if (!empty($result_user['email'])) {
			require_once ROOT . '/boot/sub_classes/path_to_swift/lib/swift_required.php';
			require_once ROOT . '/boot/sub_classes/sendemail.class.php';
			$sm = new sendemail();
			$sm->templatesPath = ROOT . "/usr/tpl/mail/";
			$sm->emailFrom = 'info@atri-energo.ru';
			$sm->fromName = 'ООО «АТРИ-ЭНЕРГО»';
			$sm->emailTo = $result_user['email'];
			$sm->toName = $result_user['name'];
			$sm->subject = $attr['title'];
			$sm->mTitle = $attr['title'];
			$sm->mText = $attr['text'];
			$varDataArray = array(
				'{title}' => $attr['title'],
				'{text}' => $attr['text'],
			);
			if (isset($attr['url'])) {
				$varDataArray['{url}'] = '<a href="' . $attr['url'] . '" style="background-color:#ff5722;border-radius:3px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:18px;line-height:38pt;text-align:center;text-decoration:none;-webkit-text-size-adjust:none; width: 142pt;font-family:\'Roboto\',\'Helvetica Neue\',Helvetica,Arial,sans-serif;box-shadow: 0 4px 6px 0 rgba(0, 0, 0, 0.07);">Перейти</a>';
			}
			$sm->varData = $varDataArray;
			$sm->templateDirname = "default";
			$send = $sm->Send();
		}
	}
	return true;
}

function sendEmailSales($attr)
{
	$result_user = \Boot\Models\Users::find($attr['uid']);
	if (!empty($result_user)) {
		$result_user = $result_user->toArray();
		if (!empty($result_user['email'])) {
			require_once ROOT . '/boot/sub_classes/path_to_swift/lib/swift_required.php';
			require_once ROOT . '/boot/sub_classes/sendemail.class.php';
			$attach = array();
			if (isset($attr['pdf']) && !empty($attr['pdf'])) {
				$attach[] = $attr['pdf'];
			}
			$sm = new sendemail();
			$sm->templatesPath = ROOT . "/usr/tpl/mail/";
			$sm->emailFrom = 'info@atri-energo.ru';
			$sm->fromName = 'ООО «АТРИ-ЭНЕРГО»';
			$sm->emailTo = $result_user['email'];
			$sm->toName = $result_user['name'];
			$sm->subject = $attr['title'];
			$sm->attach = $attach;
			$varDataArray = array(
				'{title}' => $attr['title'],
				'{text}' => $attr['text_email'],
				'{manager_post}' => $attr['manager_post'],
				'{manager_fio}' => $attr['manager_fio'],
				'{manager_email}' => $attr['manager_email'],
				'{manager_phone}' => $attr['manager_phone'],
				'{manager_dop}' => $attr['manager_dop'],
			);

			if (isset($attr['url'])) {
				$varDataArray['{url}'] = '<a href="' . $attr['url'] . '" style="background-color:#ff5722;border-radius:3px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:18px;line-height:38pt;text-align:center;text-decoration:none;-webkit-text-size-adjust:none; width: 142pt;font-family:\'Roboto\',\'Helvetica Neue\',Helvetica,Arial,sans-serif;box-shadow: 0 4px 6px 0 rgba(0, 0, 0, 0.07);">Перейти</a>';
			}
			$sm->varData = $varDataArray;
			$sm->templateDirname = "sales";
			$send = $sm->Send();
		}
	}
	return true;
}

function sendEmailByData($attr)
{
	if (!empty($attr['email'])) {
		require_once ROOT . '/boot/sub_classes/path_to_swift/lib/swift_required.php';
		require_once ROOT . '/boot/sub_classes/sendemail.class.php';
		$attach = array();
		if (isset($attr['pdf']) && !empty($attr['pdf'])) {
			$attach[] = $attr['pdf'];
		}
		$sm = new sendemail();
		$sm->templatesPath = ROOT . "/usr/tpl/mail/";
		$sm->emailFrom = 'info@atri-energo.ru';
		$sm->fromName = 'ООО «АТРИ-ЭНЕРГО»';
		$sm->emailTo = $attr['email'];
		$sm->toName = $attr['name'];
		$sm->subject = $attr['title'];
		$sm->attach = $attach;
		$varDataArray = array(
			'{title}' => $attr['title'],
			'{text}' => $attr['text'],
		);
		if (isset($attr['url'])) {
			$varDataArray['{url}'] = '<a href="' . $attr['url'] . '" style="background-color:#ff5722;border-radius:3px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:18px;line-height:38pt;text-align:center;text-decoration:none;-webkit-text-size-adjust:none; width: 142pt;font-family:\'Roboto\',\'Helvetica Neue\',Helvetica,Arial,sans-serif;box-shadow: 0 4px 6px 0 rgba(0, 0, 0, 0.07);">' . $attr['url_text'] . '</a>';
		}
		$sm->varData = $varDataArray;
		$sm->templateDirname = "default";
		$send = $sm->Send();
	}

	return true;
}

function getClientId($uid, $platform = false)
{
	$query_users_list = getQueryUsersToken($uid);
	$query_users_list = $query_users_list->where("platform", "push")->get();
	if ($query_users_list->count() > 0) {
		$list = [];
		foreach ($query_users_list as $users_list) {
			$list[] = $users_list->token;
		}
		return $list;
	} else {
		return false;
	}
}

function getAppClientId($uid, $platform = false)
{
	$query_users_list = getQueryUsersToken($uid);
	$query_users_list = $query_users_list->where("platform", "app")->get();
	if ($query_users_list->count() > 0) {
		$list = [];
		foreach ($query_users_list as $users_list) {
			$list[] = $users_list->device_id;
		}
	} else {
		return false;
	}
}
