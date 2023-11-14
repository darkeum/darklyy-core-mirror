<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Abstracts\Repositories;

use Boot\System\Core\Abstracts\Repositories\Repository as AbstractRepository;

abstract class Repository extends AbstractRepository
{    
    public function boot()
    {
        parent::boot();
    }
}
