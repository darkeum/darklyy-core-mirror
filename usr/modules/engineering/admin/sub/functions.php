<?php

//require_once("usr/modules/engineering/admin/sub/functions.php");

function section_type(){
    $type = [
        1 => "УУ", "ТМ", "ВК", "ОВ", "ТС", "НК", "АС", "Э", "АТС", "ЭОМ", "ЭО", "ЭМ", "КР", "ГСВ", "НВ", "АГСВ", "АСКУЭ", "ГСН",
    ];
    return $type;
}

function get_status(){
    $status = [
        1 => "Первый статус",
    ];
    return $status;
}