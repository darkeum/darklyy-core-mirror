<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\WEB\Components;

use Boot\Support\Carbon;
use Illuminate\Support\Collection;
use Price\Models\CatalogPriceRival;
use Price\UI\WEB\Charts\ReceivingProgressChart;
use Darkeum\Charts\Support\Livewire\ChartComponent;
use Darkeum\Charts\Support\Livewire\ChartComponentData;

class ReceivingProgress extends ChartComponent
{
    public $all_count;
    public $complete_count;

    protected function view(): string
    {
        return 'price::manager.components.receiving.progress';
    }

    protected function chartClass(): string
    {
        return ReceivingProgressChart::class;
    }

    protected function getProgress()
    {
        $price = CatalogPriceRival::with("catalog")->whereHas('catalog', function ($query) {
            $query->where('hidden', 0);
        })->whereNull('disable')->whereNot('type', 1);
        $this->all_count = $price->count();
        $this->complete_count = $price->where('received_at', ">=", Carbon::now()->startOfDay())->count();
    }

    public function mount()
    {
        $this->getProgress();
    }

    public function reload()
    {
        $this->getProgress();
    }

    protected function chartData(): ChartComponentData
    {
        $this->getProgress();
        $percent = round($this->complete_count /  $this->all_count  * 100, 2);
        $datasets = new Collection([[$percent]]);
        $labels = new Collection([['ddd']]);
        $datasets_name = [__('home::orders.dataset')];
        return (new ChartComponentData($labels, $datasets, $datasets_name));
    }
}
