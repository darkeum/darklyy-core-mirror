<?php

namespace Catalog\Grid;

use \Boot\System\Grid\Grid;
use \Boot\System\Database\DB;
use Organizations\Model\Organizations;

class CatalogManufactureStockParserGrid extends Grid
{
    protected $db_type = 'db';

    protected function init()
    {
        $this->options = array(
            'sortname' => 'cmsp.id',
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

        $this->db = DB::table('catalog_manufacture_stock_parser as cmsp');

        $this->cols = array(

            'id' => array(
                'hidden' => true,
                'db' => 'cmsp.id',
            ),

            'numb' => array(
                'db' => 'cmsp.id',
                'label' => '№',
                'width' => 10,
                'hidden' => false,
                'search_op' => 'like'
            ),

            'model_type' => array(
				'label' => 'Провайдер',
                'hidden' => false,
				'encode' => false,
				'db' => 'cmsp.model_type',
				'width' => 30,		
				'search_op' => 'like',
            ),
			
			'work' => array(
				'label' => 'В работе',
				'width' => 30,
				'db' => 'cmsp.work',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getWork(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getWork(), 'Все'),
                ),
                'search_op' => 'equal',
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

            // 'type' => array(
            //     'label' => 'Вид контрагента',
            //     'db' => 'o.type',
            //     'width' => 30,
            //     'hidden' => false,
            //     'encode' => false,
            //     'replace' => $this->getType(),
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'value' => $this->setValue($this->getType(), 'Все'),
            //     ),
            //     'search_op' => 'equal',
            // ),

            'last_date_file' => array(
                'label' => 'Дата файла последнего взятия',
                'db' => 'cmsp.last_date_file',
                'width' => 25,
                'format_date' => "d.m.Y",
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'last_date' => array(
                'label' => 'Дата последнего взятия',
                'db' => 'cmsp.last_date',
                'width' => 25,
                'format_date' => "d.m.Y H:i:s",
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
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

    protected function getWork()
    {
        $rows = [];
        $rows[0] = "Ожидание";
        $rows[1] = "В работе";
        return $rows;
    }
  
    protected function parseRow($r)
    {
        $r['ab'] = '<div class="btn-group">
            <button type="button" class="btn btn-primary" onclick="startJob('.$r["id"].')">Запустить</button>
		</div>'; 

        return $r;
    }
}
