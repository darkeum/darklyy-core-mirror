<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (19.07.2023 7:14)
*/

namespace Controls\UI\WEB\Components\Pay\Detail;

use Boot\Support\Numb;
use Boot\System\Livewire\Component;
use Controls\Models\ControlDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as CollectionAlias;
use Illuminate\View\View;

class TablePay extends Component
{
    public int $orderId;
    protected $listeners = ['commentUpdate', 'editPrice'];

    function render(): View
    {
        return view('controls::manager.pay.detail.components.table', ['content' => $this->detailContent($this->orderId)]);
    }

    /**
     * @param int $id
     * @return Builder[]|CollectionAlias
     */
    private function detailContent(int $id): CollectionAlias|array
    {
        $content = ControlDetail::with('catalog')
            ->where('control_id', $id)
            ->where('status', '!=', 0)
            ->get();

        $content->each(function($item) {
            $getNdr = get_ndr(['cid' => $item->cid, 'stock_id' => '1', 'module' => 'sales', 'pid' => null, 'returnType' => 'array']);
            $item->ndr = $getNdr['n'] . ' / ' . $getNdr['d'] . ' / ' . $getNdr['r'];
            $item->price_old = $item->price_now;
            $item->price = Numb::formatMoney($item->price);
            $item->price_now = Numb::formatMoney($item->price_now);
            $item->unit = get_unit('data', $item->unit);
        });

        return $content;
    }

    /**
     * Обновление/ Добавление комментария
     *
     * @param array $params
     * @return bool
     */
     function commentUpdate(array $params): bool
     {
        if(empty($params['text'])) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'message' => trans('controls::lang.status.error'),
            ]);
            return false;
        }

        $controlDetail = ControlDetail::find($params['id']);
        $controlDetail->comment = $params['text'];
        $update = $controlDetail->save();

        if($update) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'success',
                'message' => trans('controls::lang.status.success'),
            ]);
        } else {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'message' => trans('controls::lang.status.error'),
            ]);
        }

        return (bool) $update;
    }

    /**
     * Обновление закупочной цены
     *
     */
    function editPrice(array $params): bool
    {
        if(empty($params['newPrice'])) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'message' => trans('controls::lang.status.error'),
            ]);
            return false;
        }

        $controlDetail = ControlDetail::find($params['id']);
        $controlDetail->price_now = $params['newPrice'];
        $update = $controlDetail->save();

        if($update) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'success',
                'message' => trans('controls::lang.status.success'),
            ]);
        } else {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'message' => trans('controls::lang.status.error'),
            ]);
        }
        return (bool) $update;
    }
}