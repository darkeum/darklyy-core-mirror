<?php

function assembly()
{
    global $adminTpl, $core, $db, $lang;
    $lnn = 'Сборка товаров';
    $adminTpl->admin_head($lang['storage_main'] . ' | ' . $lnn);
    $atributs = array();
    $atributs['opts'] = 'open';
    $atributs['module'] = 'module';
    $atributs['module_name'] = 'storage/assembly';
    $atributs['module_open'] = 'detail';
    // $atributs['js'] = '  setTimeout(function () {$("#jqAssembly").jqGrid(\'setGridParam\',{postData:{status: \'1\'}}); $(\'#jqAssembly\').trigger(\'reloadGrid\');}, 300);';	
    loadGrid('jqAssembly', $lnn, $atributs, ROOT . '/usr/modules/storage/admin/grid/');
    $adminTpl->footIncludes[] = '<script src="/usr/modules/storage/admin/asset/js/assemblyGrid.js?' . time() . '"></script>';
    $adminTpl->admin_foot();
}

function assembly_sum()
{
    global $adminTpl, $core, $db, $lang;
    $lnn = 'Сборка товаров';
    $adminTpl->admin_head($lang['storage_main'] . ' | ' . $lnn);
    $atributs = array();
    $atributs['opts'] = 'open';
    $atributs['module'] = 'module';
    $atributs['module_name'] = 'storage/assembly';
    $atributs['module_open'] = 'detail';
    // $atributs['js'] = '  setTimeout(function () {$("#jqAssembly").jqGrid(\'setGridParam\',{postData:{status: \'1\'}}); $(\'#jqAssembly\').trigger(\'reloadGrid\');}, 300);';	
    loadGrid('jqAssemblySum', $lnn, $atributs, ROOT . '/usr/modules/storage/admin/grid/');
    $adminTpl->footIncludes[] = '<script src="/usr/modules/storage/admin/asset/js/assemblyGrid.js?' . time() . '"></script>';
    $adminTpl->admin_foot();
}


function getQueryMain($id)
{
    global $adminTpl, $core, $db, $lang;
    $query = $db->query("SELECT s.*, sa.uid as sauid, sa.date as sadate, sa.status as sastatus, o.name as oname, sa.type as satype, obj.name as obj_name, obj.id as obj_id, sa.uid_end as sa_uid_end
    FROM `" . DB_PREFIX . "_storage_assembly` as sa 
    LEFT JOIN `" . DB_PREFIX . "_sales` as s ON(s.id = sa.sales_id) 
    LEFT JOIN `" . DB_PREFIX . "_organizations` as o ON(o.id = s.oid) 
    LEFT JOIN `" . DB_PREFIX . "_object` as obj ON(obj.id = s.object) WHERE sa.id = '" . $id . "' LIMIT 1");
    $order = $db->getRow($query);
    return $order;
}

function assembly_detail($id)
{
    global $adminTpl, $core, $db, $lang;
    $adminTpl->admin_head('Сборка товаров | Заявка №' . $id);
    $order = getQueryMain($id);

    $uid = $core->auth->user_info['id'];
?>

    <style>
        .cp {
            cursor: pointer;
        }

        .cd {
            cursor: default;
        }
    </style>

    <input type="hidden" value="<?php echo $id; ?>" id="mainId">

    <section id="content" class="animated fadeIn">
        <div class="text-center">
            <p class="lead">ЗАЯВКА НА <?php
                                        echo $order["satype"] == 1 ? 'СБОРКУ' : 'ОТМЕНУ СБОРКИ';
                                        echo ' №';
                                        echo $id;
                                        ?></p>
        </div>
        <div class="row" id="admin-form">
            <?php
            echo assembly_detail_head($id);
            ?>
        </div>
        <div class="row">
            <div class="panel panel-dark panel-border top">
                <div class="panel-heading">
                    <span class="panel-title">Список товаров:</span>
                    <div class="widget-menu pull-right">
                        <div class="btn-group">
                            <button onclick="" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>
                        </div>
                    </div>
                </div>

                <div class="panel-body pn table-responsive">
                    <form id="tablesForm" method="POST" onsubmit="return false;">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="w40"><span class="pd-l-sm"></span>№</th>
                                    <th class="w50">Артикул</th>
                                    <th class="w200">Наименование</th>
                                    <th class="w50">Ед.</th>
                                    <th class="w50" title="Наличие / Доступно / Резерв">Н/Д/Р</th>
                                    <th class="w50 text-center bg-success light">Количество для сборки</th>
                                    <th class="w50 text-center">Количество собранного</th>
                                    <th class="w50 text-center"><?php echo $order["satype"] == 1 ? 'Осталось собрать по продаже' : 'Осталось убрать по продаже'; ?></th>
                                    <th class="w40 text-center">Статус</th>
                                    <th class="w150">Действие</th>
                                    <th class="w20 text-right">
                                        <div class="checkbox-custom mb15">
                                            <input id="all" type="checkbox" name="all" onclick="setCheckboxes('tablesForm', true); return true;">
                                            <label for="all"></label>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="dev_list">
                                <?php
                                echo assembly_detail_body($id);
                                ?>
                            </tbody>
                            <tfoot class="footer-menu">
                                <tr align="right">
                                    <td align="right" colspan="12">
                                        <nav align="right" class="text-right">
                                            <select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act">
                                                <option value="confirm">Массовое подтверждение</option>
                                            </select>
                                            <input name="submit" type="button" class="btn btn-success" style="display: inline-block;" id="sub" value="Выполнить" onclick="selectAct();"><span class="pd-l-sm"></span>
                                        </nav>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Удалить товар</h5>
                </div>
                <div class="modal-body">
                    Вы действительно хотите удалить <span id="delModalTov"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="delModalBtn" onclick="delModalBtn(this);" data-dismiss="modal">Удалить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelTovModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Количество на складе</h5>
                </div>
                <div class="modal-body">
                    <p>Укажите реальное количество на складе</p>
                    <input type="number" palceholder="Введите количество на складе" id="cancelTovModalInp" class="form-control" value="0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="cancelTovModalBtn" onclick="cancel_tov_modal_btn(this);" data-dismiss="modal">Установить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="finalModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Завершение заявки</h5>
                </div>
                <div class="modal-body">
                    Вы действительно хотите завершить заявку?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="finalModalBtn" onclick="final_modal_btn(this);" data-dismiss="modal">Завершить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="print_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Выберите дальнейшее действие</h5>
                </div>
                <div class="modal-body">
                    <table class="table list-table">
                        <tbody>
                            <tr>
                                <td onclick="window.open('/administration/module/storage/print_assembly/<?php echo $id; ?>&format=pdf&type=assembly','_blank'); return false;" style="cursor: pointer;"><span class="icon text-alert far fa-receipt"></span>Распечатать сборку</td>
                                <td class="text-right">
                                    <a href="/administration/module/storage/print_assembly/<?php echo $id; ?>&format=pdf&type=assembly" target="_blank">PDF</a>
                                    <a href="/administration/module/storage/print_assembly/<?php echo $id; ?>&format=docx&type=assembly" target="_blank">DOCX</a>
                                </td>
                            </tr>
                            <tr>
                                <td onclick="window.open('/administration/module/storage/print_assembly/<?php echo $id; ?>&format=pdf&type=assembly&combine=true','_blank'); return false;" style="cursor: pointer;"><span class="icon text-alert far fa-receipt"></span>Распечатать сборку с объединением позиций</td>
                                <td class="text-right">
                                    <a href="/administration/module/storage/print_assembly/<?php echo $id; ?>&format=pdf&type=assembly&combine=true" target="_blank">PDF</a>
                                    <a href="/administration/module/storage/print_assembly/<?php echo $id; ?>&format=docx&type=assembly&combine=true" target="_blank">DOCX</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <?php

    transferModal(["id" => $id]);

    //добавление зашедшего пользователя в чат 
    $chat = new Chat();
    $chat_find = $chat->find_chat(["once" => true, "where" => ["module" => "sales", "pid" => $order["id"]]]);
    if (count($chat_find) > 0) {
        $chat->add_users($chat_find["id"], [$uid]);
    }

    $adminTpl->footIncludes[] = '<script src="/usr/modules/storage/admin/asset/js/assembly.js?' . time() . '"></script>';

    require_once(ROOT . "usr/modules/sales/admin/sub/ndr.php");
    ndr_init();

    $adminTpl->admin_foot();
}

function assembly_detail_head($id)
{
    global $adminTpl, $core, $db, $lang;
    $order = getQueryMain($id);
    $rows_status = [];
    if ($order["satype"] == 1) {
        $rows_status[1] = "Ожидает сборки";
        $rows_status[2] = "Собрано";
    }
    if ($order["satype"] == 2) {
        $rows_status[1] = "Ожидает отмену сборки";
        $rows_status[2] = "Разобрано";
    }

    $stock_name = get_stock_name($order["stock_id"]);

    ?>

    <div class="panel mb25 mt5">
        <div class="panel-body p20 pb10">
            <div class="section row mbn admin-form">
                <div class="col-md-12 pl15">
                    <div class="section row mb15">
                        <div class="col-md-3">
                            <div class="element-groups-lg-custom wizard-options" id="">
                                <?php
                                if ((int)$order["sastatus"] === 1) {
                                ?>
                                    <div class="ph10">
                                        <a href="javascript:void(0);" onclick="final_modal(<?php echo $id; ?>);" class="holder-style p15 text-center holder-active"><span class="far fa-check-square holder-icon"></span><br> Завершить <br>заявку</a>
                                    </div>
                                <?php
                                } else {
                                ?>

                                    <div class="ph10">
                                        <a href="javascript:void(0);" onclick="transferModalOpen()" class="holder-style p15 text-center holder-active">
                                            <span class="fa fa-exchange holder-icon"></span><br> Создать<br>перемещение</a>
                                    </div>

                                <?php
                                }
                                ?>
                                <div class="ph10">
                                    <a href="javascript:void(0);" onclick="print_modal();" class="holder-style p15 text-center holder-active"><span class="glyphicon glyphicon-print holder-icon"></span><br> Вывести на<br>печать</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <small>Объект:</small><br />
                            <b>
                                <?php
                                if (empty($order["obj_id"])) {
                                    echo 'Не указан';
                                } else {
                                    echo '<a href="/administration//object/view/' . $order["obj_id"] . '" target="_blank">' . $order["obj_name"] . '</a>';
                                }
                                ?>
                            </b><br /><br />
                            <small>Контрагент:</small><br />
                            <b><?php echo $order['oname']; ?></b>&nbsp;&nbsp;
                            <a target="_blank" href="/administration/module/organizations/edit/<?php echo $order['oid']; ?>"><span class="fa fa-external-link"></span></a>
                            <br /><br />
                            <small>Ответственный:</small><br />
                            <b><?php echo shortUserName($order['sauid']); ?></b><br /><br />
                        </div>
                        <div class="col-md-3">
                            <small>Продажа:</small><br />
                            <b><?php echo '<a href="/administration/module/sales/order/' . $order["id"] . '" target="_blank">' . $order["numb"] . '</a>'; ?></b><br /><br />
                            <small>Комментарий:</small><br />
                            <b><?php echo $order["title"] ?></b><br /><br />
                            <small>Склад:</small><br />
                            <b><?php echo $stock_name; ?></b>
                        </div>
                        <div class="col-md-3">
                            <small>Дата подачи заявки:</small><br />
                            <b><?php echo date("d.m.Y H:i:s", strtotime($order['sadate'])); ?></b><br /><br />
                            <small>Статус:</small><br />
                            <b><?php echo $rows_status[$order['sastatus']]; ?></b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
}

function getQueryDetail($id)
{
    global $db;
    $query = $db->query("SELECT sd.*, sad.id as sadid, sad.status as sadstatus, sad.count_now, sad.count as sadcount, sad.count_save as sadcount_save, sa.status as sastatus 
    FROM `" . DB_PREFIX . "_storage_assembly_detail` as sad 
    LEFT JOIN `" . DB_PREFIX . "_sales_detail` as sd ON(sd.id = sad.sdid) 
    LEFT JOIN `" . DB_PREFIX . "_storage_assembly` as sa ON(sa.id = sad.said) 
    WHERE sad.said = '" . $id . "'");
    return $query;
}

function assembly_detail_body($id)
{
    global $adminTpl, $core, $db, $lang;

    $order = getQueryMain($id);

    $query = getQueryDetail($id);

    $assembly = new Storage\Assembly;

    if ($db->numRows($query) > 0) {
        $i = 0;
        while ($content = $db->getRow($query)) {

            $i++;

            $ndr_view = get_ndr(["cid" => $content["cid"], "stock_id" => "1", "module" => "sales", "pid" => null, "returnType" => "view"]);


            switch ((int)$content["sadstatus"]) {
                default:
                    $status_icon = '<i class="fas fa-question-circle text-info fs30" title="Статус отсутствует"></i>';
                    break;
                case 1:
                    $status_icon = '<i class="fas fa-question text-warning fs30" title="Ожидает"></i>';
                    break;
                case 2:
                    $status_icon = '<i class="fas fa-check text-success fs30" title="Подтверждён"></i>';
                    break;
                case 3:
                    $status_icon = '<b class="text-danger fs30 cd" title="Отсутствует" style=>' . $content["count_now"] . '</b>';
                    break;
            }

            $btnDis = (int)$content["sastatus"] === 1 ? "" : "disabled";
            $inpDis = (int)$content["sastatus"] === 1 ? "change_count_focus(this, " . $content['sadid'] . ");" : "";
            $btnDis2 = "";
            if (empty($btnDis)) {
                $btnDis2 = (int)$order["satype"] === 1 ? "" : "disabled";
            }
            $count_assembly = $assembly->get_count($content["id"]);
            // $count_assembly = $content['count_assembly'];
            $count_end = $content['count'] - $count_assembly;
    ?>

            <tr id="tr_<?php echo $content['sadid']; ?>">
                <td data-toggle="tooltip" title="<?php echo $content['sadid']; ?>"><span class="pd-l-sm"></span><?php echo $i; ?></td>
                <td><?php echo $content['s_sku']; ?></td>
                <td class=""><?php echo $content['s_name']; ?></td>
                <td><?php echo get_unit('data', $content['s_unit'], 'name'); ?></td>
                <td class="ndr" onclick="view_ndr_modal(<?php echo $content["cid"]; ?>);"><?php echo $ndr_view; ?></td>
                <td class="text-center count_save bg-success bg-light light"><?php echo $content['sadcount_save']; ?></td>
                <td class="text-center cp" onclick="<?php echo $inpDis; ?>"><?php echo $content['sadcount']; ?></td>
                <td class="text-center"><?php echo $count_end ?></td>
                <td class="text-center status"><?php echo $status_icon; ?></td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-xs btn-success" title="Подтвердить" onclick="confirm_item([<?php echo $content['sadid']; ?>]);" <?php echo $btnDis; ?>><i class="fas fa-check"></i></button>
                        <button type="button" class="btn btn-xs btn-danger" title="Отсутствует" onclick="cancel_tov_modal(<?php echo $content['sadid']; ?>);" <?php echo $btnDis . $btnDis2; ?>><i class="fas fa-ban"></i></button>
                        <!-- <button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle" <?php echo $btnDis; ?>><span class="caret"></span><span class="sr-only">Действие</span></button>
            <ul role="menu" class="dropdown-menu">
                <li><a href="javascript:void(0);" data-id="<?php echo $content['sadid']; ?>" data-text="<?php echo $content['s_name']; ?>" onclick="delTov(this);">Удалить</a></li>
            </ul> -->
                    </div>
                </td>
                <td class="w20 text-right">
                    <div class="checkbox-custom mb15">
                        <input id="checkbox<?php echo $content['sadid']; ?>" type="checkbox" name="checks[]" value="<?php echo $content['sadid']; ?>" class="checks">
                        <label for="checkbox<?php echo $content['sadid']; ?>"></label>
                    </div>
                </td>
            </tr>

        <?php
        }
    } else {
        ?>
        <tr>
            <td colspan="10">У данной заявки еще не заполнен список приборов!</td>
        </tr>
<?php
    }
}

function id_to_text($id)
{
    if (is_array($id)) {
        return implode(", ", $id);
    } else {
        return $id;
    }
}

function assembly_del_tov($id)
{
    global $db;
    if ($id) {
        $nid = id_to_text($id);
        $db->query("DELETE FROM " . DB_PREFIX . "_storage_assembly_detail WHERE id IN(" . $nid . ")");
    }
}

function assembly_update_status($id, $status)
{
    global $db;
    if ($id && $status) {
        $nid = id_to_text($id);
        $db->query("UPDATE " . DB_PREFIX . "_storage_assembly_detail SET status = '" . $status . "' WHERE id IN(" . $nid . ")");
    }
}

function assembly_cancel_tov($id, $count)
{
    global $db, $core;
    $db->query("UPDATE " . DB_PREFIX . "_storage_assembly_detail SET count_now = '" . $count . "' WHERE id = '" . $id . "' LIMIT 1");
}

function assembly_check_status($id)
{
    global $db;
    $query = $db->query("SELECT id FROM " . DB_PREFIX . "_storage_assembly_detail WHERE status = 1 AND said = '" . $id . "'");
    if ($db->numRows($query) > 0) {
        echo 'Не все позиции проставлены';
        return;
    }

    $query = $db->query("SELECT type FROM " . DB_PREFIX . "_storage_assembly WHERE id = '" . $id . "' LIMIT 1");
    $sa = $db->getRow($query);
    $query = $db->query("SELECT sad.id, sd.count_assembly, sd.count, sad.count as sadcount, sd.id as sd_id FROM " . DB_PREFIX . "_storage_assembly_detail as sad LEFT JOIN " . DB_PREFIX . "_sales_detail as sd ON(sd.id = sad.sdid) WHERE sad.said = '" . $id . "' AND sad.status = '2'");
    $assembly = new Storage\Assembly;
    if ($db->numRows($query) > 0) {
        if ($sa["type"] == 1) {
            while ($r = $db->getRow($query)) {
                $count_assembly = $assembly->get_count($r["sd_id"]);
                $count = $r["count"] - ($count_assembly + $r["sadcount"]);
                if ($count < 0) {
                    echo 'Количество собранного больше требуемого! (Позиция id = ' . $r["id"] . ')';
                    break;
                }
            }
        }
        if ($sa["type"] == 2) {
            while ($r = $db->getRow($query)) {
                $count = $r["count"] + $r["sadcount"];
                $count_assembly = $assembly->get_count($r["sd_id"]);
                if ($count != $count_assembly) {
                    echo 'Неверное количество для уравнивания! (Позиция id = ' . $r["id"] . ')';
                    break;
                }
            }
        }
    }
}

function assembly_final($id)
{
    global $db, $core;
    $uid = $core->auth->user_info['id'];
    //проверяем тип заявки
    $query = $db->query("SELECT sales_id, type FROM `" . DB_PREFIX . "_storage_assembly` WHERE id = '" . $id . "' LIMIT 1");
    $sa = $db->getRow($query);

    $query = $db->query("SELECT sad.*, sd.cid, sd.s_name 
    FROM `" . DB_PREFIX . "_storage_assembly_detail` as sad 
    LEFT JOIN `" . DB_PREFIX . "_sales_detail` as sd ON(sad.sdid = sd.id) 
    WHERE sad.said = '" . $id . "'");

    switch ($sa["type"]) {
        case 1:
            $sdid_s9 = [];      //товар собран
            $sdid_s10 = [];     //товар отсутсвует
            $sdid_s17 = [];     //товар собран частично
            while ($r = $db->getRow($query)) {
                if ($r["count"] == $r["count_save"]) {
                    $sdid_s9[] = $r["sdid"];
                } else if ($r["count"] == 0) {
                    $sdid_s10[] = $r["sdid"];
                } else {
                    $sdid_s17[] = $r["sdid"];
                }
            }
            if (count($sdid_s9) > 0) {
                sales_status_log_assembly($id, 9, $sdid_s9);
            }
            if (count($sdid_s10) > 0) {
                sales_status_log_assembly($id, 10, $sdid_s10);
            }
            if (count($sdid_s17) > 0) {
                sales_status_log_assembly($id, 17, $sdid_s17);
            }
            break;
        case 2:

            break;
    }

    $storage_assembly = \Storage\Models\StorageAssembly::find($id);
    $storage_assembly->status = 2;
    $storage_assembly->uid_end = $uid;
    $storage_assembly->save();

    $salesQuery = $db->query("SELECT uid, numb FROM `" . DB_PREFIX . "_sales` WHERE id = '" . $sa["sales_id"] . "' LIMIT 1");
    $saq = $db->getRow($salesQuery);
    $attr = [];
    $attr['uid'] = $saq["uid"];
    $attr['title'] = 'Заказ собран';
    $attr['text'] = 'Заказ №' . $saq["numb"] . ' собран ';
    $attr['icon'] = config('app.url') . '/files/push/task.png';
    $attr['url'] = '/administration/module/sales/order/' . $sa["sales_id"];
    $attr['module'] = "sales";
    $attr['pid'] = $sa["sales_id"];
    sendAllNotif($attr);
}

function sales_status_log_assembly($url_id, $status, $sdid)
{
    foreach ($sdid as $id) {
        $sdid = \Sales\Model\SalesDetail::find($id);
        if (!empty($sdid)) {
            $sdid->update(["status" => $status]);
            \Sales\Model\SalesDetailStatusLog::create([
                "sales_detail_id" => $id,
                "status" => $status,
                "url" => "/administration/module/storage/assembly/detail/" . $url_id,
            ]);
        }
    }
}

function assembly_change_count($id, $count)
{
    global $db;
    if ($id) {
        $db->query("UPDATE " . DB_PREFIX . "_storage_assembly_detail SET count = '" . $count . "' WHERE id = '" . $id . "' LIMIT 1");
    }
}

function assembly_create_excel($id)
{
    global $db, $config, $core, $tags, $url, $headTag, $cache, $lang, $dn_array;
    require_once(ROOT . 'usr/plugins/PHPExcel.php');
    require_once(ROOT . 'usr/plugins/PHPExcel/IOFactory.php');
    $objReader = PHPExcel_IOFactory::createReader('Excel2007');

    $objPHPExcel = $objReader->load(ROOT . 'files/tpl/assembly/list.xlsx');

    $query = $db->query("SELECT * FROM JMY_storage_assembly WHERE id = '" . $id . "' LIMIT 1");
    $r = $db->getRow($query);

    $objPHPExcel->getActiveSheet()->setCellValue('C2', $r["sales_id"]);
    $objPHPExcel->getActiveSheet()->setCellValue('C3', $r["id"]);
    $objPHPExcel->getActiveSheet()->setCellValue('C4', 1);
    $objPHPExcel->getActiveSheet()->setCellValue('C5', date("d.m.Y H:i:s", strtotime($r["date"])));

    $query = $db->query("SELECT sad.count, c.*, sd.* FROM " . DB_PREFIX . "_storage_assembly_detail as sad LEFT JOIN " . DB_PREFIX . "_sales_detail as sd ON(sd.id = sad.sdid) LEFT JOIN " . DB_PREFIX . "_catalog as c on(sd.cid=c.id) WHERE sad.said = '" . $id . "'");
    if ($db->numRows($query) > 0) {
        $baseRow = 8;
        $row_count = 0;
        while ($content = $db->getRow($query)) {
            $row_count++;
            $row = $baseRow + $row_count;
            $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $row_count)
                ->setCellValue('B' . $row, $content['id'])
                ->setCellValue('C' . $row, $content['s_sku'])
                ->setCellValue('D' . $row, html_entity_decode($content['s_name'])) //title
                ->setCellValue('E' . $row, $content['count']);
        }
    }





    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $dir = 'files/assembly';

    $file_url = $dir . '/' . time() . '.xlsx';

    $objWriter->save($file_url);
    header('Location: https://atri-energo.ru/' . $file_url);
    //unlink($file_url);
}

function update_field_assembly($id, $field, $value)
{
    global $db;
    if ($id && $field) {
        $db->query("UPDATE `" . DB_PREFIX . "_storage_assembly_detail` SET " . $field . " = '" . $value . "' WHERE id = '" . $id . "' LIMIT 1");
    }
}

function update_field_main_assembly($id, $field, $value)
{
    global $db;
    if ($id && $field) {
        $db->query("UPDATE `" . DB_PREFIX . "_storage_assembly` SET " . $field . " = '" . $value . "' WHERE id = '" . $id . "' LIMIT 1");
    }
}

function create_transfer_assembly($data)
{
    global $db;
    $order = getQueryMain($data["id"]);
    $query = getQueryDetail($data["id"]);
    $transfer_id = "";
    if ($db->numRows($query) > 0) {
        $sales_detail_id = [];
        $count = [];
        while ($r = $db->getRow($query)) {
            $sales_detail_id[] = $r["id"];
            $count[$r["id"]] = $r["sadcount"];
        }
        $transfer = \Module::sales_to_transfer($order["id"], [
            "sales_detail_id" => $sales_detail_id,
            "org" => $order["org"],
            "object" => $order["object"],
            "stock_in" => $data["stock_in"],
            "stock_out" => $order["stock_id"],
            "uid_gave" => $order["sa_uid_end"],
            "uid_accepted" => $order["sauid"],
            "count" => $count,
        ]);
        $transfer_id = $transfer["stock_transfer"]->id;
    }
    return $transfer_id;
}
