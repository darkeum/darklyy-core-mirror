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

class Request extends \Api\App\AppMethodLoader
{
	protected $model = \Request\Models\Request::class;
	protected $table = 'request';

	public function search($requestData)
	{
		if (!empty($requestData['search'])) {
			$search = "%{$requestData['search']}%";
			// $this->data->where();
			$this->data->where(function ($query) use ($search) {
				$query->orWhere('request.id', 'like', $search)
					->orWhere('object.name', 'like', $search)
					->orWhere('organizations.name', 'like', $search)
					->orWhere('request.title_user', 'like', $search);
			});
		}
	}

	public function get()
	{
		$this->data = $this->model::select(array('request.id', 'request.title_user as title', 'request.oid', 'organizations.name as oid_name', 'request.object', 'object.name as object_name', 'request.type', 'request.date', 'request.status'))
			->leftJoin('organizations', 'request.oid', '=', 'organizations.id')
			->leftJoin('object', 'request.object', '=', 'object.id');
		$requestData = array();
		$requestData['filter']['uid_dir'] = array('operator' => '=', 'value' => $this->uid);
		return $this->get_($requestData);
	}

	public function const()
	{
		$result = array();
		$result['type'] = array(
			'2' => 'Расчет БТП',
			'3' => 'Расчет теплообменника',
			'4' => 'Расчет узла учета',
			'5' => 'Расчет насоса',
			'6' => 'Расчет насосной станции',
		);
		$result['status'] = array(
			'1' => 'В очереди',
			'2' => 'Выполняется',
			'3' => 'Готов',
		);
		return $result;
	}

	public function view()
	{
		if (isset($this->request['id'])) {
			$id = intval($this->request['id']);
			$data = $this->model::select(array('request.*', 'organizations.name as oid_name', 'object.name as object_name'));
			$data->leftJoin('organizations', 'request.oid', '=', 'organizations.id');
			$data->leftJoin('object', 'request.object', '=', 'object.id');
			$data->where($this->table . '.id', '=', $id);
			$requestData = $data->get()->toArray()[0];
			switch ($requestData['type']) {
				default:
					throw new \Exception("Unknown request type!");
					break;
				case "2":
					$className = \Api\App\Method\Adapter\Request\Block::class;
					break;
				case "3":
					$className = \Api\App\Method\Adapter\Request\Heat::class;
					break;
				case "4":
					$className = \Api\App\Method\Adapter\Request\MeteringStation::class;
					break;
				case "5":
					$className = \Api\App\Method\Adapter\Request\Pump::class;
					break;
				case "6":
					$className = \Api\App\Method\Adapter\Request\PumpStation::class;
					break;
			}
			if (class_exists($className)) {
				$adapter = new $className($requestData);
				$result = array();
				$result['data'] = $adapter->getAdapterData();
				return $result;
			} else {
				throw new \Exception("Undefined request adapter!");
			}
		} else {
			throw new \Exception("Bad request id!");
		}
	}

	public function data()
	{
		if (isset($this->request['calcType'])) {
			//print($this->request['calcType']);	
			switch ($this->request['calcType']) {
				default:
					throw new \Exception("Unknown request type!");
					break;
				case "meteringStation":
					$className = \Api\App\Method\Adapter\Calculation\MeteringStation::class;
					break;
			}
			if (class_exists($className)) {
				$adapter = new $className();
				$adapter->uid = $this->uid;
				$adapter->request = $this->request;
				$result = array();
				$result['data'] = $adapter->getCalculationData();
				return $result;
			} else {
				throw new \Exception("Undefined request data Type!!");
			}
		} else {
			throw new \Exception("Not set data type!");
		}
	}

	public function calc()
	{
		if (isset($this->request['calcType'])) {
			//print($this->request['calcType']);	
			switch ($this->request['calcType']) {
				default:
					throw new \Exception("Unknown request type!");
					break;
				case "1":
				case "heat":
					$className = \Api\App\Method\Adapter\Calculation\Heat::class;
					break;
				case "pump":
					$className = \Api\App\Method\Adapter\Calculation\Pump::class;
					break;
				case "3":
					$className = \Api\App\Method\Adapter\Request\Heat::class;
					break;
				case "pumpStation":
					$className = \Api\App\Method\Adapter\Request\PumpStation::class;
					break;
				case "meteringStation":
					$className = \Api\App\Method\Adapter\Calculation\MeteringStation::class;
					break;
			}
			if (class_exists($className)) {
				$adapter = new $className();
				$adapter->uid = $this->uid;
				$result = array();
				$result['data'] = $adapter->getCalculation();
				return $result;
			} else {
				throw new \Exception("Undefined request calс Type!!");
			}
		} else {
			throw new \Exception("Not set calc type!");
		}
	}

	public function save()
	{
		if (isset($this->request['calcType'])) {
			switch ($this->request['calcType']) {
				default:
					throw new \Exception("Unknown request type!");
					break;
				case "1":
				case "heat":
					$className = \Api\App\Method\Adapter\Calculation\Heat::class;
					break;
				case "pump":
					$className = \Api\App\Method\Adapter\Calculation\Pump::class;
					break;
				case "3":
					$className = \Api\App\Method\Adapter\Request\Heat::class;
					break;
				case "pumpStation":
					$className = \Api\App\Method\Adapter\Request\PumpStation::class;
					break;
			}
			if (class_exists($className)) {
				$adapter = new $className();
				$result = array();
				if (isset($adapter->uid)) {
					$adapter->uid = $this->uid;
				}



				$result['data'] = $adapter->saveCalculation();

				return $result;
			} else {
				throw new \Exception("Undefined request save Type!");
			}
		} else {
			throw new \Exception("Not set calc type!");
		}
	}

	public function sales()
	{
		$id = intval($this->request['id']);
		$data = $this->model::select(array('request.*', 'organizations.name as oid_name', 'object.name as object_name'));
		$data->leftJoin('organizations', 'request.oid', '=', 'organizations.id');
		$data->leftJoin('object', 'request.object', '=', 'object.id');
		$data->where($this->table . '.id', '=', $id);

		$requestData = $data->get()->toArray()[0];
		if (isset($this->request['calcType'])) {
			switch ($this->request['calcType']) {
				default:
					throw new \Exception("Unknown request type!");
					break;
				case "1":
				case "heat":
					$className = \Api\App\Method\Adapter\Request\Heat::class;
					break;
				case "5":
				case "pump":
					$className = \Api\App\Method\Adapter\Request\Pump::class;
					break;
				case "6":
				case "pumpStation":
					$className = \Api\App\Method\Adapter\Request\PumpStation::class;
					break;
			}
			if (class_exists($className)) {
				$adapter = new $className(array(
					'id' => intval($_GET['id']),
					'uid' => $this->uid,
					'requestData' => $requestData,
				));
				$result = array();
				$result['data'] = $adapter->makeSales();
				return $result;
			} else {
				throw new \Exception("Undefined request calс Type!");
			}
		} else {
			throw new \Exception("Not set calc type!");
		}
	}
}
