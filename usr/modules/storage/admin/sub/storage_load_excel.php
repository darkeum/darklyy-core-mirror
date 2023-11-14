<?php

function storage_load_excel($id, $files){
    $file = $files["file"];
    $path = $file["tmp_name"];

    require_once(ROOT."usr/plugins/PHPExcel.php");
    $excel = PHPExcel_IOFactory::load($path);
    $countRow =  $excel->setActiveSheetIndex(0)->getHighestRow(); //количество строк
    $field_cid = 0;

	$sd = \Storage\Model\Storage::find($id);
    
    $result = [
        "error" => [],
    ];

    $cid_list = [];

    for($i = 2; $i <= $countRow; $i++){
        $cid = $excel->getActiveSheet()->getCellByColumnAndRow($field_cid, $i)->getFormattedValue();
        if(!empty($cid) && !in_array($cid, $cid_list)){
            $cid_list[] = $cid;
            $data = \Catalog_v2\Model\Catalog::find($cid)->toArray();
            if(empty($data)){
                $result["error"][] = $cid;
            } else {
                $count_stable = get_quantity_stock_transaction($cid, $sd->stock_id);
                \Storage\Model\StorageDetail::create(["cid" => $cid, "storage_id" => $id, "count_stable" => $count_stable, "status" => 1]);
            }
        }
    }
    
    return $result;
}