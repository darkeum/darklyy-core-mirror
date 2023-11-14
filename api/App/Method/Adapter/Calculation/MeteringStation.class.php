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

use \Project\Calculation\MeteringCalculation;
use \Boot\Models\GeoRegion;
use \Boot\Models\GeoCity;
use \Boot\Models\GeoTso;

class MeteringStation
{

	public $requestData = array();
	public $uid = 0;
	public $request = 0;

	public function getCalculation()
	{
		$mc = new MeteringCalculation;
		$mc->setUid($this->uid);
		$mc->makeRequest();
		$mc->init();
		$result['type'] = 'success';
		$result['file'] =  $mc->makeProject(false);
		$result['request'] = $mc->id;
		$mc->save();
		return $result;
	}

	public function getCalculationData()
	{
		$code = $this->request['dataCode'] ?? null;
		switch($this->request['dataType'] ?? null){
			default:
				throw new \Exception("Unknown data type!");
			break;			
			case "region":
				$result['type'] = 'success';
				$region = GeoRegion::where('status', 1);
				if(!empty($code))	{
					$region->where('gcid', $code);
				}
				$result['entity'] = $region->get();
			break;
			case "city":
				$result['type'] = 'success';
				$region = GeoCity::where('status', 1);
				if(!empty($code))	{
					$region->where('grid', $code);
				}
				$result['entity'] = $region->get();
			break;
			case "tso":
				$result['type'] = 'success';
				$region = GeoTso::where('status', 1);
				if(!empty($code))	{
					$region->where('gcitid', $code);
				}
				$result['entity'] = $region->get();
			break;
		}	
	
		return $result;
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

		$result['type'] = 'success';
		$result['aid'] = $pc->createCalculationAttach();
		$result['request'] = $pc->requestId;
		return $result;
	}
}
