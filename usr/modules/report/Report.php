<?php

namespace Report;

use Boot\System\Http\Request;
use Boot\System\Office\Spreadsheet\SpreadsheetSupport;



abstract class Report{

    abstract static function fields();
    abstract static function query();
    abstract static function table_data(Request $request, Array $config = []);
    abstract static function getTitle();
    abstract static function getReportName();
    abstract static function getDescription();

    static function index(){
        $fields = static::fields();
        display("@report/manager/index", [
            "title" => static::getTitle(),
            "fields" => $fields,
            "modals" => static::get_modals($fields),
            "modals_data" => static::get_modals_data($fields),
            "link" => [
                "view" => router("admin.report.".static::getReportName().".view"),
                "excel" => router("admin.report.".static::getReportName().".excel"),
            ],
            "Report_name" => static::getReportName(),
            "additional_terms" => static::additional_terms(),
            "additional_js" => static::additional_js(),
            "additional_css" => static::additional_css(),
        ]);
    }

    static function get_modals($fields){
        $modals = [];
        foreach($fields as $field){
            $modal = $field["modal"];
            if(is_subclass_of($modal, \Report\Modal\Modal::class) && !isset($modals[ $modal::getName() ])){
                $modals[ $modal::getName() ] = [
                    "template" => $modal::template(),
                    "condition" => [
                        "text" => $modal::condition_text(),
                        "input" => $modal::getCondition(),
                    ],
                ];
            }
        }
        return $modals;
    }

    static function get_modals_data($fields){
        $modals_data = [];
        foreach($fields as $field){
            $modal = $field["modal"];
            if(is_subclass_of($modal, \Report\Modal\Modal::class)){
                $modals_data[ $field["value"] ] = [
                    "name" => $modal::getName(),
                    "type" => $modal::getType(),
                    "element" => $modal::getElement(),
                    "button" => $modal::getButton(),
                    "function" => $modal::getFunction(),
                    "condition" => $modal::getCondition(),
                    "class" => $modal,
                ];
            }
        }
        return $modals_data;
    }

    static function additional_terms(){}
    static function additional_js(){}
    static function additional_css(){}

    static function condition_sql($query, $selection){       
        if($selection->active == "true"){
            if(isset($selection->class) && class_exists($selection->class) && is_subclass_of($selection->class, \Report\Modal\Modal::class)){
                $modal = $selection->class;
                if(method_exists($modal, "final_query")){
                    return $modal::final_query($query, $selection);
                }
                $query = $modal::additional_query($query, $selection);
            }
            return match((int)$selection->condition){
                1 => $query->where($selection->field, "=", $selection->value),
                2 => $query->where($selection->field, "!=", $selection->value),
                3 => $query->where($selection->field, "<", $selection->value),
                4 => $query->where($selection->field, "<=", $selection->value),
                5 => $query->where($selection->field, ">", $selection->value),
                6 => $query->where($selection->field, ">=", $selection->value),
                7 => $query->where($selection->field, "LIKE", "%{$selection->value}%"),
                8 => $query->where($selection->field, "NOT LIKE", "%{$selection->value}%"),
                9 => $query->where($selection->field, "LIKE", "{$selection->value}%"),
                10 => $query->where($selection->field, "NOT LIKE", "{$selection->value}%"),
                11 => $query->whereNotNull($selection->field),
                12 => $query->whereNull($selection->field),
                13 => $query->whereIn($selection->field, $selection->value),
            };
        }
        return $query;
    }

    static function nf($value){
        return number_format($value, 2, ",", "&nbsp;");
    }

    static function element($value, Array $request = []):\stdClass{
        $element = new \stdClass;
        $element->round = isset($request["round"]) && $request["round"] ? true : false;
        $element->align = $request["align"] ?? null;
        $element->value = $value;
        $element->style = $request["style"] ?? [];
        return $element;
    }

    static function get_selections(Request $request):Array{
        return json_decode($request->request->get("selections"));
    }

    static function get_data(Request $request){
        $selections = static::get_selections($request);
        
        $query = static::query();

        foreach($selections as $selection){
            $query = static::condition_sql($query, $selection);
        }
        
        return $query->get();
    }

    static function get_table_structure():\stdClass{
        $table = new \stdClass;
        $table->head = [];
        $table->body = [];
        return $table;
    }

    static function view(Request $request){
        $table = static::table_data($request, ["excel" => false,]);
        $view = [];
        $view[] = '<table class="table table-hover"><thead id="main_thead" class="table-light"><tr>';
        foreach($table->head as $item){
            $view[] = static::prepare_view_item("th", "head", $item);
        }
        $view[] = '</thead><tbody>';
        foreach($table->body as $row){
            $view[] = "<tr>";
            foreach($row as $item){
                $view[] = static::prepare_view_item("td", "body", $item);
            }
            $view[] = "</tr>";
        }
        $view[] = "</tbody></table>";
        echo json_encode(["template" => implode("", $view)], JSON_UNESCAPED_UNICODE);
    }

    static function prepare_view_item($tag, $place, $item){
        $align = empty($item->align) ? "" : "align=\"{$item->align}\"";
        $style = [];
        foreach($item->style as $k => $s){
            $style[] = "{$k}: {$s}";
        }
        $style = count($style) > 0 ? "style=\"".implode("; ", $style)."\"" : "";
        switch($place){
            default:
                $value = $item->round ? static::nf($item->value) : $item->value;
            break;
            case "head":
                $value = $item->value;
            break;
        }
        
        return "<{$tag} {$align} {$style}>{$value}</{$tag}>";
    }

    static function excel_create(Request $request){
        $table = static::table_data($request, ["excel" => true]);
        $head = [];
        foreach($table->head as $item){
            $head[] = $item->value;
        }
        $data = [];
        foreach($table->body as $row){
            $element = [];
            foreach($row as $item){
                $value = $item->round ? round($item->value, 2) : $item->value;
                $element[] = $value;
            }
            $data[] = $element;
        }
        $link = SpreadsheetSupport::data_to_excel_file(["head" => $head, "data" => $data, "filename" => ROOT."usr/modules/report/files/".time()]);
        return $link;
    }

    static function excel(Request $request){
        try {
            $link = static::excel_create($request);
            echo json_encode(["link" => $link], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            echo static::error($e);
        }
    }

    static function error($e){
        return json_encode(["error" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
}