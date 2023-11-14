<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Catalogv2s\UI\WEB\Controllers;

use Boot\Support\Facades\Auth;
use Boot\Controller\AbstractController;
use Catalogv2s\Models\Pump;
use Boot\System\Database\DB;
use Boot\System\Http\Request;
use Catalog\Models\Catalog;
use Catalog_v2\Model\Catalog as ModelCatalog;
use Catalog_v2\Model\CatalogManufacture;

class Catalogv2sController extends AbstractController
{
    static function index()
    {
        return view('catalogv2s::manager.index');
    }

    static function pumpIndex()
    {
        return view('catalogv2s::manager.pump.index',[
            'pumpCheck'=>"yes",
            'select_catalog' => static::getSelect([
                "name" => "select_catalog",
                "ajaxUrl" => "getCatalogToSelect",
                "select_class" => "sales_catalog",
            ]),
        ]);
    }

    static function pumpCreate(Request $request)
    {    
        $cid =array("cid" => $request->cid);
        $pump = \Catalog_v2\Model\Pump::create($cid);
        return $pump->id;
    }

    static function pumpEdit($id)
    {
        // $unit =DB::table("pump as p")->
        // where("p.id",'=',$id)->
		// leftJoin("catalog as c","p.cid","=","c.id")->
		// leftJoin("catalog_manufacture as cm", "c.man","=","cm.id")
        // ->get()->toArray();

        $pump = Pump::find($id);

        $catalog = Catalog::find($pump->cid);

        $catalog_manafacture = CatalogManufacture::find($catalog->man);

        $catalog = $catalog->toArray();
        unset($catalog['id']);

        $catalog_manafacture = $catalog_manafacture->toArray();
        unset($catalog_manafacture['id']);

        $result = array_merge($pump->toArray(),$catalog,$catalog_manafacture);

        return view('catalogv2s::manager.pump.edit',[
            'unit' => (object)$result,
            'pumpCheck'=>"yes",
            'select_catalog' => static::getSelect([
                "name" => "select_catalog",
                "ajaxUrl" => "getCatalogToSelect",
                "select_class" => "sales_catalog",
            ]),
        ]);
    }
    static function pumpUpdate($id,Request $request)
    {   
        
        $unit = Pump::find($id);
        $arr = [
            'type_of_instalation' => $request['type_of_instalation'],
            'work_line' => $request['work_line'],
            'type' => $request['type'],
            'data' => $request['data'],
            'uid_change' => Auth::id(),
        ];

        $unit->update($arr);

        return redirect()->route('admin.catalogv2s.pump.edit',['id' => $id]);
    }

    static function pumpDestroy($id)
    {
        $unit = Pump::find($id);
        // $unit->delete(); //для жОсткого удаления
        $arr = [
            'deleted_at' => date('Y-m-d H:i:s'),
        ];
        $unit->update($arr);

        return redirect()->route('admin.catalogv2s.pump');
    }

    private static function getSelect($request)
    {
        $oSelectSettingJS = [
            "mod" => "jsReturn",
            "name" => $request["name"],
            "ajaxUrl" => $request["ajaxUrl"],
            "id" => $request["id"] ?? null,
            "return" => true,
            "bootstrap" => 'default',
        ];

        if (isset($request["select_class"])) {
            $oSelectSettingJS["select_class"] = $request["select_class"];
        }

        $oSelectSettingSelect = $oSelectSettingJS;
        $oSelectSettingSelect["mod"] = "select";
        return [
            "js" => oSelectCustom($oSelectSettingJS),
            "select" => oSelectCustom($oSelectSettingSelect),
        ];
    }


}
 
