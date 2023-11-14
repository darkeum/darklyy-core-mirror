<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (10.07.2023 17:30)
*/

namespace Controls\UI\WEB\Components\ControlsDetail;

use Boot\System\Livewire\Component;
use Controls\Models\ControlDetail;
use Controls\UI\WEB\Controllers\Controls;
use Illuminate\Contracts\View\View;

class Table extends Component {

    public $control_detail;
    public int $id_show;
    protected $listeners = ['delete'];
    public $supply_order_total;
    public $sales_total;

    function render(): View {
        $total_diff = $this->sales_total - $this->supply_order_total;
        return view('controls::manager.main.show.components.table', [
            'total_diff' => $total_diff,
        ]);
    }

    /**
     * Удаление записи
     *
     * @param $id
     * @return void
     */
    function delete($id): void {
        $control_detail = ControlDetail::findOrFail($id);
        $this->getTotalPrice($id);
        $delete = $control_detail->delete();
        if($delete) {
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
    }

    private function getControl() {
        $data = Controls::getControlData($this->id_show);
        return $data['control']->control_detail;
    }

    private function getTotalPrice($id): void {
        $this->control_detail = $this->getControl();
        $detail = $this->control_detail->where('id', $id)->first();
        $this->supply_order_total -= $detail->supply_order_detail->total;
        $this->sales_total -= $detail->sales_detail->total;
    }
}