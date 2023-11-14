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

use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use RuntimeException;

class BcryptHasher extends AbstractHasher implements HasherContract
{
    /**
     * Алгоритмическую сложность по умолчанию.
     *
     * @var int
     */
    protected $rounds = 10;

    /**
     * Указывает проводить ли проверку способа шифрования при проверке хеша
     *
     * @var bool
     */
    protected $verifyAlgorithm = false;

    /**
     * Создание нового экземпляра драйвера хэширования
     *
     * @param  array  $options Опции хэширования rounds, verify 
     * @return void
     */
    public function __construct(array $options = [])
    {
        $this->rounds = $options['rounds'] ?? $this->rounds;
        $this->verifyAlgorithm = $options['verify'] ?? $this->verifyAlgorithm;
    }

    /**
     * Хэширует указанную строку
     *
     * @param  string  $value Исходная строка
     * @param  array   $options Опции хэширования rounds, verify
     * @return string  Возвращает хэшированную строку
     *
     * @throws \RuntimeException
     */
    public function make($value, array $options = [])
    {
        $hash = password_hash($value, PASSWORD_BCRYPT, [
            'cost' => $this->cost($options),
        ]);

        if ($hash === false) {
            throw new RuntimeException('Bcrypt hashing not supported.');
        }

        return $hash;
    }

    /**
     * Метод проверяет, соответствует ли указанная строка хешу
     *
     * @param  string  $value - Исходная строка
     * @param  string  $hashedValue - Хэш
     * @param  array   $options - Опции
     * @return bool    Возвращает true, если строка соответствует хешу, иначе false
     *
     * @throws \RuntimeException
     */
    public function check($value, $hashedValue, array $options = [])
    {
        if ($this->verifyAlgorithm && $this->info($hashedValue)['algoName'] !== 'bcrypt') {
            throw new RuntimeException('This password does not use the Bcrypt algorithm.');
        }

        return parent::check($value, $hashedValue, $options);
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
        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, [
            'cost' => $this->cost($options),
        ]);
    }

    /**
     * Установка алгоритмической сложности.
     *
     * @param  int  $rounds Алгоритмическая сложность
     * @return $this
     */
    public function setRounds($rounds)
    {
        $this->rounds = (int) $rounds;

        return $this;
    }

    /**
     * Получение алгоритмической сложности из массива с параметрами
     *
     * @param  array  $options Массив с опциями
     * @return int
     */
    protected function cost(array $options = [])
    {
        return $options['rounds'] ?? $this->rounds;
    }
}