<?php

namespace Boot\System\Dashboard\Http\Components;

use Boot\System\Livewire\Component;
use Boot\System\Dashboard\Dashboard;

class UpdateModeComponent extends Component
{
    public function render()
    {
        /** @var \Boot\System\Dashboard\Dashboard $dashboard */
        $dashboard = app(Dashboard::class);

        $this->emit('updateMode', $dashboard->getMode());

        return view('dashboard::components.updateMode');
    }
}
