<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description Authorization class
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

use Boot\Models\Users;
use \Boot\System\Cache\Cache;
use \Boot\System\Agent\Agent;
use \Boot\Support\Facades\Auth as AuthFacade;

class auth
{
	var $user_id = 0;
	var $isUser = false;
	var $isAdmin = false;
	var $isModer = false;
	var $group = 0;
	var $user_info = false;
	var $group_info = false;
	var $banUser = false;
	var $groups_array = [];
	var $access_array = array();
	var $newPms = false;
	var $newPmsNumb = 0;

	private $cache;
	private $userSetting;
	private $agent;

	public $login;
	public $password;

	//получаем группы пользователей
	public function getGroups()
	{
		$groupsCache = $this->cache->getItem('groups.items2');
		if ($groupsCache->isHit()) {
			$this->groups_array = $groupsCache->get();
		} else {
			$groupsArray = \Boot\Models\Groups::all()->toArray();
			foreach ($groupsArray as $value) {
				$this->groups_array[$value['id']] = $value;
			}
			$groupsCache->set($this->groups_array)->expiresAfter(\DateInterval::createFromDateString('1 day'));
			$this->cache->save($groupsCache);
		}
	}




	public function authByHash($hash)
	{
		global $db;
		// dd($this->agent->getCity());
		// var_dump($this->agent->getAll());
		if (empty($hash)) {
			if (!isset($_COOKIE[COOKIE_PAUSE])) {
				$uid = 0;
				$this->group = $this->userSetting['guestGroup'];
				$name = 'Guest';
				if ($this->agent->isRobot()) {
					$this->group = $this->userSetting['botGroup'];
					$name = $this->agent->getRobot();
				}
				$ip = $this->agent->getIp();
				\Boot\Models\Online::updateOrCreate(
					['ip' => $ip, 'group' => $this->group],
					[
						'uid' => $uid,
						'ip' => $ip,
						'group' => $this->group,
						'time_marker' => DB::raw('NOW'),
						'name' => $name,
						'agent' => $this->agent->getAll(),
						'url' => '/',
					]
				);
				setcookie(COOKIE_PAUSE, time(), time() + PAUSE_TIME, '/');
				$this->isUser = false;
				$this->group_info = $this->groups_array[$this->group];
			}
		}
		\Boot\Models\Online::where('time_marker', '<', DB::raw('CURRENT_TIMESTAMP() - INTERVAL ' . PAUSE_TIME . ' SECOND'))->delete();
		// dd(\Boot\Models\Online::where('time_marker', '>', date('Y-m-d H:i:s', time() - PAUSE_TIME))->get()->toArray());
	}


	//конструктор класса
	public function __construct($hash = '', $token = null)
	{
		$this->cache = new Cache;
		$this->userSetting = require_once(ROOT.'config/settings/user.config.php');
		$this->agent = new Agent();
		//получаем группы пользователей
		$this->getGroups();
		//получаем права пользователей

		global $db;
		// dump(AuthFacade::user());
		if (AuthFacade::check()) {
			$this->user_info = getcache('userInfo_' . AuthFacade::id());
			if (empty($this->user_info)) {
				$this->user_info = $db->getRow($db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE id = '" . AuthFacade::id() . "' "));

				$short_user_name = $this->user_info['surname'] . ' ';
				$short_user_name .= mb_substr($this->user_info['name'], 0, 1) . '. ';
				$short_user_name .= mb_substr($this->user_info['ochestvo'], 0, 1) . '.';
				$full_user_name = $this->user_info['surname'] . ' ';
				$full_user_name .= $this->user_info['name'] . ' ';
				$full_user_name .= $this->user_info['ochestvo'];
				$this->user_info['short_user_name'] =  $short_user_name;
				$this->user_info['full_user_name'] = $full_user_name;

				setcache('userInfo_' . AuthFacade::id(), $this->user_info);
			}
			$this->user_id = AuthFacade::id();
			if(Users::find($this->user_id)->can('portal access')){
				$this->isAdmin = true;
				$this->isUser = true;
			}

			// dd($this->user_info);
			// $this->group = $this->user_info['group'];
			// if (isset($this->groups_array[$this->group])) {
			// 	$group_info = $this->groups_array[$this->group];
			// 	$group_info['gname'] = $group_info['name'];
			// 	$group_info['gid'] = $group_info['id'];
			// 	unset($group_info['name'], $group_info['id']);
			// 	if ($this->user_info['exgroup'] != 0 && isset($this->groups_array[$this->user_info['exgroup']])) {
			// 		$group_info['exname'] = $this->groups_array[$this->user_info['exgroup']]['name'];
			// 		$group_info['excolor'] = $this->groups_array[$this->user_info['exgroup']]['color'];
			// 		$group_info['exicon'] = $this->groups_array[$this->user_info['exgroup']]['icon'];
			// 	}

			// 	$this->group_info = $group_info;
			// 	$this->user_info = array_merge($this->user_info, $this->group_info);
			// 	unset($group_info);
			// }

			// if ($this->group_info['user'] == 1)


			// if ($this->group_info['admin'] == 1)


			// if ($this->group_info['moderator'] == 1)
			// 	$this->isModer = true;
		}
	}



	public function initUser($id)
	{
		global $db;
		$query = $db->query("SELECT * FROM `" . DB_PREFIX . "_users` WHERE id = '{$id}' LIMIT 1");
		$this->login_data = $db->getRow($query);
		$this->user_id = $id;
		$this->user_info = $this->getUserInfo($id);
	}


	public function getUserInfo($uid)
	{
		global $db;
		$user_info_array = getcache('userInfo_' . $uid);
		if (empty($user_info_array)) {
			$user_info_array = $db->getRow($db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE id = '" . $uid . "'"));
			$short_user_name = $user_info_array['surname'] . ' ';
			$short_user_name .= mb_substr($user_info_array['name'], 0, 1) . '. ';
			$short_user_name .= mb_substr($user_info_array['ochestvo'], 0, 1) . '.';
			$full_user_name = $user_info_array['surname'] . ' ';
			$full_user_name .= $user_info_array['name'] . ' ';
			$full_user_name .= $user_info_array['ochestvo'];
			$user_info_array['short_user_name'] =  $short_user_name;
			$user_info_array['full_user_name'] = $full_user_name;
			setcache('userInfo_' . $uid, $user_info_array);
		}
		return $user_info_array;
	}

	public function login_()
	{
		$this->get_login_data();

		// $password = md5(md5($this->password));
		// $this->password_gen = md5(mb_substr($password, 0, -mb_strlen($this->login_data['tail'])) . $this->login_data['tail']);

		if ($this->password_check($this->password)) {
			return true;
		} else {
			throw new \Exception(json_encode(["type" => 0, "text" => "Incorrect password"], JSON_UNESCAPED_UNICODE));
		}
	}

	public function get_login_data()
	{
		global $db;
		if (empty($this->login)) throw new \Exception(json_encode(["type" => 2, "text" => "Not found `login`"], JSON_UNESCAPED_UNICODE));
		if (empty($this->password)) throw new \Exception(json_encode(["type" => 2, "text" => "Not found `password`"], JSON_UNESCAPED_UNICODE));

		$db->query("DELETE FROM `" . DB_PREFIX . "_online` WHERE ip = '" . getRealIpAddr() . "'");

		if ((strpos($this->login, '@') === false)) {
			$login = filter($this->login, 'nick');
			$where = "nick = '{$db->safesql($login)}'";
		} else {
			$login = filter($this->login, 'mail');
			$where = "email = '{$db->safesql($login)}'";
		}

		$q_str = "SELECT id, password, tail FROM `" . DB_PREFIX . "_users` WHERE {$where} AND active = '1' LIMIT 1";

		$query = $db->query($q_str);
		if ($db->numRows($query) > 0) {
			$this->login_data = $db->getRow($query);
		} else {
			throw new \Exception(json_encode(["type" => 1, "text" => "Incorrect login"], JSON_UNESCAPED_UNICODE));
		}
	}

	static public function password_generate($password, $tail)
	{
	}

	public function password_check($password)
	{
	}

	function login($nick, $password)
	{
		if (AuthFacade::check()) {
		}
	}

	function login_social($provider, $social_id)
	{
	}

	function logout()
	{
		AuthFacade::logout();
	}

	function updateProfile($mail, $icq, $skype, $phone, $surname, $name, $ochestvo, $place, $age, $gender, $birthDate, $hobby, $signature, $fields, $uid = false)
	{
	}

	function updatePassword($password, $uid = false)
	{
	}

	function forgotPass($new_pass, $tail, $uid)
	{
	}

	function activate($mail)
	{
	}

	function register($user_login, $password, $tail, $email, $icq, $skype, $family, $name, $ochestvo, $age, $sex, $about, $signature, $activate, $ip, $group = '2', $phone = '')
	{
	}

	function register_social($user_login, $password, $tail, $email, $social_id, $provider, $birthday, $avatar_auth, $icq, $skype, $family, $name, $ochestvo, $age, $sex, $about, $signature, $activate, $ip, $group = '2', $phone = '')
	{
	}
}
