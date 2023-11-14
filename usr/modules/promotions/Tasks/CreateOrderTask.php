<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Promotions\Tasks;

use Sales\Model\Sales;
use Sales\Model\SalesUsers;
use Sales\Model\SalesDetail;
use Boot\Support\Facades\Auth;
use Boot\System\Core\Abstracts\Tasks\Task;

class CreateOrderTask extends Task
{
    public function run(): mixed
    {
        if (isset($_POST['ids'])) {
            $ids = explode(',', $_POST['ids']);
        }
        $request = array(
            'object' => $_POST['object'] ?? null,
            'oid' => $_POST['oid'] ?? 507,
            'org' => '1',
            'nds_includes' => '1',
            'stock_id' => '1',
            'uid_send' => 0
        );
        $sales = Sales::create($request);
        $total = 0;
        foreach ($ids as $key => $value) {
            $detail = SalesDetail::find($value);
            $discount_price = \Price::get_all(["price" => $detail->s_price, "discount" => $detail->discount]);
            $total += $value;
            $requestDetail = array(
                'sales_id' => $sales->id,
                'cid' => $detail->cid,
                's_sku' => $detail->s_sku,
                's_name' => $detail->s_name,
                'price_type' => $detail->price_type,
                's_unit' => $detail->s_unit,
                'count' => 1,
                's_price' => $detail->s_price,
                'discount' => $detail->discount,
                'type_nds' =>  $detail->type_nds,
                'nds' => $discount_price['nds'],
                'total' => $discount_price['discount_price'],
            );
            SalesDetail::create($requestDetail);
        }
        $SalesUsers = new SalesUsers;
        $SalesUsers->sales_id = $sales->id;
        $SalesUsers->type = "CLIENT";
        $SalesUsers->uid = Auth::id();
        $SalesUsers->save();

        $result = [
            'type' =>  'success',
            'sales_id' =>  $sales->id,
        ];
        return $result;
    }
}
