<?php

namespace Control\Grid;

use \Boot\System\Grid\Grid;
use \Boot\System\Database\DB;

class ControlPayGrid extends ControlGrid
{

    protected function init()
    {
        parent::init();

        $this->options = array(
            'sortname' => 'plan_pay_date',
            'sortorder' => 'DESC',
        );

        $this->nav = array(
            #Set common excel export
            'excel' => true,
            'exceltext' => 'Excel',
        
        
        );	

        $this->db = $this->prepare_query($this->db->where("c.status", 3));

        $this->cols["status_view"] = [
            'label' => 'Оплата',
            'db' => 'c.pay',
            'width' => 20,
            'hidden' => false,
            'encode' => false,
            'replace' => $this->getStatus("render"),
            'stype' => 'select',
            'searchoptions' => array(
                'value' => $this->setValue($this->getStatus(), 'Все'),
            ),
            'search_op' => 'equal',
        ];
    }

    protected function prepare_query($query){
        return $query->where("c.plan_pay_date", "<=", date("Y-m-d"));
    }

    protected function getStatus($type = 'select')
    {
        $rows_text = \Control\Model\Control::getPay();

        $rows[0] = "<span class='fa fa-clock-o text-warning fa-md' data-toggle='tooltip' title='".$rows_text[0]."'></span>";
        $rows[1] = "<span class='fa fa-check-circle text-success fa-md' data-toggle='tooltip' title='".$rows_text[1]."'></span>";    
		$rows[2] = "<span class='fa fa-check-circle text-primary fa-md' data-toggle='tooltip' title='".$rows_text[2]."'></span>";  
		$rows[3] = "<span class='fa fa-check-circle text-warning-dark fa-md' data-toggle='tooltip' title='".$rows_text[3]."'></span>";  

        return $type == "select" ? $rows_text : $rows;
    }
  
    protected function parseRow($r)
    {
        $r = parent::parseRow($r);
        $r['ab'] = '<div class="btn-group">
            <a href="'.router("admin.control_pay.show", $r['id']).'" target="_blank" class="btn btn-xs btn-primary" role="button">Открыть</a>
		</div>';

        return $r;
    }
}
