<?php

namespace Realization;

use \DB;
use \Module;
use Realization\Model\ResaleRelation;

class Resale
{

    protected $uid;
    protected $realization_id;
    protected $realization_detail_id;
    protected $org_data;
    protected $resale_org;
    protected $resale_org_data;
    protected $resale_stock_id;

    protected $realization;
    protected $realization_detail;

    protected $resale_sales;
    protected $resale_sales_detail;
    protected $resale_realization;
    protected $resale_realization_detail;

    protected $supply_order;
    protected $supply_order_detail;
    protected $receipts;
    protected $receipts_detail;
    protected $reserve;

    protected $purchase_price = [];

    protected $error = false;
    protected $uniqid;

    public function __construct(int $realization_id, array $realization_detail_id, int $resale_org, int $resale_stock_id)
    {
        $this->uid = \Boot\Support\Facades\Auth::id();
        $this->uniqid = uniqid();
        $this->realization_id = $realization_id;
        $this->realization_detail_id = $realization_detail_id;
        $this->resale_org = $resale_org;
        $this->resale_stock_id = $resale_stock_id;
        //получение данных
        $this->get_data();
    }
    public function start(): bool
    {
        try {
            //открываем транзакцию
            DB::beginTransaction();
            //проверка данных
            $this->check_data();
            //создать продажу
            $this->create_sales();
            //создать реализацию
            $this->create_realization();
            //создать счёт поставщика
            $this->create_supply_order();
            //создать поступление
            $this->create_receipts();
            //сохраняем все связи
            $this->save_relation();
            //удаление резервов
            $this->remove_reserve();
            //закрываем транзакцию
            DB::commit();
            return true;
        } catch (\Exception $e) {
            $this->error = [
                "message" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ];
            //откатываем транзакцию
            DB::rollBack();
            return false;
        }
    }
    public function is_error(): bool
    {
        return (bool)$this->error;
    }
    public function get_error()
    {
        return $this->error;
    }
    protected function get_data()
    {
        $this->realization = \Realization\Model\Realization::findOrFail($this->realization_id);
        $this->realization_detail =  $this->realization->realization_detail()->whereIn("id", $this->realization_detail_id)->get();
        $this->org_data = getOrgInfo($this->realization->org);
        $this->resale_org_data = getOrgInfo($this->resale_org);

        $this->purchase_price = [];
        foreach ($this->realization_detail as $item) {
            $price_data = \Sales\Model\SalesDetail::getPurchase($item->sdid);
            $this->purchase_price[$item->sdid] = $price_data->price;
        }
    }
    public function check_data()
    {
        //проверка на товары
        if (count($this->realization_detail_id) == 0 || count($this->realization_detail) == 0) throw new \Exception('Товары не указаны!');
        //проверка на объект
        if (empty($this->realization->object)) throw new \Exception('Не выбран объект!');
        //проверка на контрагента
        if (empty($this->realization->oid)) throw new \Exception('Не выбран контрагент!');
        //проверка склада
        if (\Stocks\Models\Stock::where("org", $this->resale_org)->where("id", $this->resale_stock_id)->doesntExist()) throw new \Exception('Склад не принадлежит организации!');
        //проверка на пустую цену
        foreach ($this->purchase_price as $sdid => $purchase) {
            if (empty($purchase) || $purchase == 0) {
                $item = $this->realization_detail->firstWhere("sdid", $sdid);
                throw new \Exception("Товар поз. {$item->position} {$item->s_name} артикул: {$item->s_sku} цена закупки отсутствует!");
            }
        }
        //проверка на наличие
        $data = [];
        foreach ($this->realization_detail as $item) {
            $data[] = [
                "pid" => $item->sdid,
                "module" => "sales",
                "count" => $item->count,
                "cid" => $item->cid,
                "stock_id" => $this->resale_stock_id,
                "realization_detail" => $item,
            ];
        }
        $pid = check_minus_count_custom(["data" => $data, "org" => $this->resale_org, "module" => "sales", "pid" => ""]);
        if (!is_null($pid)) {
            $item = $data[$pid]["realization_detail"];
            throw new \Exception("Товар поз. {$item->position} {$item->s_name} артикул: {$item->s_sku} <b>отсутствует</b> на складе партнера!");
        }
    }
    protected function create_sales()
    {
        $this->resale_sales = \Sales\Models\Sales::create([
            "org" => $this->resale_org,
            "stock_id" => $this->resale_stock_id,
            "oid" => $this->org_data["org"]["oid"],
            "object" => $this->realization->object,
            "uid" => $this->uid,
        ]);
        $this->resale_sales_detail = [];
        $total = 0;
        $nds = 0;
        foreach ($this->realization_detail as $item) {
            $p = \Price::get_all([
                "price" => $this->get_prepare_purchase_price($item->sdid, $item->s_price, $item->discount, $this->resale_org_data["org"]["nds_includes"], $this->resale_org_data["org"]["nds"]),
                "count" => $item->count,
                "nds_includes" => $this->resale_org_data["org"]["nds_includes"],
                "nds_type" => $this->resale_org_data["org"]["nds"],
            ]);
            $this->resale_sales_detail[] = \Sales\Model\SalesDetail::create([
                "sales_id" => $this->resale_sales->id,
                "cid" => $item->cid,
                "s_sku" => $item->s_sku,
                "s_name" => $item->s_name,
                "price_type" => 4,
                "s_price" => $p["price"],
                "count" => $p["count"],
                "discount" => $p["discount"],
                "discount_sum" => $p["discount_sum"],
                "nds" => $p["nds"],
                "type_nds" => $p["nds_type"],
                "total" => $p["total"],
                "s_unit" => $item->s_unit,
            ]);
            $total += $p["total"];
            $nds += $p["nds"];
        }
        $total = round($total, 2);
        $nds = round($nds, 2);
        $this->resale_sales->update(["total" => $total, "nds" => $nds]);
    }
    protected function create_realization()
    {
        $rd = Module::sales_to_realization($this->resale_sales->id, [
            "sales_detail" => $this->resale_sales_detail,
            "active" => 1,
        ]);
        $this->resale_realization = $rd["realization"];
        $this->resale_realization_detail = $rd["realization_detail"];
    }   
    
    protected function remove_reserve()
    {
        $this->reserve = new \Reserv();
        $this->reserve->change_uid($this->realization->sales->uid);
        foreach ($this->realization_detail as $value) {
            $this->reserve->end_reserv($value->sdid, 'sales', $this->resale_stock_id, $value->count);
        }
        

    }
    protected function create_supply_order()
    {
        $supply_order_add = [];
        $supply_order_add["org"] = $this->realization->org;
        $supply_order_add["oid"] = $this->resale_org_data["org"]["oid"];
        $supply_order_add["object"] = $this->realization->object;
        $supply_order_add["nds_includes"] = $this->org_data["org"]["nds_includes"];
        $date = date("Y-m-d");
        $supply_order_add["date_work"] = $date;
        $supply_order_add["date_ship"] = $date;
        $supply_order_add["date_end"] = $date;
        $this->supply_order = \Supply\Model\SupplyOrder::create($supply_order_add);

        $total = 0;
        $nds = 0;
        $this->supply_order_detail = [];
        foreach ($this->realization_detail as $item) {
            $data = \Price::get_all([
                "price" => $this->get_prepare_purchase_price($item->sdid, $item->s_price, $item->discount, $supply_order_add["nds_includes"], $this->org_data["org"]["nds"]),
                "count" => $item->count,
                "nds_includes" => $supply_order_add["nds_includes"],
                "nds_type" => $this->org_data["org"]["nds"],
            ]);

            $supply_order_detail_add = [];
            $supply_order_detail_add["supply_o"] = $this->supply_order->id;
            $supply_order_detail_add["cid"] = $item->cid;
            $supply_order_detail_add["s_sku"] = $item->s_sku;
            $supply_order_detail_add["s_name"] = $item->s_name;
            $supply_order_detail_add["count"] = $data["count"];
            $supply_order_detail_add["price_now"] = $data["price"];
            $supply_order_detail_add["total"] = $data["total"];
            $supply_order_detail_add["type_nds"] = $data["nds_type"];
            $supply_order_detail_add["nds"] = $data["nds"];
            $supply_order_detail_add["unit"] = $item->s_unit;
            // $supply_order_detail_add["sale_detail_id"] = $item->id;

            $total += $supply_order_detail_add["total"];
            $nds += $supply_order_detail_add["nds"];

            $this->supply_order_detail[] = \Supply\Model\SupplyOrderDetail::create($supply_order_detail_add);
        }
        $this->supply_order->update(["total" => $total, "nds" => $nds]);
    }
    protected function create_receipts()
    {
        $supply_to_receipts_request = [];
        $supply_to_receipts_request["doc_number"] = $this->resale_realization->numb;
        $supply_to_receipts_request["date_receipts"] = date("Y-m-d");
        $supply_to_receipts_request["org"] = $this->realization->org;
        $supply_to_receipts_request["stock_id"] = $this->realization->stock_id;
        $supply_to_receipts_request["supply_detail"] = $this->supply_order_detail;
        $supply_to_receipts_request["active"] = 1;
        $supply_to_receipts_request["check_attach"] = false;

        $data = Module::supply_to_receipts($this->supply_order->id, $supply_to_receipts_request);
        $this->receipts = $data["receipts"];
        $this->receipts_detail = $data["receipts_detail"];
    }
    protected function get_prepare_purchase_price($sales_detail_id, $price, $discount, $nds_includes, $nds_type)
    {
        $purchase_price = $this->purchase_price[$sales_detail_id];
        $data = \Price::get_all([
            "price" => $price,
            "count" => 1,
            "nds_includes" => $nds_includes,
            "nds_type" => $nds_type,
            "discount" => $discount,
        ]);
        return $this->auto_price($purchase_price, $data["discount_price"]);
    }
    protected function auto_price($purchase_price, $price)
    {
        // dd($this->org_data['org']['id']);
        switch ((int)$this->org_data['org']['id']) {
            default:
            case 2:
                return $purchase_price + $purchase_price * 0.04;
                break;
            case 1:
                return $purchase_price + $purchase_price * 0.05;
                break;
            case 5:
                return ($purchase_price + $price) / 2;
                break;
        }
        return 0;
    }
    protected function save_relation()
    {
        //сохраняем продажу
        $this->create_relation(\Sales\Models\Sales::class, $this->resale_sales->id);
        foreach ($this->resale_sales_detail as $item) {
            $this->create_relation(\Sales\Model\SalesDetail::class, $item->id);
        }
        //сохраняем реализацию
        $this->create_relation(\Realization\Model\Realization::class, $this->resale_realization->id);
        foreach ($this->resale_realization_detail as $item) {
            $this->create_relation(\Realization\Model\RealizationDetail::class, $item->id);
        }
        //сохраняем счёт поставщика
        $this->create_relation(\Supply\Model\SupplyOrder::class, $this->supply_order->id);
        foreach ($this->supply_order_detail as $item) {
            $this->create_relation(\Supply\Model\SupplyOrderDetail::class, $item->id);
        }
        //сохраняем поступление
        $this->create_relation(\Supply\Model\Receipts::class, $this->receipts->id);
        foreach ($this->receipts_detail as $item) {
            $this->create_relation(\Supply\Model\ReceiptsDetail::class, $item->id);
        }
    }
    protected function create_relation(string $model_type, int $pid)
    {
        return ResaleRelation::create(["uniqid" => $this->uniqid, "realization_id" => $this->realization->id, "model_type" => $model_type, "pid" => $pid]);
    }
}
