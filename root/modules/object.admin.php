<?php

	/**
		* @name        JMY CORE
		* @link        https://jmy.su/
		* @copyright   Copyright (C) 2012-2018 JMY LTD
		* @license     LICENSE.txt (see attached file)
		* @version     VERSION.txt (see attached file)
		* @author      Komarov Ivan
	*/

	if (!defined('ADMIN_ACCESS')) {
		header('Location: /');
		exit;
	}


	use \Boot\Support\Facades\Auth;

	$core->loadModLangADM('object');
	function main()
	{
		global $adminTpl, $config, $core, $admin_conf, $db, $lang;
		$adminTpl->admin_head($lang['object_list']);
		$lnn = 'Объекты';
		$adminTpl->admin_head('Менеджер объектов | '.$lnn);
		$attributes = array();
        $attributes['opts'] = 'open';
        $attributes['module'] = '';
        $attributes['module_name'] = 'object';
        $attributes['module_open'] = 'view';
		load_grid('jqObject', $lnn, $attributes);
		$setting = [
			"mod" => "jsAdminTpl",
			"name" => "uid",
			"id" => Auth::id(),
			"ajaxUrl" => "getUsersAllToSelect",
			"return" => true,
		];
		oSelectCustom($setting);
		$setting["mod"] = "select";
		$select = oSelectCustom($setting);
		echo <<<HTML
			<div class="modal fade admin-form" id="bindModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title" id="exampleModalLabel">Привязка</h5>
			      </div>
			      <div class="modal-body">
					<form id="bindModalForm">
						{$select}
						<div id="bindModalDiv"></div>
						<div class="mt20">
							<h4>Список привязанных пользователй</h4>
							<ul id="bindModalList"></ul>
						</div>
					</form>
			      </div>
			      <div class="modal-footer">
				  	<button type="button" class="btn btn-primary" onclick="bindModal.send();">Привязать</button>
			        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			      </div>
			    </div>
			  </div>
			</div>

			<script>
				const bindModal = {
					modal: $("#bindModal"),
					async show(object){
						this.modal.modal("show");
						$("#bindModalDiv").html(`<input type="hidden" name="object" value="\${object}">`);
						const list = await this.load_list(object);
						$("#bindModalList").html(list);
					},
					async send(){
						const data = $("#bindModalForm").serialize();
						this.modal.modal("hide");
						await this.send_data(data)
					},
					async send_data(data){
						await $.post("/administration/object/bind_users_object", data);
					},
					async load_list(object){
						return await (await fetch(`/administration/object/users_object_list/\${object}`)).text();
					}
				}
			</script>
HTML;
		$adminTpl->admin_foot();
	}

	function map()
	{
		global $adminTpl, $config, $core, $admin_conf, $db, $lang;
		$adminTpl->admin_head($lang['object_map']);
		echo '<div id="content" class="animated fadeIn">';
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE coords != '' ORDER BY id DESC");
		if($db->numRows($query) > 0)
		{
			echo '<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
			<div class="panel panel-dark panel-border top">
			<div class="panel-heading"><span class="panel-title">'.$lang['object_map'].':</span></div>
			<div class="panel-body pn table-responsive">
			<script type="text/javascript">
			function init() {
			
			var myMap = new ymaps.Map(\'map\', {
			center: [56.852556, 53.211863],
			zoom: 11,
			controls: [\'zoomControl\']
			}),
			// Создаем коллекцию.
			myCollection = new ymaps.GeoObjectCollection(),
			// Создаем массив с данными.
			myPoints = [';
			while($object = $db->getRow($query))
			{
				echo '	{ coords: ['.$object['coords'].'], text: \''.$object['name'].'\' },';

			}
			echo '
			];
			
			// Заполняем коллекцию данными.
			for (var i = 0, l = myPoints.length; i < l; i++) {
			var point = myPoints[i];
			myCollection.add(new ymaps.Placemark(
			point.coords, {
			balloonContentBody: point.text
			}
			));
			}
			
			// Добавляем коллекцию меток на карту.
			myMap.geoObjects.add(myCollection);
			
			// Создаем экземпляр класса ymaps.control.SearchControl
			var mySearchControl = new ymaps.control.SearchControl({
			options: {
			// Заменяем стандартный провайдер данных (геокодер) нашим собственным.
			provider: new CustomSearchProvider(myPoints),
			// Не будем показывать еще одну метку при выборе результата поиска,
			// т.к. метки коллекции myCollection уже добавлены на карту.
			noPlacemark: true,
			resultsPerPage: 5
			}});
			
			// Добавляем контрол в верхний правый угол,
			myMap.controls
			.add(mySearchControl, { float: \'right\' });
			
			}
			
			
			// Провайдер данных для элемента управления ymaps.control.SearchControl.
			// Осуществляет поиск геообъектов в по массиву points.
			// Реализует интерфейс IGeocodeProvider.
			function CustomSearchProvider(points) {
			this.points = points;
			}
			
			// Провайдер ищет по полю text стандартным методом String.ptototype.indexOf.
			CustomSearchProvider.prototype.geocode = function (request, options) {
			var deferred = new ymaps.vow.defer(),
			geoObjects = new ymaps.GeoObjectCollection(),
			// Сколько результатов нужно пропустить.
			offset = options.skip || 0,
			// Количество возвращаемых результатов.
			limit = options.results || 20;
			
			var points = [];
			// Ищем в свойстве text каждого элемента массива.
			for (var i = 0, l = this.points.length; i < l; i++) {
			var point = this.points[i];
			if (point.text.toLowerCase().indexOf(request.toLowerCase()) != -1) {
			points.push(point);
			}
			}
			// При формировании ответа можно учитывать offset и limit.
			points = points.splice(offset, limit);
			// Добавляем точки в результирующую коллекцию.
			for (var i = 0, l = points.length; i < l; i++) {
			var point = points[i],
			coords = point.coords,
			text = point.text;
			
			geoObjects.add(new ymaps.Placemark(coords, {
			name: text + \' name\',
			description: text + \' description\',
			balloonContentBody: \'<p>\' + text + \'</p>\',
			boundedBy: [coords, coords]
			}));
			}
			
			deferred.resolve({
			// Геообъекты поисковой выдачи.
			geoObjects: geoObjects,
			// Метаинформация ответа.
			metaData: {
			geocoder: {
			// Строка обработанного запроса.
			request: request,
			// Количество найденных результатов.
			found: geoObjects.getLength(),
			// Количество возвращенных результатов.
			results: limit,
			// Количество пропущенных результатов.
			skip: offset
			}
			}
			});
			
			// Возвращаем объект-обещание.
			return deferred.promise();
			};
			
			ymaps.ready(init);
			</script>
			
			';

			echo '
			<div id="map" style="width: 100%; height: 500px"></div> 			
			</div>
			</div>';
		}
		else
		{
			$adminTpl->info('В данный момент у вас нет задач для просмотра!', 'empty', null, $lang['cats_list'], $lang['cats_add'], ADMIN.'/cats/add');
		}
		echo'</div>';
		$adminTpl->admin_foot();
	}

	function relations($id = null, $task = false)
	{
		global $adminTpl, $config, $core, $admin_conf, $db, $lang;
		if(isset($id))
		{
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE id = '" . $id . "'");
			$ccid = $id;
			$cat = $db->getRow($query);
			//dd($cat['id']);
			$name = prepareTitle($cat['name']);
			$id = $cat['id'];
			$address = $cat['address'];

			$comment = $cat['comment'];
			$preview = $cat['preview'];
			$id_zas = $cat['id_zas'];
			$id_pod = $cat['id_pod'];
			$id_gpr = $cat['id_gpr'];
			$id_spr = $cat['id_spr'];
			$id_mso = (isset($cat['id_mso']) ? $cat['id_mso'] : '');
			$id_sys = (isset($cat['id_sys']) ? $cat['id_sys'] : '');
			$id_itp = (isset($cat['id_itp']) ? $cat['id_itp'] : '');
			$id_mts = (isset($cat['id_mts']) ? $cat['id_mts'] : '');
			$type = explode(",", $cat['cat']);
			$status = $cat['status'];
			$dosave = $lang['object_edit_do'];

			$lln = $lang['object_edit'];
		}
		$adminTpl->subNavIncludes[] = '<div class="mt-30 hidden-xs"><a href="/'.ADMIN.'/object/view/'.$id.'" class="btn btn-default btn-sm fw600 ml10"><span class="fa fa-plus pr5"></span> Информация об объекте</a><a href="/'.ADMIN.'/object/task/'.$id.'" class="btn btn-default '.($task  ? 'active' : '').' btn-sm fw600 ml10"><span class="glyphicon glyphicon-ok-sign pr5"></span> Задачи</a><a href="/'.ADMIN.'/object/sales/'.$id.'" class="btn btn-default btn-sm fw600 ml10"><span class="glyphicon glyphicon-shopping-cart pr5"></span> Заказы</a><a href="/'.ADMIN.'/object/relations/'.$id.'" class="btn btn-default '.($task  ? '' : 'active').' btn-sm fw600 ml10"><span class="glyphicon glyphicon-link pr5"></span> Все связи по объекту</a></div>';

		$adminTpl->admin_head($lang['object_map']);

		echo '<section id="content" class="table-layout animated fadeIn">
		<!-- begin: .tray-center-->
		<div class="tray tray-center">
		<!-- Begin: Content Header-->
		<div class="content-header">
		<h2>'.($task ? 'Задачи по объекту' : 'Связи по объекту').'</h2>
		<p class="lead">'. $name.'</p>
		</div>
		<!-- Validation Example-->
		<div style="padding-bottom: 175px;" class="admin-form theme-primary mw1000 center-block">
		<div class="panel heading-border panel-primary">              
		<div class="panel-body bg-light">
		
		<!-- .section-divider-->
		<div id="spy1" class="section row">';

		global $recursiveArray;
		$recursiveArray = [];
		recursiveOutput(0, $id, 'object');

		recursiveOutputGet();

		// Дубликат?...
		/*
		$i = 0;
		// module_in
		$query = $db->query("SELECT * FROM ". DB_PREFIX ."_relations WHERE (module_out = 'object' ".($task ? 'AND module_in = \'task\'' : '')." AND pid_out = '". $id ."') ORDER BY id DESC");
        //dd($db->getRow($query));
        //dd($db->numRows($query));
        //dd($query);
        //dd($cat);
        //dd(intval($task['date']));

        if($db->numRows($query) > 0)
        {
            while($object = $db->getRow($query))
            {
                //dd($object);

                $i++;
                switch($object['module_in'])
                {
                    case "task":
                        $queryR = $db->query("SELECT * FROM ".DB_PREFIX."_".$object['module_in']." WHERE id = '" . $object['pid_in'] . "'");
                        $task = $db->getRow($queryR);

                        //echo 'module_in-task-';
                        //echo $object['pid_in'] . '-';

                        echo '<a href="'.ADMIN.'/'.$object['module_in'].'/view/'.$object['pid_in'].'">Задача №'.$task['id'].' от '.shortUserName($task['fromid']).' для '.shortUserName($task['toid']).' ('.formatDate($task['date'], true).')</a><br>';
                        break;

                    case "object":
                        $queryR = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE id = '" . $object['pid_in'] . "'");
                        $task = $db->getRow($queryR);

                        //echo 'module_in-object-';

                        echo '<a href="'.ADMIN.'/'.$object['module_in'].'/view/'.$object['pid_in'].'">Объект '.$task['name'].' ('.$task['address'].')</a><br>';
                        break;

                    case "sales":
                        $queryR = $db->query("SELECT * FROM ".DB_PREFIX."_sales WHERE id = '" . $object['pid_in'] . "'");
                        $task = $db->getRow($queryR);

                        echo '<a href="'.ADMIN.'/module/'.$object['module_in'].'/order/'.$object['pid_in'].'">Заказ PN-'.$task['id'].' от ('.$task['date'].')</a><br>';
                        break;

                    case "transfer":
                        $queryR = $db->query("SELECT * FROM ".DB_PREFIX."_stock_transfer WHERE id = '" . $object['pid_in'] . "'");
                        $task = $db->getRow($queryR);

                        echo '<a href="'.ADMIN.'/module/storage/transfer/detail/'.$object['pid_in'].'">Перемещение №TR-'.$task['id'].' от '.$task['date'].'</a><br>';
                        break;

                }
            }
        }

		// module_out
		$query = $db->query("SELECT * FROM ". DB_PREFIX ."_relations WHERE (module_in = 'object' ".($task ? 'AND module_out = \'task\'' : '')." AND pid_in = '". $id ."') ORDER BY id DESC");
		if($db->numRows($query) > 0)
		{
			while($object = $db->getRow($query))
			{
				$i++;
				switch($object['module_out'])
				{
					case "task":
					$queryR = $db->query("SELECT * FROM ".DB_PREFIX."_".$object['module_out']." WHERE id = '" . $object['pid_out'] . "'");
					$task = $db->getRow($queryR);

					//echo 'module_out-task-';
					//echo $object['pid_in'] . '-';

					echo '<a href="'.ADMIN.'/'.$object['module_in'].'/view/'.$object['pid_out'].'">Задача №'.$task['id'].' от '.shortUserName($task['fromid']).' для '.shortUserName($task['toid']).' ('.formatDate($task['date'], true).')</a><br>';
					break;

					case "object":
					$queryR = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE id = '" . $object['pid_out'] . "'");
					$task = $db->getRow($queryR);
					//echo 'module_out-object-';
					echo '<a href="'.ADMIN.'/'.$object['module_out'].'/view/'.$object['pid_out'].'">Объект '.$task['name'].' ('.$task['address'].')</a><br>';
					break;

					case "sales":
						$queryR = $db->query("SELECT * FROM ".DB_PREFIX."_sales WHERE id = '" . $object['pid_in'] . "'");
						$task = $db->getRow($queryR);

						echo '<a href="'.ADMIN.'/module/'.$object['module_in'].'/order/'.$object['pid_in'].'">Заказ PN-'.$task['id'].'  от ('.$task['date'].')</a><br>';
					break;

					case "transfer":
					$queryR = $db->query("SELECT * FROM ".DB_PREFIX."_stock_transfer WHERE id = '" . $object['pid_out'] . "'");
					$task = $db->getRow($queryR);
					echo '<a href="'.ADMIN.'/module/storage/transfer/detail/'.$object['pid_out'].'">Перемещение №TR-'.$task['id'].' от '.$task['date'].'</a><br>';
					break;

				}
			}
		}

		if($i == 0)
		{
			if($task)
			{
				echo 'К сожалению задач по данной заказу не найдено';
			}
			else
			{
				echo 'К сожалению связей по данной заказу не найдено';
			}
		}
		//*/

        echo'</div></div></div></div></div></section>';

		$adminTpl->admin_foot();
	}

	function allFile($id){
		global $adminTpl, $config, $core, $admin_conf, $db, $lang;

		if(isset($id))
		{
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE id = '" . $id . "'");
			$ccid = $id;
			$cat = $db->getRow($query);
			$name = prepareTitle($cat['name']);
			$id = $cat['id'];
			$address = $cat['address'];

			$comment = $cat['comment'];
			$preview = $cat['preview'];
			$id_zas = $cat['id_zas'];
			$id_pod = $cat['id_pod'];
			$id_gpr = $cat['id_gpr'];
			$id_spr = $cat['id_spr'];
			$id_mso = (isset($cat['id_mso']) ? $cat['id_mso'] : '');
			$id_sys = (isset($cat['id_sys']) ? $cat['id_sys'] : '');
			$id_itp = (isset($cat['id_itp']) ? $cat['id_itp'] : '');
			$id_mts = (isset($cat['id_mts']) ? $cat['id_mts'] : '');
			$type = explode(",", $cat['cat']);
			$status = $cat['status'];
			$dosave = $lang['object_edit_do'];

			$lln = $lang['object_edit'];
		}
		$adminTpl->subNavIncludes[] = '<div class="mt-30 hidden-xs"><a href="/'.ADMIN.'/object/view/'.$id.'" class="btn btn-default btn-sm fw600 ml10"><span class="fa fa-plus pr5"></span> Информация об объекте</a><a href="/'.ADMIN.'/object/task/'.$id.'" class="btn btn-default btn-sm fw600 ml10"><span class="glyphicon glyphicon-ok-sign pr5"></span> Задачи</a><a href="/'.ADMIN.'/object/sales/'.$id.'" class="btn btn-default btn-sm fw600 ml10"><span class="glyphicon glyphicon-shopping-cart pr5"></span> Заказы</a><a href="/'.ADMIN.'/object/relations/'.$id.'" class="btn btn-default btn-sm fw600 ml10"><span class="glyphicon glyphicon-link pr5"></span> Все связи по объекту</a><a href="/'.ADMIN.'/object/file/'.$id.'" class="btn btn-default btn-sm fw600 ml10 active"><span class="glyphicon glyphicon-link pr5"></span> Все файлы</a></div>';
		$adminTpl->admin_head($lang['object_map']);
		echo '<section id="content" class="table-layout animated fadeIn">
		<!-- begin: .tray-center-->
		<div class="tray tray-center">
		<!-- Begin: Content Header-->
		<div class="content-header">
		<h2>Все файлы</h2>
		<p class="lead">'. $name.'</p>
		</div>
		<!-- Validation Example-->
		<div style="padding-bottom: 175px;" class="admin-form theme-primary mw1000 center-block">
		<div class="panel heading-border panel-primary">              
		<div class="panel-body bg-light">
		
		<!-- .section-divider-->
		<div id="spy1" class="section row">';
		$i = 0;
		$query = $db->query("SELECT pid_in, module_in FROM JMY_relations WHERE pid_out = '".$id."' AND module_out = 'object'");
		while($r = $db->getRow($query)){
			$attachQuery = $db->query("SELECT id, name FROM JMY_attach WHERE `module` = '".$r["module_in"]."' AND pid = '".$r["pid_in"]."'");
			while($z = $db->getRow($attachQuery)){
				$i++;
				echo '<div><a href="/download/'.$z["id"].'">'.$z["name"].'</a></div>';
			}
		}
		if($i == 0)
		{
			echo 'К сожалению файлы отсутствуют';
		}
		echo' 
		</div>
		</div>
		</div>
		</div>
		</div>
		</section>
		';
		$adminTpl->admin_foot();
	}

	function sales($id = null, $task = false)
	{
		global $adminTpl, $config, $core, $admin_conf, $db, $lang;
		// require_once(ROOT."/usr/modules/sales/admin/sub/order.php");

		if(isset($id))
		{
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE id = '" . $id . "'");
			$ccid = $id;
			$cat = $db->getRow($query);
			$name = prepareTitle($cat['name']);
			$id = $cat['id'];
			$address = $cat['address'];
			$comment = $cat['comment'];
			$preview = $cat['preview'];
			$id_zas = $cat['id_zas'];
			$id_pod = $cat['id_pod'];
			$id_gpr = $cat['id_gpr'];
			$id_spr = $cat['id_spr'];
			$id_mso = (isset($cat['id_mso']) ? $cat['id_mso'] : '');
			$id_sys = (isset($cat['id_sys']) ? $cat['id_sys'] : '');
			$id_itp = (isset($cat['id_itp']) ? $cat['id_itp'] : '');
			$id_mts = (isset($cat['id_mts']) ? $cat['id_mts'] : '');
			$type = explode(",", $cat['cat']);
			$status = $cat['status'];
			$dosave = $lang['object_edit_do'];
			$lln = $lang['object_edit'];
		}

		$adminTpl->subNavIncludes[] = '<div class="mt-30 hidden-xs"><a href="/'.ADMIN.'/object/view/'.$id.'" class="btn btn-default btn-sm fw600 ml10"><span class="fa fa-plus pr5"></span> Информация об объекте</a><a href="/'.ADMIN.'/object/task/'.$id.'" class="btn btn-default '.($task  ? 'active' : '').' btn-sm fw600 ml10"><span class="glyphicon glyphicon-ok-sign pr5"></span> Задачи</a><a href="/'.ADMIN.'/object/sales/'.$id.'" class="btn btn-default active btn-sm fw600 ml10"><span class="glyphicon glyphicon-shopping-cart pr5"></span> Заказы</a><a href="/'.ADMIN.'/object/relations/'.$id.'" class="btn btn-default btn-sm fw600 ml10"><span class="glyphicon glyphicon-link pr5"></span> Все связи по объекту</a></div>';
		$adminTpl->admin_head($lang['object_map']);
		echo '<section id="content" class="table-layout animated fadeIn">
		<!-- begin: .tray-center-->
		<div class="tray tray-center">
		<!-- Begin: Content Header-->
		<div class="content-header">
		<h2>Заказы по объекту</h2>
		<p class="lead">'. $name.'</p>
		</div>
		<!-- Validation Example-->
		<div style="padding-bottom: 175px;" class="admin-form theme-primary mw1000 center-block">
		<div class="collapse-group">
		<div class="panel heading-border panel-primary">              
		<div class="panel-body bg-light" style="    padding: 0px;">
		<div class="panel-heading" style="padding: 20px 10px 10px 10px; background-color: #e5e5e557;">
		<span class="panel-title" style="font-size: 16px;">Список заказов:</span>  
		<div class="widget-menu pull-right">											
		<div class="btn-group">		
		<button type="button" class="btn btn-xs btn-success btn-block open-button">Открыть все</button>
		</div> 		<div class="btn-group">	
		<button type="button" class="btn btn-xs btn-success btn-block close-button">Закрыть все</button>					
		</div> 	
		</div>			
		</div>
		<!-- .section-divider-->
		<div id="spy1" class="section" style="padding-top: 20px;">
		
		<style>
		.admin-form .panel-title i{
		font-size: 1.1em;
		margin-right: 0; 
		}
		.hideAllToTwo{
		display: none;
		}
		</style>
		
		';
		$i = 0;
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_relations WHERE (module_out = 'object'  AND pid_out = '".$id."') OR (module_in = 'object'  AND pid_in = '".$id."') ORDER BY id DESC");
		//echo "SELECT * FROM ".DB_PREFIX."_relations WHERE (module_out = 'object'  AND pid_out = '".$id."') OR (module_in = 'object'  AND pid_in = '".$id."') ORDER BY id DESC";

        //dd($db->getRow($query)); // Возврат связи

		if($db->numRows($query) > 0)
		{

			while($object = $db->getRow($query))
			{
				$i++;
				if($object['module_out'] == 'object')
				{
					$mmm =  $object['module_in'];
					$iid = $object['pid_in'];
				}
				else
				{
					$mmm =  $object['module_out'];
					$iid = $object['pid_out'];
				}
				//print_r($object);
				switch($mmm)
				{

					case "sales":
					$queryR = $db->query("SELECT * FROM ".DB_PREFIX."_sales WHERE id = '" . $iid . "'");
					$task = $db->getRow($queryR);

					// dd($task); // Возврат продажи

					echo '
					
					<div class="panel panel-default" style="margin-bottom: -1px;">
					<div class="panel-heading" role="tab" id="headingOne" style="padding: 10px 10px 10px; overflow:visible;">
					<h4 class="panel-title" style="font-size: 16px;">
					<a role="button" data-toggle="collapse" href="#collapse_'.$task['id'].'" aria-expanded="true" aria-controls="collapse_'.$task['id'].'" class="trigger collapsed">
					Заказ '.$task['numb'].' от ('.$task['date'].') 
					</a>
					— <span class="changeTitleSales" style="cursor: pointer; border-bottom: 1px black dashed; '.(empty($task["title"]) ? "font-style: italic; color: #DD1144;" : "").'" data-id="'.$task['id'].'">'.(empty($task["title"]) ? 'Отсутствует' : $task["title"]).'</span> 
					<a href="'.ADMIN.'/module/sales/order/'.$task['id'].'" target="_blank" class="text-right pull-right">
					Перейти
					</a>
					</h4>	
					
					<div style="line-height: 20px; margin-top: 10px;">';
					$zzz = $db->query("SELECT c.*, u.surname as surname, u.name as name, u.ochestvo as ochestvo FROM `" . DB_PREFIX . "_comments_admin` as c LEFT JOIN `" . DB_PREFIX . "_users` as u ON(c.uid = u.id) WHERE c.module = 'sales' AND c.module_id = '".$task['id']."'");

					$p = 1;
					while($zz = $db->getRow($zzz))
					{
						if($p === 2){
							echo '<div class="hideAllToTwo">';
						}
						echo '<div>
						<i class="fa fa-comment-o" aria-hidden="true"></i>
						<span>'.$zz['date'].' '.$zz['surname'].' '.mb_substr($zz['name'], 0, 1).'. '.mb_substr($zz['ochestvo'], 0, 1).'.'.'</span>
						<span><i>'.$zz['text'].'</i></span>
						<div>';
						$queryTC = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE pid = '".$zz['id']."' AND `module` = 'sales'");
						if($db->numRows($queryTC) > 0){
							while($myr = $db->getRow($queryTC)){
								echo '<a target="_blank" href="/download/'.$myr['id'].'">'.$myr['name'].'</a><br>';
							}
						}
						echo '</div>
						</div>';
						$p++;
					}
					$p--;
					if($p > 1){
						echo '</div><a href="javascript:void(0);" class="showHideAllToTwo">Показать все</a>';
					}

					echo '</div> 
					
					</div>

					<div id="collapse_'.$task['id'].'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
					<div class="panel-body" style="padding: 0px;">
					<table class="table table-striped" id="tablesForm">
                    <thead>';
					echo '<tr>
					<th class="w40"><span class="pd-l-sm"></span>#</th>
					<th class="w200">Номенклатура</th>
					<th class="w150">Артикул</th>
					<th class="w150">Количество</th>							
					<th class="w150">Вид цены</th>
					<th class="w150">Цена</th>
					<th class="w200">Скидка</th>
					<th class="w200">Сумма</th>
					<th class="w40 text-center"><span class="fa fa-pencil fa-lg"></span></th>		
					<th class="w40 text-center">' . $lang['status'] .'</th>						
					
					</tr>';
					// ajax_order_table_head($iid);
                    echo '</thead>
                    <tbody id="tov_list">';

					$queryPN = $db->query("SELECT s.*, d.title, p.price FROM ".DB_PREFIX."_sales_detail as s LEFT JOIN ".DB_PREFIX."_catalog AS d on(d.id=s.cid) LEFT JOIN ".DB_PREFIX."_ver_device_price as p on(d.id=p.vdid) WHERE s.sales_id = '".$task['id']."' AND s.status != '0' AND s.del = '0'");
					//dd($db->getRow($queryPN));
					//dd($db->numRows($queryPN));

					if($db->numRows($queryPN) > 0)
					{
						$i=0;
						$total = 0;
						while($content = $db->getRow($queryPN))
						{
							$i++;
							$flag = true;
							$status_icon = '';
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
							$total = $total + ($content['total']);

							//' . $dn_array[$content['dn']] . '

							echo '
							<tr>
							<td data-toggle="tooltip" title="' . $content['id'] . '"><span class="pd-l-sm"></span>' . $i . '</td>
							<td>' . $content['s_name'] . '</td>
							<td>' . $content['s_sku'] . '</td>
							<td>' . $content['count'] . '</td>				
							<td>0</td>
							<td>' . $content['s_price']. ' рублей.</td>		
							<td>' . $content['discount']. '%</td>				
							<td>' . $content['total']. ' рублей.</td>	
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
							
							</tr>';
						}
						echo '<tr>
						<td colspan="7" class="text-right"><b>Итого:</b></td>
						<td colspan="5" class="text-left"><b>'.$total.' рублей.</b></td>
						</tr>';

					}
					else
					{
						echo '<tr><td colspan="12">У данной заявки еще не заполнен список приборов!</td></tr>';
					}

					// ajax_order_tovar_list($iid);

					echo '	</tbody>
					
					</table> 
					</div>
					</div>
					
					</div>
					
					
					
					
					
					';
					break;

				}
			}
		}


		if($i == 0)
		{
			if($task)
			{
				echo 'К сожалению задач по данному заказу не найдено';
			}
			else
			{
				echo 'К сожалению связей по данному заказу не найдено';
			}
		}
		echo'
		</div>
		</div>
		</div>
		</div>
		</div>
		</section>
		';
		$adminTpl->js_code[] = '$(".open-button").on("click", function() {
		$(this).closest(\'.collapse-group\').find(\'.collapse\').collapse(\'show\');
		});
		
		$(".close-button").on("click", function() {
		$(this).closest(\'.collapse-group\').find(\'.collapse\').collapse(\'hide\');
		});
		
		$(document).on("click", ".changeTitleSales", function(){
		let value = $(this).text();
		if(value === "Отсутствует" && $(this).data("original-title") === undefined){
		$(this).text("");
		}
		let id = $(this).data("id");
		$(this).editable({
		type: "text",
		pk: id,
		url: "/ajax.php?do=changeTitleSales",
		emptytext: "Отсутствует",
		title: "Введите наименование",
		});
		$(this).editable("show");
		});
		
		$(document).on("click", ".showHideAllToTwo", function(){
		$(this).prev().slideToggle();
		});
		
		';
		xeditableInit();
		$adminTpl->admin_foot();
	}


	function view2($id = null, $view = false)
	{
		global $adminTpl, $config, $core, $admin_conf, $db, $lang;
		$lang['object_status_ok'] = 'object_status_ok';
		$lang['object_status_mod'] = 'object_status_mod';
		$lang['object_type_1'] = 'object_type_1';
		$lang['object_type_2'] = 'object_type_2';
		$lang['object_type_3'] = 'object_type_3';
		$lang['object_no_info'] = 'object_no_info';
		$lang['object_mini'] = 'object_mini';
		$lang['object_mso'] = 'object_mso';
		$lang['object_sys'] = 'object_sys';
		$lang['object_itp'] = 'object_itp';
		$lang['object_mts'] = 'object_mts';
		if(isset($id))
		{
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE id = '" . $id . "'");
			$ccid = $id;
			$cat = $db->getRow($query);
			$name = prepareTitle($cat['name']);
			$id = $cat['id'];
			$address = $cat['address'];
			$comment = $cat['comment'];
			$preview = $cat['preview'];
			$id_con = $cat['id_con'];
			$id_zas = $cat['id_zas'];
			$id_pod = $cat['id_pod'];
			$id_gpr = $cat['id_gpr'];
			$id_spr = $cat['id_spr'];
			$id_mso = (isset($cat['id_mso']) ? $cat['id_mso'] : '');
			$id_sys = (isset($cat['id_sys']) ? $cat['id_sys'] : '');
			$id_itp = (isset($cat['id_itp']) ? $cat['id_itp'] : '');
			$id_mts = (isset($cat['id_mts']) ? $cat['id_mts'] : '');
			$type = explode(",", $cat['cat']);
			$client = explode(",", $cat['id_client']);
			$status = $cat['status'];
			$dosave = $lang['object_edit_do'];
			$lln = $lang['object_edit'];
		}
		$queryO = $db->query("SELECT id, name FROM ".DB_PREFIX."_organizations ORDER BY name ASC");
		$org_array = array();
		if($db->numRows($queryO) > 0)
		{
			while($org = $db->getRow($queryO))
			{
				$org_array[$org['id']] = $org['name'];
			}
		}
		$adminTpl->subNavIncludes[] = '<div class="mt-30 hidden-xs"><a href="/'.ADMIN.'/object/view/'.$id.'" class="btn btn-default active btn-sm fw600 ml10"><span class="fa fa-plus pr5"></span> Информация об объекте</a><a href="/'.ADMIN.'/object/task/'.$id.'" class="btn btn-default btn-sm fw600 ml10"><span class="glyphicon glyphicon-ok-sign pr5"></span> Задачи</a><a href="/'.ADMIN.'/object/sales/'.$id.'" class="btn btn-default btn-sm fw600 ml10"><span class="glyphicon glyphicon-shopping-cart pr5"></span> Заказы</a><a href="/'.ADMIN.'/object/relations/'.$id.'" class="btn btn-default btn-sm fw600 ml10"><span class="glyphicon glyphicon-link pr5"></span> Все связи по объекту</a><a href="/'.ADMIN.'/object/file/'.$id.'" class="btn btn-default btn-sm fw600 ml10"><span class="glyphicon glyphicon-link pr5"></span> Все файлы</a></div>';
		$adminTpl->admin_head($name);
		echo '<section id="content" class="table-layout animated fadeIn">
		<!-- begin: .tray-center-->
		<div class="tray tray-center">
		<!-- Begin: Content Header-->
		<div class="content-header">
		<h2>'.$name.'</h2>
		<p class="lead">'.$address.'</p>
		</div>
		<!-- Validation Example-->
		<div style="padding-bottom: 175px;" class="admin-form theme-primary mw1000 center-block">
		<div class="panel heading-border panel-primary">
		<form id="admin-form" method="post" action="/">
		<div class="panel-body bg-light">
		
		<!-- .section-divider-->
		<div id="spy1" class="section row">
		
		<div class="col-md-8">
		<p>
		<div class="section-divider mt20 mb40"><span> Основная информация по объекту</span></div>
		<b>Наименование объекта: </b>'.$name.'</br>
		<b>Адрес объекта: </b>'.$address.'</br>
		<b>Тип объекта: </b>'.(in_array("1", $type) ? $lang['object_type_1'] : '').' '.(in_array("2", $type) ? $lang['object_type_2'] : '').' '.(in_array("3", $type) ? $lang['object_type_3'] : '').'</br>
		<b>Статус объекта: </b>'.($status == 1 ? $lang['object_status_ok'] : $lang['object_status_mod']).'</br>
		<b>Закрепление объекта: </b>'.($id_con != 0 ? $org_array[$id_con] : $lang['object_no_info']).'</br>
		<b>Клиенты: </b>';
		$client_list = '';
		foreach ($client as $key => $value)
		{
			if (!empty($client_list)&& !empty($org_array[$value]))
			{
				$client_list .= ', ';
			}
			if (isset($org_array[$value]))
			{
				$client_list .= $org_array[$value];
			}
		}
		echo $client_list.'</br>
		<b>Застройщик: </b>'.($id_zas != 0 ? $org_array[$id_zas] : $lang['object_no_info']).'</br>
		<b>Ген. подрядчик: </b>'.($id_pod != 0 ? $org_array[$id_pod] : $lang['object_no_info']).'</br>
		<b>Ген. проектировщик: </b>'.($id_gpr != 0 ? $org_array[$id_gpr] : $lang['object_no_info']).'</br>
		<b>Суб. проектировщик: </b>'.($id_spr != 0 ? $org_array[$id_spr] : $lang['object_no_info']).'</br>
		<div class="section-divider mt20 mb20"><span> Информация по разделам</span></div>
		<ul class="fa-ul">';

		$section_array = array();
		$section_array['1'] = 'Раздел ТС';
		$section_array['2'] = 'Раздел ОВ';
		$section_array['3'] = 'Раздел ВК';
		$section_array['4'] = 'Раздел ТМ';

		foreach ($section_array as $key => $value)
		{
			$queryOB = $db->query("SELECT * FROM ".DB_PREFIX."_object_section WHERE object_id = '" . $id . "' AND section = '".$key."' LIMIT 1");
			if($db->numRows($queryOB) > 0)
			{
				$resultOB = $db->getRow($queryOB);
				$id_mso[$key] = $resultOB['mso'];
				$id_sys[$key] = $resultOB['sys'];
				$id_itp[$key] = $resultOB['itp'];
				$id_mts[$key] = $resultOB['mts'];
				$sw[$key] = (($resultOB['status'] == 1) ? true : false);
				$li = (($resultOB['status'] == 1) ? '<li><i class="fa-li fa fa-check-square text-primary"></i> '.$value.'</li>' : '<li><i class="fa-li fa fa-check-square text"></i> '.$value.'</li>');
			}
			else
			{
				$li = '<li><i class="fa-li fa fa-check-square text"></i> '.$value.'</li>';
				$sw[$key] = false;
				$id_mso[$key] = 0;
				$id_sys[$key] = 0;
				$id_itp[$key] = 0;
				$id_mts[$key] = 0;
			}
			echo $li;
		}
		echo '</ul>';
		foreach ($section_array as $key => $value)
		{
			if($sw[$key])
			{
				echo '<div class="section-divider mt20 mb40"><span> '.$value.'</span></div>
				<b>'.$lang['object_mso'].': </b>'.($id_mso[$key] != 0 ? $org_array[$id_mso[$key]] : $lang['object_no_info']).'</br>
				<b>'.$lang['object_sys'].': </b>'.($id_sys[$key] != 0 ? $org_array[$id_sys[$key]] : $lang['object_no_info']).'</br>
				<b>'.$lang['object_itp'].': </b>'.($id_itp[$key] != 0 ? $org_array[$id_itp[$key]] : $lang['object_no_info']).'</br>
				<b>'.$lang['object_mts'].': </b>'.($id_mts[$key] != 0 ? $org_array[$id_mts[$key]] : $lang['object_no_info']).'</br>
				';
			}
		}

		$queryA = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE `module` = 'object' AND pid = '".$id."' ORDER BY name ASC");

		if($db->numRows($queryA) > 0)
		{
			echo '			<div class="section-divider mt20 mb40"><span> Прикрепленные файлы</span></div>';
			echo '<ul>';
			while($ffile = $db->getRow($queryA))
			{
				echo '<li id="file_'.$ffile['id'].'"><a href="/download/'.$ffile['id'].'" target="_blank" >'.$ffile['name'].'</a><i data-id="'.$ffile['id'].'" style="color:red; margin-left: 20px; cursor: pointer;" class="removeFile fa fa-times" aria-hidden="true"></i></li>';
			}
			echo '</ul>';
		}

		echo '<div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Удалить файл?</h5>
		</div>
		<div class="modal-body">
        Вы действительно хотите удалить файл?
		</div>
		<div class="modal-footer">
		<button type="button" class="btn btn-danger" data-dismiss="modal" id="delBtn">Удалить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		</div>
		</div>
		</div>
		</div>';

		$adminTpl->js_code[] = '
		$(document).on("click", ".removeFile", function(){
		//console.log("!");
		let id = $(this).data("id");
		$("#delBtn").data("id", id);
		$("#delModal").modal("show");
		});
		
		$(document).on("click", "#delBtn", function(){
		let id = $(this).data("id");
		$.post("/administration/object/delFile", {id:id}, function(){
		$("#file_"+id).remove();
		});
		});
		';


		echo '	</p>
		</div>
		<div class="col-md-4"><img '.($preview ? 'src="'.$config['url'].$preview.'"' : 'data-src="holder.js/100%x147/text:'.$lang['object_mini'].'"').' alt="holder"  class="img-responsive thumbnail mr25">
		
		
		<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
		
		<script type="text/javascript">
		ymaps.ready(init);
		function init(){';
		if (empty($cat['coords']))
		{
			echo 'var geocoder = new ymaps.geocode(
			\''.$cat['address'].'\',
			{ results: 1 }
			);								
			geocoder.then(
			function (res) {
			var coord = res.geoObjects.get(0).geometry.getCoordinates();
			var myMap = new ymaps.Map(\'map\', {
			center: coord,
			zoom: 15
			});	
			var myPlacemark = new ymaps.Placemark(coord);
			myMap.geoObjects.add(myPlacemark);
			}
			);
			';
		}
		else
		{
			echo '
			
			var myMap = new ymaps.Map(\'map\', {
			center: ['.$cat['coords'].'],
			zoom: 15
			});
			var myPlacemark = new ymaps.Placemark(['.$cat['coords'].']);
			myMap.geoObjects.add(myPlacemark);';
		}



		echo'	}</script>
		
		<div id="map" class="img-responsive thumbnail mr25" style="width: 100%; height: 300px"></div> 			
		<button type="button" onclick="window.print();" class="btn btn-xs btn-dark btn-block">Распечатать</button>
		
		</div>
		</div>
		</div>
		
		<div class="panel-footer text-right">
		<a href="'.ADMIN.'/object/edit/'.$id.'" class="button btn-primary"> Редактировать</a>
		</div>
		</form>
		</div>
		</div>
		</div>
		</section>
		';
		$adminTpl->admin_foot();
	}

	function add($cat = null, $view = false)
	{
		global $adminTpl, $config, $core, $admin_conf, $db, $lang;
		if(isset($cat))
		{
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE id = '" . $cat . "'");
			$ccid = $cat;
			$cat = $db->getRow($query);
			$name = prepareTitle($cat['name']);
			$id = $cat['id'];
			$address = $cat['address'];
			$comment = $cat['comment'];
			$coords  = $cat['coords'];
			$preview = $cat['preview'];
			$id_client = $cat['id_client'];
			$id_con = $cat['id_con'];
			$id_zas = $cat['id_zas'];
			$id_pod = $cat['id_pod'];
			$id_gpr = $cat['id_gpr'];
			$id_spr = $cat['id_spr'];

			$id_mso = array();
			$id_sys =  array();
			$id_itp =  array();
			$id_mts =  array();

			$type = explode(",", $cat['cat']);
			$client_arr = explode(",", $cat['id_client']);
			$status = $cat['status'];
			$dosave = $lang['object_edit_do'];
			$lln = $lang['object_edit'];
		}
		else
		{
			$name = "";
			$address 	= "";
			$comment 	= "";
			$id_client 	= "";
			$id_con 	= "";
			$id_zas 	= "";
			$id_pod 	= "";
			$id_gpr 	= "";
			$id_spr 	= "";
			$id_mso 	= "";
			$id_sys 	= "";
			$id_itp 	= "";
			$coords  	= '';
			$id_mts 	= "";
			$client_arr = array();
			$type =	[];
			$status = 1;
			$dosave = 'Добавить объект';
			$lln = 'Добавить объект';
		}
		$validation_array = array(
		'title' => array(
		'required' =>  array('true', '')
		),
		'altname' => array(
		'required' =>  array('true', '')
		)
		);
		$disable = '';
		if ($view)
		{
			$lln = $lang['object_view'];
			$disable = 'disabled';
		}
		$adminTpl->js_code[] = '
		$(".select2-single").select2();
		$("#select2-multiple").select2({
		placeholder: "object_type_choose",
		allowClear: true
        });
		$("#select3-multiple").select2({
		placeholder: "object_client_choose",
		allowClear: true
        });
		';
		$queryO = $db->query("SELECT id, name FROM ".DB_PREFIX."_organizations ORDER BY name ASC");
		$org_list = '';
		$section_array = array();
		$section_array['1'] = 'Раздел ТС';
		$section_array['2'] = 'Раздел ОВ';
		$section_array['3'] = 'Раздел ВК';
		$section_array['4'] = 'Раздел ТМ';
		$sw = [];
		$sw_st = [];
		if(isset($cat))
		{
			foreach ($section_array as $key => $value)
			{
				$queryOB = $db->query("SELECT * FROM ".DB_PREFIX."_object_section WHERE object_id = '" . $id . "' AND section = '".$key."' LIMIT 1");
				if($db->numRows($queryOB) > 0)
				{
					$resultOB = $db->getRow($queryOB);
					$id_mso[$key] = $resultOB['mso'];
					$id_sys[$key] = $resultOB['sys'];
					$id_itp[$key] = $resultOB['itp'];
					$id_mts[$key] = $resultOB['mts'];
					$sw[$key] = (($resultOB['status'] == 1) ? 'checked' : '');
					$sw_st[$key] = (($resultOB['status'] == 1) ? 'block' : 'none');
				}
				else
				{
					$id_mso[$key] = 0;
					$id_sys[$key] = 0;
					$id_itp[$key] = 0;
					$id_mts[$key] = 0;
				}
			}
		}
		else
		{
			foreach ($section_array as $key => $value)
			{
				$sw[$key] = '';
				$sw_st[$key] = 'none';
			}

		}
		$client_list = '';
		$con_list = '';
		$zas_list = '';
		$pod_list = '';
		$gpr_list = '';
		$spr_list = '';
		foreach ($section_array as $key => $value)
		{
			$mso_list[$key]  = '';
			$sys_list[$key]  = '';
			$itp_list[$key]  = '';
			$mts_list[$key]  = '';
		}
		if($db->numRows($queryO) > 0)
		{
			// while($org = $db->getRow($queryO))
			// {
			// 	$client_list .= '<option '.(in_array($org['id'], $client_arr) ? 'selected' : '').' value="'.$org['id'].'">'.$org['name'].'</option>';
			// 	$con_list .= '<option '.(($org['id'] == $id_con) ? 'selected' : '').' value="'.$org['id'].'">'.$org['name'].'</option>';
			// 	$zas_list .= '<option '.(($org['id'] == $id_zas) ? 'selected' : '').' value="'.$org['id'].'">'.$org['name'].'</option>';
			// 	$pod_list .= '<option '.(($org['id'] == $id_pod) ? 'selected' : '').' value="'.$org['id'].'">'.$org['name'].'</option>';
			// 	$gpr_list .= '<option '.(($org['id'] == $id_gpr) ? 'selected' : '').' value="'.$org['id'].'">'.$org['name'].'</option>';
			// 	$spr_list .= '<option '.(($org['id'] == $id_spr) ? 'selected' : '').' value="'.$org['id'].'">'.$org['name'].'</option>';

			// 	foreach ($section_array as $key => $value)
			// 	{
			// 		$mso_list[$key]  .= '<option '.(($org['id'] == $id_mso[$key]) ? 'selected' : '').' value="'.$org['id'].'">'.$org['name'].'</option>';
			// 		$sys_list[$key]  .= '<option '.(($org['id'] == $id_sys[$key]) ? 'selected' : '').' value="'.$org['id'].'">'.$org['name'].'</option>';
			// 		$itp_list[$key]  .= '<option '.(($org['id'] == $id_itp[$key]) ? 'selected' : '').' value="'.$org['id'].'">'.$org['name'].'</option>';
			// 		$mts_list[$key]  .= '<option '.(($org['id'] == $id_mts[$key]) ? 'selected' : '').' value="'.$org['id'].'">'.$org['name'].'</option>';
			// 	}
			// }
		}

		validationInit($validation_array);
		ajaxInit();
		fancyboxInit();
		$cats_arr = $core->aCatList('content');
		$adminTpl->admin_head($lang['object']. ' | ' . $lln);
		echo ' <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
		<section id="content" class="table-layout animated fadeIn">			
		<div class="tray tray-center">
		<style>
		.select2-container--default .select2-selection--single {
		background-color: #f0f0f0;
		border: 1px solid rgba(0, 0, 0, 0.1);
		border-radius: 0;
		height: 38px;
		}
		
		.select2-container--default .select2-selection--single .select2-selection__rendered{
		line-height: 35px;
		padding-left: 8px;}
		</style>
		<form action="{ADMIN}/object/save" enctype="multipart/form-data" role="form" method="POST" name="content" id="admin-form">			
		<div class="panel mb25 mt5">
		<div class="panel-heading br-b-ddd"><span class="panel-title hidden-xs">'. $lln .'</span>
		<ul class="nav panel-tabs-border panel-tabs">
		<li class="active"><a href="#tab1_1" data-toggle="tab">'.$lang['object_main'].'</a></li>
		<li><a href="#tab1_2" data-toggle="tab">Информация</a></li>								
		</ul>
		</div>						
		<div class="panel-body p20 pb10">
		<div class="tab-content pn br-n admin-form">
		<div id="tab1_1" class="tab-pane active">									
		<div class="section row mbn">
		<div class="col-md-9 pl15">
		<div class="section row mb15">
		<div class="col-xs-6">
		<label for="name" class="field prepend-icon">
		<input '.$disable.'  type="text" name="name" class="form-control '.$disable.'" value="'.$name.'" id="name" placeholder="Наименование" data-parsley-required="true" data-parsley-trigger="change">	
		<label for="name" class="field-icon"><i class="fa fa-pencil"></i></label>
		</label>
		</div>
		<div class="col-xs-6 smart-widget sm-right">				
		<label for="address" class="field">
		<input '.$disable.'  id="address" type="text" name="address" value="'.$address.'" placeholder="Адрес" class="gui-input '.$disable.'" style="padding-right: 50px;"/>
		<button '.$disable.'  type="button" onclick="modal_o(\'#modal-map\')" class="button"><i class="fa fa-map-marker"></i></button>
		</label>
		<input type="hidden" name="coords" id="coords" value="'.$coords.'" />
		<div id="modal-map" class="popup-basic popup-lg bg-none mfp-with-anim mfp-hide">
		<div class="panel">							
		<script type="text/javascript">	
		ymaps.ready(init);
		function init() {
		var myPlacemark,
		myMap = new ymaps.Map(\'map\', {
		center: [56.852556, 53.211863],
		zoom: 13
		}, {
		searchControlProvider: \'yandex#search\'
		});
		
		// Слушаем клик на карте.
		myMap.events.add(\'click\', function (e) {
		var coords = e.get(\'coords\');
		
		// Если метка уже создана – просто передвигаем ее.
		if (myPlacemark) {
		myPlacemark.geometry.setCoordinates(coords);
		}
		// Если нет – создаем.
		else {
		myPlacemark = createPlacemark(coords);
		myMap.geoObjects.add(myPlacemark);
		// Слушаем событие окончания перетаскивания на метке.
		myPlacemark.events.add(\'dragend\', function () {
		getAddress(myPlacemark.geometry.getCoordinates());
		});
		}
		getAddress(coords);
		});
		
		
		// Создание метки.
		function createPlacemark(coords) {
		return new ymaps.Placemark(coords, {
		iconCaption: \'поиск...\'
		}, {
		preset: \'islands#violetDotIconWithCaption\',
		draggable: true
		});
		}
		
		// Определяем адрес по координатам (обратное геокодирование).
		function getAddress(coords) {
		myPlacemark.properties.set(\'iconCaption\', \'поиск...\');
		ymaps.geocode(coords).then(function (res) {
		var firstGeoObject = res.geoObjects.get(0);
		document.getElementById(\'address\').value =   firstGeoObject.getAddressLine();
		document.getElementById(\'coords\').value =   coords;
		myPlacemark.properties
		.set({
		// Формируем строку с данными об объекте.
		iconCaption: [
		// Название населенного пункта или вышестоящее административно-территориальное образование.
		firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
		// Получаем путь до топонима, если метод вернул null, запрашиваем наименование здания.
		firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
		].filter(Boolean).join(', '),
		// В качестве контента балуна задаем строку с адресом объекта.
		balloonContent: firstGeoObject.getAddressLine()
		});
		});
		}
		}
		</script>
		<style type="text/css">
		#map {
		width: 100%;
		height: 80%;
		}																			
		</style>
		<div id="map" style="width: 700px; height: 500px"></div> 						 
		</div>
		</div>
		</div>
		</div>
		<div class="section row mb15">
		<div class="col-xs-6">
		<label for="type" class="field">
		<select '.$disable.' name="type[]" multiple="multiple" id="select2-multiple" class="'.$disable.' select2-multiple form-control select-primary">
		<option value="1" '.(in_array("1", $type) ? 'selected' : '').'>'.($lang['object_type_1'] ?? 'object_type_1').'</option>
		<option value="2" '.(in_array("2", $type) ? 'selected' : '').'>'.($lang['object_type_2'] ?? 'object_type_2').'</option>
		<option value="3" '.(in_array("3", $type) ? 'selected' : '').'>'.($lang['object_type_3'] ?? 'object_type_3').'</option>
		</select>
		</label>
		</div>
		<div class="col-xs-6">
		<label for="status" class="field">
		<select '.$disable.'  name="status" class="'.$disable.' select2-single select-primary ">
		<option value="1">'.($lang['object_status_ok']?? 'object_status_ok').'</option>
		<option value="3">'.($lang['object_status_mod']?? 'object_status_mod').'</option>
		</select>
		</label>
		
		</div>
		</div>
		<div class="section row mb15">
		<div class="col-xs-6">
		<label for="client" class="field">
		<select '.$disable.' name="client[]" multiple="multiple" id="select3-multiple" class="'.$disable.' select2-multiple form-control select-primary">
		
		'.$client_list.'
		</select>
		</label>
		</div>
		<div class="col-xs-6">
		<label for="con" class="field">
		<select '.$disable.'  name="con" class="'.$disable.' select2-single select-primary ">
		<option value="0" disabled selected>'.($lang['object_con_choose'] ?? 'object_con_choose').'</option>
		'.$con_list.'
		</select>
		</label>
		
		</div>
		</div>
		<div class="section row mb15">
		<div class="col-xs-12">';
		$preview = false;
		$attribute = [];
		$attribute["name"] = "";
		$attribute["script"] = true;
		$adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
		$adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
		echo uploaderMaster("css", $attribute);
		echo '<div class="col-sm-6">';
		echo uploaderMaster("upload", $attribute);
		echo '</div>';

		echo '<div class="col-sm-6">';
		echo uploaderMaster("view", $attribute);
		echo '</div>';
		/*
			echo ' <label class="field prepend-icon append-button file"><span class="button btn-primary">Выберите файл</span>
			<input id="file" type="file" name="file" onchange="document.getElementById(\'uploader1\').value = this.value;" class="gui-file">
			<input id="uploader1" type="text" placeholder="При необходимости прикрепите файл" class="gui-input">
			<label class="field-icon"><i class="fa fa-upload"></i></label>
			</label>';
		*/
		echo '</div> 
		</div>
		<div class="section row mb15">
		<div class="col-xs-12">
		<textarea '.$disable.' name="comment" id="comment" rows="6" class="'.$disable.' form-control textarea-grow" placeholder="Комментарий">'.$comment.'</textarea>		
		</div>
		</div> 			
		
		
		
		
		
		</div>
		<div class="col-md-3">
		<div data-provides="fileupload" class="fileupload fileupload-'.($preview ? 'exists' : 'new').' admin-form">
		<div class="fileupload-preview thumbnail mb15">
		<img '.($preview ? 'src="'.$config['url'].$preview.'"' : 'data-src="holder.js/100%x147/text:"').' alt="holder">
		</div>
		<span class="button btn-system btn-file btn-block ph5">
		<span class="fileupload-new">'.$lang['upload'].'</span>
		<span class="fileupload-exists">'.$lang['upload_again'].'</span>
		<input name="preview" type="file">
		</span>											
		</div>										
		<div class="checkbox-custom checkbox-warning mb5 mt15 text-center">
		<input id="preview_del" name="preview_del" type="checkbox">
		<label for="preview_del">Удалить</label>
		</div>	
		</div>
		</div>								
		</div>
		<div id="tab1_2" class="tab-pane">
		<div class="section row mbn">
		<div class="col-xs-6 pr15">
		<div class="section mb10">
		<label class="field mb5">Организация:</label>
		<label for="zas" class="field">';
		/*
			echo '<select '.$disable.'  name="zas" class="'.$disable.' select2-single select-primary ">
			<option value="0" disabled selected>'.$lang['object_zas_choose'].'</option>
			'.$zas_list.'
			</select>';
		*/
		oSelect("jsAdminTpl", "zas", $id_zas);
		oSelect("select", "zas", $id_zas);
		echo '</label>
		</div>	
		<div class="section mb10">
		<label class="field mb5">object_pod:</label>
		<label for="pod" class="field">
		<select '.$disable.'  name="pod" class="'.$disable.' select2-single select-primary ">
		<option value="0" disabled selected>object_pod_choose</option>
		'.$pod_list.'
		</select>
		</label>
		</div>
		
		
		</div>	
		<div class="col-xs-6 pr15">
		<div class="section mb10">
		<label class="field mb5">object_gpr:</label>
		<label for="gpr" class="field">
		<select '.$disable.'  name="gpr" class="'.$disable.' select2-single select-primary ">
		<option value="0" disabled selected>object_gpr_choose</option>
		'.$gpr_list.'
		</select>
		</label>
		</div>	
		<div class="section mb10">
		<label class="field mb5">object_spr:</label>
		<label for="spr" class="field">
		<select '.$disable.'  name="spr" class="'.$disable.' select2-single select-primary ">
		<option value="0" disabled selected>object_spr_choose</option>
		'.$spr_list.'
		</select>
		</label>
		</div>
		
		
		</div>										
		</div>';
		foreach ($section_array as $key => $value)
		{
			echo '<div class="section row mbn">
			<div class="col-xs-5 text-right">
			<label class="field mb5" style="    margin-right: -35px; margin-left: 100px;  margin-top: 7px;">'.$value.':</label>
			</div> 
			<div class="col-xs-1 text-right">
			<div class="section mb10">
			<label for="spr" class="field" style="margin-left: 27px;">											
			<div class="switch switch-info switch-inline">
			<input id="sectionSwitch-'.$key.'" name="sectionSwitch-'.$key.'" type="checkbox" '.($sw[$key] ?? '').' onclick="showhide(\'section-'.$key.'\');">
			<label for="sectionSwitch-'.$key.'"></label>
			</div>
			</label>											
			</div>
			</div>
			<div class="col-xs-6 text-left" id="section-'.$key.'" style="display: '.($sw_st[$key] ?? '').';">
			<div class="section mb10">
			<label class="field mb5">object_mso:</label>
			<label for="mso-'.$key.'" class="field">
			<select '.$disable.'  name="mso-'.$key.'" class="'.$disable.' select2-single select-primary ">
			<option value="0" disabled selected>object_mso_choose</option>
			'.$mso_list[$key].'
			</select>
			</label>
			</div>	
			<div class="section mb10">
			<label class="field mb5">object_sys:</label>
			<label for="sys-'.$key.'" class="field">
			<select '.$disable.'  name="sys-'.$key.'" class="'.$disable.' select2-single select-primary ">
			<option value="0" disabled selected>object_sys_choose</option>
			'.$sys_list[$key].'
			</select>
			</label>
			</div>
			<div class="section mb10">
			<label class="field mb5">object_itp:</label>
			<label for="itp-'.$key.'" class="field">
			<select '.$disable.'  name="itp-'.$key.'" class="'.$disable.' select2-single select-primary ">
			<option value="0" disabled selected>object_itp_choose</option>
			'.$itp_list[$key].'
			</select>
			</label>
			</div>	
			<div class="section mb10">
			<label class="field mb5">object_mts:</label>
			<label for="mts-'.$key.'" class="field">
			<select '.$disable.'  name="mts-'.$key.'" class="'.$disable.' select2-single select-primary ">
			<option value="0" disabled selected>object_mts_choose</option>
			'.$mts_list[$key].'
			</select>
			</label>
			</div>
			</div>
			</div>';
		}




		echo '	<br>
		</div>					
		</div>
		</div>
		</div>	
		
		<div class="btn-group">
		<input type="submit" class="btn btn-primary btn-parsley" id="sub" value="' . $dosave . '" />
		</div>';
		if(isset($id))
		{
			/*
				$adminTpl->footIncludes[] = '<script>
				$(\'#admin-form\').submit(function (e) {
				var form = this;
				e.preventDefault();
				setTimeout(function () {
				form.submit();
				}, 1000); // in milliseconds
				});
			</script>';		*/
			echo '	<input type="hidden" name="edit" value="1" />						
			<input type="hidden" name="edit_id" value="'.$id.'" />';
		}
		echo'</form>
		</div>
		</section>';
		$adminTpl->admin_foot();
	}

	function object_save() {
		global $adminTpl, $db, $core, $lang;

		$section_array = array();
		$section_array['1'] = 'Раздел ТС';
		$section_array['2'] = 'Раздел ОВ';
		$section_array['3'] = 'Раздел ВК';
		$section_array['4'] = 'Раздел ТМ';

		$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);
		$name = isset($_POST['name']) ? filter(trim($_POST['name'])) : '';
		$coords = isset($_POST['coords']) ? filter($_POST['coords']) : '';
		$address = isset($_POST['address']) ? filter($_POST['address']) : false;
		$comment = isset($_POST['comment']) ? filter($_POST['comment']) : false;
		$category = isset($_POST['type']) ? array_unique($_POST['type']) : '0';
		$client = isset($_POST['client']) ? array_unique($_POST['client']) : '0';
		$status= isset($_POST['status']) ? intval($_POST['status']) : '3';
		$id_con= isset($_POST['con']) ? intval($_POST['con']) : 0;
		$id_zas= isset($_POST['zas']) ? intval($_POST['zas']) : 0;
		$id_pod= isset($_POST['pod']) ? intval($_POST['pod']) : 0;
		$id_gpr= isset($_POST['gpr']) ? intval($_POST['gpr']) : 0;
		$id_spr= isset($_POST['spr']) ? intval($_POST['spr']) : 0;
		// echo $_POST['sectionSwitch-1'];
		foreach ($section_array as $key => $value)
		{
			$id_mso[$key]  = isset($_POST['mso-'.$key]) ? intval($_POST['mso-'.$key]) : 0;
			$id_sys[$key]  = isset($_POST['sys-'.$key]) ? intval($_POST['sys-'.$key]) : 0;
			$id_itp[$key]  = isset($_POST['itp-'.$key]) ? intval($_POST['itp-'.$key]) : 0;
			$id_mts[$key]  = isset($_POST['mts-'.$key]) ? intval($_POST['mts-'.$key]) : 0;
			$sw[$key] = isset($_POST['sectionSwitch-'.$key]) ? intval($_POST['sectionSwitch-'.$key]) : 0;

		}


		$edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : '';
		$uid = $core->auth->user_info['id'];

		$cats = '';
		if(is_array($category))
		{
			ksort($category);
			foreach($category as $cid)
			{
				$cats .= intval($cid) . ",";
			}
		}
		else
		{
			$cats  = $category . ',';
		}
		$client_str = '';
		if(is_array($client))
		{
			ksort($client);
			foreach($client as $cid)
			{
				$client_str .= intval($cid) . ",";
			}
		}
		else
		{
			$client_str  = $client . ',';
		}
		//$coords_array = explode(",", $coords);
		echo '<div id="content" class="animated fadeIn">';
		if(!empty($name))
		{
			if(isset($_POST['edit']))
			{
				$db->query("UPDATE `" . DB_PREFIX . "_object` SET `name` = '" . $db->safesql($name) . "', `address` = '" . $db->safesql($address) . "',  `coords` = '" . $db->safesql($coords) . "', `comment` = '" . $db->safesql($comment) . "', `cat` = '" . $cats . "', `id_client` = '" . $client_str . "', `id_con` = '" . $id_con . "', `id_zas` = '" . $id_zas . "', `id_pod` = '" . $id_pod . "', `id_gpr` = '" . $id_gpr . "', `id_spr` = '" . $id_spr . "', `status` = '" . $status . "' WHERE `id` =".$edit_id."  LIMIT 1");
				$adminTpl->info($lang['cats_edit_success'], 'info', null, $lang['info'], $lang['cats_list'], ADMIN.'/object');
				$onid = $edit_id;
				foreach ($section_array as $key => $value)
				{
					$queryOB = $db->query("SELECT id FROM ".DB_PREFIX."_object_section WHERE object_id = '" . $onid . "' AND section = '".$key."' LIMIT 1");
					if($db->numRows($queryOB) > 0)
					{
						$resultOB = $db->getRow($queryOB);

						if ($sw[$key] == 'on')
						{
							$db->query("UPDATE `" . DB_PREFIX . "_object_section` SET `mso` = ".$id_mso[$key].", `sys` = ".$id_sys[$key].", `itp` = ".$id_itp[$key].", `mts` = ".$id_mts[$key].",  `status` = '1' WHERE `id` = '".$resultOB['id']."'  LIMIT 1");
						}
						else
						{
							$db->query("UPDATE `" . DB_PREFIX . "_object_section` SET `mso` = ".$id_mso[$key].", `sys` = ".$id_sys[$key].", `itp` = ".$id_itp[$key].", `mts` = ".$id_mts[$key].",  `status` = '0' WHERE `id` = '".$resultOB['id']."'  LIMIT 1");
						}
					}
					else
					{
						if ($sw[$key] == 'on')
						{
							$db->query("INSERT INTO `" . DB_PREFIX . "_object_section` VALUES (NULL, '" . $onid . "', '" . $key . "', '" . $id_mso[$key] . "', '" . $id_sys[$key] . "', '" . $id_itp[$key] . "', '" . $id_mts[$key] . "', '1');");
						}
					}

				}
			}
			else
			{
				if($db->query("INSERT INTO `" . DB_PREFIX . "_object` ( `id` , `name` , `address` , `coords` , `comment` , `cat` , `id_client` , `id_con` ,  `id_zas` , `id_pod` , `id_gpr` , `id_spr` , `date` , `uid` , `status`) VALUES (NULL, '" . $db->safesql($name) . "', '" . $db->safesql($address) . "',  '" . $db->safesql($coords) . "', '" . $db->safesql($comment) . "', '" . $cats . "', '" . $client_str . "', '" . $id_con . "', '" . $id_zas . "', '" . $id_pod . "', '" . $id_gpr . "', '" . $id_spr . "',  '" . time() . "', '" . $uid . "', '" . $status . "');"))
				{
					$onid = $db->insert_id();
					foreach ($section_array as $key => $value)
					{
						if ($sw[$key] == 'on')
						{
							$db->query("INSERT INTO `" . DB_PREFIX . "_object_section` VALUES (NULL, '" . $onid . "', '" . $key . "', '" . $id_mso[$key] . "', '" . $id_sys[$key] . "', '" . $id_itp[$key] . "', '" . $id_mts[$key] . "', '1');");
						}

					}
					$adminTpl->info($lang['cats_add_success'], 'info', null, $lang['info'], $lang['cats_list'], ADMIN.'/object');
				}

			}

			if (isset($_POST['preview_del']))
			{
				if(isset($_POST['edit']))
				{
					$queryPR = $db->query("SELECT preview FROM ".DB_PREFIX."_object WHERE id = '" . $onid . "' LIMIT 1");
					$newsPR = $db->getRow($queryPR);
					if (file_exists(ROOT.$newsPR['preview']))
					{
						unlink(ROOT.$newsPR['preview']);
					}
					$db->query("UPDATE `" . DB_PREFIX . "_object` SET `preview` = '' WHERE `id` = '" . $onid . "' LIMIT 1 ;");
				}
			}
			else
			{
				if($_FILES['preview']['size'] > 0)
				{
					$purl = 'files/object/'.$onid.'/';
					$path_info = pathinfo($_FILES['preview']['name']);
					$ext = $path_info['extension'];
					$file_full = $purl.'preview_' .$onid.'.'.$ext;
					if (file_exists(ROOT.$file_full))
					{
						@unlink(ROOT.$file_full);
					}
					if($foo = new Upload($_FILES['preview']))
					{
						$foo->file_new_name_body = 'preview_' .$onid;
						$foo->image_resize = false;
						$foo->image_x = $news_conf['thumb_width'];
						$foo->image_ratio_y = true;
						$foo->file_overwrite = true;
						$foo->file_auto_rename = false;
						$foo->Process(ROOT.$purl);
						$foo->allowed = array("image/*");
						if ($foo->processed)
						{
							$foo->Clean();
						}
					}
					$db->query("UPDATE `" . DB_PREFIX . "_object` SET `preview` = '/" . $file_full . "' WHERE `id` = '" . $onid . "' LIMIT 1 ;");
				}
				$module = 'object';

				$file = isset($_POST['file']) ? $_POST['file'] : false;

				$attribute = [];
				$attribute["name"] = "";
				$attribute["file"] = $file;
				$attribute["module"] = 'object';
				$attribute["mod"] = 'object';
				$attribute["id"] = $onid;
				$attribute["comm_id"] = $onid;
				uploaderMaster("download", $attribute);
				/*
					if($_FILES['file']['size'] > 0)
					{
					$purl = 'files/object/'.$onid.'/';
					$path_info = pathinfo($_FILES['file']['name']);
					$ext = $path_info['extension'];
					$file_nnid = $onid.time();
					$file_full = $purl.translit($path_info['basename']).'_'.$file_nnid.'.'.$ext;
					if (file_exists(ROOT.$file_full))
					{
					@unlink(ROOT.$file_full);
					//$db->query("UPDATE `" . DB_PREFIX . "_news` SET `preview` = '/" . $file_full . "' WHERE `id` = '" . $onid . "' LIMIT 1 ;");
					}
					if($foo = new Upload($_FILES['file']))
					{
					$foo->file_new_name_body = translit($path_info['basename']).'_'.$file_nnid;
					$foo->file_overwrite = true;
					$foo->file_auto_rename = false;
					$foo->Process(ROOT.$purl);
					if ($foo->processed)
					{
					$foo->Clean();
					}

					}

					}
				*/
			}
		}
		else
		{
			$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
		}
		echo '</div>';
		$adminTpl->admin_foot();
	}


	function delete($id) {
		global $adminTpl, $db;
		//$db->query("DELETE FROM `" . DB_PREFIX . "_categories` WHERE `id` = " . $id . " LIMIT 1");
		//$db->query("DELETE FROM `" . DB_PREFIX . "_news` WHERE `cat` like '%," . $id . ",%'");
		if(file_exists(ROOT . 'tmp/cache/categories.cache'))
		{
			@unlink(ROOT . 'tmp/cache/categories.cache');
		}
	}

	switch(isset($url[2]) ? $url[2] : null) {
		default:
		main();
		break;

		case "add":
		add();
		break;

		case "map":
		map();
		break;

		case "view":
		$id = intval($url[3]);
		view2($id, true);
		break;

		case "file":
		$id = intval($url[3]);
		allFile($id, true);
		break;

		case "task":
		$id = intval($url[3]);
		relations($id, true);
		break;

		case "relations":
		$id = intval($url[3]);
		relations($id, false);
		break;

		case "sales":
		$id = intval($url[3]);
		sales($id, false);
		break;

		case "save":
		object_save();
		break;

		case "delete":
		$id = intval($url[3]);
		delete($id);
		header('Location: /'.ADMIN.'/cats');
		break;

		case "delFile":
		if(isset($_POST["id"])){
			$id = $_POST["id"];
			$db->query("DELETE FROM JMY_attach WHERE id = '".$id."' LIMIT 1");
		}
		break;

		case "edit":
		$id = intval($url[3]);
		add($id);
		break;

		case "action":
		$type = $_POST['submit'];
		if(is_array($_POST['checks'])) {
			switch($type) {
				case _DELETE:
				foreach($_POST['checks'] as $id) {
					delete(intval($id));
				}
				break;
			}
		}
		header('Location: /'.ADMIN.'/cats');
		break;

		case "scan":
		scan();
		break;

		case "save_scan":
		save_scan();
		break;

		case 'ajax':
		global $adminTpl, $db, $lang;
		ajaxInit();
		$type = $url[3];
		switch($type)
		{
			case "iscat":

			if(isset($_POST['altname']))
			{
				if(!preg_match("/^[a-zA-Z0-9_-]+$/", $_POST['altname']))
				{
					echo(json_encode($lang['cats_url_err_3']));
				}
				else
				{
					$query = $db->query("SELECT * FROM ".DB_PREFIX."_categories WHERE altname = '" . $db->safesql($_POST['altname']) . "'");
					if(($db->numRows($query) > 0) && ($url[4] != 'update'))
					{
						echo 'false';
					}
					else
					{
						echo 'true';
					}
				}
			}
			break;
		}
		break;

		case "bind_users_object":
			\Object\Model\UsersObject::firstOrCreate($_POST);
		break;

		case "users_object_list":
			$object = $url[3];

			$o = \Object\Model\UsersObject::where("object", $object)->get();
			$data = [];
			if($o->count() > 0){
				foreach($o as $item){
					$data[] = "<li>".shortUserName($item->uid)."</li>";
				}
			} else {
				$data[] = "<li>Пользователи отсутствуют</li>";
			}

			echo implode("", $data);
		break;
	}
