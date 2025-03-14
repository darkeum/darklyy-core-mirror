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

use Throwable;
use Boot\Support\Carbon;
use Illuminate\Support\Sleep;
use Boot\Support\Facades\Date;
use Boot\System\Console\Command;
use Boot\System\Console\Application;
use Boot\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Boot\System\Console\Events\ScheduledTaskFailed;
use Illuminate\Contracts\Cache\Repository as Cache;
use Boot\System\Console\Events\ScheduledTaskSkipped;
use Boot\System\Console\Events\ScheduledTaskFinished;
use Boot\System\Console\Events\ScheduledTaskStarting;

#[AsCommand(name: 'schedule:run')]
class ScheduleRunCommand extends Command
{
    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'schedule:run';

    /**
     *  Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Выполнить запланированные команды';

    /**
     * Экземпляр планировщика.
     *
     * @var \Boot\System\Console\Scheduling\Schedule
     */
    protected $schedule;

    /**
     * 24-часовая временная метка начала выполнения этой команды планировщика.
     *
     * @var \Boot\Support\Carbon
     */
    protected $startedAt;

    /**
     * Проверьте, произошли ли какие-либо события.
     *
     * @var bool
     */
    protected $eventsRan = false;

    /**
     * Диспетчер событий.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * Обработчик исключений.
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $handler;

    /**
     * Реализация кэш-хранилища.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Двоичный файл PHP, используемый командой.
     *
     * @var string
     */
    protected $phpBinary;

    /**
     * Создайте новый экземпляр команды.
     *
     * @return void
     */
    public function __construct()
    {
        $this->startedAt = Date::now();

        parent::__construct();
    }

    /**
     * Выполнение консольной команды.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     * @param  \Illuminate\Contracts\Debug\ExceptionHandler  $handler
     * @return void
     */
    public function handle(Schedule $schedule, Dispatcher $dispatcher, Cache $cache, ExceptionHandler $handler)
    {
        $this->schedule = $schedule;
        $this->dispatcher = $dispatcher;
        $this->cache = $cache;
        $this->handler = $handler;
        $this->phpBinary = Application::phpBinary();

        $this->newLine();

        $events = $this->schedule->dueEvents($this->laravel);

        if ($events->contains->isRepeatable()) {
            $this->clearInterruptSignal();
        }

        foreach ($events as $event) {
            if (! $event->filtersPass($this->laravel)) {
                $this->dispatcher->dispatch(new ScheduledTaskSkipped($event));

                continue;
            }

            if ($event->onOneServer) {
                $this->runSingleServerEvent($event);
            } else {
                $this->runEvent($event);
            }

            $this->eventsRan = true;
        }

        if ($events->contains->isRepeatable()) {
            $this->repeatEvents($events->filter->isRepeatable());
        }

        if (! $this->eventsRan) {
            $this->components->info('Нет запланированных команд, готовых к выполнению.');
        } else {
            $this->newLine();
        }
    }

    /**
     *Запустить заданное событие одного сервера.
     *
     * @param  \Boot\System\Console\Scheduling\Event  $event
     * @return void
     */
    protected function runSingleServerEvent($event)
    {
        if ($this->schedule->serverShouldRun($event, $this->startedAt)) {
            $this->runEvent($event);
        } else {
            $this->components->info(sprintf(
                'Пропуск [%s], так как команда уже запущена на другом сервере.',
                $event->getSummaryForDisplay()
            ));
        }
    }

    /**
     * Run the given event.
     *
     * @param  \Boot\System\Console\Scheduling\Event  $event
     * @return void
     */
    protected function runEvent($event)
    {
        $summary = $event->getSummaryForDisplay();

        $command = $event instanceof CallbackEvent
            ? $summary
            : trim(str_replace($this->phpBinary, '', $event->command));

        $description = sprintf(
            '<fg=gray>%s</> Выполняется [%s]%s',
            Carbon::now()->format('Y-m-d H:i:s'),
            $command,
            $event->runInBackground ? ' в фоне' : '',
        );

        $this->components->task($description, function () use ($event) {
            $this->dispatcher->dispatch(new ScheduledTaskStarting($event));

            $start = microtime(true);

            try {
                $event->run($this->laravel);

                $this->dispatcher->dispatch(new ScheduledTaskFinished(
                    $event,
                    round(microtime(true) - $start, 2)
                ));

                $this->eventsRan = true;
            } catch (Throwable $e) {
                $this->dispatcher->dispatch(new ScheduledTaskFailed($event, $e));

                $this->handler->report($e);
            }

            return $event->exitCode == 0;
        });

        if (! $event instanceof CallbackEvent) {
            $this->components->bulletList([
                $event->getSummaryForDisplay(),
            ]);
        }
    }

    /**
     * Запустить заданные повторяющиеся события.
     *
     * @param  \Illuminate\Support\Collection<\Boot\system\Console\Scheduling\Event>  $events
     * @return void
     */
    protected function repeatEvents($events)
    {
        $hasEnteredMaintenanceMode = false;

        while (Date::now()->lte($this->startedAt->endOfMinute())) {
            foreach ($events as $event) {
                if ($this->shouldInterrupt()) {
                    return;
                }

                if (! $event->shouldRepeatNow()) {
                    continue;
                }

                $hasEnteredMaintenanceMode = $hasEnteredMaintenanceMode || $this->laravel->isDownForMaintenance();

                if ($hasEnteredMaintenanceMode && ! $event->runsInMaintenanceMode()) {
                    continue;
                }

                if (! $event->filtersPass($this->laravel)) {
                    $this->dispatcher->dispatch(new ScheduledTaskSkipped($event));

                    continue;
                }

                if ($event->onOneServer) {
                    $this->runSingleServerEvent($event);
                } else {
                    $this->runEvent($event);
                }

                $this->eventsRan = true;
            }

            Sleep::usleep(100000);
        }
    }

    /**
     * Определите, следует ли прерывать выполнение расписания.
     *
     * @return bool
     */
    protected function shouldInterrupt()
    {
        return $this->cache->get('illuminate:schedule:interrupt', false);
    }

    /**
     * Убедитесь, что сигнал прерывания сброшен.
     *
     * @return void
     */
    protected function clearInterruptSignal()
    {
        $this->cache->forget('illuminate:schedule:interrupt');
    }
}
