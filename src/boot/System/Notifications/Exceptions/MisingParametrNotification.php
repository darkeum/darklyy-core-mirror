<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Notifications\Exceptions;

use Exception;

final class MisingParametrNotification extends Exception
{
    /**
     * Исключение, когда отсутствует отсутсвует необходимы параметр
     *
     * @param  string $message
     * @return self
     */
    public static function mising(string $message): self
    {
        return new self($message);
    }


}
