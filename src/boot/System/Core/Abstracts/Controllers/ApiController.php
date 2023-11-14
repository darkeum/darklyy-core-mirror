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

use Boot\System\Core\Traits\ResponseTrait;

abstract class ApiController extends Controller
{
    use ResponseTrait;

    /**
     * Тип этого контроллера. Это будет доступно зеркально в Действиях.
     * Предоставление каждому действию возможности изменять свою внутреннюю бизнес-логику в зависимости от типа пользовательского интерфейса, вызвавшего его.
     */
    public string $ui = 'api';
}
