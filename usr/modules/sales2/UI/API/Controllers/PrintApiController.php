<?php

namespace Sales2\UI\API\Controllers;

use Sales\Model\Sales;
use Boot\System\Http\Request;
use Boot\Support\Facades\Auth;
use Boot\Controller\AbstractController;

class PrintApiController extends AbstractController
{
    static function print($type, $id, Request $request)
    {
        $sales = Sales::where('id', $id)->whereHas("client", function (\Boot\System\Database\Model\Builder $query) {
            $query->where('uid',  Auth::id());
        })->get();
        if (count($sales)) {
            \Sales\Support\SalesPrintSupport::print_sales($id, $type, $request->all());
        } else {
            abort(403);
        }
    }
}
