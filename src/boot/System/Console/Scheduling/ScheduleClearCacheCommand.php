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

use Boot\System\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'schedule:clear-cache')]
class ScheduleClearCacheCommand extends Command
{
    /**
     * Имя команды консоли.
     *
     * @var string
     */
    protected $name = 'schedule:clear-cache';

    /**
     * Консольная команда Описание.
     *
     * @var string
     */
    protected $description = 'Удалить кешированные файлы, созданные планировщиком';

    /**
     * Выполнить команду консоли.
     *
     * @param  \Boot\System\Scheduling\Schedule  $schedule
     * @return void
     */
    public function handle(Schedule $schedule)
    {
        $mutexCleared = false;

        foreach ($schedule->events($this->laravel) as $event) {
            if ($event->mutex->exists($event)) {
                $this->line('<info>Удаление mutex для:</info> ' . $event->command);

                $event->mutex->forget($event);

                $mutexCleared = true;
            }
        }

        if (! $mutexCleared) {
            $this->info('Файлов mutex не было найдено.');
        }
    }
}
