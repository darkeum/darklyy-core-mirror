<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Cashbox\UI\WEB\Controllers;

use Sync\Sync;
use Boot\System\Http\Request;
use Boot\Support\Facades\Auth;
use Cashbox\Models\CashboxChecks;
use Boot\Controller\AbstractController;

class ChecksController extends AbstractController
{
    static function index()
    {
        return view('cashbox::manager.checks');
    }

    static function store(Request $request)
    {
        $CashboxChecks = new CashboxChecks;
        $CashboxChecks->uid = Auth::id();
        $CashboxChecks->realization_id = $request->get("realization_id");
        $CashboxChecks->cashier_shifts_id = $request->get("cashier_shifts_id");
        $CashboxChecks->data = json_decode($request->get("data"));
        $CashboxChecks->TypeCheck = $request->get("TypeCheck");
        $CashboxChecks->save();
        $sync = new Sync;
        $sync->syncRegister('cashbox_checks', $CashboxChecks->id, '1', $CashboxChecks->cashier_shifts->cashbox->org);
        echo $CashboxChecks->toJson();
    }
}
