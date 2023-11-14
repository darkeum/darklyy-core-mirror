<?php

function add()
{
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $adminTpl->admin_head("Добавление заявки");
?>

    <div id="content" class="animated fadeIn">
        <div class="panel panel-dark panel-border top">
            <div class="panel-heading"><span class="panel-title"></span>
            </div>
            <div class="panel-body admin-form">
                <form id="admin-form" action="administration/module/merging/save" method="post" role="form" class="form-horizontal parsley-form">
                    <div class="form-group">
                        <label for="type" class="col-lg-3 control-label">Организация:</label>
                        <div class="col-lg-4">
                            <label class="field select">
                                <select class="form-control" name="org" id="org" onchange="view_stock();">
                                    <?php
                                        echo get_org_select_option();
                                    ?>
                                    </select><i class="arrow double"></i>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-lg-3 control-label">Склад:</label>
                        <div class="col-lg-4">
                            <label class="field select">
                                <select class="form-control" name="stock_id" id="stock_id">
                                    <?php
                                        echo get_stock_select_option();
                                    ?>
                                </select><i class="arrow double"></i>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-lg-3 control-label">Тип:</label>
                        <div class="col-lg-4">
                            <label class="field select">
                                <select class="form-control" name="type" id="type">
                                    <?php
                                    $type = get_type();
                                    foreach($type as $key => $val){
                                        echo '<option value="'.$key.'">'.$val.'</option>';
                                    }
                                    ?>
                                </select><i class="arrow double"></i>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-lg-3 control-label">Объект:</label>
                        <div class="col-lg-4">
                            <label class="field select">
                                <?php
                                    oSelect("jsAdminTpl", "object", null, "getObjectToSelect");
                                    oSelect("select", "object", null, "getObjectToSelect");
                                ?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"></label>
                        <div class="col-sm-4">
                            <input type="hidden" value="detail" name="redirect">
                            <input type="submit" class="btn btn-success btn-parsley" id="sub" value="Создать" />
                        </div>
                    </div>
                </form>
            </div>
        </div>

    <?php

    $adminTpl->footIncludes[] = '<script src="/usr/modules/merging/admin/asset/js/merging_add.js?'.time().'"></script>';

    $adminTpl->admin_foot();
}

function save($data){
    //обязательные поля
    $allow_data = ["stock_id", "org", "type"];
    foreach($allow_data as $val){
        //если нет обязательного поля, перенаправляем
        if(!isset($data[$val])){
            header("Location: /administration/module/merging/add");
        }
    }
    
    $merging = \Merging\Model\Merging::create($data);
    $iid = $merging->id;

    if(isset($data["redirect"]) && $data["redirect"] == "detail"){
        header("Location: /administration/module/merging/detail/".$iid."");
    }
}