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

use Scada\Models\ScadaCrash;
use Boot\System\Livewire\Component;

class Crash extends Component
{
    public $crashes;

    protected function getCrash()
    {
        return ScadaCrash::with('scada')->with('tags')->orderBy('id', 'DESC')->limit(6)->get();
    }

    public function reload()
    {
        $this->crashes =  $this->getCrash();
        $this->emit('tooltip');
    }

    public function mount()
    {
        $this->crashes = $this->getCrash();
    }

    public function render()
    {
        return view('scada::manager.components.crash');
    }
}
