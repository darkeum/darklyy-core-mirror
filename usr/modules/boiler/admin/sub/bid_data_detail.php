<?php

// require_once("usr/modules/boiler/admin/sub/bid_data_detail.php");

function query_bid_data($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $query = $db->query("SELECT bbd.* FROM ".DB_PREFIX."_boiler_bid_data as bbd WHERE bbd.id = '".$id."' LIMIT 1");
    $order = $db->getRow($query);
    return $order;
}

function query_bid_data_detail_result($id){//query_bid_data_deyail
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $query = $db->query("SELECT bb.*, o.name as o_name, bbd.uid as bbd_uid, bbd.category, obj.name as obj_name, bbt.section, bbt.type, bbd.status as bbd_status, bbd.comment as bbd_comment, org.nds as org_nds
    FROM `".DB_PREFIX."_boiler_bid_data` as bbd
    LEFT JOIN `".DB_PREFIX."_boiler_bid_type` as bbt ON(bbt.id = bbd.bid_type_id) 
    LEFT JOIN `".DB_PREFIX."_boiler_bid` as bb ON(bb.id = bbt.bid_id)
    LEFT JOIN `".DB_PREFIX."_organizations` as o ON (o.id = bb.oid)
    LEFT JOIN `".DB_PREFIX."_object` as obj ON (obj.id = bb.object)
    LEFT JOIN `".DB_PREFIX."_org` as org ON (org.id = bb.org)
    WHERE bbd.id = '".$id."' LIMIT 1");
    $order = $db->getRow($query);
    return $order;
}

function bid_data_detail($id)
{
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;

    

    //$order = query_bid_data($id);
    $order = query_bid_data_detail_result($id);
    
    $all_category = $core->aCatList('catalog_v2');

    $adminTpl->admin_head("0_o");

    ?>

    <input type="hidden" id="mainId" value="<?php echo $id; ?>">
    <input type="hidden" id="bbid" value="<?php echo $order["id"]; ?>">
    <input type="hidden" id="category" value="<?php echo $order["category"]; ?>">

    <style>
        .cp{
            cursor:pointer;
        }
        .font-weight-bold{
			font-weight: bold;
		}
		.link-no-style:active,
		.link-no-style:hover,
		.link-no-style:visited,
		.link-no-style:link,
		.link-no-style {
			text-decoration: none;
			color: black;
			cursor: default;
			font-weight: normal;
		}
		.link-style{
			font-weight: bold;
			cursor: pointer;
		}
    </style>

    <section id="content" class="">
        <h3 class="text-center">
            Заказ №<?php echo $order["numb"];?> &mdash; <?php echo list_type_group_name($order["section"] ?? 0)." &mdash; ".list_type_name($order["type"] ?? 0)." &mdash; ";?>
        </h3>
        <div class="row" id="admin-form">
            <div class="panel mb25">
                <div class="panel-body p20 pb10">
                    <div class="section row mbn admin-form">
                        <div class="col-md-12 pl15">
                            <div class="section row mb15" id="con_header">
                                <?php
                                    bid_data_detail_header($id);
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
					<div class="widget-menu pull-right" >
                        <?php
                            $victoria = new \Victoria;
                            $victoria->link = "/administration/module/boiler/bid/victoria_add/{$id}";
                            $bb = $victoria->button();
                            // $bb = callAddProduct($id, "https://atri-energo.ru/administration/module/boiler/bid/addProduct/", true);
                            $btn_group = '<div class="btn-group">
                                '.$bb.'
                                <button onclick="update_body(); notif(\'primary\', \'Информация\', \'Данные успешно обновлены!\');" type="button" class="btn btn-xs btn-system">
                                    <span class="mr10 icon fa fa-refresh"></span>Обновить
                                </button>
							    <button class="btn btn-xs btn-success" onclick="save_change();">Сохранить</button>
                            </div>';
                            echo $btn_group;
                        ?>						
					</div>	
                    
              </div>
              
              <input type="hidden" value="0" id="counter">
              <input type="hidden" value="" id="saveId">
              <input type="checkbox" value="1" id="includesNds" <?php echo $order['nds_includes'] == 1 ? 'checked' : ''; ?> style="display: none;">

              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" method="POST" onsubmit="return false;">
                  <table class="table table-striped">
                    <thead id="table_thead">
                        <tr>
		                	<th class="w40"><span class="pd-l-sm"></span>#</th>
		                	<th class="w200">Номенклатура</th>
		                	<th class="w150 sku-units" data-type="1"><span>Артикул</span></th>
		                	<th class="w100 text-center" data-toggle="tooltip" title="Количество">Кол-во</th>
		                	<th class="w100 text-center" data-toggle="tooltip" title="Наличие / Доступно / Резерв">Н/Д/Р</th>		
		                	<th class="w150 cp" onclick="change_purchase_price();">
                                <span id="purchase_price_title" data-step="1">Цена закупки</span>
                                <span class="text-primary fas fa-sync-alt ml10"></span>
                            </th>
		                	<th class="w200">Тип цены</th>
		                	<th class="w100">Цена</th>
		                	<th class="w100">Скидка</th>
		                	<th class="w100" style="display: none;">Сумма скидки</th>
		                	<th class="w100" style="display: none;">Тип НДС</th>
		                	<th class="w100" style="display: none;">НДС</th>
		                	<th class="w100">Сумма</th>
		                	<th class="w100">Дата поставки</th>
		                	<th class="w40 ">Комментарий</th>		
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

                        <?php bid_data_detail_body($id); ?>
                            
					</tbody>
				<tfoot class="footer-menu">
                    <tr align="right">                    
					  <td align="right" colspan="15">
                        <nav align="right" class="text-right">						
							<select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act">							
                                <option value="discount">Массовая скидка</option>
                                <option value="update_checked_price">Обновление цен</option>
                                <option value="type_price">Массовый выбор цены</option>
                                <option value="del">Удалить выбранное</option>
							</select>
							<input name="submit" type="button" class="btn btn-success" style="display: inline-block;" id="sub" value="Выполнить" disabled onclick="subAct();"><span class="pd-l-sm"></span>	
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

    <div class="modal fade" id="local_price_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Локальная цена</h5>
          </div>
          <div class="modal-body">
            <input type="number" value="" id="local_price_modal_input" class="form-control" placeholder="Введите локальную цену">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="local_price_modal_btn" onclick="save_local_data_modal({id:gtd(this, 'id'), field:'s_price_local', value:gevbi('local_price_modal_input')});" data-dismiss="modal">Сохранить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="comment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Комментарий</h5>
          </div>
          <div class="modal-body">
            <textarea name="" id="comment_modal_input" cols="30" rows="10" class="form-control" placeholder="Введите комментарий"></textarea>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="comment_modal_btn" onclick="save_local_data_modal({id:gtd(this, 'id'), field:'comment', value:gevbi('comment_modal_input')});" data-dismiss="modal">Сохранить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="date_post_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Дата поставки</h5>
          </div>
          <div class="modal-body">
             <input type="date" value="" id="date_post_modal_input" class="form-control" placeholder="Введите дату">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="date_post_modal_btn" onclick="save_local_data_modal({id:gtd(this, 'id'), field:'date_post', value:gevbi('date_post_modal_input')});" data-dismiss="modal">Сохранить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="masDiscountModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Массовая скидка</h5>
          </div>
          <div class="modal-body">
    			<input type="number" value="" placeholder="Введите скидку" class="form-control" id="masDiscountInp">
          </div>
    	  <div class="modal-footer">
    	  	<button type="button" class="btn btn-primary" data-dismiss="modal" id="masDiscountModalBtn" onclick="setMasDiscount();">Установить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="masTypePriceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Тип цены</h5>
          </div>
          <div class="modal-body">
    			<select class="select2-single form-control" id="masTypePriceModalSel">
                <?php
    				$qq = $db->query("SELECT * FROM `".DB_PREFIX."_catalog_price_type`");
    				while($cp = $db->getRow($qq)){
    					echo '<option value="'.$cp["id"].'">'.$cp["title"].'</option>';
                    }
                ?>
    			</select>
          </div>
    	  <div class="modal-footer">
    	  	<button type="button" class="btn btn-primary" data-dismiss="modal" id="masTypePriceModalBtn" onclick="changeMasTypePrice();">Изменить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="masDelModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Удалить выбранное</h5>
          </div>
          <div class="modal-body">
    		Вы действительно хотите удалить выбранные товары?
          </div>
    	  <div class="modal-footer">
    	  	<button type="button" class="btn btn-primary" data-dismiss="modal" id="masDelModalBtn" onclick="masDel();">Удалить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>

<?php

$adminTpl->footIncludes[] = js_all_func();
$adminTpl->footIncludes[] = \PriceTypeSelect::script();
$adminTpl->footIncludes[] = '<script src="/usr/other/sub/price/js/price.js?'.time().'"></script>';
$adminTpl->footIncludes[] = '<script src="/usr/modules/boiler/admin/asset/js/function.js?'.time().'"></script>';
$adminTpl->footIncludes[] = '<script src="/usr/modules/boiler/admin/asset/js/bid_detail.js?'.time().'"></script>';
$adminTpl->footIncludes[] = '<script src="/usr/modules/boiler/admin/asset/js/bid_print_modal.js?'.time().'"></script>';
$adminTpl->footIncludes[] = <<<HTML
	<script>
		\$extendedSort.sort_update = update_body;
	</script>
HTML;
$victoria_min = new \Victoria;
$victoria_min->link = '/administration/module/boiler/bid/victoria_min_add/${id}';
$adminTpl->footIncludes[] = <<<HTML
    <script>
        function change_tov(id){
            {$victoria_min->open_func()}
        }
    </script>
HTML;

union_modal_init([
    "base_name" => DB_PREFIX."_boiler_bid_data_detail", 
    "parent_name" => "bbd_id", 
    "parent_id" => $id, 
    "fields" => ["ID" => ["field" => "id"], "Наименование" => ["field" => "s_name"], "Количество" => ["field" => "count"], "Цена" => ["field" => "s_price", "view_func" => "priceSet({%VIEW_FUNC%})"]],
    "url_send" => "/administration/module/boiler/bid/union_tov/".$id,
    "send_success" => "update_body();",
]);

xeditableInit();

require_once(ROOT."usr/modules/sales/admin/sub/ndr.php");
ndr_init();

$attr = [];
$attr = [
	"module" => 'boiler_bid_data_detail', 
	"id" => $id, 
	"update" => "tov_list", 
	"update_url" => "/administration/module/boiler/bid/data_detail_body/".$id, 
	"status" => 1,
	"hotkeys" => $btn_group,
];

//$uid = $core->auth->user_info['id'];

fastBar($attr);

$adminTpl->admin_foot();
}

function bid_data_detail_header($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $order = query_bid_data_detail_result($id);

    $query = $db->query("SELECT SUM(total) as total FROM `".DB_PREFIX."_boiler_bid_data_detail` WHERE bbd_id = '".$id."'");
    $r_total = $db->getRow($query);
    ?>

        <div class="col-md-6">
            <div class="element-groups-lg-custom wizard-options" id="ajaxLoadBtn">
                <div class="ph10">
		        	<a href="javascript:void(0);"  onclick="print_modal(<?php echo $id;?>);" class="holder-style p15 text-center holder-active"><span class="glyphicon glyphicon-print  holder-icon"></span><br>Вывести на<br>печать</a>
		        </div>

                <?php
                    if($order["bbd_status"] == "1"){
                        ?>
                            <div class="ph10">
		                    	<a href="javascript:void(0);"  onclick="end_quest();" class="holder-style p15 text-center holder-active">
                                    <span class="far fa-check-circle holder-icon"></span>
                                    <br>Завершить<br>заявку
                                </a>
		                    </div>
                        <?php
                    } else {
                        ?>
                            <div class="ph10">
		                    	<a href="javascript:void(0);"  onclick="start_quest();" class="holder-style p15 text-center holder-active">
                                    <span class="fas fa-ban holder-icon"></span>
                                    <br>Отменить<br>завершение
                                </a>
		                    </div>
                        <?php
                    }
                ?>
                <div class="ph10">
		        	<a href="javascript:void(0);"  onclick="union_s();" class="holder-style p15 text-center holder-active"><span class="fas fa-umbrella holder-icon"></span><br>Объединение<br>товаров</a>
                </div>
                
                <div class="ph10">
		        	<a href="javascript:void(0);"  onclick="get_weight_modal();" class="holder-style p15 text-center holder-active"><span class="fas fa-weight-hanging holder-icon"></span><br>Вес<br>заказа</a>
		        </div>

            </div>
            <div class="mt20">
                <?php
                    $extendedSort = new \ExtendedSort;
                    $extendedSort->link = "/administration/module/boiler/bid/extendedSort/{$id}";
                    $extendedSort->init();
                    echo $extendedSort->button();
                ?>
            </div>
        </div>
        <div class="col-md-2">
            <small>Контрагент:</small><br/>
            <b><?php echo empty($order["o_name"]) ? "Не указан" : $order["o_name"];?></b><br/><br/>
            <small>Ответственный:</small><br/>
            <b><?php echo shortUserName($order["uid"]);?></b><br/><br/>
            <small>Ответственный текущей заявки:</small><br/>
            <b><?php echo shortUserName($order["bbd_uid"]);?></b><br/><br/>
        </div>
        <div class="col-md-2">
            <small>Основной объект:</small><br/>
            <b><?php echo empty($order["obj_name"]) ? "Не указан" : $order["obj_name"];?></b><br/><br/>
            <small>Дата:</small><br/>
            <b><?php echo date("d.m.Y", strtotime($order["date_create"]));?></b><br/><br/>
            <small>Сумма заказа:</small><br/>
            <b class="all_total"><?php echo number_format($r_total["total"],2,'.',' ').' руб.';?></b><br/><br/>
        </div>
        <div class="col-md-2">
            <small>Комментарий:</small><br/>
            <b class="comment_data cp" data-pk="<?php echo $id;?>"><?php echo $order["bbd_comment"];?></b><br/><br/>
        </div>

    <?php
}

function query_bid_data_detail($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $query = $db->query("SELECT bbdd.*, cm.name as cm_name, c.title as c_title, c.sku as c_sku, c.spec as c_spec, c.attach_id as c_attach_id, c.price_data as c_price_data
    FROM `".DB_PREFIX."_boiler_bid_data_detail` as bbdd 
    LEFT JOIN `".DB_PREFIX."_catalog` as c ON(c.id = bbdd.cid) 
    LEFT JOIN `".DB_PREFIX."_catalog_manufacture` as cm ON(cm.id = c.man) 
    WHERE bbdd.bbd_id = '".$id."' 
    ORDER BY bbdd.position");
    return $query;
}

function bid_data_detail_body_data($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;

    //$order = query_bid_data($id);

    $query = query_bid_data_detail($id);
    $result = [];

    if($db->numRows($query) > 0){
        while($detail = $db->getRow($query)){
            $result[] = $detail;
        }
    }

    return $result;
}

function bid_data_detail_body($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $details = bid_data_detail_body_data($id);

    $i = 0;
    $all_total = 0;
    $all_supply_total = 0;
    $all_nds = 0;
    $priceTypeSelect = new \PriceTypeSelect;
    foreach($details as $detail){
        $i++;
        $detail["i"] = $i;

        $a_price = get_supply_price($detail["s_price_local"], $detail["cid"]);
        $detail["price"] = $a_price["price"];
        $detail["datePrice"] = $a_price["datePrice"];
        $detail["fileUrl"] = $a_price["fileUrl"];
        $detail["flagSup"] = $a_price["flagSup"];
        $detail["color"] = $a_price["color"];
        $priceTypeSelect->type = $detail["price_type"];
        $priceTypeSelect->cid = $detail["cid"];
        $priceTypeSelect->id = $detail["id"];
        $detail["price_type_select"] = $priceTypeSelect->view();

        bid_data_detail_template($detail);

        $all_total += $detail["total"];
        $all_supply_total += $a_price["price"] * $detail["count"];
        $all_nds += $detail["nds"];

    }

    ?>

        <tr>
            <td colspan="5" class=" text-right"><b>Итого:</b></td>
            <td class=" text-left"><b class="all_supply_total"><?php echo number_format($all_supply_total,2,'.',' ').' руб.';?></b></td>
            <td colspan="1" class=" text-right"><b>Итого:</b></td>
            <td colspan="7" class=" text-left"><b class="all_total"><?php echo number_format($all_total,2,'.',' ').' руб.';?></b></td>
		</tr>
		<tr>
			<td colspan="7" class=" text-right"><b>НДС:</b></td>
			<td colspan="7" class=""><b class="all_nds"><?php echo number_format($all_nds,2,'.',' ').' руб.';?></b></td>
		</tr>

    <?php
}

function get_supply_price_m($id){
    global $db;
    $query = $db->query("SELECT * FROM ".DB_PREFIX."_boiler_bid_data_detail WHERE id = '".$id."' LIMIT 1");
    $r = $db->getRow($query);
    return get_supply_price($r["s_price_local"], $r["cid"]);
}

function get_supply_price($s_price_local, $cid){
    $price = $s_price_local;
    if(empty($price)){
        $priceFunc = \Price::getPrice($cid);

        $price = $priceFunc->price_purchase_;
        $datePrice = empty($priceFunc->price_purchase_detail->price->date) ? "Отсутствует" : date("d.m.Y", strtotime($priceFunc->price_purchase_detail->price->date));
        $fileUrl = $priceFunc->price_purchase_detail->price->attach->url ?? null;
        $flagSup = empty($fileUrl) ? false : true;
        $color = "";
    } else {
        $datePrice = "Отсутствует";
        $fileUrl = "";
        $flagSup = false;
        $color = "#c1c100";
    }
    return [
        "price" => $price,
        "datePrice" => $datePrice,
        "fileUrl" => $fileUrl,
        "flagSup" => $flagSup,
        "color" => $color,
    ];
}

function get_supply_price_temp($id, $detail){
    $price = $detail["price"];
    $datePrice = $detail["datePrice"];
    $fileUrl = $detail["fileUrl"];
    $flagSup = $detail["flagSup"];
    $color = $detail["color"];

    echo '<span onclick="'.($flagSup ? 'window.open(\''.$fileUrl.'\', \'1552240782632\',\'width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0\');return false;' : '').'" class="purchasePrice '.($flagSup ? 'link-style' : 'link-no-style').'" data-price="'.$price.'" style="color:'.$color.';">' . number_format($price,2,'.',' 	'). ' руб.</span>';

    ?>

    <i class="fas fa-edit cp ml5" onclick="change_local_data_modal({id:<?php echo $id;?>, func:gbbddf, field:'s_price_local', modal_id:'local_price_modal'})"></i>

    <?php

    echo '<div class="date_purchse_price">'.$datePrice.'</div></td>';
}

function bid_data_detail_template($detail){
    global $db;

    $d_id = $detail["id"];
    $i = $detail["i"];
    $s_name = $detail["s_name"];
    $s_sku = $detail["s_sku"];
    $count = $detail["count"];
    $cid = $detail["cid"];
    $price_type = $detail["price_type"];
    $s_price = $detail["s_price"];
    $discount = $detail["discount"];
    $total = $detail["total"];
    $comment = $detail["comment"];
    $date_post = $detail["date_post"];

    $discount_sum = $detail["discount_sum"];
    $nds = $detail["nds"];
    $type_nds = $detail["type_nds"];
    $typeNds = \Price::getNds($type_nds);

    $ndr_view = get_ndr(["cid" => $cid, "stock_id" => "1", "module" => "sales", "pid" => "", "returnType" => "view"]);

    $price_data = json_decode($detail["c_price_data"], true);
    $max_allowable_discount = "";
    if(empty($price_data)){
        $max_allowable_discount = "Не указано";
    } else {
        foreach($price_data["data"] as $item){
            if(isset($item["field"]) && $item["field"] == "max_allowable_discount"){
                $max_allowable_discount = $item["value"]."%";
                break;
            }
        }
    }

    // $price = $detail["price"];
    // $datePrice = $detail["datePrice"];
    // $fileUrl = $detail["fileUrl"];
    // $flagSup = $detail["flagSup"];
    // $color = $detail["purchase_color"];

    /*
    <select class="form-control priceTypeSelect" onchange="change_price_type(<?php echo $d_id;?>, <?php echo $cid;?>, gtv(this));">
                    <?php 
                        $QpriceType = $db->query("SELECT * FROM JMY_catalog_price_type");
                        while($qpt = $db->getRow($QpriceType)){
                            ?>
                                <option value="<?php echo $qpt["id"];?>" <?php echo $qpt["id"] == $price_type ? "selected" : "";?>><?php echo $qpt["title"];?></option>
                            <?php
                        }
                        
                    ?>
                    </select>
                    */
/*
                    <?php echo $detail["price_type_select"]; ?>
*/
?>

            <tr class="tov_item" id="tr_<?php echo $d_id;?>" data-id="<?php echo $d_id;?>" data-cid="<?php echo $cid;?>">
                <td class="position" data-id="<?php echo $d_id;?>" title="id:<?php echo $d_id;?>;cid:<?php echo $cid;?>"><?php echo $i;?></td>
                <td class="cp <?php echo $s_name == $detail["c_title"] ? "" : "text-danger"; ?>" ondblclick="change_tov(<?php echo $d_id;?>);"><?php echo $s_name;?></td>
                <td class="<?php echo $s_sku == $detail["c_sku"] ? "" : "text-danger"; ?>"><?php echo $s_sku;?></td>
                <td class="text-center count cp" 
                onclick="change_field_input_on({id:<?php echo $d_id;?>, selector:'.count', value:string_to_number(gtt(this))});"
                >
                    <?php echo $count;?>
                </td>
                <td class="text-center ndr cp" data-cid="<?php echo $cid;?>" onclick="view_ndr_modal(<?php echo $cid;?>);"><?php echo $ndr_view;?></td>
                <td class="s_price_local">
                    <?php  

                        get_supply_price_temp($d_id, $detail);

                    ?>
                </td>
                <td class="price_type">

                <?php echo $detail["price_type_select"]; ?>

                </td>
                <td class="price cp" title="Максимально допустимая скидка: <?php echo $max_allowable_discount; ?>"
                onclick="check_price_type(<?php echo $d_id;?>) ? change_field_input_on({id:<?php echo $d_id;?>, selector:'.price', value:string_to_number(gtt(this)), conversion:priceSet}) : warning_price_type();"
                >
                    <?php echo number_format($s_price,2,'.',' 	'). ' руб.';?>
                </td>
                <td class="discount cp"
                onclick="change_field_input_on({id:<?php echo $d_id;?>, selector:'.discount', value:string_to_number(gtt(this)), conversion:discountSet});"
                >
                    <?php echo empty($discount) ? '0' : round($discount, 2)?>%
                </td>
                <td class="discount_sum" style="display:none;"><?php echo $discount_sum;?> руб.</td>
                <td class="type_nds" style="display:none;"><?php echo $typeNds;?>%</td>
                <td class="nds" style="display:none;"><?php echo $nds;?> руб.</td>
                <td class="total"><?php echo number_format($total,2,'.',' 	'). ' руб.';?></td>
                <td class="date_post" onclick="change_local_data_modal({id:<?php echo $d_id;?>, func:gbbddf, field:'date_post', modal_id:'date_post_modal'});">
                    <span><?php echo empty($date_post) ? "Не указан" : date("d.m.Y", strtotime($date_post));?></span>
                    <i class="fas fa-edit cp ml5"></i>
                </td>
                <td class="comment" onclick="change_local_data_modal({id:<?php echo $d_id;?>, func:gbbddf, field:'comment', modal_id:'comment_modal'});">
                    <span><?php echo empty($comment) ? "Не указан" : $comment;?></span>
                    <i class="fas fa-edit cp ml5"></i>
                </td>
                <td class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-danger btn-xs" title="Удалить" onclick="del_tov_modal(<?php echo $d_id;?>);"><i class="fas fa-times"></i></button>
                        <?php
                            $attach_id = empty($detail["c_attach_id"]) ? false : $detail["c_attach_id"];
                            if($attach_id){
                                $attach_id_list = json_decode($attach_id, true);
                                if(isset($attach_id_list[0])){
                                    ?>
                                        <a href="/download/<?php echo $attach_id_list[0];?>" target="_blank" class="btn btn-primary btn-xs" title="Скачать файл"><i class="fas fa-file-upload"></i></a>
                                    <?php
                                }
                            }
                        ?>
                    </div>
                </td>
                <td class=" w20 text-right">
					<div class="checkbox-custom mb15">
						<input id="checkbox<?php echo $d_id;?>" type="checkbox" name="checks[]" value="<?php echo $d_id;?>" class="checks">
						<label for="checkbox<?php echo $d_id;?>"></label>
					</div>
				</td>
            </tr>

<?php
}

function bid_data_tovar_add(){
    global $adminTpl, $core, $db, $admin_conf, $lang;
	if((isset($_POST['cid'])))
	{
		$id = intval($_POST['id']);
        $mcid = $_POST['cid'];
        
        $data = [
            "id" => $id,
            "goods" => [],
        ];

        foreach($mcid as $cid){	
            $sum = isset($_POST['count_'.$cid]) ? $_POST['count_'.$cid] : 1;
            $data["goods"][] = [
                "cid" => $cid,
                "count" => $sum,
            ];
        }
        bid_data_detail_add($data);
        echo '1';
	}
	else
	{
		echo '2';
	}
}

function bid_save_data_detail(){
    global $adminTpl, $core, $db, $admin_conf, $lang;
    if(isset($_POST["data"], $_POST["id"])){
        $id = $_POST["id"];
        $data = $_POST["data"];
        
        foreach($data as $key => $val){
            $str = [];
            foreach($val as $k => $v){
                switch($k){
                    case "type_nds":
                        $typeNds = \Price::getNds($v, ["mode" => true]);
                        $str[] = "`".$k."` = '".$typeNds."'";
                    break;
                    case "count":
                    case "price_type":
                    case "s_price":
                    case "discount":
                    case "discount_sum":
                    case "type_nds":
                    case "nds":
                    case "total":
                        $str[] = "`".$k."` = '".$v."'";
                    break;
                    case "rep":
                        if($v == "1"){
                            $query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '".$val["cid"]."' LIMIT 1");
                            $r = $db->getRow($query);
                            $str[] = "`cid` = '".$r["id"]."'";
                            $str[] = "`s_name` = '".$r["title"]."'";
                            $str[] = "`s_sku` = '".$r["sku"]."'";
                            $str[] = "`date_post` = null";
                        }
                    break;
                }
            }
            if(count($str) > 0){
                $values = implode(", ", $str);
                $db->query("UPDATE ".DB_PREFIX."_boiler_bid_data_detail SET ".$values." WHERE id = '".$key."' LIMIT 1");
            }
        }
    }
}

function bid_data_set_position(){
    global $db, $allowFuncs, $url, $lang, $core;
    if(isset($_POST["data"])){
        $data = $_POST["data"];
        $json = json_decode($data);
        
        foreach($json as $key => $val){
            $db->query("UPDATE ".DB_PREFIX."_boiler_bid_data_detail SET position = ".$val." WHERE id = ".$key);
        }
    }
}

function bid_del_tov_data_detail($id){
    global $db, $allowFuncs, $url, $lang, $core;
    if($id){
        $db->query("DELETE FROM `".DB_PREFIX."_boiler_bid_data_detail` WHERE id = '".$id."' LIMIT 1");
    }
}

function get_boiler_bid_data_detail_field($id, $field){
    global $db, $allowFuncs, $url, $lang, $core;
    if($id){
        $data = null;
        $query = $db->query("SELECT ".$field." FROM ".DB_PREFIX."_boiler_bid_data_detail WHERE id = '".$id."' LIMIT 1");
        if($db->numRows($query) > 0){
            $r = $db->getRow($query);
            $data = $r[$field];
        }
        return $data;
    }
}

function get_tov_item($id, $cid, $field){
    global $db, $allowFuncs, $url, $lang, $core;
    $detail = [];
    $detail["id"] = $id;
    $detail["i"] = $field["position"];
    $query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '".$cid."' LIMIT 1");
    $r = $db->getRow($query);

    $detail["c_price_data"] = $r["price_data"];

    $detail["s_name"] = $detail["c_title"] = $r["title"];
    $detail["s_sku"] = $detail["c_sku"] = $r["sku"];
    $detail["count"] = $field["count"];
    $detail["cid"] = $cid;
    $detail["price_type"] = 3;
    $priceFunc = \Price::getPrice($cid);
    $detail["s_price"] = $priceFunc->price_list_;

    $detail["discount"] = 0;
    $detail["comment"] = $field["comment"];

    $detail["discount_sum"] = 0;

    $detail["type_nds"] = $field["type_nds"];

    $data = \Price::get_all([
        "price" => $detail["s_price"], 
        "count" => $detail["count"],  
        "nds_includes" => $field["nds_includes"], 
        "nds_proc" => $field["type_nds"]
    ]);

    $ndsCalc = $data["nds"];
    $total = $data["total"];
    $detail["nds"] = $ndsCalc;


    $detail["total"] = $total;

    $priceFunc = \Price::getPrice($cid);
    $detail["price"] = $priceFunc->price_purchase_;
    $detail["datePrice"] = empty($priceFunc->price_purchase_detail->price->date) ? "Отсутствует" : date("d.m.Y", strtotime($priceFunc->price_purchase_detail->price->date));
    $detail["fileUrl"] = $priceFunc->price_purchase_detail->price->attach->url ?? null;
    $detail["flagSup"] = empty($fileUrl) ? false : true;
    $detail["color"] = "";

    $detail["date_post"] = "";

    $priceTypeSelect = new \PriceTypeSelect;
    $priceTypeSelect->type = $detail["price_type"];
    $priceTypeSelect->cid = $detail["cid"];
    $priceTypeSelect->id = $detail["id"];
    $detail["price_type_select"] = $priceTypeSelect->view();

    bid_data_detail_template($detail);
} 

function union_tov($data){
    global $db;
    if($data){
        foreach($data as $key => $val){
            if(!empty($val["union"])){
                $count = 0;
                if(count($val["union_list"]) > 0){
                    $id_str = implode(", ", $val["union_list"]);
                    $query = $db->query("SELECT SUM(count) as sum_count FROM ".DB_PREFIX."_boiler_bid_data_detail WHERE id IN(".$id_str.")");
                    if($db->numRows($query) > 0){
                        $r = $db->getRow($query);
                        $count = $r["sum_count"];
                        if(($key = array_search($val["union"],$val["union_list"])) !== FALSE){
                            unset($val["union_list"][$key]);
                        }
                        $query = $db->query("SELECT bbdd.*, bb.nds_includes, bb.org
                        FROM `".DB_PREFIX."_boiler_bid_data_detail` as bbdd
                        LEFT JOIN `".DB_PREFIX."_boiler_bid_data` as bbd ON(bbd.id = bbdd.bbd_id)
                        LEFT JOIN `".DB_PREFIX."_boiler_bid_type` as bbt ON(bbt.id = bbd.bid_type_id) 
                        LEFT JOIN `".DB_PREFIX."_boiler_bid` as bb ON(bb.id = bbt.bid_id)
                        WHERE bbdd.id = '".$val["union"]."' LIMIT 1");
                        $r = $db->getRow($query);

                        $query = $db->query("SELECT nds FROM `".DB_PREFIX."_org` WHERE id = '".$r["org"]."' LIMIT 1");
                        $r1 = $db->getRow($query);
                        $nds = is_null($r1["nds"]) ? 3 :$r1["nds"];

                        $price_rub = $r["s_price"];
                        //$price_type = $r["price_type"];
                        // $price_rub_dicount = $price_rub * ((100 - $r["discount"]) / 100);

			            // $ndsCalc = 0;
			            // $typeNds = getNds($nds);
			            // if($r["nds_includes"] === "1"){
			            // 	$ndsCalc = ($price_rub_dicount * $typeNds / (100 + $typeNds)) * $count;
			            // } else {
			            // 	$ndsCalc = ($price_rub_dicount * ($typeNds / 100)) * $count;
			            // }
                    
			            // $ndsCalc = round($ndsCalc, 2, PHP_ROUND_HALF_UP);
                    
			            // $total = 0;
			            // if($r["nds_includes"] === "1"){
			            // 	$total = $price_rub_dicount * $count;
			            // } else {
			            // 	$total = ($count * $price_rub_dicount) + $ndsCalc;
			            // }
                        // $total = round($total, 2, PHP_ROUND_HALF_UP);
                        
                        // $discount_sum = $price_rub * $count - $price_rub_dicount;

                        $data = \Price::get_all([
                            "price" => $price_rub, 
                            "count" => $count, 
                            "discount" => $r["discount"], 
                            "nds_includes" => $r["nds_includes"], 
                            "nds_type" => $nds,
                        ]);

                        $total = $data["total"];
                        $ndsCalc = $data["nds"];
                        $discount_sum = $data["discount_sum"];

                        update_bid_price_data($val["union"], ["count" => $count, "discount_sum" => $discount_sum, "nds" => $ndsCalc, "total" => $total]);

                        $id_str = implode(", ", $val["union_list"]);
                        $db->query("DELETE FROM `".DB_PREFIX."_boiler_bid_data_detail` WHERE id IN(".$id_str.")");
                    }
                }
            }
        }
    }
}

function update_bid_price_data($id, $request){
    //["count" => "", "discount_sum" => "", "nds" => "", "total" => ""]
    global $db;
    $db->query("UPDATE `".DB_PREFIX."_boiler_bid_data_detail` SET count = '".$request["count"]."', discount_sum = '".$request["discount_sum"]."', nds = '".$request["nds"]."', total = '".$request["total"]."' WHERE id = '".$id."' LIMIT 1");
}

function change_all_price_boiler_bid($id){
    global $db;
    $order = query_bid_data_detail_result($id);
    $details = bid_data_detail_body_data($id);
    foreach($details as $detail){
         $p = \Price::get_all([
             "price" => $detail["s_price"], 
             "count" => $detail["count"], 
             "discount" => $detail["discount"], 
             "nds_includes" => $order["nds_includes"], 
             "nds_type" => $order["org_nds"],
         ]);
         $db->query("UPDATE `".DB_PREFIX."_boiler_bid_data_detail` SET discount_sum = '".$p["discount_sum"]."', type_nds = '".$p["nds_type"]."', nds = '".$p["nds"]."', total = '".$p["total"]."' WHERE id = '".$detail["id"]."' LIMIT 1");
    }
 }

function get_weight_boiler_bid($id){
    global $db;
    $query = $db->query("SELECT c.weight, c.title, c.sku, bbdd.position, bbdd.count 
    FROM `".DB_PREFIX."_boiler_bid_data_detail` as bbdd 
    LEFT JOIN `".DB_PREFIX."_catalog` as c ON(bbdd.cid = c.id)
    WHERE bbdd.bbd_id = '".$id."'");
    $data = [
        "error" => false,
        "item" => [],
        "weight" => 0,
        "empty_weight" => [],
    ];
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){
            try{
                $weight = json_decode($r["weight"], true);
                $w = isset($weight[1]) ? $weight[1] : 0;
            } catch(Exception $e){
                $w = 0;
            }
            
            if(empty($w)){
                $data["empty_weight"][] = $r;
            } else {
                $data["weight"] += (double)str_replace(",", ".", $w) * $r["count"];
            }
            $data["item"][] = $r;
        }
    }
    return $data;
}

function bid_print($id){
    require_once(ROOT."/vendor/autoload.php");
    require_once(ROOT.'usr/plugins/PHPWord.php');
    $time = time();
    $tpl = ROOT."/files/tpl/boiler/boiler.docx";
    $file = ROOT."/files/tpl/boiler/boiler_{$time}.docx";
    $document =  new \PhpOffice\PhpWord\TemplateProcessor($tpl);
    $order_data = [];
    $details = bid_data_detail_body_data($id);
    $i = 0;
    foreach($details as $detail){
        $i++;
        $s_unit = get_unit('data', $detail['s_unit'] ,'sname');
        $name = ee($detail['c_title']);
        $spec = json_decode($detail['c_spec'], true);
        if(is_array($spec) && !empty($spec['1'])) $name = ee($spec[1]);
        $order_data[] = [
            "n" => $i,
            "s_name" => $name,
            "cm_name" => $detail["cm_name"],
            "count" => $detail["count"],
            "unit" => $s_unit,
        ];
    }
    $document->cloneRowAndSetValues('n', $order_data);
    $document->saveAs($file);

    $filename = basename($file);
    $size = filesize($file);
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-Length: $size");
    header("Charset: UTF-8");
    header("Content-Type: application/unknown");
    readfile($file);
    unlink($file);
}

function print_boiler_sales($id, $type, $data = []){
    global $db;
    require_once ROOT."usr/modules/sales/admin/sub/print.php";
    $request = [];

    $request["id"] = $id;
	$request["module"] = "sales";
	$order = query_bid_data_detail_result($id);
	$request["date"] = $order["date_create"];
	$request["org"] = $order["org"];
	$request["oid"] = $order["oid"];
	$request["object"] = $order["object"];
	$order_data = [];
	$query = query_bid_data_detail($id);
	$org = getOrgInfo($order['org']);	
	$org_nds = \Price::getNds($org['org']['nds']);		
	if($db->numRows($query) > 0) {
		$i = 0;
		$total_all = 0;
		$nds_all = 0;
		while($catalog = $db->getRow($query)) {
			$i++;
			$stable_name = $catalog['s_name'];
			$stable_name = ee($stable_name);
			$stable_name = ee($stable_name);
			$stable_name = str_replace("<", "&lt;", $stable_name);
			$stable_name = str_replace(">", "&gt;", $stable_name);
			$stable_sku = $catalog['s_sku'];
			$stable_unit = $catalog['s_unit'];
			$sum = $catalog['count'];
			$price = round(($catalog['total']/ $catalog['count']), 2);
			$total_nds = $catalog['total'];	
            $total_all += $total_nds;
			
			if ($org_nds != '0')
			{
				$nds = round(($total_nds/120*20), 2);
				$total = $total_nds - $nds;
				// $total_all = $total_all + round($total, 2);
				
				$price_with_nds = round(($catalog['total']/ $catalog['count']), 2);
				$price = round($price_with_nds-($price_with_nds/120*20), 2);

				$nds_all += $nds;
			}
			else
			{
				$price = round(($catalog['total']/ $catalog['count']), 2);
				$price_with_nds = $price;
				$total = $total_nds;
				// $total_all = $total_all + round($total, 2);
				$nds = 0;

				$nds_all += $nds;
			}		
            $purchase_price = 0;
            if($type == "costing_purchase"){
                $sp = get_supply_price($catalog["s_price_local"], $catalog["cid"]);
                $purchase_price = $sp["price"];
            }		
			$order_data[] =  array(
					'n'        			=> $i,
					'sku' 				=> $stable_sku,
					'name'     			=> $stable_name,
					's'     			=> $sum,
					'u'     			=> get_unit('data', $stable_unit),
					'price'     		=> number_format($price,2,',',''),
					'price_with_nds' 	=> number_format($price_with_nds,2,',',''),
					'nds' 				=> number_format($nds,2,',',''),
					'total' 			=> number_format($total,2,',',''),
					'total_with_nds' 	=> number_format($total_nds,2,',',''),
					'time'     			=> '',
					'space'    			=> '',
					'purchase_price'    => $purchase_price,
			);
		}
	}
	$request["order_data"] = $order_data;
	$request["total"] = $total_all;
	$request["nds"] = $nds_all;
	// $request["uid"] = $order["uid"];
	$request["doc_numb"] = $order["numb"];
	$request["format"] = isset($data["format"]) ? $data["format"] : '';
	$request["type"] = $type;
	$request["autoprint"] = isset($data["autoprint"]) ? $data["autoprint"] : '';
    
    universal_print($request);
}