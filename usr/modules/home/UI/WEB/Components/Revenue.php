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

use Orgs\Models\Org;
use Boot\Support\Numb;
use Boot\Support\Carbon;
use Carbon\CarbonPeriod;
use Bank\Models\BankDetail;
use Sales\Support\SalesSupport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Home\UI\WEB\Charts\BudgetChart;
use Home\UI\WEB\Charts\RevenueChart;
use Darkeum\Charts\Support\Livewire\ChartComponent;
use Darkeum\Charts\Support\Livewire\ChartComponentData;

class Revenue extends ChartComponent
{
    public $salesCompleteTotal;
    public $salesRating;
    public $org;
    public $org_choose;
    public $org_choose_title;
    public $balance_now;
    public ?bool $has_second = true;

    public function reload()
    {
        $this->salesCompleteTotal =  SalesSupport::reloadSalesCompleteTotal();
    }

    public function changeRevenue($org_choose)
    {       
        $this->org_choose =  $org_choose;
        $this->org_choose_title = Org::with('organization')->find($this->org_choose)->organization->name;
        $this->getBalance();
        
    }

    public function getBalance(){
        $start = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
        $pay_in = BankDetail::whereBetween('date', [$start, $end])->where('pay_in', 1)->where('org',  $this->org_choose)->sum('sum') ?? 0;
        $pay_out = BankDetail::whereBetween('date', [$start, $end])->where('pay_in', 0)->where('org',  $this->org_choose)->sum('sum') ?? 0;
        $this->balance_now = Numb::formatMoney($pay_in - $pay_out, '₽');
    }

    public function mount()
    {
        $this->org_choose = 1;
        if (config('app.name') == 'AE') {
            $this->org =  Org::with('organization')->whereIn('id', [1, 2, 5])->get();
        } else {
            $this->org =  Org::with('organization')->get();
        }
        $this->getBalance();
        $this->org_choose_title = $this->org[0]->organization->name;
    }

    protected function cacheItems($org)
    {
        $start = Carbon::now()->subMonth(8)->startOfDay()->format('Y-m-d H:i:s');
        $end = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');

        $result_pay_in = BankDetail::select(DB::raw("SUM(sum) as totalDiff, DATE_FORMAT(date,'%m-%Y') AS dateDiff, DATE_FORMAT(date,'%Y') AS dateYear, DATE_FORMAT(date,'%m') AS dateMonth"))->where('org', $org)->where('pay_in', 1)->whereBetween('date', [$start, $end])->groupBy('dateDiff')->orderBy('dateDiff')->get();

        $result_pay_out = BankDetail::select(DB::raw("SUM(sum) as totalDiff, DATE_FORMAT(date,'%m-%Y') AS dateDiff, DATE_FORMAT(date,'%Y') AS dateYear, DATE_FORMAT(date,'%m') AS dateMonth"))->where('org', $org)->where('pay_in', 0)->whereBetween('date', [$start, $end])->groupBy('dateDiff')->orderBy('dateDiff')->get();

        return [
            'pay_in' => $result_pay_in,
            'pay_out' => $result_pay_out,
        ];
    }

    protected function chartData(): ChartComponentData
    {
        $data = $this->cacheItems($this->org_choose);
        $period = CarbonPeriod::create('2018-01-01', '1 month', '2018-12-31');
  

        $period = CarbonPeriod::create(Carbon::now()->subMonth(8), '1 month', Carbon::now());
        $real = [];
        foreach ($period as $dt) {
            $months[] = $dt->locale('ru_RU')->isoFormat('MMM');
            $real['pay_in'][$dt->format('m')] =  0;
            $real['pay_out'][$dt->format('m')] =  0;
        }

        $labels = new Collection($months);
       
        
        foreach ($data['pay_in'] as $value) {
            $real['pay_in'][$value->dateMonth] = $value->totalDiff;
        }
        foreach ($data['pay_out'] as $value) {
            $real['pay_out'][$value->dateMonth] = $value->totalDiff*-1;
        }
        $series = [];
        foreach ($real as $v) {
            $series[] = new Collection($v);
        }
        $datasets = new Collection($series);
        $datasets_name = [__('home::revenue.income'), __('home::revenue.consumption')];
        return (new ChartComponentData($labels, $datasets, $datasets_name));
    }

    protected function chartSecondData(): ChartComponentData {
        return $this->chartData();
    }

    protected function view(): string
    {
        return 'home::manager.components.revenue';
    }

    protected function chartClass(): string
    {
        return RevenueChart::class;
    }   
    
    protected function chartSecondClass(): string
    {
        return BudgetChart::class;
    }
}
