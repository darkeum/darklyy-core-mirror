<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Home\UI\WEB\Components;

use Boot\Support\Carbon;
use Boot\Support\Facades\Auth;
use Boot\System\Livewire\Component;
use Sales\Models\Sales as ModelsSales;

class Sales extends Component
{
    public $sales;

    protected function getSales()
    {
        return ModelsSales::where('uid', Auth::id())->with('organization')->with('object_')->where('task_date_end', '<=', Carbon::now())->orderByDesc('task_date_end')->limit(6)->get();
    }

    public function reload()
    {
        $this->sales =  $this->getSales();
        $this->emit('tooltip');
    }

    public function mount()
    {

        $this->sales = $this->getSales();
    }

    public function render()
    {
        return view('home::manager.components.sales');
    }
}
