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
use Home\Traits\OrderProfitTrait;
use Illuminate\Support\Collection;
use Home\UI\WEB\Charts\ProfitChart;
use Darkeum\Charts\Support\Livewire\ChartComponent;
use Darkeum\Charts\Support\Livewire\ChartComponentData;

class Profit extends ChartComponent
{
    use OrderProfitTrait;
    
    protected function view(): string
    {
        return 'home::manager.components.profit';
    }

    public function mount()
    {
        $this->total = Numb::formatMoney($this->cacheItems()['now_profit'], '₽');       
    }

    protected function chartClass(): string
    {
        return ProfitChart::class;
    }

    protected function chartData(): ChartComponentData
    {
        $data = $this->cacheItems();
        $datasets = new Collection([$data['series_profit']]);
        $labels = new Collection($data['months_profit']);
        $datasets_name = [__('home::orders.dataset')];
        return (new ChartComponentData($labels, $datasets, $datasets_name));
    }
}
