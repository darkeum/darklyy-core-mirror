<?php
function add()
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;

	$adminTpl->admin_head('Заявки на расчёт | Добавить заявку');
	?>
	<link rel="stylesheet" href="/usr/modules/request/admin/asset/css/add.css">
	<section id="content" class="animated fadeIn">
		<div class="panel panel-default">
			<div class="panel-body">
				<form class="form-horizontal admin-form" action="/administration/module/request/save" method="POST">
					<div class="form-group">
						<label class="control-label col-sm-3" for="request">Заголовок:</label>
						<div class="col-sm-6">
							<input type="text" placeholder="Введите заголовок..." class="form-control" id="title" name="title">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3" for="request">Текст заявки:</label>
						<div class="col-sm-6">
							<textarea class="form-control" id="request" name="request" placeholder="Введите текст заявки..."></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3" for="request">Объект:</label>
						<div class="col-sm-6">
							<?php
								oSelect('jsAdminTpl', 'object_id', null, 'getObjectToSelect');
								oSelect("select", "object_id", null, "getObjectToSelect");
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3" for="request">Тип заявки:</label>
						<div class="col-sm-6">
							<select id="type" name="type" class="form-control">
								<option value="1">Свободная</option>
								<option value="2">Атри-проект</option>
								<option value="3">Расчет теплообменника</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3" for="">Приложить файлы:</label>
						<div class="col-sm-6">
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
						<div class="col-sm-offset-3 col-sm-9">
							<button type="submit" class="btn btn-success">Создать заявку</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
	<?php
	$adminTpl->footIncludes[] = '<script src="/usr/modules/request/admin/asset/js/tinymce/tinymce.min.js"></script>';
	$adminTpl->footIncludes[] = '<script src="/usr/modules/request/admin/asset/js/add.js"></script>';
	$adminTpl->admin_foot();
}

function save()
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url, $designers;
	$title = isset($_POST["title"]) ? "'".$_POST["title"]."'" : 'null';
	$request = isset($_POST["request"]) ? "'".$_POST["request"]."'" : 'null';
	$object_id = isset($_POST["object_id"]) ? "'".$_POST["object_id"]."'" : 'null';
	$type = isset($_POST["type"]) ? "'".$_POST["type"]."'" : 'null';
	$file = isset($_POST["file"]) ? $_POST["file"] : false;
	$uid_dir = $core->auth->user_info['id'];
	$date = date("Y-m-d H:i:s");
	$db->query("INSERT INTO JMY_request (`uid_dir`, title, `request`, object, type, `date`) VALUES ('".$uid_dir."', ".$title.", ".$request.", ".$object_id.", ".$type.", '".$date."')");
	$iid = $db->insert_id();

	$attribute = [];
	$attribute["name"] = "1";
	$attribute["file"] = $file;
	$attribute["module"] = 'request';
	$attribute["mod"] = 'request';
	$attribute["id"] = $iid;
	$attribute["comm_id"] = $iid;
	uploaderMaster("download", $attribute);

	
	$attr = [];
	$attr['uid'] = $designers;
	$attr['title'] = 'Новая заявка на расчет!';
	$attr['text'] = "Новая заявка на расчет";
	$attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
	$attr['url'] = config('app.url').'/administration/module/request/view/'.$iid;
	$attr['pNotif'] = true;
	$attr['pPush'] = true;
	$attr['module'] = "request";
    $attr['pid'] = $iid;
	sendAllNotif($attr);

	header("Location: /administration/module/request/view/".$iid);
}
