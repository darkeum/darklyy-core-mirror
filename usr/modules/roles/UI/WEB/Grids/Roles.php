<?php


namespace Roles\UI\WEB\Grids;

use Boot\System\Database\DB;
use Decrees\Tasks\TypesTask;
use Support\Tasks\StatusTask;
use Boot\Support\Facades\Auth;
use Boot\System\Grid\GridModern;
use Boot\Models\Users;

class Roles extends GridModern
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
    protected $namespace = 'users';

    /**
     * Действие на двойной клик
     *
     * @var string
     */
    // protected $opts_type = "open";

    /**
     * Получение URL на открытие записи по двойному клику
     *
     * @return void
     */
    // protected function getOpenUrl()
    // {
    // 	return route("admin.support.show", ["id" => "@id"]);
    // }

    /**
     * Инициализация grid
     *
     * @return void
     */
    protected function init()
    {
        //Заголовок таблицы
        // $this->title = __('roles::lang.index');
        $this->title = 'Роли пользователей';

        // Опции таблицы (в данном случае сортировка)
        $this->options = [
            'sort' => [
                'name' => 'numb',
                // 'order' => 'DESC'
                'order' => 'ASC'
            ],
        ];


        // Опции панели навигации
        $this->nav = [
            //'excel' => true,
            //'exceltext' => __('grid.export.excel'),
            //'trash' => true,
            //'trashtype' => 'null',
            //'trashtext' => __('grid.trash.show'),
            //'trashfield' => $this->namespace . '.deleted_at'
        ];

        //Подключение к необходимой таблице
        $this->db = DB::table($this->namespace);

        //Настройка колонок
        $this->cols = array(

            'id' => array(
                'db' => $this->namespace . '.id',
                'visible' => false,
            ),

            'numb' => array(
                'title' => '№',
                'label' => '#',
                'db' => $this->namespace . '.id',
                'width' => '2%',
                'className' => 'text-center px-1',

                // query sql
                'search_op' => 'like',
            ),

            'title' => array(
                'title' => 'Имя',
                'db' => $this->namespace . '.name',
                'width' => '10%',
                'null' => "Отсутствует",
                // 'className' => 'text-center px-1',
            ),

            'group' => array(
                'title' => 'Группа',
                'db' => $this->namespace . '.group',
                'width' => '10%',
                'null' => "Отсутствует",
                'className' => 'text-center px-1',
            ),

            'active' => array(
                'title' => 'Статус',
                'db' => $this->namespace . '.active',
                'width' => '2%',
                'null' => '0',
                'className' => 'text-center px-1',

            ),

            // 'action' => array(
            //     'title' => __('grid.action'),
            //     'type' => 'actions',
            //     'width' => '1%',
            //
            //
            //     'manual' => true,
            //     'encode' => false,
            //     'sortable' => false,
            //     'search' => false,
            //     'excel_hidden' => true,
            //
            //     // query sql
            //     'search_op' => false,
            // ),

        );
        $this->render_filter_toolbar = true;
    }

    /**
     * Получение типов приказов
     *
     * @param bool $select
     * @return void
     */
    // protected function getTypes($select = false)
    // {
    // 	$rows_select = [];
    // 	$rows = app(TypesTask::class)->run(null, 'default');
    // 	$rows_select = app(TypesTask::class)->run(null, 'icon');
    // 	return ($select ? $rows : $rows_select);
    // }

    // /**
    //  * Получение статуса приказов
    //  *
    //  * @param  bool $select
    //  * @return void
    //  */
    // protected function getStatus($select = false)
    // {
    // 	$rows_select = [];
    // 	$rows = app(StatusTask::class)->run(null, 'default');
    // 	$rows_select = app(StatusTask::class)->run(null, 'badge');
    // 	return ($select ? $rows : $rows_select);
    // }

    /**
     * Подстановка и замена элементов в строке (подмена ячеек)
     *
     * @param  array $row
     * @return array
     */
    protected function parseRow($row)
    {
        $user = Users::find($row['id']);
        $row['title'] = user_short_name($user, true);

    	// $row['action'] = (string)view('support::manager.grid.action', [
    	//     'row' => $row,
    	//     'show' => router("admin.support.show",  $row['id']),
    	// ]);
    	// if (is_date($row["date"], "Y-m-d")) $row["date"] = date("d.m.Y", strtotime($row["date"]));

    	return $row;
    }
}
