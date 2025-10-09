<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Cache\RateLimiting;

class Limit
{
    /**
     * Ключ ограничения лимита.
     *
     * @var mixed
     */
    public $key;

    /**
     * Максимальное количество попыток, разрешенное в течение заданного количества секунд.
     *
     * @var int
     */
    public $maxAttempts;

    /**
     * Количество секунд до сброса ограничения скорости.
     *
     * @var int
     */
    public $decaySeconds;

    /**
     * Обратный вызов after используется для определения того, следует ли сработать ограничитель.
     *
     * @var ?callable
     */
    public $afterCallback = null;

    /**
     * Обратный вызов генератора ответов.
     *
     * @var callable
     */
    public $responseCallback;

    /**
     * Создайте новый экземпляр лимита.
     *
     * @param  mixed  $key
     * @param  int  $maxAttempts
     * @param  int  $decaySeconds
     */
    public function __construct($key = '', int $maxAttempts = 60, int $decaySeconds = 60)
    {
        $this->key = $key;
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;
    }

    /**
     * Создание нового лимита в секундах
     *
     * @param  int  $maxAttempts
     * @param  int  $decaySeconds
     * @return static
     */
    public static function perSecond($maxAttempts, $decaySeconds = 1)
    {
        return new static('', $maxAttempts, $decaySeconds);
    }

    /**
     * Создание нового лимита в минутах
     *
     * @param  int  $maxAttempts
     * @param  int  $decayMinutes
     * @return static
     */
    public static function perMinute($maxAttempts, $decayMinutes = 1)
    {
        return new static('', $maxAttempts, 60 * $decayMinutes);
    }

    /**
     * Создание нового лимита, используя минуты в качестве времени затухания.
     *
     * @param  int  $decayMinutes
     * @param  int  $maxAttempts
     * @return static
     */
    public static function perMinutes($decayMinutes, $maxAttempts)
    {
        return new static('', $maxAttempts, 60 * $decayMinutes);
    }

    /**
     *  Создание нового лимита в часах
     *
     * @param  int  $maxAttempts
     * @param  int  $decayHours
     * @return static
     */
    public static function perHour($maxAttempts, $decayHours = 1)
    {
        return new static('', $maxAttempts, 60 * 60 * $decayHours);
    }

    /**
     *  Создание нового лимита, используя часы в качестве времени затухания.
     *
     * @param  int  $maxAttempts
     * @param  int  $decayDays
     * @return static
     */
    public static function perDay($maxAttempts, $decayDays = 1)
    {
        return new static('', $maxAttempts, 60 * 60 * 24 * $decayDays);
    }

    /**
     * Создайте новый безлимитный лимит скорости.
     *
     * @return static
     */
    public static function none()
    {
        return new Unlimited;
    }

    /**
     * Установите ключ ограничения скорости.
     *
     * @param  mixed  $key
     * @return $this
     */
    public function by($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Установите обратный вызов, чтобы определить, следует ли сработать ограничитель.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function after($callback)
    {
        $this->afterCallback = $callback;

        return $this;
    }

    /**
     * Установите обратный вызов, который должен генерировать ответ при превышении лимита..
     *
     * @param  callable  $callback
     * @return $this
     */
    public function response(callable $callback)
    {
        $this->responseCallback = $callback;

        return $this;
    }

    /**
     * Получите потенциальный запасной ключ для лимита.
     *
     * @return string
     */
    public function fallbackKey()
    {
        $prefix = $this->key ? "{$this->key}:" : '';

        return "{$prefix}attempts:{$this->maxAttempts}:decay:{$this->decaySeconds}";
    }
}