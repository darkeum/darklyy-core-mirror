<?php
    function task_save() {
		global $adminTpl, $db, $core, $lang;

		$task = new \Task\Model\Task;
		$task->date = !empty($_POST['date']) ? date("Y-m-d H:i:s", strtotime($_POST['date'])) : \Carbon\Carbon::now();
		$task->endsw = $_POST['endsw'] ?? 0;
		$task->type = 1;
		$task->toid = $_POST['toid'] ?? false;
		$file = isset($_POST['file']) ? $_POST['file'] : false;
		$task->auto_delete = $_POST['secret_check'] ?? 0;
		$task->title = $_POST['task_title'] ?? "";	
		$task->task = $_POST['task'];
		$task->fromid = \Boot\Support\Facades\Auth::id();

		if (!$task->endsw){
			$task->date_end = !empty($_POST['dateend']) ? date("Y-m-d H:i:s", strtotime($_POST['dateend'])) : \Carbon\Carbon::now();
		}

		$task->save();

		if(isset($_POST['object'])) relationsSave('task', $task->id);

		$user = \Boot\Support\Facades\Auth::user();
		$text = $user["surname"].' '.$user["name"].' поставил вам новую задачу! '.$task->id;
					
        $attr = [];
        $attr['uid'] = $task->toid;
        $attr['title'] = 'task';
        $attr['text'] = $text;
        $attr['icon'] = config('app.url').'/files/push/task.png';
        $attr['url'] = config('app.url').'/administration/module/task/view/'.$task->id;
		$attr['module'] = "task";
		$attr['pid'] = $task->id;
        sendAllNotif($attr);
		
		$attribute = [];
		$attribute["name"] = "";
		$attribute["file"] = $file;
		$attribute["module"] = 'task';
		$attribute["mod"] = 'task';
		$attribute["id"] = $task->id;
		$attribute["comm_id"] = $task->id;
		$attribute["ftp"] = "StorageFTP";
		uploaderMaster("download", $attribute);

		setLog("info", $task->id, "task", "Создание задачи №".$task->id.". Создал ".fullUserName($task->fromid));

		header("Location: /administration/module/task/view/".$task->id);	
}