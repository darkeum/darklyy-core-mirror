<?php

function query_main_result($id){
    global $db;
    $query = $db->query("SELECT m.*, obj.name as obj_name, cu.name as cu_name, cu.sname as cu_sname
    FROM `".DB_PREFIX."_merging` as m
    LEFT JOIN `".DB_PREFIX."_object` as obj ON(obj.id = m.object)
    LEFT JOIN `".DB_PREFIX."_catalog_unit` as cu ON(cu.unit = m.unit)
    WHERE m.id = '".$id."' 
    LIMIT 1");
    $r = $db->getRow($query);
    return $r;
}

function detail($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $order = query_main_result($id);
    $title = detail_header_title($id);
    $adminTpl->admin_head($title);
    
    ?>

    <input type="hidden" id="mainId" value="<?php echo $id; ?>">
    <input type="hidden" id="org_data" value='<?php echo json_encode(get_org_data(), JSON_UNESCAPED_UNICODE);?>'>
    <input type="hidden" id="type_data" value='<?php echo json_encode(get_type(), JSON_UNESCAPED_UNICODE);?>'>

    <style>
        .cp{
            cursor:pointer;
        }
    </style>

    <section id="content" class="">
        <h3 class="text-center" id="con_header_title">
            <?php
                echo $title;
            ?>
        </h3>
        <div class="row" id="admin-form">
            <div class="panel mb25">
                <div class="panel-body p20 pb10">
                    <div class="section row mbn admin-form">
                        <div class="col-md-12 pl15">
                            <div class="section row mb15" id="con_header">
                                <?php
                                    detail_header($id);
                                ?>
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
					<div class="widget-menu pull-right" id="detail_body_btn">
                        <?php
                            echo detail_body_btn($id);
                        ?>						
					</div>	
                    
              </div>

              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" method="POST" onsubmit="return false;">
                  <table class="table table-striped">
                    <thead id="table_thead">
                        <tr>
		                	<th class="w40"><span class="pd-l-sm"></span>№</th>
		                	<th class="w40"><?php echo get_star();?></th>
		                	<th class="w200">Номенклатура</th>
		                	<th class="w150 sku-units" data-type="1"><span>Артикул</span></th>
                            <th class="w100 text-center" data-toggle="tooltip" title="Наличие / Доступно / Резерв">Н/Д/Р</th>	
		                	<th class="w100 text-center" data-toggle="tooltip" title="Количество">Кол-во</th>
		                	<th class="w100 text-center" data-toggle="tooltip" title="Ед. изм.">Ед. изм.</th>
		                	<th class="w100" data-toggle="tooltip" title="Цена">Цена</th>			
		                	<th class="w100" data-toggle="tooltip" title="Сумма">Сумма</th>			
		                	<th class="w100 text-center">Действия</th>
		                	<th class="w20 text-right">
		                		<div class="checkbox-custom mb15">
		                			<input id="all" type="checkbox" name="all" onclick="setCheckboxes('tablesForm', true); return true;" class="checks">
		                			<label for="all"></label>
		                		</div>	
		                	</th>
		                </tr>
                    </thead>
                    <tbody id="tov_list">	

                        <?php detail_body($id); ?>
                            
					</tbody>
				<tfoot class="footer-menu">
                    <tr align="right">                    
					  <td align="right" colspan="15">
                        <nav align="right" class="text-right">						
							<select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act">							
							
							</select>
							<input name="submit" type="button" class="btn btn-success" style="display: inline-block;" id="sub" value="Выполнить" disabled><span class="pd-l-sm"></span>	
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

    <div class="modal fade admin-form" id="objectChangeModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form onsubmit="return false;" id="objectChangeModalForm">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Объект</h5>
                </div>
                <div class="modal-body">
                  <?php
                      oSelect("jsAdminTpl", "object", $order["object"], "getObjectToSelect");
                      oSelect("select", "object", $order["object"], "getObjectToSelect");
                  ?>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="objectModal.change();">Изменить</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </form>
        </div>
      </div>
    </div>

    <?php

    require_once(ROOT.'usr/modules/storage/admin/sub/transferModal.php');

    transferModal(["id" => $id]);

    xeditableInit();

    $adminTpl->footIncludes[] = '<script src="/usr/other/sub/price/js/price.js?'.time().'"></script>';
    $adminTpl->footIncludes[] = '<script src="/usr/modules/merging/admin/asset/js/merging_detail.js?'.time().'"></script>';

    require_once(ROOT."usr/modules/sales/admin/sub/ndr.php");
    ndr_init();

    $attr = [
        "module" => 'merging_detail', 
        "id" => $id, 
        "update_func" => "updateBody", 
        "status" => 1,
        "hotkeys" => detail_body_btn($id),
        "copy_disabled" => $order["active"] == 0 ? "false" : "true",
    ];
    
    
    fastBar($attr);

    $adminTpl->admin_foot();
}

function detail_body_btn($id){
    $order = query_main_result($id);
    $victoria = new \Victoria;
	$victoria->link = "/administration/module/merging/victoria_add/{$id}";
    $bb = $victoria->button();
    //callAddProduct($id, "https://atri-energo.ru/administration/module/merging/addProduct/", true)
    $btn = $order["active"] == 0 ? $bb : "";
return <<<HTML

    <div class="btn-group">
        {$btn}
        <button onclick="updateBody(); notif('primary', 'Информация', 'Данные успешно обновлены!');" type="button" class="btn btn-xs btn-system">
            <span class="mr10 icon fa fa-refresh"></span>Обновить
        </button>
    </div>

HTML;
}

function detail_header_title($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $order = query_main_result($id);
    return 'Заявка №'.$order["numb"];
}

function detail_header($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $order = query_main_result($id);

    ?>

    <input type="hidden" id="stock_data" value='<?php echo json_encode(get_stock_data(["org" => $order["org"]]), JSON_UNESCAPED_UNICODE);?>'>

        <div class="col-md-3">
            <div class="element-groups-lg-custom wizard-options" id="ajaxLoadBtn">
                <?php
                    if($order["active"] == "0"){
                        ?>
                            <div class="ph10">
		                    	<a href="javascript:void(0);" 
                                onclick="<?php echo $order['type'] == 1 ? "end_quest(".$order['type'].");" : "start_quest(".$order['type'].");";?>" 
                                class="holder-style p15 text-center holder-active">
                                    <span class="far fa-check-circle holder-icon"></span>
                                    <br>Завершить<br>заявку
                                </a>
		                    </div>
                            
                            <div class="ph10">
                                <?php
                                    $victoria_min = new \Victoria;
                                    $victoria_min->link = "/administration/module/merging/victoria_add2/{$id}";
                                    $vm = $victoria_min->open_func();
                                    //clickCallAddProduct($id, "https://atri-energo.ru/administration/module/merging/addProduct2/");
                                ?>
		        	            <a href="javascript:void(0);"  onclick="<?php echo $vm; ?>" class="holder-style p15 text-center holder-active"><span class="fas fa-shopping-basket holder-icon"></span><br>Выбрать<br>товар</a>
		                    </div>
                        <?php
                    } else {
                        ?>
                            <div class="ph10">
		                    	<a href="javascript:void(0);"  
                                onclick="<?php echo $order['type'] == 1 ? "start_quest(".$order['type'].");" : "end_quest(".$order['type'].");";?>" 
                                class="holder-style p15 text-center holder-active">
                                    <span class="fas fa-ban holder-icon"></span>
                                    <br>Отменить<br>завершение
                                </a>
		                    </div>
                            <div class="ph10">
		                    	<a href="/administration/module/merging/print/<?php echo $id;?>" class="holder-style p15 text-center holder-active">
                                    <span class="glyphicon glyphicon-print holder-icon"></span>
                                    <br>Печать<br>&emsp;
                                </a>
		                    </div>
                            <div class="ph10">
		                    	<a href="javascript:void(0);" class="holder-style p15 text-center holder-active" onclick="transferModal.open();">
                                    <span class="fa fa-exchange holder-icon"></span>
                                    <br>Создать<br>перемещение
                                </a>
		                    </div>
                            <?php
                                if($order["type"] == 1){
                            ?>
                                <div class="ph10">
		                        	<a href="javascript:void(0);" class="holder-style p15 text-center holder-active" onclick="createMergingAdditional()">
                                        <span class="fas fa-plus holder-icon"></span>
                                        <br>до-<br>укомплектовка 
                                    </a>
		                        </div>
                            <?php
                                }
                            ?>
                        <?php
                    }
                ?>
                
            </div>
        </div>
        <div class="col-md-2">
            <small title="Наличие / Доступно / Резерв">Н/Д/Р:</small><br/>
            <b class="cp" data-cid="<?php echo $order["cid"];?>" onclick="view_ndr_modal(<?php echo $order['cid'];?>);">
                <?php 
                    $ndr_view = get_ndr(["cid" => $order["cid"], "stock_id" => $order["stock_id"], "module" => "sales", "pid" => "", "returnType" => "view"]);
                    echo $ndr_view;
                ?>
            </b><br/><br/>
            <small>Количество:</small><br/>
            <b><?php echo empty($order["cid"]) ? '<span class="text-danger">Не указано</span>' : '<span id="count" class="cp" '.($order["active"] == 0 ? '' : 'data-disabled="true"').'>'.$order["count"].'</span>';?></b><br/><br/>
            <small>Товар:</small><br/>
            <b><?php echo empty($order["name"]) ? '<span class="text-danger">Не указан</span>' : '<span title="cid: '.$order["cid"].'">'.$order["name"].'</span>';?></b>
            
        </div>
        <div class="col-md-2">
            <small>Тип:</small><br/>
            <b id="type" data-value="<?php echo $order["type"];?>" class="cp" <?php echo $order["active"] == 0 ? '' : 'data-disabled="true"';?>><?php echo get_type($order["type"]);?></b><br/><br/>
            <small>Склад:</small><br/>
            <b class="cp" id="stock_id" data-value="<?php echo $order["stock_id"];?>" <?php echo $order["active"] == 0 ? '' : 'data-disabled="true"';?>><?php echo get_stock_name($order["stock_id"]);?></b><br/><br/>
            <small>Организация:</small><br/>
            <b id="org" class="cp" data-value="<?php echo $order["org"];?>" <?php echo $order["active"] == 0 ? '' : 'data-disabled="true"';?>>
                <?php 
                    $org = getOrgInfo($order["org"]);
                    echo $org["organizations"]["name"];
                ?>
            </b>
        </div>
        <div class="col-md-2">
            <small>Ответственный:</small><br/>
            <b><?php echo shortUserName($order["uid"]);?></b><br/><br/>
            <small>Дата:</small><br/>
            <b><span id="date"><?php echo date("d.m.Y", strtotime($order["date"]));?></span></b><br/><br/>
            <small>Объект:</small><br/>
            <b class="cp editable-click" onclick="<?php echo $order["active"] == 0 ? 'objectModal.open();' : '';?>"><?php echo empty($order["object"]) ? "Не указан" : $order["obj_name"];?></b>
        </div>
        <div class="col-md-2">
            <small>Список перемещений:</small><br/>
            <?php
                $transfer_list = json_decode($order["transfer_list"], true);
                $transfer_list = is_array($transfer_list) ? $transfer_list : [];
                if(count($transfer_list) > 0){
                    $transfer_items = \Storage\Model\StockTransfer::findOrFail($transfer_list);
                    foreach($transfer_items as $transfer_item){
                        echo "<div><a target='_blank' href='/administration/module/storage/transfer/detail/{$transfer_item->id}'>Перемещение №{$transfer_item->numb}</a></div>";
                    }
                } else {
                    echo "Отсутствуют";
                }
            ?>
            <br/><br/>
            <small>Список докомплектаций:</small><br/>
            <?php
                $items = \Merging\Model\MergingAdditional::where("merging_id", $id)->get();
                if($items->count() > 0){
                    foreach($items as $item){
                        echo "<div><a href='/administration/module/merging/detail_additional/{$item->id}' target='_blank'>Докомлпектация №{$item->id}</a></div>";
                    }
                } else {
                    echo "Отсутствуют";
                }
            ?>
            <br/><br/>
        </div>
    <?php
}

function query_detail($id){
    global $db;
    $query = $db->query("SELECT md.*, cu.name as cu_name, cu.sname as cu_sname
    FROM `".DB_PREFIX."_merging_detail` as md 
    LEFT JOIN `".DB_PREFIX."_catalog_unit` as cu ON(cu.unit = md.unit)
    WHERE md.merging_id = '".$id."'");
    return $query;
}

function detail_body($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $query = query_detail($id);
    if($db->numRows($query) > 0){
        // $qq = $db->query("SELECT * FROM `".DB_PREFIX."_merging` as m WHERE id = '".$id."' LIMIT 1");
        // $m = $db->getRow($qq);
        $m = query_main_result($id);
        $i = 0;
        $total = 0;
        while($merging = $db->getRow($query)){
            $i++;
            $merging['i'] = $i;
            $merging['m_active'] = $m["active"];
            $merging['m_stock_id'] = $m["stock_id"];
            detail_body_template($merging);
            $total += $merging["price"] * $merging["count"];
        }
        ?>

            <tr>
                <td colspan="6" class=" text-right"><b>Итого:</b></td>
                <td colspan="4" class=" text-left"><b class="allSum"><?php echo number_format($total,2,'.',' ');?> руб.</b></td>
			</tr>

        <?php
    } else {
        ?>

        <tr>
            <td colspan="7">Товары отсутствуют</td>
        </tr>

        <?php
    }
}

function get_star($star = 0, $id = null){
    $class_text = "text-primary";
    return '<i class="fas fa-star-of-life cp '.($star == 0 ? "" : $class_text).' '.(empty($id) ? "" : "star_change").'" data-star="'.$star.'" data-id="'.$id.'" data-class_text="'.$class_text.'" title="'.($id == null ? "Звёздочками производство отмечает какие приборы положили отдельно в коробки для последующей проверки на складе" : "").'"></i>';
}

function detail_body_template($merging){
    $ndr_view = get_ndr(["cid" => $merging["cid"], "stock_id" => $merging["m_stock_id"], "module" => "sales", "pid" => "", "returnType" => "view"]);
    $star = get_star($merging["star"], $merging["id"]);
    $total = $merging["price"] * $merging["count"];
    ?>
        <tr id="tr_<?php echo $merging["id"];?>" data-cid="<?php echo $merging["cid"];?>" data-id="<?php echo $merging["id"];?>" class="tr_item">
            <td title="id: <?php echo $merging["id"];?>; cid: <?php echo $merging["cid"];?>"><?php echo $merging["i"];?></td>
            <td><?php echo $star;?></td>
            <td><?php echo $merging["name"];?></td>
            <td><?php echo $merging["sku"];?></td>
            <td class="text-center cp" data-cid="<?php echo $merging["cid"];?>" onclick="view_ndr_modal(<?php echo $merging['cid'];?>);"><?php echo $ndr_view;?></td>
            <td class="text-center"><span class="cp count" data-pk="<?php echo $merging['id'];?>" <?php echo $merging["m_active"] == 0 ? '' : 'data-disabled="true"';?>><?php echo $merging["count"];?></span></td>
            <td class="text-center"><?php echo $merging['cu_name']?></td>
            <td class=""><span class="price cp" data-pk="<?php echo $merging['id'];?>" data-value="<?php echo $merging["price"]; ?>"></span></td>
            <td class=""><span class="total" data-pk="<?php echo $merging['id'];?>" data-value="<?php echo $total; ?>"></span></td>
            <td class="text-center"><button class="btn btn-xs btn-danger" title="Удалить" onclick="<?php echo $merging["m_active"] == 0 ? "remove_item_modal(".$merging['id'].");" : "";?>" <?php echo $merging["m_active"] == 0 ? "" : "disabled";?>><i class="fas fa-times"></i></button></td>
            <td class="text-right">
                <div class="checkbox-custom mb15">
					<input id="checkbox<?php echo $merging["id"];?>" type="checkbox" name="checks[]" value="<?php echo $merging["id"];?>" class="checks">
					<label for="checkbox<?php echo $merging["id"];?>"></label>
				</div>
            </td>
        </tr>
    <?php
}

function update_filed($id, $field, $value){
    global $db;
    if($id && $field && $value){
        if((empty($value) && $value != 0) || is_null($value)){
            $new_value = "'null'";
        } else {
            $new_value = "'".$value."'";
        }
        if($field == "org"){
            // $merging = query_main_result($id);
            change_numb($id, $value);
            $stock = get_stock_data(["org" => $value]);
            $stock_id = 0;
            if(count($stock) > 0){
                $stock_id = $stock[0]["value"];
            }
            update_filed($id, "stock_id", $stock_id);
        }
        $db->query("UPDATE `".DB_PREFIX."_merging` SET `".$field."` = ".$new_value." WHERE id = '".$id."' LIMIT 1");
    }
}

function update_filed_detail($id, $field, $value){
    if($id && $field){
        $MergingDetail = \Merging\Model\MergingDetail::find($id);
        $MergingDetail->{$field} = $value;
        $MergingDetail->save();
    }
}

function remove_item($id){
    \Merging\Model\MergingDetail::destroy($id);
}

function check_minus_count_merging($id){
    global $db;
    $merging = query_main_result($id);
    $query = query_detail($id);
    if($db->numRows($query)){
        $data = [];
        while($r = $db->getRow($query)){
            $data[$r["id"]] = [
                "pid" => $r["id"],
                "module" => "merging",
                "count" => $r["count"],
                "cid" => $r["cid"],
                "stock_id" => $merging["stock_id"],
            ];
        }
        // dd($data);
        // echo check_minus_count($data);
        echo check_minus_count_custom(["data" => $data, "module" => "merging", "pid" => $id]);
    }
}

function check_minus_count_merging2($id){
    global $db;
    $merging = query_main_result($id);
    $data = [];
    $data[$merging["id"]] = [
        "pid" => $merging["id"],
        "module" => "merging",
        "count" => $merging["count"],
        "cid" => $merging["cid"],
        "stock_id" => $merging["stock_id"],
    ];
    echo check_minus_count_custom(["data" => $data, "module" => "merging", "pid" => $id]);
}

function end_quest($id){
    global $db;
    $merging = query_main_result($id);
    $type = $merging["type"];
    $query = query_detail($id);
    $source = [];
	$source['module'] = 'merging';
	$source['pid'] = $id;
	$source['comment'] = 'Проведение комплектации №'.$merging["numb"];
    if($db->numRows($query) > 0){
        updateQuantity($merging["cid"], $merging["stock_id"], $merging["count"], "plus", $source);
        while($merging_d = $db->getRow($query)){
            updateQuantity($merging_d["cid"], $merging["stock_id"], $merging_d["count"], "minus", $source);
        }
        $active = $type == 1 ? 1 : 0;
        $db->query("UPDATE ".DB_PREFIX."_merging SET active = '".$active."' WHERE id = '".$id."' LIMIT 1");
    }
}

function start_quest($id){
    global $db;
    $merging = query_main_result($id);
    $type = $merging["type"];
    $query = query_detail($id);
    $source = [];
	$source['module'] = 'merging';
	$source['pid'] = $id;
	$source['comment'] = 'Отмена проведения комплектации №'.$merging["numb"];
    if($db->numRows($query) > 0){
        updateQuantity($merging["cid"], $merging["stock_id"], $merging["count"], "minus", $source);
        while($merging_d = $db->getRow($query)){
            updateQuantity($merging_d["cid"], $merging["stock_id"], $merging_d["count"], "plus", $source);
        }
        $active = $type == 1 ? 0 : 1;
        $db->query("UPDATE ".DB_PREFIX."_merging SET active = '".$active."' WHERE id = '".$id."' LIMIT 1");
    }
}

function check_cid_have($id){
    global $db;
    $merging = query_main_result($id);
    if(empty($merging["cid"])){
        return 'Не указан основной товар!';
    }
    $query = query_detail($id);
    if($db->numRows($query) == 0){
        return 'Не указан дополнительные товары!';
    }
}

function change_numb($id, $org){
    $merging = \Merging\Model\Merging::findOrFail($id);
    $numb = \Boot\Support\Module::numb('merging', $org, $merging->date);
    $merging->numb = $numb["numb"];
    $merging->numb_counter = $numb["numb_counter"];
    $merging->save();
}

function create_transfer_merging($request){
    global $db;
    require_once(ROOT."usr/modules/storage/admin/sub/transfer_create.php");
    $id = $request["id"];
    $order = query_main_result($id);
    $query = query_detail($id);
    $transfer_id = "";
    if($db->numRows($query) > 0){
        $transfer_id = transfer_create([
			"org" => $order["org"],
			"object" => $order["object"],
			"comment" => '',
			"stock_in" => $request["stock_in"],
			"stock_out" => 1,
		]);
        while($r = $db->getRow($query)){
            $price = \Price::getPrice($r["cid"]);
            $db->query("INSERT INTO `" . DB_PREFIX . "_stock_transfer_detail` (`transfer_id`, `cid`, `s_name`, `s_sku`, `s_unit`, `s_price`, `count`, `status`) VALUES ('" . $transfer_id . "', '" . $r["cid"] . "', '" . $r["name"] . "', '" . $r["sku"] . "', '" . $r["unit"] . "', '" . $price->price_purchase_ . "', '" . $r["count"] . "', '1')");
        }
    }
    return $transfer_id;
}