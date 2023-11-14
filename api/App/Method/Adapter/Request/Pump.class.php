<?php

/**
 * @name        JMY CMS
 * @link        http://jmy.su/
 * @copyright   Copyright (C) 2012-2020 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */


namespace Api\App\Method\Adapter\Request;

use Project\Actions\Calculation\Pump\ApiPumpResultAction;
use Project\UI\API\Requests\Calculation\PumpCalculationRequest;

class Pump
{
	protected $requestData = array();

	public function __construct($requestData)
	{
		$this->requestData = $requestData;
	}

	public function getAdapterData()
	{
		// var_dump($this->requestData['request_data']);
		// exit;
		$total = 0;	
		$this->requestData['request_data']['cid'] = $this->requestData['request_data']['pump'];
		$request = PumpCalculationRequest::injectData($this->requestData['request_data']);
		$pc = app(ApiPumpResultAction::class)->run($request);
		
		$result = array();
		$result['id'] = $this->requestData['id'];
		$result['object_name'] = $this->requestData['object_name'];
		$result['date'] = $this->requestData['date'];
		$result['dateView'] = date('d.m.Y H:i:s', strtotime($this->requestData['date']));
		$result['dateOnly'] = date('d.m.Y', strtotime($this->requestData['date']));
		$result['total'] = number_format($total, 2, ',', ' ');
		$result['pid'] = $this->requestData['pid'];
		$result['fullTitle'] = $this->requestData['title_user'] . ' от ' . $result['dateOnly'];
		$result['entities'] = $pc['entities'][0];
		$result['entities']['file'] = $this->requestData['aid'] ?? 0;
		$result['type'] = $this->requestData['type'];
		$result['solutionType'] = null;
		return $result;
	}

	

	public function makeSales()
	{
		$pid[] = $this->requestData['requestData']['pid'];
		$total = 0;
		$nds = 0;
		$sDetail = array();

		$cid = $this->requestData['requestData']['request_data']['cid'];
		$count = $this->requestData['requestData']['request_data']['pump_count'];
		

		$price = \Price::getPrice($cid);

				$priceAll = \Price::get_all(array(
					'price' => $price->price_list_,
					'count' => $count
				));
				$total += $priceAll['total'];
				$nds += $priceAll['nds'];
				$sDetail[] = array(
					'cid' => $cid,
					'price' => $priceAll['price'],
					'count' => $count,
					'nds' => $priceAll['nds'],
					'total' => $priceAll['total'],

				);

		$request = array(
			'object' => $this->requestData['requestData']['object'],
			'oid' => $this->requestData['requestData']['oid'],
			'oid' => $this->requestData['requestData']['oid'],
			'org' => '1',
			'nds_includes' => '1',
			'stock_id' => '1',
			'uid_send' => $this->requestData['uid'],
			'nds' => $nds,
			'total' => $total,
		);
		$sales = \Sales\Models\Sales::create($request);
		foreach ($sDetail as $key => $value) {
			$catalog = \Catalog_v2\Model\Catalog::find($value['cid'])->toArray();
			$requestDetail = array(
				'sales_id' => $sales->id,
				'cid' => $value['cid'],
				's_sku' => $catalog['sku'],
				's_name' => $catalog['title'],
				'price_type' => '3',
				's_unit' => $catalog['unit'],
				'count' => $value['count'],
				's_price' => $value['price'],
				'type_nds' => 3,
				'nds' => $value['nds'],
				'total' => $value['total'],
			);
			$salesDetail = \Sales\Model\SalesDetail::create($requestDetail);
		}

		$SalesUsers = new \Sales\Model\SalesUsers;
		$SalesUsers->sales_id = $sales->id;
		$SalesUsers->type = "CLIENT";
		$SalesUsers->uid = $this->requestData['uid'];
		$SalesUsers->save();
		$type = 'КП';
		return array(
			'type' => 'success',
			'title' =>  $type . '_' . $sales->numb . '_' . $sales->created_at,
			'url' => 'https://api.atri-energo.ru/v1/sales/print/kp/' . $sales->id . '&format=pdf',
			'sales_id' => $sales->id,
		);
	}
}
