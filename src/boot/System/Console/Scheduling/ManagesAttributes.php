<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Console\Scheduling;

use Illuminate\Support\Reflector;

trait ManagesAttributes
{
    /**
     * Выражение Cron, представляющее частоту события.
     *
     * @var string
     */
    public $expression = '* * * * *';

    /**
     * Как часто повторять событие в течение минуты.
     *
     * @var int|null
     */
    public $repeatSeconds = null;

    /**
     * Временный пояс, дата, должна быть оценена.
     *
     * @var \DateTimeZone|string
     */
    public $timezone;

    /**
     * Пользователь команда должна работать как.
     *
     * @var string|null
     */
    public $user;

    /**
     * Список сред, в которых команда должна работать.
     *
     * @var array
     */
    public $environments = [];

    /**
     * Указывает, должна ли команда работать в режиме обслуживания.
     *
     * @var bool
     */
    public $evenInMaintenanceMode = false;

    /**
     * Указывает, не должна ли команда перекрываться.
     *
     * @var bool
     */
    public $withoutOverlapping = false;

    /**
     * Указывает, следует ли разрешено запускать только на одном сервере для каждого выражения Cron.
     *
     * @var bool
     */
    public $onOneServer = false;

    /**
     * Количество минут, которые мутекс должен быть действительным.
     *
     * @var int
     */
    public $expiresAt = 1440;

    /**
     * Указывает, должна ли команда работать в фоновом режиме.
     *
     * @var bool
     */
    public $runInBackground = false;

    /**
     * Массив обратных вызовов фильтра.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Массив обратных вызовов.
     *
     * @var array
     */
    protected $rejects = [];

    /**
     * Человеческое читаемое описание события.
     *
     * @var string|null
     */
    public $description;

    /**
     * Установите, какой пользователь команда должна работать как.
     *
     * @param  string  $user
     * @return $this
     */
    public function user($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Ограничьте среды, в которых должна работать команда.
     *
     * @param  array|mixed  $environments
     * @return $this
     */
    public function environments($environments)
    {
        $this->environments = is_array($environments) ? $environments : func_get_args();

        return $this;
    }

    /**
     * Укажите, что команда должна работать даже в режиме обслуживания.
     *
     * @return $this
     */
    public function evenInMaintenanceMode()
    {
        $this->evenInMaintenanceMode = true;

        return $this;
    }

    /**
     * Не позволяйте событию перекрывать друг друга.
     * Время истечения срока базового блокировки кэша может быть указано за считанные минуты.
     *
     * @param  int  $expiresAt
     * @return $this
     */
    public function withoutOverlapping($expiresAt = 1440)
    {
        $this->withoutOverlapping = true;

        $this->expiresAt = $expiresAt;

        return $this->skip(function () {
            return $this->mutex->exists($this);
        });
    }

    /**
     * Позвольте событию работать только на одном сервере для каждого выражения Cron.
     *
     * @return $this
     */
    public function onOneServer()
    {
        $this->onOneServer = true;

        return $this;
    }

    /**
     * Укажите, что команда должна работать в фоновом режиме.
     *
     * @return $this
     */
    public function runInBackground()
    {
        $this->runInBackground = true;

        return $this;
    }

    /**
     * Зарегистрируйте обратный вызов для дальнейшего фильтрации расписания.
     *
     * @param  \Closure|bool  $callback
     * @return $this
     */
    public function when($callback)
    {
        $this->filters[] = Reflector::isCallable($callback) ? $callback : function () use ($callback) {
            return $callback;
        };

        return $this;
    }

    /**
     * Зарегистрируйте обратный вызов для дальнейшего фильтрации расписания.
     *
     * @param  \Closure|bool  $callback
     * @return $this
     */
    public function skip($callback)
    {
        $this->rejects[] = Reflector::isCallable($callback) ? $callback : function () use ($callback) {
            return $callback;
        };

        return $this;
    }

    /**
     * Установите ориентированное на человеку описание события.
     *
     * @param  string  $description
     * @return $this
     */
    public function name($description)
    {
        return $this->description($description);
    }

    /**
     * Установите ориентированное на человеку описание события.
     *
     * @param  string  $description
     * @return $this
     */
    public function description($description)
    {
        $this->description = $description;

        return $this;
    }
}
