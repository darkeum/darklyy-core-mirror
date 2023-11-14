<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Inventory\UI\WEB\Controllers;

use Orgs\Models\Org;
use Boot\Models\Users;
use Boot\Models\Categories;
use Boot\System\Http\Request;
use Boot\Support\Facades\Auth;
use Inventory\Models\Inventory;
use Inventory\Models\InventoryCheck;
use Boot\Controller\AbstractController;
use Boot\Support\Carbon;
use Inventory\Models\InventoryLocation;

class InventoryController extends AbstractController
{
    public static function index()
    {
        return view('inventory::manager.index');
    }

    public static function create()
    {
        $cats_arr = Categories::all();
        $orgs = Org::with('organization')->get();
        $locations = InventoryLocation::all();
        $users = Users::all();

        return view('inventory::manager.create', [
            'cats_arr' => $cats_arr,
            "orgs" => $orgs,
            'users_list' => $users,
            'locations' => $locations
        ]);
    }

    public static function show($id)
    {
        return redirect(route('admin.inventory.edit', $id));
    }

    public static function edit($id)
    {
        $cats_arr = Categories::all();
        $orgs = Org::with('organization')->get();
        $locations = InventoryLocation::all();
        $users = Users::all();
        $check = InventoryCheck::with('users')->where('iid', $id)->orderBy('date', 'desc')->first();

        if (isset($check->date)) {
            $check = $check->date . ' ' . shortUserName($check->uid);
        } else {
            $check = __('inventory::general.empty');
        }

        $unit = Inventory::find($id);

        return view('inventory::manager.create', [
            'cats_arr' => $cats_arr,
            "orgs" => $orgs,
            'users_list' => $users,
            'locations' => $locations,
            'check' => $check,
            'unit' => $unit

        ]);
    }

    public static function store(Request $data)
    {
        $unit = Inventory::create([
            'category' => $data['category'],
            'serial_number' => $data['serial_number'],
            'name' => $data['name'],
            'spec' => $data['spec'],
            'location' => $data['location'],
            'uid' => Auth::id(),
            'status' => 1,
            'wid' => $data['wid'],
            'org' => $data['org']
        ]);

        return redirect()->route("admin.inventory.edit", $unit->id)->with(['message' => __('inventory::general.created')]);
    }

    public static function update(Request $data)
    {
        Inventory::find($data['id'])->update([
            'category' => $data['category'],
            'serial_number' => $data['serial_number'],
            'name' => $data['name'],
            'spec' => $data['spec'],
            'location' => $data['location'],
            'uid' => Auth::id(),
            'updated_at' => Carbon::now(),
            'wid' => $data['wid'],
            'org' => $data['org']
        ]);
        return redirect()->route("admin.inventory.edit", $data['id'])->with(['message' => __('inventory::general.updated')]);
    }
}
