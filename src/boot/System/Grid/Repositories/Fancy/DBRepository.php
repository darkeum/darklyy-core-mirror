<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Grid\Repositories\Fancy;

use Exception;
use Boot\Support\Facades\DB;

class DBRepository extends RepositoryFancy
{
	public $default;

	public $db;

	public $table;

	public $query;

	public $where;

	public $cols;

	public function setDatabase($db)
	{
		$this->db = $db;
	}

	public function setTable($table)
	{
		$this->table = $table;
	}

	public function setWhere($where)
	{
		$this->where = $where;
	}

	public function setCols($cols)
	{
		$this->cols = $cols;
	}

	public function getDefault()
	{
		if (empty($this->db)) {
			if (empty($this->table)) {
				throw new Exception("Таблица не указана");
			}
			$this->db = DB::table($this->table);
		}
	}
}
