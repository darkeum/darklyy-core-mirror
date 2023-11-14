<?php
	global $db, $allowFuncs, $url, $core;
	
	$allowFuncs = array_merge($allowFuncs, ["saveCommentsVer", "removeVer", "restoreVer"]);
	
	function saveCommentsVer(){
		global $db, $allowFuncs, $url;
		if(isset($_POST["pk"])){
			$id = $_POST["pk"];
			$comment = $_POST["value"];
			$db->query("UPDATE JMY_ver SET comment = '".$comment."' WHERE id = '".$id."' LIMIT 1");
		}
	}	

	function removeVer()
	{
        global $core;
        
		$result = array();
		if(isset($_POST['removeId']) && !empty($_POST['removeId']) && $_POST['removeId'] != '0')
		{
			$id = intval($_POST['removeId']);
			$uid = $core->auth->user_info['id'];
			$ver = new \Verification\Ver;
			if(!empty($ver->findId($id)) && $core->auth->allowAccessByName($uid, 'removeVer'))
			{					
				$ver->delete($id);
				$result['title'] = 'Информация';
				$result['text']  = 'Запись успешно удалена!';
				$result['type']  = 'success';
				$result['data']['rowID']  = $_POST['removeRowId'];				
			}
			else
			{
				$result['title'] = 'Ошибка';
				$result['text']  = 'Не найдена выбранная Запись! Или у вас недостаточно прав для выполнения данной операции!';
				$result['type']  = 'error';
			}
		}
		else
		{
			$result['title'] = 'Ошибка';
			$result['text']  = 'Произошла исключительная ситуация, свяжитесь с администрацией!';
			$result['type']  = 'error';
		}
		echo json_encode($result, JSON_UNESCAPED_UNICODE);		
	}

	function restoreVer()
	{
        global $core;		
        
		$result = array();
		if(isset($_POST['restoreId']) && !empty($_POST['restoreId']) && $_POST['restoreId'] != '0')
		{
			$id = intval($_POST['restoreId']);
			$uid = $core->auth->user_info['id'];
			$ver = new \Verification\Ver;
			if(!empty($ver->findId($id)) && $core->auth->allowAccessByName($uid, 'restoreVer'))
			{					
				$ver->restore($id);

				$result['title'] = 'Информация';
				$result['text']  = 'Запись успешно восстановлена!';
				$result['type']  = 'success';
				$result['data']['rowID']  = $_POST['restoreRowId'];				
			}
			else
			{
				$result['title'] = 'Ошибка';
				$result['text']  = 'Не найдена выбранная Запись! Или у вас недостаточно прав для выполнения данной операции!';
				$result['type']  = 'error';
			}
		}
		else
		{
			$result['title'] = 'Ошибка';
			$result['text']  = 'Произошла исключительная ситуация, свяжитесь с администрацией!';
			$result['type']  = 'error';
		}
		echo json_encode($result, JSON_UNESCAPED_UNICODE);		
	}


	