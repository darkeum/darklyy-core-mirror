<?php

namespace Users\UI\WEB\Components;

use Boot\Support\Facades\Auth;
use Boot\System\Livewire\Component;
use Boot\System\Permission\Models\Role;
use Boot\Models\Users;
use Boot\System\Permission\Models\Permission;
use Organizations\Model\Organizations;
use Orgs\Models\Org;

class Orgs extends Component
{
    public $orgs;

    protected $listeners = ['refreshOrgs'];

    public function mount()
    {
    }

    public function render()
    {
        return view('users::manager.components.orgs');
    }

    public function refreshOrgs($event)
    {
        if (!$this->orgs) {
            // $role = Users::find(Auth::id())->hasRole(['worker', 'admin', 'director']); // Есть ли у текущего пользователя необходимая роль
            // if ($role) {
            if ($event == 'worker') {

                $this->orgs = Organizations::all();
            } else {
                $this->orgs = '';
            }
            $this->mount();

        }
    }
}
