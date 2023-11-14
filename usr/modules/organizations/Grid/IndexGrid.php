<?php

namespace Organizations\Grid;

use \Boot\System\Grid\Grid;
use \Boot\System\Database\DB;
use Organizations\Model\Organizations;

class IndexGrid extends Grid
{
    protected $db_type = 'db';

    protected function init()
    {
        $this->options = array(
            'sortname' => 'o.id',
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

        $this->db = DB::table('organizations as o');

        $this->cols = array(

            'id' => array(
                'hidden' => true,
                'db' => 'o.id',
            ),

            'numb' => array(
                'db' => 'o.id',
                'label' => '№',
                'width' => 10,
                'hidden' => false,
                'search_op' => 'like'
            ),

            'name' => array(
                'db' => 'o.name',
                'label' => 'Наименование',
                'width' => 30,
                'hidden' => false,
                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            // 'uid' => array(
            //     'label' => 'Ответственный',
            //     'db' => 'support.uid',
            //     'width' => 30,
            //     'hidden' => false,
            //     'encode' => false,
            //     'sortable' => false,
            //     'replace' => $this->getUid('support'),
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'value' => $this->setValue($this->getUid('support'), 'Все'),
            //     ),
            //     'search_op' => 'equal',
            // ),

            'type' => array(
                'label' => 'Вид контрагента',
                'db' => 'o.type',
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'replace' => $this->getType(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getType(), 'Все'),
                ),
                'search_op' => 'equal',
            ),

            'client_type' => array(
                'label' => 'Тип контрагента',
                'db' => 'o.client_type',
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'replace' => $this->getClientType(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getClientType(), 'Все'),
                ),
                'search_op' => 'equal',
            ),

            'inn' => array(
				'label' => 'ИНН',
				'width' => 10,
				'hidden' => false,
				'db' => 'o.inn',
				'encode' => false,
				'search_op' => 'like'
            ),
			
			'kpp' => array(
				'label' => 'КПП',
				'width' => 10,
				'hidden' => false,
				'db' => 'o.kpp',
				'encode' => false,
				'search_op' => 'like'
            ),
			
			'yaddress' => array(
				'label' => 'Юридический адрес',
				'width' => 25,
				'hidden' => false,
				'db' => 'o.yaddress',
				'encode' => false,
				'search_op' => 'like'
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

    protected function getType()
    {
        return Organizations::getType();
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

    protected function getClientType()
    {
        $rows = Organizations::getClientType();
        return $rows;
    }

  
    protected function parseRow($r)
    {
        $r["numb"] = $r["id"];
        $r["name"] = ee($r["name"]);
        $r['ab'] = '<div class="btn-group">
			<a href="'.router("admin.organizations.show", $r['id']).'" target="_blank" class="btn btn-xs btn-primary" role="button">Открыть</a>
			<a href="'.router("admin.organizations.edit", $r['id']).'" target="_blank" class="btn btn-xs btn-secondary" role="button">Редактировать</a>
			<a href="'.router("admin.organizations.print", $r['id']).'" target="_blank" class="btn btn-xs btn-info" role="button">Печать</a>
		</div>'; 


        // if(is_date($r["create_at"], "Y-m-d H:i:s")) $r["create_at"] = date("d.m.Y H:i:s", strtotime($r["create_at"]));
        // if(is_date($r["published_at"], "Y-m-d H:i:s")) $r["published_at"] = date("d.m.Y H:i:s", strtotime($r["published_at"]));
        return $r;
    }
}
