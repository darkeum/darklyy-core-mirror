<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Notifications\UI\WEB\Components;

use Boot\Support\Carbon;
use Boot\Support\Facades\Auth;
use Boot\System\Livewire\Component;
use Notifications\Models\Notifications;
use Notifications\Support\NotificationsSupport;

class Read extends Component
{
    public $notificationsCount;

    public function read()
    {
        Notifications::where('uid', Auth::id())->where('status', 1)->update([
            'status' => 2,
            'read_date' => Carbon::now()
        ]);
        $this->notificationsCount =  NotificationsSupport::reloadNotificationsCount();
    }
    public function mount()
    {  
        $this->notificationsCount =  NotificationsSupport::getNotificationsCount();
    }

    public function render()
    {
        return view('notifications::manager.components.read');
    }
}