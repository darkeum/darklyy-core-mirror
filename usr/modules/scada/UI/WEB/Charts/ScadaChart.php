<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\WEB\Charts;

use Balping\JsonRaw\Raw;
use Darkeum\Charts\Classes\Apex\Chart;
use Darkeum\Charts\Support\Livewire\ChartComponentData;

class ScadaChart extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return void
     */
    /**
     * WanSpeedTestsChart constructor.
     *
     * @param \Darkeum\Charts\Support\Livewire\ChartComponentData $data
     */
    public function __construct(ChartComponentData $data)
    {
        parent::__construct();

        $barColor = '#f3f3f3';
        $strokeColor = '#FF9F43';

        $this->loader(false);
        $this->labels($data->labels());
        $this->options([
            'chart' => [
                'height' => 350,
                'stacked' => false,

                'zoom' => [
                    'type' => 'x',
                    'enabled' => true,
                    'autoScaleYaxis' => true,
                ],
                'toolbar' => [
                    'autoSelected' => 'zoom',
                ],
                'colors' => ['#546E7A'],
                'stroke' => [
                    'width' => 3
                ],
                'dataLabels' => [
                    'enabled' => false,
                ],
                'fill' => [
                    'opacity' => 1,
                ],
                'markers' => [
                    'size' => 0,
                ],
                'xaxis' => [
                    'type' => 'datetime',
                ],



            ],

        ]);
        $this->type('area');
        // dd($data->datasets());
        foreach ($data->datasets() as $key => $value) {

            $this->dataset($key, "area", $value);
        }
    }
}
