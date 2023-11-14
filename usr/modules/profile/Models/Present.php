<?php

namespace Profile\Models;

use Boot\Models\Model;

class Present extends Model
{
    protected $fillable = ["uid", "type", "active", "data", "data2", "comment", "count"];

    protected $casts = [
        'data' => "object",
        'data2' => "object",
    ];

    static public function getPresents()
    {
        return [
            1 => "Кружка", "Термокружка", "Футболка", "Ручка + блокнот",
        ];
    }

    static public function getPresentsImages()
    {
        return [
            1 => "/usr/tpl/dev2/assest/images/present/1.jpg", "/usr/tpl/dev2/assest/images/present/2.jpg", "/usr/tpl/dev2/assest/images/present/3.jpg", "/usr/tpl/dev2/assest/images/present/4.jpg",
        ];
    }

    static public function getPresentsSelect()
    {
        return [
            1 => array(1 => 'Цвет: белый'), array(1 => 'Цвет: белый'), array(1 => 'Размер: S', 'Размер: M', 'Размер: L', 'Размер: XS', 'Размер: XL', 'Размер: XXL'), array(1 => 'Цвет: красный', 'Цвет: черный'),
        ];
    }


}