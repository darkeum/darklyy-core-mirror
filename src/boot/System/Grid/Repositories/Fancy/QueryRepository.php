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

class QueryRepository implements RepositoryInterface
{

	public function buildQueryRows($q)
	{
		$replace = array(
			$this->query_placeholders['where'] => $this->buildWhere($this->where, $this->where_glue, 'rows'),
			$this->query_placeholders['fields'] => $this->buildFields($this->cols),
		);

		$q = strtr($q, $replace);

		if ($this->do_sort) $q .= $this->buildOrderBy($this->sidx, $this->sord) . "\n";
		if ($this->do_limit) $q .= $this->buildLimitOffset($this->limit, $this->page) . "\n";

		return $q;
	}

	public function buildFields($cols)
    {
        $fields = array();

        foreach ($cols as $k => &$c) {
            if ($c['manual']) continue;

            $fields[] = ($k == $c['db']) ? $c['db'] : ($c['db'] . ' AS ' . $k);
        }

        return implode(', ', $fields);
    }

    public function buildFieldsAgg($cols)
    {
        #Count should always be here!
        $fields = array('count(*) AS _count');

        foreach ($cols as $k => $c) {
            if (!$c['db_agg']) continue;

            switch ($c['db_agg']) {
                    #Common
                case 'sum':
                case 'avg':
                case 'count':
                case 'min':
                case 'max':
                    $fields[] = $c['db_agg'] . '(' . $c['db'] . ') AS ' . $k;
                    break;

                    #Custom
                default:
                    $fields[] = $c['db_agg'] . ' AS ' . $k;
                    break;
            }
        }

        return implode(', ', $fields);
    }

    public function buildLimitOffset($limit, $page)
    {
        $limit = intval($limit);
        $page = intval($page);

        if ($limit > 0 and $page > 0) {
            $offset = ($page * $limit) - $limit;
            return "LIMIT $limit OFFSET $offset";
        }

        return '';
    }

    public function buildOrderBy($sidx, $sord)
    {
        if ($sidx and $sord) {
            return "ORDER BY $sidx $sord";
        }

        return '';
    }

    public function buildWhere($where, $glue, $type)
    {
        return $where ? implode($glue, $where) : $this->where_empty;
    }

    public function getDefaultQuery()
    {
        if (empty($this->table)) {
            return '';
        }

        return "
			SELECT {fields}
			FROM {$this->table}
			WHERE {where}
		";
    }

	public function buildQueryAgg($q)
    {

        //-----------------
        // Placeholders
        //-----------------
        $replace = array(
            $this->query_placeholders['where'] => $this->buildWhere($this->where, $this->where_glue, 'agg'),
            $this->query_placeholders['fields'] => $this->buildFieldsAgg($this->cols),
        );

        $q = strtr($q, $replace);

        return $q;
    }
}
