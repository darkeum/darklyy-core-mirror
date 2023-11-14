<?php

namespace Boot\Traits;

trait SearchApi
{
	protected $request = false;
	protected $files = [];
	protected $uid = false;
	protected $data;

	protected $page_size = 20;

	public function __construct()
	{
		$this->request = $_REQUEST;
		$this->files = $_FILES;
	}

	public function get_($requestData = [])
	{
		$this->filter($requestData);
		if(method_exists($this, 'search')) {
			$this->search($this->request);
		}
	

		$result = [];
		$result['data']['totalCount'] = $this->data->count();

		$this->page_size($requestData);
		$this->sort($requestData);


		$dataArray = $this->data->get();

		// print_r($dataArray);
		$dataArrayClear = [];
		foreach ($dataArray as $k => $v) {
			$element = [];
			foreach ($v as $kk => $vv) {
				if(!is_array($vv)){
					$element[$kk] = ee($vv);
				}
			}
			$dataArrayClear[$k] = $element;
		}
		$result['data']['entities'] = $dataArray;

		return $result;
	}

	public function getModel($requestData = [])
	{
		$this->filter($requestData);
		if(method_exists($this, 'search')) {
			$this->search($this->request);
		}
		return $this->data->paginate(15);
	}

	public function filter($requestData)
	{
		$table = new $this->model;
		$filter = $this->prepareFilter(['table' => $table->getTable()]);

		if (isset($requestData["filter"])) {
			foreach ($requestData["filter"] as $k => $v) {
				$filter[$k] = $v;
			}
		}

		if (!empty($filter)) {
			foreach ($filter as $k => $v) {
				if (isset($v[0])) {
					foreach ($v as $vv) {
						$this->data->where($k, $vv['operator'], $vv['value']);
					}
				} else {
					if (is_array($v)) {
						$this->data->where($k, $v['operator'], $v['value']);
						
					}
				}
			}
		}
	}

	public function page_size($requestData = [])
	{
		if (isset($this->request['pageNumber'])) {
			$pageNumber = intval($this->request['pageNumber']) - 1;
			if (isset($this->request['pageSize'])) {
				$pageSize = intval($this->request['pageSize']);
			} elseif (isset($requestData['pageSize'])) {
				$pageSize = intval($requestData['pageSize']);
			} else {
				$pageSize = $this->page_size;
			}
			$this->data->offset($pageNumber * $pageSize);
			$this->data->limit($pageSize);
		} else {
			if (isset($this->request['limit'])) {
				$this->data->limit(intval($this->request['limit']));
			}
			if (isset($this->request['offset'])) {
				$this->data->offset(intval($this->request['offset']));
			}
		}
	}

	public function sort($requestData = [])
	{
		if (isset($this->request['sortField'])) {
			$sortField = $this->request['sortField'];
			if (isset($requestData['table'])) {
				$sortField = $requestData['table'] . '.' . $sortField;
			}
			$sortOrder = 'desc';
			if (isset($this->request['sortOrder'])) {
				$sortOrder = $this->request['sortOrder'];
			}
			$this->data->orderBy($sortField, $sortOrder);
		}
	}

	public function prepareFilter($parameter = array())
	{
		if (isset($this->request['filter']) && !empty($this->request['filter'])) {
			$filterArrayTemp = json_decode($this->request['filter'], true);
			if (isset($parameter['table']) || isset($parameter['whiteList'])) {
				$result = array();
				foreach ($filterArrayTemp as $k => $v) {
					$field = $k;
					if (isset($parameter['table'])) {
						$field = $parameter['table'] . '.' . $field;
					}
					$result[$field] = $v;
				}
			} else {
				$result = $filterArrayTemp;
			}
		} else {
			$result = [];
		}
		return $result;
	}
}