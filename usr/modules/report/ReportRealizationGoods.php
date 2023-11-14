<?php

namespace Report;

use Sales\Models\Sales;
use Boot\System\Http\Request;


class ReportRealizationGoods extends Report{
    static function getTitle(){
        return "Отчёт по реализации товаров (подробный)";
    }
    
    static function getReportName(){
        return "realization_goods";
    }

    static function getDescription(){
        return "Отчёт по реализации товаров (подробный)";
    }

    static function fields(){
        return [
            ["value" => "c.category", "text" => "Категория", "modal" => \Report\Modal\CategoryModal::class,],
            ["value" => "r.uid", "text" => "Пользователь", "modal" => \Report\Modal\UserModal::class,],
            ["value" => "r.oid", "text" => "Контрагент", "modal" => \Report\Modal\OrganizationsModal::class,],
            ["value" => "r.org", "text" => "Организация", "modal" => \Report\Modal\OrgModal::class,],
            ["value" => "r.date", "text" => "Дата", "modal" => \Report\Modal\DateModal::class,],
            ["value" => "rd.cid", "text" => "Номенклатура", "modal" => \Report\Modal\GoodsModal::class,],
            ["value" => "r.stock_id", "text" => "Склад", "modal" => \Report\Modal\StockModal::class,],
            ["value" => "s.sales_type", "text" => "Тип продажи", "modal" => \Report\Modal\SalesTypeModal::class,],
        ];
    }

    static function query(){
        return \DB::table("realization_detail as rd")->
        leftJoin("realization as r", "r.id", "=", "rd.rid")->
        leftJoin("catalog as c", "c.id", "=", "rd.cid")->
        leftJoin("catalog_manufacture as cm", "cm.id", "=", "c.man")->
        leftJoin("organizations as o", "o.id", "=", "r.oid")->
        leftJoin("sales as s", "s.id", "=", "r.sales_id")->
        leftJoin("object as obj", "obj.id", "=", "r.object")->
        select("rd.*", "cm.name as cm_name", "o.inn as o_inn", "o.name as o_name", "s.numb as s_numb", "s.sales_type as s_sales_type", "s.date as s_date", "r.numb as r_numb", "r.date as r_date", "obj.name as obj_name", "r.total as r_total", "c.category as c_category", "r.uid as r_uid", "r.oid as r_oid")
        ->where("r.active", 1);
    }

    static function table_data(Request $request, $config = []){
        $data = static::get_data($request);
        $table = static::get_table_structure();

        $categories = \Catalog_v2\Model\Categories::where("module", "catalog_v2")->get()->toArray();
        $new_categories = [];
        foreach($categories as $category){
            $new_categories[ $category["id"] ] = $category;
        }
        
        $table->head = [
            static::element("ID realization detail"),
            static::element("ID товара"),
            static::element("Артикул товара"),
            static::element("Наименование товара"),
            static::element("Производитель"),
            static::element("ИНН контрагента"),
            static::element("Контрагент"),
            static::element("Номер продажи"),
            static::element("Тип продажи"),
            static::element("Дата продажи"),
            static::element("Номер реализации"),
            static::element("Дата реализации"),
            static::element("Объект реализации"),
            static::element("Сумма реализации"),
            static::element("Сумма закупа"),
            static::element("Количество"),
            static::element("ID категории родителя"),
            static::element("ID категории"),
            static::element("Название категории родителя"),
            static::element("Название категории"),
            static::element("Ответственный"),
        ];

        foreach($data as $item){
            $cat = str_replace(",", "", $item->c_category);
            $cat_first = static::get_first_categories($cat, $new_categories);
            $table->body[] = [
                static::element($item->id),
                static::element($item->cid),
                static::element($item->s_sku),
                static::element($item->s_name),
                static::element($item->cm_name),
                static::element($item->o_inn),
                static::element($item->o_name),
                static::element($item->s_numb),
                static::element(Sales::getSalesType()[$item->s_sales_type] ?? 'Не известно'),
                static::element( date("d.m.Y", strtotime($item->s_date)) ),
                static::element($item->r_numb),
                static::element( date("d.m.Y", strtotime($item->r_date)) ),
                static::element($item->obj_name),
                static::element($item->total, ["round" => true]),
                static::element($item->supply_order_price * $item->count, ["round" => true]),
                static::element($item->count, ["align" => "center"]),
                static::element($cat_first),
                static::element($cat),
                static::element($new_categories[$cat_first]["name"] ?? $cat_first),
                static::element($new_categories[$cat]["name"] ?? $cat),
                static::element(shortUserName($item->r_uid)),
            ];
        }

        return $table;
    }

    static function get_first_categories($id, $categories){
        if(empty($id)){
            return "";
        } else {
            if(!array_key_exists($id, $categories) || $categories[$id]["parent_id"] == 0){
                return $id;
            } else {
                if(array_key_exists($categories[$id]["parent_id"], $categories)){
                    return static::get_first_categories($categories[$id]["parent_id"], $categories);
                } else {
                    return $id;
                }
            }
        }
    }
}