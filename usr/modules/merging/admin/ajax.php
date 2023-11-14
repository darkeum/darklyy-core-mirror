<?php
	global $db, $allowFuncs, $url, $core;
	
	$allowFuncs = array_merge($allowFuncs, ["delMerging", "restoreMerging"]);
    
	function delMerging()
	{
		global $db, $url, $core, $lang;
		$uid_exec = $core->auth->user_info['id'];
		//if($core->auth->allowAccessById($uid_exec, '13'))
		if(true)
		{
			$result = array();
			if(isset($_POST['idRemove']) && !empty($_POST['idRemove']) && $_POST['idRemove'] != '0')
			{
				$id = intval($_POST['idRemove']);
				$queryCheck = $db->query("SELECT * FROM ".DB_PREFIX."_merging WHERE id = '".$id."' LIMIT 1");	
				if($db->numRows($queryCheck) > 0) 
				{	
					$rCheck = $db->getRow($queryCheck);
					if($rCheck['active'] == '0')
					{
						$db->query("UPDATE ".DB_PREFIX."_merging SET del = '1' WHERE id = '".$id."' LIMIT 1");	
						$result['title'] = 'Информация';
						$result['text']  = 'Запись успешно удалена!';
						$result['type']  = 'success';
						$result['data']['rowID']  = $_POST['rowIDRemove'];
					}
					else
					{
						$result['title'] = 'Ошибка';
						$result['text']  = 'Удалять можно только не проведенные комплектации!';
						$result['type']  = 'danger';
					}
				}
				else
				{
					$result['title'] = 'Ошибка';
					$result['text']  = 'Не найдена выбранная комплектация!';
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
	
	function restoreMerging()
	{
		global $db, $url, $core, $lang;
		$uid_exec = $core->auth->user_info['id'];
		//if($core->auth->allowAccessById($uid_exec, '13'))
		if(true)
		{
			$result = array();
			if(isset($_POST['idRestore']) && !empty($_POST['idRestore']) && $_POST['idRestore'] != '0')
			{
				$id = intval($_POST['idRestore']);
				$queryCheck = $db->query("SELECT * FROM ".DB_PREFIX."_merging WHERE id = '".$id."' LIMIT 1");	
				if($db->numRows($queryCheck) > 0) 
				{	
					$rCheck = $db->getRow($queryCheck);
					if($rCheck['del'] == '1')
					{
						$db->query("UPDATE ".DB_PREFIX."_merging SET del = '0' WHERE id = '".$id."' LIMIT 1");	
						$result['title'] = 'Информация';
						$result['text']  = 'Комплектация успешно восстановлена!';
						$result['type']  = 'success';
						$result['data']['rowID']  = $_POST['rowIDRestore'];
					}
					else
					{
						$result['title'] = 'Ошибка';
						$result['text']  = 'Восстанавливать можно только не удаленные комплектации!';
						$result['type']  = 'danger';
					}
				}
				else
				{
					$result['title'] = 'Ошибка';
					$result['text']  = 'Не найдена выбранная комплектация!';
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