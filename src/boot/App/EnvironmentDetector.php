<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\App;

use Closure;

class EnvironmentDetector
{
    /**
     * Определение текущей среды приложения.
     *
     * @param  \Closure  $callback
     * @param  array|null  $consoleArgs
     * @return string
     */
    public function detect(Closure $callback, $consoleArgs = null)
    {
        if ($consoleArgs) {
            return $this->detectConsoleEnvironment($callback, $consoleArgs);
        }

        return $this->detectWebEnvironment($callback);
    }

    /**
     * Установка среды приложения для веб-запроса.
     *
     * @param  \Closure  $callback
     * @return string
     */
    protected function detectWebEnvironment(Closure $callback)
    {
        return $callback();
    }

    /**
     * Установка среды приложения из аргументов командной строки.
     *
     * @param  \Closure  $callback
     * @param  array  $args
     * @return string
     */
    protected function detectConsoleEnvironment(Closure $callback, array $args)
    {      
        //Сначала мы проверим, был ли передан аргумент среды через консольные аргументы
        //и если это так, то автоматически переопределяется как окружение. В противном случае мы
        //проверит среду как "веб-запрос", как обычный HTTP-запрос. 
        if (! is_null($value = $this->getEnvironmentArgument($args))) {
            return $value;
        }

        return $this->detectWebEnvironment($callback);
    }

    /**
     * Получение аргумента среды из консоли.
     *
     * @param  array  $args
     * @return string|null
     */
    protected function getEnvironmentArgument(array $args)
    {
        foreach ($args as $i => $value) {
            if ($value === '--env') {
                return $args[$i + 1] ?? null;
            }

            if (str_starts_with($value, '--env')) {
                return head(array_slice(explode('=', $value), 1));
            }
        }
    }
}
