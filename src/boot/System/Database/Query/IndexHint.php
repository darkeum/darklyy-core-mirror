<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2026 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Database\Query;

class IndexHint
{
    /**
     * The type of query hint.
     *
     * @var string
     */
    public $type;

    /**
     * The name of the index.
     *
     * @var string
     */
    public $index;

    /**
     * Create a new index hint instance.
     *
     * @param  string  $type
     * @param  string  $index
     */
    public function __construct($type, $index)
    {
        $this->type = $type;
        $this->index = $index;
    }
}
