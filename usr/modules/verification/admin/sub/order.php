<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}


function order($id) 
{
global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;

	$dn_array= array();
	$dn_array[0] = 'Нет';
	$dn_array[1] = 'Ду 15';
	$dn_array[2] = 'Ду 20';
	$dn_array[3] = 'Ду 25';
	$dn_array[4] = 'Ду 32';
	$dn_array[5] = 'Ду 40';
	$dn_array[6] = 'Ду 50';
	$dn_array[7] = 'Ду 65';
	$dn_array[8] = 'Ду 80';
	$dn_array[9] = 'Ду 100';
	$dn_array[10] = 'Ду 125';
	$dn_array[11] = 'Ду 150';
	$dn_array[12] = 'Ду 200';
	$dn_array[13] = 'Ду 10';
	
	$query = $db->query("SELECT d.*, o.name as oname  FROM ".DB_PREFIX."_ver as d LEFT JOIN ".DB_PREFIX."_organizations AS o on(d.oid=o.id)  WHERE d.id = '" . $id . "'");
	$order = $db->getRow($query);	
	$query_rel = $db->query("SELECT pid_in FROM ".DB_PREFIX."_relations WHERE module_in = 'reestr' AND module_out = 'verification' AND pid_out = '".$id."' LIMIT 1");
	if($db->numRows($query_rel) > 0) 
	{
		$rel = $db->getRow($query_rel);
		$query_doc = $db->query("SELECT * FROM ".DB_PREFIX."_reestr WHERE id = '".$rel['pid_in']."'");
		$doc = $db->getRow($query_doc);
		$dogovor = '№ ПТТ-'.$doc['numb'].' от '.date('d.m.Y', $doc['date']);
		$dogovorId = $doc['id'];
		$d_flag=true;
	}
	else
	{
		$dogovor = 'Договор еще не создан';
		$dogovorId = '';
		$d_flag=false;
	}	
	$adminTpl->admin_head('Поверка приборов | НАРЯД-ЗАКАЗ №ПР-'.$id);
	$notif_add = "notif(\'success\', \'".$lang['info']."\', \'Прибор успешно добавлен\');";
    xeditableInit();
    $adminTpl->footIncludes[] = '<script src="/usr/modules/verification/admin/assets/js/script.js"></script>';
	$adminTpl->js_code[] ='	$("#add_device").submit(function() { 
										var form_data = $(this).serialize(); 
										$.ajax({
										type: "POST", 
										url: "'.ADMIN.'/module/verification/ajax/order_divece_add/'.$id.'", 
										data: form_data,
										success: function() {	
												$("#modal-add").magnificPopup(\'close\');
												notif("success","'.$lang['info'].'", "Прибор успешно добавлен");
												ajaxGet("'.ADMIN.'/module/verification/ajax/order_divece_list/'.$id.'", "dev_list");
												document.getElementById("name").value = "";
												document.getElementById("price").value = "";
												document.getElementById("status").value = "1";
												
										}
										});
								});';								
	$adminTpl->js_code[] = '$("#points").spinner();
	  $(".select2-single").select2();
		$(\'#qrcode\').qrcode({width: 186,height: 186,text: "'.$config['url'].'/verification/view/'.md5($order['date']+$order['id']).'"});';
	echo '
	<section id="content" class="animated fadeIn">
		<div class="text-center">            
              <p class="lead">НАРЯД-ЗАКАЗ №ПР-'.$id.'</p>
        </div>	
		<div id="modal-zn" class="popup-basic bg-none mfp-with-anim mfp-hide">
            <div class="panel">
              <div class="panel-heading"><span class="panel-icon"><i class="fa fa-pencil"></i></span><span class="panel-title">Наряд заказ</span></div>';
			  if ($d_flag)
			  {
				echo '<div class="panel-body">
					<h3 class="mt5">Заказ наряд</h3>
					<p>Заказ-наряд на заявку успешно создан, вы можете скачать его в формате doc или роспечать.</p>              
				  </div>
				  <div class="panel-footer text-right">
					<a target="_blank" href="'.ADMIN.'/module/verification/nz_create/'.$id.'" class="btn btn-success">Скачать</a>
					 <button type="button" class="btn btn-info">Распечатать</button>
				  </div>';
			  }
			  else
			  {
				echo '<div class="panel-body">
					<h3 class="mt5">Наряд заказ</h3>
					<p>Чтобы создать наряд-заказ сначала необходимо создать договор по данной заявке. Внимание! Если вы создадите наряд-заказ дальнейшее изменение заявки будет невозможно!</p>              
				  </div>
				  <div class="panel-footer text-right">
					 <button type="button" class="btn btn-info">Закрыть</button>
				  </div>';  
			  }
          echo'</div>
          </div>
		 <div id="modal-doc" class="popup-basic bg-none mfp-with-anim mfp-hide">
            <div class="panel">
              <div class="panel-heading"><span class="panel-icon"><i class="fa fa-pencil"></i></span><span class="panel-title">Договор с контрагентом</span></div>
			
				';
				if ($d_flag)
				{
				 echo ' <div class="panel-body">
					<h3 class="mt5">Договор с контрагентом</h3>
					<p>Договор с контрагентом по данной заявку успешно создан, вы можете скачать его в формате doc или роспечать.</p>              
				  </div><div class="panel-footer text-right">
					<a target="_blank" href="'.ADMIN.'/module/verification/doc_create/'.$id.'" class="btn btn-success">Скачать</a>
					 <button type="button" class="btn btn-info">Распечатать</button>
				  </div>';
				}  
				else
				{
					 echo ' <div class="panel-body">
					<h3 class="mt5">Договор с контрагентом</h3>
					<p>Договор с контрагентом еще не создан! Чтобы создать нажмите на кнопку.</p>              
				  </div><div class="panel-footer text-right">
					<a href="'.ADMIN.'/module/verification/doc_create/'.$id.'" class="btn btn-success">Создать</a>
				  </div>';
				  // onclick="location.reload();"
				}
			  
				  
			echo'	</div>
          </div>
		<div class="row" id="admin-form">			
			<div class="panel mb25 mt5">										
				<div class="panel-body p20 pb10">									
					<div class="section row mbn admin-form">
						<div class="col-md-9 pl15">
							<div class="section row mb15">
									<div class="col-md-4">
										<div class="panel br-a">
											<div class="panel-heading" style="padding: 7px; line-height: 29px;"><span class="panel-title" style="font-size: 18px;">Меню</span></div>
											<div class="panel-body panel-scroller scroller-xs scroller-pn pn">
												<table class="table mbn tc-med-1 table-hover">
													<thead>
													  <tr class="hidden">
														<th>#</th>														
													  </tr>
													</thead>
													<tbody>
														<tr>
															<td style="cursor: pointer;" onclick="modal_o(\'#modal-zn\');"><span style="top: 0px;" class="glyphicon glyphicon-list-alt mr5 va-m text-warning"></span>Заказ-наряд</td>
														</tr>
														<tr>
															<td style="cursor: pointer;" onclick="modal_o(\'#modal-doc\');"><span style="font-size: 16px;" class="fa fa-file-text-o mr5 va-m text-success"></span>Договор с контрагентом</td>														
														</tr>
													  <tr>
														<td><span style="font-size: 16px;" class="fa fa-check-square-o mr5 va-m text-info"></span>Создать акт</td>
													
													  </tr>         
													  <tr>
														<td><span class="flag-xs flag-fr mr5 va-b"></span>Отменить заявку</td>
													
													  </tr>  
													</tbody>
												</table>
											</div>
										</div>
									</div>
									<div class="col-md-4">
											<small>Контрагент:</small><br/>
												<b>'.$order['oname'].'</b>
                                                <a target="_blank" href="'.ADMIN.'/module/organizations/'.$order['oid'].'/edit"><span class="fa fa-external-link"></span></a>
                                                <br/><br/>
												<small>Ответственный:</small><br/>
												<b>'. shortUserName($order['uid']).'</b><br/><br/>
												<small>Договор:</small><br/>												
												<b>'. $dogovor .'</b>  	
												<a target="_blank" href="'.ADMIN.'/module/reestr/view/'.$dogovorId.'"><span class="fa fa-external-link"></span></a>
									</div>
									<div class="col-md-4">
												<small>Дата подачи заявки:</small><br/>
												<b>'. formatdate($order['date'], false).'</b><br/><br/>
												<small>Дата завершения заявки:</small><br/>
												<b>'. ($order['date_end'] == '0' ? 'Заявка еще не завершена!' : formatdate($order['date_end'], false)).'</b><br/><br/>
                                                <small>Комментарий:</small><br/>
												<b style="cursor: pointer;" id="comment" data-type="textarea" data-pk="'.$id.'">'.$order['comment'].'</b><br/><br/>
									</div>
							</div>												
							</div>
							<div class="col-md-3 text-right">
								<a href="'.$config['url'].'/verification/view/'.md5($order['date']+$order['id']).'" target="_blank"><div id="qrcode"></div></a>
							</div>								
							</div>
						</div>
					
					</div>
				</div>
	<script type="text/javascript" language="javascript">
	
		function ajax_send(name, str_url) {
			var msg   = $("#"+name).serialize();
			$.ajax({
				type: "POST",
				url: str_url,
				data: msg,
				success: function(data) {
					$("#modal-"+name).magnificPopup(\'close\');
					notif("success","'.$lang['info'].'", data);
					ajaxGet("'.ADMIN.'/module/verification/ajax/order_divece_list/'.$id.'", "dev_list");					
				}          
			});
		}
	</script>
	<div class="row">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">Список приборов:</span>
					<div class="widget-menu pull-right" >
						<div class="btn-group">';
							$notif = "notif(\'primary\', \'".$lang['info']."\', \'".$lang['ajax_update_compl']."\');";
							echo '<button onclick="ajaxGetJS(\'{MOD_LINK}/ajax/order_divece_list/'.$id.'\', \'demoHighCharts.init(); '.$notif.'\', \'dev_list\');" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>
						</div>						
						<div class="btn-group">		
							<button '.($order['status'] != '1' ? 'disabled' : '').' onclick="modal_o(\'#modal-add\');" type="button" class="btn btn-xs btn-success btn-block">Добавить прибор</button>
						</div> 
						<div id="modal-add" class="popup-basic admin-form mfp-with-anim mfp-hide">							
							<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Добавить прибор</span></div>
								<form id="add_device" action="javascript:void(null);">
									<div class="panel-body p25">
										<div class="section">
											<label for="toid" class="field prepend-icon">
												<select name="vdid" class="select2-single form-control" id="vdid">
												<option value="0" disabled selected>Выберите тип прибора</option>;
                                                <option value="-1">Сервис</option>';
												$query_users_list = $db->query("SELECT * FROM ".DB_PREFIX."_ver_device ORDER BY name ASC");
												if($db->numRows($query_users_list) > 0) 
												{
													while($users_list = $db->getRow($query_users_list)) 
													{
														// $selected = ($users_list['id'] == $toid) ? "selected" : "";
														echo '<option value="'.$users_list['id'].'" >'.$users_list['name'].'</option>';
													}
												}																
												echo'	</select>	
												<label for="translit" class="field-icon"><i class="fa fa-tasks"></i></label>												
											</label>
										</div>
										<div class="section hid">
											<label for="text" class="field prepend-icon">
											    <input id="sum" name="sum" type="text" placeholder="Укажите количество" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-pencil"></i></label>
											</label>
										</div>	
                                        <div class="section hidm" hidden>
											<label for="text" class="field prepend-icon">
											    <input id="price2" name="price2" type="text" placeholder="Цена" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-money"></i></label>
											</label>
										</div>
                                        <div class="section hidm" hidden>
											<label for="comment" class="field prepend-icon">
                                            <textarea id="comment" name="comment" placeholder="Введите комментарий" class="gui-textarea"></textarea>
                                            <label for="comment" class="field-icon"><i class="fa fa-comments"></i></label>
										</label>
										</div>
										<div class="section hid">
											<label for="text" class="field prepend-icon">
											    <input id="model" name="model" type="text" placeholder="Модель прибора" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
											</label>
										</div>		
										<div class="section hid">
											<label for="text" class="field prepend-icon">
											    <input id="number" name="number" type="text" placeholder="Заводской номер" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-shield"></i></label>
											</label>
										</div>	                                                                                                             
										<div class="section hid">
											<label class="field prepend-icon select">											
									<select class="form-control" name="dn" id="dn">
									<option value="0" disabled selected>Выберите диаметр</option>';
										if(isset($dn_array))
										foreach ($dn_array as $key => $value) {
							
												echo '<option value="'.$key.'" >'.$value.'</option>';				
										}		
								echo '</select><i class="arrow double"></i>
								
								</label>
										</div>									
									</div>									
									<div class="panel-footer">
										<input type="hidden" name="id" value="'.$id.'" />
									  <input type="submit" class="button btn-primary" value="Добавить прибор" />
									</div>
								</form>
							</div>
						</div>						
					</div>	
              </div>
			  <script type="text/javascript">ajaxGet(\'{MOD_LINK}/ajax/order_divece_list/'.$id.'\', \'dev_list\');</script>
              <div class="panel-body pn table-responsive"> 
				
                  <table class="table table-striped" id="tablesForm">
                    <thead>
						<tr>
							<th class="w40"><span class="pd-l-sm"></span>#</th>
							<th class="w200">Прибор</th>
							<th class="w150">Модель</th>
							<th class="w150">Номер</th>							
							<th class="w150">Ду</th>
							<th class="w150">Кол-во</th>
							<th class="w200">Цена</th>
							<th class="w200">Сумма</th>
							<th class="w40 text-center"><span class="fa fa-pencil fa-lg"></span></th>		
							<th class="w40 text-center">' . $lang['status'] .'</th>						
							<th class="w200 text-right">' . $lang['actions'] . '</th>
							<th class="w20 text-right">
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
									<label for="all"></label>
								</div>	
							</th>
						</tr>
                    </thead>
                    <tbody id="dev_list">					
					</tbody>
				<tfoot class="footer-menu">
                    <tr align="right">                    
					  <td align="right" colspan="12">
                        <nav align="right" class="text-right">						
							<select style="width: 250px; display: inline-block;" class="form-control" name="act">							
								<option value="activate">Приборы прошли проверку</option>
								<option value="deActivate">Приборы не прошли проверку</option>								
							</select>
							<input name="submit" type="button" class="btn btn-success" style="display: inline-block;" id="sub" value="' .  $lang['doit'] . '" /><span class="pd-l-sm"></span>							
						 </nav>
                      </td>					 
                    </tr>
                  </tfoot> 
				</table> 
			 </div>			
          </div>
        </div>
	</section>';
      $adminTpl->js_code[] = '$("#vdid").change(function(){
                  if($("#vdid").val() == -1){
                      $(".hidm").show();
                      $(".hid").hide();
                  } else {
                      $(".hid").show();
                      $(".hidm").hide();
                  }
              });
              $("#sub").click(function(){
                  let mas = [];
                  let i = 0;
                  let str = "";
                  $("input:checked").each(function(){
                      console.log(this.value);
                      if(this.value != 1){
                        mas.push(this.value);
                        str += i + "=" + this.value + "&"; 
                        i++;
                      }
                  });
                  str.slice(0, -1);
                  $.ajax({
                            type: "POST",
                            url: \''.ADMIN.'/module/verification/ajax/order_device_check\',
                            data: str,
                            success: function(data) {
                                $("#modal-"+name).magnificPopup(\'close\');
                                notif("success","'.$lang['info'].'", data);
                                ajaxGet("'.ADMIN.'/module/verification/ajax/order_divece_list/'.$id.'", "dev_list");					
                                }          
                        });
              });';
	$adminTpl->admin_foot();	
  
} 

function ajax_device_check()
{
    global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;
    $masCheck = $_POST;
    $masCheck2 = [];
    foreach($masCheck as $value){
        if(!empty($value)) array_push($masCheck2, $value);
    }
    $strRes = implode(',', $masCheck2);
    $db->query("UPDATE `" . DB_PREFIX . "_ver_detail` SET `status` = '2' WHERE `id` IN (" . $strRes . ")");
}

function nz_create($id = 0)
{	
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang, $dn_array;	
	if (!empty($id))
	{
		$query_static = $db->query("SELECT d.*, o.name as oname, o.yaddress, o.phone, o.email  FROM ".DB_PREFIX."_ver as d LEFT JOIN ".DB_PREFIX."_organizations AS o on(d.oid=o.id)  WHERE d.id = '" . $id . "'");
		$order = $db->getRow($query_static);
		$date = $order['date'];
		$query_rel = $db->query("SELECT pid_in FROM ".DB_PREFIX."_relations WHERE module_in = 'reestr' AND module_out = 'verification' AND pid_out = '".$id."' LIMIT 1");
		if($db->numRows($query_rel) > 0) 
		{
			$rel = $db->getRow($query_rel);
			$query_doc = $db->query("SELECT * FROM ".DB_PREFIX."_reestr WHERE id = '".$rel['pid_in']."'");
			$doc = $db->getRow($query_doc);
			$dogovor = '№ ПТТ-'.$doc['numb'].' от '.date('d.m.Y', $doc['date']);
			$d_flag=true;
		}
		else
		{
			$dogovor = 'Договор еще не создан';
			$d_flag=false;
		}
		$total = 0;	
		$order_data = array(
				'n' => array(),
				'stable_name' => array(),
				's' => array(),
				'model' => array(),
				'dn' => array(),
				'number' => array(),
				'price' => array()
			);	
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_ver_detail WHERE vid = '".$id."' AND status != '0'");		
		if($db->numRows($query) > 0) 
		{
			$i = 0;
			while($catalog = $db->getRow($query)) 
			{	
				$i++;
				$stable_name = $catalog['stable_name'];
				$sum = $catalog['sum'];
				$model = $catalog['model'];
				$dn = $dn_array[$catalog['dn']];
				$number = $catalog['number'];
				$price = $catalog['stable_price']*$sum;
				$total = $total+$price;				
				array_push($order_data['n'], $i);
				array_push($order_data['stable_name'], $stable_name);
				array_push($order_data['s'], $sum);
				array_push($order_data['model'], $model);
				array_push($order_data['dn'], $dn);
				array_push($order_data['number'], $number);
				array_push($order_data['price'], $price);	
			}
		}
		
		require_once(ROOT.'usr/plugins/PHPWord.php');
		$PHPWord = new PHPWord();
		$document = $PHPWord->loadTemplate(ROOT.'/files/tpl/ver_nz.docx');
		$Month_rus = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];			
			
		$document->setValue('{number}','№ПР-'.$id);
			
		$document->setValue('{d}', date('d', $date));
		$document->setValue('{m}',  date('m', $date));
		$document->setValue('{year}', date('Y', $date));
			
		$document->setValue('{org_name}', $order['oname']);
		$document->setValue('{org_address}', $order['yaddress']);
		$document->setValue('{org_phone}', $order['phone']);
		$document->setValue('{org_email}', $order['email']);
		
		$document->setValue('{total}', $total);			
		$document->setValue('{dogovor}', $dogovor);
				
		$document->cloneRow('o', $order_data);
		
		$document->save(ROOT.'files/reestr/'.$rel['pid_in'].'/8-order-'.$id.'-'.$doc['date'].'.docx');
		$db->query("UPDATE `" . DB_PREFIX . "_ver` SET `status` = '2' WHERE `id` = " . $id . " LIMIT 1 ;");		
		header('Location: https://atri-energo.ru/files/reestr/'.$rel['pid_in'].'/8-order-'.$id.'-'.$doc['date'].'.docx');

		
	}
	else
	{
		header('Location: /');
	}
}


function doc_create($id = 0)
{	
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang, $adminTpl;	
	if (!empty($id))
	{			
		$uid = $core->auth->user_info['id'];		
		$query = $db->query("SELECT pid_in FROM ".DB_PREFIX."_relations WHERE module_in = 'reestr' AND module_out = 'verification' AND pid_out = '".$id."' LIMIT 1");		
		if($db->numRows($query) <= 0) 
		{
			
			$query_static = $db->query("SELECT * FROM ".DB_PREFIX."_ver WHERE id = '" . $id . "'");
			$order = $db->getRow($query_static);
			$date = $order['date'];
			$query_n = $db->query("SELECT numb FROM ".DB_PREFIX."_reestr WHERE org = '2' ORDER BY id DESC LIMIT 1");		
			$rn = $db->getRow($query_n);
			$numb = $rn['numb']+1;
			
			parseCertById($order['oid']);
			//echo 'gogo';
			//exit;
			$query_bank = $db->query("SELECT * FROM ".DB_PREFIX."_organizations_bank WHERE oid = '".$order['oid']."' LIMIT 1");
            $query_org = $db->query("SELECT * FROM ".DB_PREFIX."_organizations WHERE id = '".$order['oid']."' LIMIT 1");		
			$org = $db->getRow($query_org);
			
			if($db->numRows($query_bank) > 0 && isset($org['inn'])) 
			{	
				//if(true)
				if($db->query("INSERT INTO `" . DB_PREFIX . "_reestr` ( `id` , `type` , `numb` ,  `uid` , `oid` , `org` , `date` , `date_view` , `date_create` , `status`) VALUES (NULL, '8', '" . $numb . "', '" . $uid . "', '" . $order['oid'] . "', '2', '" . $date . "', '" . date('Y-m-d', $date) . "', '" . time() . "', '1');"))				
				{
				
					$rid = $db->insert_id();
					$db->query("INSERT INTO `" . DB_PREFIX . "_relations` ( `id` , `module_out` , `pid_out` ,  `module_in` , `pid_in` , `date` , `uid` , `status`) VALUES (NULL, 'verification', '" . $id . "', 'reestr', '" . $rid . "', '". time() ."', '" . $uid . "', '1');");
					require_once(ROOT.'usr/plugins/PHPWord.php');
					$PHPWord = new PHPWord();
					$document = $PHPWord->loadTemplate(ROOT.'/files/tpl/ver_doc.docx');
					$Month_rus = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
					
										
					
					$bank = $db->getRow($query_bank);	
					$org['post'] = mb_ucfirst(mb_strtolower($org['post']));
					
					switch($org['post']) {
						default:
							$post=$org['post'];
						break;				
						case "Директор":
							$post='директора';
						break;
						case "Генеральный директор":
							$post='генерального директора';
						break;
					}
					if (empty($org['dey']))
					{
						$dey = 'устава';
					}
					$dey = $org['dey'];
					if ($dey == 'Устав')
					{
							$dey = 'устава';
					}
					elseif($dey == 'Устава')
					{
						$dey = 'устава';
					}	
					
					// if (empty($org['director_detail']))
					// {
					// 	$class_path = ROOT.'boot/sub_classes/dadata.class.php';
					// 	if (file_exists($class_path))
					// 	{
					// 	require_once($class_path);
					// 	}
					// 	$dadata = new dadata();
					// 	$result = $dadata->clean('name', $org['director']);
					// 	$db->query("UPDATE `" . DB_PREFIX . "_organizations` SET `director_detail` = '". $db->safesql(json_encode($result, JSON_UNESCAPED_UNICODE)) ."' WHERE `id` = " . $order['oid'] . " LIMIT 1 ;");	
					// 	$result_d = json_decode(json_encode($result, JSON_UNESCAPED_UNICODE));						
					// }
					// else
					// {
					// 	$result_d = json_decode($org['director_detail']);	
					// }

					$result_d = getDirectorDetail($org, $order);
				
				
					$result_s = mb_substr($result_d->name, 0, 1) . '. ';
					$result_s .= mb_substr($result_d->patronymic, 0, 1) . '. ';
					$result_s .=  $result_d->surname;
						
						
					$document->setValue('{doc_numb}', $numb);
					$document->setValue('{post}', $post);
                    if($org['type'] == "INDIVIDUAL"){
                        $document->setValue('{post_i}', "Индивидуальный предприниматель");
                    } else {
                        $document->setValue('{post_i}', $org['post']);
                    }
					$document->setValue('{director}', $result_d->result_genitive);
					$document->setValue('{director_short}', $result_s);
					$document->setValue('{dey}', $dey);
					
					$document->setValue('{day}', date('d', $date));
					$document->setValue('{month}', $Month_rus[ date( "n", $date) - 1 ]);
					$document->setValue('{year}', date('Y', $date));
					
					$document->setValue('{org_name}', $org['name']);
					$document->setValue('{org_inn}', $org['inn']);
					$document->setValue('{org_address}', $org['yaddress']);
					if($org['type'] == "INDIVIDUAL"){
                        $document->setValue('{org_kpp}', "");
                    } else {
                        $document->setValue('{org_kpp}', "КПП: ".$org['kpp']);
                    }
					
					$document->setValue('{org_bank}', $bank['name_short']);
					$document->setValue('{org_bik}', $bank['bik']);
					$document->setValue('{org_rs}', $bank['rschet']);
					$document->setValue('{org_ks}', $bank['kschet']);
					$dir = ROOT.'files/reestr/'.$rid;
					if (!file_exists($dir))
					{
						mkdir($dir, 0777);						
					}
					$file_url= 'files/reestr/'.$rid.'/8-'.$numb.'-'.$date.'.docx';
					$document->save(ROOT.$file_url);	
					$db->query("INSERT INTO `" . DB_PREFIX . "_attach` ( `id` , `name` , `url` ,  `pid` , `module` , `access`) VALUES (NULL, 'Договор №".$numb."', '" . $file_url . "', '" . $rid . "', 'reestr', '0');");						
					header('Location: https://atri-energo.ru/'.$file_url);							
				}
			}
			else
			{
				$adminTpl->admin_head('Ошибка создания договора!');
				echo '<div id="content" class="animated fadeIn">';	
				$adminTpl->info('Для составления договора у контрагента заполнены не все поля!', 'error', null, $lang['error'], 'Редактировать контрагента', ADMIN.'/module/organizations/edit/'.$order['oid']);
				echo '</div>';	
				$adminTpl->admin_foot();
			}			
		}
		else
		{
			$rel = $db->getRow($query);
			$query_doc = $db->query("SELECT * FROM ".DB_PREFIX."_reestr WHERE id = '".$rel['pid_in']."'");
			$doc = $db->getRow($query_doc);
			header('Location: https://atri-energo.ru/files/reestr/'.$rel['pid_in'].'/8-'.$doc['numb'].'-'.$doc['date'].'.docx');
		}	
	}		
}


function ajax_order_device_list($vid)
{
	global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;
	$page = init_page();
	$cut = ($page-1)*$admin_conf['num'];	
	$query = $db->query("SELECT s.*, d.name, p.price FROM ".DB_PREFIX."_ver_detail as s LEFT JOIN ".DB_PREFIX."_ver_device AS d on(d.id=s.vdid) LEFT JOIN ".DB_PREFIX."_ver_device_price as p on(d.id=p.vdid) WHERE s.vid = '".$vid."' AND s.status != '0'");
	$query_ver = $db->query("SELECT status, total FROM ".DB_PREFIX."_ver WHERE id = '". $vid."' LIMIT 1");
	$ver = $db->getRow($query_ver); 
	echo '	<div id="modal-no" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Внимание!</span></div>						  
						  <div class="panel-body">
							<h3 class="mt5">Действие наивозможно!</h3>					
							<hr class="short alt">
							<p>По данной заявки уже был сформирована наряд-заказ дальнейшее изменение невозможно, обратитесь к администратору!</p>
						  </div>					 
						</div>
					  </div>';
	if($db->numRows($query) > 0)
	{	
	$i=0;
	$total = 0;
    $flag = true;
	while($content = $db->getRow($query)) 
		{
			$i++;
			
			
			if ($content['status'] == 1)
			{
				//print_r($content['status']);
				$status_icon = '<span class="fa fa-clock-o text-warning fa-md" data-toggle="tooltip" title="Прибор проверяется"></span>';
				$flag = false;
			}
			elseif ($content['status'] == 2)
			{
				$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
			}			
			elseif ($content['status'] == 3)
			{
				$status_icon = '<span class="fa fa-circle-o text-danger fa-md"></span>';
			}
			$total = $total + ($content['sum']*$content['stable_price']);
			echo '
			<tr>
				<td data-toggle="tooltip" title="' . $content['id'] . '"><span class="pd-l-sm"></span>' . $i . '</td>
				<td title="'.$content['comment'].'">' . $content['stable_name'] . '</td>
				<td>' . $content['model'] . '</td>
				<td>' . $content['number'] . '</td>				
				<td>' . (isset($dn_array[$content['dn']]) ? $dn_array[$content['dn']] : '') . '</td>
				<td>' . $content['sum']. '</td>
				<td>' . $content['stable_price']. ' рублей.</td>		
				<td>' . $content['sum']*$content['stable_price']. ' рублей.</td>	
				<td>'.(!empty($content['comment']) ? '<span style="cursor: pointer;" onclick="modal_o(\'#modal-comm-'.$content['id'].'\')" class="fa fa fa-edit text-info fa-md"></span>
				<div id="modal-comm-'.$content['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
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
				<td class="text-center">' . $status_icon . '</td>
				<td class="w200 text-right">				
					<div class="btn-group" >
						<button type="button" '.(($content['status'] != 1) && ($content['vdid'] != -1) ? 'disabled' : '').' onclick="modal_o(\'#modal-compl-'.$content['id'].'\')" class="btn btn-xs btn-primary">Выполнить</button>
						<button type="button" '.(($content['status'] != 1) && ($content['vdid'] != -1) ? 'disabled' : '').' data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
						';
						if ($ver['status'] == '2')
						{
						echo '<ul role="menu" class="dropdown-menu">	
							<li><a disabled onclick="modal_o(\'#modal-no\')" href="javascript:void(0);">Изменить</a></li>  																			
							<li class="divider"></li>
							<li><a href="javascript:void(0);" onclick="modal_o(\'#modal-no\')">Отметить на удаление</a></li>
						</ul>';
						}
						else
						{
							echo '<ul role="menu" class="dropdown-menu">	
							<li><a onclick="modal_o(\'#modal-edit-'.$content['id'].'\')" href="javascript:void(0);">Изменить</a></li>  																			
							<li class="divider"></li>
							<li><a href="javascript:void(0);" onclick="modal_o(\'#modal-form-'.$content['id'].'\')">Отметить на удаление</a></li>
						</ul>';
						}
						echo '
					</div>
					
					<div id="modal-compl-'.$content['id'].'" class="popup-basic admin-form mfp-with-anim mfp-hide">						
						<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Выполнение проверки</span></div>
								<form id="compl-'.$content['id'].'" action="javascript:void(null);" onsubmit="ajax_send(\'compl-'.$content['id'].'\', \''.ADMIN.'/module/verification/ajax/order_divece_compl\');">
									<div class="panel-body p25">
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <textarea id="comment" name="comment" class="gui-textarea" placeholder="Комментарий к прибору"/></textarea>
												<label for="text" class="field-icon"><i class="fa fa-comments"></i></label>
											</label>
										</div>																		
										<div class="section">
											<label class="col-sm-4 control-label">Прибор прошел проверку?</label>
											<div class="col-sm-4">
												'.radio("result", '1').'														
											</div>
										</div>
									</div>									
									<div class="panel-footer">
										<input type="hidden" name="id" value="'.$content['id'].'" />
										<input type="submit" class="button btn-primary" value="Выполнить проверку прибора" />
									</div>
								</form>
							</div>					
					</div>
					  
					<div id="modal-form-'.$content['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
						  <form id="del-'.$content['id'].'" action="javascript:void(null);" onsubmit="ajax_send(\'del-'.$content['id'].'\', \''.ADMIN.'/module/verification/ajax/order_divece_del\');">
						  <div class="panel-body">
							<h3 class="mt5">Удалить '.$content['stable_name'].'?</h3>					
							<hr class="short alt">
							<p>Вы действительно хотите удалить '.$content['stable_name'].' из списка?</p>
						  </div>
						  <div class="panel-footer text-right">
							<input type="hidden" name="id" value="'.$content['id'].'" />
							<button type="submit" class="btn btn-danger">' . $lang['delete'] .'</button>
						  </div>
						  </form>
						</div>
					  </div>
					  <div id="modal-edit-'.$content['id'].'" class="popup-basic admin-form mfp-with-anim mfp-hide">						
						<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Изменить прибор</span></div>
								<form id="edit-'.$content['id'].'" action="javascript:void(null);" onsubmit="ajax_send(\'edit-'.$content['id'].'\', \''.ADMIN.'/module/verification/ajax/order_device_edit\');">
									<div class="panel-body p25">';
                                    if($content['vdid'] == -1){
                                      echo '<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="stable_price" name="stable_price" type="text" placeholder="Введите цену" value="'.$content['stable_price'].'" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-comments"></i></label>
											</label>
										</div>
                                        <div class="section">
                                        <label for="comment" class="field prepend-icon">
                                            <textarea id="comment" name="comment" placeholder="Введите комментарий" class="gui-textarea">'.$content['comment'].'</textarea>
                                            <label for="comment" class="field-icon"><i class="fa fa-comments"></i></label>
                                        </label>
										</div>'; 
                                    } else {
                                       echo '<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="model" name="model" type="text" placeholder="Введите наименование модели" value="'.$content['model'].'" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-comments"></i></label>
											</label>
										</div>	
                                        <div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="number" name="number" type="text" placeholder="Введите номер" value="'.$content['number'].'" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-comments"></i></label>
											</label>
										</div>
                                        <div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="dn" name="dn" type="text" placeholder="Введите Ду" value="'.$content['dn'].'" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-comments"></i></label>
											</label>
										</div> 
                                        <div class="section">
											<label class="col-sm-3 control-label">Активировать прибор?</label>
											<div class="col-sm-4">
												'.radio("status", $content['status']).'														
											</div>
										</div>';
                                    }
								
									echo '</div>									
									<div class="panel-footer">
										<input type="hidden" name="id" value="'.$content['id'].'" />
                                        <input type="hidden" name="vdid" value="'.$content['vdid'].'" />
										<input type="submit" class="button btn-primary" value="Изменить прибор" />
									</div>
								</form>
							</div>					
					  </div>
				</td>
				<td class="w20 text-right">
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $content['id'] . '" type="checkbox" name="checks[]" value="' . $content['id'] . '">
						<label for="checkbox' . $content['id'] . '"></label>
					</div>
				</td>
			</tr>';		
		}
		echo '<tr>
		<td colspan="7" class="text-right"><b>Итого:</b></td>
		<td colspan="5" class="text-left"><b>'.$total.' рублей.</b></td>
		</tr>';
		if($ver['total'] != $total)
		{
			$db->query("UPDATE `" . DB_PREFIX . "_ver` SET `total` = '".$total."' WHERE `id` = " . $vid . " LIMIT 1 ;");
		}
		if($flag)
		{
			$query_ver = $db->query("SELECT status,date_end FROM ".DB_PREFIX."_ver WHERE id = '". $vid."' LIMIT 1");
			$ver = $db->getRow($query_ver); 
			if ($ver['date_end'] == '0')
			{
				$db->query("UPDATE `" . DB_PREFIX . "_ver` SET `status` = '3', `date_end` = '".time()."' WHERE `id` = " . $vid . " LIMIT 1 ;");
			}
		}
	} 	
	else 
	{
		echo '<tr><td colspan="12">У данной заявки еще не заполнен список приборов!</td></tr>';	
	}	
}

function ajax_order_device_add()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
    if((isset($_POST['vdid'])&&$_POST['vdid']=="-1")){
        $id = intval($_POST['id']);
        $comment = $_POST['comment'];
        $db->query("INSERT INTO `" . DB_PREFIX . "_ver_detail` (`vid`, `vdid`, `stable_name`, `stable_price`, `sum`, `comment`, `status`) VALUES ('" . $db->safesql($id) . "', '-1', 'Сервис', '" . $_POST['price2'] . "', '1', '".$db->safesql($comment)."', '2');");
    } else
	if((isset($_POST['vdid'])&&$_POST['vdid']!="0"))
	{
		$id = intval($_POST['id']);
		$vdid = intval($_POST['vdid']);
		$query_ver = $db->query("SELECT id, iprice, oid FROM ".DB_PREFIX."_ver WHERE id = '". $id."' LIMIT 1");
		$ver = $db->getRow($query_ver);
		$oid = $ver['oid'];		
		$query_stable = $db->query("SELECT id, name FROM ".DB_PREFIX."_ver_device WHERE id = '". $vdid ."' LIMIT 1");
		$device_stable = $db->getRow($query_stable);	
		$query_price = $db->query("SELECT price FROM ".DB_PREFIX."_ver_device_price WHERE vdid = '".$device_stable['id']."' AND oid = '".$oid."' LIMIT 1");
		if(($db->numRows($query_price) > 0)&&($ver['iprice'])) 
		{
			$rprice = $db->getRow($query_price);
			$stable_price = $rprice['price'];
		}
		else
		{
			$query_stable_pr = $db->query("SELECT price FROM ".DB_PREFIX."_ver_device_price WHERE vdid = '".$device_stable['id']."' AND oid = '0' LIMIT 1");
			$stable_pr = $db->getRow($query_stable_pr);	
			$stable_price = $stable_pr['price'];			
		}		
		$sum = intval($_POST['sum']);
		$stable_name = $device_stable['name'];
		$model = $_POST['model'];
		$number = $_POST['number'];
		$dn = intval($_POST['dn']);
		if($db->query("INSERT INTO `" . DB_PREFIX . "_ver_detail` ( `id` , `vid`, `vdid`, `stable_name`, `stable_price`, `sum`, `model`, `number`, `dn`, `status`) VALUES (NULL, '" . $db->safesql($id) . "', '" . $db->safesql($vdid) . "', '" . $db->safesql($stable_name) . "', '" . $db->safesql($stable_price) . "', '" . $db->safesql($sum) . "', '" . $db->safesql($model) . "', '" . $db->safesql($number) . "', '" . $db->safesql($dn) . "','1');")) 
		{
			echo '1';
		}
		else
		{
			echo '3';
		}
	}
	else
	{
		echo '2';
	}
}

function ajax_order_device_edit()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
    if($_POST['vdid'] == '-1'){
        $id = intval($_POST['id']);
        $stable_price = $_POST['stable_price'];
        $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
        $db->query("UPDATE `" . DB_PREFIX . "_ver_detail` SET `stable_price` = '".$stable_price."', `comment` = '".$comment."' WHERE `id` = " . $id . " LIMIT 1 ;");
        echo 'Данные успешно изменены!';
    } else
	if((isset($_POST['model'])&&$_POST['model']!="") && (isset($_POST['number'])&&$_POST['number']!="") && (isset($_POST['dn'])&&$_POST['dn']!="") && isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$model = $_POST['model'];
        $number = $_POST['number'];
        $dn = $_POST['dn'];
		$status = isset($_POST['status']) ? $_POST['status'] : 0;
		$db->query("UPDATE `" . DB_PREFIX . "_ver_detail` SET `status` = '".$status."', `model` = '".$model."', `number` = '".$number."', `dn` = '".$dn."' WHERE `id` = " . $id . " LIMIT 1 ;");
		echo 'Данные успешно изменены!';
	}
}

function ajax_order_device_compl()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$comment = $_POST['comment'];
		$result = isset($_POST['result']) ? $_POST['result'] : 0;
		$status = ($result == 0) ? '3' : '2';
		$db->query("UPDATE `" . DB_PREFIX . "_ver_detail` SET `status` = '".$status."', `comment` = '". $db->safesql($comment) ."' WHERE `id` = " . $id . " LIMIT 1 ;");
		echo 'Информация успешно сохранена!';		
		
	}
}

function ajax_order_device_del()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$comment = $_POST['comment'];
		$result = isset($_POST['result']) ? $_POST['result'] : 0;
		$status = '0';
		$db->query("UPDATE `" . DB_PREFIX . "_ver_detail` SET `status` = '".$status."' WHERE `id` = " . $id . " LIMIT 1 ;");
		echo 'Прибор успешно удален!';		
		
	}
}


