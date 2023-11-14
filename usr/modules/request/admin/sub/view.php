<?php

global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;

function queryMain($id)
{
    global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;
    $query = $db->query("SELECT r.* FROM " . DB_PREFIX . "_request as r WHERE r.id = '" . $id . "' LIMIT 1");
    $request = $db->getRow($query);
    return $request;
}

function getDataTitle($type = "all"){
    $nn = ["02","04","6M","07","08","09","12M","14","18","19","20","21","21E","22","25E","31","35E","36","37","41","42","43","44","45E","47","55E","62","64","65","67","81","86","100","110","113","121","130","152","155","188"];
    $clh = ["CLH 32S","CLH 32M","CLH 50S","CLH 50M","CLH 50L","CLH 65M","CLH 65S","CLH 100S","CLH 100M"];

    $all = array_merge($nn, $clh);
    if($type == "all"){
        return $all;
    } else {
        return isset($$type) ? $$type : $all;
    }
}

function view2($id)
{
    global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;
    $adminTpl->admin_head('Заявки на расчёт | Заявка №' . $id);
    $request = queryMain($id);
    ?>
    <style>
        hr {
            margin: 15px 0;
        }

        .gts {
            color: grey;
            width: 50%;
        }

        .blockInfo>td {
            padding: 10px 0;
            border-bottom: 1px #e4e4e4 solid;
        }

        .blockInfoNoLine>td {
            padding: 10px 0;
        }

        .blockTableInfo {
            width: 100%;
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 20px;
        }

        .takeBtn {
            margin-bottom: 20px;
        }

        .changeH3 {
            margin-top: 0;
        }

        .tabPanelBlock {
            padding: 10px;
        }

        .tox-statusbar__branding {
            display: none;
        }

        .commMain {
            display: flex;
            margin-bottom: 10px;
        }

        .uidComm {
            color: #2067b0;
            font-size: 13px;
            font-weight: bold;
        }

        .dateComm {
            color: #bcc2c7;
            font-size: 11px;
        }

        .commWrapper {
            padding: 15px;
            width: 400px;
            border-radius: 23px;
            background-color: #edf1f3;
            overflow-y: auto;
        }

        #commentCon {
            max-height: 400px;
            overflow: auto;
            padding: 0 15px;
        }

        .circleLoad {
            margin-left: 10px;
            display: none;
        }
        .cp{
            cursor: pointer;
        }
        .activeFile{
            color: red;
        }
        .commComm{
            margin-top: 10px;
        }
        .addFile{
            float: right;
            color: #05bf05;
            font-size: 20px;
            cursor: pointer;
            margin-right: 20px;
        }
        .delFileRes{
            margin-right: 20px;
        }
    </style>

    <section id="content" class="animated fadeIn">
        <h3 class="changeH3">Заявка №<?php echo $id.(empty($request["title"]) ? "" : " - ".$request["title"]); ?></h3>
        <input type="hidden" value="<?php echo $id; ?>" id="mainId">
        <div class="col-sm-8">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php
                    echo $request["request"];
                    ?>
                    <hr>
                    <div class="gts">Приложенные файлы:</div>
                    <div>
                        <?php
                        $query = $db->query("SELECT id, name FROM " . DB_PREFIX . "_attach WHERE pid = '" . $id . "' AND `module` = 'request'");
                        if ($db->numRows($query) > 0) {
                            echo '<ul>';
                            while ($r = $db->getRow($query)) {
                                echo '<li><a href="/download/' . $r["id"] . '">' . $r["name"] . '</a></li>';
                            }
                            echo '</ul>';
                        } else {
                            echo 'Отсутствует';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#panel1">Комментарии</a></li>
                </ul>

                <div class="tab-content">
                    <div id="panel1" class="tab-pane fade in active tabPanelBlock">
                        <div id="commentCon"></div>
                        <form id="formTextarea">
                            <textarea id="mytextarea" name="comment"></textarea>
                        </form>
                        <button class="btn btn-info" title="Отправить комментарий" onclick="sendComment();"><i class="fa fa-paper-plane fa-lg" aria-hidden="true"></i></button>
                        <button class="btn btn-primary" title="Загрузить файл" onclick="modalFile();"><i class="fa fa-paperclip fa-lg" aria-hidden="true"></i><span class="circleLoad">0</span></button>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-sm-4" >
            <form id="mainPanel"> 

            </form>
        </div>
    </section>

    <div class="modal fade" id="take_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Стать исполнителем</h5>
                </div>
                <div class="modal-body">
                    Вы действительно хотите стать исполнителем?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="take_modal_btn();">Приступить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="load_file" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Загрузка файлов</h5>
                </div>
                <div class="modal-body">
                    <form id="fileForm">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="finalModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Подтверждение финального файла</h5>
                </div>
                <div class="modal-body">
                    Вы действительно хотите потвердеть финальный файл? Это действие завершит заявку
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="finalModalBtn(this);" id="finalModalBtn">Завершить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="endQuestModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Завершить заявку</h5>
                </div>
                <div class="modal-body">
                    Вы действительно хотите завершить заявку?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="endQuestBtn();">Завершить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addFileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Добавить файл</h5>
          </div>
          <div class="modal-body">
            <form id="fileForm2">
                        <?php
                        $attribute = [];
                        $attribute["name"] = "2";
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
            <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="loadFile();">Загрузить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="delFileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Удалить файл</h5>
                </div>
                <div class="modal-body">
                    Вы действительно хотите удалить файл?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="delFileRes(this);" id="delFileBtn">Удалить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/html" id="commentTemplate">
        <div class="commMain">
            <img src="" class="avatar">
            <div class="commWrapper">
                <span class="uidComm"></span>
                <span class="dateComm"></span>
                <div class="commComm"></div>
                <div>
                    <ul class="fileComm">
                    </ul>
                </div>
            </div>
        </div>
    </script>

    <?php
    $adminTpl->footIncludes[] = '<script src="/usr/modules/request/admin/asset/js/tinymce/tinymce.min.js"></script>';
    $adminTpl->footIncludes[] = '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
    $adminTpl->footIncludes[] = '<script src="/usr/modules/request/admin/asset/js/view.js"></script>';
    $adminTpl->admin_foot();
}

function viewPanel($id)
{
    global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;
    $request = queryMain($id);

    $queryAttach = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE `pid` = '".$id."' AND `module` = 'request_catalog'");
    $count_query = (int)$db->numRows($queryAttach);

    $disabled = "";
    if((int)$request["status"] === 3){
        $disabled = "disabled";
    }
    ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <?php
            if (empty($request["uid_exec"])) {
                ?>

                <div class="takeBtn">
                    <input type="button" class="btn btn-success btn-block" onclick="takeBtn();" value="Взять заявку">
                </div>

            <?php
        }

            if($request["status"] === "2"){
                ?>
                <div class="takeBtn">
                    <input type="submit" value="Завершить заявку" class="btn btn-success btn-block" <?php echo $count_query > 0 ? "" : "disabled";?>>
                </div>
                <?php
            }
            if($request["status"] === "3" && $request["type"] === "3"){
                ?>
                <div class="takeBtn">
                    <input type="button" value="Редактировать" class="btn btn-warning btn-block" onclick="edit();">
                </div>
                <?php
            }
        ?>
            <div>
                <table class="blockTableInfo">
                    <tr class="blockInfo">
                        <td class="gts">Статус:</td>
                        <td>
                            <?php  
                            switch ($request["status"]) {
                                case "1":
                                    echo '<span>Ожидает исполнителя</span>';
                                    break;
                                case "2":
                                    echo '<span>Ожидает выполнения</span>';
                                    break;
                                case "3":
                                    echo '<span>Выполнено</span>';
                                    break;
                            }
                            ?>
                        </td>
                    </tr>
                    <tr class="blockInfo">
                        <td class="gts">Тип заявки:</td>
                        <td>
                            <?php
                            switch ($request["type"]) {
                                case "1":
                                    echo '<span>Свободная</span>';
                                break;
                                case "2":
                                    echo '<span>Атри-проект</span>';
                                break;
                                case "3":
                                    echo '<span>Расчет теплообменника</span>';
                                break;
                            }
                            ?>
                        </td>
                    </tr>
                    <tr class="blockInfo">
                        <td class="gts">Наименование у заказчика:</td>
						<td>
                            <?php
                            echo $request["title_user"];
                            ?>
                        </td>
                    </tr>
                   
					<tr class="blockInfo">
					<td class="gts" colspan="2">Постановщик:</td>
                    </tr>
                    <tr class="blockInfoNoLine">
                        <td colspan="2">
                            <?php
                            echo '<img class="avatar" src="' . avatar($request["uid_dir"]) . '">';
                            echo ((int)$request["uid_dir"] === 0 ? "Гость" : fullUserName($request["uid_dir"]));
                            ?>
                        </td>
                    </tr>
                    <?php
                    if ($request["status"] !== "1") {
                        ?>
                        <tr class="blockInfo">
                            <td class="gts" colspan="2">Исполнитель:</td>
                        </tr>
                        <tr class="blockInfo">
                            <td colspan="2">
                                <?php
                                echo '<img class="avatar" src="' . avatar($request["uid_exec"]) . '">';
                                echo fullUserName($request["uid_exec"]);
                                ?>
                            </td>
                        </tr>
                    <?php
                }
                ?>

                <?php
                    if((int)$request["type"] === 3 && ((int)$request["status"] === 2 || (int)$request["status"] === 3)){
                ?>

                    <tr class="blockInfo">
                        <td class="gts">Файлы:</td>
                        <td>
                            <?php 
                            if(empty($disabled)){
                                echo '<i class="fas fa-file-medical addFile" title="Добавить файл" onclick="addFileModal();"></i>';
                            }
                            ?>
                        </td>
                    </tr>
                    
                        <?php
                        if($count_query > 0){
                            $i = 0;
                            while($attach = $db->getRow($queryAttach)){
                                $i++;
                                echo '<tr class="'.($i === $count_query ? "blockInfo" : "blockInfoNoLine").'"><td><a href="/download/'.$attach["id"].'">'.$attach["name"].'</td>
                                <td class="text-right">';
                                if(empty($disabled)){
                                    echo '<i class="fas fa-times cp text-danger delFileRes" title="Удалить файл" onclick="delFileModal('.$attach["id"].');"></i>';
                                }
                                echo '</td></tr>';
                            }
                        } else {
                            echo '<tr class="blockInfo"><td colspan="2">Файлы отсутствуют!</td></tr>';
                        }
                        ?>
                    
                    <?php 
                        $spec = json_decode($request["specification"], true);
                        $sku = array_key_exists("sku", $spec) ? $spec["sku"] : "";
                        $title = array_key_exists("title", $spec) ? $spec["title"] : "";
                        $nds =  array_key_exists("nds", $spec) ? (int)$spec["nds"] : 0;
                        $price = array_key_exists("price", $spec) ? $spec["price"] : "";
                        $price_type = array_key_exists("price_type", $spec) ? $spec["price_type"] : 3;
                        $assigment = array_key_exists("assigment", $spec) ? $spec["assigment"] : "";
                        $assigment2 = array_key_exists("assigment2", $spec) ? $spec["assigment2"] : "";
                        $diameter = array_key_exists("diameter", $spec) ? $spec["diameter"] : "";
                        $burden = array_key_exists("burden", $spec) ? $spec["burden"] : "";
                        $units = array_key_exists("units", $spec) ? $spec["units"] : "";
                        $square = array_key_exists("square", $spec) ? $spec["square"] : "";
                        $plates = array_key_exists("plates", $spec) ? $spec["plates"] : "";
                        $pressure = array_key_exists("pressure", $spec) ? $spec["pressure"] : "";
                        $temperature = array_key_exists("temperature", $spec) ? $spec["temperature"] : "";
                        $weight = array_key_exists("weight", $spec) ? $spec["weight"] : "";
                        
                    ?>
                    <tr class="blockInfo">
                        <td class="gts">Номер расчёта:</td>
                        <td>
                            <input type="text" value="<?php echo $sku;?>" placeholder="Введите номер расчёта" class="form-control" id="sku" onchange="saveSpecData(this);" required <?php echo $disabled; ?>>
                        </td>
                    </tr>
                    <tr class="blockInfo">
                        <td class="gts" title="Аппарат теплообменный пластинчатый разборный НН№">РПТО НН№:</td>
                        <td>
                            <select class="form-control" id="title" onchange="saveSpecData(this);" required <?php echo $disabled; ?>>
                                <?php
                                    $array_title = getDataTitle();
                                    echo '<option value="" '.(empty($title) ? 'selected' : '').' disabled>Выберите наименование</option>';
                                    foreach($array_title as $val){
                                        $selected = "";
                                        if($title === $val){
                                            $selected = "selected";
                                        }
                                        echo '<option value="'.$val.'" '.$selected.'>'.$val.'</option>';
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="blockInfo">
                        <?php
                            if($nds === 0){
                                $nds_text_1 = "Без НДС";
                                $nds_text_2 = "С НДС";
                            } else {
                                $nds_text_1 = "С НДС";
                                $nds_text_2 = "Без НДС";
                            }
                        ?>
                        <td class="gts" onselectstart="return false">Цена (<span class="cp" onclick="change_nds_price(this);" data-text="<?php echo $nds_text_2; ?>" data-nds="<?php echo $nds; ?>"><?php echo $nds_text_1; ?></span>):</td>
                        <td>
                            <div class="col-sm-8" style="padding: 0;">
                                <input type="text" value="<?php echo $price;?>" id="price" placeholder="Введите цену" class="form-control" onchange="saveSpecData(this);" required <?php echo $disabled; ?>>
                            </div>
                            <div class="col-sm-4" style="padding: 0;">
                                <select onchange="saveSpecData(this);" class="form-control" id="price_type" <?php echo $disabled; ?>>
                                <?php
                                    $priceType = \Price\Models\CatalogPriceType::where("type", "PRICE")->get();
                                    // $priceType = [
                                    //     [
                                    //         "id" => 3,
                                    //         "name" => "руб",
                                    //     ],
                                    //     [
                                    //         "id" => 2,
                                    //         "name" => "eur",
                                    //     ]
                                    // ];
                                    
                                    // foreach($priceType as $val){
                                    //     $selected = "";
                                    //     if($val["id"] == $price_type){
                                    //         $selected = "selected";
                                    //     }
                                    //     echo '<option value="'.$val["id"].'" '.$selected.'>'.$val["name"].'</option>';
                                    // }
                                   
                                    foreach($priceType as $vv){
                                        // var_dump($vv);
                                        $selected = "";
                                        if($price_type == $vv->id){
                                            $selected = "selected";
                                        }
                                        echo '<option value="'.$vv->id.'" '.$selected.'>'.$vv->title.'</option>';
                                    }
                                ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr class="blockInfo">
                        <td class="gts">Назначение:</td>
                        <td>
                            <select class="form-control" id="assigment" onchange="toggleAssigment2(this); saveSpecData(this);" required <?php echo $disabled; ?>>
                                <?php
                                    $array_assigment = ["Отопления", "Вентиляции", "ГВС", "Технология"];
                                    echo '<option value="" '.(empty($assigment) ? 'selected' : '').' disabled>Выберите назначение</option>';
                                    foreach($array_assigment as $val){
                                        $selected = "";
                                        if($assigment === $val){
                                            $selected = "selected";
                                        }
                                        echo '<option value="'.$val.'" '.$selected.'>'.$val.'</option>';
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="blockInfo" id="assigment2block" style="display:<?php echo ($assigment === "ГВС" ? "" : "none");?>;">
                        <td class="gts">Назначение 2:</td>
                        <td>
                            <select class="form-control" id="assigment2" <?php echo ($assigment === "ГВС" ? "" : "disabled");?> onchange="saveSpecData(this);" required <?php echo $disabled; ?>>
                                <?php
                                    $array_assigment2 = ["Параллельная","Моноблок","1 ступень","2 ступень"];
                                    echo '<option value="" '.(empty($assigment2) ? 'selected' : '').' disabled>Выберите назначение 2</option>';
                                    foreach($array_assigment2 as $val){
                                        $selected = "";
                                        if($assigment2 === $val){
                                            $selected = "selected";
                                        }
                                        echo '<option value="'.$val.'" '.$selected.'>'.$val.'</option>';
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="blockInfo">
                        <td class="gts">Диаметр (мм):</td>
                        <td>
                            <input type="number" class="form-control" id="diameter" value="<?php echo $diameter; ?>" placeholder="Введите диаметр" onchange="saveSpecData(this);" required <?php echo $disabled; ?>>
                        </td>
                    </tr>
                    <tr class="blockInfo">
                        <td class="gts">Q нагрузка:</td>
                        <td>
                            <input type="text" step="any" class="form-control" id="burden" value="<?php echo $burden;?>" placeholder="Введите нагрузку" oninput="convert_for_andrew(this);" onchange="saveSpecData(this);" required <?php echo $disabled; ?>>
                        </td>
                    </tr>
                    <tr class="blockInfo">
                        <td class="gts">Q единицы измерения:</td>
                        <td>
                        <select class="form-control" id="units" onchange="saveSpecData(this);" required <?php echo $disabled; ?>>
                            <?php
                                $array_units = ["ккал/ч","Гкал/ч","кВт","МВт"];
                                echo '<option value="" '.(empty($units) ? 'selected' : '').' disabled>Выберите единицы измерения</option>';
                                foreach($array_units as $val){
                                    $selected = "";
                                    if($units === $val){
                                        $selected = "selected";
                                    }
                                    echo '<option value="'.$val.'" '.$selected.'>'.$val.'</option>';
                                }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="blockInfo">
                        <td class="gts">Площадь (м<sup>2</sup>):</td>
                        <td>
                            <input type="text" id="square" class="form-control" value="<?php echo $square;?>" placeholder="Введите площадь" oninput="convert_for_andrew(this);" onchange="saveSpecData(this);" required <?php echo $disabled; ?>>
                        </td>
                    </tr>
                    <tr class="blockInfo">
                        <td class="gts">Пластин (шт):</td>
                        <td>
                            <input type="number" id="plates" class="form-control" value="<?php echo $plates; ?>" placeholder="Введите пластин" onchange="saveSpecData(this);" required <?php echo $disabled; ?>>
                        </td>
                    </tr>
                    <tr class="blockInfo">
                        <td class="gts">Давление (МПа):</td>
                        <td>
                            <input type="text" id="pressure" class="form-control" value="<?php echo $pressure; ?>" placeholder="Введите давление" oninput="convert_for_andrew(this);" onchange="saveSpecData(this);" required <?php echo $disabled; ?>>
                        </td>
                    </tr>
                    <tr class="blockInfo">
                        <td class="gts">Температура (&deg;C):</td>
                        <td>
                            <input type="number" id="temperature" class="form-control" value="<?php echo $temperature;?>" placeholder="Введите температуру" onchange="saveSpecData(this);" required <?php echo $disabled; ?>>
                        </td>
                    </tr>
                    <tr class="blockInfo">
                        <td class="gts">Масса (кг):</td>
                        <td>
                            <input type="text" id="weight" class="form-control" value="<?php echo $weight;?>" placeholder="Введите массу" oninput="convert_for_andrew(this);" onchange="saveSpecData(this);" required <?php echo $disabled; ?>>
                        </td>
                    </tr>

                    <?php
                    }
                    ?>

                </table>
            </div>
        </div>
    </div>
<?php

// use Price\UI\CLI\CatalogPriceDaemon;
// Из-за этого не открывало
}

function takeRequest($id)
{
    global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;
    $uid = $core->auth->user_info['id'];
    $query = $db->query("SELECT uid_exec FROM " . DB_PREFIX . "_request WHERE id = '".$id."'");
    $r = $db->getRow($query);
    if(empty($r["uid_exec"])){
        $db->query("UPDATE " . DB_PREFIX . "_request SET status = '2', uid_exec = '" . $uid . "' WHERE id = '" . $id . "' LIMIT 1");
    } else {
        echo 'error';
    }
}

function saveComment($id)
{
    global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;
    $uid = $core->auth->user_info['id'];
    $comment = htmlspecialchars($_POST["comment"], ENT_QUOTES);
    $db->query("INSERT INTO " . DB_PREFIX . "_request_comments (`rid`, `uid`, `comment`, `date`, `status`) VALUES ('" . $id . "', '" . $uid . "', '" . $comment . "', '" . date("Y-m-d H:i:s") . "', '1')");
    $file = isset($_POST["file"]) ? $_POST["file"] : false;
    $iid = $db->insert_id();
    $attribute = [];
    $attribute["name"] = "1";
    $attribute["file"] = $file;
    $attribute["module"] = 'request';
    $attribute["mod"] = 'request_comment';
    $attribute["id"] = $id;
    $attribute["comm_id"] = $iid;
    uploaderMaster("download", $attribute);

    $query = $db->query("SELECT uid_dir, uid_exec FROM " . DB_PREFIX . "_request WHERE id = '".$id."'");
    $r = $db->getRow($query);
    $arrayClient = [$r["uid_dir"], $r["uid_exec"]];
    if(($key = array_search($uid,$arrayClient)) !== FALSE){
        unset($arrayClient[$key]);
    } 
    $attr = [];
	$attr['uid'] = $arrayClient;
	$attr['title'] = 'Новая комментарий в заявке №'.$id;
	$attr['text'] = htmlspecialchars_decode($comment, ENT_QUOTES);
	$attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
	$attr['url'] = config('app.url').'/administration/module/request/view/'.$id;
	$attr['pNotif'] = true;
	$attr['pPush'] = true;
	sendAllNotif($attr);
}

function loadComment($id)
{
    global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;
    $mm = $db->query("SELECT aid FROM " . DB_PREFIX . "_request WHERE id = '".$id."'");
    $m = $db->getRow($mm);
    $query = $db->query("SELECT * FROM " . DB_PREFIX . "_request_comments WHERE rid = '" . $id . "'");
    $json = ["comment" => []];
    while ($r = $db->getRow($query)) {
        $ff = $db->query("SELECT id, name FROM " . DB_PREFIX . "_attach WHERE `module` = 'request_comment' AND `pid` = '" . $r["id"] . "'");
        $file = [];
        while ($z = $db->getRow($ff)) {
            $finalFile = "false";
            if($z["id"] == $m["aid"]){
                $finalFile = "true";
            }
            $file[] = [
                "id" => $z["id"],
                "name" => $z["name"],
                "finalFile" => $finalFile,
            ];
        }
        $json["comment"][] = [
            "id" => $r["id"],
            "uid" => $r["uid"],
            "uidFull" => fullUserName($r["uid"]),
            "avatar" => avatar($r["uid"]),
            "comment" => htmlspecialchars_decode($r["comment"], ENT_QUOTES),
            "date" => date("d.m.Y H:i:s", strtotime($r["date"])),
            "file" => $file,
        ];
    }
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

function finalFile(){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;
    if(isset($_POST["id"], $_POST["mainId"])){
        $id = $_POST["id"];
        $mainId = $_POST["mainId"];
        $db->query("UPDATE " . DB_PREFIX . "_request SET aid = '".$id."', status = '3' WHERE id = '".$mainId."'");
    }
}

function endQuest($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;
    $db->query("UPDATE " . DB_PREFIX . "_request SET status = '3' WHERE id = '".$id."'");

    $query = $db->query("SELECT * FROM " . DB_PREFIX . "_request WHERE id = '".$id."'");
    $r = $db->getRow($query);

    $uid = $core->auth->user_info['id'];

    $p = new \Price;

    if((int)$r["type"] === 3){
        $spec = json_decode($r["specification"], true);

        $clh = getDataTitle("clh");

        $catalog_groups_id = false;

        switch($spec["title"]){
            default:
                $type_name = "НН";
                $catalog_groups_id = 54;
            break;
            case in_array($spec["title"], $clh):
                $type_name = "CLH";
            break;
        }

        $specMark = "{$type_name}№".$spec["title"];

        $title = 'Аппарат теплообменный разборный '.$specMark;

        $altname = translit($title).'_'.time();

        $specTitle = 'Аппарат теплообменный разборный';
        $specTitle .= ' '.$spec["assigment"];
        if($spec["assigment"] === "ГВС"){
            $specTitle .= array_key_exists("assigment2", $spec) ? ' '.$spec["assigment2"] : '';
        }
        $specTitle .= ' Ду'.$spec["diameter"].' мм;';
        $specTitle .= ' Q='.$spec["burden"].' '.$spec["units"].';';
        $specTitle .= ' S='.$spec["square"].' м²;';
        $specTitle .= ' n='.$spec["plates"].' шт.;';
        $specTitle .= ' Ру '.$spec["pressure"].' МПа;';
        $specTitle .= ' Tmax='.$spec["temperature"].' °С;';

        $specArray = ["1" => $specTitle, "2" => $specMark, "3" => ""];

        $weightArray = ["1" => $spec["weight"], "2" => "", "3" => "", "4" => "", "5" => ""];

        $short = 'Разборные пластинчатые теплообменники применяются для теплообмена между различными жидкими и газообразными средами. От лучших производителей, удобные в эксплуатации, оптимизируются с необходимыми условиями, имеют малый вес и занимают небольшую площадь.';

        $attach_id_array = [];
        $query = $db->query("SELECT id FROM ".DB_PREFIX."_attach WHERE `pid` = '".$id."' AND `module` = 'request_catalog'");
        if($db->numRows($query) > 0){
            while($ai = $db->getRow($query)){
                $attach_id_array[] = $ai["id"];
            }
        }

        if(empty($r["final_cid"])){
            $catalog = new \Catalog_v2\Model\Catalog;
        } else {
            $catalog = \Catalog_v2\Model\Catalog::firstOrNew(["id" => $r["final_cid"]]);
        }

        $catalog->sku = $spec["sku"];
        $catalog->unit = 796;
        $catalog->title = $title;
        $catalog->fulltitle = $title;
        $catalog->short = $short;
        $catalog->uid = $uid;
        $catalog->category = 451;
        $catalog->altname = $altname;
        $catalog->man = 12;
        $catalog->fixed = 1;
        $catalog->types = 3;
        $catalog->active = 0;
        $catalog->active = 0;
        $catalog->spec = $specArray;
        $catalog->weight = $weightArray;
        $catalog->hidden = 0;
        $catalog->preview = '/files/cats/Ridan.jpg';
        $catalog->attach_id = $attach_id_array;
        #Назначение группы товаров
        if($catalog_groups_id){
            $catalog->catalog_groups_id = $catalog_groups_id;
        }

        $catalog->save();

        $cid = $catalog->id;

        $price_type = array_key_exists("price_type", $spec) ? $spec["price_type"] : 3;

        $CatalogPrice = \Price\Models\CatalogPrice::create(["price_type" => $price_type, "module" => "request", "pid" => $id, "active" => 1]);
        $iid = $CatalogPrice->id;

        $nds_proc = \Price::getNds("3");
        $spec_price = (float)$spec["price"];
        if((int)$spec["nds"] === 1){
            //цена с ндс
            $price = $spec_price;
        } else {
            //цена без ндс (начислить)
            $price = $spec_price / 100 * ($nds_proc + 100);
        }

        $price = round($price, 2);

        \Price\Models\CatalogPriceDetail::create(["cid" => $cid, "price_id" => $iid, "sum" => $price]);
        // CatalogPriceDaemon::key_create();
        \Price\UI\CLI\CatalogPriceDaemon::key_create();
        
        $db->query("UPDATE `" . DB_PREFIX . "_request` SET final_cid = '".$cid."' WHERE id = '".$id."'");

        #Создание настройки цены
        if($catalog_groups_id){
            \Request\Support\RequestSupport::catalogPriceRival($cid);
        }
		
		$orderHeat = \Order\Model\OrderHeat::with('order')->where('request_id', $id)->first();
        // dd($orderHeat);
        if(!empty($orderHeat)) {
            $attach_id_array = [];
		    $queryA = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE `pid` = '".$id."' AND `module` = 'request_catalog'");
		    if($db->numRows($queryA) > 0){
		    	while($ai = $db->getRow($queryA)){
		    		$pdf = config('app.url').'/'.$ai["url"];
		    	}
		    }
		    $priceType[3]  = 'руб.';
		    $priceType[2]  = 'EUR';

		    $request_data = json_decode($r['request_data'], true);
		    $attr = array();
		    $attr['email'] = $orderHeat->order->email;
		    $attr['name'] = $orderHeat->order->fio_source['result'];
		    $attr['title'] = 'Заявка на расчет №'.$id.' завершена';
		    $attr['pdf'] = $pdf;
		    $attr['text'] = 'Здравствуйте, <b>' . $attr['name'].'!</b> <br> Ваша заявка по расчету теплообменного оборудования <b>№'.$id.'</b> завершена. Результаты выполнения     прикреплен во вложении. Стоимость подобранного оборудование на '.date('d.m.Y').' составляет '.$price.' '.$priceType[$price_type].' с НДС. Заказать данное оборудование вы можете  связавшись с отделом продаж по  телефону +7(3412)908-123';
		    $attr['url'] = $pdf;
		    $attr['url_text'] = 'Скачать';
            // dd($attr);
            \Order\Model\Order::find($orderHeat->order_id)->update(['status' => '3']);
		    sendEmailByData($attr);
         
        }
		
			// dd($attr);
		
        if($r["uid_dir"] != '0')
        {
			$arrayClient = [$r["uid_dir"], $r["uid_exec"]];
			if(($key = array_search($uid,$arrayClient)) !== FALSE){
				unset($arrayClient[$key]);
			} 
			$attr = [];
			$attr['uid'] = $arrayClient;
			$attr['title'] = 'Заявка на расчет №'.$id.' завершена';
			$attr['text'] = 'Здравствуйте, '.$core->auth->getUserInfo($r['uid_dir'])['full_user_name'].'! Ваша заявка по расчету теплообменного оборудования №'.$id.' завершена. Результаты выполнения заявки вы можете посмотреть в личном кабинете.';
			$attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
			$attr['url'] = config('app.url').'/profile/request/view/'.$id;
			$attr['pNotif'] = true;
			$attr['pPush'] = true;
			// sendAllNotif($attr);
			$attr['uid'] = $r["uid_dir"];	
			$attr['text'] = 'Здравствуйте, <b>'.$core->auth->getUserInfo($r['uid_dir'])['full_user_name'].'!</b> <br> Ваша заявка по расчету теплообменного оборудования <b>№'.$id.'</b> завершена. Результаты выполнения заявки вы можете посмотреть в личном кабинете.';
			// sendEmail($attr);
		}
		
		
		
		
    }
}

function saveData($id, $field, $value){
    global $db;
    $db->query("UPDATE ".DB_PREFIX."_request SET ".$field." = '".$value."' WHERE id = '".$id."' LIMIT 1");
}

function saveSpecData($id){
    global $db;
    $field = isset($_POST["field"]) ? $_POST["field"] : false;
    $value = isset($_POST["value"]) ? $_POST["value"] : "";
    if($field){
        $query = $db->query("SELECT specification FROM ".DB_PREFIX."_request WHERE id = '".$id."' LIMIT 1");
        $r = $db->getRow($query);

        $spec = json_decode($r["specification"], true);
        if(empty($value)){
            if(array_key_exists($field, $spec)){
                unset($spec[$field]);
            }
        } else {
            $spec[$field] = $value;
        }

        if(!array_key_exists("nds", $spec)){
            $spec["nds"] = 0;
        }
        if(!array_key_exists("price_type", $spec)){
            $spec["price_type"] = 3;
        }

        $spec = json_encode($spec, JSON_UNESCAPED_UNICODE);
        $db->query("UPDATE ".DB_PREFIX."_request SET specification = '".$spec."' WHERE id = '".$id."' LIMIT 1");
    }
}

function loadFile($id){
    $file = isset($_POST["file"]) ? $_POST["file"] : false;

    $attribute = [];
    $attribute["name"] = "2";
    $attribute["file"] = $file;
    $attribute["module"] = 'request';
    $attribute["mod"] = 'request_catalog';
    $attribute["id"] = $id;
    $attribute["comm_id"] = $id;
    uploaderMaster("download", $attribute);
}

function delFileRes($id){
    global $db;
    $query = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE id = '".$id."' LIMIT 1");
    $attach = $db->getRow($query);
    unlink($attach["url"]);
    $db->query("DELETE FROM `".DB_PREFIX."_attach` WHERE id = '".$id."' LIMIT 1");
}

function get_sales($id){
    global $db;
    $query = $db->query("SELECT * FROM ".DB_PREFIX."_request WHERE id = '".$id."' LIMIT 1");
    $r = $db->getRow($query);
    $query = $db->query("SELECT s.numb, s.id FROM ".DB_PREFIX."_sales_detail as sd LEFT JOIN ".DB_PREFIX."_sales as s ON(s.id = sd.sales_id) WHERE sd.cid = '".$r["final_cid"]."'");
    if($db->numRows($query) > 0){
        while($sales = $db->getRow($query)){
            echo '<div><a href="/administration/module/sales/order/'.$sales["id"].'">Продажа №'.$sales["numb"].'</a></div>';
        }
    }
}