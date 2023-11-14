<?php

namespace Profile\UI\WEB\Controllers;

use Boot\System\Http\Request;
use Profile\UI\WEB\Grids\RoleGrid;
use Boot\Controller\AbstractController;
use Boot\System\Permission\Models\Role;
use Boot\System\Permission\Models\Permission;

class RoleController extends AbstractController
{
    static function index()
    {
        $grid = new RoleGrid;
        display('@profile/roles/index', [
            'grid' => $grid->render(),
        ]);
    }

    static function create()
    {
        display('@profile/roles/createOrEdit', [
            "permission" => Permission::get(),
        ]);
    }

    static function store(Request $request)
    {
        $role = Role::create([
            "name" => $request->input('name'),
            "guard_name" => $request->input('guard_name'),
        ]);
        $role->syncPermissions($request->input('permission'));
        return redirect(router("admin.profile.role.edit", $role->id))->with('status', 'Роль создана!');
    }

    // Нет роута
    // static function show($id){}

    static function edit($id)
    {
        display('@profile/roles/createOrEdit', [
            "id" => $id,
            "role" => Role::findOrFail($id),
            "permission" => Permission::get(),
            "rolePermissions" => \DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
                ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
                ->all(),
        ]);
    }

    static function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update([
            "name" => $request->input('name'),
            "guard_name" => $request->input('guard_name'),
        ]);
        $role->syncPermissions($request->input('permission'));
        return redirect()->route("admin.profile.role.edit", $role->id)->with('status', 'Роль обновлена!');
    }

    static function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return redirect()->route('admin.profile.role.index')->with('status', 'Роль успешно удалена');
    }
}
