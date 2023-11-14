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

use Boot\Models\Users;
use Boot\Support\Facades\Hash;

class Auth extends \Api\App\AppMethodLoader
{
	public function login()
	{
		if ($this->request['type'] == 'password') {
			$login = empty($this->request['login']) ? "" : base64_decode($this->request['login']);
			$password = empty($this->request['password']) ? "" : base64_decode($this->request['password']);
			$user = Users::where('email', $login)->where('password', Hash::make($password))->first();
			if (!empty($user)) {
				$result = true;
			} else {
				$result = false;
			}
			
		} elseif ($this->request['type'] == 'email' || $this->request['type'] == 'phone' ) {
			// $login = empty($this->request['login']) ? "" : base64_decode($this->request['login']);
			// $code = $this->request['code'] ?? null;
			// $session = $this->request['session'] ?? null;
			// $result = $auth->checkCode($login, $session, $code);
			
			// $result = $auth->login();
			$result = false;
		} else {
			$result = ['error' => 'Неизвестный способ авторизации!'];
		}
		return $result;
	}

	public function send()
	{
		// $login = empty($this->request['login']) ? "" : base64_decode($this->request['login']);
		// $auth = new SystemAuth();
		// $result = $auth->sendCode($login);
		$result = false;
		return $result;
	}

	public function check()
	{

		// $auth = new SystemAuth();
		

		// return $result;
	}
}
