<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Abstracts\Actions;

abstract class Action
{

    protected string $ui;

    public function getUI()
    {
        return $this->ui;
    }

    public function setUI($interface)
    {
        $this->ui = $interface;

        return $this;
    }
}
