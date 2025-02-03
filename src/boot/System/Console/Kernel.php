<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Console;

use Closure;
use Throwable;
use SplFileInfo;
use ReflectionClass;
use DateTimeInterface;
use Boot\Support\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Arr;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use Boot\System\Console\Command;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\InteractsWithTime;
use Boot\Contracts\Debug\ExceptionHandler;
use Boot\System\Console\Scheduling\Schedule;
use Symfony\Component\Console\ConsoleEvents;
use Boot\System\Console\Application as Artisan;
use Boot\System\Console\Events\CommandFinished;
use Boot\System\Console\Events\CommandStarting;
use Illuminate\Contracts\Foundation\Application;
use Boot\Contracts\Console\Kernel as KernelContract;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Illuminate\Contracts\Events\Dispatcher;

class Kernel implements KernelContract
{

    use InteractsWithTime;

    /**
     * Экзкмпляр приложения.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Реализация диспетчера событий.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * Реализация диспетчера событий Symfony.
     *
     * @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface|null
     */
    protected $symfonyDispatcher;

    /**
     * Экземпляр приложения Artisan.
     *
     * @var \Boot\System\Application|null
     */
    protected $artisan;

    /**
     * Экземпляр приложения Darksander.
     *
     * @var \Boot\System\Application|null
     */
    protected $darksander;

    /**
     * Команды Darksander, предоставляемые приложением.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Указывает, загружены ли команды закрытия.
     *
     * @var bool
     */
    protected $commandsLoaded = false;

    /**
     * Все зарегистрированные обработчики длительности команд.
     *
     * @var array
     */
    protected $commandLifecycleDurationHandlers = [];

    /**
     * Когда началась текущая обрабатываемая команда.
     *
     * @var \Boot\Support\Carbon|null
     */
    protected $commandStartedAt;

    /**
     * Классы начальной загрузки для приложения.
     *
     * @var string[]
     */
    protected $bootstrappers = [
        \Boot\App\Loaders\LoadEnvironmentVariables::class,
        \Boot\App\Loaders\LoadConfiguration::class,
        \Boot\App\HandleExceptions::class,
        \Boot\App\RegisterFacades::class,
        \Boot\App\SetRequestForConsole::class,
        \Boot\App\RegisterProviders::class,
        \Boot\App\BootProviders::class,
    ];

    /**
     * Создайте новый экземпляр ядра консоли.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function __construct(Application $app, Dispatcher $events)
    {
        if (! defined('ARTISAN_BINARY')) {
            define('ARTISAN_BINARY', 'darksander');
        }

        if (! defined('DARKSANDER_BINARY')) {
            define('DARKSANDER_BINARY', 'darksander');
        }

        $this->app = $app;
        $this->events = $events;

        $this->app->booted(function () {
            if (! $this->app->runningUnitTests()) {
                $this->rerouteSymfonyCommandEvents();
            }
            $this->defineConsoleSchedule();
        });
    }

    /**
     * Перенаправьте события команд Symfony на их аналоги Darklyy.
     *
     * @internal
     *
     * @return $this
     */
    public function rerouteSymfonyCommandEvents()
    {
        if (is_null($this->symfonyDispatcher)) {
            $this->symfonyDispatcher = new EventDispatcher;

            $this->symfonyDispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) {
                $this->events->dispatch(
                    new CommandStarting($event->getCommand()->getName(), $event->getInput(), $event->getOutput())
                );
            });

            $this->symfonyDispatcher->addListener(ConsoleEvents::TERMINATE, function (ConsoleTerminateEvent $event) {
                $this->events->dispatch(
                    new CommandFinished($event->getCommand()->getName(), $event->getInput(), $event->getOutput(), $event->getExitCode())
                );
            });
        }

        return $this;
    }


    /**
     * Определите расписание команд приложения.
     *
     * @return void
     */
    protected function defineConsoleSchedule()
    {
        $this->app->singleton(Schedule::class, function ($app) {
            return tap(new Schedule($this->scheduleTimezone()), function ($schedule) {
                $this->schedule($schedule->useCache($this->scheduleCache()));
            });
        });
    }

    /**
     * Получите имя хранилища кэша, которое должно управлять планированием.
     *
     * @return string
     */
    protected function scheduleCache()
    {
        return $this->app['config']->get('cache.schedule_store', Env::get('SCHEDULE_CACHE_DRIVER'));
    }

    /**
     * Запустите консольное приложение.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface|null  $output
     * @return int
     */
    public function handle($input, $output = null)
    {
        $this->commandStartedAt = Carbon::now();

        try {
            if (in_array($input->getFirstArgument(), ['env:encrypt', 'env:decrypt'], true)) {
                $this->bootstrapWithoutBootingProviders();
            }

            $this->bootstrap();

            return $this->getArtisan()->run($input, $output);
        } catch (Throwable $e) {
            $this->reportException($e);

            $this->renderException($output, $e);

            return 1;
        }
    }

    /**
     * Завершите приложение.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  int  $status
     * @return void
     */
    public function terminate($input, $status)
    {
        $this->app->terminate();

        if ($this->commandStartedAt === null) {
            return;
        }

        $this->commandStartedAt->setTimezone($this->app['config']->get('app.timezone') ?? 'UTC');

        foreach ($this->commandLifecycleDurationHandlers as ['threshold' => $threshold, 'handler' => $handler]) {
            $end ??= Carbon::now();

            if ($this->commandStartedAt->diffInMilliseconds($end) > $threshold) {
                $handler($this->commandStartedAt, $input, $status);
            }
        }

        $this->commandStartedAt = null;
    }

    /**
     * Зарегистрируйте обратный вызов, который будет вызван, когда продолжительность жизненного цикла команды превысит заданный промежуток времени.
     *
     * @param  \DateTimeInterface|\Carbon\CarbonInterval|float|int  $threshold
     * @param  callable  $handler
     * @return void
     */
    public function whenCommandLifecycleIsLongerThan($threshold, $handler)
    {
        $threshold = $threshold instanceof DateTimeInterface
            ? $this->secondsUntil($threshold) * 1000
            : $threshold;

        $threshold = $threshold instanceof CarbonInterval
            ? $threshold->totalMilliseconds
            : $threshold;

        $this->commandLifecycleDurationHandlers[] = [
            'threshold' => $threshold,
            'handler' => $handler,
        ];
    }

    /**
     * Когда началась обработка команды.
     *
     * @return \Boot\Support\Carbon|null
     */
    public function commandStartedAt()
    {
        return $this->commandStartedAt;
    }

    /**
     * Определите расписание команд приложения.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }

    /**
     * Получите часовой пояс, который следует использовать по умолчанию для запланированных событий.
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        $config = $this->app['config'];

        return $config->get('app.schedule_timezone', $config->get('app.timezone'));
    }

    /**
     * Зарегистрируйте команды для приложения.
     *
     * @return void
     */
    protected function commands()
    {
        //
    }

    /**
     * Зарегистрируйте команду на основе замыкания в приложении.
     *
     * @param  string  $signature
     * @param  \Closure  $callback
     * @return \Illuminate\Foundation\Console\ClosureCommand
     */
    public function command($signature, Closure $callback)
    {
        $command = new ClosureCommand($signature, $callback);

        Artisan::starting(function ($artisan) use ($command) {
            $artisan->add($command);
        });

        return $command;
    }

    /**
     * Зарегистрируйте все команды в указанном каталоге.
     *
     * @param  array|string  $paths
     * @return void
     */
    protected function load($paths)
    {
        $paths = array_unique(Arr::wrap($paths));

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return;
        }

        $namespace = $this->app->getNamespace();

        foreach (Finder::create()->in($paths)->files() as $file) {
            $command = $this->commandClassFromFile($file, $namespace);

            if (
                is_subclass_of($command, Command::class) &&
                ! (new ReflectionClass($command))->isAbstract()
            ) {
                Artisan::starting(function ($artisan) use ($command) {
                    $artisan->resolve($command);
                });
            }
        }
    }

    /**
     * Извлечь имя класса команды из указанного пути к файлу.
     *
     * @param  \SplFileInfo  $file
     * @param  string  $namespace
     * @return string
     */
    protected function commandClassFromFile(SplFileInfo $file, string $namespace): string
    {
        return $namespace . str_replace(
            ['/', '.php'],
            ['\\', ''],
            Str::after($file->getRealPath(), realpath(app_path()) . DIRECTORY_SEPARATOR)
        );
    }


    /**
     * Зарегистрируйте данную команду в консольном приложении.
     *
     * @param  \Symfony\Component\Console\Command\Command  $command
     * @return void
     */
    public function registerCommand($command)
    {
        $this->getArtisan()->add($command);
    }

    /**
     * Запустите консольную команду Artisan по имени.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @param  \Symfony\Component\Console\Output\OutputInterface|null  $outputBuffer
     * @return int
     *
     * @throws \Symfony\Component\Console\Exception\CommandNotFoundException
     */
    public function call($command, array $parameters = [], $outputBuffer = null)
    {
        if (in_array($command, ['env:encrypt', 'env:decrypt'], true)) {
            $this->bootstrapWithoutBootingProviders();
        }

        $this->bootstrap();

        return $this->getArtisan()->call($command, $parameters, $outputBuffer);
    }

    /**
     * Поставить в очередь данную консольную команду.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function queue($command, array $parameters = [])
    {
        return QueuedCommand::dispatch(func_get_args());
    }

    /**
     * Зарегистрируйте все команды в консоли.
     *
     * @return array
     */
    public function all()
    {
        $this->bootstrap();

        return $this->getArtisan()->all();
    }


    /**
     * Получите вывод последней выполненной команды.
     *
     * @return string
     */
    public function output()
    {
        $this->bootstrap();

        return $this->getArtisan()->output();
    }

    /**
     * Загрузить приложение для команд Artisan.
     *
     * @return void
     */
    public function bootstrap()
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }

        $this->app->loadDeferredProviders();

        if (! $this->commandsLoaded) {
            $this->commands();

            $this->commandsLoaded = true;
        }
    }

    /**
     * Загрузите приложение без загрузки поставщиков услуг.
     *
     * @return void
     */
    public function bootstrapWithoutBootingProviders()
    {
        $this->app->bootstrapWith(
            collect($this->bootstrappers())->reject(function ($bootstrapper) {
                // return $bootstrapper === \Illuminate\Foundation\Bootstrap\BootProviders::class;
            })->all()
        );
    }

    /**
     * Получите экземпляр приложения Artisan.
     *
     * @return \Illuminate\Console\Application
     */
    protected function getArtisan()
    {
        if (is_null($this->artisan)) {
            $this->artisan = (new Artisan($this->app, $this->events, $this->app->version()))
                ->resolveCommands($this->commands)
                ->setContainerCommandLoader();

            if ($this->symfonyDispatcher instanceof EventDispatcher) {
                $this->artisan->setDispatcher($this->symfonyDispatcher);
                $this->artisan->setSignalsToDispatchEvent();
            }
        }

        return $this->artisan;
    }

    /**
     * Установите экземпляр приложения Artisan.
     *
     * @param  \Illuminate\Console\Application|null  $artisan
     * @return void
     */
    public function setArtisan($artisan)
    {
        $this->artisan = $artisan;
    }

    /**
     * Получите классы начальной загрузки для приложения.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }

    /**
     * Сообщите об исключении обработчику исключений.
     *
     * @param  \Throwable  $e
     * @return void
     */
    protected function reportException(Throwable $e)
    {
        $this->app[ExceptionHandler::class]->report($e);
    }

    /**
     * Вывести данное исключение.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  \Throwable  $e
     * @return void
     */
    protected function renderException($output, Throwable $e)
    {
        $this->app[ExceptionHandler::class]->renderForConsole($output, $e);
    }
}
