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

use Scada\Models\Scada;
use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;

class ScadaTagsGrid extends GridModern
{
    protected $db_type = 'db';

    protected $namespace = 'scada_tags';

    protected function init()
    {
        $this->options = array(
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        );

        $this->title = __('scada::tags.list');

        $this->db = DB::table($this->namespace)->whereNull('deleted_at');

        $this->cols = array(

            # ID
            'id' => array(
                'db' => $this->namespace . '.id',
                'visible' => false,
            ),

            # №
            'numb' => array(
                'title' => '№',
                'db' => $this->namespace . '.id',
                'width' => '5%',
                'className' => 'text-center px-1',

                'search_op' => 'like'
            ),

            # Объект диспетчеризации
            'scada_id' => array(
                'title' =>  __('scada::tags.grid.scada_id'),
                'db' => $this->namespace . '.scada_id',
                'width' => '10%',
                'encode' => false,
                'replace' => $this->getScada(),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getScada(true), __('grid.all')),
                ),

                'search_op' => 'equal',
            ),

            # Название
            'title' => array(
                'title' =>  __('scada::tags.grid.title'),
                'db' => $this->namespace . '.title',
                'width' => '10%',
                'className' => 'text-center px-1',
                'render' => 'text',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            # Адрес устройства
            'unit_id' => array(
                'title' =>  __('scada::tags.grid.unit_id'),
                'db' => $this->namespace . '.unit_id',
                'width' => '5%',
                'className' => 'text-center px-1',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            # Адрес тега
            'address' => array(
                'title' =>  __('scada::tags.grid.address'),
                'db' => $this->namespace . '.address',
                'width' => '5%',
                'className' => 'text-center px-1',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            # Допустимые действия
            'actions' => array(
                'title' =>  __('scada::tags.grid.actions'),
                'db' => $this->namespace . '.actions',
                'width' => '5%',
                'className' => 'text-center px-1',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            # Регистр
            'registr' => array(
                'title' =>  __('scada::tags.grid.registr'),
                'db' => $this->namespace . '.registr',
                'width' => '8%',
                'className' => 'text-center px-1',
                'render' => 'text',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            # Дата создания
            'created_at' => [
                'title' => __('scada::tags.grid.created'),
                'db' => $this->namespace . '.created_at',
                'type' => 'date_range',
                'width' => '10%',
                'className' => 'text-center px-1',

                'search_op' => 'date_range'
            ],

            # Дата опроса
            'polling_at' => [
                'title' => __('scada::tags.grid.polling'),
                'db' => $this->namespace . '.polling_at',
                'type' => 'date_range',
                'width' => '10%',
                'className' => 'text-center px-1',

                'search_op' => 'date_range'
            ],

            # Действия
            'action' => [
                'type' => 'actions',
                'width' => '1%',
                'className' => 'text-center px-1',
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search' => false,
                'excel_hidden' => true,
                'search_op' => false,
            ]
        );
        $this->render_filter_toolbar = true;
    }

        protected function getScada(){
        $rows = [];
        foreach(Scada::all() as $item){
            $rows[$item->id] = $item->title;
        }
        return $rows;
    }

    protected function parseRow($row)
    {
        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => 'scada.tags',
            'actions' => [
                'edit' => true,
                'destroy' => true,
            ],
        ])->render();
        return $row;
    }
}