<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Decrees\UI\WEB\Components;

use Boot\Models\Users;
use Boot\System\Livewire\Component;

class InfoUser extends Component
{
    public $user;

    public function mount($uid)
    {
        $this->user = Users::find($uid);
    }

    public function render()
    {
        return view('decrees::manager.components.InfoUser');
    }

}
