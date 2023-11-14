<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Core\Abstracts\Models;

use Boot\System\Core\Traits\FactoryLocatorTrait;
use Boot\System\Core\Traits\HashIdTrait;
use Boot\System\Core\Traits\HasResourceKeyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Boot\Models\Users;
use Boot\System\Permission\Traits\HasRoles;

abstract class UserModel extends Users
{
    use HashIdTrait;
    use HasResourceKeyTrait;
    use HasRoles;
    use HasFactory, FactoryLocatorTrait {
        FactoryLocatorTrait::newFactory insteadof HasFactory;
    }
}
