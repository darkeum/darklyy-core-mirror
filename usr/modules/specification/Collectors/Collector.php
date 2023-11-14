<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Specification\Collectors;

use Boot\Support\Str;
use Boot\Support\Numb;
use Specification\Contracts\CollectorContract;

abstract class Collector implements CollectorContract
{
    protected static $id;
    protected static $data;
    protected static $result = [];
    protected static $counter = 0;

    public static function get($id)
    {
        static::$id = $id;
        static::get_data();
        static::get_result();
        return static::$result;
    }

    public static function get_title($id)
    {
        return null;
    }

    protected static function add_result($sku, $title, $unit, $count, $model, $manufacture, $weight)
    {
        static::$counter++;
        static::$result[] = [
            'numb' => static::$counter,
            'sku' => $sku,
            'title' => $title,
            'unit' => $unit,
            'count' => $count,
            'model' => $model,
            'manufacture' => $manufacture,
            'weight' => $weight
        ];
    }

    public static function get_result()
    {
        foreach (static::$data as $value) {
            $spec = $value->catalog->spec;
            $sku = Str::word($value->catalog->sku);
            $title = static::prepare_title($value->catalog->title, $spec);
            $unit = Str::word($value->catalog->catalog_unit->sname, $spec);
            $count = $value->count;
            $model = static::prepare_model($spec);
            $manufacture = static::prepare_manufacture($value->catalog->catalog_manufacture->name ?? null, $spec);
            $weight = static::prepare_weight($value->catalog->weight);
            static::add_result($sku, $title, $unit, $count, $model, $manufacture, $weight);
        }
    }

    protected static function prepare_title($title = null, $spec = null)
    {
        $result = Str::word($title);
        if (is_array($spec)) {
            if (!empty($spec['1'])) {
                $result = Str::word($spec['1']);
            }
        }
        return $result;
    }

    protected static function prepare_manufacture($manufacture = null, $spec = null)
    {
        $result = Str::word($manufacture);
        if (is_array($spec)) {
            if (!empty($spec['3'])) {
                $result = Str::word($spec['3']);
            }
        }
        return $result;
    }

    protected static function prepare_model($spec = null)
    {
        $result = '';
        if (is_array($spec)) {
            if (!empty($spec['2'])) {
                $result = Str::word($spec['2']);
            }
        }
        return $result;
    }

    protected static function prepare_weight($weight = null)
    {
        $result = '';
        if (is_array($weight)) {
            if (!empty($weight['1'])) {
                $result = Numb::prepareNumeric($weight['1'], true);
            }
        }
        return $result;
    }
}
