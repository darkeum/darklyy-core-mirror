<?php

namespace Report;

use Boot\System\Http\Request;


class ReportRealization extends ReportRealizationGoods{
    static function getTitle(){
        return "Отчёт по реализации";
    }
    
    static function getReportName(){
        return "realization";
    }

    static function getDescription(){
        return "Отчёт по реализации";
    }

    static function table_data(Request $request, $config = []){
        $data = static::get_data($request);
        $table = static::get_table_structure();
        
        $table->head = [
            static::element("Контрагент"),
            static::element("Количество заказов"),
            static::element("Итого (руб.)"),
        ];

        $cidArray = [];
        $id_array = [];
        foreach($data as $r){
            if(!in_array($r->rid, $id_array)){
                if(array_key_exists($r->r_oid, $cidArray)){
                    $count = $cidArray[$r->r_oid]["count"];
                    $count++;
                    $cidArray[$r->r_oid]["count"] = $count;
    
                    $total = $cidArray[$r->r_oid]["total"];
                    $total += $r->r_total;
                    $cidArray[$r->r_oid]["total"] = $total;
                } else {
                    $cidArray[$r->r_oid] = [
                        "oid" => $r->o_name,
                        "count" => 1,
                        "total" => $r->r_total,
                    ];
                }
                $id_array[] = $r->rid;
            }
        }

        foreach($cidArray as $item){
            $table->body[] = [
                static::element($item["oid"]),
                static::element($item["count"]),
                static::element($config["excel"] ? $item["total"] : static::nf($item["total"])),
            ];
        }

        return $table;
    }
}