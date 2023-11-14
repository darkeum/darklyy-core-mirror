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
use Boot\System\Settings\Facades\Settings;

class ThemeMod extends Component
{
    public $theme;

    public function like($theme)
    {
        Settings::set('theme-mod', ($theme));
        $this->theme = $theme;
    }
    public function mount()
    {
        $this->theme =  Settings::get('theme-mod', 'light');
    }

    public function render()
    {
        return view('components.theme-mod');
    }
}