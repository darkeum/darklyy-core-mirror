<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Api\App;

class AppMain{
    protected $auth;
	protected $result;
	protected $url;	
	protected $method;	

	public function __construct(){
		try{
			
			$this->url = explode('/', parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));	
		
			$temp = explode('.',$this->url[3]);	
			$this->method = ucfirst($temp[0]);
			if(!isset($temp[1])) $this->error_action_not_found();
			$this->action = $temp[1];		
			$this->auth = new \Api\App\Adapter\AdapterAuth();
			$this->request = isset($_REQUEST) ? $_REQUEST : [];
			$this->check_token();
			// $this->get_method();
			$this->route();
		} catch(\Exception $e){
			$this->result = $this->response($e->getMessage().' Line '.$e->getLine() . ' File '.$e->getFile());
			// dd('test');
		}
    }

	protected function response($data, $status = 500) {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
		if(!is_array($data)) {
			$data = ['error' => $data];
		}
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

	private function requestStatus($code) {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code])?$status[$code]:$status[500];
    }

	public function check_token(){
		if(isset($this->request['token'])){
			$this->auth->token = $this->request['token'];
			$this->auth->checkToken();
		}
	}

	public function route(){
		if(empty($this->method)) throw new \Exception("Not found `method`!");
		$className = '\Api\App\Method\\'.$this->method;
	
		if($this->auth->isUser){
			$this->isUser();
			if(class_exists($className)){
				$class = new $className;
				if(method_exists($class, $this->action)){
					
					$class->setUid($this->auth->uid);
					
					$data = $class->{$this->action}();
					$this->result = $this->response($data, 200);			
				} else {
					$this->error_action_not_found();
				}
			} else {
				throw new \Exception("Method not found!");
			}
		} else {			
			if($this->method == 'Auth'){
				if(class_exists($className)){
					$class = new $className;
					if(method_exists($class, $this->action)){
						$class->setUid($this->auth->uid);
						// dd($this->auth->uid);
						$data = $class->{$this->action}();
						$this->result = $this->response($data, 200);			
					}
				}
			} else {
				$this->isUser();
			}			
		}

		// if(method_exists($this, $method)){
		// 	$this->$method();
		// } else {
		// 	switch($method){
		// 		default:
		// 			throw new \Exception("Bad request!");
		// 		break;
		// 		case "auth":
		// 			$this->authMethod();
		// 		break;
		// 	}
		// }
	}

	public function error_action_not_found(){
		throw new \Exception("Action not found!");
	}

	public function get_method(){
		$this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new \Exception("Unexpected Header");
            }
        }
	}
	
    public function getResult(){
        return $this->result;
    }

	public function authMethod(){
		$this->auth->login = isset($this->request['login']) ? $this->request['login'] : null;
		$this->auth->password = isset($this->request['password']) ? $this->request['password'] : null;
		$this->auth->client_id = isset($this->request['client_id']) ? $this->request['client_id'] : null;
		$this->auth->token = isset($this->request['token']) ? $this->request['token'] : null;
		$this->auth->login();
		$this->result = $this->response($this->auth->getResult(), 200);
	}

	public function checkToken(){
		$this->result = $this->response($this->auth->getResult(), 200);
	}

	public function isUser(){
		if(!$this->auth->isUser) throw new \Exception("Вы не авторизованы!");
	}

	

	public function getUserInfo(){
		global $core;
		$this->isUser();
		$userInfoArray = [];
		if(isset($this->request['uid'])){							
			$core->auth->getUserInfo($this->auth->uid);
			$userInfo = [];
		} else {
			$userInfo = $core->auth->getUserInfo($this->auth->uid);
			$userInfoArray[] = [
				'id' => $this->auth->uid,
				'name' => $userInfo['name'],
				'surname' => $userInfo['surname'],
				'ochestvo' => $userInfo['ochestvo'],
				'group' => $userInfo['group'],
				'email' => $userInfo['email'],
				'avatar' => avatar($this->auth->uid),
				'phone' => $userInfo['phone'],
			];
		}
		$this->result = $this->response($userInfoArray, 200);
	}

	public function passwordCheck(){
		global $core;
		$this->isUser();
		if(empty($this->request["password"])) throw new \Exception("Не хватает данных");
		$result["success"] = $core->auth->password_check(base64_decode($this->request["password"]));
		$this->result = $this->response($result, 200);
	}

	public function userInfo(){
		global $core;
		$this->isUser();
		switch($this->method){
			case 'GET':
				$userInfo = $core->auth->getUserInfo($this->auth->uid);
				$avatar = \Boot\Models\Users::avatar($this->auth->uid);
				$result = [
					'id' => $this->auth->uid,
					'name' => $userInfo['name'],
					'surname' => $userInfo['surname'],
					'ochestvo' => $userInfo['ochestvo'],
					'birthday' => $userInfo['birthday'],
					'sex' => $userInfo['sex'],
					'place' => $userInfo['place'],
					'group' => $userInfo['group'],
					'email' => $userInfo['email'],
					'avatar' => "https://atri-energo.ru/".$avatar."?".time(),
					'phone' => $userInfo['phone'],
				];
				$this->result = $this->response($result, 200);
			break;
			case 'POST':
				$allow_update = ["name", "surname", "ochestvo", "sex", "birthday", "place", "phone", "email"];
				$update = [];
				foreach($this->request as $field => $value){
					if(in_array($field, $allow_update)) $update[ $field ] = $value;
				}

				\Boot\Models\Users::find($this->auth->uid)->update($update);
				if(isset($this->request["avatar"])) \Profile\Support\UsersSupport::avatar_base64($this->auth->uid, $this->request["avatar"]);
				$this->result = $this->response("Данные сохранены", 200);
			break;
			// case 'POST':
			//     return 'createAction';
			// break;
			// case 'DELETE':
			//     return 'deleteAction';
			// break;
		}
	}

	public function changePassword(){
		global $core;
		$this->isUser();
		$request = $this->request;
		if(empty($request["oldPassword"]) || empty($request["newPassword"]) || empty($request["verifyPassword"])) throw new \Exception("Не хватает данных");
		if($request["newPassword"] != $request["verifyPassword"]) throw new \Exception("Пароли не совпадают");
		if($core->auth->password_check(base64_decode($request["oldPassword"]))){
			$core->auth->updatePassword(base64_decode($request["newPassword"]));
			$this->result = $this->response("Пароль обновлен", 200);
		} else {
			throw new \Exception("Старый пароль не правильный!");
		}
	}

	public function getNotif(){
		global $db;
		$this->isUser();	
		$notifArray = [];
		$start = isset($this->request['start']) ? intval($this->request['start']) : 5;
		$limit = isset($this->request['limit']) ? intval($this->request['limit']) : 10;
		$module = isset($this->request['module']) ? filter($this->request['module']) : '';
		$where = '';
		if(!empty($module) && $module != 'all'){
			switch($module){	
				default:
					throw new \Exception("Bad request!");
				break;	
				case "supply":
					$where = "AND (module = 'twodetail' OR module = 'supply' OR module = 'supply_logistics' OR module = 'supply_order')";
				break;	
				case "sales":
				case "task":
					$where = "AND module = '{$module}'";
				break;
				
			}
		}
		
		$queryNotif =  "SELECT * FROM `".DB_PREFIX."_notif`  where uid = '{$this->auth->uid}' {$where} ORDER BY date DESC LIMIT {$limit} OFFSET {$start}";
		$resultNotif = $db->query($queryNotif);
		if($db->numRows($resultNotif) > 0){
			while($rowNotif = $db->getRow($resultNotif)){
				$notifArray[] = [
					'id' => $rowNotif['id'],
					'title' => $rowNotif['title'],
					'text' => $rowNotif['text'],
					'status' => $rowNotif['status'],
					'url' => $rowNotif['url'],
					'icon' => $rowNotif['icon'],
					'date' => $rowNotif['date'],
					'date_read' => $rowNotif['date_read'],
					'module' => $rowNotif['module']
				];
			}
		}
		$this->result = $this->response($notifArray, 200);
	}

	public function getStatistic(){
		global $db;
		$this->isUser();
		$statArray = [];
		$start = isset($this->request['start']) ? intval($this->request['start']) : 5;
		$limit = isset($this->request['limit']) ? intval($this->request['limit']) : 10;
		$queryNotif =  "SELECT * FROM `".DB_PREFIX."_notif` where uid = '{$this->auth->uid}' AND status = '1'";
		$resultNotif = $db->query($queryNotif);
		$notifCount = $db->numRows($resultNotif);
		$query_task_numb = $db->query("SELECT status FROM `".DB_PREFIX."_task` WHERE toid = '{$this->auth->uid}' AND status != 3");
		$task_numb = $db->numRows($query_task_numb);
		$query_sales = $db->query("SELECT status FROM `".DB_PREFIX."_sales` WHERE uid = '{$this->auth->uid}' AND end != '1' AND del != '1'");
		$sales_numb = $db->numRows($query_sales);
		$query_real = $db->query("SELECT SUM(total) as totalDiff, DATE_FORMAT(date,'%m-%Y') AS dateDiff, DATE_FORMAT(date,'%Y') AS dateYear, DATE_FORMAT(date,'%m') AS dateMonth 
		FROM `".DB_PREFIX."_realization` 
		WHERE 'org' = '1' AND 'active' = '1' AND 'date' >= '2019-01-01' 
		GROUP BY dateDiff 
		ORDER BY dateDiff ASC");

		if($db->numRows($query_real) > 0){
			$real = [];
			while($real_stat = $db->getRow($query_real)) {
				$real[$real_stat['dateYear']][$real_stat['dateMonth']]	= $real_stat['totalDiff'];
			}
		}
		
		$statArray = [
			'notif' => $notifCount,
			'title' => $notifCount,
			'task' => $task_numb,
			'sales' => $sales_numb,
			'url' => $notifCount,
			'icon' => $notifCount,
			'date' => $notifCount,
			'date_read' => $notifCount,
			'module' => 'ff'
		];
		$this->result = $this->response($statArray, 200);
	}
	
}