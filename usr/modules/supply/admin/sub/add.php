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
	
	function add($edit_id = null, $logistic = false)
	{
		global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;
        $attach = "";
		if (empty($edit_id))
		{
			$date_bill = gmdate('d.m.Y H:i'); 
			$date_work = false; 
			$date_ship = false; 
			$date_end = false; 
			$oid = false;
			$object = false;
			$sum = false;
			$numb = false;		
			$bill_code = false;	
            $delivery = false;
			$object_disabled = false;
			$oid_choose = '<option value="0" disabled selected>Выберите контрагента</option>';		
			$lnn = 'Создание новой закупки';
			$org = false;
		}
		else
		{
			$query = $db->query("SELECT s.*, o.name, o.inn FROM ".DB_PREFIX."_supply_order as s LEFT JOIN ".DB_PREFIX."_organizations AS o on(s.oid=o.id) WHERE s.id = '" . $edit_id . "'");
			$supply = $db->getRow($query);
			$object = $supply['object'];
			$object_disabled = false;
			$org = $supply['org'];
			if (!empty($supply['sales_id']))
			{
				$query_sales = $db->query("SELECT object FROM ".DB_PREFIX."_sales WHERE id = '" . intval($supply['sales_id']) . "'");
				$sales = $db->getRow($query_sales);
				$object = $sales['object'];
				$object_disabled = true;
			}
			$id = $supply['id']; 
			$date_bill =  empty($supply['date_bill']) ? '' : date('d.m.Y', strtotime($supply['date_bill']));
			$date_work =  empty($supply['date_work']) ? '' : date('d.m.Y', strtotime($supply['date_work']));
			$date_ship =   empty($supply['date_ship']) ? '' : date('d.m.Y', strtotime($supply['date_ship']));
			$date_end =   empty($supply['date_end']) ? '' : date('d.m.Y', strtotime($supply['date_end']));
			$oid =  $supply['oid'];
            $delivery = $supply["delivery"];
			$oid_choose = '<option value="'.$oid.'" selected>'.$supply['name'].' ('.$supply['inn'].')</option>';
			$bill_code =  $supply['bill_code']; 
			$lnn = 'Редактирование закупки';
            
            $qq = $db->query("SELECT * FROM JMY_attach WHERE pid = '".$edit_id."' AND `module` = 'supply'");
            if($db->numRows($qq) > 0){
            $attach = '<ul>';
                while($r = $db->getRow($qq)){
                    $attach .= '<li id="li_'.$r["id"].'"><a target="_blank" href="'.config('app.url').'/download/'.$r["id"].'">'.$r["name"].'</a><a href="javascrip:void(0);" class="ml20 removeAttach" data-id="'.$r["id"].'" data-name="'.$r["name"].'" style="color: red;">Удалить</a></li>';
                }
            $attach .= '</ul>';
            }
            
		}	
		$adminTpl->admin_head($lang['cats'].' | '.$lnn);
		
		$validation_array = array(
		'date_bill' => array(
		'required' =>  array('true', $lang['cats_name_err'])
		),
		'oid' => array(
		'required' =>  array('true', $lang['cats_name_err'])
		)
		);
		validationInit($validation_array);
		datetimepickerInit('date_bill');
		datetimepickerInit('date_work');
		datetimepickerInit('date_ship');
		datetimepickerInit('date_end');

		echo ajaxorgadd($oid);
		$adminTpl->js_code[] = '$(".select2-single").select2();';	
		echo '<div id="content" class="animated fadeIn">
		<div class="panel panel-dark panel-border top">
		<div class="panel-heading"><span class="panel-title">'. $lnn .'</span>
		</div>			
		<div class="panel-body admin-form">
		<form id="admin-form" action="'.($logistic ? '{ADMIN}/module/supply/saveLogistics' : '{ADMIN}/module/supply/save').'" method="post" enctype="multipart/form-data" name="cats" role="form" class="form-horizontal parsley-form">	';
		
		echo '			
		<div class="form-group" '.($logistic ? 'style="display:none;"' : '').'>
			<label for="toid"  class="col-lg-3 control-label">Поставщик</label>
			<div class="col-lg-4">
				<div class="smart-widget sm-right smr-50">';
				// echo '<label for="toid" class="field prepend-icon">
				// <select name="oid" class="select2-single gui-input" id="orglist">
				// '.$oid_choose.'
				// </select>								
				// <label for="translit" class="field-icon"><i class="fa fa-user"></i></label>												
				// </label>';
				oSelect('jsAdminTpl', 'oid', $oid, "getOrganizations", false, false, false, true);
				oSelect('select', 'oid', $oid, "getOrganizations", false, false, false, true);
				echo '<button  onclick="modal_o(\'#modal-add-org\');" type="button" class="button"><i class="fa fa-plus"></i></button>
				</div>';
				if(isset($_SESSION["empty_oid"])){
					echo '<strong class="text-danger ">
							'.$_SESSION["empty_oid"].'
						</strong>';
					unset($_SESSION["empty_oid"]);
				}
			echo '</div>	
		</div>
		<div class="form-group">
						<label for="type"  class="col-lg-3 control-label">'.$lang['org'].'</label>
						<div class="col-lg-4">
								<label class="field select">
									<select class="form-control" name="org" id="org">';
										$query_o = $db->query("SELECT n.*, o.name FROM ".DB_PREFIX."_org as n LEFT JOIN ".DB_PREFIX."_organizations as o on(o.id=n.oid) ORDER BY id ASC");
										if($db->numRows($query_o) > 0) 
										{
											while($o_list = $db->getRow($query_o)) 
											{
												$selected = ($o_list['id'] == $org) ? "selected" : "";
												echo '<option value="'.$o_list['id'].'" '.$selected.'>'.$o_list['name'].'</option>';
											}
										}		
								echo '	</select><i class="arrow double"></i>
								</label>
						</div>
					</div>';
		
		
		echo '<div class="form-group" '.($logistic ? 'style="display:none;"' : '').'>
		<label for="date_bill"  class="col-lg-3 control-label">Дата счета</label>
		<div class="col-lg-4">
		<label for="date_bill" class="field prepend-picker-icon">
		<input id="date_bill" type="text" name="date_bill" placeholder="Введите дату " class="gui-input" value="'.$date_bill.'">
		</label>
		</div>	
		</div>	
		<div class="form-group" '.($logistic ? 'style="display:none;"' : '').'>
		<label for="date_bill"  class="col-lg-3 control-label"></label>
		<div class="col-lg-4">
		<a href="javascript:void(0)" onclick="showhide(\'addition\')"><font size="3pt">Заполнить дополнительные даты</font></a>
		</div>	
		</div>	
		<div id="addition" style="display: '.(empty($edit_id) ? 'none' : 'block').'">
		<div class="form-group" '.($logistic ? 'style="display:none;"' : '').'>
		<label for="date_work"  class="col-lg-3 control-label">Дата размещения</label>
		<div class="col-lg-4">
		<label for="date_work" class="field prepend-picker-icon">
		<input id="date_work" type="text" name="date_work" placeholder="Введите дату размещения" class="gui-input" value="'.$date_work.'">
		</label>
		</div>	
		</div>	
		<div class="form-group" '.($logistic ? 'style="display:none;"' : '').'>
		<label for="date_ship"  class="col-lg-3 control-label">Дата отгрузки</label>
		<div class="col-lg-4">
		<label for="date_ship" class="field prepend-picker-icon">
		<input id="date_ship" type="text" name="date_ship" placeholder="Введите дату отгрузки" class="gui-input" value="'.$date_ship.'">
		</label>
		</div>	
		</div>	
		<div class="form-group">
		<label for="date_end"  class="col-lg-3 control-label">Дата поступления</label>
		<div class="col-lg-4">
		<label for="date_end" class="field prepend-picker-icon">
		<input id="date_end" type="text" name="date_end" placeholder="Введите дату поступления" class="gui-input" value="'.$date_end.'">
		</label>
		</div>	
		</div>	
		</div>
        <div class="form-group" '.($logistic ? '' : 'style="display:none;"').'>
		<label for="delivery"  class="col-lg-3 control-label">Тип доставки</label>
		<div class="col-lg-4">
		<div class="smart-widget sm-right">
		<label for="delivery" class="field prepend-icon">
        <select id="delivery" name="delivery" class="select2-single gui-input">
        <option value="1" '.($delivery == 1 ? 'selected' : '').'>Самовывоз</option>
        <option value="2" '.($delivery == 2 ? 'selected' : '').'>ТК</option>
        </select>								
        <label for="delivery" class="field-icon"><i class="fa fa-user"></i></label>												
		</label>
		</div>	
		</div>	
		</div>	
		<div class="form-group" '.($logistic ? 'style="display:none;"' : '').'>
		<label for="bill_code"  class="col-lg-3 control-label">Номер счета</label>
		<div class="col-lg-4">
		<label for="bill_code" class="field prepend-icon">
		<input  type="text" name="bill_code" placeholder="Необходим для автоматического присвоения платежа" class="gui-input" value="'.$bill_code.'">
		<label for="name" class="field-icon"><i class="fa fa-pencil"></i></label>
		</label>
		</div>	
		</div>	
        <div class="form-group" '.($logistic ? 'style="display:none;"' : '').'>
		<label for="object"  class="col-lg-3 control-label">Объект</label>
		<div class="col-lg-4">';
            oSelect("jsAdminTpl", "object", $object, "getObjectToSelect", false);
            oSelect("select", "object", $object, "getObjectToSelect", false);
		echo '</div>	
		</div>	
        <div class="form-group" '.($logistic ? 'style="display:none;"' : '').'>
		<label for="date"  class="col-lg-3 control-label">Приложенные файлы</label>
		<div class="col-lg-3">
        '.$attach.'
		</div> 
        </div>
        
        <div class="modal fade" id="delFileSupply" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Удалить файл?</h5>
              </div>
              <div class="modal-body">
                Вы точно хотите удалить <span id="nameFile"></span>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="delFileBtn">Удалить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
              </div>
            </div>
          </div>
        </div>
        
		<div class="form-group" '.($logistic ? 'style="display:none;"' : '').'>
        <div class="col-lg-3"></div><div class="col-lg-3">';
        /*
		<label for="date"  class="col-lg-3 control-label">Файл счета</label>
		<div class="col-lg-3">
		<label class="field prepend-icon append-button file"><span class="button btn-primary">Выберите файл</span>
		<input id="file" type="file" name="file" onchange="document.getElementById(\'uploader1\').value = this.value;" class="gui-file">
		<input id="uploader1" type="text" class="gui-input">
		<label class="field-icon"><i class="fa fa-upload"></i></label>
		</label>
		</div> 
        */
        $attribute = [];
        $attribute["name"] = "";
        $attribute["script"] = true;
        $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
        $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
        echo uploaderMaster("css", $attribute);
        echo uploaderMaster("upload", $attribute);
        echo uploaderMaster("view", $attribute);
		echo '</div> 
        </div>
		<div class="form-group">
		<label class="col-sm-3 control-label">Комментарий</label>
		<div class="col-sm-4">
		<label for="comment" class="field prepend-icon">
		<textarea name="comment" id="comment" placeholder="" class="gui-textarea"></textarea>
		<label for="comment" class="field-icon"><i class="fa fa-comments"></i></label><span class="input-footer">Введите ваш комментарий к закупке!</span>
		</label>
		</div>
		</div>
		<div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-4">
		'.(empty($edit_id) ? '' : '<input type="hidden" name="edit_id" value="'.$edit_id.'" />').'
		<input type="submit" class="btn btn-success btn-parsley" id="sub" value="'.$lnn.'" />
		</div>
		</div>
		</form>
		</div>
		</div>';
        echo '<script src="usr/modules/supply/admin/asset/js/supplyEdit.js"></script>';
		$adminTpl->admin_foot();
	}
	
	function save($logistic = false) {
		global $adminTpl, $db, $core, $lang;
		$request = [];

		$edit_id = !empty($_POST['edit_id']) ? intval($_POST['edit_id']) : false;	

		$date_bill_value = 	!empty($_POST['date_bill']) ? date('Y-m-d', strtotime($_POST['date_bill'])) : null;
		$request["date_bill"] = $date_bill_value;

		$date_work_value = !empty($_POST['date_work']) ? date('Y-m-d', strtotime($_POST['date_work'])) : null;
		$request["date_work"] = $date_work_value;

		$date_ship_value = !empty($_POST['date_ship']) ? date('Y-m-d', strtotime($_POST['date_ship'])) : null;
		$request["date_ship"] = $date_ship_value;

		$date_end_value = !empty($_POST['date_end']) ? date('Y-m-d', strtotime($_POST['date_end'])) : null;
		$request["date_end"] = $date_end_value;

		if($edit_id){
			$zz = \Supply\Model\SupplyOrder::find($edit_id)->toArray();
			if(empty($zz["date_end"]) && empty($date_end_value) && !empty($date_ship_value)){
				$date_end = strtotime("{$date_ship_value} + 1 week");
				$date_end_value = date('Y-m-d',$date_end);
				$request["date_end"] = $date_end_value;
			}
		}

		$status = 1;
		if(!empty($date_ship_value)){ 
			$status = 2;  
		}
		if(!empty($date_end_value)){ 
			$status = 3;  
		}
		$request["status"] = $status;

		$bill_code = !empty($_POST['bill_code']) ? filter($_POST['bill_code']) : null;	
		$request["bill_code"] = $bill_code;

		$object_value = !empty($_POST['object']) ? intval($_POST['object']) : null;	
		$request["object"] = $object_value;

		$oid = isset($_POST['oid']) ? intval($_POST['oid']) : null;
		if(empty($oid)){
			$_SESSION["empty_oid"] = 'Выберите поставщика';
			header("Location: /administration/module/supply/add");
			exit;
		}	
		$request["oid"] = $oid;

		$org = isset($_POST['org']) ? intval($_POST['org']) : null;	
		$request["org"] = $org;

		$comment= isset($_POST['comment']) ? $_POST['comment'] : null;
		$request["comment"] = $comment;
	
		$uid = $core->auth->user_info['id'];

		$file = isset($_POST['file']) ? $_POST['file'] : false;
	
        if($logistic){
			$delivery_value = isset($_POST['delivery']) ? $_POST['delivery'] : null;	
			$request["delivery"] = $delivery_value;
		}

		$flagNotif = false;
		if($edit_id){
			$ql = \Supply\Model\SupplyOrder::find($edit_id)->toArray();

			if(!empty($date_ship_value)){
				$flagNotif = empty($ql["date_ship"]) ? true : false;
			}
	
			if($date_bill_value != $ql["date_bill"] || $date_work_value != $ql["date_work"] || $date_ship_value != $ql["date_ship"] || $date_end_value != $ql["date_end"]){
				require_once(ROOT."usr/modules/supply/admin/sub/function.php");
				send_notif_change_date($edit_id);
			}
		}
		
		if(!empty($date_bill_value) && !empty($oid)){			
			if($edit_id){

				\Supply\Model\SupplyOrder::find($edit_id)->update($request);

				$orid = $edit_id;
			} else {
				$supply_order = \Supply\Model\SupplyOrder::create($request);
				$orid = $supply_order->id;
			}

			if($flagNotif){
				$attr = [];
				$attr['uid'] = 16; //16
				$attr['title'] = 'Новая заявка на доставку!';
				$attr['text'] = "Вам поступила новая заявка на доставку №".module_perfix('supply_logistics', $orid);
				$attr['icon'] = config('app.url').'/files/push/comment.png';
				$attr['url'] = config('app.url').'/administration/module/supply/logistics_detail/'.$orid;
				$attr['pNotif'] = true;
				$attr['pPush'] = true;
				$attr['module'] = "supply_logistics";
            	$attr['pid'] = $orid;
				sendAllNotif($attr);
			}

			$module = 'supply';
            
            $attribute = [];
            $attribute["name"] = "";
            $attribute["file"] = $file;
            $attribute["module"] = $module;
            $attribute["mod"] = $module;
            $attribute["id"] = $orid;
            $attribute["comm_id"] = $orid;
			$attribute["ftp"] = "StorageFTP";
            uploaderMaster("download", $attribute);
		}

		if($logistic){
			header("Location: /administration/module/supply/logistics_detail/".$orid);
		} else {
			header("Location: /administration/module/supply/order_detail/".$orid);
		}
	}
