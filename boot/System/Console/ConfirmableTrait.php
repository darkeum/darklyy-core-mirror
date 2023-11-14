<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Migrate BaseCommand class
*/

namespace Boot\System\Console;

use Closure;

trait ConfirmableTrait
{
    /**
     * Подтверждение, перед выполнением действия
     * 
     * Метод спрашивает пользователя о намерении выполнить действие если приложение находится в статусе продакшн
     *
     * @param  string  $warning
     * @param  \Closure|bool|null  $callback
     * @return bool
     */
    public function confirmToProceed($warning = 'Приложение находится в статусе продакшн!', $callback = null)
    {
        $callback = is_null($callback) ? $this->getDefaultConfirmCallback() : $callback;

        $shouldConfirm = $callback instanceof Closure ? call_user_func($callback) : $callback;

        if ($shouldConfirm) {
            if ($this->hasOption('force') && $this->option('force')) {
                return true;
            }

            $this->alert($warning);

            $confirmed = $this->confirm('Вы действительно хотите выполнить данную команду?');

            if (! $confirmed) {
                $this->comment('Команда отменена!');

                return false;
            }
        }

        return true;
    }

    /**
     * Получите обратный вызов подтверждения по умолчанию
     *
     * @return \Closure
     */
    protected function getDefaultConfirmCallback()
    {
        return function () {
            return true;
        };
    }
}
