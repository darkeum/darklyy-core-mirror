<?php

namespace Report;

use Boot\System\Http\Request;


class ReportGoodsMovementNoTrans extends Report{
    static function getTitle(){
        return "Отчёт по фактическим остаткам";
    }
    
    static function getReportName(){
        return "goods_movement_no_trans";
    }

    static function getDescription(){
        return "Отчёт по фактическим остаткам товаров на складе";
    }

    static function fields(){
        return [
            ["value" => "sd.stock", "text" => "Склад", "modal" => \Report\Modal\StockModal::class,],
            ["value" => "sd.cid", "text" => "Номенклатура", "modal" => \Report\Modal\GoodsModal::class,],
            ["value" => "c.category", "text" => "Категория", "modal" => \Report\Modal\CategoryModal::class,],
            ["value" => "c.hidden", "text" => "Скрытность", "modal" => \Report\Modal\HiddenModal::class,],
        ];
    }

    static function query(){
        return \DB::table("catalog as c")->
        leftJoin("stock_detail as sd", "c.id", "=", "sd.cid")->
        select("c.id", "c.sku", "c.title", "c.unit", "c.category", \DB::raw("SUM(JMY_sd.sum) as sum"))->
        where("sd.sum", ">", 0)->
        groupBy("c.id");
    }

    static function table_data(Request $request, $config = []){
        $data = static::get_data($request);
        $table = static::get_table_structure();
        
        $table->head = [
            static::element("ID"),
            static::element("Артикул"),
            static::element("Наименование"),
            static::element("Ед. изм."),
            static::element("Количество"),
            static::element("Категория"),
        ];

        foreach($data as $item){
            $category = new \Boot\System\Categories\Categories;
            $category_element = $category->getCategory(str_replace(",", "", $item->category));
            $table->body[] = [
                static::element($item->id),
                static::element($item->sku),
                static::element($item->title),
                static::element(get_unit('data', $item->unit, 'name')),
                static::element($item->sum),
                static::element($category_element["name"] ?? ''),
            ];
        }

        return $table;
    }
}