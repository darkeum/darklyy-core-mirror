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

interface RepositoryInterface {


	public function buildQueryRows($q);

	public function buildFields($cols);

	public function buildFieldsAgg($cols);

	public function buildLimitOffset($limit, $page);

	public function buildOrderBy($sidx, $sord);

	public function buildWhere($where, $glue, $type);

	public function getDefaultQuery();

	public function buildQueryAgg($q);

}
