<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

function get_control_result($id){
  global $db;
  $query = $db->query("SELECT c.*, o.name as oname FROM JMY_control as c LEFT JOIN JMY_supply_order as so ON(so.id = c.supply_o) LEFT JOIN JMY_organizations as o ON(so.oid = o.id) WHERE c.id = '" . $id . "'"); 	
  $order = $db->getRow($query);
  return $order;
}


function detail($id, $pay_detail = false)  
{
global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
	
	$order = get_control_result($id);
  $adminTpl->admin_head('Поверка приборов | Закупка №SN-'.$id);
  xeditableInit();
  echo '
  <input type="hidden" value="'.$id.'" id="mainId">
  <input type="hidden" value="'.$pay_detail.'" id="pay_detail">
	<section id="content" class="animated fadeIn">
		<div class="text-center">            
              <p class="lead" style="text-transform: uppercase;">Заявка на согласование №'.module_perfix('fcontrol', $id).' (счет №'.module_perfix('supply_order', $order["supply_o"]).')</p>
        </div>	
        
        
        <script src="usr/modules/fcontrol/admin/asset/js/script.js?'.time().'"></script>
        
        <div class="modal fade" id="approve" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Согласовать заявку?</h5>
              </div>
              <div class="modal-body">
                Вы хотите согласовать заявку?
              </div>
              <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-id="'.$id.'" id="approveConfirm">Продолжить</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Закрыть</button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal fade" id="noApprove" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Отправить на доработку?</h5>
              </div>
              <div class="modal-body">
              Комментарий:
                <textarea class="form-control" id="commentText" placeholder="Введите комментарий"></textarea>
              </div>
              <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-id="'.$id.'" id="noApproveConfirm">Продолжить</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Закрыть</button>
              </div>
            </div>
          </div>
        </div>
        
		<div class="row" id="admin-form">			
			<div class="panel mb25 mt5">										
        <div class="panel-body p20 pb10" id="order_head">';
          ajax_order_detail_head($id, $pay_detail);
				echo '</div>
			</div>
		</div>
        
        
        <div class="modal fade" id="commModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Комментарии</h5>
              </div>
              <div class="modal-body">';
              $comment_s = get_coment_supply($id);
              echo $comment_s;
              echo '</div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal fade" id="payCancleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Отмена оплаты</h5>
              </div>
              <form action="/administration/module/fcontrol/canclePay/'.$id.'" method="POST">
                <div class="modal-body">
                  <div>Выберите причину отмены</div>
                  <select class="form-control" name="reason">';
                    $data = pay_reason_list();
                    foreach($data as $key => $val){
                      echo '<option value="'.$key.'">'.$val.'</option>';
                    }
                  echo '</select>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Выполнить</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                </div>
              </form>
            </div>
          </div>
        </div>

		<div class="row">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">Список товаров на закупку:</span>
					<div class="widget-menu pull-right">
						<div class="btn-group">';
							$notif = "notif(\'primary\', \'".$lang['info']."\', \'".$lang['ajax_update_compl']."\');";
							echo '<button onclick="ajaxGetJS(\'{MOD_LINK}/ajax/order_detail_list/'.$id.'\', \'demoHighCharts.init(); '.$notif.'\', \'tov_list\');" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>
						</div>											
					</div>	
              </div>


              <div class="panel-body pn table-responsive"> 
				        <form id="tablesForm" method="POST" onsubmit="return false;">
                  <table class="table table-striped">
                    <thead>
						          <tr>
						          	<th class="w40"><span class="pd-l-sm"></span>#</th>
						          	<th class="w50">Артикул</th>
						          	<th class="w200">Наименование</th>							
						          	<th class="w50">Кол-во</th>
						          	<th class="w50">Ед.</th>
						          	<th class="w50 text-center" title="Наличие / Доступно / Резерв">Н/Д/Р</th>
						          	<th class="w50 text-center">Реально</th>
						          	<th class="w50">Цена продажи</th>
						          	<th class="w50">Цена закупки</th>
						          	<th class="w50">Комментарий</th>												
						          	<th class="w200 text-right">' . $lang['actions'] . '</th>
						          	<th class="w20 text-right">
						          		<div class="checkbox-custom mb15">
						          			<input id="all" type="checkbox" name="all" class="checks" onclick="setCheckboxes(\'tablesForm\', true); return true;">
						          			<label for="all"></label>
						          		</div>	
						          	</th>
						          </tr>
                    </thead>
                    <tbody id="tov_list">';
                      ajax_order_detail_list($id);
					          echo '</tbody>
				          </table> 
                </form>
			      </div>			
          </div>
        </div>
  </section>
  
  <div class="modal fade" id="payModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Оплата</h5>
      </div>
      <form action="/administration/module/fcontrol/savePayFcotrol/'.$id.'" method="POST">
      <div class="modal-body">
        <div class="form-group">
            <label for="pay_num">Номер ПП</label>
            <input type="text" class="form-control" id="pay_num" name="pay_num" placeholder="Введите платежного поручения" required>
        </div>
        <div class="form-group">
            <label for="pay_date">Дата</label>
            <input type="datetime-local" class="form-control" id="pay_date_1" name="pay_date" value="'.date("Y-m-d").'T'.date("H:i:s").'" required>
        </div>
        <div class="form-group">
            <label for="">Сумма оплаты</label>
            <input type="number" class="form-control" name="pay_total" value="" placeholder="Введите сумму оплаты, если она частичная" step="any">
        </div>';
            
            $attribute = [];
            $attribute["name"] = "";
            $attribute["script"] = true;
            $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
            $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
            echo uploaderMaster("css", $attribute);
            echo uploaderMaster("upload", $attribute);
            echo uploaderMaster("view", $attribute);
            
        echo '
      </div>
      <div class="modal-footer">
            <input type="submit" class="btn btn-success" data-id="'.$id.'" id="payConfirm" value="Сохранить">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Закрыть</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="payAddModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Добавить документы</h5>
      </div>
      <form action="/administration/module/fcontrol/saveAddPayFcotrol/'.$id.'" method="POST">
      <div class="modal-body">';
            
            $attribute = [];
            $attribute["name"] = "1";
            $attribute["script"] = true;
            $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
            $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
            echo uploaderMaster("css", $attribute);
            echo uploaderMaster("upload", $attribute);
            echo uploaderMaster("view", $attribute);
            
        echo '
      </div>
      <div class="modal-footer">
            <input type="submit" class="btn btn-success" data-id="'.$id.'" id="payAddConfirm" value="Сохранить">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Закрыть</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="dateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form id="dateModalForm" onsubmit="return false;">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
      </div>
      <div class="modal-body">
        <strong>Введите дату</strong>
        <input type="date" class="form-control" name="date">
        <input type="hidden" value="'.$id.'" name="id">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="saveDateModal();">Добавить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
      </form>
    </div>
  </div>
</div>
  
  
  ';

	$adminTpl->admin_foot();
} 

function ajax_order_detail_list($id)
{
	global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;
    
    $queryMain = $db->query("SELECT * FROM JMY_control WHERE id = ".$id);
    $r = $db->getRow($queryMain);
    
    $queryTest = $db->query("SELECT  cd.*, cd.id as cdid, c.*, c.id as catalog_id FROM ".DB_PREFIX."_control_detail as cd LEFT JOIN ".DB_PREFIX."_catalog as c ON(cd.cid = c.id) WHERE control_id = '".$id."' AND status != 0");
    
    if($db->numRows($queryTest) > 0){
        $i = 0;
        while($content = $db->getRow($queryTest))
        {
            $i++;
            if(empty($content["sdid"])){
                $price = 0;
                
            } else {
                $querySales = $db->query("SELECT * FROM ".DB_PREFIX."_sales_detail WHERE id = '".$content["sdid"]."' LIMIT 1");
                $salesGap = $db->getRow($querySales);
                $price = $salesGap["s_price"];
            }

            $unit = get_unit('data', $content['unit'] ,'sname');

            $ndr_view = get_ndr(["cid" => $content["id"], "stock_id" => "1", "module" => "sales", "pid" => null, "returnType" => "view"]);

            echo '
			<tr>
				<td data-toggle="tooltip" title="' . $content['cdid'] . '">' . $i . '</td>
				<td>' . $content['s_sku'] . '</td>
				<td class="changeGoods" id="cg_'.$content['cdid'].'">' . $content['title'] . '</td>
				<td>' . $content['count'] . '</td>				
				<td>' . $unit . '</td>		
				<td class="text-center">'.$ndr_view.'</td>		
				<td class="text-center">Не указано</td>		
				<td>' . number_format($price,2,'.',' '). ' руб.</td>	
				<td class="edit" id="id_'.$content['cdid'].'">' . number_format($content['price_now'],2,'.',' '). ' руб.</td>	

				<td>'.(!empty($content['comment']) ? '<span style="cursor: pointer;" onclick="modal_o(\'#modal-comm-'.$content['cdid'].'\')" class="fa fa fa-edit text-info fa-md"></span>
				<div id="modal-comm-'.$content['cdid'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Комментарий</span></div>
						  <div class="panel-body">
							<h3 class="mt5">Комментарий:</h3>					
							<hr class="short alt">
							<p>'.$content['comment'].'</p>
						  </div>
						  <div class="panel-footer text-right">
							
						  </div>
						</div>
					  </div>' : '<span class="fa fa fa-edit fa-md"></span>').'</td>					
				
				<td class="w200 text-right">				
					<div class="btn-group" >';
					echo   '<button type="button" onclick="modal_o(\'#modal-compl-'.$content['cdid'].'\');" class="btn btn-xs btn-primary">Изменить цену</button>
									<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
									<ul role="menu" class="dropdown-menu">											
										<li><a href="javascript:void(0);" onclick="modal_o(\'#modal-no\')">Отметить на удаление</a></li>
									</ul>';
						
					echo '				
						
					</div>					
					<div id="modal-compl-'.$content['cdid'].'" class="popup-basic admin-form mfp-with-anim mfp-hide">
						<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Отправить в счет</span></div>
									<div class="panel-body p25">																										
										<div class="section">
											Количество товаров: <br>
											<label for="price_now" class="field prepend-icon">
												<input id="price_now" type="text" name="price_now" placeholder="Укажите цену по счету" value="'.$content['price_now'].'" class="gui-input">
												<label for="price_now" class="field-icon"><i class="fa fa-link"></i></label>
											</label>
										</div>
									</div>									
									<div class="panel-footer">
										<input type="hidden" name="id" value="'.$content['cdid'].'" />										
										<input type="submit" class="button btn-success" value="Сохранить"/>
									</div>
							</div>	
					</div>		  
				</td>
				<td class="w20 text-right">
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $content['cdid'] . '" type="checkbox" name="checks[]" class="checks" value="' . $content['cdid'] . '">
						<label for="checkbox' . $content['cdid'] . '"></label>
					</div>
				</td>
			</tr>';
        }
        
    } else {
        echo '<tr><td colspan="12">У данной заявки еще не заполнен список приборов!</td></tr>';	
    }
}

function ajax_order_detail_head($id, $pay_detail = false){
  global $db;
  $rows_status = array();
	$rows_status[1] = 'Ожидает проверки';
  $rows_status[2] = 'Отправлен на доработку';
  $rows_status[3] = 'Согласован';
  
  $order = get_control_result($id);
  echo '<div class="section row mbn admin-form">
						<div class="col-md-12 pl15">
              <div class="section row mb15">	
              
								<div class="col-md-3">
									<div class="">
										<div class="hideBtn" '.($order["status"] > 1 ? 'style="display:none;"' : "").'><div class="ph10"><a id="approveBtn" class="holder-style p15 text-center holder-active"><span class="fa fa-edit holder-icon"></span><br> Согласовать<br>(отправить на оплату)</a></div></div>
										<div '.($order["status"] == 2 ? 'style="display:none;"' : "").'><div class="ph10"><a class="holder-style p15 text-center holder-active" id="noApproveBtn"><span class="fa fa-check-circle holder-icon"></span><br>Отправить на<br>доработку</a></div></div>
									</div>
								</div>

                <div class="col-md-2" '.($pay_detail == false ? 'style="display:none;"' : (($order["pay"] > 0 && $order["pay"] != 3) ? 'style="display:none;"' : "")).'>
                  <div class="element-groups-lg-custom wizard-options">
                  
                    <div class="ph10">
                      <a id="payBtn" class="holder-style p15 text-center holder-active">
                      <span class="fa fa-edit holder-icon"></span><br>Оплатить
                      </a>
                    </div>

                    <div class="ph10">
                      <a class="holder-style p15 text-center holder-active" onclick="payCancleModal();">
                      <span class="fas fa-ban holder-icon"></span><br>Отмена <br> оплаты
                      </a>
                    </div>

									</div>
                </div>
                                
                <div class="col-md-2" '.($pay_detail == false ? 'style="display:none;"' : (($order["pay"] == 1) ? '' : 'style="display:none;"')).'>
									<div class="element-groups-lg-custom wizard-options">
										<div class="ph10"><a id="payBtnAdd" class="holder-style p15 text-center holder-active"><span class="fa fa-edit holder-icon"></span><br>Добавить документ</a></div>
										<div class="ph10"><a id="payBtnCancel" class="holder-style p15 text-center holder-active"><span class="fas fa-ban holder-icon"></span><br>Отменить оплату</a></div>
									</div>
								</div>

								<div class="col-md-2">
									<small>Поставщик:</small><br/>
									<b>'.$order['oname'].'</b>&nbsp;&nbsp;				
									<a target="_blank" href="'.ADMIN.'/module/organizations/edit/'.$order['oid'].'"><span class="fa fa-external-link"></span></a>
									<br/><br/>
									<small>Ответственный:</small><br/>
									<b>'. shortUserName($order['uid']).'</b><br/><br/>
									<small>Дата создания заказа:</small><br/>
                  <b>'. (empty($order['date']) ? 'Не указано!' : date("d.m.Y H:i:s", strtotime($order['date']))).'</b><br/><br/>
                  <small>Дата оплаты:</small><br/>
                  <b><a href="#" id="plan_pay_date" data-type="date" data-pk="'.$id.'" data-url="/ajax.php?do=setPlanPayDateControl" data-title="Введите дату оплаты">'. (empty($order['plan_pay_date']) ? 'Не указано!' : date("d.m.Y", strtotime($order['plan_pay_date']))).'</a></b>
								</div>
								<div class="col-md-2">
                                    <small>Статус:</small><br/>
									<b>'.  $rows_status[$order['status']] .'</b><br/><br/>
									<small>Документы по счету:</small><br/>';
									
									$attach = $db->query("SELECT * FROM JMY_attach WHERE `module` = 'supply' AND pid = ".$order["supply_o"]);
                                    if($db->numRows($attach) > 0){
                                    echo '<ul>';
									while($ra = $db->getRow($attach)){
                                        echo '<li><a target="_blank" href="/download/'.$ra["id"].'">'.$ra["name"].'</a></li>';
                                    }
                                    echo '</ul>';
                                    } else {
                                        echo '<b>Отсутствует</b>';
                                    }	
									
									
									echo '<br/><br/>
									<small>Привязанные продажи:</small><br/>
									<b>';									
									$query_sales = $db->query("SELECT DISTINCT sl.id, sl.numb, sl.date, sl.object, o.name FROM ".DB_PREFIX."_supply_order_detail as sod LEFT JOIN ".DB_PREFIX."_sales_detail as s on(sod.sale_detail_id=s.id) LEFT JOIN ".DB_PREFIX."_sales AS sl on(s.sales_id=sl.id) LEFT JOIN ".DB_PREFIX."_object AS o on(o.id=sl.object) WHERE sod.supply_o = '" . $order['supply_o'] . "'"); 	
									if($db->numRows($query_sales) > 0)
									{	
										while($qs = $db->getRow($query_sales)) 
										{
											$qs_object = '';
											if (!empty($qs['name']))
											{
												$qs_object = ' - <a href="'.ADMIN.'/object/view/'.$qs['object']. '">'.$qs['name'].'</a>';
											}
											echo '<a href="'.ADMIN.'/module/sales/order/'.$qs['id']. '">'.$qs['numb'].'</a> '.$qs_object.'<br>';
										}
									}								
						echo'		</b>													
								</div>
                                
                                <div class="col-md-2">
									<small>Статус оплаты:</small><br/>
                  <b>';

                  echo pay_status_view($order["pay"], $order["reason"]);

                  echo '</b>				
									<br/><br/>
									<small>Номер / дата счета:</small><br/>
									<b>'.($order["pay"] == 0 ? "Отсутствует" : $order["pay_num"].' / <a href="#" id="pay_date" data-type="date" data-pk="'.$id.'" data-url="/ajax.php?do=setPlanPayDateControl" data-title="Введите дату счёта">'.date("d.m.Y", strtotime($order["pay_date"]))).'</a></b><br/><br/>
									<small>Комментарий:</small><br/>';
                                    
                  $comment_s = get_coment_supply($id);
                  if(empty($comment_s)){
                    $flag = false;
                  } else {
                    $flag = true;
                  }
                                    
                                    echo '<b>'.($flag ? "<a onclick=$('#commModal').modal('show'); href=javascript:void(0); id=comm>Комментарий</a>" : "Отсутствует").'</b>';
                                   echo '</div>
                                <div class="col-md-3">
								<small>Прикрепленные файлы:</small><br/>';
                                    $attach = $db->query("SELECT * FROM JMY_attach WHERE `module` = 'pay' AND pid = ".$id);
                                    if($db->numRows($attach) > 0){
                                    echo '<ul>';
									while($ra = $db->getRow($attach)){
                                        echo '<li><a target="_blank" href="/download/'.$ra["id"].'">'.$ra["name"].'</a></li>';
                                    }
                                    echo '</ul>';
                                    } else {
                                        echo '<b>Отсутствует</b>';
                                    }	

                $ca = new CommentsAdmin;
                $comm = $ca->find(["where" => "module = 'supply_order_control' AND module_id = '".$order["supply_o"]."'"]);
                if(count($comm) > 0){
                  echo '<div style="border: 3px red dashed; padding: 5px;" class="mt-10">';
                  foreach($comm as $val){
                      echo '<div style="padding-bottom: 5px; border-bottom: 1px grey solid;">
                      <div><b>'.shortUserName($val["uid"]).' '.date("d.m.Y", strtotime($val["date"])).'</b></div>
                      <div>'.$val["text"].'</div>
                      </div>';
                  }
                  echo '</div>';
                }
                

                echo '</div>';
                if($pay_detail){
                  ?>
                  <div class="col-md-3 mt-5 p-2" style="border: 1px black solid">
                    <strong class="mb-2">Даты частичной оплаты</strong>
                    <div class="cp" onclick="addDateModal();">Добавить дату <i class="fas fa-plus text-success"></i></div>
                    <?php
                      $query = $db->query("SELECT * FROM `".DB_PREFIX."_control_payment` WHERE control_id = '".$id."'");
                      while($r = $db->getRow($query)){
                        echo '<div style="margin-bottom: 5px;border-bottom: 1px grey solid;">'.date("d.m.Y", strtotime($r["date"])).' <i class="fas fa-times text-danger cp" onclick="delDateModal('.$r["id"].');"></i></div>';
                      }
                    ?>
                  </div>
                <?php
                }
							echo '</div>												
						</div>
          </div>';
}

function get_coment_supply($id){
  global $db;
  $order = get_control_result($id);
  $commQuery = $db->query("SELECT * FROM `".DB_PREFIX."_control` WHERE supply_o = ".$order["supply_o"]);
  $str = "";
  
  while($pr = $db->getRow($commQuery)){
      if(!empty($pr["comment"])){
          $str .= '<div>'.$pr["comment"].'</div>';
      }
  }
  return $str;
}

function save_date_payment($request){
  global $db, $core;
  if(empty($request["id"])){
    echo 'Нет id';
    exit;
  }
  if(empty($request["date"])){
    echo 'Нет даты';
    exit;
  }
  $uid = $core->auth->user_info['id'];
  $db->query("INSERT INTO `".DB_PREFIX."_control_payment` (control_id, date, uid) VALUES ('".$request["id"]."', '".$request["date"]."', '".$uid."')");
}

function del_date_payment($request){
  global $db, $core;
  if(!empty($request["id"])){
    $db->query("DELETE FROM `".DB_PREFIX."_control_payment` WHERE id = '".$request["id"]."' LIMIT 1");
  }
}