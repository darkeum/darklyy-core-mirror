<?php

namespace Control\Grid;

use \Boot\System\Grid\Grid;
use \Boot\System\Database\DB;

class ControlGrid extends Grid
{
    protected $db_type = 'db';

    protected $opts_type = "open";

 

	protected function getOpenUrl(){
        return route("admin.control.show", ["id" => "@id"]);
    }

    protected function getDBQuery(){
        return DB::table('control as c')
        ->leftJoin("organizations as o", "o.id", "=", "c.oid")
        ->leftJoin("supply_order as so", "so.id", "=", "c.supply_o")
        ->leftJoin("object as obj", "obj.id", "=", "c.object");
    }

    protected function prepareQuery(){
        $org = \Organizations\Model\Org::where("director", \Boot\Support\Facades\Auth::id())->first();
        return $this->getDBQuery();
    }

    protected function init()
    {
        $this->options = array(
            'sortname' => 'c.id',
            'sortorder' => 'DESC',
        );

        $this->nav = array(
            #Set common excel export
            'excel' => true,
            'exceltext' => 'Excel',
        
        
        );	

        // $this->nav = array(
        //     'excel' => true,
        //     'exceltext' => 'Сохранить в Excel',
        //     'trash' => true,
        //     'trashtype' => 'null',
        //     'trashtext' => 'Показывать удаленные',
        //     'trashfield' => 'r.deleted_at',
        // );

        $this->db = $this->prepareQuery();

        $this->cols = array(

            'id' => array(
                'hidden' => true,
                'db' => 'c.id',
            ),

            'real_status' => array(
                'hidden' => true,
                'db' => 'c.status',
            ),

            'supply_o' => array(
                'hidden' => true,
                'db' => 'c.supply_o',
            ),	

			'oid' => array(
                'hidden' => true,
                'db' => 'c.oid',
            ),

			'object' => array(
                'hidden' => true,
                'db' => 'c.object',
            ),

			'status' => array(
                'hidden' => true,
                'db' => 'c.status',
            ),

            'numb' => array(
                'db' => 'c.id',
                'label' => '№',
                'width' => 10,
                'hidden' => false,
                'search_op' => 'like'
            ),

            'oid_name' => array(
                'db' => 'o.name',
                'label' => 'Контрагент',
                'width' => 30,
                'hidden' => false,
                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            'object_name' => array(
                'db' => 'obj.name',
                'label' => 'Объект',
                'width' => 30,
                'hidden' => false,
                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            'so_numb' => array(
                'db' => 'so.numb',
                'label' => 'Счёт поставщика',
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            'uid' => array(
                'label' => 'Ответственный',
                'db' => 'c.uid',
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getUid('control'),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getUid('control'), 'Все'),
                ),
                'search_op' => 'equal',
            ),

            'date' => array(
                'label' => 'Дата',
                'db' => 'c.date',
                'width' => 25,
                'format_date' => "d.m.Y H:i:s",
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'plan_pay_date' => array(
                'label' => 'Дата оплаты',
                'db' => 'c.plan_pay_date',
                'width' => 25,
                'format_date' => "d.m.Y",
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'total' => array(
                'db' => 'c.total',
                'label' => 'Сумма',
                'width' => 30,
                'hidden' => false,
                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            'status_view' => array(
                'label' => 'Статус',
                'db' => 'c.status',
                'width' => 20,
                'hidden' => false,
                'encode' => false,
                'replace' => $this->getStatus("render"),
                'stype' => 'select',
                'align' => 'center',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getStatus(), 'Все'),
                ),
                'search_op' => 'equal',
            ),

            'ab' => array(
                'label' => 'Действия',
                'width' => 20,
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
        $rows_text = \Control\Model\Control::getStatus();
        unset($rows_text[0]);

        $rows[1] = "<span class='fa fa-clock-o text-warning fa-md' data-toggle='tooltip' title='".$rows_text[1]."'></span>";
        $rows[2] = "<span class='far fa-times-circle text-danger fa-md' data-toggle='tooltip' title='".$rows_text[2]."'></span>";    
		$rows[3] = "<span class='fa fa-check-circle text-success fa-md' data-toggle='tooltip' title='".$rows_text[3]."'></span>";  

        return $type == "select" ? $rows_text : $rows;
    }

  
    protected function parseRow($r)
    {
        $r["numb"] = $r["id"];
        $confirmWithoutLooking = '<li><a class="dropdown-item" onclick="confirmWithoutLooking('.$r["id"].')" href="javascript:void(0)">Подтвердить не глядя</a></li>';
        if($r["real_status"] != 1){
            $confirmWithoutLooking = "";
        }
        $r['ab'] = '<div class="btn-group">
            <a href="'.router("admin.control.show", $r['id']).'" target="_blank" class="btn btn-sm btn-primary" role="button">Открыть</a>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" id="dropdownMenuReference" data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuReference">
              '.$confirmWithoutLooking.'
            </ul>
		</div>'; 

        $r["so_numb"] = '<a href="/'.ADMIN.'/module/supply/order_detail/'.$r['supply_o'].'" target="_blank">'.$r['so_numb'].'</a>';

        return $r;
    }
}
