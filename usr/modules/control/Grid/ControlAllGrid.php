<?php

namespace Control\Grid;

use \Boot\System\Grid\Grid;
use \Boot\System\Database\DB;

class ControlAllGrid extends ControlGrid{
    protected function prepareQuery(){
        return $this->getDBQuery();
    }
}