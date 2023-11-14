<?php

switch(isset($url[2]) ? $url[2] : null) {
    default:
    case "in":
        main_in();
    break;
    
    case "out":
        main_out();
	break;
	
	case "add":
		add();
	break;

	case "save":
		save();
	break;

	case "edit":
		$id = $url[3];
		edit($id);
	break;
}

function main_in()
{	
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Входящие';
	$adminTpl->admin_head('Реестр корреспонденции | '.$lnn);
	$atributs['opts'] = 'open';
	$atributs['module'] = '';
	$atributs['module_name'] = 'letter/in';
	$atributs['module_open'] = 'detail';
	load_grid('jqLetter', $lnn, $atributs);	
	$adminTpl->admin_foot();
}

function main_out()
{	
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Исходящие';
	$adminTpl->admin_head('Реестр корреспонденции | '.$lnn);
	$atributs['opts'] = 'open';
	$atributs['module'] = '';
	$atributs['module_name'] = 'letter/out';
	$atributs['module_open'] = 'detail';
	load_grid('jqLetter', $lnn, $atributs);	
	$adminTpl->admin_foot();
}

function file_load($id = null){
	global $db;
	$attach = "Файлы отсутствуют!";
	if(!empty($id)){
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE `module` = 'letter' AND `pid` = '".$id."'");
		if($db->numRows($query) > 0){
			$attach = '<ul>';
			while($r = $db->getRow($query)){
				$attach .= '<li><a href="/download/'.$r["id"].'">'.$r["name"].'</a></li>';
			}
			$attach .= '<ul>';
		}
	}
}

function getTypeLetter($array = "eng", $type = null){
	$rus = [1 => "Входящее", "Исходящее"];
	$eng = [1 => "in", "out"];
	if(empty($type)){
		return $$array;
	} else {
		return $$array[$type];
	}
}

function add($id = null){
	global $db, $adminTpl, $core;
	if($id){
		$query = $db->query("SELECT l.* FROM ".DB_PREFIX."_letter as l WHERE l.id = '".$id."' LIMIT 1");
		$letter = $db->getRow($query);

		$uid = $letter["uid"];
		$type = $letter["type"];
		$oid_from = $letter["oid_from"];
		$oid_for = $letter["oid_for"];
		$date = $letter["date"];

		$attach = file_load($id);

		$lnn = 'Редактировать';
	} else {
		$uid = $core->auth->user_info['id'];
		$type = "";
		$oid_from = "";
		$oid_for = "";
		$date = date("Y-m-d");

		$attach = file_load();

		$lnn = 'Добавить';
	}

	$adminTpl->admin_head('Реестр корреспонденции | '.$lnn);

	?>

<div id="content" class="animated fadeIn">
	<div class="panel panel-dark panel-border top">
		<div class="panel-heading"><span class="panel-title"><?php echo $lnn;?> приказ</span>
	</div>
	<div class="panel-body admin-form">
		<?php
		if(!empty($id)){
			?>
			<h3>Приказ №<?php echo $id;?></h3>
			<?php
		}
		?>
		
		<form id="admin-form" method="post" name="cats" role="form" enctype="multipart/form-data" class="form-horizontal parsley-form" action="/administration/letter/save">

		<?php
			if(!empty($id)){
				echo '<input type="hidden" value="'.$id.'" name="letter_id">';
			}
		?>

			<div class="form-group">
				<label for="" class="col-lg-3 control-label">Ответственный:</label>
				<label for="" class="col-lg-3 pt10"><?php echo fullUserName($uid);?></label>
			</div>
			<div class="form-group">
				<label for="type"  class="col-lg-3 control-label">Тип корреспонденции:</label>
				<div class="col-lg-3">
					<select name="type" id="type" class="form-control">
						<?php
							$array_type = getTypeLetter("rus");

							foreach($array_type as $key => $val){
								echo '<option value="'.$key.'" '.((int)$key === (int)$type ? 'selected' : '').'>'.$val.'</option>';
							}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="type"  class="col-lg-3 control-label">От кого:</label>
				<div class="col-lg-3">
					<?php
						oSelect("jsAdminTpl", "oid_from", $oid_from, "getOrganizations");
						oSelect("select", "oid_from", $oid_from, "getOrganizations");
					?>
				</div>
			</div>
			<div class="form-group">
				<label for="type"  class="col-lg-3 control-label">Для кого:</label>
				<div class="col-lg-3">
					<?php
						oSelect("jsAdminTpl", "oid_for", $oid_for, "getOrganizations");
						oSelect("select", "oid_for", $oid_for, "getOrganizations");
					?>
				</div>
			</div>
			<div class="form-group">
				<label for="type"  class="col-lg-3 control-label">Дата:</label>
				<div class="col-lg-3">
					<input type="date" value="<?php echo $date;?>" name="date" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label for="type"  class="col-lg-3 control-label">Загрузка файлов:</label>
				<div class="col-lg-3">
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
				</div>
			</div>
			<div class="form-group">
				<label for="type"  class="col-lg-3 control-label"></label>
				<div class="col-lg-3">
					<input type="submit" class="btn btn-success" value="Создать">
				</div>
			</div>
		</form>

	<?php
	$adminTpl->admin_foot();
}

function save(){
	global $db, $core;

	$letter_id = isset($_POST["letter_id"]) ? $_POST["letter_id"] : false;

	if(!$letter_id){
		$uid = $core->auth->user_info['id'];
		$date_create = date("Y-m-d H:i:s");
	}
	
	$type = isset($_POST["type"]) ? $_POST["type"] : 1;
	$oid_from = isset($_POST["oid_from"]) ? "'".$_POST["oid_from"]."'" : 'null';
	$oid_for = isset($_POST["oid_for"]) ? "'".$_POST["oid_for"]."'" : 'null';
	$date = isset($_POST["date"]) ? "'".$_POST["date"]."'" : 'null';
	$file = isset($_POST["file"]) ? "'".$_POST["file"]."'" : 'null';

	if($letter_id){
		$db->query("UPDATE ".DB_PREFIX."_letter SET type = '".$type."', oid_from = ".$oid_from.", oid_for = ".$oid_for.", date = ".$date." WHERE id = '".$letter_id."' LIMIT 1");
	} else {
		$db->query("INSERT INTO ".DB_PREFIX."_letter (`type`, `uid`, `oid_from`, `oid_for`, `date`, `date_create`) VALUES ('".$type."', '".$uid."', ".$oid_from.", ".$oid_for.", ".$date.", '".$date_create."')");
		$letter_id = $db->insert_id();
	}

	$attribute = [];
    $attribute["name"] = "1";
    $attribute["file"] = $file;
    $attribute["module"] = 'letter';
    $attribute["mod"] = 'letter';
    $attribute["id"] = $letter_id;
    $attribute["comm_id"] = $letter_id;
	uploaderMaster("download", $attribute);

	$type_text = getTypeLetter("eng", $type);
	
	header("Location: /administration/letter/".$type_text);
}