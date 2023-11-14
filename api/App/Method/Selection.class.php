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

use \Scan\Scan as BaseScan;
use \Selection\Model\SelectionArea;

class Selection extends \Api\App\AppMethodLoader
{
	protected $model = SelectionArea::class;
	protected $table = 'selection_area';

	public function create()
	{
		if(!empty($_POST['projectName']) && !empty($_POST['oid']) && !empty($_POST['object_id'])) {
			$requset = [
				'title' => $_POST['projectName'],
				'oid' => $_POST['oid'],
				'object_id' => $_POST['object_id'],
				'uid' => $this->uid,
			];
			$sa = SelectionArea::create($requset);
			return [
				'type' => 'success',
				'area_id' => $sa->id,
			];
		}
		return [
			'type' => 'error',
			'error' => 'Invalid or empty argument (projectName, oid, object_id)',
		];
	}

	public function get()
	{
		$this->data = $this->model::select(array('selection_area.*', 'object.name as object_name', 'organizations.name as oid_name'))->where('selection_area.uid',  $this->uid)->leftJoin('organizations', 'selection_area.oid', '=', 'organizations.id')->leftJoin('object', 'selection_area.object_id', '=', 'object.id');
		$requestData = array();
		return $this->get_($requestData);
	}

	public function save()
	{
		
		return $_POST;
	}

	
}
