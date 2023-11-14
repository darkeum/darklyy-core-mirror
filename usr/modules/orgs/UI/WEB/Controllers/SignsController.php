<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Orgs\UI\WEB\Controllers;

use Orgs\Models\Org;
use Boot\Models\Users;
use Boot\Support\Carbon;
use Boot\Models\UsersSign;
use Boot\System\Http\Request;
use Organizations\Model\Organizations;
use Boot\Abstracts\Controllers\WebController;

class SignsController extends WebController
{
    static public function index()
    {
        return view('orgs::manager.sign.index');
    }

    static function create()
    {
        return view('orgs::manager.sign.form', [
            "orgs" => Org::with('organization')->get(),
            'users_list' => Users::all(),
            "type_list" => UsersSign::getType(),
            "base_list" => UsersSign::getBase(),
            "type_post_list" => UsersSign::getTypePost(),
            "now" => Carbon::now()->format("Y-m-d"),
        ]);
    }

    static function store(Request $request)
    {
        UsersSign::create($request->all());
        return redirect()->route("admin.orgs.sign.index");
    }

    static function edit($id)
    {
        $unit = UsersSign::find($id);
        return view('orgs::manager.sign.form', [
            'unit' => $unit,
            "orgs" =>  Org::with('organization')->get(),
            'users_list' => Users::all(),
            "type_list" => UsersSign::getType(),
            "base_list" => UsersSign::getBase(),
            "type_post_list" => UsersSign::getTypePost(),
            "now" => Carbon::now()->format("Y-m-d"),
        ]);
    }

    static function update(Request $request, $id)
    {
        $users_sign = UsersSign::find($id);
        $users_sign->update($request->all());
        return redirect()->route("admin.orgs.sign.index");
    }

    static function show($id)
    {
        return redirect()->route("admin.orgs.sign.index");
    }
}
