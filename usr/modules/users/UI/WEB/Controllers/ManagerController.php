<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Users\UI\WEB\Controllers;

use Boot\System\Permission\Models\Role;
use Boot\Controller\AbstractController;
use Boot\Models\Users;
use Boot\System\Http\Request;
use Illuminate\Support\Facades\Hash;
use Organizations\Model\Organizations;

class ManagerController extends AbstractController
{
    static function index()
    {
        return view('users::manager.index');
    }

    static function show($id)
    {
        // $unit = Users::find($id);
        // return redirect()->to($unit->url);
    }

    static function create()
    {
        return view('users::manager.create', [
            'roles' => Role::all(),
            'actives' => trans('users::lang.admin.active'),
        ]);
    }

    static function store(Request $request)
    {
        request()->validate([
            //'published_at' => ['required'],
        ]);

        $roles = $request->input('roles');
        $request->merge([
            'password' => Hash::make($request->input('password')),
            // 'group' => ($roles) ? Role::findByName($roles)->id : 0,
        ]);

        $unit = Users::create($request->all());

        // Роли
        $unit->syncRoles($roles);

        return redirect()->route('admin.users.index');
    }

    static function edit($id)
    {
        // $unit = Users::withTrashed()->findOrFail($id);
        $unit = Users::find($id);

        return view('users::manager.create', [
            'unit' => $unit,
            'roles' => Role::all(),
            'actives' => trans('users::lang.admin.active'),
        ]);
    }

    static function update(Request $request, $id)
    {
        request()->validate([
            //'published_at' => ['required'],
        ]);

        $unit = Users::withTrashed()->with('roles')->find($id);

        $password = $request->input('password');
        $roles = $request->input('roles');
        $orgs = $request->input('orgs');

        // Роли
        $unit->syncRoles($roles);

        $request->merge([
            'password' => ($password) ? Hash::make($password) : $unit->password,
            // 'group' => ($roles) ? Role::findByName($roles)->id : 0,
        ]);

        if ($orgs) {
            $unit->orgs()->attach($orgs);
            // foreach ($orgs as $org) {
            //     Organizations::findOrFail($org)->users()->attach(\Auth::id());
            // }
        }

        $unit->update($request->all());

        return redirect()->route('admin.users.index');
    }

    static function destroy($id)
    {
        // $unit = Users::find($id);
        // $unit->syncRoles([]);
        // $unit->delete();

        return redirect()->route('admin.users.index');
    }
}
