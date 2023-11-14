<?php

namespace Supply;

class SearchName extends \SearchName\SearchName{
    protected $url = "/administration/module/supply/search_name";

    public function search($request){
        global $db;
        $search = $request["search"];
        $query = $db->query("SELECT DISTINCT sid FROM JMY_supply_detail WHERE s_name LIKE '%{$search}%' OR s_sku LIKE '%{$search}%'");
        if($db->numRows($query) > 0){
            echo '<div><h3>Заявка на закупку</h3><ul>';
            while($r = $db->getRow($query)){
                echo '<li><a target="_blank" href="/administration/module/supply/detail/'.$r["sid"].'">Заявка на закупку № '.module_perfix("supply", $r["sid"]).'</a></li>';
            }
            echo '</ul></div>';
        } else {    
            echo '<div>Заявки на закупку отсутствуют</div>';
        }
        $query = $db->query("SELECT DISTINCT supply_o FROM JMY_supply_order_detail as sod LEFT JOIN JMY_catalog as c ON(c.id = sod.cid) WHERE c.title LIKE '%{$search}%' OR c.sku LIKE '%{$search}%'");
        if($db->numRows($query) > 0){
            echo '<div><h3>Счет поставщика</h3><ul>';
            while($r = $db->getRow($query)){
                echo '<li><a target="_blank" href="/administration/module/supply/order_detail/'.$r["supply_o"].'">Счет поставщика № '.module_perfix("supply_order", $r["supply_o"]).'</a></li>';
            }
            echo '</ul><div>';
        } else {    
            echo '<div>Счета поставщиков отсутствуют</div>';
        }
    }
}