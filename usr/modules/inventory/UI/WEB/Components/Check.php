<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Inventory\UI\WEB\Components;

use Boot\Models\Users;
use Boot\Models\Categories;
use Boot\Support\Carbon;
use Boot\Support\Facades\Auth;
use Inventory\Models\Inventory;
use Boot\System\Livewire\Component;
use Inventory\Models\InventoryCheck;

class Check extends Component
{
    public $idu, $unitCheck, $check, $uid, $cat = null;

    public function render()
    {
        return view('inventory::manager.components.check');
    }

    public function UpdatedIdu()
    {
        $this->unitCheck = $this->uid = $this->check = $this->cat = null;

        $this->unitCheck = Inventory::find($this->idu);

        $this->uid = Users::find($this->unitCheck->wid);

        $this->check = InventoryCheck::all()->where('iid', '=', $this->idu);

        $this->cat = Categories::find($this->unitCheck->category);
    }

    public function checkObj()
    {
        $this->check = InventoryCheck::create([
            'iid' => $this->idu,
            'uid' => Auth::id(),
            'date' => Carbon::now(),
            'status' => 1
        ]); 
    }
}
