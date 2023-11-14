<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Markets\UI\WEB\Controllers;

use Boot\Controller\AbstractController;
use Boot\System\Http\Request;
use Illuminate\Support\Facades\Http;

use Catalog\Models\Catalog;


class OzonController extends AbstractController
{
    // API KEY  - 5c76599a-d6b5-4922-be45-da672b3039f9
    // CLIENT ID - 365843
    static public function createCard($id)
    {   
        // if(!$request->has('catalogID'))
        // {
        //     $arr = ['message' => 'Ошибка'];
        //     return collect($arr)->toJson();
        // }
        // $catalogID = $request->get('catalogID');

        return Http::dd()->withHeaders([
            'Client-Id' => env('OZON_CLIENT_ID'),
            'Api-Key' => env('OZON_API_KEY'),
        ])->get('http://example.com',[
            'host' => 'api-seller.ozon.ru',
            'Content-Type' => 'application/json'
        ]);
        $unit = Catalog::find($id);
        dd($unit);
        // $arr = ['message' => $catalogID];
        // return collect($arr)->toJson();
        // dd(env('OZON_CLIENT_ID'),env('OZON_API_KEY'),$id);
    }
}
