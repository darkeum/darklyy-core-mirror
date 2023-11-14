<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Reestr\UI\WEB\Grids;

use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;
use Orgs\Models\Org;
use Reestr\Models\ReestrType;

class Types extends GridModern
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
	protected $namespace = 'reestr';

	/**
	 * Инициализация grid
	 *
	 * @return void
	 */
	protected function init()
	{
		//Заголовок таблицы
		$this->title = __('reestr::admin.types');

		//Опции таблицы (в данном случае сортировка)
		$this->options = array(
			'sort' => [
				'name' => 'id',
				'order' => 'DESC'
			],
            'button' => view('reestr::manager.actions.newtype',['orgs' => Org::with('organization')->get()])->render(),
		);

		//Опции панели навигации
		$this->nav = array(
			'export' => true,
			'export.detail' => [
				'excel' => true,
				'pdf' => true,
				'print' => true,
				'copy' => true,
				'csv' => false,
			],
		);

		$this->db = DB::table('reestr_type' . ' as r')
		->leftJoin('org as o','o.id','=','r.org');

		//Настройка колонок
		$this->cols = [
			'id' => array(
				'visible' => false,
				'db' => 'r.id',
			),

            'org' => array(
				'title' => 'Организация',
                'db' => 'o.oid',
				'render' => 'text',
                'width' => '50%',
                'replace' => $this->getOrgTypes(),
                'type' => 'select',
				'options' => array(
                    'value' => $this->setValue($this->getOrgTypes(), 'Все'),
                ),
                'search_op' => 'equal',
            ),

            'name' => array(
				'title' => 'Название',
                'db' => 'r.name',
                'width' => "40%",
                'replace' => $this->getReestr(),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getReestr(), 'Все'),
                ),
                'search_op' => 'equal',
            ),

            'short' => array(
				'title' => 'Префикс',
                'db' => 'r.short',
                'width' => "10%",
                'replace' => $this->getReestr('short'),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getReestr('short'), 'Все'),
                ),
                'className' => 'text-center',
                'search_op' => 'equal',
            ),

            'action' => array(
                'type' => 'actions',
                // 'width' => '1%', // Для actions всегда стоит 1% в фильтрах
                'className' => 'text-center px-1',
                'visible' => false,
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search' => false,
                'excel_hidden' => true,

                // query sql
                'search_op' => false,
            ),

		];
		$this->render_filter_toolbar = true;
	}

	protected function getOrgTypes()
    {
		$result = DB::table('org as r')
		->leftJoin('organizations as o','r.oid','=','o.id')
		->get();

		foreach($result as $item)
		{
			$rows[$item->id] = $item->name;
		}
        return $rows;
    }

    protected function getReestr($mode = null)
    {
        $result = ReestrType::all();

        if($mode == 'short')
        {
            foreach($result as $item)
            {
                $rows[$item->id] = $item->short;
            }

        }
        else
        {
            foreach($result as $item)
            {
                $rows[$item->id] = $item->name;
            }
        }

        return $rows;
    }


	/**
	 * Подстановка и замена элементов в строке (подмена ячеек)
	 *
	 * @param  array $row
	 * @return void
	 */
	protected function parseRow($row)
	{
		return $row;
	}
}
