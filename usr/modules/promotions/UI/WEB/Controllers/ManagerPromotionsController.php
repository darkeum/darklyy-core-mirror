<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Promotions\UI\WEB\Controllers;

use Boot\System\Http\Request;
use Promotions\Models\Promotions;
use Boot\Controller\AbstractController;
use Sales\Models\Sales;

class ManagerPromotionsController extends AbstractController
{
    public static function index()
    {
        return view('promotions::manager.index',[
            'title' => __("promotions::general.promitions"),
        ]);
    }

    public static function create()
    {     
        return view('promotions::manager.create',[        
            'types' => self::types()
        ]);
    }

    public static function store(Request $request)
    {
       
        $array = $request->request->all();

        if ($request->file('preview')) {
            $preview = storage_img_load($request->file('preview'), 'promotions', null, 'png');
        }

        if ($request->file('preview_doc')) {
            $preview_doc = storage_img_load($request->file('preview_doc'), 'promotions', null, 'png');
        }

        $array['preview'] = $preview ?? '';
        $array['preview_doc'] = $preview_doc ?? '';

        $unit = Promotions::create($array);

        
        return redirect()->route("admin.promotions.edit",['id' => $unit->id])->with(['message' => "Акция создана"]);
    }    

    public static function update($id, Request $request)
    {
        
        $array = $request->request->all();

        if ($request->file('preview')) {
            $preview = storage_img_load($request->file('preview'), 'promotions', null, 'png');
            $array['preview'] = $preview ?? '';
        }

        if ($request->file('preview_doc')) {
            $preview_doc = storage_img_load($request->file('preview_doc'), 'promotions', null, 'png');
            $array['preview_doc'] = $preview_doc ?? '';
        }     
        
        $unit = Promotions::find($id);
        $unit->update($array);

        return redirect()->route("admin.promotions.edit",['id' => $unit->id])->with(['message' => "Акция изменена"]);

    }

    public static function edit($id)
    {   
        $unit = Promotions::findOrFail($id);   
        
        $sale = Sales::find($unit->sales_id);
        if($sale != null)
            $sale->push($sale->organization);

        return view('promotions::manager.create',[
            'title' => __("promotions::general.editor.edited"),           
            'types' =>  self::types(),
            'do' => __("promotions::general.editor.edit"),
            'unit' => $unit,   
            'type' => 'update',
            'sale' => $sale,
        ]);
    }

    public function getSales(Request $request)
    {
        $res = [];
        $search = $request->q;

        $res = Sales::Where('id', 'LIKE', "%$search%")
            ->orWhere('numb', 'LIKE', "%$search%")
            ->orWhere('title','LIKE', "%$search%")
            ->get();

        foreach ($res as $item) {
            $item->push($item->organization);
        }

        return response()->json($res);
    }

    protected static function types() {
        return [
            1 => __("promotions::general.types.sales"),
            2 => __("promotions::general.types.custom"),
        ];    
    }
}
