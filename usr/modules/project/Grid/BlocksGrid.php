<?php

namespace Project\Grid;

use \Boot\System\Grid\Grid;
use \Boot\System\Database\DB;
use \Boot\Support\Facades\Translation;
use \Project\Model\Blocks;

class BlocksGrid extends Grid
{
    protected $db_type = 'db';

    protected $table = "blocks";

    protected function init()
    {

        $this->options = array(
            'sortname' => 'blocks.id',
            'sortorder' => 'DESC',
        );

        $this->db = DB::connection("AEWIN")->table("blocks as b")->whereNull("b.deleted_at");
        

        $this->cols = array(

            'id' => array(
                'hidden' => true,
                'db' =>  'b.id',
            ),

            'numb' => array(
                'db' => 'b.id',
                'label' => '№',
                'width' => 8,
                'hidden' => false,
                'search_op' => 'like'
            ),

            'type' => array(
                'label' => "Тип",
                'db' => 'b.type',
                'width' => 10,
                'hidden' => false,
                'encode' => false,
                'replace' => Blocks::getType(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue(Blocks::getType(), Translation::trans('grid.all')),
                ),
                'search_op' => 'equal',
            ),

            'code' => array(
                'db' => 'b.code',
                'label' => "Код",
                'width' => 25,
                'hidden' => false,
                'encode' => false,
                'null' => Translation::trans('grid.null'),
                'search_op' => 'like'
            ),

            '3d' => array(
                'db' => 'b.3d',
                'label' => "3d",
                'width' => 10,
                'align' => 'center',
                'hidden' => false,
                'encode' => false,
                'search_op' => 'like'
            ),
            '3d_' => array(
                'hidden' => true,
                'db' =>  'b.3d',
            ),

            '2d' => array(
                'label' => "2d",
                'width' => 10,
                'align' => 'center',
                'hidden' => false, 
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search_op' => false,
                'search' => false,
                'title' => false,
            ),

            '2d_back' => array(
                'hidden' => true,
                'db' =>  'b.2d_back',
            ),
            '2d_left' => array(
                'hidden' => true,
                'db' =>  'b.2d_left',
            ),
            '2d_all' => array(
                'hidden' => true,
                'db' =>  'b.2d_all',
            ),
            '2d_plan' => array(
                'hidden' => true,
                'db' =>  'b.2d_plan',
            ),
            '2d_right' => array(
                'hidden' => true,
                'db' =>  'b.2d_right',
            ),
            '2d_front' => array(
                'hidden' => true,
                'db' =>  'b.2d_front',
            ),

            'uid' => array(
                'label' => Translation::trans('grid.uid'),
                'db' => 'b.uid',
                'width' => 15,
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getUid('blocks'),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getUid('blocks'), Translation::trans('grid.all')),
                ),
                'search_op' => 'equal',
            ),

            'created_at' => array(
                'label' => 'Дата создания',
                'db' => 'b.created_at',
                'width' => 15,
                'format_date' => "d.m.Y H:i:s",
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'updated_at' => array(
                'label' => 'Дата изменения',
                'db' => 'b.updated_at',
                'width' => 15,
                'format_date' => "d.m.Y H:i:s",
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'ab' => array(
                'label' => "Действия",
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

    protected function getUid($table = null, $column = 'uid'){
        $rows = array();
        $rows["0"] = "Гость";
        $users = DB::connection("AEWIN")->table($table)->whereNull("deleted_at")->select($column)->distinct()->get();
        foreach ($users as $k => $v) {
            $rows[$v->$column] = shortUserName($v->$column);
        }  
        return $rows;
    }

    protected function parseRow($r)
    {
        $r["3d"] = '<i class="fas fa-check-circle fa-2x '.(empty($r["3d_"]) ? "text-danger" : "text-success").'"></i>';
        $list2d = Blocks::get2dList();
        $list2dTrans = Blocks::getView();
        $list2dEmpty = [];
        $len = count($list2d);
        foreach($list2d as $l){
            if(empty($r[ $l ])){
                $list2dEmpty[] = $list2dTrans[ $l ];
            }
        }
        $len_count = count($list2dEmpty);
        if($len_count == 0){
            $status2d = "text-success";
        } else {
            $status2d = $len_count == $len ? "text-danger" : "text-warning";
        }
        $r["2d"] = '<i class="fas fa-check-circle fa-2x '.$status2d.'" title="'.implode("&#013;", $list2dEmpty).'"></i>';

        $btn_empty = '<div style="cursor: auto;" class="btn btn-sm btn-primary"></div>';
        $btn = [];
        if(!empty($r["3d_"])){
            $btn[] = '<a class="btn btn-sm btn-info" href="'.router("admin.project.blocks.download", ["id" => $r["id"], "view" => "3d", "type" => "dwg"]).'" target="_blank">Скачать 3d</a>';
        }
        if($len_count >= 0){
            $btn[] = '<a class="btn btn-sm btn-info" href="'.router("admin.project.blocks.download", ["id" => $r["id"], "view" => "2d", "type" => "dwg"]).'" target="_blank">Скачать 2d</a>';
            $btn[] = '<a class="btn btn-sm btn-info" href="'.router("admin.project.blocks.download", ["id" => $r["id"], "view" => "2d", "type" => "preview"]).'" target="_blank">Скачать 2d изображения</a>';
        }
        $btn = implode($btn_empty, $btn);
        $r['ab'] = '<div class="btn-group">
            '.$btn.'
            <button type="button" class="btn btn-sm btn-danger" onclick="deleteBlock('.$r["id"].')">Удалить</button>
        </div>'; 
        
        return $r;
    }
}
