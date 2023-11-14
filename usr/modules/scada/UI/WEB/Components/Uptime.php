<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\WEB\Components;

use Boot\Support\Facades\Cache;
use Boot\System\Livewire\Component;

class Uptime extends Component
{
    public $has_poll;
    public $poll;

    protected function getPoll()
    {
        $this->has_poll = Cache::has('scada.work.key');
        $this->poll = Cache::get('scada.work.key.last') ?: __('scada::uptime.poll.empty');
      
    }

    public function reload()
    {
        $this->getPoll();
        $this->emit('tooltip');
    }

    public function mount()
    {
        $this->getPoll();
    }

    public function render()
    {
        return view('scada::manager.components.uptime');
    }
}
