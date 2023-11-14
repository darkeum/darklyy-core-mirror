<?php

function index_catalog_groups_discount(){
    global $url, $adminTpl;
    switch(isset($url[4]) ? $url[4] : null){
        case "add":
            $catalog_groups_discount = \Catalog_v2\Model\CatalogGroupsDiscount::create($_POST);
            echo $catalog_groups_discount->id;
        break;
        case "delete":
            $id = $url[5];
            \Catalog_v2\Model\CatalogGroupsDiscount::destroy($id);
        break;
        case "update":
          $id = $url[5];
          \Catalog_v2\Model\CatalogGroupsDiscount::findOrFail($id)->update($_POST);
        break;
    }
}
