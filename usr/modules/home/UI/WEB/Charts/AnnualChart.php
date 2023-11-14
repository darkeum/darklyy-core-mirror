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

class AnnualChart extends Chart
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

        // config('home-config-dashboard.charts.textHeadingColor');

        $textHeadingColor = '#5e5873';
        $strokeColor = '#ebe9f1';
        $labelColor = '#e7eef7';
        $avgSessionStrokeColor2 = '#ebf0f7';
        $budgetStrokeColor2 = '#dcdae3';
        $goalStrokeColor2 = '#51e5a8';
        $revenueStrokeColor2 = '#d0ccff';
        $textMutedColor = '#b9b9c3';
        $salesStrokeColor2 = '#df87f2';
        $white = '#fff';
        $earningsStrokeColor2 = '#28c76f66';
        $earningsStrokeColor3 = '#28c76f33';

        $count=count($data->datasets());

        $dashArray=array();
        $width=array();
        $colors=array();
        $gradientToColors=array();
        $stops=array();


        for($i=1;$i<=$count;$i++)
        {
            
            if($i==$count)
            {
                $dashArray[]= 0;
                $width[]=  4;
                $colors[]= $revenueStrokeColor2;
                $gradientToColors[]= '#fa381d';
                $stops[]= 100;
            }
            else
            {
                $dashArray[]= 12;
                $width[]= 3;
                $colors[]= $strokeColor;
                $gradientToColors[]= $strokeColor;
                if($i==1)
                {
                    $stops[] = 0;
                }
                else
                {
                    $stops[]= 100;
                }
                
            }

        }    
        // dd($width);

        $this->loader(false);
        $this->labels($data->labels());
        $this->options([
            'chart' => [
                'height' => 300,
                'toolbar' => [
                    'show' => false
                ],
                'zoom' => [
                    'enabled' => false
                ],
                'type' => 'line',
                'offsetX' => -10
            ],
            'stroke' => [
                'curve' => 'smooth',
                'dashArray' => $dashArray,
                'width' => $width
            ],
            'grid' => [
                'borderColor' => $labelColor
            ],
            'legend' => [
                'show' => false
            ],
            'colors' => $colors,
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'inverseColors' => false,
                    'gradientToColors' => $gradientToColors,
                    'shadeIntensity' => 1,
                    'type' => 'horizontal',
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => $stops
                ]
            ],
            'markers' => [
                'size' => 0,
                'hover' => [
                    'size' => 5
                ]
            ],
            'xaxis' => [
                'labels' => [
                    'style' => [
                        'colors' => $textMutedColor,
                        'fontSize' => '1rem',
                    ],
                ],
                'axisTicks' => [
                    'show' => false,
                ],
                'categories' => $data->labels(),
                'axisBorder' => [
                    'show' => false,
                ],
                'tickPlacement' => 'on',
            ],
            'yaxis' => [
                'tickAmount' => 5,
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
            'tooltip' => [
                'x' => [
                    'show' => false,
                ],
            ],
        ]);
        $this->type('line');
        foreach ($data->datasets() as $key => $value) {
            $this->dataset("Текст", "line", $value);
        }
    }
}
