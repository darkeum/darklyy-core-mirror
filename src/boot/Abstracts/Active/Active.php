<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Abstracts\Active;

use \Boot\Exceptions\OptionsException;

abstract class Active{
    abstract public function active();
    abstract public function deactivate();
    abstract public function get_main_module();

    public function toggle(): array
    {
        $module = $this->get_main_module();
        switch($module->active){
            default:
                throw new OptionsException("Неизвестный тип активации", options:["code" => 3]);
            break;
            case 0:
                return $this->active();
            break;
            case 1:
                return $this->deactivate();
            break;
        }
    }
}