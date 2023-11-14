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
use Storage\Models\StockTransfer;
use Illuminate\Support\Collection;
use Realization\Model\Realization;
use Home\UI\WEB\Charts\EarningsChart;
use Sales\Models\Sales as ModelsSales;
use Darkeum\Charts\Support\Livewire\ChartComponent;
use Darkeum\Charts\Support\Livewire\ChartComponentData;


class Earnings extends ChartComponent
{
    public $total;
    public $more;
    public $proc;

    protected function view(): string
    {
        return 'home::manager.components.earnings';
    }

    protected function chartClass(): string
    {
        return EarningsChart::class;
    }

    public function mount()
    {
        $now = $this->salesCache();
        $sub = $this->salesCache(2);
        $total = 0;
        $total_sub = 0;
        foreach ($now['result'] as $key => $value) {
            $total = $total + $value;
        }
        foreach ($sub['result'] as $key => $value) {
            $total_sub = $total_sub + $value;
        }
        $this->more = ($total > $total_sub) ? __('home::earnings.plus') : __('home::earnings.minus');
        if ($total_sub == 0) {
            $this->proc = 0;
        } else {
            $this->proc = round($total / $total_sub * 100, 2);
        }
        $this->total = Numb::formatMoney($total, '₽');
    }

    protected function salesCache($sub = 1)
    {
        return Cache::remember('earnings.' . $sub, 86000, function () use ($sub) {
            if ($sub - 1 == 0) {
                $start = Carbon::now()->format('Y-m-d');
                $end = Carbon::now()->subMonth()->format('Y-m-d');
            } else {
                $start = Carbon::now()->subMonth($sub - 1)->format('Y-m-d');
                $end = Carbon::now()->subMonth($sub)->format('Y-m-d');
            }
            $sales = ModelsSales::whereBetween('date', [$end, $start])->get();
            $result = [];
            foreach ($sales as $sale) {
                $total = 0;
                switch ($sale->sales_type) {
                    case 1:
                    case 3:
                        $total = Realization::where("sales_id", $sale->id)->where("active", 1)->sum("total");
                        break;
                    case 2:
                    case 4:
                        $transfer = StockTransfer::where("sales_id", $sale->id)->where("active", 1)->withSum('stock_transfer_detail as detail_total', 's_price')->get();
                        $total = 0;
                        foreach ($transfer as $value) {
                            $total = $total + $value->detail_total;
                        }
                        break;
                }
                if ($total != 0) {
                    $result[$sale->sales_type] = ($result[$sale->sales_type] ?? 0) +  $total;
                }
            }
            $title = [];
            foreach ($result as $key => $value) {
                $title[] = ModelsSales::getSalesType()[$key] ?? $key;
                $result[$key] = round($result[$key], 2);
            }
            return [
                'title' => $title,
                'result' => $result
            ];
        });
    }

    protected function chartData(): ChartComponentData
    {
        $data = $this->salesCache();
        $total = 0;
        foreach ($data['result'] as $value) {
            $total = $total + $value;
        }
        $percent = [];
        foreach ($data['result'] as $value) {
            $percent[] = round($value / $total * 100, 2) + 10;
        }
        $datasets = new Collection([$percent]);
        $labels = new Collection($data['title']);
        $datasets_name = [__('home::orders.dataset')];
        return (new ChartComponentData($labels, $datasets, $datasets_name));
    }
}
