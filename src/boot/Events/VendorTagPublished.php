<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Events;

class VendorTagPublished
{
    /**
     * Опубликованный тег поставщика.
     *
     * @var string
     */
    public $tag;

    /**
     * Публикуемые пути, зарегистрированные тегом.
     *
     * @var array
     */
    public $paths;

    /**
     * Создайте новый экземпляр события.
     *
     * @param  string  $tag
     * @param  array  $paths
     * @return void
     */
    public function __construct($tag, $paths)
    {
        $this->tag = $tag;
        $this->paths = $paths;
    }
}
