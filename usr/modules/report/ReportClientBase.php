<?php

namespace Report;

use Boot\System\Http\Request;
use \Report\Support\ReportRangeDateSupport;

class ReportClientBase extends Report{
    static function getTitle(){
        return "Отчёт по клиентской базе";
    }
    
    static function getReportName(){
        return "client_base";
    }

    static function getDescription(){
        return "Отчёт по реализациям и маржинальности клиентов в двух периодах с ответственными менеджерами";
    }

    static function fields(){
        return [
            ["value" => "org", "text" => "Организация", "modal" => \Report\Modal\OrgModal::class,],
            ["value" => "oid", "text" => "Контрагент", "modal" => \Report\Modal\OrganizationsModal::class,],
        ];
    }

    static function query(){
        return \Realization\Model\Realization::select("*", 
        \DB::raw("SUM(total) as sum_total"), 
        \DB::raw("COUNT(*) as realization_count"), 
        \DB::raw("SUM(supply_order_price) as sum_supply_order_price"))->
        where("active", 1)->
        groupBy("oid");
    }

    static function additional_terms(){
        $html = ReportRangeDateSupport::date_range_picker_html();
        return <<<HTML
            <div class="row mt-3">
                {$html}
                <div class="col-4">
                    <label for="customer_turnover_checkbox" style="margin-bottom: 4px;">
                        <input type="checkbox" id="customer_turnover_checkbox" name="customer_turnover_checkbox">
                        Выборка по % оборота клиентов
                    </label>
                    <input type="number" class="form-control" name="customer_turnover" placeholder="Введите процент...">
                </div>
            </div>
HTML;
    }

    static function additional_js(){
        return ReportRangeDateSupport::date_range_picker_js();
    }

    static function additional_css(){
        return ReportRangeDateSupport::date_range_picker_css(); 
    }

    static function distribute($data, $field, $list){
        foreach($data as $item){
            if(!isset($list[ $item->oid ])){
                $list[ $item->oid ] = cc();
                $list[ $item->oid ]->organizations = \Organizations\Model\Organizations::find($item->oid);
            }
            $list[ $item->oid ]->{$field} = $item;
            if(!isset($list[ $item->oid ]->main_data)){
                $list[ $item->oid ]->global = cc();
                $list[ $item->oid ]->global->org = getOrgInfo($item->org, "object");
            }
        }
        return $list;
    }

    static function sortData($data){
        $new_data = [];
        foreach($data as $item){
            $new_data[] = $item;
        }
        for ($j = 0; $j < count($new_data) - 1; $j++){
            for ($i = 0; $i < count($new_data) - $j - 1; $i++){
                if (($new_data[$i]->second_data->sum_total ?? 0) < ($new_data[$i + 1]->second_data->sum_total ?? 0)){
                    $tmp_var = $new_data[$i + 1];
                    $new_data[$i + 1] = $new_data[$i];
                    $new_data[$i] = $tmp_var;
                }
            }
        }
        return $new_data;
    }

    static function middleware_date($date){
        ReportRangeDateSupport::middleware_date($date);
    }

    static function get_data(Request $request){
        $selections = static::get_selections($request);
        $customer_turnover_checkbox = false;
        if($request->has("customer_turnover_checkbox")){
            $customer_turnover_checkbox = true;
            $customer_turnover = $request->get("customer_turnover") ?? 0;
        }

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
        $data = [];
        $data = static::distribute($data_first, "first_data", $data);
        $data = static::distribute($data_second, "second_data", $data);
        foreach($data as $item){
            if(!isset($item->first_data)){
                $item->first_data = null;
            }
            if(!isset($item->second_data)){
                $item->second_data = null;
            }
        }
        $data = static::sortData($data);
        if($customer_turnover_checkbox){
            $data = static::customer_turnover($data, $customer_turnover);
        }
        return [
            "date" => $date,
            "data" => $data,
        ];
    }

    static function customer_turnover($data, $customer_turnover){
        $total = 0;
        foreach($data as $item){
            $total += $item->second_data->sum_total ?? 0;
        }
        $new_total = $total * ($customer_turnover / 100);
        $new_data = [];
        $total = 0;
        foreach($data as $item){
            $total += $item->second_data->sum_total ?? 0;
            if($total <= $new_total){
                $new_data[] = $item;
            } else {
                return $new_data;
            }
        }
        return $new_data;
    }

    static function table_data(Request $request, $config = []){
        $main_data = static::get_data($request);
        $data = $main_data["data"];
        $date = $main_data["date"];
        $table = static::get_table_structure();

        $first_date = $date[0][0]." - ".$date[0][1];
        $second_date = $date[1][0]." - ".$date[1][1];
        
        $table->head = [
            static::element("Организация"), 
            static::element("Контрагент"), 
            static::element("ИНН"),
            static::element("Основной менеджер"),
            static::element("Количество реализаций за {$first_date}"),
            static::element("Сумма реализаций за {$first_date} (с НДС)"),
            static::element("Количество реализаций за {$second_date}"),
            static::element("Сумма реализаций за {$second_date} (с НДС)"),
            static::element("Маржинальность продаж за {$first_date}, %"),
            static::element("Маржинальность продаж за {$second_date}, %"),
            static::element("Тренд"),
            static::element("Статус"),
        ];
        $total = ["realization_count" => [0, 0], "sum_total" => [0, 0], "sum_supply_order_price" => [0,0], "trend" => 0];

        $margin_re = function($sum_total, $sum_supply_order_price){
            return empty($sum_total) ? 0 : ($sum_total - $sum_supply_order_price) / $sum_total * 100;
        };

        foreach($data as $item){
            $realization_count = [
                $item->first_data->realization_count ?? 0,
                $item->second_data->realization_count ?? 0
            ];
            $sum_total = [
                $item->first_data->sum_total ?? 0,
                $item->second_data->sum_total ?? 0,
            ];
            $element = [];
            $element[] = static::element($item->global->org->organizations->name);
            $element[] = static::element($item->organizations->name);
            $element[] = static::element($item->organizations->inn);
            $element[] = static::element(empty($item->organizations->default_responsible) ? "Не указан" : shortUserName($item->organizations->default_responsible));
            $element[] = static::element($realization_count[0], ["align" => "center"]); //количество
            $element[] = static::element($sum_total[0], ["round" => true, "align" => "right"]); //сумма
            $element[] = static::element($realization_count[1], ["align" => "center"]); //количество
            $element[] = static::element($sum_total[1], ["round" => true, "align" => "right"]); //сумма
            $sum_supply_order_price = [
                $item->first_data->sum_supply_order_price ?? 0,
                $item->second_data->sum_supply_order_price ?? 0
            ];
            $margin = [
                empty($sum_total[0]) ? 0 : $margin_re($sum_total[0], $sum_supply_order_price[0]),
                empty($sum_total[1]) ? 0 : $margin_re($sum_total[1], $sum_supply_order_price[1]),
            ];
            $margin_style = [
                $margin[0] >= 0 ? [] : ["color" => "red"],
                $margin[1] >= 0 ? [] : ["color" => "red"],
            ];
            $element[] = static::element($margin[0], ["round" => true, "align" => "center", "style" => $margin_style[0]]); //Маржинальность
            $element[] = static::element($margin[1], ["round" => true, "align" => "center", "style" => $margin_style[1]]); //Маржинальность
            $trend = ($item->second_data->sum_total ?? 0) - ($item->first_data->sum_total ?? 0);
            $element[] = static::element($trend, ["round" => true, "align" => "right", "style" => $trend >= 0 ? [] : ["color" => "red"]]); //Тренд
            $element[] = static::element($item->organizations->client_type_view);
            $table->body[] = $element;

            $total["realization_count"][0] += $realization_count[0];
            $total["realization_count"][1] += $realization_count[1];
            $total["sum_total"][0] += $sum_total[0];
            $total["sum_total"][1] += $sum_total[1];
            $total["sum_supply_order_price"][0] += $sum_supply_order_price[0];
            $total["sum_supply_order_price"][1] += $sum_supply_order_price[1];
            $total["trend"] += $trend;
        }
        $style_total = ["font-weight" => "bold"];
        $trend_style = $total["trend"] >= 0 ? [] : ["color" => "red"];
        array_unshift($table->body, [
            static::element("ИТОГО", ["style" => $style_total]),
            static::element(""),
            static::element(""),
            static::element(""),
            static::element($total["realization_count"][0], ["align" => "center", "style" => $style_total]),
            static::element($total["sum_total"][0], ["round" => true, "align" => "right", "style" => $style_total]),
            static::element($total["realization_count"][1], ["align" => "center", "style" => $style_total]),
            static::element($total["sum_total"][1], ["round" => true, "align" => "right", "style" => $style_total]),
            static::element($margin_re($total["sum_total"][0], $total["sum_supply_order_price"][0]), ["round" => true, "align" => "center", "style" => $style_total]),
            static::element($margin_re($total["sum_total"][1], $total["sum_supply_order_price"][1]), ["round" => true, "align" => "center", "style" => $style_total]),
            static::element($total["trend"], ["round" => true, "align" => "right", "style" => array_merge($style_total, $trend_style)]),
            static::element(""),
        ]);
        return $table;
    }
}