<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\Data\Repositories;

use Scada\Models\Scada;
use Boot\System\Core\Abstracts\Repositories\Repository;

class ScadaRepository extends Repository
{
    protected $fieldSearchable = [
        'title' => 'like',
        'address' => 'like',
        'oid' => '=',
        'id' => '=',
        'status' => '=',
    ];
    
    public function model(): string
    {
        return Scada::class;
    }
}
