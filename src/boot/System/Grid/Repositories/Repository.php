<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Grid\Repositories;

use Boot\Support\Facades\DB;
use Boot\System\Grid\Exception\ExceptionGrid;
use Boot\System\Grid\Utils\Utils;

abstract class Repository
{
    public $rows = [];
    public $db;
    public $cols;
    public $limit;
    public $page;
    public $short;
    public $short_field;
    public $request;
    public $userdata;
    public $total;
    public $count;
    public $where;
    public $grid;

    protected $treegrid = false;

    protected $primary_key = [];
    protected $primary_key_glue = '_';
    protected $primary_key_auto_increment = null;


    /**
     * Конструктор репозитория
     *
     * @param  mixed  $parameter
     * @return void
     */
    public function __construct($parameter = [])
    {
        if (!empty($parameter)) {
            $this->setParameters($parameter);
        }
    }

    /**
     * Установка параметров
     *
     * @param  mixed  $parameter
     * @return void
     */
    public function setParameters($parameter)
    {
        $this->db = $parameter['db'];
        $this->cols = $parameter['cols'];
        $this->request = $parameter['request'];
        $this->grid = $parameter['grid'];
        $this->search();

        reset($this->cols);
        $this->primary_key = $this->primary_key ? (array) $this->primary_key : array(key($this->cols));
        if (is_null($this->primary_key_auto_increment)) {
            $this->primary_key_auto_increment = count($this->primary_key) == 1;
        }

        $this->short_field = $this->request->order[0]['column_name'] ?: $this->primary_key[0];
        $this->short = in_array(($this->request->order[0]['dir'] ?? 'asc'),
            array('asc', 'desc')) ? $this->request->order[0]['dir'] : 'asc';
        // dd($this->short_field, $this->short);
        $this->page = max(1, intval($this->request->page ?? 1));
        $this->limit = max(-1, intval($this->request->rows));
        $this->getDataAgg();
        $this->setRowCount();
    }

    /**
     * Выполнение поиска
     *
     * @return void
     */
    protected function search()
    {
        $columns = $this->request->columns;
        $request = [];
        foreach ($columns as $column) {
            $request[$column['data']] = $column;
        }

        foreach ($this->cols as $k => $c) {

            if (!isset($request[$k])) {
                continue;
            }

            if ($request[$k]['search']['value'] === null) {
                continue;
            }
            $val = $request[$k]['search']['value'];
            $val = $this->searchCleanVal($val);

            // if (is_array($val)) {
            //     foreach ($val as $kk => $vv) {
            //         Utils::checkAlphanum($kk);
            //         $val[$kk] = $this->searchCleanVal($vv);
            //     }
            // } else {
            //     $val = $this->searchCleanVal($val);
            // }

            $callback = array($this, Utils::uscore2camel('searchOp', $c['search_op']));

            if (!is_callable($callback)) {
                throw new ExceptionGrid('Search operation '.$c['search_op'].' is not defined');
            }

            $wh = call_user_func($callback, $c, $val);
            if ($wh) {
                $this->where[] = $wh;
            }
        }
        // dd($this->request->columns);
    }

    protected function searchCleanVal($val)
    {
        $val = trim($val);
        $val = "'".$val."'";
        $start = (mb_strpos($val, "E'") === 0) ? 2 : 1;
        $val = mb_substr($val, $start, -1);
        return $val;
    }

    protected function searchOpAuto($c, $val)
    {
        #Search type - select?
        if (isset($c['stype']) and $c['stype'] == 'select') {
            return self::searchOpEqual($c, $val);
        }

        #Numeric by formatter?
        if (isset($c['formatter']) and in_array($c['formatter'], array('integer', 'numeric', 'currency'))) {
            return self::searchOpNumeric($c, $val);
        }

        #Numeric by value?
        if (preg_match('#^([<>=!]{1,2})?\d+$#', $val)) {
            return self::searchOpNumeric($c, $val);
        }

        #Seems to be 'like'
        return self::searchOpLike($c, $val);
    }

    /**
     * (Output) Disable search
     *
     * @param $c  - column
     * @param $val  - value
     * @return bool
     */
    protected function searchOpIgnore($c, $val)
    {
        return false;
    }

    /**
     * (Output) Look for EXACT match
     *
     * @param $c  - column
     * @param $val  - value
     * @return string
     */
    protected function searchOpEqual($c, $val)
    {
        return ['column' => $c['db'], 'operator' => '=', 'value' => $val, 'field' => $c['field'] ?? null];
    }

    /**
     * (Output) Look for EXACT match
     *
     * @param $c  - column
     * @param $val  - value
     * @return string
     */
    protected function searchOpNull($c, $val)
    {
        if ($val == 0) {
            $operator = 'null';
        } else {
            $operator = 'not_null';
        }
        return ['column' => $c['db'], 'operator' => $operator, 'value' => $val, 'field' => $c['field'] ?? null];
    }

    /**
     * (Output) Look for similar text
     *
     * @param $c  - column
     * @param $val  - value
     * @return string
     */
    protected function searchOpLike($c, $val)
    {
        #Escape wildcards
        $val = addcslashes($val, '%_');
        $op = 'LIKE';

        return ['column' => $c['db'], 'operator' => $op, 'value' => '%'.$val.'%', 'field' => $c['field'] ?? null];
    }

    /**
     * (Output) Look for list of values
     *
     * @param $c  - column
     * @param $val  - value
     * @return string
     */
    protected function searchOpIn($c, $val)
    {
        $hash = array_map('trim', explode(',', $val));
        return ['column' => $c['db'], 'operator' => 'IN', 'value' => $hash, 'field' => $c['field'] ?? null];
    }

    /**
     * (Output) Look for numeric value
     * Supports prefixes to search for range of numeric values
     *
     * @param $c  - column
     * @param $val  - value
     * @return string
     */
    protected function searchOpNumeric($c, $val)
    {
        static $prefix = array('<=', '>=', '<>', '!=', '<', '>', '=');

        foreach ($prefix as $p) {
            if (strpos($val, $p) === 0) {
                $op = $p;
                $val = substr($val, strlen($p));
                break;
            }
        }

        $op = isset($op) ? $op : '=';
        $val = floatval(trim($val));
        return ['column' => $c['db'], 'operator' => $op, 'value' => $val, 'field' => $c['field'] ?? null];
    }

    # Поиск просто по дате
    protected function searchOpDate($c, $val)
    {
        //--------------
        // Single date (Только дата если в БД)
        //--------------

        // Обработка пустого значения
        if (empty($val)) {
            return null;
        }

        $val = strtotime(trim($val));

        if (!$val) {
            throw new ExceptionGrid('Invalid date format');
        }

        $val = date('Y-m-d', $val);

        return ['column' => $c['db'], 'operator' => '=', 'value' => $val];
    }

    # Поиск по диапазону дат
    protected function searchOpDateRange($c, $val)
    {
        //--------------
        // Date range
        //--------------

        if (strpos($val, ' - ') !== false) {
            list($start, $end) = explode(' - ', $val, 2);

            $start = strtotime(trim($start));
            $end = strtotime(trim($end));

            if (!$start or !$end) {
                throw new ExceptionGrid('Invalid date format');
            }

            #Stap dates if start is bigger than end
            if ($start > $end) {
                list($start, $end) = array($end, $start);
            }

            $start = date('Y-m-d', $start);
            $end = date('Y-m-d', $end);

            // return $c['db'] . " BETWEEN '$start' AND '$end'";
            return ['column' => $c['db'], 'operator' => 'between', 'value' => [$start, $end]];
        }
        if (strpos($val, '-') !== false) {
            $date = explode("-", $val);
            if (!empty($date[0])) {
                return ['column' => $c['db'], 'operator' => '>=', 'value' => date('Y-m-d', strtotime(trim($date[0])))];
            }
            if (!empty($date[1])) {
                return ['column' => $c['db'], 'operator' => '<=', 'value' => date('Y-m-d', strtotime(trim($date[1])))];
            }
        }

        //------------
        // Single date
        //------------

        $start = date('Y-m-d H:i:s', strtotime($val));
        $end = date('Y-m-d H:i:s', strtotime($val.' + 1 days'));

        if (!$start or !$end) {
            throw new ExceptionGrid('Invalid date format');
        }

        // return $c['db'] . " BETWEEN '$start' AND '$end'";
        return ['column' => $c['db'], 'operator' => 'between', 'value' => [$start, $end]];
    }

    # Загрузка строк
    public function getDataRows()
    {
        $db = $this->buildRows($this->db);
        // dd($db->toSql());
        foreach ($db->get() as $r) {
            // dd($r);
            $this->addRow((array) $r);
        }
        // dd($this->raws);
        return $this->rows;
    }

    /**
     * (Output) Get AGG data - over the whole result set
     * Save it to $this->agg
     */
    protected function getDataAgg()
    {
        $db = $this->buildWhere($this->db);
        $result = ['_count' => $db->count()];
        $this->agg = $result;
    }

    # ...
    public function getUserData()
    {
        if ($this->agg) {
            $this->userdata['agg'] = $this->agg;
        }
        return $this->userdata;
    }

    # ...
    public function getRecords()
    {
        return $this->count;
    }

    # ...
    public function getTotal()
    {
        return $this->total;
    }


    # ...
    protected function setRowCount($count = null)
    {
        if (is_null($count)) {
            if (isset($this->agg['_count'])) {
                $count = $this->agg['_count'];
            } else {
                $count = count($this->rows);
            }
        }

        $count = intval($count);

        if ($count < 0) {
            throw new ExceptionGrid('Invalid count value');
        }

        if (!$count) {
            $this->count = 0;
            $this->page = 0;
            $this->total = 0;
            $this->offset = 0;

            return;
        }

        $this->count = $count;

        if ($this->limit == -1) {
            $this->total = 1;
            $this->page = 1;
            $this->offset = 0;
        } elseif ($this->limit) {
            $this->total = ($this->count > 0) ? ceil($this->count / $this->limit) : 0;

            $this->page = ($this->page <= $this->total) ? $this->page : $this->total;
            $this->offset = $this->limit * $this->page - $this->limit;
        }
    }

    # ...
    protected function buildRows($db)
    {
        $db = $this->buildWhere($db);
        $limit = intval($this->limit);
        $page = intval($this->page);
        //Установка сортировки
        if ($this->short_field && $this->short) {
            $db->orderBy($this->short_field, $this->short);
        }
        // dd($this->request->length);
        //установка limit и offset для постраничного вывода
        if ($this->request->length) {
            $db->offset($this->request->start);
            $db->limit($this->request->length);
        }
        return $db;
    }

    # ...
    protected function buildWhere($db)
    {
        //выборка полей для вывода
        $fields = array();
        foreach ($this->cols as $k => &$c) {
            if ($c['manual']) {
                continue;
            }
            if ($c['db'] instanceof \Illuminate\Database\Query\Expression) {
                $fields[] = $c['db'];
            } else {
                $fields[] = ($k == $c['db']) ? $c['db'] : ($c['db'].' AS '.$k);
            }
        }
        $db->select($fields);

        //установка фильтров
        if (!empty($this->where)) {

            foreach ($this->where as $k => $v) {
                if ($v['column'] instanceof \Illuminate\Database\Query\Expression) {
                    $v['column'] = $v['field'];
                }
                // dump($v);
                if ($v['value'] != "GRIDSELECT-ALL") {
                    switch (mb_strtolower($v["operator"])) {
                        default:
                            $db->where($v['column'], $v['operator'], $v['value']);
                            break;
                        case "between":
                            $db->whereBetween($v['column'], $v['value']);
                            break;
                        case "null":
                            $db->whereNull($v['column']);
                            break;
                        case "not_null":
                            $db->whereNotNull($v['column']);
                            break;
                    }
                }
            }
            // dd($db->toSql());
        }
        return $db;
    }


    # ...
    protected function implodePrimaryKey($row)
    {
        foreach ($this->primary_key as $k) {
            $pk_parts[] = $row[$k];
        }

        return implode($this->primary_key_glue, $pk_parts);
    }

    # ...
    protected function addRow($row)
    {
        #Allow modification of result row without annoying overloading of 'addRow'
        $row = $this->parseRow($row);

        $id = $this->implodePrimaryKey($row);
        $cell = array();

        //----------------
        // Fill cells
        //----------------

        foreach ($this->cols as $k => $c) {
            #Discard this column in output?
            if ($c['unset']) {
                continue;
            }
            if (!empty($c['format_date'])) {
                $row[$k] = empty($row[$k]) ? ($row["null"] ?? $row[$k]) : date($c['format_date'], strtotime($row[$k]));
            }

            #Parse each cell
            $cell[$c['name']] = $this->addRowCell($c, $row);
        }
        // dump($row);

        #Prase whole row
        $cell = $this->addRowComplete($id, $cell, $row);
        // dd($cell);
        $this->rows[] = $cell;
    }

    /**
     * (Output) Per-row result modification
     *
     * @param  integer  $id  row_id
     * @param  array  $cell  - parsed cells
     * @param  array  $row  - data row
     * @return array - modified $cell
     */
    protected function addRowComplete($id, $cell, $row)
    {
        //----------------
        // TreeGrid handling
        //----------------

        if ($this->treegrid == 'adjacency') {
            $cell[] = $row['level'];
            $cell[] = $row['parent'];
            $cell[] = $row['isLeaf'];
            $cell[] = $row['expanded'];
            $cell[] = isset($row['loaded']) ? $row['loaded'] : false;
        }

        if ($this->treegrid == 'nested') {
            $cell[] = $row['level'];
            $cell[] = $row['lft'];
            $cell[] = $row['rgt'];
            $cell[] = $row['isLeaf'];
            $cell[] = $row['expanded'];
            $cell[] = isset($row['loaded']) ? $row['loaded'] : false;
        }

        //----------------
        // Send all the vars beginning with '_' to userdata!
        //----------------
        // $data = $this->nav;
        // //echo $_COOKIE[$this->grid_id.'Trash'];
        // if (isset($data['trash']) && $data['trash']) {
        //     if (isset($_COOKIE[$this->grid_id . 'Trash']) && !empty($_COOKIE[$this->grid_id . 'Trash']) && $_COOKIE[$this->grid_id . 'Trash'] == '1') {
        //         $trash_field = explode(".", $data["trashfield"]);
        //         $trash_field = $trash_field[1] ?? $trash_field[0];
        //         if (isset($data['trashtype']) && $data['trashtype'] == 'null') {
        //             if (!empty($row[$trash_field])) {
        //                 $cols_class = 'text-danger';
        //                 $row['_class'] = $cols_class;
        //             }
        //         } else {
        //             if ($row[$trash_field] == 1) {
        //                 $cols_class = 'text-danger';
        //                 $row['_class'] = $cols_class;
        //             }
        //         }
        //     }
        // }


        foreach ($row as $k => $v) {
            if (strpos($k, '_') === 0) {
                $this->userdata[$k][$id] = $v;
            }
        }
        // dd($this->userdata);
        return $cell;
    }

    /**
     * (Output) Populate cell
     *
     * @param  array  $c  - column options
     * @param  array  $row  - data row
     * @return mixed - final cell value
     */
    protected function addRowCell($c, $row)
    {
        $val = isset($row[$c['name']]) ? $row[$c['name']] : null;

        #Handle nulls
        if ($val === null and $c['null'] !== null) {
            $val = $c['null'];
        }

        #Easy replace values
        if ($c['replace']) {
            $val = isset($c['replace'][$val]) ? $c['replace'][$val] : $val;
        }
        // WIP
        //if (is_array($c['replace'])) {
        //    $val = isset($c['replace'][$val]) ? $c['replace'][$val] : $val;
        //} else {
        //    //$val = isset($c['replace']) ? $c['replace'] : $val;
        //}

        #Encode before output
        if ($c['encode']) {
            $val = $this->outputEncodeValue($c, $val);
        }

        #Замена лишних пробелов на одинарные (Читабельность JSON) - WIP
        //$val = preg_replace('/\s{2,}/', ' ', $val);

        return $val;
    }


    /**
     * (Output) Encode each row value before output
     *
     * @param $c  - column
     * @param $val  - value
     * @return string - encoded value
     */
    protected function outputEncodeValue($c, $val)
    {
        return htmlspecialchars($val, ENT_QUOTES);
    }


    /**
     * Подготовка строк
     *
     * @param  mixed  $raw
     * @return void
     */
    protected function parseRow($raw)
    {
        return $this->grid->parseRowGrid($raw);
    }


    # ...
    protected function buildQueryAgg($q)
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

    /**
     * (Output) Build complete ROWS-query
     *
     * @param  string  $q  - base query
     * @return string - final query
     */
    protected function buildQueryRows($q)
    {
        //-----------------
        // Placeholders
        //-----------------
        // dd($this->where);
        $replace = array(
            $this->query_placeholders['where'] => $this->buildWhere($this->where, $this->where_glue, 'rows'),
            $this->query_placeholders['fields'] => $this->buildFields($this->cols),
        );

        $q = strtr($q, $replace);

        //-----------------
        // ORDER BY, LIMIT, OFFSET
        //-----------------

        if ($this->do_sort) {
            $q .= $this->buildOrderBy($this->sidx, $this->sord)."\n";
        }
        if ($this->do_limit) {
            $q .= $this->buildLimitOffset($this->limit, $this->page)."\n";
        }

        return $q;
    }

    /**
     * (Output) Implode {fields} for ROWS-query using 'db' property
     *
     * @param  array  $cols  - grid columns
     * @return string - imploded list
     */
    protected function buildFields($cols)
    {
        $fields = array();

        foreach ($cols as $k => &$c) {
            if ($c['manual']) {
                continue;
            }

            $fields[] = ($k == $c['db']) ? $c['db'] : ($c['db'].' AS '.$k);
        }

        return implode(', ', $fields);
    }

    /**
     * (Output) Implode {fields} for AGG-query using 'db_agg' property
     *
     * @param  array  $cols  - grid columns
     * @return string - imploded list
     */
    protected function buildFieldsAgg($cols)
    {
        #Count should always be here!
        $fields = array('count(*) AS _count');

        foreach ($cols as $k => $c) {
            if (!$c['db_agg']) {
                continue;
            }

            switch ($c['db_agg']) {
                #Common
                case 'sum':
                case 'avg':
                case 'count':
                case 'min':
                case 'max':
                    $fields[] = $c['db_agg'].'('.$c['db'].') AS '.$k;
                    break;

                #Custom
                default:
                    $fields[] = $c['db_agg'].' AS '.$k;
                    break;
            }
        }

        return implode(', ', $fields);
    }

    /**
     * (Output) Builds paging for ROWS-query
     * LIMIT x OFFSET y
     *
     * Alter this if your database requires other syntax
     *
     * @param  integer  $limit
     * @param  integer  $page
     * @return string
     */
    protected function buildLimitOffset($limit, $page)
    {
        $limit = intval($limit);
        $page = intval($page);

        if ($limit > 0 and $page > 0) {
            $offset = ($page * $limit) - $limit;
            return "LIMIT $limit OFFSET $offset";
        }

        return '';
    }

    /**
     * (Output) Builds sorting for ROWS-query
     * Overload it to introduce more complex
     *
     * Input is checked in 'getOutputParams', so we trust it
     *
     * @param  string  $sidx  - field name
     * @param  string  $sord  - order (asc, desc)
     * @return string
     */
    protected function buildOrderBy($sidx, $sord)
    {
        if ($sidx and $sord) {
            return "ORDER BY $sidx $sord";
        }

        return '';
    }


    /**
     * (Output) Generate default query if only $this->table was set
     *
     * @return string
     */
    protected function getDefaultDB()
    {
        if (empty($this->table)) {
            return '';
        }

        return DB::table($this->table);
    }

    # ...
    protected function getDefaultQuery()
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
}
