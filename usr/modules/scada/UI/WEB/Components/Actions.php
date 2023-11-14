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

use Scada\Models\ScadaActions;
use Boot\System\Livewire\Component;

class Actions extends Component
{
    public $actions;

    protected function getActions()
    {
        return ScadaActions::with('tags')->with('tags.scada')->orderBy('id', 'DESC')->limit(6)->get();
    }

    public function reload()
    {
        $this->actions =  $this->getActions();      
        $this->emit('tooltip');
    }

    public function mount()
    {        
        $this->actions = $this->getActions();
    }

    public function render()
    {
        return view('scada::manager.components.actions');
    }
}
