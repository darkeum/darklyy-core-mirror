<?php

namespace Realization;

use \Boot\Exceptions\OptionsException;
use \DB;

class RealizationActive extends \Boot\Abstracts\Active\Active
{
    protected $realization;
    protected $reserve;
    protected $sync;
    protected $balance;
    protected $reestr;

    protected $module = "realization";

    public function __construct(int $realization_id)
    {
        $this->realization = \Realization\Model\Realization::with(["realization_detail.sales_detail", "realization_detail.catalog.catalog_types", "sales"])->where("id", $realization_id)->firstOrFail();
        $this->reserve = new \Reserv();
        $this->reserve->change_uid($this->realization->sales->uid);
        $this->sync = new \Sync\Sync;
        $this->balance = new \Economic\Balance;
        $this->balance->org = $this->realization->org;
        $this->balance->oid = $this->realization->oid;
        $this->balance->date = $this->realization->date;
        $this->balance->value = $this->realization->total;
        $this->balance->pid = $this->realization->id;
        $this->balance->module = $this->module;
        $this->reestr = \Reestr\Models\Reestr::searchByModule("sales", $this->realization->sales_id);
        $this->balance->reestr_id = $this->reestr->id ?? null;
    }
    public function get_main_module(){
        return $this->realization;
    }
    //minus
    public function active(): array
    {
        return DB::transaction(function(){
            if ($this->realization->active == 1) throw new OptionsException("Реализация уже проведена", options: ["code" => 6]);
            foreach ($this->realization->realization_detail as $item) {
                $raz = $item->sales_detail->count - ($item->sales_detail->count_realization + $item->count);
                if ($raz < 0) throw new OptionsException("Количество реализуемого больше требуемого", options: ["code" => 5]);
            }
            $this->active_detail();
            $this->realization->update(["active" => 1]);
            $this->sync();
            $this->balance->action = "plus";
            $this->balance->create();
            $this->active_sales_status();
            return [
                "code" => 1,
            ];
        });
    }
    //plus
    public function deactivate(): array
    {
        return DB::transaction(function(){
            if ($this->realization->active == 0) throw new OptionsException("Реализация уже распроведена", options: ["code" => 7]);
            $this->deactivate_detail();
            $this->realization->update(["active" => 0]);
            $this->sync();
            $this->balance->action = "minus";
            $this->balance->create();
            $this->deactivate_sales_status();
            return [
                "code" => 2,
            ];
        });
    }
    protected function sync()
    {
        $intrval = getIntervalKv(date('Y-m-d', time()));
        $date = strtotime($this->realization->date);
        if ((strtotime($intrval['sd']) <= $date) && (strtotime($intrval['ed']) >= $date)) {
            if ($this->realization->sales->sales_type == 3) {
                $this->sync->syncRegister('realization_cashbox', $this->realization->id, '1', $this->realization->org);
            } else {
                $this->sync->syncRegister('realization', $this->realization->id, '1', $this->realization->org);
            }
        }
    }
    protected function active_detail()
    {
        $comment = 'Проведение реализации #' . module_perfix('realization', $this->realization->id);
        if ($this->realization->realization_detail->count() > 0) {
            $supply_order_price_all = 0;
            foreach ($this->realization->realization_detail as $item) {
                if ($item->catalog->catalog_types->service == 0) {
                    insert_shipment_minus(['cid' => $item->cid, 'module' => $this->module, 'pid' => $this->realization->id, 'stock_id' => $this->realization->stock_id, 'count' => $item->count, 'comment' => $comment, 'insert' => "before"]);
                    $this->updateQuantity($item->cid, $item->count, 'minus',  $comment);
                }
                $gppds = \Sales\Model\SalesDetail::getPurchase($item->sdid);
                $supply_order_price = empty($gppds->price) ? 0 : $gppds->price;
                $supply_order_price_all += $supply_order_price * $item->count;
                $item->update(["supply_order_price" => $supply_order_price]);

                //удаление резервов
                $this->reserve->end_reserv($item->sdid, "sales", $this->realization->stock_id, $item->count);
                //обновление количества реализованного
                $count_realization = $item->sales_detail->count_realization;
                $count_realization += $item->count;
                $item->sales_detail->update(["count_realization" => $count_realization]);
            }
            $this->realization->update(["supply_order_price" => $supply_order_price_all]);
        }
    }
    protected function deactivate_detail()
    {
        $comment = 'Отмена проведения реализации #' . module_perfix('realization', $this->realization->id);
        if ($this->realization->realization_detail->count() > 0) {
            foreach ($this->realization->realization_detail as $item) {
                if ($item->catalog->catalog_types->service == 0) {
                    $this->updateQuantity($item->cid, $item->count, 'plus',  $comment);
                }
                $item->update(["supply_order_price" => null]);

                //обновление количества реализованного
                $count_realization = $item->sales_detail->count_realization;
                $count_realization -= $item->count;
                $item->sales_detail->update(["count_realization" => $count_realization]);
            }
            $this->realization->update(["supply_order_price" => null]);
        }
    }
    protected function updateQuantity($cid, $count, $sign, $comment)
    {
        updateQuantity($cid, $this->realization->stock_id, $count, $sign, [
            "module" => $this->module,
            "pid" => $this->realization->id,
            "comment" => $comment,
        ]);
    }
    protected function active_sales_status()
    {
        $this->sales_detail_status(function ($item, $status) {
            \Sales\Model\SalesDetailStatusLog::create([
                "sales_detail_id" => $item->sales_detail->id,
                "status" => $status,
                "url" => "/administration/module/realization/order/" . $this->realization->id,
            ]);
        });
        $this->realization->sales->re_status_client();
    }
    protected function deactivate_sales_status()
    {
        $s_status = 19;
        foreach ($this->realization->realization_detail as $item) {
            $item->sales_detail->update(["status" => $s_status, "status_client" => 6]);
            \Sales\Model\SalesDetailStatusLog::create([
                "sales_detail_id" => $item->sales_detail->id,
                "status" => $s_status,
                "url" => "/administration/module/realization/order/" . $this->realization->id,
            ]);
        }
        $this->sales_detail_status();
        $this->realization->sales->re_status_client();
    }
    protected function sales_detail_status($callback = null)
    {
        foreach ($this->realization->realization_detail as $item) {
            $count = \Realization\Model\RealizationDetail::whereHas("realization", function ($query) {
                $query->where("active", 1);
            })->where("sdid", $item->sdid)->sum("count");
            if ($count >= $item->sales_detail->count) {
                //если да -> установить статус 12
                //добавить в историю статус 12
                $s_status = 12;
                $item->sales_detail->update(["status" => $s_status]);

                if (is_callable($callback)) $callback($item, $s_status);
            } else if ($count != 0) {
                //иначе -> установить статус 13
                //добавить в историю статус 13
                $s_status = 13;
                $item->sales_detail->update(["status" => $s_status]);

                if (is_callable($callback)) $callback($item, $s_status);
            }
        }
    }
}
