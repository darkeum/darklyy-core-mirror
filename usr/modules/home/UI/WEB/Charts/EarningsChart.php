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
use Balping\JsonRaw\Raw;
use Balping\JsonRaw\Encoder;

class EarningsChart extends Chart
{
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
        $earningsStrokeColor = [];
        $earningsStrokeColor[] = '#28c76f88';
        $earningsStrokeColor[] = '#ff9f4388';
        $earningsStrokeColor[] = '#887ef277';
        $earningsStrokeColor[] = '#fa381d66';
        $earningsStrokeColor[] = '#28c76f55';
        $earningsStrokeColor[] = '#28c76f44';
        $earningsStrokeColor[] = '#28c76f33';

        $this->loader(false);
        $this->labels($data->labels());
        $this->options([
            'chart' => [
                'height' => 160,
                'type' => 'donut',
                'toolbar' => [
                    'show' => false
                ],
            ],
            'dataLabels' => [
                'enabled' => false
            ],
            'legend' => [
                'show' => false
            ],
            'labels' => $data->labels(),
            // 'dataLabels' => [
            //     'formatter' => new Raw("function (val) {
            //         return (val).toFixed(2) + '%';
            //     }"),
            // ],

            'tooltip' => [
                'custom' => new Raw("function (data) {
                  return (
                    '<div class=\"px-1 py-50\">' +
                    '<span>' +
                    (data.series[data.seriesIndex]-10).toFixed(2) +
                    '%</span>' +
                    '</div>'
                  );
                }"),
            ],
            // 'comparedResult' => [2, -3, 8, 15],
            'stroke' => [
                'width' => 0
            ],
            'colors' => $earningsStrokeColor,
            'grid' => [
                'padding' => [
                    'right' => -20,
                    'left' => -20,
                    'bottom' => -8
                ]
            ],
            'plotOptions' => [
                'pie' => [
                    'startAngle' => -10,
                    'donut' => [
                        'labels' => [
                            'show' => true,
                            'name' => [
                                'offsetY' => 15,
                            ],
                            'value' => [
                                'offsetY' => -15,
                                'formatter' => new Raw("function(val) {
                                    return (val-10).toFixed(2) + '%';
                                }")
                            ],
                            'total' => [
                                'show' => true,
                                'offsetY' => 15,
                                'label' => $data->labels()[0] ?? 'Null',
                                'formatter' => new Raw("function(w) {
                                    return '" . ($data->datasets()[0][0] ?? 10 - 10) . "%';
                                }")
                            ],
                        ],
                    ],
                ],
            ],
            'responsive' => [
                0 => [
                    'breakpoint' => 1325,
                    'options' => [
                        'chart' => [
                            'height' => 100,
                        ],
                    ],
                ],
                1 => [
                    'breakpoint' => 1200,
                    'options' => [
                        'chart' => [
                            'height' => 120,
                        ],
                    ],
                ],
                2 => [
                    'breakpoint' => 1045,
                    'options' => [
                        'chart' => [
                            'height' => 100,
                        ],
                    ],
                ],
                3 => [
                    'breakpoint' => 992,
                    'options' => [
                        'chart' => [
                            'height' => 120,
                        ],
                    ],
                ],
            ],


        ]);
        $this->type('donut');
        foreach ($data->datasets() as $key => $value) {
            $this->dataset($data->datasets_name[$key] ?? $key, "line", $value);
        }
    }
}
