<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Kirill Gusev
*/

namespace Changelog\UI\WEB\Grids;

use \Boot\System\Database\DB;
use Boot\System\Grid\GridModern;
use Boot\Models\Categories;

class ChangelogIndex extends GridModern
{
    protected string $db_type = 'db';
    protected string $namespace = 'changelog';

    protected function init()
    {
        $this->title = 'Список изменений';

        $this->options = array(
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        );

        $this->db = DB::table('changelog as cl') -> where("deleted_at",'=',null) ->
        leftJoin("categories as ct", "ct.id", "=", "cl.category");

        $this->cols = array(

            'id' => array(
				'db' => 'cl.id',
                'visible' => false,
                'width' => '5%',
			),
			
			'numb' => array(
				'title' => '№',
                'label' => '#',
                'db' => 'cl.id',
                'width' => '5%',
                'className' => 'text-center',


				'search_op' => 'like'
				
			),

            'title' => array(
				'title' => 'Заголовок',
                'label' => 'Заголовок',
                'db' => 'cl.title',
                'width' => '10%',
                'className' => 'text-left',

				'search_op' => 'like'
				
			),

            'text' => array(
				'title' => 'Текст',
                'label' => 'Текст',
                'db' => 'cl.text',
                'width' => '30%',
                'render' => 'text',
                'className' => 'text-left',

				'search_op' => 'like'
				
			),

            'category' => array(
				'title' => 'Категория',
                'label' => 'Категория',
                'db' => 'ct.name',
                'width' => '5%',
                'type' => 'select',

                'className' => '',

                'options' => array(
                    'value' => $this->setValue($this->getChangelogCat(), __('grid.all')),
                ),

                'replace' => $this->getChangelogCat(),
                'search_op' => 'equal',
				
			),

            'uid' => array(
                'title' => "Ответственный",
                'db' => 'cl.uid',
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getUid($this->namespace), __('grid.all')),
                ),
                'width' => '5%',
                'className' => 'text-center',

                'encode' => false,
                'sortable' => false,
                'replace' => $this->getUid($this->namespace),

                'search_op' => 'equal',
            ),
  
           

            'date' => array(
                'title' => 'Дата',
                'label' => 'Дата',
                'db' => 'cl.published_at',
                'type' => 'date_range',
                'format_date' => "d.m.Y",
                'options' => $this->dateRangeSearch(),
                'width' => '1%',
                'className' => 'text-center',

                'search_op' => 'date_range',
            ),

            'action' => array(
                'type' => 'actions',
                'className' => 'text-center',

                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search' => false,
                'excel_hidden' => true,


                'search_op' => false,
            ),
        );
        $this->render_filter_toolbar = true;
    }
    
    protected function getChangelogCat($table = null)
    {
        $rows = array();
        
        $categories =Categories::all();
        foreach ($categories as $k => $v) {
            $name = $v->name;
            $rows[$name] = $name;
        }
        // dd($rows);
        return $rows;
    }

    protected function parseRow($row)
    {
        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            //'module' => $this->namespace,
            'module' => 'changelog',
            'actions' => [
                'show' => true,
                'edit' => true,
                // 'destroy' => true,
            ],
        ])->render();

        return $row;
    }

}
