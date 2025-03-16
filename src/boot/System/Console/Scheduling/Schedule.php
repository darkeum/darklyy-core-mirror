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

use BadMethodCallException;
use Closure;
use DateTimeInterface;
use Illuminate\Bus\UniqueLock;
use Boot\System\Console\Application;
use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Support\Collection;
use Illuminate\Support\ProcessUtils;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;

class Schedule
{
    use Macroable {
        __call as macroCall;
    }

    const SUNDAY = 0;

    const MONDAY = 1;

    const TUESDAY = 2;

    const WEDNESDAY = 3;

    const THURSDAY = 4;

    const FRIDAY = 5;

    const SATURDAY = 6;

    /**
     * Все события в графике.
     *
     * @var \Boot\System\Console\Scheduling\Event[]
     */
    protected $events = [];

    /**
     * Реализация событие mutex.
     *
     * @var \Boot\System\Console\Scheduling\EventMutex
     */
    protected $eventMutex;

    /**
     * Реализация планирования mutex.
     *
     * @var \Boot\System\Console\Scheduling\SchedulingMutex
     */
    protected $schedulingMutex;

    /**
     * Временный пояс, дата, должна быть оценена.
     *
     * @var \DateTimeZone|string
     */
    protected $timezone;

    /**
     * Реализация работы диспетчера.
     *
     * @var \Illuminate\Contracts\Bus\Dispatcher
     */
    protected $dispatcher;

    /**
     * Кэш mutex результаты.
     *
     * @var array<string, bool>
     */
    protected $mutexCache = [];

    /**
     * Атрибуты, которые должны пройти на мероприятие.
     *
     * @var \Illuminate\Console\Scheduling\PendingEventAttributes|null
     */
    protected $attributes;

    /**
     * Стек атрибутов группы графика.
     *
     * @var array<int, PendingEventAttributes>
     */
    protected array $groupStack = [];

    /**
     * Создайте новый экземпляр графика.
     *
     * @param  \DateTimeZone|string|null  $timezone
     * @return void
     *
     * @throws \RuntimeException
     */
    public function __construct($timezone = null)
    {
        $this->timezone = $timezone;

        if (! class_exists(Container::class)) {
            throw new RuntimeException(
                'Для использования планировщика требуется реализация контейнера. Пожалуйста, установите пакет Illuminate/Container.'
            );
        }

        $container = Container::getInstance();

        $this->eventMutex = $container->bound(EventMutex::class)
            ? $container->make(EventMutex::class)
            : $container->make(CacheEventMutex::class);

        $this->schedulingMutex = $container->bound(SchedulingMutex::class)
            ? $container->make(SchedulingMutex::class)
            : $container->make(CacheSchedulingMutex::class);
    }

    /**
     * Добавьте новое событие обратного вызова в расписание.
     *
     * @param  string|callable  $callback
     * @param  array  $parameters
     * @return \Illuminate\Console\Scheduling\CallbackEvent
     */
    public function call($callback, array $parameters = [])
    {
        $this->events[] = $event = new CallbackEvent(
            $this->eventMutex,
            $callback,
            $parameters,
            $this->timezone
        );

        $this->mergePendingAttributes($event);

        return $event;
    }

    /**
     * Добавьте в расписание новое командное мероприятие Darkander.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return \Boot\System\Console\Scheduling\Event
     */
    public function command($command, array $parameters = [])
    {
        if (class_exists($command)) {
            $command = Container::getInstance()->make($command);

            return $this->exec(
                Application::formatCommandString($command->getName()),
                $parameters,
            )->description($command->getDescription());
        }

        return $this->exec(
            Application::formatCommandString($command),
            $parameters
        );
    }

    /**
     * Добавьте новое событие обратного вызова работы в расписание.
     *
     * @param  object|string  $job
     * @param  string|null  $queue
     * @param  string|null  $connection
     * @return \Illuminate\Console\Scheduling\CallbackEvent
     */
    public function job($job, $queue = null, $connection = null)
    {
        $jobName = $job;

        if (! is_string($job)) {
            $jobName = method_exists($job, 'displayName')
                ? $job->displayName()
                : $job::class;
        }

        return $this->name($jobName)->call(function () use ($job, $queue, $connection) {
            $job = is_string($job) ? Container::getInstance()->make($job) : $job;

            if ($job instanceof ShouldQueue) {
                $this->dispatchToQueue($job, $queue ?? $job->queue, $connection ?? $job->connection);
            } else {
                $this->dispatchNow($job);
            }
        });
    }

    /**
     * Отправить данную работу в очередь.
     *
     * @param  object  $job
     * @param  string|null  $queue
     * @param  string|null  $connection
     * @return void
     *
     * @throws \RuntimeException
     */
    protected function dispatchToQueue($job, $queue, $connection)
    {
        if ($job instanceof Closure) {
            if (! class_exists(CallQueuedClosure::class)) {
                throw new RuntimeException(
                    'Чтобы обеспечить поддержку для закрытия, пожалуйста, установите illuminate/queue пакет.'
                );
            }

            $job = CallQueuedClosure::create($job);
        }

        if ($job instanceof ShouldBeUnique) {
            return $this->dispatchUniqueJobToQueue($job, $queue, $connection);
        }

        $this->getDispatcher()->dispatch(
            $job->onConnection($connection)->onQueue($queue)
        );
    }

    /**
     * Отправьте данную уникальную работу в очередь.
     *
     * @param  object  $job
     * @param  string|null  $queue
     * @param  string|null  $connection
     * @return void
     *
     * @throws \RuntimeException
     */
    protected function dispatchUniqueJobToQueue($job, $queue, $connection)
    {
        if (! Container::getInstance()->bound(Cache::class)) {
            throw new RuntimeException('Кэш драйвер недоступен. Планирование уникальных работ не поддерживается.');
        }

        if (! (new UniqueLock(Container::getInstance()->make(Cache::class)))->acquire($job)) {
            return;
        }

        $this->getDispatcher()->dispatch(
            $job->onConnection($connection)->onQueue($queue)
        );
    }

    /**
     * Отправьте данную работу прямо сейчас.
     *
     * @param  object  $job
     * @return void
     */
    protected function dispatchNow($job)
    {
        $this->getDispatcher()->dispatchNow($job);
    }

    /**
     * Добавьте новое командное событие в расписание.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return \Boot\System\Console\Scheduling\Event
     */
    public function exec($command, array $parameters = [])
    {
        if (count($parameters)) {
            $command .= ' ' . $this->compileParameters($parameters);
        }

        $this->events[] = $event = new Event($this->eventMutex, $command, $this->timezone);

        $this->mergePendingAttributes($event);

        return $event;
    }

    /**
     * Создайте новую группу расписания.
     *
     * @param  \Boot\System\Console\Scheduling\Event  $event
     * @return void
     *
     * @throws \RuntimeException
     */
    public function group(Closure $events)
    {
        if ($this->attributes === null) {
            throw new RuntimeException('Вызовите метод атрибутов, такой как Prade :: Daily (), прежде чем определить группу графика.');
        }

        $this->groupStack[] = $this->attributes;

        $events($this);

        array_pop($this->groupStack);
    }

    /**
     * Объедините текущие атрибуты группы с данным событием.
     *
     * @param  \Boot\System\Console\Scheduling\Event  $event
     * @return void
     */
    protected function mergePendingAttributes(Event $event)
    {
        if (isset($this->attributes)) {
            $this->attributes->mergeAttributes($event);

            $this->attributes = null;
        }

        if (! empty($this->groupStack)) {
            $group = end($this->groupStack);

            $group->mergeAttributes($event);
        }
    }

    /**
     * Параметры компиляции для команды.
     *
     * @param  array  $parameters
     * @return string
     */
    protected function compileParameters(array $parameters)
    {
        return (new Collection($parameters))->map(function ($value, $key) {
            if (is_array($value)) {
                return $this->compileArrayInput($key, $value);
            }

            if (! is_numeric($value) && ! preg_match('/^(-.$|--.*)/i', $value)) {
                $value = ProcessUtils::escapeArgument($value);
            }

            return is_numeric($key) ? $value : "{$key}={$value}";
        })->implode(' ');
    }

    /**
     * Компиляция ввода массива для команды.
     *
     * @param  string|int  $key
     * @param  array  $value
     * @return string
     */
    public function compileArrayInput($key, $value)
    {
        $value = (new Collection($value))->map(function ($value) {
            return ProcessUtils::escapeArgument($value);
        });

        if (str_starts_with($key, '--')) {
            $value = $value->map(function ($value) use ($key) {
                return "{$key}={$value}";
            });
        } elseif (str_starts_with($key, '-')) {
            $value = $value->map(function ($value) use ($key) {
                return "{$key} {$value}";
            });
        }

        return $value->implode(' ');
    }

    /**
     * Определите, разрешено ли сервер для запуска этого события.
     *
     * @param  \Boot\System\Console\Scheduling\Event  $event
     * @param  \DateTimeInterface  $time
     * @return bool
     */
    public function serverShouldRun(Event $event, DateTimeInterface $time)
    {
        return $this->mutexCache[$event->mutexName()] ??= $this->schedulingMutex->create($event, $time);
    }

    /**
     * Получите все события в расписании, которое должно быть.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return \Illuminate\Support\Collection
     */
    public function dueEvents($app)
    {
        return (new Collection($this->events))->filter->isDue($app);
    }

    /**
     * Получите все события по расписанию.
     *
     * @return \Boot\System\Console\Scheduling\Event[]
     */
    public function events()
    {
        return $this->events;
    }

    /**
     * Укажите хранилище кеша, которое следует использовать для хранения mutexes.
     *
     * @param  string  $store
     * @return $this
     */
    public function useCache($store)
    {
        if ($this->eventMutex instanceof CacheAware) {
            $this->eventMutex->useStore($store);
        }

        if ($this->schedulingMutex instanceof CacheAware) {
            $this->schedulingMutex->useStore($store);
        }

        return $this;
    }

    /**
     * Получите диспетчер работы, если таковые имеются.
     *
     * @return \Illuminate\Contracts\Bus\Dispatcher
     *
     * @throws \RuntimeException
     */
    protected function getDispatcher()
    {
        if ($this->dispatcher === null) {
            try {
                $this->dispatcher = Container::getInstance()->make(Dispatcher::class);
            } catch (BindingResolutionException $e) {
                throw new RuntimeException(
                    'Невозможно разрешить диспетчер из контейнера обслуживания. Пожалуйста, свяжите его или установите illuminate/bus.',
                    is_int($e->getCode()) ? $e->getCode() : 0,
                    $e
                );
            }
        }

        return $this->dispatcher;
    }

    /**
     * Динамически обрабатывать вызовы в экземпляр графика.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if (method_exists(PendingEventAttributes::class, $method)) {
            $this->attributes ??= end($this->groupStack) ?: new PendingEventAttributes($this);

            return $this->attributes->$method(...$parameters);
        }

        throw new BadMethodCallException(sprintf(
            'Метод %s::%s не существует.',
            static::class,
            $method
        ));
    }
}
