<?php

namespace Boot\System\Livewire;

use Illuminate\View\Engines\CompilerEngine;
use Boot\System\Livewire\ComponentConcerns\RendersLivewireComponents;

class LivewireViewCompilerEngine extends CompilerEngine
{
    use RendersLivewireComponents;
}
