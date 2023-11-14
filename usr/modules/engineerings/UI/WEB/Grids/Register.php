<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Engineerings\UI\WEB\Grids;

use Boot\System\Database\DB;
use Decrees\Tasks\TypesTask;
use Support\Tasks\StatusTask;
use Boot\Support\Facades\Auth;
use Boot\System\Grid\GridModern;

class Register extends GridModern
{
	/**
	 * Тип подключение к базе данных
	 *
	 * @var string
	 */
	protected $db_type = 'db';

	/**
	 * Пространство имен
	 *
	 * @var string
	 */
	protected $namespace = 'engineerings';


	/**
	 * Инициализация grid
	 *
	 * @return void
	 */
	protected function init()
	{
		//Заголовок таблицы
		$this->title = __('engineerings::general.register.grid');

		//Опции таблицы (в данном случае сортировка)
		$this->options = array(
			'sort_name' => 'id',
			'sort_order' => 'DESC',
		);

		//Опции панели навигации
		$this->nav = array(
			'excel' => true,
			'exceltext' => __('grid.export.excel'),
			'trash' => true,
			'trashtype' => 'null',
			'trashtext' => __('grid.trash.show'),
			'trashfield' => $this->namespace . '.deleted_at',
		);

		//Подключение к необходимой таблице
		$this->db = DB::table("engineering_register as er")
		->leftJoin("object as obj",'obj.id','=','er.object');
        

		//Настройка колонок
		$this->cols = array(

			'id' => array(
				'db' => 'er.id',
                'visible' => false,
			),

			'status_real' => array(
				'db' => 'er.status',
                'visible' => false,
			),

			'object_id' => array(
				'db' => 'er.object',
                'visible' => false,
			),
			
			'numb' => array(
				'title' => '№',
                'label' => '#',
                'db' => 'er.id',
                'width' => '5%',
                'className' => 'text-center',

                // query sql
				'search_op' => 'like'
				
			),

			'title' => array(
				'title' => 'Шифр',
                'db' => 'er.numb',
				'null' => "Отсутствует",

				'width' => '15%',
				'className' => 'text-left',

                // query sql
				'search_op' => 'like'
			),
			
			'section' => array(
				'title' => 'Раздел',
                'label' => 'Раздел',
                'db' => 'er.section',

				'null' => 'Не указано',
				'encode' => false,
				'sortable' => false,

                'width' => '15%',
                'className' => '',

                // query sql
				'search_op' => 'like'
				
			),

			'object' => array(
				'title' => 'Объект',
                'label' => 'Объект',
                'db' => 'obj.name',
				
				'popover' => true,

				'null' => 'Не указано',
				'encode' => false,

                'width' => '15%',
                'className' => '',

				
			),

			'date' => array(
			    'title' => 'Дата',
                'db' => 'er.date',
                'type' => 'date_range',
                //'format_date' => 'd.m.Y',
                'options' => $this->dateRangeSearch(),
                'width' => '10%',
                'className' => 'text-center',

                // query sql
			    'search_op' => 'date_range'
			),

			'uid' => array(
                'title' => "Ответственный",
                'db' => 'er.uid',

                'width' => '5%',
                'className' => 'text-center',
				
                'encode' => false,
                'sortable' => false,

                'search_op' => 'equal',
            ),
		

			'status' => array(
				'title' => 'Статус',
                'label' => 'Статус',
                'db' => 'er.status',

				'null' => 'Не указано',
				'encode' => false,
				'sortable' => false,
				'replace'=>$this->getAutoTypes(),

				'stype' => 'select',

                'width' => '15%',
                'className' => 'text-center',

				'search_op' => 'equal',
			),

			'action' => array(
                'type' => 'actions',
                'width' => '1%',
                'className' => 'text-center px-1',

				'manual' => true,
				'encode' => false,
				'sortable' => false,
				'search' => false,
				'excel_hidden' => true,

                // query sql
                'search_op' => false,
			),

		);
		$this->render_filter_toolbar = true;
	}

	protected function getAutoTypes($select = false)
    {       
		require_once("usr/modules/engineering/admin/sub/functions.php");

		$rows_select = get_status();	
		$rows = get_status();
		
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getSectionTypes()
    {
		require_once("usr/modules/engineering/admin/sub/functions.php");
		$rows = section_type();
        return $rows;
    }

	protected function parseRow($row)
	{
		$row['object'] = empty($row['object_id']) ? '<font color="red">Не указано</font>' : '<a href="/administration/object/view/'.$row['object_id'].'" target="_blank">'.$row['object'].'</a>';
        
		$uid = json_decode($row["uid"], true);
        $elems = [];
        if(count($uid) > 0){
            foreach($uid as $val){
                $elems[] = shortUserName($val);
            }
            $row["uid"] = implode(" / ", $elems);
        }

        require_once("usr/modules/engineering/admin/sub/functions.php");
        $section = json_decode($row["section"], true);
        $section_type = section_type();
        $elems = [];
        if(count($section) > 0){
            foreach($section as $val){
                $elems[] = $section_type[$val];
            }
            $row["section"] = implode(" / ", $elems);
        }

        // $row['action'] = view('grid::layouts.actions', [
        //     'row' => $row,
        //     'module' => $this->namespace,
        //     'actions' => [
        //         'show' => true,
        //     ],
        // ])->render();

		if (is_date($row["date"], "Y-m-d")) $row["date"] = date("d.m.Y", strtotime($row["date"]));
		return $row;
	}
}
