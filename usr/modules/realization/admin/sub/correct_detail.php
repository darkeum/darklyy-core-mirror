<?php

function correct_main(){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = "Лист";
	$adminTpl->admin_head('Корректировка | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'realization';
	$atributs['module_open'] = 'correct_detail';
	load_grid('jqCorrect', $lnn, $atributs, ROOT.'/usr/modules/realization/admin/grid/');

	$adminTpl->admin_foot();
}

function set_correct($id, $field, $value){
    global $db;
    $db->query("UPDATE `".DB_PREFIX."_correct` SET ".$field." = '".$value."' WHERE id = '".$id."' LIMIT 1");
}

function set_correct_detail($id, $field, $value){
    global $db;
    $db->query("UPDATE `".DB_PREFIX."_correct_detail` SET ".$field." = '".$value."' WHERE id = '".$id."' LIMIT 1");
}

function query_correct_detail_result($id){
    global $db;
    $query = $db->query("SELECT c.*,
    r.nds_includes, r.stock_id, r.numb as r_numb, r.id as r_id, r.date as r_date,
    s.id as s_id, s.numb as s_numb,
    o.id as o_id, o.name as o_name, obj.name as obj_name,
    c2.numb as c2_numb, c2.id as c2_id
    FROM `".DB_PREFIX."_correct` as c
    LEFT JOIN `".DB_PREFIX."_realization` as r ON(r.id = c.realization_id)
    LEFT JOIN `".DB_PREFIX."_sales` as s ON(s.id = r.sales_id)
    LEFT JOIN `".DB_PREFIX."_organizations` as o ON(r.oid = o.id)
    LEFT JOIN `".DB_PREFIX."_object` as obj ON(r.object = obj.id)
    LEFT JOIN `".DB_PREFIX."_correct` as c2 ON(c.pid = c2.id)
    WHERE c.id = '".$id."' LIMIT 1");

    $r = $db->getRow($query);

    return $r;
}

function correct_detail($id){
    global $db, $adminTpl, $core;

    $order = query_correct_detail_result($id);

    $adminTpl->admin_head("0_o");
    ?>

    <input type="hidden" id="mainId" value="<?php echo $id; ?>">

    <style>
        .cp{
            cursor:pointer;
        }
        .before_change{
            margin-bottom: 20px;
            color: grey;
        }
    </style>

    <section id="content" class="">
        <h3 class="text-center">
            Корректировка №<?php echo $order["numb"];?>
        </h3>
        <div class="row" id="admin-form">
            <div class="panel mb25">
                <div class="panel-body p20 pb10">
                    <div class="section row mbn admin-form">
                        <div class="col-md-12 pl15">
                            <div class="section row mb15" id="con_header">
                                <?php
                                    correct_detail_header($id);
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
                            $btn_group = '<div class="btn-group">
                                <button onclick="update_body(); notif(\'primary\', \'Информация\', \'Данные успешно обновлены!\');" type="button" class="btn btn-xs btn-system">
                                    <span class="mr10 icon fa fa-refresh"></span>Обновить
                                </button>
							    <button class="btn btn-xs btn-success" onclick="save_change();" id="save_change" style="display: '.($order["active"] == 0 ? "" : "none").';">Сохранить</button>
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
                            <th class="w100"><span>Артикул</span></th>
                            <th class="w100 text-center" data-toggle="tooltip" title="Единицы измерения">Ед. изм.</th>
                            <th class="w100 text-center" data-toggle="tooltip" title="Наличие / Доступно / Резерв">Н/Д/Р</th>
		                	<th class="w150"></th>
		                	<th class="w100 text-center" data-toggle="tooltip" title="Количество">Кол-во</th>
		                	<!-- <th class="w150 cp" onclick="change_purchase_price();">
                                <span id="purchase_price_title" data-step="1">Цена закупки</span>
                                <span class="text-primary fas fa-sync-alt ml10"></span>
                            </th> -->
		                	<!-- <th class="w200">Тип цены</th> -->
		                	<th class="w100">Цена</th>
                            <th class="w100">Скидка</th>
                            <th class="w100">Сумма</th>
                            <th class="" style="display:none;">Сумма скидки</th>
		                	<th class="w100">Ставка НДС</th>
		                	<th class="w100">НДС</th>
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

                        <?php correct_detail_body($id); ?>

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

    <div class="modal fade" id="addFileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Добавить файл</h5>
          </div>
          <div class="modal-body">
            <form id="addFileModalForm" onsubmit="return false;">
                <?php
                    $attribute = [];
			    	$attribute["name"] = "";
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
            <button type="button" class="btn btn-primary" id="addFileModalBtn">Добавить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>

<?php

$adminTpl->footIncludes[] = '<script src="/usr/other/sub/price/js/function.js?'.time().'"></script>';
$adminTpl->footIncludes[] = '<script src="/usr/modules/realization//admin/asset/js/correct_detail.js?'.time().'"></script>';


xeditableInit();

require_once(ROOT."usr/modules/sales/admin/sub/ndr.php");
ndr_init();


$adminTpl->admin_foot();
}

function get_correct_total($id){
    global $db;
    $query = $db->query("SELECT SUM(total) as total FROM `".DB_PREFIX."_correct_detail` WHERE correct_id = '".$id."'");
    $r = $db->getRow($query);
    return $r["total"];
}

function correct_detail_header($id){
global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $order = query_correct_detail_result($id);

    $total = get_correct_total($id);
    ?>

        <div class="col-md-5">
            <div class="element-groups-lg-custom wizard-options" id="ajaxLoadBtn">
                <div class="ph10">
		        	<a href="/administration/module/realization/correct_print/<?php echo $id;?>" class="holder-style p15 text-center holder-active"><span class="glyphicon glyphicon-print  holder-icon"></span><br>Вывести на<br>печать</a>
		        </div>

                <?php
                    if($order["active"] == 0){
                        ?>
                            <div class="ph10">
		                    	<a href="javascript:void(0);"  onclick="end_modal();" class="holder-style p15 text-center holder-active">
                                    <span class="far fa-check-circle holder-icon"></span>
                                    <br>Завершить<br>заявку
                                </a>
		                    </div>
                        <?php
                    } else {
                        ?>
                            <div class="ph10">
		                    	<a href="javascript:void(0);"  onclick="start_quest();" class="holder-style p15 text-center holder-active" style="outline-color: red;">
                                    <span class="fas fa-ban holder-icon" style="color: red;"></span>
                                    <br>Отменить<br>завершение
                                </a>
		                    </div>
                        <?php
                    }
                ?>

                <div class="ph10">
		        	<a href="javascript:void(0);"  onclick="<?php echo $order["active"] == 0 ? "correctCorrectModalError();" : "correctCorrectModal();";?>" class="holder-style p15 text-center holder-active"><span class="fas fa-pencil-ruler holder-icon"></span><br>Корр.<br>корр.</a>
		        </div>

            </div>
        </div>
        <div class="col-md-2">
            <small>Продажа:</small><br/>
            <b class=""><a href="/administration/module/sales/order/<?php echo $order["s_id"];?>"><?php echo $order["s_numb"];?></a></b><br/><br/>
            <small>Реализация:</small><br/>
            <b class=""><a href="/administration/module/realization/order/<?php echo $order["r_id"];?>"><?php echo $order["r_numb"];?></a></b><br/><br/>
            <?php
                if(check_correct_mod($order)){
                    ?>

                        <small>Корректировка:</small><br/>
                        <b class=""><a href="/administration/module/realization/correct_detail/<?php echo $order["c2_id"];?>"><?php echo $order["c2_numb"];?></a></b><br/><br/>

                    <?php
                }
            ?>
        </div>
        <div class="col-md-2">
            <small>Организация:</small><br/>
            <b><?php
                $org = getOrgInfo($order["org"]);
                echo $org["organizations"]["name"];
            ?></b><br/><br/>
            <small>Основной объект:</small><br/>
            <b><?php echo empty($order["obj_name"]) ? "Не указан" : $order["obj_name"];?></b><br/><br/>
            <small>Дата:</small><br/>
            <b><span id="date" data-value="<?php echo $order["date"];?>" data-disabled="<?php echo $order["active"] == 1 ? "true" : "false";?>"><?php echo date("d.m.Y", strtotime($order["date"]));?></span></b><br/><br/>
        </div>
        <div class="col-md-2">
            <!-- <small>Сумма заказа:</small><br/>
            <b class="all_total"><?php echo number_format($total,2,'.',' ').' руб.';?></b><br/><br/> -->

            <small>Контрагент:</small><br/>
            <b><?php echo empty($order["o_name"]) ? "Не указан" : $order["o_name"];?></b><br/><br/>
            <small>Ответственный:</small><br/>
            <b><?php echo shortUserName($order["uid"]);?></b><br/><br/>
            <small>Файлы:</small><i class="fas fa-plus ml10 cp text-success" data-toggle="modal" data-target="#addFileModal"></i><br/>
            <div><?php
                $items = \Boot\Models\Attach::where("module", "correct")->where("pid", $id);
                if($items->exists()){
                    foreach($items->get() as $item){
                        echo "<div><a href='/download/{$item->id}'>{$item->name}</a></div>";
                    }
                } else {
                    echo "<b>Файлы не приложены!</b>";
                }
            ?></div><br/><br/>
        </div>
    <?php
}

function query_correct_detail($id){
   return \Realization\Support\CorrectDetailSupport::getCorrectDetailQuery($id);
}

function check_correct_mod($request){
    $module = $request["module"];
    return $module == "correct";
}

function correct_detail_body($id){
    global $db, $core;
    $order = query_correct_detail_result($id);
    //если корректировка от корректировки
    $flagCorrect = check_correct_mod($order);

    $query = query_correct_detail($id);
    if($db->numRows($query) > 0){
        $i = 0;
        while($r = $db->getRow($query)){
            $i++;

            $unit = get_unit('data', $r['s_unit'] ,'sname');
            $cid = $r["cid"];
            $ndr_view = get_ndr(["cid" => $cid, "stock_id" => "1", "module" => "sales", "pid" => "", "returnType" => "view"]);
            $typeNds = \Price::getNds($r["nds_type"]);

            $count = $flagCorrect ? $r["cd2_count"] : $r["rd_count"];
            $price = $flagCorrect ? $r["cd2_s_price"] : $r["rd_s_price"];
            $discount = $flagCorrect ? $r["cd2_discount"] : $r["rd_discount"];
            //(empty($content['discount']) ? '0' : round($content['discount'], 2))
            $total = $flagCorrect ? $r["cd2_total"] : $r["rd_total"];
            $discount_sum = $flagCorrect ? $r["cd2_discount_sum"] : $r["rd_discount_sum"];
            $nds_type = $flagCorrect ? \Price::getNds($r["cd2_nds_type"]) : \Price::getNds($r["rd_nds_type"]);
            $nds = $flagCorrect ? $r["cd2_nds"] : $r["rd_nds"];

            ?>

                <tr id="tr_<?php echo $r["id"];?>" data-id="<?php echo $r["id"];?>" data-cid="<?php echo $cid;?>" class="tov_item">
                    <td class="position" title="id: <?php echo $r["id"];?>; cid: <?php echo $cid;?>;"><?php echo $i;?></td>
                    <td><?php echo $r["s_name"];?></td>
                    <td><?php echo $r["s_sku"];?></td>
                    <td class="text-center"><?php echo $unit;?></td>
                    <td class="text-center ndr cp" data-cid="<?php echo $cid;?>" onclick="view_ndr_modal(<?php echo $cid;?>);"><?php echo $ndr_view;?></td>
                    <td class="text-right">
                        <div class="before_change">до изменения</div>
                        <div class="after_change">после изменения</div>
                    </td>
                    <td>
                        <div class="before_change text-center"><?php echo $count;?></div>
                        <div class="after_change text-center cp count"
                            onclick="change_field_input_on({id:<?php echo $r['id'];?>, selector:'.count', value:string_to_number(gtt(this))});"
                        >
                        <?php echo $r["count"];?></div>
                    </td>
                    <td>
                        <div class="before_change"><?php echo number_format($price,2,'.',' '). ' руб.'; ?></div>
                        <div class="after_change cp s_price"
                            onclick="change_field_input_on({id:<?php echo $r['id'];?>, selector:'.s_price', value:string_to_number(gtt(this)), conversion:priceSet});"
                        ><?php echo number_format($r["s_price"],2,'.',' '). ' руб.'; ?></div>
                    </td>
                    <td>
                        <div class="before_change"><?php echo $discount.'%';?></div>
                        <div class="after_change cp discount"
                            onclick="change_field_input_on({id:<?php echo $r['id'];?>, selector:'.discount', value:string_to_number(gtt(this)), conversion:discountSet});"
                        ><?php echo $r["discount"].'%';?></div>
                    </td>
                    <td>
                        <div class="before_change"><?php echo number_format($total,2,'.',' '). ' руб.'; ?></div>
                        <div class="after_change total"><?php echo number_format($r["total"],2,'.',' '). ' руб.'; ?></div>
                    </td>
                    <td style="display:none;">
                        <div class="before_change"><?php echo number_format($discount_sum,2,'.',' '). ' руб.'; ?></div>
                        <div class="after_change discount_sum"><?php echo number_format($r["discount_sum"],2,'.',' '). ' руб.'; ?></div>
                    </td>
                    <td>
                        <div class="before_change"><?php echo $nds_type;?>%</div>
                        <div class="after_change nds_type"><?php echo $typeNds;?>%</div>
                    </td>
		            <td>
                        <div class="before_change"><?php echo number_format($nds,2,'.',' '). ' руб.'; ?></div>
                        <div class="after_change nds"><?php echo number_format($r["nds"],2,'.',' '). ' руб.'; ?></div>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-xs fas fa-times" onclick="delete_modal(<?php echo $r['id'];?>);"></button>
                    </td>
                    <td class="text-right">
                        <div class="checkbox-custom mb15">
					    	<input id="checkbox<?php echo $r["id"];?>" type="checkbox" name="checks[]" value="<?php echo $r["id"];?>" class="checks">
					    	<label for="checkbox<?php echo $r["id"];?>"></label>
					    </div>
                    </td>
                </tr>

            <?php
        }
    } else {
        ?>

            Товары не указаны!

        <?php
    }
}

function correct_save($id, $data){
    global $db;
    $allow_field = ["count", "s_price", "discount", "discount_sum", "nds", "total"];
    if(is_array($data) && count($data) > 0){
        foreach($data as $key => $val){
            $update = [];
            foreach($val as $k => $v){
                if(in_array($k, $allow_field)){
                    $update[] = $k." = '".$v."'";
                }
            }
            $update_str = implode(", ", $update);
            $db->query("UPDATE `".DB_PREFIX."_correct_detail` SET ".$update_str." WHERE id = '".$key."' LIMIT 1");
        }
    }
}

function correct_check_minus($id){
    global $db;
    // $query = $db->query("SELECT r.stock_id FROM `".DB_PREFIX."_correct` as c LEFT JOIN `".DB_PREFIX."_realization` as r ON(r.id = c.realization_id) WHERE c.id = '".$id."' LIMIT 1");
    // $r = $db->getRow($query);
    // $query = $db->query("SELECT cd.*, rd.cid FROM `".DB_PREFIX."_correct_detail` as cd LEFT JOIN `".DB_PREFIX."_realization_detail` as rd ON(rd.id = cd.realization_detail_id) WHERE cd.correct_id = '".$id."'");
    $order = query_correct_detail_result($id);
    $flagCorrect = check_correct_mod($order);

    $query = query_correct_detail($id);
    $data = [];
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){
            $count_before = $flagCorrect ? $r["cd2_count"] : $r["rd_count"];
            $count_after = $r["count"];
            if(($count_before != $count_after) && ($count_before < $count_after)){
                $data[$r["id"]] = ["pid" => $r["id"], "module" => "correct", "count" => $count_after, "cid" => $r["cid"], "stock_id" => $order["stock_id"]];
            }
        }
    }
    // dd($data);
    echo check_minus_count($data);
}

function getBalanceObject($request){
    $balance = new \Economic\Balance;
	$balance->org = $request["org"];
	$balance->oid = $request["o_id"];
	$balance->date = $request["date"];
	// $balance->value = $total;
	$balance->pid = $request["id"];
	$balance->module = "correct";
	$reestr = \Reestr\Models\Reestr::searchByModule("sales", $request["s_id"]);
	$balance->reestr_id = $reestr->id ?? null;
    return $balance;
}

function correct_end($id){
    global $db;
    $order = query_correct_detail_result($id);

    $flagCorrect = check_correct_mod($order);

    $query = query_correct_detail($id);
    $total = 0;
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){

            $total_ = $flagCorrect ? $r["cd2_total"] : $r["rd_total"];
            $total += $r["total"] - $total_;

            //было
            $count_before = $flagCorrect ? $r["cd2_count"] : $r["rd_count"];
            //стало
            $count_after = $r["count"];
            //итог
            $count = $count_before - $count_after;
            $abs_count = abs($count);
            if($count != 0){
                if($count > 0){
                    $sign = "plus";
                } else {
                    $sign = "minus";
                }
                $source = [];
	            $source['module'] = 'correct';
                $source['pid'] = $id;
                $source['comment'] = 'Проведение корректировки id-parent:'.$id.'; id-child:'.$r["id"].'; №: '.$order["numb"];

                updateQuantity($r["cid"], $order["stock_id"], $abs_count, $sign, $source);
            }
        }
    }
    set_correct($id, "active", 1);

    $balance = getBalanceObject($order);
    $balance->value = $total;
    $balance->action = "plus";
	$balance->create();
}

function correct_start($id){
    global $db;
    $order = query_correct_detail_result($id);

    $flagCorrect = check_correct_mod($order);

    $query = query_correct_detail($id);
    $total = 0;
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){

            $total_ = $flagCorrect ? $r["cd2_total"] : $r["rd_total"];
            $total += $r["total"] - $total_;

            //было
            $count_before = $flagCorrect ? $r["cd2_count"] : $r["rd_count"];
            //стало
            $count_after = $r["count"];
            //итог
            $count = $count_before - $count_after;
            $abs_count = abs($count);
            if($count != 0){
                if($count > 0){
                    $sign = "minus";
                } else {
                    $sign = "plus";
                }
                $source = [];
	            $source['module'] = 'correct';
                $source['pid'] = $id;
                $source['comment'] = 'Распроведение корректировки id-parent:'.$id.'; id-child:'.$r["id"].'; №: '.$order["numb"];

                updateQuantity($r["cid"], $order["stock_id"], $abs_count, $sign, $source);
            }
        }
    }
    set_correct($id, "active", 0);

    $balance = getBalanceObject($order);
    $balance->value = $total;
    $balance->action = "minus";
	$balance->create();
}

function correct_print($id){
    global $db;
    require_once(ROOT."/vendor/autoload.php");

    require_once(ROOT.'usr/plugins/PHPWord.php');

    $order = query_correct_detail_result($id);
    $flagCorrect = check_correct_mod($order);

    $org = getOrgInfo($order["org"]);
    $org_nds = \Price::getNds($org["org"]['nds']);
    if ($org_nds != '0'){
		$org_nds_view = $org_nds.'%';
		$st = '1';
	} else {
		$org_nds_view = 'Без НДС';
		$st = '2';
    }
    $date = strtotime($order['date']);
    $date_p = strtotime($order['r_date']);
    $Month_rus = getMonthRus();

    $oid = \Organizations\Model\Organizations::find($order["o_id"])->toArray();

    $doc = "Универсальный передаточный документ ".date("d.m.Y", $date_p).", №".$order["r_numb"];
    $doc_change = "Договор поставки П-274 от 30.03.2018";

    $u_head = "";
    $u_buch = "";
    $u_ship = "";
    $u_ship_post = "";

    $tpl_list = [
        ROOT."files/tpl/correct/correct.docx",
        ROOT."files/tpl/correct/correct_one.docx",
        ROOT."files/tpl/correct/correct_one_next.docx",
    ];

    $order_data = [
        'I' => [],
        'SKU' => [],
        'NAME' => [],
        'CT' => [],
        'CT_A' => [],
        'UN' => [],
        'UN_A' => [],
        'U' => [],
        'N_SUM' => [],
        'N_SUM_A' => [],
        'N_SUM_I' => [],
        'N_SUM_A_D' => [],
        'AK' => [],
        'AK_A' => [],
        'PRICE' => [],
        'PRICE_A' => [],
        'PRICE_T' => [],
        'PRICE_T_A' => [],
        'PRICE_T_I' => [],
        'PRICE_T_A_D' => [],
        'TOTAL' => [],
        'TOTAL_A' => [],
        'TOTAL_I' => [],
        'TOTAL_A_D' => [],
    ];

    $order_data_l = $order_data;

    $nf = function($price){
        return number_format($price,2,'.',' ');
    };

    $i = 1;
    $query = query_correct_detail($id);
    $tpl = $tpl_list[0];

    $flag_s = true;
    $flag_l = true;
    $t_price_t_i = 0;
    $t_price_t_a_d = 0;
    $t_n_sum_i = 0;
    $t_n_sum_a_d = 0;
    $t_total_i = 0;
    $t_total_a_d = 0;
    $numRows = $db->numRows($query);
    if($numRows > 0){
        while($r = $db->getRow($query)){
            if($numRows == 1){
                if(mb_strlen($r["s_name"]) < 61){
                    $tpl = $tpl_list[1];
                    $flag_l = false;
                } else {
                    $tpl = $tpl_list[2];
                    $flag_s = false;
                }
            }

            $order_data["I"][] = $i;
            $order_data["SKU"][] = $r["s_sku"];
            $order_data["NAME"][] = $r["s_name"];

            $stable_unit_code = empty($r['s_unit']) ?  '796' : $r['s_unit'];
            $stable_unit = get_unit('data', $stable_unit_code);
            $order_data["U"][] = $stable_unit_code;
            $order_data["UN"][] = $stable_unit;
            $order_data["U_A"][] = $stable_unit_code;
            $order_data["UN_A"][] = $stable_unit;

            $b_nds_type = $flagCorrect ? $r["cd2_nds_type"] : $r["rd_nds_type"];
            $a_nds_type = $r["nds_type"];
            $b_nds = \Price::getNds($b_nds_type);
            $a_nds = \Price::getNds($a_nds_type);
            $b_count = $flagCorrect ? $r["cd2_count"] : $r["rd_count"];
            $order_data["CT"][] = $b_count;
            $order_data["CT_A"][] = $r["count"];

            $b_price = $flagCorrect ? $r["cd2_s_price"] : $r["rd_s_price"];
            $b_discount = $flagCorrect ? $r["cd2_discount"] : $r["rd_discount"];
            $b_d_price_o = \Price::get_all([
                "price" => $b_price,
                "count" => 1,
                "discount" => $b_discount,
                "nds_type" => $b_nds_type,
                "nds_includes" => $order["nds_includes"],
            ]);
            $b_d_price = \Price::get_all([
                "price" => $b_price,
                "count" => $b_count,
                "discount" => $b_discount,
                "nds_type" => $b_nds_type,
                "nds_includes" => $order["nds_includes"],
            ]);

            $a_price = $r["s_price"];
            $a_discount = $r["discount"];
            $a_d_price_o = \Price::get_all([
                "price" => $a_price,
                "count" => 1,
                "discount" => $a_discount,
                "nds_type" => $a_nds_type,
                "nds_includes" => $order["nds_includes"],
            ]);
            $a_d_price = \Price::get_all([
                "price" => $a_price,
                "count" => $r["count"],
                "discount" => $a_discount,
                "nds_type" => $a_nds_type,
                "nds_includes" => $order["nds_includes"],
            ]);

            $order_data["PRICE"][] = $nf($b_d_price_o["discount_price"] - $b_d_price_o["nds"]);
            $order_data["PRICE_A"][] = $nf($a_d_price_o["discount_price"] - $a_d_price_o["nds"]);
            $price_t = $b_d_price["total"] - $b_d_price["nds"];
            $price_t_a = $a_d_price["total"] - $a_d_price["nds"];
            $order_data["PRICE_T"][] = $nf($price_t);
            $order_data["PRICE_T_A"][] = $nf($price_t_a);

            $ak = 'без акциза';
            $order_data["AK"][] = $ak;
            $order_data["AK_A"][] = $ak;

            $order_data["N"][] = $b_nds;
            $order_data["N_A"][] = $a_nds;

            $order_data["N_SUM"][] = $nf($b_d_price["nds"]);
            $order_data["N_SUM_A"][] = $nf($a_d_price["nds"]);

            $order_data["TOTAL"][] = $nf($b_d_price["total"]);
            $order_data["TOTAL_A"][] = $nf($a_d_price["total"]);

            $price_t_i = 0; //увелечение
            $price_t_a_d = 0; //уменьшение
            if($price_t - $price_t_a > 0){
                $price_t_a_d = $price_t - $price_t_a;
            } else {
                $price_t_i = $price_t_a - $price_t;
            }
            $order_data["PRICE_T_I"][] = $nf($price_t_i);
            $order_data["PRICE_T_A_D"][] = $nf($price_t_a_d);

            $n_sum_i = 0;
            $n_sum_a_d = 0;
            if($b_d_price["nds"] - $a_d_price["nds"] > 0){
                $n_sum_a_d = $b_d_price["nds"] - $a_d_price["nds"];
            } else {
                $n_sum_i = $a_d_price["nds"] - $b_d_price["nds"];
            }
            $total_i = 0;
            $total_a_d = 0;
            if($b_d_price["total"] - $a_d_price["total"] > 0){
                $total_a_d = $b_d_price["total"] - $a_d_price["total"];
            } else {
                $total_i = $a_d_price["total"] - $b_d_price["total"];
            }
            $order_data["N_SUM_I"][] = $nf($n_sum_i);
            $order_data["N_SUM_A_D"][] = $nf($n_sum_a_d);
            $order_data["TOTAL_I"][] = $nf($total_i);
            $order_data["TOTAL_A_D"][] = $nf($total_a_d);

            $t_price_t_i += $price_t_i;
            $t_price_t_a_d += $price_t_a_d;
            $t_n_sum_i += $n_sum_i;
            $t_n_sum_a_d += $n_sum_a_d;
            $t_total_i += $total_i;
            $t_total_a_d += $total_a_d;

            $i++;
        }
    }

    $order_data_save = $order_data;

    if($flag_l){
        $last = $i - 2;
        foreach($order_data as $key => $val){
            $order_data_l[ $key ][] = $val[ $last ];
            unset($order_data[ $key ][ $last ]);
        }
    }

    // dd($order_data);
    // dd($order_data_l);

    $PHPWord = new PHPWord();
    $document = $PHPWord->loadTemplate($tpl);
    if($flag_s) $document->cloneRow('S', $order_data);
    if($flag_l) $document->cloneRow('L', $order_data_l);

	$document->setValue('{S}', $st);
    $document->setValue('{DOC_NUMB}', $order["numb"]);
    $doc_numb_p = $flagCorrect ? $order["c2_numb"] : $order["r_numb"];
	$document->setValue('{DOC_NUMB_P}', $doc_numb_p);
	$document->setValue('{DAY}', date('d', $date));
	$document->setValue('{MONTH}', $Month_rus[ date( "n", $date) - 1 ]);
	$document->setValue('{YEAR}', date('Y', $date));
	$document->setValue('{DAY_P}', date('d', $date_p));
	$document->setValue('{MONTH_P}', $Month_rus[ date( "n", $date_p) - 1 ]);
	$document->setValue('{YEAR_P}', date('Y', $date_p));
	$document->setValue('{OID_NAME}', $oid['name']);
	$document->setValue('{OID_INN}', $oid['inn']);
	$document->setValue('{OID_ADDRESS}', $oid['yaddress']);
	$document->setValue('{ORG_NAME}', $org["organizations"]['name']);
	$document->setValue('{ORG_INN}', $org["organizations"]['inn']);
	$document->setValue('{ORG_ADDRESS}', $org["organizations"]['yaddress']);
    $document->setValue('{CURRENCY}', 'Российский рубль, 643');
    if($oid['type'] == "INDIVIDUAL"){
		$document->setValue('{OID_KPP}', "");
	} else {
		$document->setValue('{OID_KPP}', "/".$oid['kpp'].',');
	}
	if($org["organizations"]['type'] == "INDIVIDUAL"){
		$document->setValue('{ORG_KPP}', "");
	} else {
		$document->setValue('{ORG_KPP}', "/".$org["organizations"]['kpp'].',');
	}

    $document->setValue('{DOC}', $doc);
    $document->setValue('{DOC_CHANGE}', $doc_change);

    $document->setValue('{U_HEAD}', $u_head);
	$document->setValue('{U_BUCH}', $u_buch);
	$document->setValue('{U_SHIPPED}', $u_ship);
	$document->setValue('{U_SHIPPED_POST}', $u_ship_post);

    $document->setValue('{PRICE_T_I}', $nf($t_price_t_i));
    $document->setValue('{PRICE_T_A_D}', $nf($t_price_t_a_d));
    $document->setValue('{N_SUM_I}', $nf($t_n_sum_i));
    $document->setValue('{N_SUM_A_D}', $nf($t_n_sum_a_d));
    $document->setValue('{TOTAL_I}', $nf($t_total_i));
    $document->setValue('{TOTAL_A_D}', $nf($t_total_a_d));

    $file = "files/correct_".time().".docx";

    $document->save($file);

    if (file_exists($file)) {
        $filename = basename($file);
        $size = filesize($file);
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Length: $size");
        header("Charset: UTF-8");
        header("Content-Type: application/unknown");
        readfile($file);
        unlink($file);
    }
}