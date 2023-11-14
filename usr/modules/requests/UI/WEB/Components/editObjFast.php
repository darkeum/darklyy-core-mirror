<?php

namespace Requests\UI\WEB\Components;

use Boot\System\Livewire\Component;

use Objects\Models\Objects;

class editObjFast extends Component
{
    public $obj = null;
    public $unit = null;
    public $objName = null;

    public $newObj;

    public function mount($unit)
    {
        $this->unit = $unit;
        $this->obj = $unit->object;
        if($this->obj != null){
            $this->objName = Objects::find($this->obj)->name;
        }
    }
    
    public function render()
    {
        return view('requests::manager.components.editObjFast');
    }

    public function UpdatedNewObj(){

        $this->unit->update(['object' => $this->newObj]);
        $this->obj = $this->newObj;
        if($this->obj != null){
            $this->objName = Objects::find($this->obj)->name;
        }
        $this->newObj = null;

        $this->emit('updateObj');
    }
    
}
