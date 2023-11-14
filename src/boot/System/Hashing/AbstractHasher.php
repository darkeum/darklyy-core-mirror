<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Hashing;

abstract class AbstractHasher
{
    /**
     * Получение информации о используемом хешировании строки.
     *
     * @param  string  $hashedValue Искомая строка
     * @return array   Возвращает данные о способе хэширования строки, если способ обнаружить не удалось вернет false
     */
    public function info($hashedValue)
    {
        return password_get_info($hashedValue);
    }

    /**
     * Метод проверяет, соответствует ли указанная строка хешу
     *
     * @param  string  $value - Исходная строка
     * @param  string  $hashedValue - Хэш
     * @param  array   $options - Опции
     * @return bool    Возвращает true, если строка соответствует хешу, иначе false
     */
    public function check($value, $hashedValue, array $options = [])
    {
        if (strlen($hashedValue) === 0) {
            return false;
        }

        return password_verify($value, $hashedValue);
    }
}