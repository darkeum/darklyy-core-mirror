<?php

function get_query_catalog($atribute = []){
    // global $core;
    // $uid = isset($core->auth->user_info['id']) ? $core->auth->user_info['id'] : 0;
    // $oid = find_org_uid($uid);

    // $view_all = isset($atribute["view_all"]) ? $atribute["view_all"] : false;

    // $where_user = "";
    // if(!$view_all){
    //     if($core->auth->isUser){
    //         $where_user =  " AND n.active_user = '1'";
    //     } else {
    //         $where_user =  " AND n.active_guest = '1'";
    //     }
    // }

    // $const_where = "n.hidden = '0'".$where_user;
    // //IFNULL(cg.discount, 0) as discount, ROUND(IF(cg.discount IS NULL, cpd.sum, (cpd.sum * (1 - cg.discount / 100)) ), 2) as sum, cpd.sum as sum_original, cpd.price_type
    // //ROUND(IF(cg.discount IS NULL, cpd.sum, (cpd.sum * (1 - cg.discount / 100)) ), 2) as sum, cpd.sum as sum_original, cg.discount as discount, cpd.price_type

    // $no_price = isset($atribute["no_price"]) ? $atribute["no_price"] : false;
    // $no_discount = isset($atribute["no_discount"]) ? $atribute["no_discount"] : false;

    // $qd = "";
    // $price_table = "";
    // $price_field = "";
    // $discount_field = "";
    // if(!$no_price){
    //     if(!$no_discount){
    //         $qd = \Price::get_discount_query($oid);
    //         $qd = "LEFT JOIN (
    //             {$qd}
    //         ) as cg ON(n.id = cg.cid)";
    //         $discount_field = ", IFNULL(cg.discount, 0) as discount, ROUND(IF(cg.discount IS NULL, @sum, (@sum * (1 - cg.discount / 100)) ), 2) as sum";
    //     }

    //     $price_table = "LEFT JOIN `".DB_PREFIX."_catalog_price_temp` as cpte ON(cpte.id = n.id)
    //     LEFT JOIN `".DB_PREFIX."_catalog_price_detail` as cpd ON(cpd.id = cpte.price_price)
    //     LEFT JOIN `".DB_PREFIX."_catalog_price_type` as cpt ON(cpt.id = cpte.price_type_price)";

    //     $price_field = ", cpt.id as price_type, @sum:=cpd.sum * cpt.course, @sum as sum_original";
    //     if(empty($discount_field)){
    //         $price_field .= ", @sum as sum";
    //     }
    // }


    // $catalog = [
    //     "SELECT" => "SELECT n.*, u.sname as unit_sname, m.name as manufacture_name, m.id as manufacture_id, m.preview as m_preview {$price_field} {$discount_field}",

    //     "FROM" => "FROM `".DB_PREFIX."_catalog` as n
    //     LEFT JOIN  `".DB_PREFIX."_catalog_unit` as u on(u.unit=n.unit)
    //     LEFT JOIN `".DB_PREFIX."_catalog_manufacture` as m on(m.id = n.man)
    //     {$price_table}
    //     {$qd}",

    //     "WHERE" => isset($atribute["where"]) ? "WHERE ".$atribute["where"]." AND ".$const_where : $const_where,

    //     "GROUP_BY" => "GROUP BY n.id",

    //     "ORDER_BY" => "ORDER BY ".$atribute["sort"]." ".$atribute["sort_type"],

    //     "LIMIT" => "LIMIT ".($atribute["count"] * $atribute['page']).", ".$atribute["count"],
    // ];
    return $catalog;
}