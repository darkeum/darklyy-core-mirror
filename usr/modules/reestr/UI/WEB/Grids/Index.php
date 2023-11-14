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
use Decrees\Tasks\TypesTask;
use Boot\System\Grid\GridModern;
use Illuminate\Database\Query\JoinClause;
use Orgs\Models\Org;
use Reestr\Models\Reestr;
use Reestr\Models\ReestrType;

class Index extends GridModern
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
		$this->title = __('decrees::admin.list');

		//Опции таблицы (в данном случае сортировка)
		$this->options = array(
			'sort' => [
				'name' => 'id',
				'order' => 'DESC'
			],

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

		//Подключение к необходимой таблице
		$sub = DB::table('reestr_doc')
			->where('sign', '=', 1)
			->whereNotNull('scan')
			->groupBy('reestr_id')
			->select(DB::raw('COUNT(*) as count'), 'reestr_id as id');

		$this->db = DB::table($this->namespace . ' as r')
			->leftJoin('reestr_type as t', 't.id', '=', 'r.type')
			->leftJoin('org as o', 'o.id', '=', 'r.org')
			->leftJoin('organizations as org', 'org.id', '=', 'r.oid')
			->leftJoin('users as u', 'u.id', '=', 'r.uid')
			->leftJoin('object as obj', 'obj.id', '=', 'r.object')
			->leftJoinSub($sub, 'cc', function (JoinClause $join) {
				$join->on('cc.id', '=', 'r.id');
			})
			->where('r.status', '=', 1)
			->where("r.deleted_at", NULL);


		//Настройка колонок
		$this->cols = [
			'id' => array(
				'visible' => false,
				'db' => 'r.id',
			),

			'del' => array(
				'visible' => false,
				'db' => 'r.deleted_at',
			),
			'numb_real' => array(
				'visible' => false,
				'db' => 'r.numb',
			),
			'type_real' => array(
				'visible' => false,
				'db' => 't.short',
			),
			'oid_real' => array(
				'visible' => false,
				'db' => 'r.oid',
			),
			'org_real' => array(
				'visible' => false,
				'db' => 'r.org',
			),
			'date_real' => array(
				'visible' => false,
				'db' => 'r.date',
			),

			'id_view' => array(
				'title' => 'ID',
				'db' => 'r.id',
				'width' => '1%',
				'className' => 'text-center',
			),

			'numb' => array(
				'title' => '№',
				'db' => 'r.id',
				'width' => '1%',
				'className' => 'text-center',
			),

			'type' => array(
				'title' => 'Тип договора',
				'width' => "1%",
				'className' => 'text-center',
				'db' => 'r.type_doc',
				'encode' => false,
				'replace' => $this->getReestrTypes(),
				'type' => 'select',
				'options' => array(
					'value' => $this->setValue($this->getReestrTypes(true), 'Все'),
				),
				'search_op' => 'equal',
			),

			'type_excel' => array(
				'title' => 'Тип договора',
				'render' => 'text',
				'width' => '1%',
				'className' => 'text-center',
				'db' => 'r.type_doc',
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getReestrTypes(true),
				'type' => 'select',
				'options' => array(
					'value' => $this->setValue($this->getReestrTypes(true), 'Все'),
				),
				'visible' => false,
				'search_op' => 'equal',
			),

			'title' => array(
				'title' => 'Тип договора',
				'db' => 't.id',
				'width' => '2%',
				'render' => 'text',
				'replace' => $this->getDeliveryTypes(),
				'type' => 'select',
				'options' => array(
					'value' => $this->setValue($this->getDeliveryTypes(), 'Все'),
				),
				'search_op' => 'equal',
			),

			'other_numb' => array(
				'title' => 'Особый номер',
				'db' => 'r.other_numb',
				'width' => '5%',
				'render' => 'text'
			),

			'org' => array(
				'title' => 'Организация',
				'db' => 'o.oid',
				'render' => 'text',
				'width' => '5%',
				'replace' => $this->getOrgTypes(),
				'type' => 'select',
				'options' => array(
					'value' => $this->setValue($this->getOrgTypes(), 'Все'),
				),
				'search_op' => 'equal',
			),

			'oid' => array(
				'title' => 'Контрагент',
				'db' => 'org.name',
				'width' => '5%',
				'render' => 'text'
			),

			'object' => array(
				'title' => 'Объект',
				'db' => 'obj.name',
				// 'null' => 'Не указано',
				'encode' => false,
				'width' => '5%',
				'render' => 'text',
			),

			'date_view' => array(
				'title' => 'Дата',
				'width' => "1%",
				'db' => 'r.date_view',
				'type' => 'date_range',
				'format_date' => "d.m.Y",
				'options' => $this->dateRangeSearch(),
				'search_op' => 'date_range',
				'className' => 'text-center',
			),

			'uid' => array(
				'title' => 'Ответственный',
				'db' => 'u.id',
				'width' => '3%',
				'replace' => $this->getUid(),
				'type' => 'select',
				'options' => array(
					'value' => $this->setValue($this->getUid(), 'Все'),
				),
				'search_op' => 'equal',
				'className' => 'text-center',
			),

			'comment' => array(
				'title' => 'Комментарий',
				'db' => 'r.comment',
				'width' => "1%",
				'render' => 'text',
				'replace' => $this->getDeliveryTypes(),
				'type' => 'select',
				'options' => array(
					'value' => $this->setValue($this->getDeliveryTypes(), 'Все'),
				),
				'search_op' => 'equal',
				'excel_only' => true,
				'visible' => false,
			),

			'sign' => array(
				'title' => 'Подпись',
				'width' => "1%",
				'className' => 'text-center',
				'db' => 'r.sign',
				'encode' => false,
				'replace' => $this->getSignTypes(),
				'type' => 'select',
				'options' => array(
					'value' => $this->setValue($this->getSignTypes(true), 'Все'),
				),
				'search_op' => 'equal',
				'excel_hidden' => true,
			),

			'sign_excel' => array(
				'title' => 'Подпись',
				'width' => "1%",
				'className' => 'text-center',
				'db' => 'r.sign',
				'encode' => false,
				'replace' => $this->getSignTypes(),
				'type' => 'select',
				'options' => array(
					'value' => $this->setValue($this->getSignTypes(true), 'Все'),
				),
				'search_op' => 'equal',
				'visible' => false,
				'excel_only' => true,
			),

			'scan' => array(
				'title' => 'Скан',
				'width' => "1%",
				'className' => 'text-center',
				'db' => 'r.scan',
				'encode' => false,
				'replace' => $this->getScanTypes(),
				'type' => 'select',
				'options' => array(
					'value' => $this->setValue($this->getScanTypes(true), 'Все'),
				),
				'search_op' => 'equal',
				'excel_hidden' => true,
			),

			'scan_excel' => array(
				'title' => 'Скан',
				'width' => "1%",
				'className' => 'text-center',
				'db' => 'r.scan',
				'encode' => false,
				'replace' => $this->getScanTypes(),
				'type' => 'select',
				'options' => array(
					'value' => $this->setValue($this->getScanTypes(true), 'Все'),
				),
				'search_op' => 'equal',
				'visible' => false,
				'excel_only' => true,
			),

			'count_doc' => array(
				'title' => 'Количество подписанных документов',
				'db' => 'cc.count',
				'width' => "1%",
				'visible' => false,
				'excel_only' => true,
			),


			'action' => array(
				'type' => 'actions',
				// 'width' => '1%', // Для actions всегда стоит 1% в фильтрах
				'className' => 'text-center px-1',

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

	protected function getScanTypes($select = false)
	{
		$rows_select = array();
		$rows_select[0] = 'Скан не приложен';
		$rows_select[1] = 'Скан приложен частично';
		$rows_select[2] = 'Скан приложен';

		$rows = array();
		$rows[0] = "<span class='fal fa-file-check fa-md text-danger fs-2' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $rows_select[0] . "'></span>";
		$rows[1] = "<span class='fal fa-file-check fa-md text-warning fs-2' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $rows_select[1] . "'></span>";
		$rows[2] = "<span class='fal fa-file-check fa-md text-success fs-2' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $rows_select[2] . "'></span>";

		return ($select ?  $rows_select :  $rows);
	}

	protected function getSignTypes($select = false)
	{
		$rows_select = array();
		$rows_select[0] = 'Не подписан';
		$rows_select[1] = 'Подписан частично';
		$rows_select[2] = 'Подписан';

		$rows = array();
		$rows[0] = "<span class='fal fa-file-signature fa-md text-danger fs-2' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $rows_select[0] . "'></span>";
		$rows[1] = "<span class='fal fa-file-signature fa-md text-warning fs-2' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $rows_select[1] . "'></span>";
		$rows[2] = "<span class='fal fa-file-signature fa-md text-success fs-2' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $rows_select[2] . "'></span>";

		return ($select ?  $rows_select :  $rows);
	}

	protected function getOrgTypes()
	{
		$result = DB::table('org as r')
			->leftJoin('organizations as o', 'r.oid', '=', 'o.id')
			->get();

		foreach ($result as $item) {
			$rows[$item->id] = $item->name;
		}
		// dd($rows);
		return $rows;
	}

	protected function getDeliveryTypes()
	{
		$result = ReestrType::all();
		foreach ($result as $item) {
			$rows[$item->id] = $item->short . ' - ' . $item->name;
		}

		return $rows;
	}

	protected function getReestrTypes($select = false)
	{
		$rows_select = Reestr::getTypeDoc();

		$rows['out'] = "<span class='fal fa-shopping-basket fa-md text-info fs-2' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $rows_select['out'] . "'></span>";
		$rows['in'] = "<span class='fal fa-truck fa-md text-alert fs-2' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $rows_select['in'] . "'></span>";

		return ($select ?  $rows_select :  $rows);
	}

	/**
	 * Подстановка и замена элементов в строке (подмена ячеек)
	 *
	 * @param  array $row
	 * @return void
	 */
	protected function parseRow($row)
	{
		$row['numb'] = $row['type_real'] . '-' . $row['numb_real'];
		$row['object'] = empty($row['object']) ? '<font color="red">Не указано</font>' : $row['object'];
		$row['oid'] = htmlspecialchars_decode($row['oid']);

		$row['action'] = view('grid::layouts.actions', [
			'row' => $row,
			'module' => 'reestr',
			'actions' => [
				'show' => true,
				'edit'  => true,
				'destroy' => true
			],
		])->render();

		return $row;
	}
}
