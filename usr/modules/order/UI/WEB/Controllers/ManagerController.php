<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Portfolio Manager Controller
*/

namespace Order\UI\WEB\Controllers;

use \Order\Models\Order;
use \Order\Models\OrderJob;
use Order\Models\OrderPump;
use \Order\Models\OrderHeat;
use Order\Models\OrderBlock;
use Order\Models\OrderValve;
use Order\UI\WEB\Grids\Index;
use Boot\Support\Facades\Auth;
use Order\Models\OrderPumpStation;
use Order\Models\OrderMeteringStation;
use Boot\Controller\AbstractController;

class ManagerController extends AbstractController
{

    public static function index()
    {
        return view('order::manager.index', []);
    }

    public static function show($id)
    {
        $order = Order::find($id);
        $read_uid =  $order->read_uid;
        $read_uid[] = ['uid' => Auth::id(), 'date' => date('Y-m-d H:i:s')];
        $order->update(['read_uid' => $read_uid, 'read_status' => 1]);
        $suborder = [];
        if (!empty($order->type == 'job')) {
            $suborder = OrderJob::with('categories')->where('order_id', $order->id)->first();
        } elseif (!empty($order->type == 'heat')) {
            $suborder = OrderHeat::with('request')->where('order_id', $order->id)->first();
        } elseif (!empty($order->type == 'block')) {
            $suborder = OrderBlock::with('request')->where('order_id', $order->id)->first();
        } elseif (!empty($order->type == 'pump')) {
            $suborder = OrderPump::with('request')->where('order_id', $order->id)->first();
        } elseif (!empty($order->type == 'pump_station')) {
            $suborder = OrderPumpStation::with('request')->where('order_id', $order->id)->first();
        }elseif (!empty($order->type == 'metering_station')) {
            $suborder = OrderMeteringStation::with('request')->where('order_id', $order->id)->first();
        }elseif (!empty($order->type == 'valve')) {
            $suborder = OrderValve::with('request')->where('order_id', $order->id)->first();
        }

        switch ($order->type) {
            case 'job':
                $order_title = __('order::general.type.job');
                break;
            case 'heat':
                $order_title = __('order::general.type.heat');
                break;
            case 'block':
                $order_title = __('order::general.type.block');
                break;
            case 'pump':
                $order_title = __('order::general.type.pump');
                break;
            case 'pump_station':
                $order_title = __('order::general.type.pump_station');
                break;
            case 'metering_station':
                $order_title = __('order::general.type.metering_station');
                break;
            case 'valve':
                $order_title = __('order::general.type.valve');
                break;
        }
        

        return view('order::manager.show', [
            'title' => __('order::general.order'),
            'order' => $order,
            'order_title' => $order_title,
            'suborder' => $suborder,
        ]);
    }
}
