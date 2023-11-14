<?php

namespace Supply;

use \Boot\Exceptions\OptionsException;
use \DB;

class ReceiptsActive extends \Boot\Abstracts\Active\Active
{
    protected $receipts;
    protected $balance;
    protected $reestr;
    protected $reserve;
    protected $sync;
    protected $check_attach;

    public function __construct(int $receipt_id, bool $check_attach = true)
    {
        $this->receipts = \Supply\Model\Receipts::with(["receipts_detail.supply_order_detail"])->where("id", $receipt_id)->firstOrFail();
        $this->check_attach = $check_attach;
        $this->sync = new \Sync\Sync;
        $this->reserve = new \Reserv();
        $this->balance = new \Economic\Balance;
        $this->balance->org = $this->receipts->org;
        $this->balance->oid = $this->receipts->oid;
        $this->balance->date = $this->receipts->date_receipts;
        $this->balance->value = $this->receipts->total;
        $this->balance->pid = $this->receipts->id;
        $this->balance->module = "receipt";
        $this->reestr = \Reestr\Models\Reestr::searchByModule("supply_order", $this->receipts->supply_id);
        $this->balance->reestr_id = $this->reestr->id ?? null;
    }
    public function get_main_module()
    {
        return $this->receipts;
    }
    //plus
    public function active()
    {
        return DB::transaction(function(){
            if ($this->receipts->active == 1) throw new OptionsException("Поступление уже проведено", options: ["code" => 6]);
            foreach ($this->receipts->receipts_detail as $item) {
                $raz = $item->supply_order_detail->count - ($item->supply_order_detail->count_receipts + $item->count);
                if ($raz < 0) throw new OptionsException("Количество реализуемого больше требуемого", options: ["code" => 5]);
            }

            if ($this->check_attach && \Boot\Models\Attach::where("pid", $this->receipts->id)->where("module", "receipts")->doesntExist()) {
                throw new OptionsException("Не приложен скан документа поступления", options: ["code" => 4]);
            }

            $this->logistics_receipts();
            \Logistics\Support\LogisticsDetailSupport::logistics_car_delivery_mark();

            $this->active_detail();
            $this->receipts->update(["active" => 1]);
            $this->sync();

            $this->balance->action = "minus";
            $this->balance->create();

            $this->active_sales_status();

            return [
                "code" => 1,
            ];
        });
    }
    //minus
    public function deactivate()
    {
        return DB::transaction(function(){
            if ($this->receipts->active == 0) throw new OptionsException("Поступление уже распроведено", options: ["code" => 7]);
            $this->deactivate_detail();
            $this->receipts->update(["active" => 0]);
            $this->sync();

            $this->balance->action = "plus";
            $this->balance->create();

            $this->deactivate_sales_status();

            return [
                "code" => 2,
            ];
        });
    }
    protected function active_detail()
    {
        foreach ($this->receipts->receipts_detail as $item) {
            $this->updateQuantity($item->cid, $item->count, "plus", 'Проведение поступления #' . module_perfix('receipts', $this->receipts->id));
            if (empty($item->sales_detail_id)) {
                $count_save = $item->count;
                $reserve_items = \DB::table("receipts_detail as rd")->leftJoin("stock_reserv as sr", "rd.cid", "=", "sr.cid")->leftJoin("supply_detail as sd", "sd.sdid", "=", "sr.pid")->leftJoin("supply as s", "s.id", "=", "sd.sid")->where("rd.id", $item->id)->where("sr.stock_id", $this->receipts->stock_id)->where("sr.module", "sales")->where("sr.status", 2)->where("sd.status", 7)->where("s.type", 1)->select("sr.*")->get();
                foreach ($reserve_items as $r_item) {
                    if ($count_save >= 0) {
                        $new_count = $count_save - $r_item->count >= 0 ? $r_item->count : $count_save;
                        $count_save -= $r_item->count;
                        $this->reserve->change_uid($r_item->uid);
                        $this->reserve->confirm_reserv($r_item->pid, "sales", $this->receipts->stock_id, $new_count, $item->cid);
                        \Boot\Models\StockReservSave::create([
                            "receipts_detail_id" => $item->id,
                            "pid" => $r_item->pid,
                            "module" => $r_item->module,
                            "count" => $new_count,
                            "uid" => $r_item->uid,
                            "stock_id" => $r_item->stock_id,
                        ]);
                    } else {
                        break;
                    }
                }
            } else {
                $this->reserve($item, function($item){
                    $this->reserve->confirm_reserv($item->sales_detail_id, "sales", $this->receipts->stock_id, $item->count, $item->cid);
                });
            }

            //count_receipt количество отправленного
            $count_receipts = $item->supply_order_detail->count_receipts;
            $count_receipts += $item->count;
            $item->supply_order_detail->update(["count_receipts" => $count_receipts]);
        }
        $this->check_supply_order();
    }
    protected function deactivate_detail()
    {
        foreach ($this->receipts->receipts_detail as $item) {
            $this->updateQuantity($item->cid, $item->count, "minus", 'Отмена проведения поступления #' . module_perfix('receipts', $this->receipts->id));
            if (empty($item->sales_detail_id)) {
                $reserve_items = \Boot\Models\StockReservSave::where("receipts_detail_id", $item->id)->get();
				foreach($reserve_items as $r_item){
					$this->reserve->change_uid($r_item->uid);
					$this->reserve->del_reserv($r_item->pid, $r_item->module, stock_id:$r_item->stock_id);
					$this->reserve->set_reserv($r_item->pid, $r_item->module, $r_item->stock_id);
					$r_item->delete();
				}
            } else {
                $this->reserve($item, function($item){
                    $this->reserve->del_reserv($item->sales_detail_id, "sales", stock_id:$this->receipts->stock_id);
                    $this->reserve->set_reserv($item->sales_detail_id, "sales", $this->receipts->stock_id);
                });
            }
            //count_receipt количество отправленного
            $count_receipts = $item->supply_order_detail->count_receipts;
            $count_receipts -= $item->count;
            $item->supply_order_detail->update(["count_receipts" => $count_receipts]);
        }
        $this->check_supply_order();
    }
    protected function reserve($item, $callback){
        if ($this->reserve->check_reserv($item->sales_detail_id, "sales", $this->receipts->stock_id)) {
            $qq = \Boot\Models\StockReserv::where("module", "sales")->where("pid", $item->sales_detail_id)->where("stock_id", $this->receipts->stock_id)->first();
            $this->reserve->change_uid($qq->uid);

            if(is_callable($callback)){
                $callback($item);
            }
        }
    }
    protected function check_supply_order()
    {
        //проверка на приход для supply_order
        $count = \Supply\Model\SupplyOrderDetail::where("supply_o", $this->receipts->supply_id)->sum("count");
        $count_receipts = \Supply\Model\SupplyOrderDetail::where("supply_o", $this->receipts->supply_id)->sum("count_receipts");
        $admission = 3;
        $close = 1;
        if ($count != $count_receipts) {
            $close = 0;
            if ($count_receipts == 0) {
                $admission = 1;
            } else {
                $admission = 2;
            }
        }
        \Supply\Model\SupplyOrder::findOrFail($this->receipts->supply_id)->update(["admission" => $admission, "close" => $close]);
    }
    protected function updateQuantity($cid, $count, $sign, $comment)
    {
        updateQuantity($cid, $this->receipts->stock_id, $count, $sign, [
            "module" => "receipts",
            "pid" => $this->receipts->id,
            "comment" => $comment,
        ]);
    }
    protected function sync()
    {
        if ($this->receipts->syncRegister == 1) {
            $this->sync->syncRegister('receipts', $this->receipts->id, '1', $this->receipts->org);
        }
    }
    protected function logistics_receipts()
    {
        $items = \DB::select("SELECT rd.*, l.count as l_count, l.ldd_id, l.ld_id
        FROM `" . DB_PREFIX . "_receipts_detail` as rd
        LEFT JOIN (
            SELECT ld.sod_id, ldd.count, ldd.id as ldd_id, ld.id as ld_id
            FROM `" . DB_PREFIX . "_logistics_driver_detail` as ldd
            LEFT JOIN `" . DB_PREFIX . "_logistics_detail` as ld ON(ld.id = ldd.logistics_detail_id)
            WHERE ldd.status = '2' AND ldd.receipts IS NULL
        ) as l ON(l.sod_id = rd.supply_detail_id)
        WHERE rd.rid = '{$this->receipts->id}' AND l.ldd_id IS NOT NULL");
        $flag = false;
        foreach ($items as $item) {
            if ($item->l_count > $item->count) {
                $count = $item->l_count - $item->count;
                \Logistics\Model\LogisticsDetail::findOrFail($item->ld_id)->update(["count" => $count, "status" => "6"]);
                $flag = true;
            }
            \Logistics\Model\LogisticsDriverDetail::findOrFail($item->ldd_id)->update(["receipts" => \Carbon\Carbon::now()]);
        }
        if ($flag) {
            \Logistics\Support\LogisticsDetailSupport::notif_return_items();
        }
    }
    protected function active_sales_status(){
        $link = [];
        $sales = [];
        foreach($this->receipts->receipts_detail as $item){
            if(!empty($item->sales_detail)){
                $link[] = "supply_o_tov[]={$item->sales_detail->id}";
                if(!array_key_exists($item->sales_detail->sales_id, $sales)){
                    $sales[ $item->sales_detail->sales_id ] = $item->sales_detail->sales;
                }

                $receipts_count = \Supply\Model\ReceiptsDetail::whereHas("receipts", function($query){
                    $query->where("active", 1);
                })->where("sales_detail_id", $item->sales_detail->id)->sum("count");
                if($receipts_count >= $item->sales_detail->count){
                    $s_status = 7;

                    $item->sales_detail->update([
                        "status" => $s_status,
						"status_client" => 6,
                    ]);
					\Sales\Model\SalesDetailStatusLog::create([
                        "sales_detail_id" => $item->sales_detail->id,
                        "status" => $s_status,
                        "url" => "/administration/module/supply/receipts_detail/".$this->receipts->id,
                    ]);
                } else {
                    $s_status = 11;

					$item->sales_detail->update(["status" => $s_status]);
					\Sales\Model\SalesDetailStatusLog::create([
                        "sales_detail_id" => $item->sales_detail->id,
                        "status" => $s_status,
                        "url" => "/administration/module/supply/receipts_detail/".$this->receipts->id,
                    ]);
                }
            }
        }
        $link = implode("&", $link);
        foreach($sales as $item){
            $attr = [];
			$attr['uid'] = $item->uid;
			$attr['title'] = 'Товары пришли';
			$attr['text'] = 'Товары по заказу №'.$item->numb.' пришли';
			$attr['icon'] = config('app.url').'/files/push/task.png';
			$attr['url'] = "https://atri-energo.ru/administration/module/sales/order/".$item->id."?".$link;
			$attr['module'] = "sales";
			$attr['pid'] = $item->id;
			sendAllNotif($attr);
        }
    }
    protected function deactivate_sales_status(){
        foreach($this->receipts->receipts_detail as $item){
            if(!empty($item->sales_detail)){
                $s_status = 18;
			    //изменить статус продажи на 6

			    $item->sales_detail->update(["status" => $s_status]);
			    \Sales\Model\SalesDetailStatusLog::create([
                    "sales_detail_id" => $item->sales_detail->id,
                    "status" => $s_status,
                    "url" => "/administration/module/supply/receipts_detail/".$this->receipts->id,
                ]);
            }
        }
    }
}
