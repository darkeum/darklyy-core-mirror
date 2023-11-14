<?php

namespace Api\App\Method;
	
class ObjectApi extends \Api\App\AppMethodLoader{		
	protected $model = \Object\Model\ObjectModel::class;
	

	public function search($requestData)
	{
		if(!empty($requestData['search'])){
			$search = "%{$requestData['search']}%";
			// $this->data->where();
			$this->data->where(function($query) use ($search) {
                $query->orWhere('o.id', 'like', $search)
                      ->orWhere('o.name', 'like', $search)
                      ->orWhere('o.address', 'like', $search)
                      ->orWhere('o.coords', 'like', $search);
            });
			
		}
		
	}


	public function get(){
		$this->data = \DB::table("users_object as uo")
		->leftJoin("object as o", "uo.object", "=", "o.id")
		->select("o.id", "o.name", "o.address", "o.coords")
		->where("uo.uid", "=", $this->uid);
		return $this->get_();			
	}

	public function store(){
		if(empty($this->request["name"])) throw new \Exception("Отсутствует поле имя объекта");
		if(empty($this->request["address"])) throw new \Exception("Отсутствует поле адрес объекта");
		if(empty($this->request["coords"])) throw new \Exception("Отсутствует поле координаты объекта");

		$object = new $this->model;
		$object->name = $this->request["name"];
		$object->address = $this->request["address"];
		$object->coords = $this->request["coords"];
		$object->uid = $this->uid;
		$object->date = time();
		$object->save();

		$UsersObject = new \Object\Model\UsersObject;
		$UsersObject->uid = $this->uid;
		$UsersObject->object = $object->id;
		$UsersObject->save();

		$return = [];
		$return['id'] = $object->id;
		$return['type'] = 'success';
		$return['title'] = 'Объект добавлен!';
		$return['text'] = 'Указанный вами объект добавлен в ваш список!';
		$return['data'] = $object;

		return $return;
	}
}