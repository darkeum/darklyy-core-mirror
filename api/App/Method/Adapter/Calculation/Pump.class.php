<?php

/**
 * @name        JMY CMS
 * @link        http://jmy.su/
 * @copyright   Copyright (C) 2012-2020 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */


namespace Api\App\Method\Adapter\Calculation;

class Pump
{
	
	protected $requestData = array();
	public $uid = 0;

	public function getCalculation()
	{
		$pc = new \Calculation\PumpCalculation();
		$request = [];
		$request['h'] = $_POST['pressure'] ?? '';
		$request['q'] = $_POST['qPump'] ?? '';
		$request['u'] = $_POST['r2'] ?? '';
		$request['p'] = $_POST['p'] ?? array('10 bar', '16 bar');
		$request['coefficient'] = $_POST['coefficient'] ?? '1.05';
		$request['pumpUse'] = $_POST['type'] ?? '';
		$request['frequency'] = $_POST['r1'] ?? '';
		$request['manufacture'] = $_POST['manufacture'] ?? '0';
		$request['model'] = $_POST['model'] ?? '0';
		$request['soloOrBinary'] = $_POST['soloOrBinary'] ?? '0';
		$request['pumpSelector'] = $_POST['pumpSelector'] ?? '0';
		
		$pc->setData($request);
		$pc->setSearchType();

		
		$pc->collectPump(false);
		
		return $pc->getResult();
		
	}

	public function saveCalculation()
	{
		$pc = new \Calculation\PumpCalculation();
		$request = [];
		$request['h'] = $_POST['pressure'] ?? '';
		$request['q'] = $_POST['qPump'] ?? '';
		$request['u'] = $_POST['r2'] ?? '';
		$request['p'] = $_POST['p'] ?? array('10 bar', '16 bar');
		$request['coefficient'] = $_POST['coefficient'] ?? '1.05';
		$request['pumpUse'] = $_POST['type'] ?? '';
		$request['frequency'] = $_POST['r1'] ?? '';
		$request['t_oid'] = $_POST['t_oid'] ?? '';
		$request['object_id'] = $_POST['object_id'] ?? '';
		$request['manufacture'] = $_POST['manufacture'] ?? '0';
		$request['model'] = $_POST['model'] ?? '0';
		$request['soloOrBinary'] = $_POST['soloOrBinary'] ?? '0';
		$request['pumpSelector'] = $_POST['pumpSelector'] ?? '0';
		
		$pc->setData($request);
		$pc->setSearchType($request);

		$pc->uid = $this->uid;
		$pc->setPump($_POST['cid']);
		$pc->collectPump(false);
		$pc->createRequestAfter();
		// var_dump($pc);
		$result['type'] = 'success';
		$result['aid'] = $pc->createCalculationAttach();
		$result['request'] = $pc->requestId;
		// var_dump($result);
		return $result;
	}
}
