<?php

namespace Reestr\UI\WEB\Components;

use Boot\Models\Attach;
use Boot\System\Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Organizations\Model\Organizations;
use Orgs\Models\Org;
use Reestr\Models\Reestr;
use Reestr\Models\ReestrType;

class form extends Component
{
    public $formOrg;
    public $orgs,$type,$type_doc,$contragent,$unit,$unitContragent;

    protected $listeners = ['updateFields' => 'UpdatedFormOrg'];

    public function mount($orgs, $unit = null)
    {
        $this->type_doc = $unit != null ? ReestrType::where('org','=',$unit->org)->get() : ReestrType::where('org','=',1)->get();
        $this->type = Reestr::getTypeDoc();
        $this->orgs = $orgs;
        $this->formOrg = $unit != null ? $unit->org : 1; // 1 - id атри-энерго
        $this->unitContragent  = $unit != null ? Organizations::find($unit->oid) : null;
    }

    public function render()
    {
        return view('reestr::manager.components.form');
    }

    public function UpdatedFormOrg() {
        $this->type_doc = ReestrType::where('org','=',$this->formOrg)->get();
        $this->emit('reloadFields');
    }

}
