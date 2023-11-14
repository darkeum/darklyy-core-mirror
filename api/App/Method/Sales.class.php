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

use Chat as BaseChat;
use \Boot\Models\Attach;
use \Realization\Model\Realization;
use Organizations\Model\Organizations;
use Boot\System\Office\Spreadsheet\SpreadsheetSupport;

class Sales extends \Api\App\AppMethodLoader
{
	protected $model = \Sales\Model\Sales::class;
	protected $table = 'sales';

	public function search($requestData)
	{
		if (!empty($requestData['search'])) {
			$search = "%{$requestData['search']}%";
			// $this->data->where();
			$this->data->where(function ($query) use ($search) {
				$query->orWhere('sales.numb', 'like', $search)
					->orWhere('object.name', 'like', $search)
					->orWhere('organizations.name', 'like', $search)
					->orWhere('sales.title', 'like', $search);
			});
		}
	}

	public function get()
	{
		$this->data = $this->model::select(array('sales.*', 'object.name as object_name', 'organizations.name as oid_name'))->whereHas("client", function (\Boot\System\Database\Model\Builder $query) {
			$query->where('uid',  $this->uid);
		})->with('organization', 'object_')->leftJoin('organizations', 'sales.oid', '=', 'organizations.id')->leftJoin('object', 'sales.object', '=', 'object.id');
		if (isset($this->request['archive']) && $this->request['archive'] == '1') {
			$this->data->where('archive', 1);
		} else {
			$this->data->where('archive', 0);
		}
		$requestData = array();
		return $this->get_($requestData);
	}

	public function view()
	{
		if (!empty($this->request['id'])) {
			$id = intval($this->request['id']);
			$data = $this->model::select(array('sales.*', 'object.name as object_name', 'organizations.name as oid_name'))
				->leftJoin('organizations', 'sales.oid', '=', 'organizations.id')
				->leftJoin('object', 'sales.object', '=', 'object.id')
				->where($this->table . '.id', '=', $id);
			$request = [];
			$request['data'] = $data->first();
			$request["data"]["oid_name"] = ee($request["data"]["oid_name"]);
			$request["data"]["object_name"] = ee($request["data"]["object_name"]);

			$manager = \Boot\Models\Users::find($request['data']['uid']);
			$request['data']['manager'] = [
				'name' => $manager->name ?? null,
				'surname' => $manager->surname ?? null,
				'ochestvo' => $manager->ochestvo ?? null,
				'email' => $manager->email ?? null,
				'phone' => $manager->phone ?? null,
				'phone2' => '+7(3412)908123',
				'avatar' => config('app.url').'/' . \Boot\Models\Users::avatar($manager->id ?? null),
			];

			// $chat = new BaseChat();
			// print_r($id);
			// $chat_array = $chat->find_chat(["once" => true, "where" => ["module" => 'sales', "pid" => 'null', "type" => 2, "uid" => ""]]);
			$request['data']['chat'] = 'null';

			return $request;
		} else {
			throw new \Exception("Bad request id!");
		}
	}

	public function middleware_change($id, $sales = null)
	{
		if (empty($id)) throw new \Exception("Не указано id продажи");
		if (is_null($sales)) $sales =  $this->model::find($id);
		if ($sales->status_client >= 4) {
			throw new \Exception("Заказ размещен");
		} else {
			return true;
		}
	}

	public function update()
	{
		$return = [];
		if (!empty($this->request['id'])) {
			$sales = $this->model::find(intval($this->request['id']));
			$this->middleware_change($this->request['id'], $this->model::find(intval($this->request['id'])));
			foreach ($this->request as $key => $value) {
				switch ($key) {
					case "status_client":
						if (in_array($value, [1, 2, 4]) && $sales->{$value} <= 4) {
							$sales->{$key} = $value;
							if (in_array($value, [1, 2, 4])) {
								\Sales\Model\SalesDetail::where('sales_id', $this->request['id'])->update(['status_client' => $value]);
								$od = Organizations::findOrFail($sales->oid);
								if (!empty($sales->uid)) {
									$r_uid = $sales->uid;
								} elseif (!empty($od->default_responsible)) {
									$r_uid = $od->default_responsible;
								} else {
									$r_uid = ['2', '120'];
								}
								$attr = [];
								$attr['uid'] = $r_uid;
								switch ($value) {
									case "1":
										$attr['title'] = 'Отменил согласование';
										$attr['text'] = 'Контрагент ' . $od->name . ' отменил согласование заказа №' . $sales->numb;
										break;
									case "2":
										$attr['title'] = 'Отправил на согласование';
										$attr['text'] = 'Контрагент ' . $od->name . ' отправил на согласование заказ №' . $sales->numb;
										break;
									case "3":
										$attr['title'] = 'Разместил заказ';
										$attr['text'] = 'Контрагент ' . $od->name . ' разместил заказ №' . $sales->numb;
										break;
								}
								$attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
								$attr['url'] = config('app.url').'/administration/module/sales/order/' . $sales->id;
								$attr['pNotif'] = true;
								$attr['pPush'] = true;
								sendAllNotif($attr);
							}
						} else {
							$return["error"] = "Неприменимый статус";
						}
						break;
				}
			}
			if ($sales->isDirty()) {
				$sales->save();
				$return = [
					'type' => 'success',
				];
			} else {
				$return["type"] = 'error';
				if (empty($return["error"])) $return["type"] = 'Нет доступных полей для обновления';
			}
		} else {
			$return = [
				'type' => 'error',
				'error' => 'Не указано id продажи'
			];
		}

		return $return;
	}

	public function update_no_protect()
	{
		$return = [];
		if (!empty($this->request['id'])) {
			$sales = $this->model::find(intval($this->request['id']));
			foreach ($this->request as $key => $value) {
				switch ($key) {
					case "client_comment":
					case "client_title":
						$sales->{$key} = $value;
						break;
					case "archive":
						$sales->archive = ($value == 1) ? 1 : 0;
						break;
				}
			}
			if ($sales->isDirty()) {
				$sales->save();
				$return = [
					'type' => 'success',
				];
			} else {
				$return["type"] = 'error';
				if (empty($return["error"])) $return["type"] = 'Нет доступных полей для обновления';
			}
		} else {
			$return = [
				'type' => 'error',
				'error' => 'Не указано id продажи'
			];
		}

		return $return;
	}

	public function detail()
	{
		$return = [];
		if (!empty($this->request['id'])) {
			$id = intval($this->request['id']);
			$r = \Sales\Model\SalesDetail::where('sales_id', $id)->orderBy('position', 'ASC')->get()->toArray();
			$rr = [];
			$i = 0;
			$total = 0;
			foreach ($r as $key => $value) {
				$i++;
				$ct = \Catalog_v2\Model\Catalog::find($value['cid']);
				$gppds = \Sales\Model\SalesDetail::getDatePost($value["id"], ["sales_detail" => (object)$value]);
				if ($value['status_client'] >= 6) {
					$date_post = '';
				} else {
					$date_post = empty($gppds->date_post) ? ($value['local_date_delivery'] ?? 'По согласованию') : $gppds->date_post;
				}
				$rr[] = [
					'id' => $value['id'],
					'cid' => $value['cid'],
					'position' => $i,
					'name' => ee($value['s_name']),
					'unit' => get_unit('data', $value['s_unit'], 'sname'),
					'preview' => isset($ct->preview) ? 'https://atri-energo.ru' . $ct->preview : '',
					'sku' => $value['s_sku'],
					'total' => number_format($value['total'], 2, ',', ' '),
					'count' => $value['count'],
					'price' => number_format(($value['total'] / $value['count']), 2, ',', ' '),
					'delivery' => $date_post,
					'delivery_type' => $value['local_date_delivery'],
					'status' => $value['status_client'],
					'nds_type' => 'С НДС 20%',
				];

				$total += $value['total'];
			}
			$request['data'] = $rr;
			$request['total']['total'] = number_format($total, 2, ',', ' ');
			$request['total']['count'] = $i;
		} else {
			$request = [
				'type' => 'error',
				'error' => 'Не указано id продажи'
			];
		}

		return $request;
	}

	public function add_detail()
	{
		$request = [];
		if (!empty($this->request['id'])) {
			$this->middleware_change($this->request['id']);
			$request = $_POST;
			$request["sales_data"] = \Sales\Model\Sales::findOrFail($request["id"]);
			\Sales\Support\SalesDetailSupport::victoria_order_tovar_add($request);
			$request = [
				'type' => 'success',
			];
		} else {
			$request = [
				'type' => 'error',
				'error' => 'Не указано id продажи'
			];
		}

		return $request;
	}

	public function delete_detail()
	{
		$request = [];
		if (!empty($this->request['id'])) {
			$id = intval($this->request['id']);
			$sales_detail = \Sales\Model\SalesDetail::findOrFail($id);
			$this->middleware_change($sales_detail->sales_id);

			$sales_detail->delete();
			// print_r($id);
			$request = [
				'type' => 'success',
			];
		} else {
			$request = [
				'type' => 'error',
				'error' => 'Не указано id товара'
			];
		}

		return $request;
	}

	public function update_detail()
	{
		$return = [];
		if (!empty($this->request['id'])) {
			$id = intval($this->request['id']);
			$sales_detail = \Sales\Model\SalesDetail::find($id);
			$sales_id = $sales_detail->sales_id;
			$this->middleware_change($sales_id);
			foreach ($_POST as $key => $value) {
				switch ($key) {
					case "count":
						$sales_detail->{$key} = $value > 0 ? (float)$value : 1;
						break;
				}
			}

			if ($sales_detail->isDirty()) {
				$sales_detail->save();
				\Sales\Support\SalesDetailSupport::recount_sales($sales_id);

				$return = [
					'type' => 'success',
				];
			} else {
				$return = [
					'type' => 'error',
					'error' => 'Нет доступных полей для обновления'
				];
			}
		} else {
			$return = [
				'type' => 'error',
				'error' => 'Не указано id продажи'
			];
		}

		return $return;
	}

	public function store()
	{
		if (empty($this->request["oid"])) 	throw new \Exception("Отсутствует организация");
		if (empty($this->request["object"])) 	throw new \Exception("Отсутствует объект");

		$sales = new \Sales\Model\Sales;
		$sales->oid = $this->request["oid"];
		$sales->object = $this->request["object"];
		$sales->uid_send = $this->uid;
		$sales->org = 1;
		$od = Organizations::findOrFail($this->request["oid"]);
		if (!empty($od->default_responsible)) {
			$sales->uid = $od->default_responsible;
		}
		$sales->save();

		$SalesUsers = new \Sales\Model\SalesUsers;
		$SalesUsers->sales_id = $sales->id;
		$SalesUsers->type = "CLIENT";
		$SalesUsers->uid = $this->uid;
		$SalesUsers->save();




		if (!empty($od->default_responsible)) {
			$r_uid = $od->default_responsible;
		} else {
			$r_uid = ['2', '120'];
		}
		$attr = [];
		$attr['uid'] = $r_uid;
		$attr['title'] = 'Новый заказ';
		$attr['text'] = 'Контрагент ' . $od->name . ' создал новый заказ';
		$attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
		$attr['url'] = config('app.url').'/administration/module/sales/order/' . $sales->id;
		$attr['pNotif'] = true;
		$attr['pPush'] = true;
		sendAllNotif($attr);


		return [
			"id" => $sales->id,
		];
	}


	public function changeDiscount()
	{

		return [
			"status" => 'success'
		];
	}

	public function realizationAttach()
	{
		if (!empty($this->request['id'])) {
			$id = intval($this->request['id']);
			$result = [];
			$realization = Realization::where('sales_id', $id)->where('scan', 1)->where('active', 1)->get();
			foreach ($realization as $key => $value) {
				$attach = Attach::where('module', 'realization')->where('pid', $value->id)->get();
				$attachArray = [];
				if (!empty($attach)) {
					foreach ($attach as $k => $v) {
						$attachArray[] = [
							'id' => $v->id,
							'url' => config('app.url').'/' . $v->url,
							'name' => $v->name
						];
					}
				}
				$result[] = [
					'id' => $value->id,
					'numb' => $value->numb,
					'date' => $value->date,
					'attach' => $attachArray,
				];
				// print_r($value->id);
			}




			return $result;
		}
	}

	public function excelImport()
	{
		if (isset($this->files["file"], $this->request["id"])) {
			$id = $this->request["id"];
			$items = (new SpreadsheetSupport)->get_excel_data($this->files["file"]);
			$len = count($items);
			$flag = false;
			$data = ["id" => $id, "items" => []];
			if ($len > 0) {
				foreach ($items as $item) {
					$catalog = \Catalog_v2\Model\Catalog::where("sku", $item[0])->where("active", 1)->where("hidden", 0)->first();
					if (!empty($catalog)) {
						$data["items"][$catalog->id] = [
							"cid" => $catalog->id,
							"count" => $item[1],
						];
						$flag = true;
					}
				}
				if ($flag) \Sales\Support\SalesDetailSupport::victoria_order_tovar_add($data);
			}
			return [
				'type' => 'success',
				'text' => "Товаров добавлено " . count($data["items"]),
			];
		} else {
			throw new \Exception("Файл отсутствует");
		}
	}
}
