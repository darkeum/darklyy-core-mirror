<?php

namespace Roles\UI\WEB\Components;

use Boot\System\Livewire\Component;
use Boot\System\Permission\Models\Role;
use Boot\Models\Users;
use Boot\System\Permission\Models\Permission;

class Roles extends Component
{

    public $roles;
    public $type = array();
    public $edit_id;
    public $type_list;
    public $array_total_users = array();

    public $show = false;
    public $text_id = null;
    public $text_name = null;

    public function mount()
    {

        $users = Users::all();
        $this->type_list = Permission::all();
        $this->roles = Role::all();
        foreach ($this->roles as $role) {
            $id = $role->id;
            $count = 0;
            foreach ($users as $user) {
                if ($id == $user->group)
                    $count += 1;
            }

            $this->array_total_users[$role->name] = $count;
        }
    }

    public function add()
    {
        $this->show = false;
        Role::create(['name' => $this->text_name, 'guard_name' => "web"]);
        $this->text_id = null;
        $this->text_name = null;
        $this->radio_type = null;

        $role = Role::latest()->first();
        $role->givePermissionTo($this->type);

        $this->type = ['1'];
        $this->mount();
    }

    public function update($id)
    {
        $this->show = false;
        Role::where('id', $id)->update(['name' => $this->text_name]);
        $role = Role::find($id);
        $actual_permisions = $role->permissions;

        $role->revokePermissionTo($actual_permisions);
        $role->givePermissionTo($this->type);

        $this->close();
        $this->mount();
    }


    public function edit($id, $name, $type)
    {

        if ($id == null) {
            $this->text_id = null;
            $this->text_name = null;
            $this->type = ['1'];
        } else {

            $this->text_id = $id;
            $this->text_name = $name;
            $role = Role::find($id);
            $permissions = $role->permissions;

            foreach ($permissions as $permission) {
                $this->type[] = $permission->id;
            }
        }
        $this->mount();
        $this->show = true;


    }

    public function close()
    {

        $this->text_id = null;
        $this->text_name = null;
        $this->type = null;
        $this->show = false;
    }

    public function render()
    {
        return view('roles::manager.components.roles');
    }
}
