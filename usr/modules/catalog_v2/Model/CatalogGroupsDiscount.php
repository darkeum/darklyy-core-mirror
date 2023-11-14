<?php

namespace Catalog_v2\Model;

class CatalogGroupsDiscount extends \Model\Model{
    protected $fillable = ["catalog_groups_id", "catalog_groups_discount_name_id", "discount"];

    public function catalog_groups_discount_name(){
        return $this->belongsTo(CatalogGroupsDiscountName::class, "catalog_groups_discount_name_id");
    }

    static public function get_discount_table($request){
        global $db;
        $sql_table = $request["sql_table"];
        $result = [];
        $query = $db->query("SELECT cg.name as cg_name, cg.id as cg_id, cc.name as cgd_name, cc.discount as cgd_discount, cc.id as cgu_id, cc.catalog_groups_discount_id as cgu_catalog_groups_discount_id, cg.comment as cg_comment
        FROM `".DB_PREFIX."_catalog_groups` as cg 
        LEFT JOIN (
            SELECT cgdn.name, cgd.catalog_groups_id, cgd.discount, cgu.id, cgu.catalog_groups_discount_id
            FROM `".DB_PREFIX."_catalog_groups_discount` as cgd 
            LEFT JOIN `".DB_PREFIX."_catalog_groups_discount_name` as cgdn ON(cgdn.id = cgd.catalog_groups_discount_name_id)
            LEFT JOIN (
                {$sql_table}
            ) as cgu ON(cgd.id = cgu.catalog_groups_discount_id)
            HAVING 1
            ORDER BY cgu.id
        ) as cc ON(cg.id = cc.catalog_groups_id)
        GROUP BY cg.id 
        ORDER BY cg.id");

        while($r = $db->getRow($query)){
            $result[] = $r;
        }
        return $result;
    }
}