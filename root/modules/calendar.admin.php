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

$core->loadModLangADM('bank');
use Kily\Tools1C\ClientBankExchange\Parser;

function main()
{	
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$adminTpl->admin_head('Календарь событий');		
	echo '
	<!-- Modal -->
		<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			<form class="form-horizontal" id="add_event" method="POST"  action="javascript:void(null);">			
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Добавить событие</h4>
			  </div>
			  <div class="modal-body">				
				  <div class="form-group">
					<label for="title" class="col-sm-2 control-label">Событие</label>
					<div class="col-sm-10">
					  <input type="text" name="title" class="form-control" id="title" placeholder="Укажите событие">
					</div>
				  </div>
				  <div class="form-group">
					<label for="color" class="col-sm-2 control-label">Цвет</label>
					<div class="col-sm-10">
					  <select name="color" class="form-control" id="color">
						  <option value="">Выберите цвет события</option>
						  <option style="color:#0071c5;" value="#0071c5">&#9724; Темно синий</option>
						  <option style="color:#40E0D0;" value="#40E0D0">&#9724; Turquoise</option>
						  <option style="color:#008000;" value="#008000">&#9724; Зеленый</option>						  
						  <option style="color:#FFD700;" value="#FFD700">&#9724; Желтый</option>
						  <option style="color:#FF8C00;" value="#FF8C00">&#9724; Оранжевый</option>
						  <option style="color:#FF0000;" value="#FF0000">&#9724; Красный</option>
						  <option style="color:#000;" value="#000">&#9724; Черный</option>						  
						</select>
					</div>
				  </div>
				  <div class="form-group">
					<label for="start" class="col-sm-2 control-label">Начало</label>
					<div class="col-sm-10">
					  <input type="text" name="start" class="form-control" id="start" readonly>
					</div>
				  </div>
				  <div class="form-group">
					<label for="end" class="col-sm-2 control-label">Конец</label>
					<div class="col-sm-10">
					  <input type="text" name="end" class="form-control" id="end" readonly>
					</div>
				  </div>				
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
				<button type="submit" class="btn btn-primary">Сохранить</button>
			  </div>
			</form>
			</div>
		  </div>
		</div>
		<!-- Modal -->
		<div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			<form class="form-horizontal" method="POST" id="edit_event" action="javascript:void(null);">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Редактировать событие</h4>
			  </div>
			  <div class="modal-body">
				
				  <div class="form-group">
					<label for="title" class="col-sm-2 control-label">Событие</label>
					<div class="col-sm-10">
					  <input type="text" name="title" class="form-control" id="title" placeholder="Title">
					</div>
				  </div>
				  <div class="form-group">
					<label for="color" class="col-sm-2 control-label">Цвет</label>
					<div class="col-sm-10">
					  <select name="color" class="form-control" id="color">
						  <option value="">Выберите цвет события</option>
						  <option style="color:#0071c5;" value="#0071c5">&#9724; Темно синий</option>
						  <option style="color:#40E0D0;" value="#40E0D0">&#9724; Turquoise</option>
						  <option style="color:#008000;" value="#008000">&#9724; Зеленый</option>						  
						  <option style="color:#FFD700;" value="#FFD700">&#9724; Желтый</option>
						  <option style="color:#FF8C00;" value="#FF8C00">&#9724; Оранжевый</option>
						  <option style="color:#FF0000;" value="#FF0000">&#9724; Красный</option>
						  <option style="color:#000;" value="#000">&#9724; Черный</option>						  
						</select>
					</div>
				  </div>
				    <div class="form-group"> 
						<div class="col-sm-offset-2 col-sm-10">
						  <div class="checkbox">
							<label class="text-danger"><input type="checkbox"  name="delete"> Удалить событие?</label>
						  </div>
						</div>
					</div>
				  
				  <input type="hidden" name="id" class="form-control" id="id">
				
				
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
				<button type="submit" class="btn btn-primary">Сохранить</button>
			  </div>
			</form>
			</div>
		  </div>
		</div>	
	<link rel="stylesheet" href="/usr/tpl/corp/assets/css/fullcalendar.min.css" />
	<style>
		.fc-title-clone {
				font-size: 20px;
		text-align: center;
		margin: 8px 0 10px;
		padding-bottom: 15px;
		border-bottom: 1px solid #E7E7E7;
		background-color: white;
		}
	</style><section id="content" class="table-layout animated fadeIn">
          <!-- begin: .tray-left-->
          <aside data-tray-mobile="#content &gt; .tray-center" data-tray-height="match" class="sidebar-visible-tablet tray tray-left tray290">
            <!-- Demo HTML - Via JS we insert a cloned fullcalendar title-->
            <div class="fc-title-clone"></div>
            <!-- Demo HTML - Via JS we insert a sync minicalendar-->
            <div class="section admin-form theme-primary">
              <div class="inline-mp minimal-mp center-block"></div>
            </div>            
            <h4 class="mt30"> Обозначения</h4>
            <hr class="mv15 br-light">
            <ul class="list-group">
              <li class="list-group-item"><span class="badge badge-primary">14</span>Поставленная задача</li>
			  <li class="list-group-item"><span class="badge badge-warning">18</span>Групповое событие</li>
              <li class="list-group-item"><span class="badge badge-success">9</span>День рождение</li>
              <li class="list-group-item"><span class="badge badge-info">11</span>Праздник</li>            
            </ul>
          </aside>
          <!-- begin: .tray-center-->
          <div class="tray tray-center">
            <!-- Calendar-->
            <div id="calendar" class="admin-theme"></div>
          </div>
        </section>
      </section>';
	$adminTpl->footIncludes[''] = '<script src="/usr/tpl/corp/assets/js/fullcalendar.min.js"></script>
	<script src="/usr/tpl/corp/assets/js/jquery-ui.min.js"></script>
	<script src="/usr/tpl/corp/assets/js/moment.min.js"></script>';
	datetimepickerInit('');
	$adminTpl->js_code[] = '  
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		var Picker = $(\'.inline-mp\');
		var calendar = $(\'#calendar\');
		
		calendar.fullCalendar({
			firstDay: 1,
			editable: true,
			header: {
				left: \'prev,next today\',
				center: \'title\',
				right: \'month,agendaWeek,agendaDay\'
			},	  
			locale: \'ru\',
			eventLimit: true,
			monthNames: ["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],
			monthNamesShort: ["Янв.","Фев.","Март","Апр.","Май","Июнь","Июль","Авг.","Сент.","Окт.","Ноя.","Дек."],
			dayNames: ["Воскресенье","Понедельник","Вторник","Среда","Четверг","Пятница","Суббота"],
			dayNamesShort: ["ВС","ПН","ВТ","СР","ЧТ","ПТ","СБ"],
			buttonText: {					   
				prevYear: "&nbsp;&lt;&lt;&nbsp;",
				nextYear: "&nbsp;&gt;&gt;&nbsp;",
				today: "Сегодня",
				month: "Месяц",
				week: "Неделя",
				day: "День"
			},					
			viewRender: function (view) {
				if (Picker.hasClass(\'hasMonthpicker\')) {
					var selectedDate = calendar.fullCalendar(\'getDate\');
					var formatted = moment(selectedDate, \'MM-DD-YYYY\').format(\'MM/YY\');
					Picker.monthpicker("setDate", formatted);
				}						
				var titleContainer = $(\'.fc-title-clone\');
				if (!titleContainer.length) {
					return;
				}
				titleContainer.html(view.title);
			},
			eventSources: [
				{
					url: \config('app.url').'/administration/calendar/events\', // use the `url` property
					type: \'POST\',
					error: function() {
						notif("danger","'.$lang['error'].'", "Ошибка при загрузке данных!");	
					}					 
				}
			],	
			eventRender: function(event, element, view) {
				if (event.allDay === \'true\') {
					event.allDay = true;
				} else {
					event.allDay = false;
				}
			},
			//Добавление события
			selectable: true,
			selectHelper: true,
			select: function(start, end) {			
				$(\'#ModalAdd #start\').val(moment(start).format(\'YYYY-MM-DD HH:mm:ss\'));
				$(\'#ModalAdd #end\').val(moment(end).format(\'YYYY-MM-DD HH:mm:ss\'));
				$(\'#ModalAdd\').modal(\'show\');
			},
			//Редактирование события
			eventRender: function(event, element) {
				element.bind(\'dblclick\', function() {
					$(\'#ModalEdit #id\').val(event.id);
					$(\'#ModalEdit #title\').val(event.title);
					$(\'#ModalEdit #color\').val(event.color);
					$(\'#ModalEdit\').modal(\'show\');
				});
			},
			eventDrop: function(event, delta, revertFunc) { 
				edit(event);
			},
			eventResize: function(event,dayDelta,minuteDelta,revertFunc) {
				edit(event);
			}
		});
		function edit(event){
			var start = event.start.format(\'YYYY-MM-DD HH:mm:ss\');
			if(event.end){
				var end = event.end.format(\'YYYY-MM-DD HH:mm:ss\');
			}else{
				var end = start;
			}			
			var id =  event.id;			
			Event = [];
			Event[0] = id;
			Event[1] = start;
			Event[2] = end;			
			$.ajax({
			 url: 	"'.ADMIN.'/calendar/event_edit_date/",
			 type: "POST",
			 data: {Event:Event},
			 success: function(rep) {
					if(rep == \'OK\'){
						notif("success","'.$lang['info'].'", "Событие успешно изменено!");			
					}else{
						notif("danger","'.$lang['error'].'", "Ошибка при попытки изменения данных!");	
					}
				}
			});
		};
		Picker.monthpicker({
			prevText: \'<i class="fa fa-chevron-left"></i>\',
			nextText: \'<i class="fa fa-chevron-right"></i>\',
			showButtonPanel: false,
			onSelect: function (selectedDate) {
				var formatted = moment(selectedDate, \'MM/YYYY\').format(\'MM/DD/YYYY\');
				calendar.fullCalendar(\'gotoDate\', formatted)
			}
        });';
		$adminTpl->js_code[] ='	$("#add_event").submit(function() { 
										var form_data = $(this).serialize(); 
										$.ajax({
										type: "POST",  
										url: "'.ADMIN.'/calendar/event_add/", 
										data: form_data,
										success: function() {	
											$(\'#ModalAdd\').modal(\'hide\');
											notif("success","'.$lang['info'].'", "Событие успешно добавлено!");												
											$(\'#calendar\').fullCalendar(\'refetchEvents\');
										}
										});
								});
								$("#edit_event").submit(function() { 
										var form_data = $(this).serialize(); 
										$.ajax({
										type: "POST",  
										url: "'.ADMIN.'/calendar/event_edit/", 
										data: form_data,
										success: function() {	
											$(\'#ModalEdit\').modal(\'hide\');
											notif("success","'.$lang['info'].'", "Событие успешно отредактировано!");												
											$(\'#calendar\').fullCalendar(\'refetchEvents\');
										}
										});
								});
								
								
								
';	
	$adminTpl->admin_foot();
}

function events()
{
	global $core, $db;
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_events WHERE uid = '".$core->auth->user_info['id']."' ORDER BY id");
	$json = '';
	if($db->numRows($query) > 0) 
	{
		$json .= '[';
		while($event = $db->getRow($query)) 
		{
			$start = explode(" ", $event['start']);
			$end = explode(" ", $event['end']);
			if($start[1] == '00:00:00'){
				$start = $start[0];
			}else{
				$start = $event['start'];
			}
			if($end[1] == '00:00:00'){
				$end = $end[0];
			}else{
				$end = $event['end'];
			}	
			$json .=  '{"id":"'.$event['id'].'","title":"'.$event['title'].'","start":"'.$start.'" ,"end":"'.$end.'","color": "'.$event['color'].'"},';
		}
		$json = substr($json, 0, -1);
		$json .= ']';
	}
	/*
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_users WHERE group = '1' AND birthday_view IS NOT NULL ORDER BY id");
	$json = '';
	if($db->numRows($query) > 0) 
	{
		$json .= '[';
		while($event = $db->getRow($query)) 
		{
			$start = explode(" ", $event['birthday_view'].'00:00:00');
			$end = $start;
			if($start[1] == '00:00:00'){
				$start = $start[0];
			}else{
				$start = $event['start'];
			}
			if($end[1] == '00:00:00'){
				$end = $end[0];
			}else{
				$end = $event['end'];
			}	
			$json .=  '{"id":"'.$event['id'].'","title":"День рождения","start":"'.$start.'" ,"end":"'.$end.'","color": "'.$event['color'].'"},';
		}
		$json = substr($json, 0, -1);
		$json .= ']';
		
	}*/
	echo $json;
}

function event_add()
{
	global $core, $db;
	if (isset($_POST['title']) && isset($_POST['start']) && isset($_POST['end']) && isset($_POST['color']))
	{
		$title = $_POST['title'];
		$start = $_POST['start'];
		$end = $_POST['end'];
		$color = $_POST['color'];
		$db->query("INSERT INTO `" . DB_PREFIX . "_events` ( `id` , `uid` , `title` , `start` ,  `end` , `type` , `text` , `color` , `status`) VALUES (NULL, '" . $core->auth->user_info['id'] . "', '" . $db->safesql($title) . "', '" . $db->safesql($start) . "', '" . $db->safesql($end) . "', '1', '', '" . $color . "', '1');");
	}	
	header('Location: /administration/calendar/');
}

function event_edit()
{
	global $core, $db;
	if (isset($_POST['delete']) && isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$db->query("DELETE FROM `" . DB_PREFIX . "_events` WHERE `id` = " . $id . " LIMIT 1 ;");			
	}
	elseif (isset($_POST['title']) && isset($_POST['color']) && isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$title = $_POST['title'];
		$color = $_POST['color'];
		$db->query("UPDATE `" . DB_PREFIX . "_events` SET `title` = '".$db->safesql($title)."', `color` = '".$db->safesql($color)."'  WHERE `id` = " . $id . " LIMIT 1 ;");	
	}
	header('Location: /administration/calendar/');
}

function event_edit_date()
{
	global $core, $db;
	if (isset($_POST['Event'][0]) && isset($_POST['Event'][1]) && isset($_POST['Event'][2]))
	{
		$id = intval($_POST['Event'][0]);
		$start =  $_POST['Event'][1];
		$end = $_POST['Event'][2];
		$db->query("UPDATE `" . DB_PREFIX . "_events` SET `start` = '".$db->safesql($start)."', `end` = '".$db->safesql($end)."'  WHERE `id` = " . $id . " LIMIT 1 ;");	
		echo ('OK');
	}
	else
	{
		echo ('ERROR');
	}
}

switch(isset($url[2]) ? $url[2] : null) {
	default:
		main();
	break;
	case "events":
		events();
	break;
	case "event_add":
		event_add();
	break;
	case "event_edit":
		event_edit();
	break;
	case "event_edit_date":
		event_edit_date();
	break;
}
