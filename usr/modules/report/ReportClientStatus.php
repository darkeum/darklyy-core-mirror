<?php

namespace Report;

use Boot\System\Http\Request;

use \Report\Support\ReportRangeDateSupport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;

class ReportClientStatus extends Report{
    static function getTitle(){
        return "Отчёт по статусам клиентов";
    }
    
    static function getReportName(){
        return "client_status";
    }

    static function getDescription(){
        return "Отчёт по статусам клиентов в двух периодах";
    }

    static function fields(){
        return [
            ["value" => "org", "text" => "Организация", "modal" => \Report\Modal\OrgModal::class,],
            ["value" => "oid", "text" => "Контрагент", "modal" => \Report\Modal\OrganizationsModal::class,],
        ];
    }

    static function query(){
        return \DB::table("realization as r")->select("o.client_type",
        \DB::raw("SUM(JMY_r.total) as sum_total"), 
        \DB::raw("COUNT(DISTINCT JMY_r.oid) as realization_count"))->
        leftJoin("organizations as o", "r.oid", "=", "o.id")->
        where("r.active", 1)->
        groupBy("o.client_type");
    }

    static function additional_terms(){
        $html = ReportRangeDateSupport::date_range_picker_html();
        return <<<HTML
            <div class="row mt-3">
                {$html}
            </div>
HTML;
    }

    static function additional_js(){
        return ReportRangeDateSupport::date_range_picker_js();
    }

    static function additional_css(){
        return ReportRangeDateSupport::date_range_picker_css(); 
    }

    static function middleware_date($date){
        ReportRangeDateSupport::middleware_date($date);
    }

    static function get_data(Request $request){
        $selections = static::get_selections($request);

        $data_first_query = static::query();
        $data_second_query = static::query();

        foreach($selections as $selection){
            $data_first_query = static::condition_sql($data_first_query, $selection);
            $data_second_query = static::condition_sql($data_second_query, $selection);
        }

        $date = $request->get("date");
        static::middleware_date($date);
        $p_date = ReportRangeDateSupport::parse_date($date);

        $data_first_query->whereDate("date", ">=", $p_date[0][0])->whereDate("date", "<=", $p_date[0][1]);
        $data_second_query->whereDate("date", ">=", $p_date[1][0])->whereDate("date", "<=", $p_date[1][1]);

        $data_first = $data_first_query->get();
        $data_second = $data_second_query->get();
        $data = \Organizations\Model\Organizations::getClientType();
        foreach($data as $key => $item){
            $data[ $key ] = cc();
            $data[ $key ]->first = null;
            $data[ $key ]->second = null;
        }
        foreach($data_first as $item){
            $data[ $item->client_type ]->first = $item;
        }
        foreach($data_second as $item){
            $data[ $item->client_type ]->second = $item;
        }
        return [
            "date" => $date,
            "data" => $data,
        ];
    }

    static function table_data(Request $request, $config = []){
        $main_data = static::get_data($request);
        $data = $main_data["data"];
        $date = $main_data["date"];
        $table = static::get_table_structure();
        $client_type = \Organizations\Model\Organizations::getClientType();

        $first_date = $date[0][0]." - ".$date[0][1];
        $second_date = $date[1][0]." - ".$date[1][1];
        $style = [
            [],
            ["align" => "right"],
            ["round" => true, "align" => "center"],
            ["align" => "right"],
            ["round" => true, "align" => "center"],
        ];
        $table->head = [
            static::element("Статус", $style[0]),
            static::element("Кол-во клиентов, {$first_date}", $style[1]),
            static::element("Сумма {$first_date}, руб.", $style[2]),
            static::element("Кол-во клиентов, {$second_date}", $style[3]),
            static::element("Сумма {$second_date}, руб.", $style[4]),
        ];
        foreach($data as $key => $item){
            $element = [];
            $element[] = static::element($client_type[ $key ], $style[0]);
            $element[] = static::element($item->first->realization_count ?? 0, $style[1]);
            $element[] = static::element($item->first->sum_total ?? 0, $style[2]);
            $element[] = static::element($item->second->realization_count ?? 0, $style[3]);
            $element[] = static::element($item->second->sum_total ?? 0, $style[4]);
            $table->body[] = $element;
        }
        return $table;
    }

    static function excel_create(Request $request){
        $link = parent::excel_create($request);
        $excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($link);
        $excel->setActiveSheetIndex(0);
        $worksheet  = $excel->getActiveSheet();
        $start = 2;
        $end_type = count(\Organizations\Model\Organizations::getClientType()) - 1;
        $end = $start + $end_type;

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$1', null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$E$1', null, 1),
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$'.$start.':$A$'.$end, null, 4),
        ];
        
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$'.$start.':$C$'.$end, null, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$E$'.$start.':$E$'.$end, null, 4),
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART, // plotType
            DataSeries::GROUPING_STANDARD, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues        // plotValues
        );
        // Set additional dataseries parameters
        //     Make it a vertical column rather than a horizontal bar graph
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);
        // Set the chart legend
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);

        $title = new Title('Объемы продаж по типам клиентов');

        // Create the chart
        $chart = new Chart(
            'chart', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('B'.($end + 3));
        $chart->setBottomRightPosition('Q'.($end + 3 + 18));

        // Add the chart to the worksheet
        $worksheet->addChart($chart);

        // Save Excel 2007 file
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        $writer->setIncludeCharts(true);
        $writer->save($link);
        return $link;
    }
}