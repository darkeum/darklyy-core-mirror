<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Task\Observers;

use Task\Models\Task;
use Boot\Support\Facades\Cache;

class TaskObserver
{ 
    /**
     * Срабатывает после изменения из базы данных
     *
     * @param  mixed $page
     * @return void
     */
    public function saved(Task $task)
    {
        $this->taskCache($task);
    }
    
    /**
     * taskCache
     *
     * @param  mixed $task
     * @return void
     */
    protected function taskCache(Task $task)
    {
        Cache::put('task.user.'. $task->toid.'.count', Task::where("toid", $task->toid)->where("status", "!=", 3)->count());
    }
}
