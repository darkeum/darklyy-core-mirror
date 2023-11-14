<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace App\Components;

use Boot\System\Livewire\Component;
use Notifications\Support\NotificationsSupport;
 
class Notifications extends Component
{
    public $notification_count;
    public $notifications;
 
    public function mount()
    {
        $this->notification_count = NotificationsSupport::getNotificationsCount();
        $this->notifications = NotificationsSupport::getNotificationsBar();
    }

    public function render()
    {
        return view('components.notifications');
    }
}