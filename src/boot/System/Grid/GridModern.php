<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Grid;

use Boot\Models\Users;
use Boot\Support\Facades\Auth;
use Boot\Support\Facades\DB;
use Boot\Support\Str;
use Boot\System\Database\Model\Builder as ModelBuilder;
use Boot\System\Grid\Data\Raw;
use Boot\System\Grid\Exception\ExceptionGrid;
use Boot\System\Grid\Repositories\DBRepository;
use Boot\System\Grid\Repositories\ModelRepository;
use Boot\System\Grid\Repositories\QueryRepository;
use Boot\System\Grid\Repositories\Repository;
use Boot\System\Grid\Traits\RenderTrait;
use Boot\System\Grid\Utils\Utils;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

abstract class GridModern
{
    use RenderTrait;

    protected Repository $repository;
    protected
        $grid_id,
        $request,
        $count = null,
        $db = null,
        $raws,
        $title,
        //$cols_default = array(),
        $out = 'json',
        $internal_col_prop = array('db', 'db_agg', 'unset', 'manual', 'search_op'),
        $default;

    public $options, $cols;

    /**
     * Конструктор класса
     *
     * @return void
     * @throws ExceptionGrid
     */
    public function __construct()
    {
        //
        $this->default = config('grid.default');
        $this->beforeInit();
        $this->init();
        $this->afterInit();
        $this->prepareColumn();

    }

    /**
     * Инициализация Grid
     *
     * @return void
     */
    abstract protected function init();

    /**
     * Действие перед инициализаций
     *
     * @return void
     */
    protected function beforeInit()
    {
    }

    /**
     * Действие после инициализации
     *
     * @return void
     */
    protected function afterInit()
    {
    }

    /**
     * Установка идентификатора
     *
     * @param  mixed  $grid_id
     * @return void
     */
    public function setGridId($grid_id)
    {
        $this->grid_id = $grid_id;
    }

    /**
     * Подготовка колонок
     *
     * @return void
     * @throws ExceptionGrid
     */
    protected function prepareColumn()
    {
        $this->reserved_col_names = $this->getReservedColNames();
        foreach ($this->cols as $k => &$c) {
            $c = $this->initColumn($k, $c);
        }
    }

    /**
     * Вывод ответа на запрос
     *
     * @param  mixed  $request
     * @return array
     * @throws Exception
     */
    public function output($request)
    {
        $this->request = $request;

        $this->getCheckOrgs(); // Закоментить если не надо разделять по пользователям и организациям
        $this->getOutputParams();
        $this->getRepository();
        $this->getRepositoryRows();
        $this->getRepositoryUserData();
        $this->getRepositoryRecords();
        $this->getRepositoryTotal();

        return $this->outDataTable();
    }

    /**
     * Получение построителя запросов
     *
     * @return void
     * @throws Exception
     */
    protected function getRepository()
    {
        if ($this->db == null) {
            $this->repository = new QueryRepository();
        } elseif ($this->db instanceof ModelBuilder) {
            $this->repository = new ModelRepository();
        } elseif ($this->db instanceof Builder) {
            $this->repository = new DBRepository();
        } else {
            throw new Exception("Неизвестный тип построения запросов");
        }

        $this->repository->setParameters([
            'db' => $this->db,
            'cols' => $this->cols,
            'request' => $this->request,
            'grid' => $this,
        ]);

    }

    #
    protected function getRepositoryRows()
    {
        $this->rows = $this->repository->getDataRows();
    }

    #
    protected function getRepositoryUserData()
    {
        $this->userdata = $this->repository->getUserData();
    }

    #
    protected function getRepositoryRecords()
    {
        $this->records = $this->repository->getRecords();
    }

    #
    protected function getRepositoryTotal()
    {
        $this->total = $this->repository->getTotal();
    }

    /**
     * Подготовка строк
     *
     * @param  mixed  $raw
     * @return void
     */
    public function parseRowGrid($raw)
    {
        return $this->parseRow($raw);
    }

    /**
     * Подготовка строк
     *
     * @param  mixed  $raw
     * @return void
     */
    protected function parseRow($raw)
    {
        return $raw;
    }

    /**
     * Получение и проверка доступа к материалам по организации пользователя
     *
     * @return void
     */
    protected function getCheckOrgs()
    {
        $user = Auth::user();
        //$user = Users::find(1);
        //$this->cols['org']['visible'] = false; // Не робит

        if ($this->db and array_key_exists('uid', $this->cols) or array_key_exists('org', $this->cols)) {

            if (array_key_exists('uid', $this->cols)) {
                $uid_db = $this->cols['uid']['db'];
                $this->db = $this->db->where($uid_db, $user->id)->orWhereNull($uid_db);
            }

            if (array_key_exists('org', $this->cols)) {
                $orgs = $user->orgs->pluck('id');
                $org_db = $this->cols['org']['db'];

                if ($orgs->isNotEmpty()) {
                    $this->db = $this->db->whereIn($org_db, $orgs->toArray())->orWhereNull($org_db);
                } else {
                    //$this->db = $this->db->where(1, 0);
                }
            }
        }
    }

    /**
     * Получение параметров запроса
     *
     * @return void
     */
    protected function getOutputParams()
    {
        $this->out = $this->request->_out ?? 'json';
    }

    /**
     * Получение зарезервированных имен
     *
     * @return array
     */
    protected function getReservedColNames()
    {
        $names = $this->default['reserved_col_names'];
        $names = array_merge($names, array('parent', 'level', 'isLeaf', 'lft', 'rgt', 'expanded', 'loaded'));
        return $names;
    }

    /**
     * Инициализация колонок
     *
     * @param  mixed  $k
     * @param  mixed  $c
     * @return array
     * @throws ExceptionGrid
     */
    protected function initColumn($k, $c)
    {
        #Check reserved keys
        if (in_array($k, $this->reserved_col_names)) {
            throw new ExceptionGrid("Column name '$k' reserved for internal usage!");
        }

        if (strpos($k, '_') === 0) {
            throw new ExceptionGrid("Column name '$k' must NOT begin with underscore!");
        }

        #Name and index always match the array key
        $c['name'] = $k;
        $c['index'] = $k;

        # Начало фильтрации *****************************************************
        include('Utils/Filters.php');

        #Merge with defaults
        //$c = array_merge($this->default['cols'], $this->cols_default, $c);
        $c = array_merge($this->default['cols'], $c);

        #DB = column key if not set
        //$c['db'] = $c['db'] ? $c['db'] : $k;
        $c['db'] = $c['db'] ?? $k;

        return $c;
    }

    # Задание значений в select2 для Grid
    protected function setValue($data, $first = false, $column = false)
    {
        // Старый вариант
        //return new Value($data, $first);

        //dd($data);

        // Создадим пустую коллекцию
        $result = collect([]);

        // Если массив добавим его в коллекцию
        if (is_array($data)) {
            $result = collect($data);
        }

        // Если уже подана коллекция из класса
        if ($data instanceof Collection) {
            $result = $data;
        }

        // Если подан Class то будем использовать запрос к Class'у
        if (is_string($data) and class_exists($data)) {
            //dd(get_declared_classes());
            //dd(class_exists($data));
            $data = route('grid.ui.select2', ['model' => $data, 'col' => $column]);
            $result->prepend($data, "GRID-SELECT-AJAX");
        }

        // Задание первого значения в select2 для Grid
        if (is_bool($first) and $first) {
            $result->prepend(__('grid::lang.cols.all'), "GRID-SELECT-ALL");
        }

        //
        if (is_string($first)) {
            $result->prepend($first, "GRID-SELECT-ALL");
        }

        // Если массив
        if (is_array($first) and !empty($first)) {
            $first = collect($first);

            $custom = $first->toJson(JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
            //$custom = $first->toJson();
            $result->prepend($custom, "GRID-SELECT-CUSTOM");
            $result->prepend($first->take(1)->keys()->first(), $first->take(1)->values()->first());
        }

        //dd($result);

        return $result->toJson(JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
    }

    #
    protected function setRaw($data)
    {
        return new Raw($data);
    }

    #
    protected function outJson()
    {
        $data = array(
            'page' => max(1, intval($this->request->page ?? 1)),
            'total' => $this->total ?? 0,
            'records' => $this->records ?? 0,
            'rows' => $this->rows ?? 0,
            'userdata' => $this->userdata ?? 0,
        );

        if (config('grid.debug_output')) {
            $data['debug'] = $this->debug ?? 0;
        }
        return $data;
    }

    #
    protected function outDataTable()
    {
        $data = array(
            'draw' => $this->request->draw ?? 1,
            'recordsTotal' => $this->records ?? 0,
            'recordsFiltered' => $this->records ?? 0,
            'data' => $this->rows ?? 0,
        );

        if (config('grid.debug_output')) {
            $data['debug'] = $this->debug ?? 0;
        }

        return $data;
    }

    #
    public function dateRangeSearch()
    {
        return array(
            'dataInit' => $this->initDateRangePicker(array(
                'dateFormat' => 'dd.mm.yy',
                'onChange' => new Raw('dateRangePicker_onChange'),
                'presetRanges' => array(
                    array('text' => 'За 2016 год', 'dateStart' => '2016-01-01', 'dateEnd' => '2016-12-31'),
                    array('text' => 'За 2017 год', 'dateStart' => '2017-01-01', 'dateEnd' => '2017-12-31'),
                    array('text' => 'За 2018 год', 'dateStart' => '2018-01-01', 'dateEnd' => '2018-12-31'),
                    array('text' => 'За 2019 год', 'dateStart' => '2019-01-01', 'dateEnd' => '2019-12-31'),
                    array('text' => 'За 2020 год', 'dateStart' => '2020-01-01', 'dateEnd' => '2020-12-31'),
                    array('text' => 'За 2021 год', 'dateStart' => '2021-01-01', 'dateEnd' => '2021-12-31'),
                ),
                'datepickerOptions' => array(
                    'changeMonth' => true,
                    'changeYear' => true,
                    'dateFormat' => 'dd.mm.yy',
                ),
            ))
        );
    }

    #
    protected function initDateRangePicker($options = null)
    {
        $options = is_array($options) ? $options : array();
        return new Raw('function(el){$(el).daterangepicker('.Utils::jsonEncode($options).');}');
    }

    #
    protected function getOrg($table, $column = 'org')
    {
        $list = DB::table($table)->leftJoin('org', 'org.id', '=', $table.'.'.$column)
            ->leftJoin('organizations',
                'organizations.id', '=', 'org.oid')
            ->distinct()
            ->select("organizations.*", "org.id as org_id")
            ->pluck('name', 'org_id')
            ->filter()
            //->map(function ($item){
            //    return trim($item);
            //})
            ->toArray();

        return $list;
    }

    # Получаем производителей
    protected function getManufacture($table, $column)
    {
        $list = DB::table($table)
            ->leftJoin('catalog_manufacture as cm', 'cm.id', '=',
                $table.'.'.$column)
            ->distinct()
            ->select("cm.*")
            ->orderBy("cm.name")
            ->pluck('name', 'id')
            ->filter()
            ->map(function ($item){
                return trim($item);
            })
            ->toArray();

        return $list;
    }

    # Получаем список категорий
    protected function getCategories($func_id = null, $empty = false)
    {
        $rows = [];
        $cats_arr = \Boot\Support\Facades\Categories::getNameTree('catalog_v2');
        //if ($empty) {
        //    $rows[''] = 'Без категории';
        //}
        $rows[''] = 'Без категории';
        foreach ($cats_arr as $cid => $name) {
            $id = $cid;
            if (!empty($func_id) && is_callable($func_id)) {
                $id = $func_id($cid);
            }
            $rows[$id] = $name;
        }

        return $rows;
    }

    #
    protected function getCategoriesOrigin($func_id = null)
    {
        $rows = [];
        $array = DB::table('categories')->get();

        foreach ($array as $item) {
            $cats_arr[$item->id] = $item->name;
        }
        $rows[''] = 'Без категории';

        foreach ($cats_arr as $cid => $name) {
            $id = $cid;
            if (!empty($func_id) && is_callable($func_id)) {
                $id = $func_id($cid);
            }
            $rows[$id] = $name;
        }

        return $rows;
    }

    /**
     * WIP Потом надо будет переделать (Пока пользователей мало это работает +- нормально но потом может тянуть время загрузки)
     *
     * @param $table
     * @param $column
     * @return array
     */
    protected function getUid($table = null, $column = 'uid')
    {
        $rows = [];
        $rows["0"] = "Гость";
        if (!empty($table)) {
            $users = DB::table($table)->leftJoin('users', 'users.id', '=',
                $table.'.'.$column)->distinct()->orderBy("users.surname")->select($table.'.'.$column)->get();
            foreach ($users as $k => $v) {
                $rows[$v->$column] = shortUserName($v->$column);
            }
        } else {
            $users = DB::table('users')->orderBy("surname")->select('id')->get();
            foreach ($users as $k => $v) {
                $rows[$v->id] = shortUserName($v->id);
            }
        }
        return $rows;
    }

    /**
     * @param  string  $table
     * @param  string  $column
     * @param  string  $orderBy
     * @return array
     */
    protected function getValues(string $table, string $column = 'numb', string $orderBy = 'id'): array
    {
        $rows = [];
        if ($table) {
            $numbs = DB::table($table.' as t')
                ->select('t.'.$column)
                ->orderBy('t.'.$orderBy, 'desc')
                ->distinct()
                ->get();

            foreach ($numbs as $v) {
                $str = Str::replace($v->$column, '"', '\"');
                $rows[$str] = (string) $v->$column;
            }
        }
        return $rows;
    }

    # WIP Получение данных из БД
    protected function getIDs($table = null, $column = 'id', $ids = false, $order = 'id')
    {
        if ($table) {
            if ($ids) {
                $users = DB::table($table)->whereIn($column, $ids->unique())
                    ->orderBy($order)
                    ->get();
            } else {
                $users = DB::table($table)->get();
            }
        } else {
            $users = false;
        }

        return $users;
    }

    /**
     * Получение данных пользователя
     *
     * @param  Collection  $collection
     * @param  string  $groupBy
     * @param  null  $key
     * @return Collection
     */
    protected function getFilterParam(Collection $collection, string $groupBy = 'id', $key = null): Collection
    {
        return $collection->groupBy($groupBy)->transform(function ($u) use ($key) {
            return ($key)
                ? $u->first()->$key
                : $u->first();
        });
    }

}
