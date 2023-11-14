<?php

namespace Report\Modal;

abstract class Modal{
    abstract static function getPrefix();
    abstract static function getType();
    abstract static function template();

    static function getName(){
        return static::getPrefix()."Modal";
    }

    static function getElement(){
        return static::getPrefix()."Modal".mb_ucfirst(mb_strtolower(static::getType()));
    }

    static function getButton(){
        return static::getPrefix()."ModalButton";
    }

    static function getCondition(){
        return static::getPrefix()."Condition";
    }

    static function condition_text(){
        return [
            1 => "Равно",
            2 => "Не равно",
            3 => "Меньше",
            4 => "Меньше или равно",
            5 => "Больше",
            6 => "Больше или равно",
            7 => "Содержит",
            8 => "Не содержит",
            9 => "Начинается с",
            10 => "Не начинается с",
            11 => "Заполнено",
            12 => "Не заполнено",
            13 => "В группе",
        ];
    }

    static function getFunction(){
        return "";
    }

    //Дополняет основной query
    static function additional_query($query, $selection){
        return $query;
    }

    //Заменяет основной query
    // static function final_query($query, $selection)
}