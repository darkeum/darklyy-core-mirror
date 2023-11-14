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

use \Calculation\Model\HeatSolution;
use \Pto\Model\Pto;
use \Catalog_v2\Model\Catalog;
use \Boot\Support\Facades\Config;
use \Sales\Model\Sales;
use \Sales\Model\SalesDetail;
use \Price;
use \Organizations\Model\Organizations;

class Heat
{
	protected $requestData = array();

	public function __construct($requestData)
	{
		$this->requestData = $requestData;
	}

	public function getAdapterData()
	{
		$total = 0;
		$solution = array();
		$pid = $this->requestData['pid'];
		$heatSolution = HeatSolution::find($pid)->toArray();
		$step[0] = 0;
		if ($heatSolution['result'][0]['SolutionType'] == 'GVS2SEPARATED') {
			$step[0] = 0;
			$step[1] = 1;
		}
		foreach ($step as $value) {

			$usePTOSize = $heatSolution['result'][$value]['UsePTOSize'];
			$pto = Pto::where('ptoTags', 'like', '%"' . $usePTOSize . '"%')->get()->toArray();
			$preview = '';
			$ptoUrl = '';
			$ptoType = $heatSolution['result'][$value]['PTOType'];
			if (isset($pto[0])) {
				$preview = (!empty($pto[0]['img_3d'])) ? $pto[0]['img_3d'] : $pto[0]['img_2d'];
				$ptoUrl = Config::get('global.url').'/pto/' . $pto[0]['altname'] . '.html';
			}
			$solution[] = array(
				'aid' => $heatSolution['aid'][$value],
				'cid' => $heatSolution['cid'][$value],
				'deliveryWeeks' => $heatSolution['deliveryWeeks'],
				'detailed' => $heatSolution['detailed'],
				'result' => $heatSolution['result'],
				'response' => $heatSolution['response'],
				'effectiveSurface' => $heatSolution['effectiveSurface'],
				'heat_id' => $heatSolution['heat_id'],
				'height' => $heatSolution['height'],
				'length' => $heatSolution['length'],
				'price' => $heatSolution['result'][$value]['price']['value']*1.2,
				'solution' => $heatSolution['solution'],
				'weight' => $heatSolution['weight'],
				'width' => $heatSolution['width'],
				'description' => 'Аппарат теплообменный пластинчатый разборный ' . $ptoType . '' . $usePTOSize . ' - это функциональное и уникальное в своем роде устройство, предназначенное для обмена жидкостями и газами в системах теплоснабжения.',
				'preview' => $preview,
				'ptoUrl' => $ptoUrl,
				'brand' => 'РИДАН (Danfoss)',
				'object_name' => $this->requestData['object_name'],
				'dateView' => date('d.m.Y H:i:s', strtotime($this->requestData['date'])),

			);
			$total = $total + $heatSolution['result'][$value]['price']['value']*1.2;
		}
		$result = array();
		//общее
		$result['id'] = $this->requestData['id'];
		$result['object_name'] = $this->requestData['object_name'];
		$result['date'] = $this->requestData['date'];
		$result['dateView'] = date('d.m.Y H:i:s', strtotime($this->requestData['date']));
		$result['dateOnly'] = date('d.m.Y', strtotime($this->requestData['date']));
		$result['total'] = number_format($total, 2, ',', ' ');
		$result['pid'] = $this->requestData['pid'];
		$result['type'] = $this->requestData['type'];
		$result['solutionType'] = $heatSolution['result'][0]['SolutionType'];
		$result['fullTitle'] = $this->requestData['title_user'] . ' от ' . $result['dateOnly'];
		//решения
		$result['solution'] = $solution;
		return $result;
	}

	public function makeSales()
	{
		$pid[] = $this->requestData['requestData']['pid'];
		$total = 0;
		$nds = 0;
		$sDetail = array();
		foreach ($pid as $value) {
			$heatSolution = HeatSolution::find($value)->toArray();
			foreach ($heatSolution['cid'] as $k => $v) {
				$price = \Price::getPrice($v);

				$priceAll = \Price::get_all(array(
					'price' => $price->price_list_,
					'count' => 1
				));
				$total += $priceAll['total'];
				$nds += $priceAll['nds'];
				$sDetail[] = array(
					'cid' => $v,
					'price' => $priceAll['price'],
					'nds' => $priceAll['nds'],
					'total' => $priceAll['total'],

				);
			}
		}
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
			'uid' => \Boot\Support\Facades\Auth::id(),
		);
		$sales = Sales::create($request);


		$SalesUsers = new \Sales\Model\SalesUsers;
		$SalesUsers->sales_id = $sales->id;
		$SalesUsers->type = "CLIENT";
		$SalesUsers->uid = $this->requestData['uid'];
		$SalesUsers->save();

		// $od = Organizations::findOrFail($this->request["oid"]);
		// if(!empty($od->default_responsible)) {
		// 	$r_uid = $od->default_responsible;
		// } else {
		// 	$r_uid = ['2', '120'];
		// }
		// $attr = [];
		// $attr['uid'] = $r_uid;
		// $attr['title'] = 'Новый заказ из расчета ПТО';
		// $attr['text'] = 'Контрагент '.$od->name.' создал новый заказ из расчета ПТО';
		// $attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
		// $attr['url'] = config('app.url').'/administration/module/sales/order/'.$sales->id;
		// $attr['pNotif'] = true;
		// $attr['pPush'] = true;
		// sendAllNotif($attr);


		foreach ($sDetail as $value) {
			$catalog = Catalog::find($value['cid'])->toArray();
			$requestDetail = array(
				'sales_id' => $sales->id,
				'cid' => $value['cid'],
				's_sku' => $catalog['sku'],
				's_name' => $catalog['title'],
				'price_type' => '3',
				's_unit' => $catalog['unit'],
				'count' => 1,
				's_price' => $value['price'],
				'type_nds' => 3,
				'nds' => $value['nds'],
				'total' => $value['total'],
			);
			SalesDetail::create($requestDetail);
		}
		$type = 'КП';
		return array(
			'type' => 'success',	
			'title' =>  $type . '_' . $sales->numb . '_' . $sales->created_at,		
			'url' => 'https://api.atri-energo.ru/v1/sales/print/kp/' . $sales->id . '&format=pdf',
			'sales_id' => $sales->id,
		);
	}
}
