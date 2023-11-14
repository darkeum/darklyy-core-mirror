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

use Illuminate\Support\Collection;
use Home\UI\WEB\Charts\OrdersChart;
use Darkeum\Charts\Support\Livewire\ChartComponent;
use Darkeum\Charts\Support\Livewire\ChartComponentData;
use Home\Traits\OrderProfitTrait;

class Orders extends ChartComponent
{
    use OrderProfitTrait;

    public $total;

    protected function view(): string
    {
        return 'home::manager.components.orders';
    }

    
    public function mount()
    {
        $this->total = $this->cacheItems()['now_order'];       
    }

    protected function chartClass(): string
    {
        return OrdersChart::class;
    }
   
    protected function chartData(): ChartComponentData
    {
        $data = $this->cacheItems();
        $datasets = new Collection([$data['series_order']]);
        $labels = new Collection($data['months_order']);
        $datasets_name = [__('home::orders.dataset')];
        return (new ChartComponentData($labels, $datasets, $datasets_name));
    }
}
