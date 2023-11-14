<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Support;

use Boot\System\Office\Pdf\Merge;
use Boot\System\Office\Pdf\Factory;
use Boot\System\Filesystem\Filesystem;

class Office
{
    public static function merge(){
       $merge = new Merge(new Filesystem);
       return $merge->init();
    }

}