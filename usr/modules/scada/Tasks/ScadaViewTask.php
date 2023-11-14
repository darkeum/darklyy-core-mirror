<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\Tasks;

use Scada\Models\Scada;
use Boot\Abstracts\Tasks\Task;

class ScadaViewTask extends Task
{
	public function run(array $data): array
	{
		$id = intval($data['id']);
		$data = Scada::select(array('scada.id', 'scada.title', 'scada.uid', 'scada.oid', 'organizations.name as oid_name', 'scada.object', 'object.name as object_name', 'scada.status'))
			->leftJoin('organizations', 'scada.oid', '=', 'organizations.id')
			->leftJoin('object', 'scada.object', '=', 'object.id')->where('scada.id', $id);
		$request = [];
		$request['data'] = $data->first();
		$request["data"]["oid_name"] = ee($request["data"]["oid_name"]);
		$request["data"]["object_name"] = ee($request["data"]["object_name"]);
		return $request;
	}
}
