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

use Darkeum\Charts\Classes\Apex\Chart;
use Darkeum\Charts\Support\Livewire\ChartComponentData;

class ProfitChart extends Chart
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

        $strokeColor = '#00cfe8';
        $trackBgColor = '#EBEBEB';

        $this->loader(false);
        $this->labels($data->labels());
        $this->options([
            'chart' => [
                'height' => 70,
                'toolbar' => [
                    'show' => false
                ],
                'type' => 'line',
                'zoom' => [
                    'enabled' => false
                ]
            ],
            'grid' => [
                'borderColor' => $trackBgColor,
                'strokeDashArray' => 5,
                'xaxis' => [
                    'lines' => [
                        'show' => true
                    ]
                ],
                'yaxis' => [
                    'lines' => [
                        'show' => false
                    ]
                ],
                'padding' => [
                    'top' => -30,
                    'bottom' => -10
                ]
            ],
            'stroke' => [
                'width' => 3
            ],
            'colors' => [$strokeColor],
            'markers' => [
                'size' => 2,
                'colors' => $strokeColor,
                'strokeColors' =>  $strokeColor,
                'strokeWidth' => 2,
                'strokeOpacity' => 1,
                'strokeDashArray' => 0,
                'fillOpacity' => 1,
                'discrete' => [
                    [
                        'seriesIndex' => 0,
                        'dataPointIndex' => 5,
                        'fillColor' => '#ffffff',
                        'strokeColor' =>  $strokeColor,
                        'size' => 5
                    ]
                ],
                'shape' => 'circle',
                'radius' => 2,
                'hover' => [
                    'size' => 3
                ]
            ],
            'xaxis' => [
                'categories' => $data->labels(),
                'labels' => [
                    'show' => true,
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
            'yaxis' => [
                'show' => false
            ]
        ]);
        $this->type('line');
        foreach ($data->datasets() as $key => $value) {
            $this->dataset($data->datasets_name[$key] ?? $key, "line", $value);
        }
    }
}
