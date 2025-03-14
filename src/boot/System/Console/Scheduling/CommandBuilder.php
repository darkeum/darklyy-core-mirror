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

use Boot\System\Console\Application;
use Illuminate\Support\ProcessUtils;

class CommandBuilder
{
    /**
     * Создайте команду для данного события.
     *
     * @param  \Boot\System\Console\Scheduling\Event  $event
     * @return string
     */
    public function buildCommand(Event $event)
    {
        if ($event->runInBackground) {
            return $this->buildBackgroundCommand($event);
        }

        return $this->buildForegroundCommand($event);
    }

    /**
     * Создайте команду для запуска события на переднем плане.
     *
     * @param  \Boot\System\Console\Scheduling\Event  $event
     * @return string
     */
    protected function buildForegroundCommand(Event $event)
    {
        $output = ProcessUtils::escapeArgument($event->output);

        return laravel_cloud()
            ? $this->ensureCorrectUser($event, $event->command . ' 2>&1 | tee ' . ($event->shouldAppendOutput ? '-a ' : '') . $output)
            : $this->ensureCorrectUser($event, $event->command . ($event->shouldAppendOutput ? ' >> ' : ' > ') . $output . ' 2>&1');
    }

    /**
     * Создайте команду для запуска события в фоновом режиме.
     *
     * @param  \Boot\System\Console\Scheduling\Event  $event
     * @return string
     */
    protected function buildBackgroundCommand(Event $event)
    {
        $output = ProcessUtils::escapeArgument($event->output);

        $redirect = $event->shouldAppendOutput ? ' >> ' : ' > ';

        $finished = Application::formatCommandString('schedule:finish') . ' "' . $event->mutexName() . '"';

        if (windows_os()) {
            return 'start /b cmd /v:on /c "(' . $event->command . ' & ' . $finished . ' ^!ERRORLEVEL^!)' . $redirect . $output . ' 2>&1"';
        }

        return $this->ensureCorrectUser(
            $event,
            '(' . $event->command . $redirect . $output . ' 2>&1 ; ' . $finished . ' "$?") > '
                . ProcessUtils::escapeArgument($event->getDefaultOutput()) . ' 2>&1 &'
        );
    }

    /**
     * Завершите синтаксис команды события с правильным пользователем.
     *
     * @param  \Boot\System\Console\Scheduling\Event  $event
     * @param  string  $command
     * @return string
     */
    protected function ensureCorrectUser(Event $event, $command)
    {
        return $event->user && ! windows_os() ? 'sudo -u ' . $event->user . ' -- sh -c \'' . $command . '\'' : $command;
    }
}
