<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Home\Traits;

use Boot\Support\Carbon;
use Carbon\CarbonPeriod;
use Boot\Support\Facades\Auth;
use Boot\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Sales\Models\Sales as ModelsSales;

trait OrderProfitTrait
{
    protected function cacheItems()
    {
        return Cache::remember('order.profit.user.' .  Auth::id(), 86000, function () {
            $sub = 4;
            $sales = ModelsSales::select(DB::raw("SUM(total) as totalDiff, count(*) as countDiff, DATE_FORMAT(date,'%m-%Y') AS dateDiff, DATE_FORMAT(date,'%Y') AS dateYear, DATE_FORMAT(date,'%m') AS dateMonth"))->where('uid', Auth::id())->where('date', '>=', Carbon::now()->subMonth($sub))->groupBy('dateMonth')->orderBy('dateMonth')->get();         
            $period = CarbonPeriod::create(Carbon::now()->subMonth($sub), '1 month', Carbon::now());
            foreach ($sales as $value) {
                $order[$value->dateMonth] = $value->countDiff;
                $profit[$value->dateMonth] = $value->totalDiff;
            }
            $series_order = [];
            $series_profit = [];
            $total_order = 0;
            $total_profit = 0;
            foreach ($period as $dt) {
                if (isset($order[$dt->format('m')])) {
                    $series_order[$dt->format('m')] = $order[$dt->format('m')];
                    $series_profit[$dt->format('m')] = $profit[$dt->format('m')];
                    $total_order = $total_order + $order[$dt->format('m')];
                    $total_profit = $total_profit + $profit[$dt->format('m')];
                } else {
                    $series_order[$dt->format('m')] = 0;
                    $series_profit[$dt->format('m')] = 0;
                }
                if($dt->format('m') == Carbon::now()->format('m')){
                    $now_order = $order[$dt->format('m')] ?? 0;
                    $now_profit = $profit[$dt->format('m')] ?? 0;
                }
                $months_order[] = $dt->locale('ru_RU')->isoFormat('MMMM');
                $months_profit[] = $dt->locale('ru_RU')->isoFormat('MMMM');
            }
            
            return [
                'series_order' => $series_order, 
                'series_profit' => $series_profit, 
                'months_order' => $months_order, 
                'months_profit' => $months_profit, 
                'total_order' => $total_order, 
                'total_profit' => $total_profit, 
                'now_order' => $now_order, 
                'now_profit' => $now_profit, 
            ];
        });
    }

}
	