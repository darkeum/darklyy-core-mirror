<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Task\Providers;

use Task\Models\Task;
use Task\Observers\TaskObserver;
use Boot\Support\ServiceProvider;

class TaskModelServiceProvider extends ServiceProvider 
{
    public function boot() {
        Task::observe(TaskObserver::class);
    }
}