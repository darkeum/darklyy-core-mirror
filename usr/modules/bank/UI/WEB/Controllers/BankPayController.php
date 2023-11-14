<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Bank\UI\WEB\Controllers;

use Bank\Models\BankPay;
use Boot\System\Http\Request;
use Boot\Controller\AbstractController;

class BankPayController extends AbstractController
{
    static function store(Request $request)
    {
        $BankPay = BankPay::create($request->all());
        static::sales_pay_status($BankPay);
        echo $BankPay->toJson();
    }

    static function update($id, Request $request)
    {
        $BankPay = BankPay::findOrFail($id);
        $BankPay->update($request->all());
        if ($BankPay->wasChanged()) {
            static::sales_pay_status($BankPay);
        }
        echo $BankPay->toJson();
    }

    static function destroy($id)
    {
        $BankPay = BankPay::findOrFail($id);
        $BankPay->delete();
        static::sales_pay_status($BankPay);
        echo $BankPay->toJson();
    }

    static private function sales_pay_status($BankPay)
    {
        $bank_detail = $BankPay->bank_detail;
        if ($bank_detail->module == "sales") {
            BankController::sales_pay_status($BankPay->sales);
        }
    }
}