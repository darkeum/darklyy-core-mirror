<?php

/**
 * @name        JMY CMS
 * @link        http://jmy.su/
 * @copyright   Copyright (C) 2012-2020 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */


namespace Api\App\Method;

use Chat as BaseChat;

class Chat extends \Api\App\AppMethodLoader
{
	protected $model = \Sales\Model\Sales::class;
	protected $table = 'sales';

	public function load()
	{
		$chat_id = $this->request["chat_id"] ?? false;
		$limit = $this->request["limit"] ?? null;
		$message_id = isset($this->request["message_id"]) ? ($this->request["message_id"] === "false" ? false : $this->request["message_id"]) : false;
		$chat = new BaseChat();
		$this->read();
		return [
			'type' => 'success', 
			'data' => json_decode($chat->load_chat_message($chat_id, $message_id, "return", $limit), true)
		];
	}

	public function check()
	{
		$chat_id = $this->request["chat_id"] ?? false;
		$chat = new BaseChat();
		$data = $chat->check_site($chat_id);
		if ($data) {
			return $data;
		}
	}

	public function read()
	{
		$chat_id = $this->request["chat_id"] ?? false;
		if ($chat_id) {
			$chat = new BaseChat();
			$chat->read_chat_message($chat_id);
		}
	}

	public function add()
	{
		if (isset($this->request["chat_id"], $this->request["message"])) {
			$chat = new BaseChat();
			return [
				'type' => 'success', 
				'data' => ['message_id' => $chat->add_message($this->request)]
			];
		}
	}

	public function notif()
	{
		$chat_id = $this->request["chat_id"] ?? false;
		$chat = new BaseChat();
		$chat->notif_send_message($chat_id);
	}

	public function file()
	{
		global $db;
		$chat_id = $this->request["chat_id"] ?? false;
		$file = $_FILES["file"] ?? false;

		$chat = new BaseChat();
		$message_id = $chat->add_message(["chat_id" => $chat_id, "websocket" => false]);
		$aiid = attach_save_file($file, $message_id, "chat", "chat");
		$db->query("UPDATE `" . DB_PREFIX . "_chat_message` SET file = '" . $aiid . "' WHERE id = '" . $message_id . "' LIMIT 1");
		$chat->send_websocket($chat_id, $message_id);
		echo $message_id;
	}
}
