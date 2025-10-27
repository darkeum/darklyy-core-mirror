<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Contracts\Broadcasting;

interface Factory
{
    /**
     * Получить реализацию вещательной компании по имени.
     *
     * @param string|null $name
     * @return \Boot\Contracts\Broadcasting\Broadcaster
     */
    public function connection($name = null);
}
