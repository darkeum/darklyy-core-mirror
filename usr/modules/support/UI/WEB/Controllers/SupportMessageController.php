<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Support\UI\WEB\Controllers;

use Boot\System\Http\Request;
use Support\Models\SupportMessage;
use Boot\Controller\AbstractController;

class SupportMessageController extends AbstractController
{
    public static function show($id)
    {
        return view('support::manager.messages', [
            'messages' => SupportMessage::where("support_id", $id)->with("attach")->get(),
        ]);
    }

    public static function store(Request $request)
    {
        //dd($request->all());
        SupportMessage::create($request->request->all());
    }
}
