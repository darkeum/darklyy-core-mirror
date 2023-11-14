<?php

namespace Catalog_v2\Model;

class CatalogGroupsOrganizations extends \Model\Model{

    protected $fillable = ["oid", "catalog_groups_discount_id"];

    static public function get_organizations_discount($oid){
        $sql_table = "SELECT * FROM `".DB_PREFIX."_catalog_groups_organizations` WHERE oid = '{$oid}'";
        return \Catalog_v2\Model\CatalogGroupsDiscount::get_discount_table(["sql_table" => $sql_table]);
    }
}