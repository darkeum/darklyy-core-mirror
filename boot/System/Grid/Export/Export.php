<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Grid\Export;

abstract class Export
{
    protected $Loader;

    public $input;
    public $grid_id;

    public $cols;
    public $rows;
    public $userdata;

    public $page;
    public $total;
    public $records;
    public $title;

    abstract public function doExport();

    protected function getSetting($key)
    {
        return isset($this->settings[$key]) ? $this->settings[$key] : null;
    }
}