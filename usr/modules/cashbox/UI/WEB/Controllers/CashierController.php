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

use \Sync\Sync;
use Boot\System\Http\Request;
use Boot\Support\Facades\Auth;
use Cashbox\Models\CashierShifts;
use Boot\Controller\AbstractController;
use Cashbox\UI\WEB\Grids\CashierShiftsGrid;

class CashierController extends AbstractController
{
    static function index()
    {
        return view('cashbox::manager.cashier');
    }

    static function store(Request $request)
    {
        $CashierShifts = new CashierShifts;
        $CashierShifts->uid_start = Auth::id();
        $CashierShifts->cashiers_id = $request->get("cashiers_id");
        $CashierShifts->cashbox_id = $request->get("cashbox_id");
        $data_start = json_decode($request->get("data_start"));
        $CashierShifts->shift_number = $data_start->SessionNumber;
        $CashierShifts->start_shift = $data_start->Info->DateTimeKKT;
        $CashierShifts->number_checks = 0;
        $CashierShifts->data_start = $data_start;
        $CashierShifts->status = 1;
        $CashierShifts->save();

        static::sync($CashierShifts);

        echo $CashierShifts->toJson();
    }

    static function close_shift(Request $request)
    {
        $data_end = json_decode($request->get("data_end"));
        $cashiers_id = $request->get("cashiers_id");
        $cashbox_id = $request->get("cashbox_id");
        $shift_number = $data_end->SessionNumber;
        $CashierShifts = CashierShifts::where("cashiers_id", $cashiers_id)->where("cashbox_id", $cashbox_id)->where("status", 1)->where("shift_number", $shift_number)->first();
        $CashierShifts->uid_end = Auth::id();
        $CashierShifts->end_shift = $data_end->Info->DateTimeKKT;
        $CashierShifts->number_checks = $data_end->CheckNumber ?? 0;
        $CashierShifts->data_end = $data_end;
        $CashierShifts->status = 2;
        $CashierShifts->save();

        static::sync($CashierShifts);

        echo $CashierShifts->toJson();
    }

    static function find(Request $request)
    {
        $cashiers_id = $request->get("cashiers_id");
        $cashbox_id = $request->get("cashbox_id");
        $CashierShifts = CashierShifts::where("cashiers_id", $cashiers_id)->where("cashbox_id", $cashbox_id)->where("status", 1)->first();
        echo empty($CashierShifts) ? json_encode([]) : $CashierShifts->toJson();
    }

    static private function sync($CashierShifts)
    {
        $sync = new Sync;
        $sync->syncRegister('cashier_shifts', $CashierShifts->id, '1', $CashierShifts->cashbox->org);
    }
}
