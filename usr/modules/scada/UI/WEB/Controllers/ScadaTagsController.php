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
use Scada\Models\ScadaTags;
use Boot\System\Http\Request;
use Boot\Support\Facades\Auth;
use Boot\Controller\AbstractController;

class ScadaTagsController extends AbstractController
{
    static $registr = ['ReadInputRegistersRequest', 'ReadInputDiscretesRequest', 'WriteSingleCoilRequest', 'ReadHoldingRegistersRequest', 'ReadWriteMultipleRegistersRequest', 'ReadWriteSingleCoilRequest'];

    static $actions = ['Read', 'Write', 'ReadWrite'];

    static $actions_type = ['CommandReset'];

    static $field_type = ['thermometer', 'crash', 'auto', 'auto_invert', 'pump', 'open', 'water_level', 'puls', 'Input', 'ErrorReset', 'valve_rvd', 'crash_rvd', 'pressure', 'work'];

    static $endian = ['BIG_ENDIAN', 'LITTLE_ENDIAN', 'LOW_WORD_FIRST', 'BIG_ENDIAN_LOW_WORD_FIRST'];

    static $type = ['bool', 'int16', 'UInt16', 'int32', 'UInt32', 'float', 'Int64', 'highByte', 'lowByte', 'highByteBits', 'lowByteBits'];

    static function index()
    {
        return view('scada::manager.tags.index');
    }

    static function parameters()
    {
        return [
            'scada' => Scada::orderBy('id', 'desc')->get(),
            'registr' => static::$registr,
            'actions' => static::$actions,
            'actions_type' => static::$actions_type,
            'field_type' => static::$field_type,
            'endian' => static::$endian,
            'type' => static::$type,
        ];
    }

    static function prepare(Request $request)
    {
        $request->validate([
            'scada_id' => 'required',
            'title' => 'required',
            'unit_id' => 'required|numeric',
            'actions' => 'required',
            'registr' => 'required',
            'address' => 'required|numeric',
            'quantity' => 'required|numeric',
            'recalculation' => 'required',
            'type' => 'required',
        ]);
        $data = $request->all();
        $data['endian'] = $request->input('endian') == '0'  ? null : $request->input('endian');
        $data['actions_type'] = $request->input('actions_type') == '0'  ? null : $request->input('actions_type');
        $data['recalculation'] = $request->input('recalculation') == '0'  ? 0 : $request->input('recalculation');
        $data['field_type'] = $request->input('field_type') == '0'  ? null : $request->input('field_type');
        $data['notifiable'] = $request->input('notifiable') == 'on' ? 1 : 0;
        $data['status'] = $request->input('status') == 'on' ? 1 : 0;
        return $data;
    }

    static function create()
    {
        return view('scada::manager.tags.create', static::parameters());
    }

    static function store(Request $request)
    {
        $data = static::prepare($request);
        $data['uid'] = Auth::id();
        ScadaTags::create($data);
        return back()->with('created', __('scada::form.tags.created'));
    }

    static function edit($id)
    {
        $item = ScadaTags::findOrFail($id);
        return view('scada::manager.tags.edit', [
            'item' => $item,
        ] + static::parameters());
    }

    static function update(Request $request, $id)
    {
        $data = static::prepare($request);
        $data['uid'] = Auth::id();
        ScadaTags::findOrFail($id)->update($data);
        return back()->with('updates', __('scada::form.tags.updated'));
    }

    static function destroy($id)
    {
        $scada_tag = ScadaTags::findOrFail($id);
        $scada_tag->delete();
        return back();
    }
}
