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

use Boot\Support\Numb;
use Sales\Support\SalesSupport;
use Boot\System\Livewire\Component;

class Rating extends Component
{
    public $salesCompleteTotal;
    public $salesRating;

    public function reload()
    {
        $this->salesCompleteTotal =  SalesSupport::reloadSalesCompleteTotal();
    }

    public function mount()
    {
        $this->salesCompleteTotal =  Numb::formatMoney(SalesSupport::getSalesCompleteTotal(), '₽');
        $this->salesRating =  SalesSupport::getSalesRating();
    }

    public function render()
    {
        return view('home::manager.components.rating');
    }
}