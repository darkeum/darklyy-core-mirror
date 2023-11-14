<?php

namespace Boot\System\Console\Scheduling;

use Boot\System\Console\Command;

class ScheduleClearCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'schedule:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удалить кешированные файлы , созданные планировщиком';

    /**
     * Execute the console command.
     *
     * @param  \Boot\System\Scheduling\Schedule  $schedule
     * @return void
     */
    public function handle(Schedule $schedule)
    {
        $mutexCleared = false;

        foreach ($schedule->events($this->laravel) as $event) {
            if ($event->mutex->exists($event)) {
                $this->line('<info>Deleting mutex for:</info> '.$event->command);

                $event->mutex->forget($event);

                $mutexCleared = true;
            }
        }

        if (! $mutexCleared) {
            $this->info('No mutex files were found.');
        }
    }
}
