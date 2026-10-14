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

use Illuminate\Contracts\Database\Query\Expression as ExpressionContract;
use Boot\System\Database\Grammar;
use Illuminate\Database\Grammar as LaravelGrammar;

/**
 * @template TValue of literal-string|int|float
 */
class Expression implements ExpressionContract
{
    /**
     * Create a new raw query expression.
     *
     * @param  TValue  $value
     */
    public function __construct(
        protected $value,
    ) {
    }

    /**
     * Get the value of the expression.
     *
     * @param  \Boot\System\Database\Grammar  $grammar
     * @return TValue
     */
    public function getValue(Grammar | LaravelGrammar $grammar)
    {
        return $this->value;
    }
}
