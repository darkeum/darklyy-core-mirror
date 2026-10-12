<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2026 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Testing\Exceptions;


use PHPUnit\Framework\Exception;

class InvalidArgumentException extends Exception
{
    /**
     * Creates a new exception for an invalid argument.
     *
     * @param  int  $argument
     * @param  string  $type
     * @return static
     */
    public static function create(int $argument, string $type): static
    {
        $stack = debug_backtrace();

        $function = $stack[1]['function'];

        if (isset($stack[1]['class'])) {
            $function = sprintf('%s::%s', $stack[1]['class'], $stack[1]['function']);
        }

        return new static(
            sprintf(
                'Argument #%d of %s() must be %s %s',
                $argument,
                $function,
                in_array(lcfirst($type)[0], ['a', 'e', 'i', 'o', 'u'], true) ? 'an' : 'a',
                $type
            )
        );
    }
}
