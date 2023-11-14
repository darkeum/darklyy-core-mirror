<?php

namespace News\Grid;

use \Boot\System\Grid\Grid;
use \Boot\System\Database\DB;

class IndexGrid extends Grid
{
    protected $db_type = 'db';

    protected function init()
    {
        $this->options = array(
            'sortname' => 'news.id',
            'sortorder' => 'DESC',
        );

        // $this->nav = array(
        //     'excel' => true,
        //     'exceltext' => 'Сохранить в Excel',
        //     'trash' => true,
        //     'trashtype' => 'null',
        //     'trashtext' => 'Показывать удаленные',
        //     'trashfield' => 'r.deleted_at',
        // );

        $this->db = DB::table('news')->leftJoin('langs', function ($join) {
            $join->on('langs.postId', '=', 'news.id');
            $join->on('langs.module', '=', DB::raw("'news'"));
        });

        $this->cols = array(

            'id' => array(
                'hidden' => true,
                'db' => 'news.id',
            ),

            'numb' => array(
                'db' => 'news.id',
                'label' => '№',
                'width' => 10,
                'hidden' => false,
                'search_op' => 'like'
            ),

            'title' => array(
                'db' => 'langs.title',
                'label' => 'Заголовок',
                'width' => 30,
                'hidden' => false,
                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            'uid' => array(
                'label' => 'Ответственный',
                'db' => 'news.uid',
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getUid('news'),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getUid('news'), 'Все'),
                ),
                'search_op' => 'equal',
            ),

            'create_at' => array(
                'label' => 'Дата создания',
                'db' => 'news.create_at',
                'width' => 25,
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'published_at' => array(
                'label' => 'Дата публикации',
                'db' => 'news.published_at',
                'width' => 25,
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'category' => array(
				'label' => 'Категории',
				'width' => 30,
				'db' => 'news.category',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getCategoryTypes(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' =>  $this->setValue($this->getCategoryTypes(), 'Все'),
                ),
                'search_op' => 'equal',
            ),

            'status' => array(
                'label' => 'Статус',
                'db' => 'news.active',
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'replace' => $this->getStatus('render'),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getStatus('select'), 'Все'),
                ),
                'search_op' => 'equal',
            ),

            'ab' => array(
                'label' => 'Действия',
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
            $rows[0] = "Отключена";
            $rows[1] = "Опубликована";
        } elseif ($type == 'render') {
            $rows[0] = "<span class='far fa-circle text-danger fa-md' data-toggle='tooltip' title='Отключена'></span>";
            $rows[1] = "<span class='far fa-check-circle text-success fa-md' data-toggle='tooltip' title='Опубликована'></span>";
        }
        return $rows;
    }

    protected function getCategoryTypes() {
		global $core;
        $rows = array();   
		foreach ($core->aCatList('news') as $cid => $name) {
			$rows[$cid] = $name;
        }
        return $rows;
    }

    protected function parseRow($r)
    {
        $r["numb"] = $r["id"];
        $r['ab'] = '<div class="btn-group">
								<button type="button" onclick="location.href = \'/administration/module/news/'.$r['id'].'/edit/\'" class="btn btn-xs btn-primary">Открыть</button>
								<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only"></span></button>
								<ul role="menu" class="dropdown-menu">
									<li><a href="javascript:void(0);" onclick="modal_o(\'#modal-ok-' . $r['id'] . '\')"></a></li>
									<li><a href="javascript:void(0);" onclick="modal_o(\'#modal-comment-' . $r['id'] . '\')"></a></li>
									<li class="divider"></li>
									
									<li><a href="javascript:void(0);" onclick="modal_o(\'#modal-archive-' . $r['id'] . '\')"></a></li>
								</ul>
							</div>'; 


        if(is_date($r["create_at"], "Y-m-d H:i:s")) $r["create_at"] = date("d.m.Y H:i:s", strtotime($r["create_at"]));
        if(is_date($r["published_at"], "Y-m-d H:i:s")) $r["published_at"] = date("d.m.Y H:i:s", strtotime($r["published_at"]));
        return $r;
    }
}
