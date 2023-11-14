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

class RevenueChart extends Chart
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

        $textMutedColor = '#b9b9c3';

        $this->loader(false);
        $this->labels($data->labels());
        $this->options([
            'chart' => [
                'height' => 260,
                'toolbar' => [
                    'show' => false
                ],
                'type' => 'bar',
                'stacked' => true,
            ],
            'plotOptions' => [
                'bar' => [
                    'columnWidth' => '17%',
                    'endingShape' => 'rounded',
                ],
                'distributed' => true,
            ],
            'colors' => [new Raw("window.colors.solid.primary"), new Raw("window.colors.solid.warning")],
            'dataLabels' => [
                'enabled' => false,
            ],
            'legend' => [
                'show' => false,
            ],
            'grid' => [
                'padding' => [
                    'top' => -20,
                    'bottom' => -10,
                ],
                'yaxis' => [
                    'lines' => [
                        'show' => false,
                    ],
                ],
            ],
            'xaxis' => [
                'categories' => $data->labels(),
                'labels' => [
                    'style' => [
                        'colors' => $textMutedColor,
                        'fontSize' => '0.86rem',
                    ],
                ],
                'axisTicks' => [
                    'show' => false,
                ],
                'axisBorder' => [
                    'show' => false,
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'colors' => $textMutedColor,
                        'fontSize' => '0.86rem',
                    ],
                    'formatter' => new Raw("function (val) {
                        return (val / 1000000).toFixed(2) + ' млн';
                      }"),
                ],

            ],


        ]);
        $this->type('line');
        foreach ($data->datasets() as $key => $value) {
            $this->dataset($data->datasets_name[$key], "line", $value);
        }
    }
}
