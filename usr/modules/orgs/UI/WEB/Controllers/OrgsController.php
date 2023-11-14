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

use Price;
use Orgs\Models\Org;
use Boot\Models\Users;
use Stocks\Models\Stock;
use Boot\System\Http\Request;
use Boot\Support\Facades\Storage;
use Organizations\Model\Organizations;
use Boot\Abstracts\Controllers\WebController;

class OrgsController extends WebController
{
    static public function index()
    {
        return view('orgs::manager.index');
    }

    static function create()
    {
        return view('orgs::manager.create', [
            "orgs" => Organizations::all(),
            "nds" => Price::getNds("all"),
            'users_list' => Users::all(),
        ]);
    }

    static function store(Request $request)
    {   
        $org = Org::create($request->all());

        return redirect()->route("admin.orgs.edit", $org->id)->with('success', __('orgs::general.created'));
    }

    static function edit($id)
    {
        $unit = Org::findOrFail($id);

        return view('orgs::manager.create', [
            "unit" => $unit,
            "nds" => Price::getNds("all"),
            "orgs" => Organizations::all(),
            "stocks" => Stock::all(),
            'users_list' => Users::all(),
        ]);
    }

    static function update(Request $request, $id)
    {
        $org = Org::find($id);

        if ($request->has("nds")) $org->nds = $request->get("nds");
        if ($request->has("nds_includes")) $org->nds_includes = $request->get("nds_includes");
        if ($request->has("prefix")) $org->prefix = $request->get("prefix");
        if ($request->has("taxation")) $org->taxation = $request->get("taxation") ?: null;
        if ($request->has("accountant")) $org->accountant = $request->get("accountant") ?: null;
        if ($request->has("director")) $org->director = $request->get("director") ?: null;
        if ($request->has("phone")) $org->phone = $request->get("phone") ?: null;
        if ($request->has("stock_id_default")) $org->stock_id_default = $request->get("stock_id_default") ?: null;
        if ($request->has("change_alert")) $org->change_alert = $request->get("change_alert");

        $files = $request->files->all();
        foreach ($files as $field => $file) {
            $path = "/files/org/".$org->id."/";
            $ext = $file->getClientOriginalExtension();
            $filename = $field.".".$ext;
            Storage::disk('local')->putFileAs(
                $path,
                $file,
                $filename
            );
            $org->$field = $path.$filename;
        }

        $org->save();
        
        return redirect()->route("admin.orgs.edit", $org->id)->with('success', __('orgs::general.updated'));
    }


    static function show($id)
    {
        $unit = Org::findOrFail($id);

        return view('orgs::manager.show', [
            "unit" => $unit,
           
        ]);
    }

 }
