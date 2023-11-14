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

use Boot\System\Livewire\Component;
use Organizations\Model\Organizations;

class InfoOrg extends Component
{
    public $unit, $logo;

    public function mount($unit)
    {   
        $this->logo = $unit->logo;
        $this->unit = Organizations::find($unit->oid);  
    }

    public function render()
    {
        return view('orgs::manager.components.infoorg');
    }

}
