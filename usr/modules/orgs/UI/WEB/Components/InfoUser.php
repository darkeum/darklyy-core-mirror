<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Orgs\UI\WEB\Components;

use Boot\Models\Users;
use Boot\System\Livewire\Component;

class InfoUser extends Component
{
    public $unit,$user,$field;

    public $users;

    public function mount($unit,$field)
    {
        $this->unit = $unit;
        switch ($field) {
            case "accountant":
                $this->user = Users::find($unit->accountant);
                break;
            case "uid":
                $this->user = Users::find($unit->uid);
                break;
            case "director":
                $this->user = Users::find($unit->director);
                break;
        }                           
        $this->users = Users::all();            
        $this->field = $field;
        
    }

    public function render()
    {
        return view('orgs::manager.components.infouser');
    }

}
