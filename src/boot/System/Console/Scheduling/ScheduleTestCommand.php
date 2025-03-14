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
use Boot\System\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\select;

#[AsCommand(name: 'schedule:test')]
class ScheduleTestCommand extends Command
{
    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $signature = 'schedule:test {--name= : Имя запланированной для выполнения команды}';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Выполнить запланированную команду';

    /**
     * Выполнение консольной команды.
     *
     * @param  \Boot\System\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function handle(Schedule $schedule)
    {
        $phpBinary = Application::phpBinary();

        $commands = $schedule->events();

        $commandNames = [];

        foreach ($commands as $command) {
            $commandNames[] = $command->command ?? $command->getSummaryForDisplay();
        }

        if (empty($commandNames)) {
            return $this->components->info('No scheduled commands have been defined.');
        }

        if (! empty($name = $this->option('name'))) {
            $commandBinary = $phpBinary.' '.Application::artisanBinary();

            $matches = array_filter($commandNames, function ($commandName) use ($commandBinary, $name) {
                return trim(str_replace($commandBinary, '', $commandName)) === $name;
            });

            if (count($matches) !== 1) {
                $this->components->info('No matching scheduled command found.');

                return;
            }

            $index = key($matches);
        } else {
            $index = $this->getSelectedCommandByIndex($commandNames);
        }

        $event = $commands[$index];

        $summary = $event->getSummaryForDisplay();

        $command = $event instanceof CallbackEvent
            ? $summary
            : trim(str_replace($phpBinary, '', $event->command));

        $description = sprintf(
            'Running [%s]%s',
            $command,
            $event->runInBackground ? ' normally in background' : '',
        );

        $event->runInBackground = false;

        $this->components->task($description, fn () => $event->run($this->laravel));

        if (! $event instanceof CallbackEvent) {
            $this->components->bulletList([$event->getSummaryForDisplay()]);
        }

        $this->newLine();
    }

    /**
     * Получить выбранное имя команды по индексу.
     *
     * @param  array  $commandNames
     * @return int
     */
    protected function getSelectedCommandByIndex(array $commandNames)
    {
        if (count($commandNames) !== count(array_unique($commandNames))) {
            // Некоторые команды (вероятно, замыкания) имеют одинаковое имя, добавляйте к каждой из них уникальные индексы...
            $uniqueCommandNames = array_map(function ($index, $value) {
                return "$value [$index]";
            }, array_keys($commandNames), $commandNames);

            $selectedCommand = select('Which command would you like to run?', $uniqueCommandNames);

            preg_match('/\[(\d+)\]/', $selectedCommand, $choice);

            return (int) $choice[1];
        } else {
            return array_search(
                select('Which command would you like to run?', $commandNames),
                $commandNames
            );
        }
    }
}