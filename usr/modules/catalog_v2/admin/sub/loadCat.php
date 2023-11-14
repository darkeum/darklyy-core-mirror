<?php

use Boot\System\Office\Spreadsheet\SpreadsheetSupport;

global $adminTpl, $core, $db, $core, $config, $lang;

const LIMIT_COUNT = 500;

function get_file_name(){
    $list = glob(get_file_name_num("*"));
    $end_list = end($list);
    return $end_list ?: get_file_name_num("1");
}

function get_count_file_num(){
    return count(glob(get_file_name_num("*")));
}

function get_file_name_num($num, $root = true){
    global $core;
    $uid = $core->auth->user_info['id'];
    $link = "files/catalog_excel/CatList_{$uid}_{$num}.xlsx";
    return $root ? ROOT.$link : $link;
}

function loadCat($request)
{
    global $adminTpl, $core, $db, $core, $config, $lang;

    //set_time_limit(0);

    $type = isset($request["type"]) ? $request["type"] : "category";
    $id = $request[$type] ?? "";
    $limit = isset($request["limit"]) ? $request["limit"] : 0;
    $lFlag = false;
    if(isset($request["limit_active"])){
        $limit = isset($request["limit_inp"]) ? $request["limit_inp"] : $limit;
        $lFlag = true;
    }

    $uid = $core->auth->user_info['id'];

    switch($type){
        default: 
            $search = empty($id) ? 'IS NULL' : "LIKE '".$id."'";
        break;
        case "category":
            $search = empty($id) ? 'IS NULL' : "LIKE ',".$id.",'";
        break;
    }

    $search_str = $type == "all" ? "" : "c.{$type} {$search} AND";
    

    $qLimit = (int)$limit === 0 ? "" : ("LIMIT ".(LIMIT_COUNT * ($limit-1)).", ".LIMIT_COUNT);

    $query = $db->query("SELECT c.*, cm.name as cm_name, cg.id as cg_id, cg.name as cg_name, sd.sum as sd_quantity, cpr.rival_link as cpr_rival_link, cpr.type as cpr_type
    FROM `" . DB_PREFIX . "_catalog` as c 
    LEFT JOIN `" . DB_PREFIX . "_catalog_manufacture` as cm ON(cm.id = c.man) 
    LEFT JOIN `" . DB_PREFIX . "_catalog_groups` as cg ON(c.catalog_groups_id = cg.id) 
    LEFT JOIN `" . DB_PREFIX . "_catalog_price_rival` as cpr ON(c.id = cpr.id) 
    LEFT JOIN (SELECT * FROM `" . DB_PREFIX . "_stock_detail` WHERE stock = '1') as sd ON(sd.cid = c.id)
    WHERE ".$search_str." c.hidden = 0 ".$qLimit);

    // require_once("usr/plugins/PHPExcel.php");
    
    $file = get_file_name();
    
    $startLine = 1; // Начальная координата y
    if(file_exists($file)){
        $fx = false;
        // $document = PHPExcel_IOFactory::load($file);
        $document = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $sheet = $document->setActiveSheetIndex(0);
        // $countRow =  LIMIT_COUNT * $limit + 1; //количество строк
        $countRow = $document->setActiveSheetIndex(0)->getHighestRow(); //количество строк
        $columnPosition = $countRow; // Начальная координата x
        if($countRow > 5000){
            $fx = true;
            $file_count = get_count_file_num() + 1;
            $file = get_file_name_num($file_count);
            $columnPosition = 1;
            // $document = new PHPExcel();
            $document = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $document->setActiveSheetIndex(0);
        }
    } else {
        $fx = true;
        // $document = new PHPExcel();
        $document = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $document->setActiveSheetIndex(0);

        $columnPosition = 1; // Начальная координата x
    }

    $result = [
        "end" => false,
        "link" => get_file_name_num("*", false),
        "count" => 0,
        "empty" => false,
    ];
    
    $cert_type = [];
    $queryCert = $db->query("SELECT * FROM JMY_cert_type");
    while($r = $db->getRow($queryCert)){
        $cert_type[] = $r;
    }

    // ***all***
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'id {%id%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("sku").' {%sku%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("sku2").' {%sku2%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("sku3").' {%sku3%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("sku_UdmurtSnab").' {%sku_UdmurtSnab%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("unit").' {%unit%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("title").' {%title%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("short").' {%short%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("description").' {%description%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("fulltitle").' {%fulltitle%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("category").' {%category%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("preview").' {%preview%}');
    $startLine++;
    // if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'штрих-код {%barcode%}');
    // $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("man").' {%man%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'производитель наименование {%%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("active").' {%active%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("active_guest").' {%active_guest%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("active_user").' {%active_user%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("types").' {%types%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("hidden").' {%hidden%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("catalog_groups_id").' {%catalog_groups_id%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, \Catalog_v2\Model\Catalog::translate_field("date_delivery").' {%date_delivery%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'Закупочная цена {%%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'Прайс-лист {%%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'Количество {%%}');
    $startLine++;
    // if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'Ссылки партнеров и конкурентов (вводить через пробел) {%rival_link%}');
    // $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'Ссылки партнеров и конкурентов (вводить через пробел) {%%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'Тип цены {%%}');
    $startLine++;
    // ***cert***
    foreach($cert_type as $val){
        if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $val["title"].' {%%}');
        $startLine++;
    }
    // ***spec***
    if($fx)  $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'наименование {%spec_1%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'модель/марка {%spec_2%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'дополнительный производитель {%spec_3%}');
    $startLine++;
    // ***weight***
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'вес {%weight_1%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'длина {%weight_2%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'объем {%weight_3%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'площадь {%weight_4%}');
    $startLine++;
    if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, 'количество в упаковке {%weight_5%}');
    $startLine++;

    // ***attribute***
    $arrayAttr = [];
    if($type === "category"){
        $atrQuery = $db->query("SELECT id, title FROM `".DB_PREFIX."_catalog_atribute` WHERE cat LIKE '%,".$id.",%'");
        while($r = $db->getRow($atrQuery)){
            $arrayAttr[$r["id"]] = $startLine;
            if($fx) $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["title"].' {%attribute_'.$r["id"].'%}');
            $startLine++;
        }
    }

    // dd($db->numRows($query));
    $count = $db->numRows($query);
    $result["count"] = $count;
    if($count > 0){
        while ($r = $db->getRow($query)) {
            $columnPosition++;
            $startLine = 1;
            // ***attribute***
            if($type === "category"){
                if(strlen($r["attribute"]) > 0){
                    $attribute = json_decode($r["attribute"], true);
                    if(is_array($attribute)){
                        foreach($attribute as $key => $val){
                            if(isset($arrayAttr[ $key ])){
                                $sheet->setCellValueByColumnAndRow($arrayAttr[ $key ], $columnPosition, $val);
                            }
                        }
                    }
                }
            }
            // ***all***
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["id"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["sku"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["sku2"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["sku3"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["sku_UdmurtSnab"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["unit"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, ee($r["title"]));
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, ee($r["short"]));
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, ee($r["description"]));
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, ee($r["fulltitle"]));
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, str_replace(',', '', $r["category"]));
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["preview"]);
            $startLine++;
            // $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["barcode"]);
            // $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["man"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["cm_name"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["active"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["active_guest"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["active_user"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["types"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["hidden"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["cg_name"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["date_delivery"]);
            $startLine++;
            $price = \Price::getPrice($r["id"]);
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $price->price_purchase_);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $price->price_list_);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, empty($r["sd_quantity"]) ? 0 : $r["sd_quantity"]);
            $startLine++;
            // $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["rival_link"]);
            // $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["cpr_rival_link"]);
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $r["cpr_type"]);
            $startLine++;
            // ***cert***
            $value = [];
            $qc = $db->query("SELECT c.* FROM JMY_cert_relation as cr LEFT JOIN JMY_cert as c ON(c.id = cr.cert_id) WHERE cr.cid = '".$r["id"]."'");
            while($c = $db->getRow($qc)){
                $value[$c["type"]][] = $c;
            }
            foreach($cert_type as $val){
                $new_value = [];
                if(isset($value[ $val["id"] ])){
                    foreach($value[ $val["id"] ] as $v){
                        $new_value[] = $v["id"]." - ".date("d.m.Y", strtotime($v["date_end"]));
                    }
                }
                $new_value_str = implode(", ", $new_value);
                $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $new_value_str);
                $startLine++;
            }
            // ***spec***
            $spec = json_decode($r["spec"], true);
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, ((isset($spec["1"]) && !is_array($spec["1"])) ? ee($spec["1"]) : ''));
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, ((isset($spec["2"]) && !is_array($spec["2"])) ? ee($spec["2"], ["enter" => "\n "]) : ''));
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, ((isset($spec["3"]) && !is_array($spec["3"])) ? ee($spec["3"]) : ''));
            $startLine++;
            // ***weight***
            $weight = json_decode($r["weight"], true);
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, (isset($weight["1"]) ? $weight["1"] : ''));
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, (isset($weight["2"]) ? $weight["2"] : ''));
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, (isset($weight["3"]) ? $weight["3"] : ''));
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, (isset($weight["4"]) ? $weight["4"] : ''));
            $startLine++;
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, (isset($weight["5"]) ? $weight["5"] : ''));
            $startLine++;
        }

        // $objWriter = PHPExcel_IOFactory::createWriter($document, 'Excel5');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($document, 'Xlsx');
        
        $objWriter->save($file);
        chmod($file, 0755);
        if($lFlag) $result["end"] = true;
        return $result;
    } else {
        $result["end"] = true;
        if($fx){
            $result["empty"] = true;
        }
        return $result;
    }
}

function loadCatFile($file){
    $list = glob($file);
    if($list){
        if(count($list) == 1){
            $file = $list[0];
            SpreadsheetSupport::get_file($file);
        } else {
            $zip = new ZipArchive();
            $zip_name = time().".zip";
            $zip->open($zip_name, ZIPARCHIVE::CREATE);
            foreach($list as $doc){
                $ext = pathinfo($doc);
                $zip->addFile($doc, $ext["basename"]);
            }
            $zip->close();
            if(file_exists($zip_name)){
                header('Content-type: application/zip');
                header('Content-Disposition: attachment; filename="'.$zip_name.'"');
                readfile($zip_name);
                // удаляем zip файл если он существует
                unlink($zip_name);
                foreach($list as $doc){
                    unlink($doc);
                }
            }
        }
    }
}

function load_cat_view()
{
    global $adminTpl, $core, $db, $config, $lang;
    $adminTpl->admin_head('Каталог товаров | Загрузка по категории');
    ?>

    <div class="panel panel-dark panel-border top">
        <div class="panel-heading">

        </div>
        <div class="panel-body pn">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <h3>Выгрузка по категории</h3>
                    <form class="form-horizontal" onsubmit="return false;" id="load_cat_form">
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="category_radio">Категория: <input type="radio" checked name="type" value="category" id="category_radio"></label>
                            <div class="col-sm-9">
                                <select name="category" class="select2">
                                    <?php
                                    $cats_arr = $core->aCatList('catalog_v2');
                                    echo '<option value="0" selected>Отсутсвует</option>';
                                    echo '<option value="">Без категории</option>';
                                    foreach ($cats_arr as $cid => $name) {
                                        echo '<option value="' . $cid . '">' . $name . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="man_radio">Производитель: <input type="radio" name="type" value="man" id="man_radio"></label>
                            <div class="col-sm-9">
                                <select name="man" class="select2">
                                    <?php
                                    echo '<option value="0" selected>Отсутсвует</option>';
                                    echo '<option value="">Без производителя</option>';
                                    $query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_manufacture");
                                    while($m = $db->getRow($query)) {
                                        echo '<option value="' . $m["id"] . '">' .$m["id"].' - '. $m["name"] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="all_radio">Все: <input type="radio" name="type" value="all" id="all_radio"></label>
                            <div class="col-sm-9">
                                
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="check"><input type="checkbox" id="check" name="limit_active" value="1" onchange="toggle_btn();">Постраничная загрузка, введите страницу(ограничение в <?php echo LIMIT_COUNT;?> записей):</label>
                            <div class="col-sm-9">
                                <input type="number" value="1" placeholder="Введите номер страницы" name="limit_inp" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="button" value="Скачать" id="submit" class="btn btn-success" onclick="save();">
                            <input type="button" value="Скачать" id="submit_limit" class="btn btn-success" onclick="save2();" style="display:none;">
                            <!-- <input type="button" value="Очистить временные файлы" class="btn btn-danger" onclick="clear_temp_file();"> -->
                        </div>
                    </form>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <h3>Загрузка</h3>
                    <form method="post" action="/administration/module/catalog_v2/loadCatSave" id="loadFileForm" class="form-horizontal" enctype="multipart/form-data">
                        <div class="d-flex align-items-center">
                            <input type="file" name="file" class="form-control-file" onchange="check_file();" required>
                            <input type="submit" value="Загрузить" id="save" class="btn btn-success" disabled>
                            <input type="button" value="Очистить" id="clear" class="btn btn-primary" style="display:none;" onclick="reset_send_form();">
                            <h5 id="countRow" class="ml20"></h5>
                        </div>
                        <div style="min-height: 30px;" id="items_list">
                            <table id="items_list_table" class="table table-striped" style="display: none;">
                                <thead>
                                    <th>№</th>
                                    <th>id</th>
                                    <th>Артикул</th>
                                    <th>Наименование</th>
                                    <th>Статус</th>
                                </thead>
                                <tbody id="items_list_table_body"></tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    $adminTpl->footIncludes[] = '
        <script>
            $(".select2").select2();
        </script>';
    $adminTpl->footIncludes[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/load_cat.js?'.time().'"></script>';
    $adminTpl->admin_foot();
}

function loadCatSave($request, $files)
{
    global $adminTpl, $core, $db, $config, $lang;

    // require_once("usr/plugins/PHPExcel.php");

    $ff = isset($files["file"]) ? $files["file"] : false;
    $file = isset($ff["tmp_name"]) ? $ff["tmp_name"] : false;

    // $excel = PHPExcel_IOFactory::load($file);
    $excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $countRow =  $excel->setActiveSheetIndex(0)->getHighestRow(); //количество строк
    $countColumnAlp =  $excel->setActiveSheetIndex(0)->getHighestColumn(); //количество столбцов в буквенном виде
    $countColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($countColumnAlp); //количество столбцов в числовом виде с 0

    $result = [
        "countRow" => $countRow, 
        "last" => 0, 
        "items" => [],
    ];

    $array_name = [];
    for($i = 0; $i <= $countColumn; $i++){
        $arrayRes = [];
        $str =  $excel->getActiveSheet()->getCellByColumnAndRow($i, 1)->getValue();
        preg_match('/[{]%.+%[}]/ui', $str, $arrayRes);
        if(empty($arrayRes[0])){
            $array_name[$i] = null;
        } else {
            $array_name[$i] = str_replace('%}', '', str_replace('{%', '', $arrayRes[0]));
        }
    }

    $number = $request["number"];
    $countLast = $number + 10;
    if($countLast >= $countRow){
        $countLast = $countRow;
    }

    //проход по строкам
    for ($i = $number; $i <= $countLast; $i++) {
        //проход по столбцам
        $spec = ["1" => null, "2" => null, "3" => null];
        $specFlag= false;
        $weight = ["1" => null, "2" => null, "3" => null, "4" => null, "5" => null];
        $weightFlag = false;
        $attribute = [];
        $id = null;
        $str = null;
        $sku = null;
        $cat = null;
        $data = [];

        for($j = 0; $j<= $countColumn; $j++){
            $flag_no_empty = true;
            $val = $excel->getActiveSheet()->getCellByColumnAndRow($j, $i)->getFormattedValue();
            if(empty($array_name[$j])){
                continue;
            }
            if(empty($val) && $val !== 0 && $val !== "0") $flag_no_empty = false;
            $val = $flag_no_empty ? trim($val) : null;
                //получение id
            if($array_name[$j] === 'id'){
                if($flag_no_empty){
                    $id = $val;
                }
                //добавление в spec
            } else if(strstr($array_name[$j], "spec_")){
                if($flag_no_empty){
                    $num = explode("_", $array_name[$j]);
                    $spec[$num[1]] = htmlspecialchars($val, ENT_QUOTES);
                    $specFlag = true;
                }
                //добавление в weight
            } else if(strstr($array_name[$j], "weight_")){
                if($flag_no_empty){
                    $num = explode("_", $array_name[$j]);
                    $weight[$num[1]] = $val;
                    $weightFlag = true;
                }
                //добавление в attribute
            } else if(strstr($array_name[$j], "attribute_")){
                if($flag_no_empty){
                    $num = explode("_", $array_name[$j]);
                    $attribute[$num[1]] = $val;
                }
                //категории
            } else if(strstr($array_name[$j], "category")){
                $data[ $array_name[$j] ] = $val;
                if($flag_no_empty) $cat = htmlspecialchars($val, ENT_QUOTES);
                //производитель
            } else if(strstr($array_name[$j], "man")){
                $data[ $array_name[$j] ] = $val;
                //наименование
            } else if(strstr($array_name[$j], "title")){
                $data[ $array_name[$j] ] = $val;
                //артикул
            } else if(strstr($array_name[$j], "sku")){
                $data[ $array_name[$j] ] = $val;
                if($flag_no_empty && $array_name[$j] === "sku") $sku = $val;
                //наименование группы товаров
            } else if(strstr($array_name[$j], "catalog_groups_id")){
                if($flag_no_empty){
                    $cg = \Catalog_v2\Model\CatalogGroups::where("name", $val)->first();
                    if(!empty($cg)){
                        $cg_id = $cg->id;
                        $data[ $array_name[$j] ] = $cg_id;
                    }
                } else {
                    $data[ $array_name[$j] ] = null;
                }
                //добавление в общий
            } else {
                $data[ $array_name[$j] ] = $val;
            }
        }

        if(count($attribute) > 0) $data["attribute"] = $attribute;
        if($specFlag) $data["spec"] = $spec;
        if($weightFlag) $data["weight"] = $weight;

        $uid = $core->auth->user_info['id'];

        $data["date"] = time();

        $color = "red";
        $res = "fail";
        $resFlag = false;
        if(empty($id)){
            $ca = \Catalog_v2\Model\Catalog::where("title", "like", $data["title"]);
            if(!empty($sku)) $ca->orWhere("sku", "like", $sku);
            if($ca->count() == 0){
                $res = "СОЗДАТЬ";
                $color = "green";
                $resFlag = true;
            } else {
                $res = "Такой товар уже есть!";
                $catalog = $ca->first();
                $id = $catalog->id;
            }
        } else {
            $res = "ОБНОВИТЬ";
            $color = "green";
            $resFlag = true;
        }

        if(empty($id) && empty($cat) && empty($sku)){
            $res = "Нет категории и артикула! Создание не возможно.";
            $resFlag = false;
            $color = "red";
        }

        // $resFlag = false;

        if($resFlag){
            if(empty($id)){
                // $id = $catalog->add($data);
                $catalog = \Catalog_v2\Model\Catalog::create($data);
                $id = $catalog->id;
            } else {
                // $catalog->update($id, $data);
                $catalog = \Catalog_v2\Model\Catalog::find($id);
                $catalog->update($data);
            }
        }

        $result["items"][] = [
            "id" => $id,
            "res" => $res,
            "color" => $color,
            "number" => $number,
            "sku" => $catalog->sku ?? "",
            "title" => $catalog->title ?? "",
        ];

        $number++;
    }

    $result["last"] = $number;

    return $result;
}

function clear_temp_file_load_cat(){
    $list = glob(get_file_name_num("*"));
    if($list){
        foreach($list as $doc){
            if(file_exists($doc)) unlink($doc);
        }
    }
}

function loadCatCheck(){
    $data = ["countRow" => 0];

    $file = isset($_FILES["file"]["tmp_name"]) ? $_FILES["file"]["tmp_name"] : false;
    if($file){
        $excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $data["countRow"] =  $excel->setActiveSheetIndex(0)->getHighestRow() - 1; //количество строк
    }

    return $data;
}