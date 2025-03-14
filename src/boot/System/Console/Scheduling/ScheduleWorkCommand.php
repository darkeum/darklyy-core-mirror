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

use Boot\Support\Carbon;
use Boot\Support\ProcessUtils;
use Boot\System\Console\Command;
use Boot\System\Console\Application;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'schedule:work')]
class ScheduleWorkCommand extends Command
{
    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $signature  = 'schedule:work {--run-output-file= : Файл для вывода данных из команды <info>schedule:run</info>}';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Запустить обработчик расписания запланированных задач';

    /**
     * Выполнение консольной команды.
     *
     * @return void
     */
    public function handle()
    {
        $this->components->info(
            'Обработчик расписания запланированных задач запущен успешно.',
            $this->getDarklyy()->environment('local') ? OutputInterface::VERBOSITY_NORMAL : OutputInterface::VERBOSITY_VERBOSE
        );

        [$lastExecutionStartedAt, $executions] = [Carbon::now()->subMinutes(10), []];

        $command = Application::formatCommandString('schedule:run');

        if ($this->option('run-output-file')) {
            $command .= ' >> ' . ProcessUtils::escapeArgument($this->option('run-output-file')) . ' 2>&1';
        }

        while (true) {
            usleep(100 * 1000);

            if (
                Carbon::now()->second === 0 &&
                ! Carbon::now()->startOfMinute()->equalTo($lastExecutionStartedAt)
            ) {
                $executions[] = $execution = Process::fromShellCommandline($command);

                $execution->start();

                $lastExecutionStartedAt = Carbon::now()->startOfMinute();
            }

            foreach ($executions as $key => $execution) {
                $output = $execution->getIncrementalOutput() .
                    $execution->getIncrementalErrorOutput();

                $this->output->write(ltrim($output, "\n"));

                if (! $execution->isRunning()) {
                    unset($executions[$key]);
                }
            }
        }
    }
}
