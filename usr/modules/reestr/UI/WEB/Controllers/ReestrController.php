<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Reestr\UI\WEB\Controllers;

use Boot\Controller\AbstractController;
use Boot\System\Http\Request;
use Objects\Models\Objects;
use Organizations\Model\Organizations;
use Orgs\Models\Org;
use Reestr\Models\Reestr;
use Boot\Models\Users;

class ReestrController extends AbstractController
{
    static public function index()
    {
        return view('reestr::manager.index');
    }

    static public function show($id)
    {
        return view('reestr::manager.show', [
            'unit' => Reestr::find($id),
        ]);
    }

    static public function create()
    {
        return view('reestr::manager.create', [
            'orgs' => Org::with('organization')->get()
        ]);
    }

    static public function store(Request $request)
    {
        $array = $request->request->all();
        $array['type'] = $array['rtype'];
        $array['auto'] = 0;
        $array['status'] = 1;
        $array['date_view'] = $array['date'] ?? null;
        $array['date'] = isset($array['date']) ? strtotime($array['date']) : null;

        $unit = Reestr::create($array);

        return redirect()->route('admin.reestr.show', $unit->id)->with(['message' => 'Документ создан']);
    }

    static public function destroy($id)
    {
        return redirect()->route('admin.reestr.index');
    }

    static public function edit($id)
    {
        return view('reestr::manager.create', [
            'orgs' => Org::with('organization')->get(),
            'unit' => Reestr::find($id),
        ]);
    }

    static public function update($id, Request $request)
    {
        $unit = Reestr::find($id);
        $array = $request->request->all();
        $array['type'] = $array['rtype'];
        $array['date'] = isset($array['date']) ? strtotime($array['date']) : null;

        $unit->update($array);

        return redirect()->route('admin.reestr.show', $unit->id)->with(['message' => 'Документ отредактирован']);
    }

    public function getOrgs(Request $request)
    {
        $res = [];
        $search = $request->q;
        $res = Organizations::select("id", "name", "inn")
            ->where('name', 'LIKE', "%$search%")
            ->orWhere('id', 'LIKE', "%$search%")
            ->orWhere('inn', 'LIKE', "%$search%")
            ->get();
        $result = [];
        foreach ($res as $value) {
            $result[] = ['id' => $value->id, 'name' => $value->name . ' (' . $value->inn . ')'];
        }
        return response()->json($result);
    }

    public function getUsers(Request $request)
    {
        $res = [];
        $search = $request->q;
        $res = Users::select("id", "name", 'surname', 'ochestvo')
            ->where('name', 'LIKE', "%$search%")
            ->orWhere('id', 'LIKE', "%$search%")
            ->orWhere('surname', 'LIKE', "%$search%")
            ->orWhere('ochestvo', 'LIKE', "%$search%")
            ->get();

        return response()->json($res);
    }

    public function getObj(Request $request)
    {
        $res = [];
        $search = $request->q;
        $res = Objects::select("id", "name")
            ->where('name', 'LIKE', "%$search%")
            ->orWhere('id', 'LIKE', "%$search%")
            ->get();

        return response()->json($res);
    }

    static function types()
    {
        return view('reestr::manager.types', []);
    }
}
