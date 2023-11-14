<?php

namespace Report;

use Boot\System\Http\Request;


class ReportGoodsMovement extends Report{
    static function getTitle(){
        return "Отчёт по движению товара";
    }

    static function getReportName(){
        return "goods_movement";
    }

    static function getDescription(){
        return "Отчёт по движению товара";
    }

    static function fields(){
        return [
            ["value" => "st.stock_id", "text" => "Склад", "modal" => \Report\Modal\StockModal::class,],
            ["value" => "st.cid", "text" => "Номенклатура", "modal" => \Report\Modal\GoodsModal::class,],
            ["value" => "c.category", "text" => "Категория", "modal" => \Report\Modal\CategoryModal::class,],
            ["value" => "st.date", "text" => "Дата", "modal" => \Report\Modal\DateModal::class,],
            ["value" => "c.hidden", "text" => "Скрытность", "modal" => \Report\Modal\HiddenModal::class,],
        ];
    }

    static function query(){
        return \DB::table("stock_transaction as st")->
        leftJoin("catalog as c", "c.id", "=", "st.cid")->
        leftJoin("stock as s", "s.id", "=", "st.stock_id")->
        select("st.*", "s.name as s_name", "c.title as c_title", "c.sku as c_sku", "c.id as c_id", "c.unit as c_unit")->
        orderBy("st.id");
    }

    static function module_link($module){
        $link = [
            "catalog" => "",
            "catalog_v2" => "",
            "correct" => "/administration/module/realization/correct_detail/",
            "merging" => "/administration/module/merging/detail/",
            "realization" => "/administration/module/realization/order/",
            "receipts" => "/administration/module/supply/receipts_detail/",
            "storage" => "/administration/module/storage/detail/",
            "storage_transfer" => "/administration/module/storage/transfer/detail/",
            "transform" => "",
        ];

        if(empty($module)){
            return "";
        }
        return array_key_exists($module, $link) ? $link[ $module ] : "";
    }

    static function module_link_view($text, $module, $id){
        $link = static::module_link($module, $id);
        if(empty($link)){
            return $text;
        } else {
            return '<a href="'.$link.$id.'" target="_blank" style="font-weight: bold; color: black;">'.$text.'</a>';
        }
    }

    static function table_data(Request $request, $config = []){
        $data = static::get_data($request);
        $table = static::get_table_structure();

        $table->head = [
            static::element("CID"),
            static::element("Артикул"),
            static::element("Номенклатура"),
            static::element("Ед. изм."),
            static::element(""),
            static::element(""),
            static::element(""),
            static::element("Конечный остаток"),
            static::element(""),
            static::element(""),
        ];
        $cidArray = [];
        foreach($data as $item){
            switch($item->sign){
                case "set":
					$sign_rus = "Установка значения";
                    $icon = '<i class="fas fa-wrench text-primary fs26" title="'.$sign_rus.'"></i>';
                break;
                case "plus":
					$sign_rus = "+";
                    $icon = '<i class="fas fa-plus text-success fs26" title="Добавление значения"></i>';
                break;
                case "minus":
					$sign_rus = "-";
                    $icon = '<i class="fas fa-minus text-danger fs26" title="Вычет значения"></i>';
                break;
            }
            if(!array_key_exists($item->cid, $cidArray)){
                $cidArray[$item->cid]["header"] = [
                  "id" => $item->c_id,
                  "sku" => $item->c_sku,
                  "title" => $item->c_title,
                  "unit" => $item->c_unit,
                ];
                $idMax[$item->cid] = -1;
                $cidArray[$item->cid]["header"]["sum"] = 0;
              }

              $cidArray[$item->cid]["body"][] = [
                  "comment" => $item->comment,
                  "icon" => $icon,
                  "sign" => $item->sign,
                  "sign_rus" => $sign_rus,
                  "count" => $item->count,
                  "sum" => $item->sum,
                  "uid" => $item->uid,
                  "date" => $item->date,
                  "pid" => $item->pid,
                  "module" => $item->module,
                  "stock_id" => $item->stock_id,
                  "s_name" => $item->s_name,
              ];

              if($idMax[$item->cid] < $item->id){
                  $idMax[$item->cid] = $item->id;
                  $cidArray[$item->cid]["header"]["sum"] = $item->sum;
              }
        }
        foreach($cidArray as $val){
            $table->body[] = [
                static::element($val["header"]["id"]),
                static::element($val["header"]["sku"]),
                static::element($val["header"]["title"]),
                static::element(get_unit('data', $val["header"]["unit"], 'name')),
                static::element(""),
                static::element(""),
                static::element(""),
                static::element($val["header"]["sum"]),
                static::element(""),
                static::element(""),
            ];
            foreach($val["body"] as $v){
                $table->body[] = [
                    static::element(""),
                    static::element(""),
                    static::element(
                        $config["excel"]
                        ? $v["comment"]
                        : static::module_link_view($v["comment"].' (id: '.$v["pid"].'; module: '.$v["module"].';)', $v["module"], $v["pid"])
                    ),
                    static::element(
                        $config["excel"]
                        ? $v["sign_rus"]
                        : $v["icon"]
                    ),
                    static::element($v["s_name"]),
                    static::element(""),
                    static::element($v["count"]),
                    static::element($v["sum"]),
                    static::element(shortUserName($v["uid"])),
                    static::element(date("d.m.Y H:i:s", strtotime($v["date"]))),
                ];
            }
        }

        return $table;
    }
}