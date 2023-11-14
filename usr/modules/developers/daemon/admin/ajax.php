<?php
	global $db, $allowFuncs, $url, $core;
	
	$allowFuncs = array_merge($allowFuncs, ["addDaemonInTable", "startDaemon", "stopDaemon", "removeDaemon", "restoreDaemon"]);

	function addDaemonInTable() {
		global $core;
		
		$uid = $core->auth->user_info['id'];
		if(isset($_POST['title']) && isset($_POST['command']) && isset($_POST['autostart'])) {
			$add = [
				'uid' => $uid,
				'title' => $_POST['title'],
				'command' => $_POST['command'],
				'autostart' => $_POST['autostart']
			];

			\Daemon\Model\Daemon::create($add);
		}
	};

	function startDaemon() {
		if (isset($_POST['id'])) {
			$daemonTable = \Daemon\Model\Daemon::findOrFail($_POST['id']);
			$process = new \Daemon\DaemonProcess($daemonTable->command);
			$processId = $process->getPid();
			$daemonTable->update(['pid'=>$processId]);
			print_r($processId);
		}
	};

	function stopDaemon($setId = false) {
		$id = $setId ? $_POST['removeId'] : $_POST['id'];			
		$process = new \Daemon\DaemonProcess();
		$result = \Daemon\Model\Daemon::findOrFail(intval($id));
		$process->setPid($result->pid);
		$process->stop();
	};

	function removeDaemon()
	{
		global $core;
		
		stopDaemon($_POST['removeId']);        
		$result = array();
		if(isset($_POST['removeId']) && !empty($_POST['removeId']) && $_POST['removeId'] != '0')
		{
			$id = intval($_POST['removeId']);
			$uid = $core->auth->user_info['id'];
			$daemonTable = \Daemon\Model\Daemon::findOrFail($id);
			if (!empty($daemonTable) && $core->auth->allowAccessByName($uid, 'removeDaemon'))
			{					
				$daemonTable->delete();
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

	function restoreDaemon()
	{
        global $core;		
        
		$result = array();
		if(isset($_POST['restoreId']) && !empty($_POST['restoreId']) && $_POST['restoreId'] != '0')
		{
			$id = intval($_POST['restoreId']);
			$uid = $core->auth->user_info['id'];
			$daemonTable = \Daemon\Model\Daemon::withTrashed()->where("id", $id)->first();
			if(!empty($daemonTable) && $core->auth->allowAccessByName($uid, 'restoreDaemon'))
			{					
				$daemonTable->restore();

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