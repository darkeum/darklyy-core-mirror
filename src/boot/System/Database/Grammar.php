<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2026 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Database;

use Illuminate\Contracts\Database\Query\Expression;
abstract class Grammar extends \Illuminate\Database\Grammar
{
    /**
     * The connection used for escaping values.
     *
     * @var \Boot\System\Database\Connection
     */
    protected $connection;

    /**
     * Create a new grammar instance.
     *
     * @param  \Boot\System\Database\Connection  $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Determine if the given value is a raw expression.
     *
     * @param  mixed  $value
     * @return bool
     */
    public function isExpression($value)
    {
        return $value instanceof Expression;
    }

}
