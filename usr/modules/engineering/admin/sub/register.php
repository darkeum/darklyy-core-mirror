<?php

function main_register() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = "Проектирование";
	$adminTpl->admin_head($lnn. ' | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'engineering/register';
	$atributs['module_open'] = 'edit';
	load_grid('jqEngineeringRegister', $lnn, $atributs, ROOT.'/usr/modules/engineering/admin/grid/');
	$adminTpl->admin_foot();
} 

function add_register($id = false){
    global $adminTpl, $core, $db;

    $lnn = $id ? "Редактировать" : "Добавить";
    $adminTpl->admin_head("Проектирование | ".$lnn);
    if($id){
        $query = $db->query("SELECT * FROM ".DB_PREFIX."_engineering_register WHERE id = '${id}' LIMIT 1");
        $r = $db->getRow($query);
        $numb = $r["numb"];
        $section = json_decode($r["section"], true);
        $object = $r["object"];
        $date = $r["date"];
        $uid = json_decode($r["uid"], true);
        $status = (int)$r["status"];
        $col_size = 6;
        $inp_size = 10;
    } else {
        $numb = "";
        $section = [];
        $object = null;
        $date = date("Y-m-d");
        $uid = null;
        $status = -1;
        $col_size = 12;
        $inp_size = 8;
    }

    

    ?>

<link rel="stylesheet" type="text/css" href="/usr/modules/engineering/admin/asset/css/add_register.css">

<div class="container">
    <div class="row">
        <div class="panel mb25 mt5">
            <div class="panel-body p20 pb10">
                <div class="section row mbn admin-form">
                    <div class="col-md-<?php echo $col_size;?> pl15">
                        <form class="form-horizontal" onsubmit="save(this);">
                        <?php
                        if($id){
                            echo '<input type="hidden" value="'.$id.'" name="id" id="mainId">';
                        }
                        ?>
                        <h3 class="col-xs-offset-2">Данные</h3>
                            <div class="form-group">
                                <label for="numb" class="control-label col-xs-2">Шифр</label>
                                <div class="col-xs-<?php echo $inp_size;?>">
                                    <input type="text" class="form-control" id="numb" name="numb" placeholder="Введите уникальный шифр" value="<?php echo $numb;?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="section" class="control-label col-xs-2">Раздел</label>
                                <div class="col-xs-<?php echo $inp_size;?>">
                                    <select class="form-control select2" id="section" name="section[]" multiple required>
                                        <?php
                                            $section_type = section_type();
                                            echo "<option></option>";
                                            foreach($section_type as $key => $val){
                                                $selected = "";
                                                if(in_array($key, $section)){
                                                    $selected = "selected";
                                                }
                                                echo "<option value='${key}' ${selected}>${val}</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="object" class="control-label col-xs-2">Объект</label>
                                <div class="col-xs-<?php echo $inp_size;?>">
                                    <?php
                                        oSelect('jsAdminTpl', 'object', $object, 'getObjectToSelect');
                                        oSelect('select', 'object', $object, 'getObjectToSelect');
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="numb" class="control-label col-xs-2">Дата</label>
                                <div class="col-xs-<?php echo $inp_size;?>">
                                    <input type="date" class="form-control" id="date" name="date" placeholder="Введите дату" value="<?php echo $date;?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="uid" class="control-label col-xs-2">Исполнитель</label>
                                <div class="col-xs-<?php echo $inp_size;?>">
                                    <?php
                                        oSelect('jsAdminTpl', 'uid[]', $uid, 'getUsersAllToSelect', false, true);
                                        oSelect('select', 'uid[]', $uid, 'getUsersAllToSelect', false, true);
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="status" class="control-label col-xs-2">Статус</label>
                                <div class="col-xs-<?php echo $inp_size;?>">
                                    <select class="form-control select2" id="status" name="status" required>
                                        <?php
                                            $status_type = get_status();
                                            echo "<option></option>";
                                            foreach($status_type as $key => $val){
                                                $selected = "";
                                                if($key === $status){
                                                    $selected = "selected";
                                                }
                                                echo "<option value='${key}' ${selected}>${val}</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-offset-2 col-xs-10">
                                    <button type="submit" class="btn btn-success"><?php echo $id ? "Сохранить" : "Создать";?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php
                    if($id){
                        ?>
                    <div class="col-md-6 pl15">
                        <h3>Файлы</h3>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <button type="button" class="btn btn-primary" onclick="file_modal_open();">Добавить файлы</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12" id="div_files">
                                <?php
                                    file_register($id);
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="file_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Загрузка файлов</h5>
          </div>
          <div class="modal-body">
            <form id="file_modal_form">
                <?php
                    $attribute = [];
                    $attribute["name"] = "1";
                    $attribute["script"] = true;
                    $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
                    $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
                    echo uploaderMaster("css", $attribute);
                    echo uploaderMaster("upload", $attribute);
                    echo uploaderMaster("view", $attribute);
                ?>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="save_file();">Загрузить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="del_file_register_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Удалить файл</h5>
          </div>
          <div class="modal-body">
            Вы действительно хотите удалить этот файл?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="del_file_register($(this).data('id'));" id="del_file_register_modal_btn">Удалить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>

    <?php
    $adminTpl->footIncludes[] = '<script src="/usr/modules/engineering/admin/asset/js/add_register.js?071120191732"></script>';
    $adminTpl->admin_foot();
}

function file_register($id){
    global $db;
    $query = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE `pid` = '".$id."' AND `module` = 'engineering_register'");
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){
            echo '<div class="file-elem">
                <a href="/download/'.$r["id"].'">'.$r["name"].'</a>
                <i class="fas fa-times text-danger" onclick="del_file_register_modal('.$r["id"].')"></i>
            </div>';
        }
    }
}

function save_register(){
    global $db;
    $id = isset($_POST["id"]) ? htmlspecialchars($_POST["id"], ENT_QUOTES) : false;
    $numb = isset($_POST["numb"]) ? htmlspecialchars($_POST["numb"], ENT_QUOTES) : false;
    $section = isset($_POST["section"]) ? $_POST["section"] : [];
    $section_json = json_encode($section, JSON_UNESCAPED_UNICODE);
    $object = isset($_POST["object"]) ? htmlspecialchars($_POST["object"], ENT_QUOTES) : 'null';
    $date = isset($_POST["date"]) ? htmlspecialchars($_POST["date"], ENT_QUOTES) : false;
    $uid = isset($_POST["uid"]) ? $_POST["uid"] : [];
    $uid_json = json_encode($uid, JSON_UNESCAPED_UNICODE);
    $status = isset($_POST["status"]) ? htmlspecialchars($_POST["status"], ENT_QUOTES) : false;
    
    if($id){
        $db->query("UPDATE ".DB_PREFIX."_engineering_register SET `numb` = '".$numb."', `section` = '".$section_json."', `object` = ".$object.", `date` = '".$date."', `uid` = '".$uid_json."', `status` = '".$status."' WHERE id = '".$id."' LIMIT 1");
    } else {
        $db->query("INSERT INTO ".DB_PREFIX."_engineering_register (`numb`, `section`, `object`, `date`, `uid`, `status`) VALUES ('".$numb."', '".$section_json."', ".$object.", '".$date."', '".$uid_json."', '".$status."')");
        $id = $db->insert_id();
        echo $id;
    }
}

function check_numb($numb){
    global $db;
    if($numb){
        $query = $db->query("SELECT id FROM ".DB_PREFIX."_engineering_register WHERE numb LIKE '".$numb."' LIMIT 1");
        if($db->numRows($query) > 0){
            echo "ERROR";
        }
    }
}

function file_save(){
    global $db;
    $file = isset($_POST["file"]) ? $_POST["file"] : false;
    $id = isset($_POST["id"]) ? $_POST["id"] : false;
    $attribute = [];
    $attribute["name"] = "1";
    $attribute["file"] = $file;
    $attribute["module"] = 'engineering';
    $attribute["mod"] = 'engineering_register';
    $attribute["id"] = $id;
    $attribute["comm_id"] = $id;
    uploaderMaster("download", $attribute);
}

function file_del($id){
    attach_del_file($id);
}