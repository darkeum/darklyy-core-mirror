<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
namespace Api\App\Adapter;

use Carbon\Carbon;
use Boot\Models\UsersToken;
use Boot\Support\Facades\Auth;
use Boot\System\Sanctum\PersonalAccessToken;

class AdapterAuth
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
		if(isset($token)&&!empty($token))
		{
			
		}
		else
		{
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
	
	public function login(){
		$login = empty($this->request['login']) ? "" : base64_decode($this->request['login']);
		$password = empty($this->request['password']) ? "" : base64_decode($this->request['password']);

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

			$token = hash('sha256', $_GET['token']);
			$user = PersonalAccessToken::findToken($_GET['token']);
			// var_dump($user);
			// exit;
			if (!empty($user)) {
				Auth::loginUsingId($user->tokenable_id, true);
				$this->uid = $user->tokenable_id;
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
