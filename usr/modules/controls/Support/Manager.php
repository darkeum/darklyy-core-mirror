<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (18.03.2023 11:20)
*/

namespace Controls\Support;

use Boot\Models\Attach;
use Boot\Models\CommentsAdmin;
use Controls\Models\Control;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Sales\Models\SalesDetailStatusLog;
use Sales\Support\SalesSupport;
use Supply\Models\SupplyOrder;

class Manager
{

    /**
     * @param int $pay_status
     * @param $pay_reason
     * @return string
     */
    static function payStatusView(int $pay_status, $pay_reason = ''): string
    {
        return match ($pay_status) {
            default => 'Статус -' . $pay_status . ' неизвестный',
            0 => 'Не оплачен',
            1 => 'Оплачен',
            2 => 'Отмена оплаты по причине ' . self::payReasonList($pay_reason),
            3 => 'Частичная оплата',
        };
    }

    /**
     * @param string $id
     * @return mixed|string|string[]
     */
    static function payReasonList(string $id = "all"): mixed
    {
        $data = [
            'Оплачено по акту сверки',
            'Дублируется',
            'Не актуально',
            'Другое',
        ];

        if (empty($id) && $id != 0) {
            return $id;
        }

        return $id == "all" ? $data : $data[$id];
    }

    /**
     * Документы по счету
     *
     * @param $control
     * @return mixed
     */
    static function getAttach($control)
    {
        return Attach::where('module', 'supply')
            ->where('pid', $control->supply_o)
            ->get();
    }

    static function linkedSales($order)
    {
        return DB::table('supply_order_detail', 'sod')
            ->select(['sl.id', 'sl.numb', 'sl.date', 'sl.object', 'o.name'])
            ->leftJoin('sales_detail as s', 'sod.sale_detail_id', '=', 's.id')
            ->leftJoin('sales as sl', 's.sales_id', '=', 'sl.id')
            ->leftJoin('object as o', 'o.id', '=', 'sl.object')
            ->where('sod.supply_o', $order->supply_o)
            ->distinct()
            ->get();
    }

    /**
     * @param int $id
     * @return Model|Builder|object|null
     */
    static function order(int $id)
    {
        return DB::table('control', 'c')
            ->select(['c.*', 'o.name as oname'])
            ->leftJoin('supply_order as so', 'so.id', '=', 'c.supply_o')
            ->leftJoin('organizations as o', 'so.oid', '=', 'o.id')
            ->where('c.id', $id)
            ->first();
    }

    /**
     * Отрисовка контента в шапке на детальной странице оплаты
     *
     * @param int $id
     * @return Factory|View|Application|null
     */
    static function viewContentHead(int $id): Factory|View|Application|null
    {
        $order = self::order($id);
        $status = [
            1 => 'Ожидает проверки',
            2 => 'Отправлен на доработку',
            3 => 'Согласован',
        ];

        return view('controls::manager.pay_detail.content_head', [
            'order' => $order,
            'status' => $status,
            'linkedSales' => self::linkedSales($order),
            'statusPay' => self::payStatusView($order->pay, $order->reason),
            'attach' => self::getAttach($order),
            'commentSupply' => self::getCommentSupply($order),
            'attachFiles' => self::getAttachFiles($id),
            'id' => $id,
            'comments' => self::comments($order->supply_o),
            'payDate' => self::datePay($id),
        ]);
    }

    /**
     * @param $order
     * @return string
     */
    static function getCommentSupply($order): string
    {
        $result =  DB::table('control')
            ->select('comment')
            ->where('supply_o', $order->supply_o)
            ->get();

        $str = '';
        foreach ($result as $item) {
            if (!empty($item->comment)) {
                $str .= '<div>' . $item->comment . '</div>';
            }
        }

        return $str;
    }

    /**
     * @param int $id
     * @return Collection
     */
    static function getAttachFiles(int $id): Collection
    {
        return DB::table('attach')
            ->where('module', 'pay')
            ->where('pid', $id)
            ->get();
    }

    static function comments($supply_o)
    {
        return CommentsAdmin::select('comments_admin.date', 'comments_admin.text', 'users.name', 'users.surname', 'users.ochestvo')
            ->join('users', 'comments_admin.uid', '=', 'users.id')
            ->where('module', 'supply_order_control')
            ->where('module_id', $supply_o)
            ->get();
    }

    /**
     * @param $id
     * @return Collection
     */
    static function datePay($id): Collection
    {
        return DB::table('control_payment')
            ->where('control_id', $id)
            ->get();
    }

    /**
     * Отмена
     *
     * @param $id
     * @param $status
     * @param $comment
     * @return array
     */
    static function end($id, $status, $comment): array
    {
        $control = Control::findOrFail($id);
        $supply_order = SupplyOrder::findOrFail($control->supply_o);
        switch($status) {
            case 2:
                SalesSupport::send_comment_supply_order($control->supply_o, "control", $id, $comment);

                $control->update(["status" => $status]);

                $attr = [
                    'uid' => $supply_order->uid,
                    'title' => 'Заявка на закупку №'.$supply_order->numb.' отправлена на доработку',
                    'text' => 'Заявка на закупку №'.$supply_order->numb.' отправлена на доработку',
                    'icon' => config('app.url').'/files/push/task.png',
                    'url' => '/administration/module/supply/order_detail/'.$supply_order->id,
                    'module' => 'supply_order',
                    'pid' => $id
                ];
                sendAllNotif($attr);
                break;
            case 3:
                $control->load(["control_detail.sales_detail.sales"]);
                $set = [];
                $set["status"] = $status;
                if($control->plan_pay_date < date("Y-m-d")){
                    $set["plan_pay_date"] = date("Y-m-d");
                }
                $control->update($set);

                $attr = [
                    'uid' => 28,
                    'title' => 'Новая заявка на оплату',
                    'text' => 'Заявка на оплату #'.module_perfix('fcontrol', $id),
                    'icon' => config('app.url').'/files/push/task.png',
                    'url' => '/administration/module/fcontrol/pay_detail/'.$id,
                    'module' => 'fcontrol_pay',
                    'pid' => $id,
                ];
                sendAllNotif($attr);

                $attr = [
                    'uid' => $supply_order->uid,
                    'title' => 'Заказ согласован',
                    'text' => 'Заказ №'.$supply_order->numb.' согласован',
                    'icon' => config('app.url').'/files/push/task.png',
                    'url' => '/administration/module/supply/order_detail/'.$control->supply_o,
                    'module' => 'supply_order',
                    'pid' => $control->supply_o,
                ];
                sendAllNotif($attr);

                $sales = [];
                $s_status = 6;
                foreach($control->control_detail as $item){
                    if(!empty($item->sales_detail)){
                        $item->sales_detail->update(["status" => $s_status]);

                        SalesDetailStatusLog::create([
                            "sales_detail_id" => $item->sales_detail->id,
                            "status" => $s_status,
                            "url" => "/administration/module/supply/order_detail/".$control->supply_o,
                        ]);
                        $sales[$item?->sales_detail?->sales?->id] = $item?->sales_detail?->sales;
                    }
                }
                foreach($sales as $item) {
                    $attr = [
                        'uid' => $item->uid,
                        'title' => 'Заказ согласован',
                        'text' => 'Заказ №'.$item->numb.' согласован',
                        'icon' => config('app.url').'/files/push/task.png',
                        'url' => '/administration/module/sales/order/'.$item->id,
                        'module' => 'sales',
                        'pid' => $item->id,
                    ];
                    sendAllNotif($attr);
                }
                break;
        }
        $supply_order->update(["control" => $status]);

        return [
            "control" => $control,
            "supply_order" => $supply_order,
        ];
    }

}
