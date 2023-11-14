<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (25.03.2023 15:41)
*/
namespace Controls\UI\WEB\Grids;

use Boot\Support\Facades\DB;

class ControlsAllGrid extends ControlsGrid {

    protected function init() {
        parent::init();
        $this->title = __('controls::grid.list') . '(Все)';

        $this->db = DB::table('control as c')
            ->leftJoin('organizations as o', 'o.id', '=', 'c.oid')
            ->leftJoin('supply_order as so', 'so.id', '=', 'c.supply_o')
            ->leftJoin('object as obj', 'obj.id', '=', 'c.object');
    }
}