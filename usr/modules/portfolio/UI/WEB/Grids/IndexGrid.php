<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Portfolio\UI\WEB\Grids;

use \Boot\System\Database\DB;
use \Boot\System\Categories\Categories;
use \Boot\Support\Facades\Translation;
Use Boot\System\Grid\GridModern;

class IndexGrid extends GridModern
{
    protected $db_type = 'db';
    protected $namespace = 'portfolio';
    protected function init()
    {
        $this->options = array(
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        );

        $this->title = 'Портфолио';

        $this->nav = array(
            'excel' => true,
            'exceltext' => __('grid.export.excel'),
            'trash' => true,
            'trashtype' => 'null',
            'trashtext' => __('grid.trash.show'),
            'trashfield' => $this->namespace . '.deleted_at',
        );
        
        $this->db = DB::table('portfolio');

        $this->cols = array(

            'id' => array(
                'visible' => false,
                'db' => 'portfolio.id',
            ),

            'numb' => array(
                'db' => 'portfolio.id',
                'title' => '№',
                'width' => "5%",
                'className' => 'text-center',
                'search_op' => 'like'
            ),

            'title' => array(
                'db' => 'portfolio.title',
                'title' => "Заголовок",
                'width' => "15%",
                'search_op' => 'like'
            ),

            'description' => array(
                'db' => 'portfolio.description',
                'title' => "Описание объекта",
                'width' => "15%",
                'search_op' => 'like'
            ),

            'uid' => array(
                'title' => Translation::trans('portfolio_grid_uid'),
                'db' => 'portfolio.uid',
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getUid('portfolio'),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getUid('portfolio'), Translation::trans('portfolio_grid_all')),
                ),
                'search_op' => 'equal',
            ),

            'created_at' => array(
                'title' => Translation::trans('portfolio_grid_created_at'),
                'db' => 'portfolio.created_at',
                'width' => 25,
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'updated_at' => array(
                'title' => Translation::trans('portfolio_grid_updated_at'),
                'db' => 'portfolio.updated_at',
                'width' => 25,
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),
        
            'category' => array(
				'title' => Translation::trans('portfolio_grid_category'),
				'width' => 30,
				'db' => 'portfolio.category',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getCategoryTypes(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' =>  $this->setValue($this->getCategoryTypes(), Translation::trans('portfolio_grid_all')),
                ),
                'search_op' => 'equal',
            ),

            'status' => array(
                'title' => Translation::trans('portfolio_grid_status'),
                'db' => 'portfolio.status',
                'width' => 15,
                'hidden' => false,
                'encode' => false,
                'replace' => $this->getStatus('render'),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getStatus('select'), Translation::trans('portfolio_grid_all')),
                ),
                'search_op' => 'equal',
            ),

            'ab' => array(
                'title' => Translation::trans('portfolio_grid_action'),
                'width' => 50,
                'hidden' => false, 
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search_op' => false,
                'search' => false,
                'title' => false,
            ),
        );
        $this->render_filter_toolbar = true;
    }

    protected function getStatus($type = 'select')
    {
        $rows = [];
        if ($type == 'select') {
            $rows[0] = Translation::trans('portfolio_status_off');
            $rows[1] = Translation::trans('portfolio_status_on');
        } elseif ($type == 'render') {
            $rows[0] = "<span class='far fa-circle text-danger fa-md' data-toggle='tooltip' title='". Translation::trans('portfolio_status_off')."'></span>";
            $rows[1] = "<span class='far fa-check-circle text-success fa-md' data-toggle='tooltip' title='". Translation::trans('portfolio_status_on')."'></span>";
        }
        return $rows;
    }

    protected function getCategoryTypes() {
        $categories = new Categories; 
        $rows = [];
        foreach ( $categories->getModuleCategories('portfolio') as $value) {
			$rows[$value['id']] = $value['name'];
        }
        return $rows;
    }

    protected function parseRow($r)
    {
        $r["numb"] = $r["id"];
        $r['ab'] = '<a class="btn btn-xs btn-primary" href="'.router('admin.portfolio.edit', $r['id']).'">Изменить</a>'; 
        if(is_date($r["created_at"], "Y-m-d H:i:s")) $r["created_at"] = date("d.m.Y H:i:s", strtotime($r["created_at"]));
        if(is_date($r["updated_at"], "Y-m-d H:i:s")) $r["updated_at"] = date("d.m.Y H:i:s", strtotime($r["updated_at"]));
        return $r;
    }
}
