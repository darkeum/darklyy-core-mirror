<?php

namespace Requests\UI\WEB\Components;

use Boot\System\Livewire\Component;

class status extends Component
{
    public $status = null;
        
    protected $listeners = ['statusUpdate' => 'mount'];

    public function mount($status)
    {
        $this->status = $status;
    }
    
    public function render()
    {
        return view('requests::manager.components.status');
    }
    
}
