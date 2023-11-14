<?php

namespace Reestr\UI\WEB\Components;

use Boot\System\Livewire\Component;
use Reestr\Models\ReestrType;

class createOrg extends Component
{

    public $newOrg;

    public function mount()
    {
    }

    public function render()
    {
        return view('reestr::manager.components.createorg');
    }

    public function createOrg()
    {
    }
}
