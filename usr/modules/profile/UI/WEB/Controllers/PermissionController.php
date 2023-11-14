<?php

namespace Profile\UI\WEB\Controllers;

use Boot\System\Http\Request;
use Profile\UI\WEB\Grids\PermissionGrid;
use Boot\Controller\AbstractController;
use Boot\System\Permission\Models\Role;
use Boot\System\Permission\Models\Permission;

class PermissionController extends AbstractController
{
    static function index()
    {
        $grid = new PermissionGrid;
        display('@profile/roles/index', [
            'grid' => $grid->render(),
        ]);
    }

    static function create()
    {
        display('@profile/permissions/createOrEdit', [

        ]);
    }

    static function store(Request $request)
    {
        $permission = Permission::create([
            "name" => $request->input('name'),
            "guard_name" => $request->input('guard_name'),
        ]);
        return redirect(router("admin.profile.permission.edit", $permission->id))->with('status', 'Разрешение создано!');
    }

    // static function show($id){

    // }

    static function edit($id)
    {
        display('@profile/permissions/createOrEdit', [
            "id" => $id,
            "permission" => Permission::findOrFail($id),
        ]);
    }

    static function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->update([
            "name" => $request->input('name'),
            "guard_name" => $request->input('guard_name'),
        ]);
        return redirect()->route("admin.profile.permission.edit", $permission->id)->with('status', 'Разрешение обновлено!');
    }

    static function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return redirect()->route('admin.profile.permission.index')->with('status', 'Разрешение успешно удалено');
    }
}
