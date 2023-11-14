<?php

namespace Requests\UI\WEB\Components;

use Boot\Models\Attach;
use Boot\System\Livewire\Component;
use Illuminate\Support\Facades\Storage;

class filesTree extends Component
{
    public $files = null;

    protected $listeners = ['treeUpdate'];

    public function mount($unit)
    {   
        $this->files = $unit->attachRequestCatalog;
    }

    public function render()
    {
        return view('requests::manager.components.filestree');
    }

    public function treeUpdate($files)
    {   
        $this->files = collect($files);
        $this->dispatchBrowserEvent('treeUpdate');
    }
}
