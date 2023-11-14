<?php

function merging_print($id){
    global $db;
    require_once(ROOT."/vendor/autoload.php");

    $tpl = ROOT.'/files/tpl/merging/merging.docx';

    $order = query_main_result($id);

    $document =  new \PhpOffice\PhpWord\TemplateProcessor($tpl);

    $document->setValue('numb', $order['numb']); 
    $document->setValue('date', date("d.m.Y", strtotime($order['date'])));
    $document->setValue('name_main', $order["name"]);
    $document->setValue('count_main', $order["count"]);
    $document->setValue('unit_main', $order["cu_sname"]);
    $document->setValue('fio', fullUserName($order["uid"]));

    $query = query_detail($id);
    $order_data = [];
    if($db->numRows($query) > 0){
        $i = 0;
        while($r = $db->getRow($query)){
            $i++;
            $order_data[] = [
                "n" => $i,
                "name" => $r["name"],
                "sku" => $r["sku"],
                "count" => $r["count"],
                "unit" => $r["cu_sname"],
            ];
        }
    }
    $document->cloneRowAndSetValues('n', $order_data);

    $file_name = "Merging_№".$order["numb"];
    $file_url= ROOT.'files/'.$file_name.'.docx';
    $document->saveAs($file_url);
    if (file_exists($file_url)) {
        $filename = basename($file_url);
        $size = filesize($file_url);
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Length: $size");
        header("Charset: UTF-8");
        header("Content-Type: application/unknown");
        if (readfile($file_url)) {
            unlink($file_url);
        }
    }
}