<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Stocks\UI\WEB\Controllers;

use Orgs\Models\Org;
use Stocks\Models\Stock;
use Boot\System\Http\Request;
use Boot\Controller\AbstractController;

class ManagerController extends AbstractController
{

    public static function index()
    {   
        return view('stocks::manager.index');
    }


    public static function create()
    {
        $orgs = Org::all();
        foreach($orgs as $org){
            
            $org['name'] = $org->organization->name;
        }

        return view('stocks::manager.create',[
            'orgs' => $orgs,
        ]);
    }

    public static function store(Request $dataReq)
    {   
        $array = [
            'name' => $dataReq['name'],
            'org' =>  $dataReq['org']
        ];
        $unit = Stock::create($array);

        return redirect()->route('admin.stocks.edit',$unit->id)->with('success', __('stocks::general.stock.created'));
    }

    public static function show($id)
    {
        $unit = Stock::find($id);
        $org = Org::find($unit->org)->organization; 

        return view('stocks::manager.show', [
            'unit' => $unit,
            'org' => $org,
        ]);
    }

    public static function edit($id)
    {
        $orgs = Org::all();
        foreach($orgs as $org){
            
            $org['name'] = $org->organization->name;
        }
        $unit = Stock::find($id);

        return view('stocks::manager.create', [
            'orgs' => $orgs,
            'unit' => $unit,
        ]);
    }

    public static function update(Request $dataReq)
    {

        $unit = Stock::find($dataReq['id']);
        $unit->update($dataReq->request->all());

        return redirect()->route('admin.stocks.edit',$unit->id)->with('success', __('stocks::general.stock.updated'));
    }

}
