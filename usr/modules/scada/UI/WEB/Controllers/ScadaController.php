<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\WEB\Controllers;

use Scada\Models\Scada;
use Boot\System\Http\Request;
use Boot\Support\Facades\Auth;
use Boot\Controller\AbstractController;

class ScadaController extends AbstractController
{
    static function dashboard()
    {
        return view('scada::manager.dashboard');
    }

    static function index()
    {
        return view('scada::manager.index');
    }

    static function create()
    {
        return view('scada::manager.create');
    }

    static function edit($id)
    {
        $item = Scada::findOrFail($id);
        return view('scada::manager.edit', [
            'item' => $item,
        ]);
    }

    static function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'address' => 'required',
            'ip' => 'required',
            'port' => 'required|numeric',
        ]);
        Scada::create([
            'title' => $request->input('title'),
            'uid' => Auth::id(),
            'address' => $request->input('address'),
            'ip' => $request->input('ip'),
            'port' => $request->input('port'),
            'rtuConverter' => $request->input('rtuConverter') == 'on' ? 1 : 0,
            'status' => $request->input('status') == 'on' ? 1 : 0,
        ]);
        return back()->with('created', __('scada::form.main.created'));
    }

    static function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'address' => 'required',
            'ip' => 'required',
            'port' => 'required|numeric',
        ]);
        $item = Scada::findOrFail($id);
        $item->update([
            'title' => $request->input('title'),
            'address' => $request->input('address'),
            'ip' => $request->input('ip'),
            'port' => $request->input('port'),
            'rtuConverter' => $request->input('rtuConverter') == 'on' ? 1 : 0,
            'status' => $request->input('status') == 'on' ? 1 : 0,
        ]);
        return back()->with('updates', __('scada::form.main.updated'));
    }

    static function show($id)
    {
        return view('scada::manager.show');
    }
}
