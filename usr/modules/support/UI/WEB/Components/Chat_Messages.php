<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Support\UI\WEB\Components;

use Support\Models\Support;
use Boot\Support\Facades\Auth;
use Support\Models\SupportMessage;
use Boot\System\Livewire\Component;
use Boot\System\Livewire\WithFileUploads;
use Support\Notifications\SupportNewMessage;

class Chat_Messages extends Component
{
    public $support;

    //protected $listeners = [
    //    'messages'
    //];

    //public function messages()
    //{
    //    //$this->render();
    //    //return false;
    //}

    public function render()
    {
        return view('support::manager.components.chat_messages');
    }
}
