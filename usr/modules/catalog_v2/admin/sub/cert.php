<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ADMIN_ACCESS')) {
    header('Location: /');
    exit;
}

function cert_view()
{	
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Список сертификатов';
	$adminTpl->admin_head('Сертификаты | '.$lnn);
	if(!isset($_GET["jqgrid"])){
		echo '<i class="text-danger">Сертификаты с истекшим сроком будут скрыты с сайта автоматически</i>';
	}
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'catalog_v2';
	$atributs['module_open'] = 'cert/edit';
	$atributs['button'] = 'Добавить сертификат';
	$atributs['button_url'] = '/administration/module/catalog_v2/cert/add';	
	load_grid('jqCert', $lnn, $atributs);	
	$adminTpl->admin_foot();
}

function cert_list_view($id)
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Список сертификатов';
	$adminTpl->admin_head('Сертификаты | '.$lnn);
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'catalog_v2';
	$atributs['module_open'] = 'cert/edit';
	$atributs['button'] = 'Действия';
	$atributs['button_onclick'] = "$('#actionModal').modal('show');";
	$atributs['button_url'] = '';
	// $atributs['button'] = 'Удалить все';
	// $atributs['button_onclick'] = "confirm('Удалить все?') ? $(location).attr('href', '/administration/module/catalog_v2/cert/delete_all/".$id."') :''";
	// $atributs['button_url'] = '/administration/module/catalog_v2/cert/delete_all/'.$id;
	load_grid('jqCertView', $lnn, $atributs);	
	echo <<<HTML
		<div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Действия</h5>
		      </div>
		      <div class="modal-body">
		        	<button type="button" class="btn btn-block btn-primary btn-xs" onclick="confirm('Удалить выбранное?') ? delete_checked() : '';">Удалить выбранное</button>
		        	<button type="button" class="btn btn-block btn-danger btn-xs" onclick="confirm('Удалить все?') ? document.location.href = '/administration/module/catalog_v2/cert/delete_all/{$id}' : '';">Удалить все</button>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		      </div>
		    </div>
		  </div>
		</div>

		<script>
			async function delete_checked(){
				const data = [];
				$(".cbox:checked").each((i, item) => {
					const id = $(item).parents("tr").attr("id");
					if(id != undefined){
						data.push(id);
					}
				});
				const id = '{$id}';
				const lw = new LW;
				lw.on();
				await $.post("/administration/module/catalog_v2/cert/delete_select_id", {data, id});
				window.location.href += "";
			}
		</script>
HTML;
	$adminTpl->admin_foot();
}

function cert_list($id)
{	
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Прикрепление товаров';
	$adminTpl->admin_head('Сертификаты | '.$lnn);
	
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'catalog_v2';
	$atributs['module_open'] = 'edit';
	$atributs['button'] = 'Прикрепить сертификат';
	$atributs['button_url'] = '/administration/module/catalog_v2/cert/add';	
	$atributs['button_onclick'] = 	"ajax_send('table_form_jqCatalog', '/administration/module/catalog_v2/cert/ajax_relation/".$id."/');";	
	$atributs['js'] = '
	 $grid.jqGrid("navButtonAdd", pager,
        {
            caption:"Change price",
            title:"Custom oper",
            icon:"ui-icon-flag",
            onClickButton:function () {
                var price = prompt("Enter new price.\Number between 1 and 3000");
                if (!price) return;

                $(this).jqGrid("extRequest",
                    {
                        "oper":"price", //oper name
                        "price":price    //and other values
                    },
                    {
                        \'selrow\':true   //add selected rows to request
                    });
            }
        });';
		
	load_grid('jqCatalog', $lnn, $atributs);
	echo '<form id="cert_list">';
	echo '</form>';	
	echo '<script type="text/javascript" language="javascript">
	function ajax_send(name, str_url) {
			var msg   = $("#"+name).serialize();
			$.ajax({
				type: "POST",
				url: str_url,
				data: msg,
				success: function(data) {
					$("#modal-"+name).magnificPopup(\'close\');
					notif("success","'.$lang['info'].'", data);										
				}          
			});
	};</script>';	
	$adminTpl->admin_foot();
}

function ajax_relation($id)
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;	
	$insert_values  = '';
	foreach($_POST as $k => $v)
	{
		if (!(strpos($k, 'jqg_jqCatalog_') === false))
		{
			$now_id = str_replace("jqg_jqCatalog_", '', $k);
			$hash = $now_id.'_'.$id;
			$insert_values .=  "(NULL, '".$now_id."','".$id."', '".$hash."', '".date("Y-m-d", time())."', '1'),";
			
		}
	}
	if (!empty($insert_values))
	{
		$insert = "INSERT INTO `JMY_cert_relation`(`id`, `cid`, `cert_id`, `hash`, `date`, `status`) VALUES ".substr_replace($insert_values,'',-1)." ON DUPLICATE KEY UPDATE date='".date("Y-m-d", time())."';";
		$db->query($insert);
		echo 'Товары успешно прикреплены!';
					
	}
	else
	{
		echo 'Выберете товары!';
	}
}


function cert_add($id = null)
{
global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	if(isset($id))
	{
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_categories WHERE id = '" . $id . "'");
		$ccid = $id;
		$cat = $db->getRow($query);
		$lnn = $lang['cats_edit'];
		
		$myQuery = $db->query("SELECT * FROM ".DB_PREFIX."_cert WHERE id = '".$id."'");
		$r = $db->getRow($myQuery);
		$rtype = $r["type"];
		$date = $r["date_start"];
		
		$dateend = $r["date_end"];
		$numb = $r["numb"];
		$issue = $r["issue"];
		$full = $r["comment"];
	}
	else
	{
		$date = gmdate('d.m.Y H:i'); 
		$dateend = gmdate('d.m.Y H:i'); 
		$task = false;
		$toid = false;
		$endsw = false;		
		$numb = "";
		$issue = "";
		$full = "";
		$rtype = "";
		$lnn = 'Создание нового сертификата';
	}
	$adminTpl->admin_head($lang['cats'].' | '.$lnn);
	$validation_array = array(
		
		'toid' => array(
			'required' =>  array('true', $lang['cats_name_err'])
		),
		
		'task' => array(
			'required' =>  array('true', $lang['cats_name_err'])
		)
	);
	validationInit($validation_array);
	datetimepickerInit('date');
	datetimepickerInit('dateend');
	echo ajaxorgadd();
	$adminTpl->js_code[] = '	
        var selectList = $(\'.admin-form select\');
        selectList.each(function (i, e) {
          $(e).on(\'change\', function () {
            if ($(e).val() == "0") $(e).addClass("empty");
            else $(e).removeClass("empty")
          });
        });
        selectList.each(function (i, e) {
          $(e).change();
        });
        $("input#tagsinput").tagsinput({
          tagClass: function (item) {
            return \'label label-default\';
          }
        });		
		$(".select2-single").select2();

		';	
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lnn .'</span>
			</div>
			<div class="panel-body admin-form">
				<form id="admin-form" action="{ADMIN}/module/catalog_v2/cert/save" method="post" name="cats" role="form" enctype="multipart/form-data" class="form-horizontal parsley-form">					
					<div class="form-group">
						<label for="type"  class="col-lg-3 control-label">Выберите тип сертификата</label>
						<div class="col-lg-3">
								<label class="field select">
									<select class="form-control" name="rtype">';
										$query_r = $db->query("SELECT * FROM ".DB_PREFIX."_cert_type ORDER BY id ASC");
										if($db->numRows($query_r) > 0) 
										{
											while($r_list = $db->getRow($query_r)) 
											{
												$selected = ($r_list['id'] == $rtype) ? "selected" : "";
												echo '<option value="'.$r_list['id'].'" '.$selected.'>'.$r_list['title'].'</option>';
											}
										}		
								echo '	</select><i class="arrow double"></i>
								</label>
						</div>
					</div>				
					
					<div class="form-group">
						<label for="date"  class="col-lg-3 control-label">Действует с</label>
						<div class="col-lg-3">
							<label for="date" class="field prepend-picker-icon">
								<input id="date" onkeyup="date_mask(this);" onfocus="date_focus(this);" type="text" name="date" class="gui-input" value="'.date('d.m.Y', strtotime($date)).'">
							</label>
						</div>	
					</div>
					<div class="form-group">
						<label for="dateend"  class="col-lg-3 control-label">Действует по</label>
						<div class="col-lg-3">
							<label for="dateend" class="field prepend-picker-icon">
								<input id="dateend" onkeyup="date_mask(this);" onfocus="date_focus(this);" type="text" name="dateend" class="gui-input" value="'.date('d.m.Y', strtotime($dateend)).'">
							</label>
						</div>	
					</div>
					<div class="form-group">
						<label for="numb" class="col-lg-3 control-label">Номер сертификата</label>
						<div class="col-lg-4">
							<label for="numb" class="field prepend-icon">
								<input id="numb" type="text" name="numb" value="'.$numb.'" placeholder="Введите номер сертификата" class="gui-input">
								<label for="numb" class="field-icon"><i class="fa fa-pencil"></i></label>
							</label>
						</div>
					</div>			
					<div class="form-group">
						<label for="issue" class="col-lg-3 control-label">Выдан</label>
						<div class="col-lg-4">
							<label for="issue" class="field prepend-icon">
								<input id="issue" type="text" name="issue" value="'.$issue.'" placeholder="Введите название организации выдавшей сертификат" class="gui-input">
								<label for="issue" class="field-icon"><i class="fa fa-pencil"></i></label>
							</label>
						</div>
					</div>	
					<div class="form-group">					
						<label for="date"  class="col-lg-3 control-label">Файл договора</label>
						<div class="col-lg-3">
                            <label class="field prepend-icon append-button file"><span class="button btn-primary">Выберите файл</span>
                              <input id="file" type="file" name="file" onchange="document.getElementById(\'uploader1\').value = this.value;" class="gui-file">
                              <input id="uploader1" type="text" class="gui-input">
                              <label class="field-icon"><i class="fa fa-upload"></i></label>
                            </label>
						</div> 
                    </div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Комментарий</label>
						<div class="col-sm-3">
							<label for="comment" class="field prepend-icon">
								<textarea name="full" id="full" placeholder="Комментарий к настоявшему договору" class="gui-textarea">'.$full.'</textarea>
								<label for="comment" class="field-icon"><i class="fa fa-comments"></i></label><span class="input-footer">Старайтесь заполнять комментарий!</span>
							</label>
						</div>
					</div>	
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-4">
							<input type="submit" class="btn btn-primary btn-parsley" id="sub" value="'. (isset($id) ?  $lang['update'] :  'Добавить сертификат') .'" />
						</div>
					</div>';
					if(isset($ccid))
					{
						echo '
							<script>
								$(\'#admin-form\').submit(function (e) {
									var form = this;
									e.preventDefault();
									setTimeout(function () {
										form.submit();
									}, 1000); // in milliseconds
								});
								</script>
							<input type="hidden" name="edit" value="1">
							<input type="hidden" name="cid" value="' . $ccid . '">';
					}
	echo '</form></div></div>';
	?>

					<script>
					function date_mask(This){
						const elem = $(This);
						const val = elem.val();
						const len = elem.val().length;
						if(event.keyCode !== 8){
							switch(len){
								case 2:
								case 5:
									elem.val(val+'.');
								break;
							}
						} else {
							switch(len){
								case 2:
								case 5:
									elem.val(val.substr(0, len - 1));
								break;
							}
						}
						
						const new_val = elem.val();
						elem.val(new_val.replace(/\.\./g, '.').substr(0, 10));
					}
					function date_focus(This){
						const elem = $(This);
						elem.select();
					}
					</script>

	<?php
	$adminTpl->admin_foot();
}

function cert_save() {
global $adminTpl, $db, $core, $lang;

	$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);
	$ttime = time();
	$date = !empty($_POST['date']) ? filter($_POST['date']) : $ttime;	
	$dateend = !empty($_POST['dateend']) ? filter($_POST['dateend']) : $ttime;	
	$org= isset($_POST['org']) ? intval($_POST['org']) : false;
	$numb= isset($_POST['numb']) ?  filter($_POST['numb']) : false;
	$issue= isset($_POST['issue']) ?  filter($_POST['issue']) : false;
	$comment= isset($_POST['full']) ?  filter($_POST['full']) : false;
	$rtype= isset($_POST['rtype']) ? intval($_POST['rtype']) : false;
	$oid= isset($_POST['oid']) ? intval($_POST['oid']) : false;	
	$uid = $core->auth->user_info['id'];

	$cert_type = \Catalog_v2\Model\CertType::findOrFail($rtype);

	if($date != $ttime)
	{
		$parseDate = explode(' ', $date);
		$subDate = explode('.', $parseDate[0]);
		if(isset($parseDate[1]))
		{
			$subTime = explode(':', $parseDate[1]);
		}
		else
		{
			$subTime[0] = 12;
			$subTime[1] = 0;
		}
		$date = gmmktime($subTime[0], $subTime[1], 0, $subDate[1], $subDate[0], $subDate[2]);
	}
	if($dateend != $ttime)
	{
		$parseDate = explode(' ', $dateend);
		$subDate = explode('.', $parseDate[0]);
		if(isset($parseDate[1]))
		{
			$subTime = explode(':', $parseDate[1]);
		}
		else
		{
			$subTime[0] = 12;
			$subTime[1] = 0;
		}
		$dateend = gmmktime($subTime[0], $subTime[1], 0, $subDate[1], $subDate[0], $subDate[2]);
	}
	$date_start = $cert_type->date_start ? date('Y-m-d', $date) : '0000-00-00';
	$date_end = $cert_type->date_end ? date('Y-m-d', $dateend) : '0000-00-00';
	
	echo '<div id="content" class="animated fadeIn">';	
	if(!empty($date))
	{
		if(isset($_POST['edit']))
		{
			$onid = $_POST['cid'];
			$db->query("UPDATE `" . DB_PREFIX . "_cert` SET type = '".$rtype."', date_edit = '".date("Y-m-d")."', date_start = '".$date_start."', date_end = '".$date_end."', issue = '".$issue."', comment = '".$comment."', uid = '".$uid."', numb = '".$numb."' WHERE `id` =".$onid."  LIMIT 1");
			$adminTpl->info('Сертификат успешно добавлен!', 'info', null, $lang['info'], 'Список сертификатов', ADMIN.'/module/catalog_v2/cert');
		}
		else
		{
			if($db->query("INSERT INTO `" . DB_PREFIX . "_cert` ( `id` , `uid`, `type` , `date_add` ,  `date_start` , `date_end` , `issue` , `numb` , `comment` , `status`) VALUES (NULL, '" . $uid . "', '" . $rtype . "', '" .  date('Y-m-d', time()) . "', '" . $date_start . "', '" .  $date_end . "', '" . $db->safesql($issue) . "', '" . $db->safesql($numb) . "', '" . $db->safesql($comment) . "', '1');"))
			{		
				$adminTpl->info('Сертификат успешно добавлен!', 'info', null, $lang['info'], 'Список сертификатов', ADMIN.'/module/catalog_v2/cert');	
				$onid = $db->insert_id();				
			}			
		}
		
		$module = 'cert';
		if($_FILES['file']['size'] > 0) 
		{	
			$purl = 'files/cert/'.$onid.'/';
			$path_info = pathinfo($_FILES['file']['name']);
			$ext = $path_info['extension'];	
			$file_nnid = $onid.time();	
			$file_full = $purl.translit($path_info['basename']).'_'.$file_nnid.'.'.$ext;
			if (file_exists(ROOT.$file_full)) 
			{
				@unlink(ROOT.$file_full);		
			}
			if($foo = new \Boot\System\Upload\Upload($_FILES['file']))
			{
				$foo->file_new_name_body = translit($path_info['basename']).'_'.$file_nnid;						
				$foo->file_overwrite = true;
				$foo->file_auto_rename = false;
				$foo->Process(ROOT.$purl);										
				if ($foo->processed) 
				{
					$foo->Clean();
				}
				$db->query("INSERT INTO `" . DB_PREFIX . "_attach` ( `id` , `name` , `url` , `pid` , `module` , `downloads` ,  `access`) VALUES (NULL, '" . $path_info['basename'] . "', '" . $purl .$foo->file_dst_name . "', '" . $onid . "', '" . $module . "', '0', '1');");
				$aid = $db->insert_id();
				$db->query("UPDATE `" . DB_PREFIX . "_cert` SET `aid` = '" . $db->safesql($aid) . "' WHERE `id` =".$onid."  LIMIT 1");
			}	
		}
	}
	else
	{
		$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
	}
	echo '</div>';
	$adminTpl->admin_foot();
}

