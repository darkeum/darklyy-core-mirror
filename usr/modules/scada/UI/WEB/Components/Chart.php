<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\WEB\Components;

use Boot\Support\Carbon;
use Illuminate\Support\Collection;
use Price\Models\CatalogPriceRival;
use Scada\UI\WEB\Charts\ScadaChart;
use Darkeum\Charts\Support\Livewire\ChartComponent;
use Darkeum\Charts\Support\Livewire\ChartComponentData;
use Scada\Models\ScadaTags;
use Scada\Models\ScadaValues;

class Chart extends ChartComponent
{
    public $all_count;
    public $complete_count;
    public $tags;
    public $scada_id;
    public $tags_choose;
    public $start;
    public $end;
    public $end_sql;
    public $start_sql;

    protected function view(): string
    {
        return 'scada::public.components.chart';
    }

    protected function chartClass(): string
    {
        return ScadaChart::class;
    }


    protected function getProgress()
    {
        $price = CatalogPriceRival::whereNull('disable')->whereNot('type', 1);
        $this->all_count = $price->count();
        $this->complete_count = $price->where('received_at', ">=", Carbon::now()->startOfWeek())->count();
    }

    protected function getTags()
    {
        $this->tags = ScadaTags::where('scada_id', $this->scada_id)->get();
    }

    public function mount()
    {

        $this->getTags();
        $this->getProgress();
    }

    public function reload()
    {
        $this->getProgress();
    }

    // protected $rules = [
    //     'support.title' => 'string',
    //     'support.type' => 'string|nullable',
    //     'support.place' => 'string|nullable',
    //     'support.url' => 'string|nullable',
    //     'support.mark' => 'numeric|nullable',
    // ];

    // public function graph()
    // {
    //     $this->validate();
    //     $this->start_sql = Carbon::parse($this->start)->format('Y-m-d H:i:s');
    //     $this->end_sql = Carbon::parse($this->end)->format('Y-m-d H:i:s');
    // }


    protected function chartData(): ChartComponentData
    {
        // $this->tags_choose = [340, 341,332,346,355,354];
        if (!empty($this->tags_choose)) {
            $this->start = Carbon::now()->startOfWeek();
            $this->end = Carbon::now();
            $items_values = ScadaValues::with('tags')->whereIn('scada_tags_id', $this->tags_choose)->whereBetween('created_at', [$this->start, $this->end])->get();
            if (!empty($items_values)) {
                $data = [];
                $label = [];
                $current_value = [];
                $last_value = [];
                $items = [];
                $items_counter = [];
                foreach ($this->tags_choose as $value) {
                    $items[$value] = $items_values->where('scada_tags_id', $value)->sortBy('created_at')->values();
                    $items_counter[$value] = 0;
                }
                for ($i = 0; $i <= $this->start->diffInMinutes($this->end); $i++) {
                    foreach ($this->tags_choose as $tag) {
                        $item = $items[$tag]->first();

                        if (isset($item->created_at) && $item->created_at->between(Carbon::parse($this->start)->addMinutes($i), Carbon::parse($this->start)->addMinutes($i + 1))) {
                            $current_value[$tag] = $item->value;

                            $items[$tag]->forget($items_counter[$tag]);
                            $items_counter[$tag] = $items_counter[$tag] + 1;
                        } else {
                            if (!empty($last_value[$tag])) {
                                $current_value[$tag] = $last_value[$tag];
                            }
                        }
                        if (!empty($current_value[$tag])) {
                            $data[$tag][] = $current_value[$tag];
                            $label[$tag][] = Carbon::parse(Carbon::parse($this->start)->addMinutes($i))->format('Y.m.d H:i:s');
                            $last_value[$tag] = $current_value[$tag];
                        }
                    }
                }
                // $datasets_name = [];
                // foreach ($this->tags_choose as $value) {
                //     $datasets_name[ScadaTags::find($value)->title] = ScadaTags::find($value)->title;
                // }


                $datasets = new Collection($data);
                $labels = new Collection(array_values($label)[0]);
                return (new ChartComponentData($labels, $datasets));
            } else {
                $datasets = new Collection([[]]);
                $labels = new Collection([]);
                return (new ChartComponentData($labels, $datasets));
            }
        } else {
            $datasets = new Collection([[]]);
            $labels = new Collection([]);
            return (new ChartComponentData($labels, $datasets));
        }
    }
}
