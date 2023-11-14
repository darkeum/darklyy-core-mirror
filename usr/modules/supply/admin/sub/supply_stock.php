<?php

function supply_stock_main()
{
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $lnn = $lang['supply_request'];
    $adminTpl->admin_head($lang['supply_main'] . ' | ' . $lnn);
    $atributs = array();
    $atributs['opts'] = 'open';
    $atributs['module'] = 'module';
    $atributs['module_name'] = 'supply';
    $atributs['module_open'] = 'supply_stock_detail';
    //$atributs['js'] = 'setTimeout(function () {$("#jqSupplyMain").jqGrid(\'setGridParam\',{postData:{status: \'1\'}}); $(\'#jqSupplyMain\').trigger(\'reloadGrid\');}, 150);';	
    load_grid('jqSupplyStock', $lnn, $atributs, ROOT . '/usr/modules/supply/admin/grid/');
    $adminTpl->admin_foot();
}

function query_main($id)
{
    global $db;
    $query = $db->query("SELECT ss.* FROM " . DB_PREFIX . "_supply_stock as ss WHERE ss.id = '" . $id . "' LIMIT 1");
    return $query;
}

function supply_stock_detail($id)
{
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $adminTpl->admin_head('Заявка на закупку | Заявка на заказ №' . $id);
    if ($id) {
        $query = query_main($id);
        if ($db->numRows($query) > 0) {
            $content = $db->getRow($query);
            ?>

        <input type="hidden" value='<?php echo $id; ?>' id="mainId">
            <section id="content" class="animated fadeIn">
                <div class="text-center">
                    <p class="lead">ЗАЯВКА НА ЗАКАЗ №<?php echo $id; ?></p>
                </div>
                <div class="row" id="admin-form">
                    <div class="panel mb25 mt5">
                        <div class="panel-body p20 pb10">
                            <div class="section row mbn admin-form">
                                <div class="col-md-12 pl15">
                                    <div class="section row mb15">
                                        <div class="col-md-3">

                                            <div class="element-groups-lg-custom wizard-options" id="ajaxLoadBtn">
                                                <div class="ph10">
                                                    <a href="javascript:void(0);" class="holder-style p15 text-center" id="awtw"><span class="fa fa-handshake holder-icon"></span><br> Пересчитать <br> товары</a>
                                                </div>
                                                <div class="ph10">
                                                    <a href="javascript:void(0);" onclick="" class="holder-style p15 text-center holder-active"><span class="glyphicon glyphicon-print holder-icon"></span><br> Распечатать <br>заявку</a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="awtwModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Согласовать со складом?</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        Вы действительно хотите отправить на согласование?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary" data-dismiss="modal" id="awtwSaveBtn" data-id="'.$id.'">Отправить</button>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- <div class="col-md-2">
										<small>Контрагент:</small><br/>
										<b>'.$order['oname'].'</b>&nbsp;&nbsp;				
										<a target="_blank" href="'.ADMIN.'/module/organizations/edit/'.$order['oid'].'"><span class="fa fa-external-link"></span></a>
										<br/><br/>
										<small>Постановщик:</small><br/>
										<b>'. shortUserName($order['uid']).'</b><br/><br/>
									</div>

									<div class="col-md-2">
										<small>Дата подачи заявки:</small><br/>
										<b>'. date("d.m.Y H:i:s", strtotime($order['date'])) .'</b><br/><br/>
										<small>Статус:</small><br/>
										<b>'.  $rows_status[$order['status']] .'</b>
									</div>

									<div class="col-md-2">
										<small>Заказ:</small><br/>
										<b><a href="/administration/module/sales/order/'.$order['slid'].'">№'.  $order['numb']  .'</a></b><br/><br/>
										<small>Объект:</small><br/>
										<b><a target="_blank" href="/administration/object/view/'.$order["obj_id"].'">'.  $order['obj_name'].'</a></b><br/><br/>
									</div>

									<div class="col-md-2">

									<small>Комментарий:</small><br/>
									<b><a href="javascript:void(0);" onclick="modal_o(\'#modal-comm\'); ajaxGet(\''.ADMIN.'/module/sales/ajax/comments_get/'.$order['slid'].'\', \'sale_comments_list\');">Посмотреть комментарии</a></b> <span class="badge badge-danger">'.$countComm.'</span><br/><br/>					
												
										<div id="modal-comm" class="popup-basic bg-none mfp-with-anim mfp-hide">
										<form id="sale_comments" action="javascript:void(null);" onsubmit="ajax_comments_send(\'sale_comments\', \''.ADMIN.'/module/salescommSave/'.$order['slid'].'\');">
										<div class="panel">
										<div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Комментарии<span></div>
										<div class="panel-body">
										<h3 class="mt5">Комментарии:</h3>
										<div id="sale_comments_list" style="max-height: 300px; overflow-y: scroll;">';
										
										
										
										echo '</div><hr class="short alt">
										<textarea class="form-control" rows="5" name="comm" id="comm" placeholder="Комментарий..." required></textarea>
										
										</div>
										<div class="panel-footer">
										<input type="submit" value="Добавить комментарий" class="btn btn-success">
										</div>
										</div>
										</form>
										</div>

									</div> -->

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="panel panel-dark panel-border top">
                        <div class="panel-heading">
                            <span class="panel-title">Список товаров:</span>
                            <div class="widget-menu pull-right">
                                <div class="btn-group">
                                    <button onclick="updateBody();" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>
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
                                            <th class="w50">Производитель</th>
                                            <th class="w100">Кол-во</th>
                                            <th class="w100">Кол-во отправленного</th>
                                            <th class="w50">Ед. изм.</th>
                                            <th class="w50" title="Наличие / Доступно / Резерв">Н/Д/Р</th>
                                            <th class="w40 text-center">Статус</th>
                                            <th class="w200 text-right">Действия</th>
                                            <th class="w20 text-right">
                                                <div class="checkbox-custom mb15">
                                                    <input id="all" type="checkbox" name="all" onclick="setCheckboxes('tablesForm', true); check_checks(); return true;">
                                                    <label for="all"></label>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="dev_list">
                                        <?php
                                            supply_stock_detail_list($id);
                                        ?>
                                    </tbody>
                                    <tfoot class="footer-menu">
                                        <tr align="right">
                                            <td align="right" colspan="12">
                                                <nav align="right" class="text-right">
                                                    <select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act">
                                                        <option value="mas_supply_order">Массовый заказ</option>
                                                    </select>
                                                    <input name="submit" id="sub" type="button" class="btn btn-success" style="display: inline-block;" onclick="select_help();" value="Выполнить" disabled><span class="pd-l-sm"></span>
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

            <div class="modal fade admin-form" id="modal-compl" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <form id="compl" action="javascript:void(null);">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Отправить в счет</h5>
                            </div>

                            <div class="panel-body p25">
                                <div class="section">
                                    Номер закупки: <br>
                                    <div class="smart-widget sm-right smr-50">
                                        <label class="field select prepend-icon">
                                            <select class="select2-single2 form-control" name="order_numb" id="order_numb"></select>
                                            <i class="arrow double"></i>
                                            <label for="order_numb" class="field-icon"><i class="fas fa-shopping-cart"></i></label>
                                        </label>
                                        <button onclick="showhide('addition')" type="button" class="button"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="section" id="addition" style="display: none">
                                    Выберите контрагента: <br>
                                    <label class="field select prepend-icon">
                                        <select class="select2-single1 gui-input" name="new_order" id="new_order"></select>
                                        <label for="new_order" class="field-icon"><i class="fas fa-users"></i></label>
                                    </label>
                                </div>
                                <div class="section">
                                    Комментарий: <br>
                                    <label for="text" class="field prepend-icon">
                                        <textarea id="comment" name="comment" class="gui-textarea" placeholder="Комментарий" /></textarea>
                                        <label for="text" class="field-icon"><i class="fa fa-comments"></i></label>
                                    </label>
                                </div>
                                <div class="section" id="div_count">
                                    Количество товаров: <br>
                                    <label for="count" class="field prepend-icon">
                                        <input id="countModal" type="text" name="count" value="  " placeholder="Количество" class="gui-input">
                                        <label for="countModal" class="field-icon"><i class="fas fa-calculator"></i></label>
                                    </label>
                                </div>
                            </div>

                            <div class="panel-footer">
                                <input type="hidden" name="id" value="" id="id_modal">
                                <input type="button" class="button btn-success" value="Отправить" id="modalComplBtn" onclick="order_send_ajax();" data-dismiss="modal">
                                <input type="button" class="button btn-success" value="Отправить" id="modalComplBtn2" onclick="order_send_mas_ajax();" data-dismiss="modal">
                            </div>

                        </div>
                    </div>
                </form>
            </div>


            <div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Удалить товар</h5>
                        </div>
                        <div class="modal-body">
                            Вы действительно хотите удалить товар?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" id="delModalBtn" onclick="delTov(this);">Удалить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>

    <?php
            }
        }
        require_once(ROOT."usr/modules/sales/admin/sub/ndr.php");
        ndr_init();

        $adminTpl->footIncludes[] = '<script src="usr/modules/supply/admin/asset/js/supply_stock_detail.js"></script>';
        $adminTpl->admin_foot();
    }

    function query_detail($id)
    {
        global $db;
        $query = $db->query("SELECT ssd.*, c.title, c.sku, c.unit, cm.name as cm_name FROM " . DB_PREFIX . "_supply_stock_detail as ssd LEFT JOIN ".DB_PREFIX."_catalog as c ON(c.id = ssd.cid) LEFT JOIN ".DB_PREFIX."_catalog_manufacture as cm ON(cm.id = c.man) WHERE ssd.ssid = '" . $id . "'");
        return $query;
    }

    function supply_stock_detail_list($id)
    {
        global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;
        $query  = query_detail($id);

        if ($db->numRows($query) > 0) {
            $i = 0;
            while ($content = $db->getRow($query)) {
                $i++;
                $flag = false;

                switch ($content['status']) {
                    default:
                    case 1:
                        $status_icon = '<span class="far fa-circle text-danger fa-md"></span>';
                        break;
                    case 2:
                        $status_icon = '<span class="far fa-circle text-success fa-md" data-toggle="tooltip" title=""></span>';
                        break;
                }

                $ndr_view = get_ndr(["cid" => $content["cid"], "stock_id" => "1", "module" => "sales", "pid" => null, "returnType" => "view"]);

                ?>

			<tr>
				<td data-toggle="tooltip" title="<?php echo $content['id']; ?>"><span class="pd-l-sm"></span><?php echo $i; ?></td>
				<td><?php echo $content['sku']; ?></td>
				<td><?php echo $content['title']; ?></td>
				<td><?php echo $content['cm_name']; ?></td>
				<td class="count"><?php echo $content['count']; ?></td>	
				<td><?php echo $content['count_send']; ?></td>						
				<td><?php echo get_unit('data', $content['unit'], 'sname'); ?></td>		
				<td onclick="view_ndr_modal(<?php echo $content['cid']; ?>)" class="ndr"><?php echo $ndr_view ?></td>						
				<td class="text-center"><?php echo $status_icon; ?></td>
				<td class="w200 text-right">				
					<div class="btn-group" >
                        <button type="button" class="btn btn-xs btn-primary makeOrder" onclick="makeOrder(this);" data-id="<?php echo $content['id']; ?>">Сделать заказ</button>
					</div>
                </td>
				<td class="w20 text-right">
					<div class="checkbox-custom mb15">
						<input id="checkbox<?php echo $content['id']; ?>" type="checkbox" name="checks[]" value="<?php echo $content['id']; ?>" class="checks">
						<label for="checkbox<?php echo $content['id']; ?>"></label>
					</div>
				</td>
            </tr>
            <?php
            }
        } else {
            ?> 

                <tr><td colspan="12">Товары отсутствуют!</td></tr>

            <?php
        }
    }
