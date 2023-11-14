<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Core\Abstracts\Controllers;

use Boot\System\Bus\DispatchesJobs;
use Boot\System\Core\Traits\HashIdTrait;
use Boot\System\Validation\ValidatesRequests;
use Boot\System\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as LaravelBaseController;

abstract class Controller extends LaravelBaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, HashIdTrait;
}
