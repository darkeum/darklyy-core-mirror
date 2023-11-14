<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Home\UI\WEB\Components;

use Boot\Support\Numb;
use Carbon\CarbonPeriod;
use Boot\Support\Carbon;
use Sales\Support\SalesSupport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Realization\Model\Realization;
use Boot\System\Livewire\Component;
use Home\UI\WEB\Charts\AnnualChart;
use Darkeum\Charts\Support\Livewire\ChartComponent;
use Darkeum\Charts\Support\Livewire\ChartComponentData;

class Annual extends ChartComponent
{
    protected function view(): string
    {
        return 'home::manager.components.annual';
    }


    public function reload()
    {
       
    }

    protected function chartClass(): string
    {
        return AnnualChart::class;
    }

    protected function chartData(): ChartComponentData
    {
        $realization = Realization::select(DB::raw("SUM(total) as totalDiff, DATE_FORMAT(date,'%m-%Y') AS dateDiff, DATE_FORMAT(date,'%Y') AS dateYear, DATE_FORMAT(date,'%m') AS dateMonth"))->where('org', 1)->where('active', 1)->where('date', '>=', '2019-01-01')->groupBy('dateDiff')->orderBy('dateDiff')->get();


        $period = CarbonPeriod::create('2018-01-01', '1 month', '2018-12-31');
        $months = [];
        foreach ($period as $dt) {
            $months[] = $dt->locale('ru_RU')->isoFormat('MMM');
        }
        $labels = new Collection($months);
        $real = [];
        foreach ($realization as $key => $value) {
            $real[$value->dateYear][$value->dateMonth] = $value->totalDiff;
        }
        $series = [];
        if (empty($real)) {
            $series[] = new Collection([]);
        } else {
            foreach ($real as $k => $v) {
                $series[] = new Collection($v);
            }
        }


        $datasets = new Collection($series);

        return (new ChartComponentData($labels, $datasets));
    }
}
