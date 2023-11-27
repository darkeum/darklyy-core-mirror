<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Permission\Contracts;

use Boot\Models\Model;

interface Wildcard
{
    public function __construct(Model $record);

    public function getIndex(): array;

    public function implies(string $permission, string $guardName, array $index): bool;
}
