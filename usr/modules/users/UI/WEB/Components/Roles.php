<?php

namespace Users\UI\WEB\Components;

use Boot\Support\Facades\Auth;
use Boot\System\Livewire\Component;
use Boot\System\Permission\Models\Role;
use Boot\Models\Users;
use Boot\System\Permission\Models\Permission;

class Roles extends Component
{
    public $roles;

    public function mount()
    {
        $this->roles = Role::all();
    }

    public function render()
    {
        return view('users::manager.components.roles');
    }

    public function refreshOrgs($event) {
        $this->emit('refreshOrgs', $event);
    }
}
