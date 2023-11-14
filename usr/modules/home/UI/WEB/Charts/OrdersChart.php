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

class OrdersChart extends Chart
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
                'height' => 70,
                'toolbar' => [
                    'show' => false
                ],
                'type' => 'bar',
                'stacked' => true
            ],          
            'grid' => [
                'show' => false,
                'padding' => [
                    'left' => 0,
                    'right' => 0,
                    'top' => -15,
                    'bottom' => -15
                ]
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '20%',
                    'startingShape' => 'rounded',
                    'colors' => [
                        'backgroundBarColors' => [$barColor, $barColor, $barColor, $barColor, $barColor],
                        'backgroundBarRadius' => 5
                    ]
                ]
            ],
            'legend' => [
                'show' => false
            ],
            'dataLabels' => [
                'enabled' => false
            ],
            'colors' => [$strokeColor],  
            'xaxis' => [
                'categories' => $data->labels(),
                'labels' => [
                    'show' => false
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
