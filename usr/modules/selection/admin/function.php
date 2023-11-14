<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}
function getTypeMetring($type = 0){
    $array = [
        [
            "1" => "Общая", 
            "2" => "Подпитка",
            "3" => "ГВС",
            "4" => "ГВС с циркуляцией",
        ],
        [
            "1" => "block_vvoda", 
            "2" => "block_otopleniya",
            "3" => "block_gvs",
            "4" => "block_podpitky",
        ]
    ];
    
    return $array[$type]; 
}

function getTypeMetringScheme($type = 0){
    $array = [
        [
            "1" => "Схема №1 (2-х трубная)", 
            "2" => "Схема №2 (2-х трубная с подпиткой)",
            "3" => "Схема №3 (3-х трубная с ГВС)",
            "4" => "Схема №4 (4-х трубная с ГВС и циркуляцией)",
        ],
        [
            "1" => "block_vvoda", 
            "2" => "block_otopleniya",
            "3" => "block_gvs",
            "4" => "block_podpitky",
        ]
    ];
    
    return $array[$type]; 
}