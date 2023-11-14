<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (15.03.2023 7:38)
*/
namespace Controls\UI\WEB\Controllers;

use Boot\Abstracts\Controllers\Controller;
use Boot\Models\Attach;
use Boot\Models\CommentsAdmin;
use Boot\Models\Users;
use Controls\Models\Control;
use Controls\Models\ControlPayment;
use Controls\Support\Manager;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Collection as CollectionAlias;
use Illuminate\Database\Eloquent\Model;
use Supply\Models\SupplyOrderDetail;
use Sales\Models\SalesDetail;

class ControlsPay extends Controller {

    function index():View
    {
        return view('controls::manager.index');
    }

    /**
     * Отображение делательной страницы
     *
     * @param int $id
     * @param Control $control
     * @return View
     */
    function show(int $id, Control $control): View
    {
        $order = $this->order($control, $id);
        return view('controls::manager.pay.detail.index', [
            'order' => $order,
            'shortUserName' => user_short_name(Users::query()->find($order->uid)),
            'status' => $this->status($order->status),
            'statusPay' => $this->statusPay($order->pay, $order->reason),
            'attach' => $this->getAttach($order->supply_o),
            'sales' => $this->linkedSales($order->supply_o),
            'commentSupply' => $this->commentSupply($order->supply_o),
            'attachFiles' => Attach::query()->where('module', 'pay')->where('pid', $order->id),
            'comments' => $this->comments($order->supply_o),
            'payDate' => ControlPayment::query()->where('control_id', $order->id)->get(),
        ]);
    }

    function destroy(int $id)
    {
        $control = Control::findOrFail($id);
        $control->delete();

        return redirect()->route('admin.controls_pay.index');
    }

    /**
     * @param Control $model
     * @param int $id
     * @return Builder|Builder[]|CollectionAlias|Model|null
     */
    private function order(Control $model, int $id)
    {
        return $model::with([
            'supply_order.organization'
        ])->findOrFail($id);
    }

    private function status($status): string
    {
        return match($status) {
            1 => '<span class="badge bg-warning rounded-pill">'. trans('controls::lang.status.waiting') .'</span>',
            2 => '<span class="badge bg-danger rounded-pill">'. trans('controls::lang.status.sent') .'</span>',
            3 => '<span class="badge bg-success rounded-pill">'. trans('controls::lang.status.agreed') .'</span>',
        };
    }

    private function statusPay($pay_status, $pay_reason): string
    {
        return match ($pay_status) {
            default => 'Статус -' . $pay_status . ' неизвестный',
            0 => '<span class="badge bg-warning rounded-pill">'. trans('controls::lang.statusPay.unpaid') .'</span>',
            1 => '<span class="badge bg-success rounded-pill">'. trans('controls::lang.statusPay.paid') .'</span>',
            2 => '<span class="badge bg-danger rounded-pill">'. trans('controls::lang.statusPay.cancel') .' '. Manager::payReasonList($pay_reason) . '</span>',
            3 => '<span class="badge bg-info rounded-pill">'. trans('controls::lang.statusPay.partial') .'</span>',
        };
    }

    /**
     * Документы по счету
     *
     * @param int $pid
     * @return array|Collection
     */
    private function getAttach(int $pid): array|Collection
    {
        return Attach::query()
            ->where('module', 'supply')
            ->where('pid', $pid)
            ->get();
    }

    /**
     * @param int $supply_o
     * @return Collection|\Illuminate\Support\Collection|array
     */
    private function linkedSales(int $supply_o): Collection|\Illuminate\Support\Collection|array
    {
        $orderDetail = SupplyOrderDetail::with('sales_detail.sales.objects')
            ->where('supply_o', $supply_o)
            ->distinct()
            ->get();

        if(empty($orderDetail->sales_detail)) return collect();

        return $orderDetail;
    }

    /**
     * @param int $supply_o
     * @return string
     */
    private function commentSupply(int $supply_o): string
    {
        $result = Control::query()
            ->select('comment')
            ->where('supply_o', $supply_o)
            ->get();

        $str = '';
        foreach ($result as $item) {
            if(!empty($item->comment)) {
                $str .= '<div>' . $item->comment . '</div>';
            }
        }

        return $str;
    }

    private function comments(int $supply_o)
    {
        return CommentsAdmin::with('user')
            ->select('date', 'text', 'uid')
            ->where('module', 'supply_order_control')
            ->where('module_id', $supply_o)
            ->get();
    }
}