<?php

namespace Catalog\Grid;

use \Boot\System\Grid\Grid;
use \Boot\System\Database\DB;
use Organizations\Model\Organizations;

class IndexGrid extends Grid
{
    protected $db_type = 'db';

    protected $opts_type = "open";

	protected function getOpenUrl(){
        return route("admin.catalog.edit", ["id" => "@id"]);
    }

    protected function init()
    {
        $this->options = array(
            'sortname' => 'c.id',
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

        $stock = DB::table("stock_detail")->where("stock", 1);
        $this->db = DB::table('catalog as c')->leftJoinSub($stock, 's', function($query){
            $query->on('s.cid', '=', 'c.id');
        });

        $this->cols = array(

            'id' => array(
                'hidden' => true,
                'db' => 'c.id',
            ),

            'altname' => array(
                'hidden' => true,
                'db' => 'c.altname',
            ),

            'numb' => array(
                'db' => 'c.id',
                'label' => '№',
                'width' => 10,
                'hidden' => false,
                'search_op' => 'like'
            ),

            'sku' => array(
				'label' => 'Артикул',
                'hidden' => false,
				'encode' => false,
				'db' => 'c.sku',
				'width' => 30,		
				'search_op' => 'like',
            ),	
					
			'title' => array(
				'label' => 'Наименование',
                'hidden' => false,
				'encode' => false,
				'db' => 'c.title',
				'width' => 60,		
				'editable' => false,
				'search_op' => 'like',
            ),	
			
			'manufacture' => array(
				'label' => 'Производитель',
				'width' => 30,
				'db' => 'c.man',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
                'null' => "Не указано",
				'replace' => $this->getManufacture("catalog", "man"),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getManufacture("catalog", "man"), 'Все'),
                ),
                'search_op' => 'equal',
            ),			
					
			'category' => array(
				'label' => 'Категории',
				'width' => 30,
				'db' => 'c.category',
				'hidden' => false,
				'encode' => false,
                'null' => "Не указано",
				'sortable' => false,
				'replace' => $this->getCategories(fn($cid) => ','.$cid.','),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getCategories(fn($cid) => ','.$cid.','), 'Все'),
                ),
                'search_op' => 'equal',
            ),
					

			'active' => array(
				'label' => 'Активность',
				'width' => 30,
				'db' => 'c.active',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getActiveTypes(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getActiveTypes(), 'Все'),
                ),
                'search_op' => 'equal',
            ),

			'hidden' => array(
				'label' => 'Скрытность',
				'width' => 30,
				'db' => 'c.hidden',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getHiddenTypes(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getHiddenTypes(), 'Все'),
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

            // 'date' => array(
            //     'label' => 'Дата',
            //     'db' => 'bd.date',
            //     'width' => 25,
            //     'format_date' => "d.m.Y",
            //     'searchoptions' => $this->dateRangeSearch(),
            //     'search_op' => 'date_range',
            // ),

            'sum' => array(
				'label' => 'Остаток',
                'hidden' => false,
				'encode' => false,
				'db' => 's.sum',
                'null' => "0",
				'width' => 20,		
				'editable' => false,
				'search_op' => 'like',
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

    protected function getActiveTypes(){
		$rows['1'] = 'Включен';
		$rows['0'] = 'Отключен';
        return $rows;
    }

    protected function getHiddenTypes(){
		$rows['0'] = 'Включен';
		$rows['1'] = 'Отключен';
        return $rows;
    }
  
    protected function parseRow($r)
    {
        $r["numb"] = $r["id"];

        $r['ab'] = '<div class="btn-group">
            <a href="'.router("admin.catalog.edit",  $r['id']).'" target="_blank" class="btn btn-sm btn-primary">Просмотр</a>
            <a href="/catalog_v2/'.$r['altname'].'.html" target="_blank" class="btn btn-sm btn-success">
                <span class="fal fa-globe " data-toggle="tooltip" title="Открыть на сайте"></span>
            </a>
		</div>'; 

        return $r;
    }
}
