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
use Boot\Support\Carbon;
use Boot\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Home\UI\WEB\Charts\GoalChart;
use Darkeum\Charts\Support\Livewire\ChartComponent;
use Darkeum\Charts\Support\Livewire\ChartComponentData;
use Realization\Model\Realization;

class Goal extends ChartComponent
{


    public $total;
    public $need;
    public $need_period;
    public $progress;
    public $progress_text;

    protected function view(): string
    {
        return 'home::manager.components.goal';
    }

    public function changeGoal($period)
    {
        if ($period == 'year' || $period == 'quarter') {
            $this->period = $period;
        } else {
            $this->period = 'quarter';
        }
        $this->getVariable();
    }


    protected function cacheItems()
    {
        Cache::forget('goal');
        return  Cache::remember('goal', 3600, function () {
            $result = [];
            $startYear = Carbon::now()->startOfYear()->format('Y-m-d');
            $startQuarter = Carbon::now()->startOfQuarter()->format('Y-m-d');
            $end = Carbon::now()->endOfDay()->format('Y-m-d');
            $result['year'] =  Realization::whereBetween('date', [$startYear, $end])->where('active', 1)->where('org', 1)->sum('total');
            $result['quarter'] =  Realization::whereBetween('date', [$startQuarter, $end])->where('active', 1)->where('org', 1)->sum('total');
            return $result;
        });
    }

    protected function getVariable()
    {
        $this->need = [26200000, 60000000, 97800000, 78000000];
        $data = $this->cacheItems();
        $need_period = $this->getNeedPeriod();   
        $progress = $need_period - $data[$this->period];    
        if($data[$this->period] > 1000000){
            $this->total = Numb::formatMoney($data[$this->period]/1000000 ?: 0, 'млн. ₽');
        } else {
            $this->total = Numb::formatMoney($data[$this->period] ?: 0, '₽');
        }
        if($need_period > 1000000){
            $this->need_period = Numb::formatMoney($need_period/1000000 ?: 0, 'млн. ₽');
        } else {
            $this->need_period = Numb::formatMoney($need_period ?: 0, '₽');
        }
        if(abs($progress) > 1000000){
            $this->progress = Numb::formatMoney(abs($progress/1000000) ?: 0, 'млн. ₽');
        } else {
            $this->progress = Numb::formatMoney(abs($progress) ?: 0, '₽');
        }
        if($progress < 0){
            $this->progress_text = __('home::goal.progress.more');
        } else {
            $this->progress_text = __('home::goal.progress');
        }
    }

    public function mount()
    {
        $this->period = 'quarter';
        $this->getVariable();
    }

    protected function chartClass(): string
    {
        return GoalChart::class;
    }

    protected function getNeedPeriod(): string
    {
        if ($this->period == 'year') {
            $need_period = array_sum($this->need);
        } else {
            $need_period = $this->need[(Carbon::now()->quarter)-1];
        }
        return $need_period;
    }

    protected function chartData(): ChartComponentData
    {
        $data = $this->cacheItems();
        $need_period = $this->getNeedPeriod();
        $percent = round($data[$this->period] / $need_period * 100, 2);
        $datasets = new Collection([[$percent]]);
        // dd($datasets);
        $labels = new Collection([$data['year']]);
        $datasets_name = [__('home::orders.dataset')];
        return (new ChartComponentData($labels, $datasets, $datasets_name));
    }
}
