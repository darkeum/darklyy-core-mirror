<?php

namespace Sales;

class FastTask{
    static function create($request){
		$sales_id = $request["id"];
		$sales = \Sales\Model\Sales::find($sales_id);
        $task = new \Task\Model\Task;
		$task->title = "Задача по продаже №{$sales->numb}";
		if(!empty($request["task"])) $task->task = $request["task"];
		$task->fromid = \Boot\Support\Facades\Auth::id();
		$task->toid = \Boot\Support\Facades\Auth::id();
		$task->date = \Carbon\Carbon::now();
		$task->endsw = 1;
		$task->date_end = \Carbon\Carbon::parse($request["dateend"]);
		$task->save();

		$relation = new \Boot\Models\Relations;
		$relation->module_in = "task";
		$relation->pid_in = $task->id;
		$relation->module_out = "sales";
		$relation->pid_out = $sales_id;
		$relation->save();

		$sales->task_id = $task->id;
		$sales->task_title = $task->task;
		$sales->task_date_end = $task->date_end;
		$sales->task_availability = 1;
		$sales->save();

        return [
			"task" => $task,
			"relation" => $relation,
		];
    }

    static function get($id){
        $relation = static::find_relation($id);
		$task = empty($relation->pid_in) ? cc() : \Task\Model\Task::with("comments")->where("id", $relation->pid_in)->first();
		$sales = \Sales\Model\Sales::find($id);
		return [
            "task" => $task,
            "sales" => $sales,
			"count" => static::find_relation_query($id)->count(),
        ];
    }

	static function all($id){
		$data = [];
		$relations = static::find_relation_query($id)->get();
		$sales = \Sales\Model\Sales::find($id);
		foreach($relations as $relation){
			$data[] = [
				"task" => empty($relation->pid_in) ? cc() : \Task\Model\Task::with("comments")->where("id", $relation->pid_in)->first(),
				"sales" => $sales,
			];
		}
		return $data;
	}

    static function find_relation_query($id){
        return \Boot\Models\Relations::where("module_in", "task")->
		where("module_out", "sales")->
		where("pid_out", $id)->
		orderByDesc("id");
    }

    static function find_relation($id){
        return static::find_relation_query($id)->first();
    }
}