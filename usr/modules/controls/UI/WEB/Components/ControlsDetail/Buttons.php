<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (11.07.2023 12:22)
*/

namespace Controls\UI\WEB\Components\ControlsDetail;

use Boot\System\Livewire\Component;
use Controls\Models\Control;
use Controls\Support\Manager;
use Illuminate\Contracts\View\View;

class Buttons extends Component {

    public int $status;
    public int $detail_id;
    protected $listeners = ['end', 'update'];

    function render(): View {
        return view('controls::manager.main.show.components.buttons');
    }

    function end($params): void
    {
        $content = Manager::end($this->detail_id, $params['status'], $params['comment']);
        if ($content) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'success',
                'message' => trans('controls::lang.status.success'),
            ]);

            $this->status = $params['status'];
        } else {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'message' => trans('controls::lang.status.error'),
            ]);
        }
    }

    function update($params) {
        $control = Control::findOrFail($this->detail_id);
        $update = $control->update($params);

        if($update) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'success',
                'message' => trans('controls::lang.status.success'),
            ]);

            $this->status = $params['status'];
        } else {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'message' => trans('controls::lang.status.error'),
            ]);
        }
    }
}