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
	
class Organization extends \Api\App\AppMethodLoader{		
	protected $model = \Organizations\Model\Organizations::class;
	
	public function search($requestData) {
		if(!empty($requestData['search'])){
			$search = "%{$requestData['search']}%";
			// $this->data->where();
			$this->data->where(function($query) use ($search) {
                $query->orWhere('o.id', 'like', $search)
                      ->orWhere('o.name', 'like', $search)
                      ->orWhere('o.inn', 'like', $search)
                      ->orWhere('uo.post', 'like', $search);
            });
			
		}		
	}


	public function get(){
		$this->data = \DB::table("users_org as uo")
		->leftJoin("organizations as o", "uo.oid", "=", "o.id")
		->select("o.id", "o.name", "o.inn", "uo.choose", "uo.post")
		->where("uo.uid", "=", $this->uid);
		return $this->get_();			
	}

	public function store(){
		$return = [];
		if(empty($this->request["inn"])) 	throw new \Exception("Отсутствует поле инн");
		if(empty($this->request["post"])) 	throw new \Exception("Отсутствует поле должность");

		$oid = createOrgByInn($this->request["inn"]);
		$post = filter($this->request['post']);

		if(\Boot\Models\UsersOrg::where("uid", $this->uid)->where("oid", $oid)->count() > 0){
			$return['id'] = $oid;
			$return['type'] = 'success';
			$return['title'] = 'Данная организация уже есть в списке!';
			$return['text'] = 'Посмотрите внимательней, данная организация уже есть в вашем списке!';
		} else {
			$UsersOrg = new \Boot\Models\UsersOrg;
			$UsersOrg->uid = $this->uid;
			$UsersOrg->oid = $oid;
			$UsersOrg->post = $post;
			$UsersOrg->choose = 0;
			$UsersOrg->status = 1;
			$UsersOrg->save();

			$data = \Organizations\Model\Organizations::find($oid);
			$return['id'] = $oid;
			$return['type'] = 'success';
			$return['title'] = 'Организация добавлена!';
			$return['text'] = 'Указанная вами организация добавлена в ваш список!';
			$return['data'] = $data;
		}
		return $return;
	}

}	
	
?>