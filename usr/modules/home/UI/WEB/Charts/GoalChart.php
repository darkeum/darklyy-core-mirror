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

class GoalChart extends Chart
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

        $strokeColor = '#ebe9f1';
        $goalStrokeColor2 = '#51e5a8';
        $textHeadingColor = '#5e5873';

        $this->loader(false);
        $this->labels($data->labels());
        $this->options([
            'chart' => [
                'height' => 245,
                'type' => 'radialBar',
                'sparkline' => [
                    'enabled' => true
                ],
                'dropShadow' => [
                    'enabled' => true,
                    'blur' => 3,
                    'left' => 1,
                    'top' => 1,
                    'opacity' => 0.1
                ]
            ],
            'colors' => [$goalStrokeColor2],
            'plotOptions' => [
                'radialBar' => [
                    'offsetY' => -10,
                    'startAngle' => -150,
                    'endAngle' => 150,
                    'hollow' => [
                        'size' => '77%',
                    ],
                    'track' => [
                        'background' => $strokeColor,
                        'strokeWidth' => '50%',
                    ],
                    'dataLabels' => [
                        'name' => [
                            'show' => false,
                        ],
                        'value' => [
                            'color' => $textHeadingColor,
                            'fontSize' => '2.86rem',
                            'fontWeight' => '600',
                        ],
                    ],
                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                  'shade' => 'dark',
                  'type' => 'horizontal',
                  'shadeIntensity' => 0.5,
                  'gradientToColors' => [new Raw('window.colors.solid.success')],
                  'inverseColors' => true,
                  'opacityFrom' => 1,
                  'opacityTo' => 1,
                  'stops' => [
                    0 => 0,
                    1 => 100,
                  ],
                ],
              ],
              'series' => [
                0 => 83,
              ],
              'stroke' => [
                'lineCap' => 'round',
              ],
              'grid' => [
                'padding' => [
                  'bottom' => 30,
                ],
              ],
        ]);
        $this->type('radialBar');
        foreach ($data->datasets() as $key => $value) {
            $this->dataset($data->datasets_name[$key] ?? $key, "line", $value);
        }
    }
}
