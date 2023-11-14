<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Home\UI\WEB\Components;

use Task\Support\TaskSupport;
use Sales\Support\SalesSupport;
use Boot\System\Livewire\Component;
use Notifications\Support\NotificationsSupport;

class Statistics extends Component
{
    public $taskCount;
    public $taskExpiredCount;
    public $notificationsCount;
    public $salesActiveCount;

    public function reload()
    {
        $this->taskCount =  TaskSupport::reloadTaskCount();
        $this->taskExpiredCount =  TaskSupport::reloadTaskExpiredCount();
        $this->notificationsCount =  NotificationsSupport::reloadNotificationsCount();
        $this->salesActiveCount =  SalesSupport::reloadSalesActiveCount();
    }
    public function mount()
    {
        $this->taskCount =  TaskSupport::getTaskCount();
        $this->taskExpiredCount =  TaskSupport::getTaskExpiredCount();        
        $this->notificationsCount =  NotificationsSupport::getNotificationsCount();
        $this->salesActiveCount =  SalesSupport::getSalesActiveCount();
    }

    public function render()
    {
        return view('home::manager.components.statistics');
    }
}