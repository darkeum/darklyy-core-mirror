<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\WEB\Grids;

use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;

class ScadaCrashGrid extends GridModern
{
    protected $db_type = 'db';

    protected $namespace = 'scada_crash';

    protected $namespace_second = 'scada_tags';

    protected $namespace_third = 'scada';

    protected function init()
    {
        $this->options = array(
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        );

        $this->title = __('scada::crash.list');

        $this->db = DB::table($this->namespace)->leftJoin($this->namespace_second, $this->namespace_second . ".id", "=", $this->namespace . ".tags_id")->leftJoin($this->namespace_third, $this->namespace_third . ".id", "=", $this->namespace . ".scada_id");

        $this->cols = array(

            'id' => array(
                'db' => $this->namespace . '.id',
                'visible' => false,
            ),

            'numb' => array(
                'title' => '№',
                'db' => $this->namespace . '.id',
                'width' => '5%',
                'className' => 'text-center px-1',

                'search_op' => 'like'
            ),

            'tags_id' => array(
                'title' => __('scada::crash.grid.tag'),
                'db' => $this->namespace . '.tags_id',
                'width' => '5%',
                'className' => 'text-center px-1',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            'tags_title' => array(
                'title' =>  __('scada::crash.grid.title.tag'),
                'db' => $this->namespace_second . '.title',
                'width' => '10%',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            'scada_title' => array(
                'title' =>  __('scada::crash.grid.title.scada'),
                'db' => $this->namespace_third . '.title',
                'width' => '10%',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            'description' => array(
                'title' =>  __('scada::crash.grid.description'),
                'db' => $this->namespace . '.description',
                'width' => '5%',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),         
       
            'created_at' => [
                'title' => __('scada::crash.grid.created'),
                'db' => $this->namespace . '.created_at',
                'type' => 'date_range',
                'width' => '10%',
                'className' => 'text-center px-1',

                'search_op' => 'date_range'
            ], 
           
            //'action' => [
            //    'type' => 'actions',
            //    'width' => '1%',
            //    'className' => 'text-center px-1',
            //    'manual' => true,
            //    'encode' => false,
            //    'sortable' => false,
            //    'search' => false,
            //    'excel_hidden' => true,
            //
            //    'search_op' => false,
            //]
        );
        $this->render_filter_toolbar = true;
    }   

    protected function parseRow($row)
    {
        return $row;
    }
}
