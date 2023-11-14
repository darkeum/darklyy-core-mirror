<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Warranties\UI\WEB\Controllers;

use Boot\System\Http\Request;
use Boot\Controller\AbstractController;
use Realization\Model\Realization;

class WarrantiesController extends AbstractController
{
    static function create()
    {
        return view("warranties::manager.generate");
    }  

    public function getRealization(Request $request)
    {
        $res = [];
        $search = $request->q;

        $res = Realization::Where('id', 'LIKE', "%$search%")
            ->orWhere('numb', 'LIKE', "%$search%")
            ->get();

        foreach ($res as $item) {
            $item->push($item->organization);
        }

        return response()->json($res);
    }

    public function getData(Request $request)
    {
        $realization_id = $request->get("realization_id");
        $result = Realization::with(["realization_detail" => function ($query) {
            $query->whereHas("catalog", function ($query) {
                $query->where("man", 20);
            });
        }, "user_create", "org_.organization"])->where("id", $realization_id)->first();
        $arr['data'] = [];
        if ($result->realization_detail->isNotEmpty()) {
            $arr['data'] = $result->realization_detail->toArray();
        }
        return json_encode($arr);
    }
}
