<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\WEB\Controllers;

use Boot\System\Http\Request;
use Price\Models\CatalogPriceType;
use Boot\Controller\AbstractController;

class PriceTypeController extends AbstractController
{

    static function index()
    {
        return view('price::manager.types.index');
    }

    static function edit($id)
    {
        $data = CatalogPriceType::find($id);
        return view(
            'price::manager.types.edit',
            [
                'data' => $data,
            ]
        );
    }

    static function update(Request $request)
    {
        $pt = CatalogPriceType::find($request->id);
        $pt->update($request->all());
        if ($request->get("update_link")) {
            $pt->link->course = $pt->course;
            $pt->link->save();
        }
        return back()->with('status', __('price::types.updated'));
    }
}
