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

use Boot\Support\Manager;
use Boot\Support\Facades\Config;
use Boot\Contracts\Hashing\Hasher;

class HashManager extends Manager implements Hasher
{
    /**
     * Создание экземпляра Bcrypt шифрования
     *
     * @return \Boot\System\Hashing\BcryptHasher
     */
    public function createBcryptDriver()
    {
        return new BcryptHasher(Config::get('hashing.bcrypt') ?? []);
    }

    /**     
     * Создание экземпляра Argon2i шифрования
     *
     * @return \Boot\System\Hashing\ArgonHasher
     */
    public function createArgonDriver()
    {
        return new ArgonHasher(Config::get('hashing.argon') ?? []);
    }

    /**
     * Создание экземпляра Argon2Id шифрования
     *
     * @return \Boot\System\Hashing\Argon2IdHasher
     */
    public function createArgon2idDriver()
    {
        return new Argon2IdHasher(Config::get('hashing.argon') ?? []);
    }

    /**
     * Получение информации о используемом хешировании строки.
     *
     * @param  string  $hashedValue Искомая строка
     * @return array   Возвращает данные о способе хэширования строки, если способ обнаружить не удалось вернет false
     */
    public function info($hashedValue)
    {
        return $this->driver()->info($hashedValue);
    }

    /**
     * Хэширует указанную строку драйвером хэширования по умолчанию
     *
     * @param  string  $value Исходная строка
     * @param  array   $options Опции хэширования
     * @return string  Возвращает хэшированную строку
     */
    public function make($value, array $options = [])
    {
        return $this->driver()->make($value, $options);
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
        return $this->driver()->check($value, $hashedValue, $options);
    }

    /**
     * Проверяет, что указанный хеш соответствует заданным опциям и заданному алгоритму. 
     * Если нет, то можно сделать вывод о том, что хеш надо пересоздать.
     *
     * @param  string  $hashedValue - Хэшированная строка
     * @param  array  $options - Опции хэширования См. https://www.php.net/manual/ru/password.constants.php
     * @return bool Возвращает true, если пароль должен быть перехеширован, или false, если нет.
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        return $this->driver()->needsRehash($hashedValue, $options);
    }

    /**
     * Определения драйвера хэширования по умолчанию
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config->get('hashing.driver', 'bcrypt');
    }
}
