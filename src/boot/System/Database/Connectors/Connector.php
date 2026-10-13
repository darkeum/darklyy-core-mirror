<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2026 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Database\Connectors;

use Boot\System\Database\DetectsLostConnections;

class Connector extends \Illuminate\Database\Connectors\Connector
{
    use DetectsLostConnections;
}
