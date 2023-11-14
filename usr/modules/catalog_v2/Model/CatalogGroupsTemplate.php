<?php

namespace Catalog_v2\Model;

class CatalogGroupsTemplate extends \Model\Model{

    protected $fillable = ["catalog_groups_template_name_id", "catalog_groups_discount_id"];

    static public function get_template_discount($id){
        $sql_table = "SELECT * FROM `".DB_PREFIX."_catalog_groups_template` WHERE catalog_groups_template_name_id = '{$id}'";
        return \Catalog_v2\Model\CatalogGroupsDiscount::get_discount_table(["sql_table" => $sql_table]);
    }
}