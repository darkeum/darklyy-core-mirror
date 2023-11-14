<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (19.07.2023 7:58)
*/

namespace Controls\UI\WEB\Components\Pay\Detail;

use Boot\System\Livewire\Component;
use Boot\System\Livewire\WithFileUploads;
use Controls\Models\Control;
use Controls\Models\ControlDetail;
use Illuminate\View\View;
use Sales\Models\SalesDetail;
use Sales\Models\SalesDetailStatusLog;
use Supply\Models\SupplyOrder;

class ButtonsPay extends Component
{
    use WithFileUploads;
    public int $status;
    public int $pay;
    public int $order_id;
    public $file;

    protected $listeners = ['statusUpdate', 'addDocument', 'cancelPay', 'pay'];

    function render(): View
    {
        return view('controls::manager.pay.detail.components.buttons');
    }

    /**
     * @param $params
     * @return void
     */
    function statusUpdate($params): void
    {
        $content = Control::end($params['id'], $params['status'], $params['comment']);
        if ($content) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'success',
                'message' => trans('controls::lang.status.success'),
            ]);

            $this->status = $params['status'];
            $this->emitTo('controls::head', 'refreshComponent');
        } else {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'message' => trans('controls::lang.status.error'),
            ]);
        }
    }

    /**
     * Добавление документа
     *
     * @param $id
     * @return void
     */
    function addDocument($id): void
    {
        $attribute = [
            'name' => 1,
            'file' => $this->file,
            'module' => 'controls',
            'mod' => 'pay',
            'id' => $id,
            'comm_id' => $id,
        ];

        uploaderMaster('download', $attribute);
        $this->emitTo('controls::head', 'refreshComponent');
        $this->dispatchBrowserEvent('swal:modal', [
            'type' => 'success',
            'message' => trans('controls::lang.status.success'),
        ]);
    }

    /**
     * Отмена оплаты
     *
     * @param $params
     * @return void
     */
    function cancelPay($params): void
    {
        if(empty($params['data'])) \response(trans('controls::lang.status.error'), 404);

        if(empty($params['id'])) \response(trans('controls::lang.status.error'), 404);

        $update = [];
        foreach ($params['data'] as $key => $item) {
            $update[$key] = $item;
        }

        $control = Control::find($params['id']);
        $result = $control->update($update);

        if($result) {
            $this->pay = $params['data']['pay'];
            $this->emitTo('controls::head', 'refreshComponent');
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'success',
                'message' => trans('controls::lang.status.success'),
            ]);
        }else {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'message' => trans('controls::lang.status.error'),
            ]);
        }
    }

    /**
     * Оплата
     *
     * @param $params
     * @return void
     */
    function pay($params): void
    {
        $data = json_decode($params);
        $control = Control::query()->findOrFail($this->order_id);
        $s_data = SupplyOrder::query()->findOrFail($control->supply_o);

        $update = [];
        $update['pay_num'] = $data->pay_number;
        $update['pay_date'] = str($data->pay_date)->replace('T', ' ')->value();
        $update['pay_uid'] = auth()->id();
        $update['pay'] = 1;

        $pay_total = empty($data->pay_sum) ? false : $data->pay_sum;
        if($pay_total) {
            $update['pay'] = 3;
            $pay_total_all = $control->pay_total + $pay_total;
            $update['pay_total'] = $pay_total_all;
            if($pay_total_all >= $s_data->total) {
                $update['pay'] = 1;
            }
        }

        $control->update($update);
        $supplyOrder = SupplyOrder::query()->findOrFail($this->order_id);
        $supplyOrder->update(['pay' => 1]);

        $controlDetail = ControlDetail::query()
            ->where(
                [
                    ['control_id', '=', $this->order_id],
                    ['sdid', '<>', 0]
                ]
            )
            ->whereNotNull('sdid')
            ->get();

        if($controlDetail->count() > 0) {
            $s_status = 20;

            foreach ($controlDetail as $item) {
                SalesDetail::find($item->sdid)->update(['status' => $s_status]);

                SalesDetailStatusLog::create([
                    'sales_detail_id' => $item->sdid,
                    'status' => $s_status,
                    'url' => router('admin.controls.pay.show', $this->order_id)
                ]);
            }
        }

        $attribute = [
            'name' => '',
            'file' => $this->file ?? false,
            'module' => 'controls',
            'mod' => 'pay',
            'id' => $this->order_id,
            'comm_id' => $this->order_id,
        ];

        uploaderMaster('download', $attribute);

        $attribute = [
            'uid' => $s_data->uid,
            'title' => 'Счет поставщика оплачен',
            'text' => 'Счет поставщика оплачен',
            'icon' => config('app.url') . '/files/push/new_order.php',
            'url' => url(ADMIN. '/module/supply/order_detail', $control->supply_o),
            'pNotif' => true,
            'pPush' => true,
            'module' => 'supply_order',
            'pid' => $control->supply_o,
        ];

        sendAllNotif($attribute);
        $this->pay = $update['pay'];
        $this->emitTo('controls::head', 'refreshComponent');
        $this->dispatchBrowserEvent('swal:modal', [
            'type' => 'success',
            'message' => 'Успешно!',
        ]);
    }
}