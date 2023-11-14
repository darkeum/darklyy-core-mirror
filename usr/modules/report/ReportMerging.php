<?php

namespace Report;

use Boot\System\Http\Request;


class ReportMerging extends Report
{
    static function getTitle()
    {
        return "Отчёт по комплектациям";
    }

    static function getReportName()
    {
        return "merging";
    }

    static function getDescription()
    {
        return "Отчёт по использование номенклатуры в производстве";
    }

    static function fields()
    {
        return [
            ["value" => "m.date", "text" => "Дата", "modal" => \Report\Modal\DateModal::class,],
        ];
    }

    static function query()
    {
        return \DB::table("merging_detail as md")->leftJoin("merging as m", "m.id", "=", "md.merging_id")->leftJoin("catalog as c", "c.id", "=", "md.cid")->leftJoin("catalog_manufacture as cm", "cm.id", "=", "c.man")->leftJoin("object as o", "o.id", "=", "m.object")->leftJoin("stock as s", "s.id", "=", "m.stock_id")->select("md.*", "m.cid as m_cid", "m.name as m_name", "m.numb as m_numb", "m.date as m_date", "m.org as m_org", "cm.name as cm_name", "o.name as o_name", "s.name as s_name", "m.uid as m_uid", "c.category as c_category")
            ->where("m.type", 1)
            ->where("m.active", 1)
            ->where("m.del", 0);
    }

    static function table_data(Request $request, $config = [])
    {
        $data = static::get_data($request);
        $table = static::get_table_structure();

        $categories = \Catalog_v2\Model\Categories::where("module", "catalog_v2")->get()->toArray();
        $new_categories = [];
        foreach ($categories as $category) {
            $new_categories[$category["id"]] = $category;
        }

        $table->head = [
            static::element("ID товара"),
            static::element("Артикул товара"),
            static::element("Наименование товара"),
            static::element("ID категории родителя"),
            static::element("ID категории"),
            static::element("Название категории родителя"),
            static::element("Название категории"),
            static::element("ID готового блока"),
            static::element("Наименование готового блока"),
            static::element("Заявка №"),
            static::element("Дата"),
            static::element("Организация"),
            static::element("Производитель"),
            static::element("Объект"),
            static::element("Склад"),
            static::element("Цена закупа"),
            static::element("Кол-во"),
            static::element("Сумма"),
            static::element("Ответственный"),
        ];

        foreach ($data as $item) {
            $org = getOrgInfo($item->m_org);
            $cat = str_replace(",", "", $item->c_category);
            $cat_first = static::get_first_categories($cat, $new_categories);
            $table->body[] = [
                static::element($item->cid),
                static::element($item->sku),
                static::element($item->name),
                static::element($cat_first),
                static::element($cat),
                static::element($new_categories[$cat_first]["name"] ?? $cat_first),
                static::element($new_categories[$cat]["name"] ?? $cat),
                static::element($item->m_cid),
                static::element($item->m_name),
                static::element($item->m_numb),
                static::element(date("d.m.Y", strtotime($item->m_date))),
                static::element($org["organizations"]["name"]),
                static::element($item->cm_name),
                static::element($item->o_name),
                static::element($item->s_name),
                static::element((float)$item->price),
                static::element((float)$item->count),
                static::element((float)($item->price * $item->count)),
                static::element(shortUserName($item->m_uid)),
            ];
        }

        return $table;
    }

    static function get_first_categories($id, $categories)
    {
        if (empty($id)) {
            return "";
        } else {
            if (!array_key_exists($id, $categories) || $categories[$id]["parent_id"] == 0) {
                return $id;
            } else {
                if (array_key_exists($categories[$id]["parent_id"], $categories)) {
                    return static::get_first_categories($categories[$id]["parent_id"], $categories);
                } else {
                    return $id;
                }
            }
        }
    }
}
