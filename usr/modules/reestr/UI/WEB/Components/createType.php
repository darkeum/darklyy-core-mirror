<?php

namespace Reestr\UI\WEB\Components;

use Boot\System\Livewire\Component;
use Reestr\Models\ReestrType;

class createType extends Component
{
    public $orgs,$actualOrg;
    public $ModalOrg,$ModalPrefix,$ModalType;

    public function mount($orgs)
    {
        $this->orgs = $orgs;
        $this->ModalOrg = $orgs->first()->id;
    }

    public function render()
    {
        return view('reestr::manager.components.createtype');
    }

    public function createType()
    {
        $array = [
            'name' => $this->ModalType,
            'short' => $this->ModalPrefix,
            'org' => $this->ModalOrg,
            'status' => 1
        ];
        ReestrType::create($array);
        $this->emit('createType');
        $this->emit('updateFields');

        $this->ModalType = null;
        $this->ModalPrefix = null;
    }

}
