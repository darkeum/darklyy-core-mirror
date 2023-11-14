<?php
namespace App\Components;

use Boot\System\Livewire\Component;
use Boot\System\Livewire\WithFileUploads;

class SingleUpload extends Component
{
    use WithFileUploads;

    public $photo;

    public function render()
    {
        return view('livewire.single-upload');
    }
}
