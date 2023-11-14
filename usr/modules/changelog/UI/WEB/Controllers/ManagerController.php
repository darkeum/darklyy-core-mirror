<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Certificates Manager Controller
*/

namespace Changelog\UI\WEB\Controllers;

use Boot\Models\Categories;
use Boot\System\Http\Request;
use Boot\Support\Facades\Auth;
use Changelog\Models\Changelog;
use Boot\Controller\AbstractController;


class ManagerController extends AbstractController
{
    //public static $module = 'changelog';
    //public static $lang = 'changelog';

    public function index()
    {
        return view('changelog::manager.index');
    }

    public function show($id)
    {
        $unit = Changelog::find($id);
        $cat = Categories::find($unit->category);
        //$name = shortUserName($unit->uid);

        return view('changelog::manager.show', [
            'unit' => $unit,
            //'id' => $id,
            //'data' => $data,
            'cat' => $cat,
            //'name' => $name
        ]);
    }

    public function create()
    {
        return view('changelog::manager.create');
    }

    public function store(Request $request)
    {
        $model = new Changelog();
        $arr = [
            'uid' =>  Auth::id(),
            'category' => $request['category'],
            'title' => $request['title'],
            'text' => $request['text']
        ];
        $model->fill($arr);
        $model->save();

        return redirect()->action([ManagerController::class, 'create']);
    }

    public function edit($id)
    {
        $unit = Changelog::find($id);
        //$cat = Categories::find($unit->category);
        $cat = Categories::find($unit['category']);

        //$name = shortUserName($unit->uid);

        return view('changelog::manager.create', [
            'unit' => $unit,
            'cat' => $cat,
            //'name' => $name
        ]);
    }

    public function update($id, Request $request)
    {
        $model = Changelog::find($id);
        $arr = [
            'category' => $request['category'],
            'title' => $request['title'],
            'text' => $request['text']
        ];
        $model->update($arr);

        return redirect()->route('admin.changelog.show', $id);
    }


    public function categories(Request $request)
    {
        $res = [];
        if($request->has('q')){
            $search = $request->q;
            $res =Categories::select("id", "name")
            		->where('name', 'LIKE', "%$search%")
                    ->orWhere('id', 'LIKE', "%$search%")
            		->get();
        }

        return response()->json($res);
    }

    
}
