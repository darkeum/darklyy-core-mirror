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

use \Boot\Models\Users;
use \Order\Model\Order;
use \Boot\Support\Facades\Auth;
use \Order\Model\OrderPumpStation;
use \Calculation\Model\HeatSolution;
use Project\Actions\Calculation\Pump\ApiPumpResultAction;
use Project\UI\API\Requests\Calculation\PumpCalculationRequest;
use Request\Model\Request;

class PumpStation
{
	public $uid = 'null';

	protected $requestData = [];

	public function __construct($requestData)
	{
		$this->requestData = $requestData;
	}


	public function getAdapterData()
	{
		$request = Request::find($this->requestData['id']);
		$res = $request->result[0];
		// var_dump($result);
		// exit;
		$total = 0;

		$preview = '';
		switch ($res['info']['series_calculate']) {
			case 'wp':
				$preview = 'https://atri-energo.ru/files/media/ns1.png';
				$total =  $res['price_series_1'];
				$description =  'Насосные установки повышения давления с частотным преобразователем степенью защиты IP65 с функцией контроллера на каждом насосе предназначены для обеспечения требуемого расхода и напора воды в следующих системах:';
				$title = $res['info']['title'] . ' Smart WP';
				break;
			case 'wpb':
				$preview = 'https://atri-energo.ru/files/media/ns2.png';
				$total =  $res['price_series_2'];
				$description =  'Насосные установки повышения давления с частотными преобразователями на каждый насос с функцией контроллера в шкафу, степенью зашиты класса IP 65 предназначены для обеспечения требуемого расхода и напора воды в следующих системах:';
				$title = $res['info']['title'] . ' Smart WPB';
				break;
			case 'wpbs':
				$preview = 'https://atri-energo.ru/files/media/ns3.png';
				$total =  $res['price_series_3'];
				$description =  'Насосные установки повышения давления с частотными преобразователями на каждый насос с функцией контроллера с собственным программным обеспечением в шкафу, степенью зашиты класса IP 65 предназначены для обеспечения требуемого расхода и напора воды в следующих системах:';
				$title = $res['info']['title'] . ' Smart Control WPB';
				break;
		}

		$title_parameter = 'Рабочие параметры насосной станции ';
        if ($res['info']['pump_reserve']) {
            $title_parameter .= '(' . $res['info']['pump_count'] . ' рабочих + ' . $res['info']['pump_reserve_count'] . ' резервный)';
        } else {
            $title_parameter .= '(' . $res['info']['pump_count'] . ' рабочих)';
        }
		$attribute_cc = cc($res['info']['pump_catalog']['attribute']);
		$power = $attribute_cc->where('id', '137')->first()['value'];
        $power_all = $power * $res['info']['pump_count'];

		$entities = [];
		$entities[] = [
			'sku' => '',
			'cid' => $res['info']['cid'],
			'attribute' => $res['info']['attribute'],
			'title' => $title,
			'preview' => $preview,
			// 'price_series_1' => $value['price_series_1'],
			// 'price_series_2' => $value['price_series_2'],
			// 'price_series_3' => $value['price_series_3'],
			'price' => number_format($total, 2, ',', ' '),
			'delta' => 0,
			'man_stock' => 0,
			'storage' => 0,
			'man' => 'АТРИ-ЭНЕРГО',
			'description' => $description,
			'title_parameter' => $title_parameter,
			'series' => $res['series'],
			'charts' => $res['info']['charts'],
			'points' => $res['info']['points'],
			'q_all' => $res['info']['q_all'] . ' м³/ч',
			'h' => $res['info']['h'] . ' м.в.ст',
			'power_all' => $power_all. ' кВт',
			'speed' => '2 900 1/мин',
			'frequency' => '50 Гц',
		];


		$result = array();
		$result['id'] = $this->requestData['id'];
		$result['object_name'] = $this->requestData['object_name'];
		$result['date'] = $this->requestData['date'];
		$result['dateView'] = date('d.m.Y H:i:s', strtotime($this->requestData['date']));
		$result['dateOnly'] = date('d.m.Y', strtotime($this->requestData['date']));
		$result['total'] = number_format($total, 2, ',', ' ');
		$result['pid'] = $this->requestData['pid'];
		$result['fullTitle'] = $this->requestData['title_user'] . ' от ' . $result['dateOnly'];
		$result['entities'] = $entities[0];
		$result['entities']['file'] = $this->requestData['aid'] ?? 0;
		$result['type'] = $this->requestData['type'];
		$result['man'] = 'АТРИ-ЭНЕРГО';
		$result['solutionType'] = null;
		return $result;
	}



	public function makeSales()
	{
		$pid[] = $this->requestData['requestData']['pid'];
		$total = 0;
		$nds = 0;
		$sDetail = array();

		$request = Request::find($this->requestData['id']);

		$cid = $request['final_cid'];
		$count = 1;

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
