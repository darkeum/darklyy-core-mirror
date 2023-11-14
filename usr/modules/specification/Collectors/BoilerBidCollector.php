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

use Boiler\Model\BoilerBidData;
use Boiler\Model\BoilerBidDataDetail;
use Specification\Collectors\Collector;

class BoilerBidCollector extends Collector
{
    protected static $data;

    public static function get_data()
    {
        static::$data = BoilerBidDataDetail::with('catalog', 'catalog.catalog_unit', 'catalog.catalog_manufacture')->where('bbd_id', static::$id)->orderBy('position')->get();
    }

    public static function get_title($id)
    {
        return BoilerBidData::findOrFail(static::$id)->comment;
    }

    
}
