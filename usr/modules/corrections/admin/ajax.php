<?php
	global $db, $allowFuncs, $url, $core;	
	$allowFuncs = array_merge($allowFuncs, ['addCorr', 'complCorr', 'delCorr']);	
	function addCorr()
	{
		global $db, $url, $core, $lang;
		$result = array();
		if(isset($_POST['textCorr']) && !empty($_POST['textCorr']))
		{
			$uid = $core->auth->user_info['id'];
			$corrModule = (!empty($_POST['moduleCorr']) ? $_POST['moduleCorr'] : '0');
			$corrText = $_POST['textCorr'];
			$typeCorr = (!empty($_POST['typeCorr']) ? intval($_POST['typeCorr']) : '0');
			$markCorr = (!empty($_POST['markCorr']) ? intval($_POST['markCorr']) : '0');
			$corrUrl = (!empty($_POST['urlCorr']) ? $_POST['urlCorr'] : '');
			$date_add = date("Y-m-d H:i:s");
			$db->query("INSERT INTO " . DB_PREFIX . "_corrections (`id`, `uid`, `type`, `mark`, `modules`, `text`, `url`, `date_add`, `status`) VALUE (NULL, '".$uid."', '".$typeCorr."', '".$markCorr."', '".$corrModule."', '".$corrText."', '".$corrUrl."', '".$date_add."', 1)");
			$corrId = $db->insert_id(); 
			$result['title'] = 'Информация';
			$result['text'] = 'Заявку успешна добавлена!';
			$result['type'] = 'success';			
			$attr = [];
			$attr['uid'] =  getGroup(["group" => "admin"]);
			$attr['title'] = 'Новая заявка на замечание и исправление';
			$attr['text'] = $corrText;
			$attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
			$attr['url'] = config('app.url').'/administration/module/corrections/'.$corrId;
			$attr['pNotif'] = true;
			$attr['pPush'] = true;
			sendAllNotif($attr);
			
		}
		else
		{
			$result['title'] = 'Ошибка';
			$result['text'] = 'Вы не заполнили описание заявки!';
			$result['type'] = 'danger';
		}
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}
	
	function complCorr()
	{
		global $db, $url, $core, $lang;
		$uid_exec = $core->auth->user_info['id'];
		if($core->auth->allowAccessById($uid_exec, '13'))
		{
			$result = array();
			if(isset($_POST['idComplCorr']) && !empty($_POST['idComplCorr']) && $_POST['idComplCorr'] != '0')
			{
				$id = intval($_POST['idComplCorr']);
				$queryCheck = $db->query("SELECT id, uid FROM ".DB_PREFIX."_corrections WHERE id = '".$id."' LIMIT 1");	
				if($db->numRows($queryCheck) > 0) 
				{	
					$rCheck = $db->getRow($queryCheck);
					$textComplCorr = $_POST['textComplCorr'];
					$date_end = date("Y-m-d H:i:s");
					$date_end_view = date("d.m.Y H:i:s");
					$status = ($_POST['typeComplCorr'] == '1') ? '3' : '4';
					$notifCorr = ($_POST['typeComplCorr'] == '1') ? 'Ваша заявка на исправление №'.$rCheck['id'].' выполнена!' : 'Ваша заявка на исправление №'.$rCheck['id'].' отклонена!';
					$ststusIconCorr = ($_POST['typeComplCorr'] == '1') ? '<span style="margin-left: -3px;" class="fal fa-check-circle text-success fa-md" data-toggle="tooltip" title="Выполнено"></span>' : '<span style="margin-left: -3px;" class="fal fa-circle text-danger fa-md" data-toggle="tooltip" title="Отклонено"></span>';
					$db->query("UPDATE ".DB_PREFIX."_corrections SET date_end = '".$date_end."', uid_exec = '".$uid_exec."', status = '".$status."' WHERE id = '".$id."' LIMIT 1");			
					$result['title'] = 'Информация';
					$result['text']  = 'Заявку успешна завершена!';
					$result['type']  = 'success';
					$result['data']['rowID']  = $_POST['rowID'];
					$result['data']['status']  = $ststusIconCorr;
					$result['data']['date_end']  = $date_end_view;
					$result['data']['textComplCorr']  = $textComplCorr;
					$result['data']['uid_exec']  = $uid_exec;
					$attr['uid'] =  $rCheck['uid'];
					$attr['title'] = $notifCorr;
					$attr['text'] = $notifCorr;
					$attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
					$attr['url'] = config('app.url').'/administration/module/corrections/'.$rCheck['id'];
					$attr['pNotif'] = true;
					$attr['pPush'] = true;
					sendAllNotif($attr);
				}
				else
				{
					$result['title'] = 'Ошибка';
					$result['text']  = 'Не найдена выбранная заявка!';
					$result['type']  = 'danger';
				}
			}
			else
			{
				$result['title'] = 'Ошибка';
				$result['text']  = 'Произошла исключительная ситуация, свяжитесь с администрацией!';
				$result['type']  = 'danger';
			}
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		}
		else
		{
			$result['title'] = 'Ошибка';
			$result['text']  = 'У вас нет прав доступа для совершения данной операции!';
			$result['type']  = 'danger';
		}
	}
	
	function delCorr()
	{
		global $db, $url, $core, $lang;
		$uid_exec = $core->auth->user_info['id'];
		if($core->auth->allowAccessById($uid_exec, '13'))
		{
			$result = array();
			if(isset($_POST['idRemoveCorr']) && !empty($_POST['idRemoveCorr']) && $_POST['idRemoveCorr'] != '0')
			{
				$id = intval($_POST['idRemoveCorr']);
				$queryCheck = $db->query("SELECT * FROM ".DB_PREFIX."_corrections WHERE id = '".$id."' LIMIT 1");	
				if($db->numRows($queryCheck) > 0) 
				{	
					$rCheck = $db->getRow($queryCheck);
					if($rCheck['status'] == '4')
					{
						$db->query("UPDATE ".DB_PREFIX."_corrections SET del = '1' WHERE id = '".$id."' LIMIT 1");	
						$result['title'] = 'Информация';
						$result['text']  = 'Запись успешно удалена!';
						$result['type']  = 'success';
						$result['data']['rowID']  = $_POST['rowIDRemove'];
					}
					else
					{
						$result['title'] = 'Ошибка';
						$result['text']  = 'Удалять можно только отклоненные записи!';
						$result['type']  = 'danger';
					}
				}
				else
				{
					$result['title'] = 'Ошибка';
					$result['text']  = 'Не найдена выбранная заявка!';
					$result['type']  = 'danger';
				}
			}
			else
			{
				$result['title'] = 'Ошибка';
				$result['text']  = 'Произошла исключительная ситуация, свяжитесь с администрацией!';
				$result['type']  = 'danger';
			}
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		}
		else
		{
			$result['title'] = 'Ошибка';
			$result['text']  = 'У вас нет прав доступа для совершения данной операции!';
			$result['type']  = 'danger';
		}
	}
    