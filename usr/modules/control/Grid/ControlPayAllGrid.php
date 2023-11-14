<?php

namespace Control\Grid;

use \Boot\System\Grid\Grid;
use \Boot\System\Database\DB;

class ControlPayAllGrid extends ControlPayGrid
{
    protected function prepare_query($query){
        return $query;
    }
}
