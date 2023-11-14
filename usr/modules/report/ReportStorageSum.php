<?php

namespace Report;

use Boot\System\Http\Request;


class ReportStorageSum extends Report{
    static function getTitle(){
        return "Отчёт по сумме на складах";
    }
    
    static function getReportName(){
        return "storage_sum";
    }

    static function getDescription(){
        return "Отчёт по сумме на складах (текущая закупочная цена)";
    }

    static function fields(){
        return [
            ["value" => "stock", "text" => "Склад", "modal" => \Report\Modal\StockModal::class,],
        ];
    }

    static function query(){
        return \Stocks\Models\StockDetail::with("stock_")->where("sum", ">", 0);
    }

    static function table_data(Request $request, $config = []){
        $data = static::get_data($request);
        $table = static::get_table_structure();

        $table->head = [
            static::element("ID склада"),
            static::element("Наименование склада"),
            static::element("Сумма"),
        ];

        $storages = [];
        foreach($data as $item){
            if(!isset($storages[ $item->stock ])){
                $storages[ $item->stock ] = cc();
                $storages[ $item->stock ]->stock_id = $item->stock_->id;
                $storages[ $item->stock ]->name = $item->stock_->name;
                $storages[ $item->stock ]->sum = 0;
            }
            $price = \Price::getPrice($item->cid);
            $storages[ $item->stock ]->sum += $item->sum * $price->price_purchase_;
        }
        foreach($storages as $storage){
            $table->body[] = [
                static::element($storage->stock_id),
                static::element($storage->name),
                static::element($storage->sum, ["round" => true]),
            ];
        }

        return $table;
    }
}