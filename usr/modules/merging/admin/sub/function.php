<?php

function get_type($id = null){
    $type = [
        1 => "Комплектовка",
        2 => "Раскомплектовка",
    ];
    if(is_null($id)){
        return $type;
    } else {
        return isset($type[$id]) ? $type[$id] : "Не указан";
    }
}