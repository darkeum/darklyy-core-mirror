<?php

use Carbon\Carbon;
use Boot\Models\Users;
use Boot\Models\UsersToken;
use Boot\Support\Facades\Auth;
use Boot\Support\Facades\Hash;

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

class appAuth
{
	public $login;
	public $password;
	public $client_id;
	public $token;
	public $result;
	public $uid;
	public $isUser = false;


	public function __construct()
	{
		if (isset($token) && !empty($token)) {
		} else {
			//$this->login();
		}
	}

	private function getVerCode($length = 6)
	{
		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function login()
	{
		$login = empty($this->login) ? "" : base64_decode($this->login);
		$password = empty($this->password) ? "" : base64_decode($this->password);

		if (Auth::attempt(['email' => $login, 'password' => $password], true)) {
			$uid = Auth::id();
			$token = md5($uid . uniqid() . uniqid() . uniqid()) . uniqid();
			$userToken = UsersToken::firstOrCreate([
				'uid' => $uid,
				'platform' => 'app',
				'status'=> 1
			], [
				'token' => $token,
				'date' => Carbon::now(),
				'veritification_code' => $this->getVerCode(),
			]);
			$this->result = array('token' => $userToken->token);
		} else {
			$this->result = false;
		}		
	}

	public function checkToken()
	{
		if (isset($_GET['token']) && !empty($_GET['token'])) {
			$token = $_GET['token'];	
			$user = UsersToken::where('token', $token)->where('platform', 'app')->where('status', 1)->first();
			if (!empty($user)) {
				$this->uid = $user->uid;
				$this->isUser = true;
				$check = true;
			} else {
				$check = false;
			}
			$this->result = array('check' => $check);			
		} else {
			$this->result = array('error' => array('type' => '0', 'text' => 'Incorrect password'));
		}
	}




	public function getResult()
	{
		return $this->result;
	}
}
