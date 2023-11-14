<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Home\UI\WEB\Charts;

use Balping\JsonRaw\Raw;
use Darkeum\Charts\Classes\Apex\Chart;
use Darkeum\Charts\Support\Livewire\ChartComponentData;

class BudgetChart extends Chart
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

        $budgetStrokeColor2 = '#dcdae3';

        $this->loader(false);
        $this->labels($data->labels());
        $this->options([
            'chart' => [
                'height' => 80,
                'toolbar' => [
                    'show' => false
                ],
                'zoom' => [
                    'enabled' => false
                ],
                'type' => 'line',
                'sparkline' => [
                    'enabled' => false
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'dashArray' => [
                    0 => 0,
                    1 => 5,
                ],
                'width' => [
                    0 => 2,
                ],
            ],
            'colors' => [new Raw("window.colors.solid.primary"), $budgetStrokeColor2],
            'tooltip' => [
                'enabled' => false
            ],
            'yaxis' => [
                'show' => false
            ],
            'xaxis' => [
                'labels' => [
                    'show' => false,
                    'style' => [
                        'fontSize' => '0px'
                    ]
                ],
                'axisBorder' => [
                    'show' => false
                ],
                'axisTicks' => [
                    'show' => false
                ]
            ],
            'legend' => [
                'show' => false
            ],
            'grid' => [
                'xaxis' => [
                    'lines' => [
                        'show' => false
                    ]
                ],
                'yaxis' => [
                    'lines' => [
                        'show' => false
                    ]
                ],
            ],


        ]);
        $this->type('line');
        foreach ($data->datasets() as $key => $value) {
            $this->dataset("", "line", $value);
        }
    }
}
