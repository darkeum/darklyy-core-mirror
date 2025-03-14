<?php

namespace Boot\System\Console\Scheduling;

use Closure;
use Throwable;
use Cron\CronExpression;
use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;
use Boot\System\Console\Application;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Date;
use Illuminate\Contracts\Mail\Mailer;
use Symfony\Component\Process\Process;
use Illuminate\Support\Traits\Tappable;
use Illuminate\Support\Traits\Macroable;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Contracts\Container\Container;
use Psr\Http\Client\ClientExceptionInterface;
use Illuminate\Support\Traits\ReflectsClosures;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Telegram\Bot\HttpClients\HttpClientInterface;
use Boot\System\Console\Scheduling\ManagesAttributes;

class Event
{
    use Macroable, ManagesAttributes, ManagesFrequencies, ReflectsClosures, Tappable;

    /**
     * Командная строка.
     *
     * @var string|null
     */
    public $command;

    /**
     * Место, куда следует отправлять выходные данные.
     *
     * @var string
     */
    public $output = '/dev/null';

    /**
     * Указывает, следует ли добавлять вывод.
     *
     * @var bool
     */
    public $shouldAppendOutput = false;

    /**
     * Массив обратных вызовов, которые необходимо выполнить перед началом события.
     *
     * @var array
     */
    protected $beforeCallbacks = [];

    /**
     * Массив обратных вызовов, которые будут запущены после завершения события.
     *
     * @var array
     */
    protected $afterCallbacks = [];

    /**
     * Реализация mutex событий.
     *
     * @var \Boot\System\Console\Scheduling\EventMutex
     */
    public $mutex;

    /**
     * Обратный вызов распознавателя имен мьютексов.
     *
     * @var \Closure|null
     */
    public $mutexNameResolver;

    /**
     * Последний раз мероприятие проверялось на предмет возможности проведения.
     *
     * Используется для повторяющихся событий с интервалом менее минуты.
     *
     * @var \Boot\Support\Carbon|null
     */
    protected $lastChecked;

    /**
     * Код статуса завершения команды.
     *
     * @var int|null
     */
    public $exitCode;

    /**
     * Создайте новый экземпляр события.
     *
     * @param  \Boot\System\Console\Scheduling\EventMutex  $mutex
     * @param  string  $command
     * @param  \DateTimeZone|string|null  $timezone
     * @return void
     */
    public function __construct(EventMutex $mutex, $command, $timezone = null)
    {
        $this->mutex = $mutex;
        $this->command = $command;
        $this->timezone = $timezone;

        $this->output = $this->getDefaultOutput();
    }

    /**
     * Получите вывод по умолчанию в зависимости от ОС.
     *
     * @return string
     */
    public function getDefaultOutput()
    {
        return (DIRECTORY_SEPARATOR === '\\') ? 'NUL' : '/dev/null';
    }

    /**
     * Запустить указанное событие.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     *
     * @throws \Throwable
     */
    public function run(Container $container)
    {
        if ($this->shouldSkipDueToOverlapping()) {
            return;
        }

        $exitCode = $this->start($container);

        if (! $this->runInBackground) {
            $this->finish($container, $exitCode);
        }
    }

    /**
     * Определите, следует ли пропустить событие из-за наложения другого процесса.
     *
     * @return bool
     */
    public function shouldSkipDueToOverlapping()
    {
        return $this->withoutOverlapping && ! $this->mutex->create($this);
    }

    /**
     * Определите, настроено ли событие на повторение несколько раз в минуту.
     *
     * @return bool
     */
    public function isRepeatable()
    {
        return ! is_null($this->repeatSeconds);
    }

    /**
     * Определите, готово ли событие к повторению.
     *
     * @return bool
     */
    public function shouldRepeatNow()
    {
        return $this->isRepeatable()
            && $this->lastChecked?->diffInSeconds() >= $this->repeatSeconds;
    }

    /**
     * Запустите командный процесс.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return int
     *
     * @throws \Throwable
     */
    protected function start($container)
    {
        try {
            $this->callBeforeCallbacks($container);

            return $this->execute($container);
        } catch (Throwable $exception) {
            $this->removeMutex();

            throw $exception;
        }
    }

    /**
     * Запустите командный процесс.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return int
     */
    protected function execute($container)
    {
        return Process::fromShellCommandline(
            $this->buildCommand(), base_path(), null, null, null
        )->run(
            laravel_cloud()
                ? fn ($type, $line) => fwrite($type === 'out' ? STDOUT : STDERR, $line)
                : fn () => true
        );
    }

    /**
     * Отметьте процесс команды как завершенный и запустите обратные вызовы/очистку.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @param  int  $exitCode
     * @return void
     */
    public function finish(Container $container, $exitCode)
    {
        $this->exitCode = (int) $exitCode;

        try {
            $this->callAfterCallbacks($container);
        } finally {
            $this->removeMutex();
        }
    }

    /**
     * Вызов всех обратных вызовов «до» события.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    public function callBeforeCallbacks(Container $container)
    {
        foreach ($this->beforeCallbacks as $callback) {
            $container->call($callback);
        }
    }

    /**
     * Вызов всех обратных вызовов «после» события.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    public function callAfterCallbacks(Container $container)
    {
        foreach ($this->afterCallbacks as $callback) {
            $container->call($callback);
        }
    }

    /**
     * Создайте командную строку.
     *
     * @return string
     */
    public function buildCommand()
    {
        return (new CommandBuilder)->buildCommand($this);
    }

    /**
     * Определите, следует ли запустить данное событие, на основе выражения Cron.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return bool
     */
    public function isDue($app)
    {
        if (! $this->runsInMaintenanceMode() && $app->isDownForMaintenance()) {
            return false;
        }

        return $this->expressionPasses() &&
               $this->runsInEnvironment($app->environment());
    }

    /**
     * Определите, выполняется ли событие в режиме обслуживания.
     *
     * @return bool
     */
    public function runsInMaintenanceMode()
    {
        return $this->evenInMaintenanceMode;
    }

    /**
     * Определите, проходит ли выражение Cron.
     *
     * @return bool
     */
    protected function expressionPasses()
    {
        $date = Date::now();

        if ($this->timezone) {
            $date = $date->setTimezone($this->timezone);
        }

        return (new CronExpression($this->expression))->isDue($date->toDateTimeString());
    }

    /**
     * Определите, происходит ли событие в данной среде.
     *
     * @param  string  $environment
     * @return bool
     */
    public function runsInEnvironment($environment)
    {
        return empty($this->environments) || in_array($environment, $this->environments);
    }

    /**
     * Определите, соответствуют ли фильтры событию.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return bool
     */
    public function filtersPass($app)
    {
        $this->lastChecked = Date::now();

        foreach ($this->filters as $callback) {
            if (! $app->call($callback)) {
                return false;
            }
        }

        foreach ($this->rejects as $callback) {
            if ($app->call($callback)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Убедитесь, что выходные данные сохранены на диске в файле журнала.
     *
     * @return $this
     */
    public function storeOutput()
    {
        $this->ensureOutputIsBeingCaptured();

        return $this;
    }

    /**
     * Отправить вывод команды в указанное место.
     *
     * @param  string  $location
     * @param  bool  $append
     * @return $this
     */
    public function sendOutputTo($location, $append = false)
    {
        $this->output = $location;

        $this->shouldAppendOutput = $append;

        return $this;
    }

    /**
     * Добавить вывод команды в указанное место.
     *
     * @param  string  $location
     * @return $this
     */
    public function appendOutputTo($location)
    {
        return $this->sendOutputTo($location, true);
    }

    /**
     * Результаты запланированной операции отправьте по электронной почте.
     *
     * @param  array|mixed  $addresses
     * @param  bool  $onlyIfOutputExists
     * @return $this
     *
     * @throws \LogicException
     */
    public function emailOutputTo($addresses, $onlyIfOutputExists = true)
    {
        $this->ensureOutputIsBeingCaptured();

        $addresses = Arr::wrap($addresses);

        return $this->then(function (Mailer $mailer) use ($addresses, $onlyIfOutputExists) {
            $this->emailOutput($mailer, $addresses, $onlyIfOutputExists);
        });
    }

    /**
     * Если запланированная операция дала какой-либо результат, отправьте ее по электронной почте.
     *
     * @param  array|mixed  $addresses
     * @return $this
     *
     * @throws \LogicException
     */
    public function emailWrittenOutputTo($addresses)
    {
        return $this->emailOutputTo($addresses, true);
    }

    /**
     * Если запланированная операция не удалась, отправьте по электронной почте результаты.
     *
     * @param  array|mixed  $addresses
     * @return $this
     */
    public function emailOutputOnFailure($addresses)
    {
        $this->ensureOutputIsBeingCaptured();

        $addresses = Arr::wrap($addresses);

        return $this->onFailure(function (Mailer $mailer) use ($addresses) {
            $this->emailOutput($mailer, $addresses, false);
        });
    }

    /**
     * Убедитесь, что вывод команды фиксируется.
     *
     * @return void
     */
    protected function ensureOutputIsBeingCaptured()
    {
        if (is_null($this->output) || $this->output == $this->getDefaultOutput()) {
            $this->sendOutputTo(storage_path('logs/schedule-'.sha1($this->mutexName()).'.log'));
        }
    }

    /**
     * Отправьте получателям по электронной почте результаты мероприятия.
     *
     * @param  \Illuminate\Contracts\Mail\Mailer  $mailer
     * @param  array  $addresses
     * @param  bool  $onlyIfOutputExists
     * @return void
     */
    protected function emailOutput(Mailer $mailer, $addresses, $onlyIfOutputExists = true)
    {
        $text = is_file($this->output) ? file_get_contents($this->output) : '';

        if ($onlyIfOutputExists && empty($text)) {
            return;
        }

        $mailer->raw($text, function ($m) use ($addresses) {
            $m->to($addresses)->subject($this->getEmailSubject());
        });
    }

    /**
     * Получите тему электронного письма для получения результатов.
     *
     * @return string
     */
    protected function getEmailSubject()
    {
        if ($this->description) {
            return $this->description;
        }

        return "Вывод запланированного задания [{$this->command}]";
    }

    /**
     * Зарегистрируйте обратный вызов для проверки связи с указанным URL-адресом перед запуском задания.
     *
     * @param  string  $url
     * @return $this
     */
    public function pingBefore($url)
    {
        return $this->before($this->pingCallback($url));
    }

    /**
     * Зарегистрируйте обратный вызов для отправки ping-запроса на указанный URL-адрес перед запуском задания, если заданное условие выполняется.
     *
     * @param  bool  $value
     * @param  string  $url
     * @return $this
     */
    public function pingBeforeIf($value, $url)
    {
        return $value ? $this->pingBefore($url) : $this;
    }

    /**
     * Зарегистрируйте обратный вызов для отправки ping-запроса на указанный URL-адрес после выполнения задания.
     *
     * @param  string  $url
     * @return $this
     */
    public function thenPing($url)
    {
        return $this->then($this->pingCallback($url));
    }

    /**
     * Register a callback to ping a given URL after the job runs if the given condition is true.
     *
     * @param  bool  $value
     * @param  string  $url
     * @return $this
     */
    public function thenPingIf($value, $url)
    {
        return $value ? $this->thenPing($url) : $this;
    }

    /**
     * Register a callback to ping a given URL if the operation succeeds.
     *
     * @param  string  $url
     * @return $this
     */
    public function pingOnSuccess($url)
    {
        return $this->onSuccess($this->pingCallback($url));
    }

    /**
     * Register a callback to ping a given URL if the operation succeeds and if the given condition is true.
     *
     * @param  bool  $value
     * @param  string  $url
     * @return $this
     */
    public function pingOnSuccessIf($value, $url)
    {
        return $value ? $this->onSuccess($this->pingCallback($url)) : $this;
    }

    /**
     * Register a callback to ping a given URL if the operation fails.
     *
     * @param  string  $url
     * @return $this
     */
    public function pingOnFailure($url)
    {
        return $this->onFailure($this->pingCallback($url));
    }

    /**
     * Register a callback to ping a given URL if the operation fails and if the given condition is true.
     *
     * @param  bool  $value
     * @param  string  $url
     * @return $this
     */
    public function pingOnFailureIf($value, $url)
    {
        return $value ? $this->onFailure($this->pingCallback($url)) : $this;
    }

    /**
     * Get the callback that pings the given URL.
     *
     * @param  string  $url
     * @return \Closure
     */
    protected function pingCallback($url)
    {
        return function (Container $container) use ($url) {
            try {
                $this->getHttpClient($container)->request('GET', $url);
            } catch (ClientExceptionInterface|TransferException $e) {
                $container->make(ExceptionHandler::class)->report($e);
            }
        };
    }

    /**
     * Get the Guzzle HTTP client to use to send pings.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return \GuzzleHttp\ClientInterface
     */
    protected function getHttpClient(Container $container)
    {
        return match (true) {
            $container->bound(HttpClientInterface::class) => $container->make(HttpClientInterface::class),
            $container->bound(HttpClient::class) => $container->make(HttpClient::class),
            default => new HttpClient([
                'connect_timeout' => 10,
                'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                'timeout' => 30,
            ]),
        };
    }

    /**
     * Register a callback to be called before the operation.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function before(Closure $callback)
    {
        $this->beforeCallbacks[] = $callback;

        return $this;
    }

    /**
     * Register a callback to be called after the operation.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function after(Closure $callback)
    {
        return $this->then($callback);
    }

    /**
     * Register a callback to be called after the operation.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function then(Closure $callback)
    {
        $parameters = $this->closureParameterTypes($callback);

        if (Arr::get($parameters, 'output') === Stringable::class) {
            return $this->thenWithOutput($callback);
        }

        $this->afterCallbacks[] = $callback;

        return $this;
    }

    /**
     * Register a callback that uses the output after the job runs.
     *
     * @param  \Closure  $callback
     * @param  bool  $onlyIfOutputExists
     * @return $this
     */
    public function thenWithOutput(Closure $callback, $onlyIfOutputExists = false)
    {
        $this->ensureOutputIsBeingCaptured();

        return $this->then($this->withOutputCallback($callback, $onlyIfOutputExists));
    }

    /**
     * Register a callback to be called if the operation succeeds.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function onSuccess(Closure $callback)
    {
        $parameters = $this->closureParameterTypes($callback);

        if (Arr::get($parameters, 'output') === Stringable::class) {
            return $this->onSuccessWithOutput($callback);
        }

        return $this->then(function (Container $container) use ($callback) {
            if ($this->exitCode === 0) {
                $container->call($callback);
            }
        });
    }

    /**
     * Register a callback that uses the output if the operation succeeds.
     *
     * @param  \Closure  $callback
     * @param  bool  $onlyIfOutputExists
     * @return $this
     */
    public function onSuccessWithOutput(Closure $callback, $onlyIfOutputExists = false)
    {
        $this->ensureOutputIsBeingCaptured();

        return $this->onSuccess($this->withOutputCallback($callback, $onlyIfOutputExists));
    }

    /**
     * Register a callback to be called if the operation fails.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function onFailure(Closure $callback)
    {
        $parameters = $this->closureParameterTypes($callback);

        if (Arr::get($parameters, 'output') === Stringable::class) {
            return $this->onFailureWithOutput($callback);
        }

        return $this->then(function (Container $container) use ($callback) {
            if ($this->exitCode !== 0) {
                $container->call($callback);
            }
        });
    }

    /**
     * Register a callback that uses the output if the operation fails.
     *
     * @param  \Closure  $callback
     * @param  bool  $onlyIfOutputExists
     * @return $this
     */
    public function onFailureWithOutput(Closure $callback, $onlyIfOutputExists = false)
    {
        $this->ensureOutputIsBeingCaptured();

        return $this->onFailure($this->withOutputCallback($callback, $onlyIfOutputExists));
    }

    /**
     * Get a callback that provides output.
     *
     * @param  \Closure  $callback
     * @param  bool  $onlyIfOutputExists
     * @return \Closure
     */
    protected function withOutputCallback(Closure $callback, $onlyIfOutputExists = false)
    {
        return function (Container $container) use ($callback, $onlyIfOutputExists) {
            $output = $this->output && is_file($this->output) ? file_get_contents($this->output) : '';

            return $onlyIfOutputExists && empty($output)
                ? null
                : $container->call($callback, ['output' => new Stringable($output)]);
        };
    }

    /**
     * Get the summary of the event for display.
     *
     * @return string
     */
    public function getSummaryForDisplay()
    {
        if (is_string($this->description)) {
            return $this->description;
        }

        return $this->buildCommand();
    }

    /**
     * Determine the next due date for an event.
     *
     * @param  \DateTimeInterface|string  $currentTime
     * @param  int  $nth
     * @param  bool  $allowCurrentDate
     * @return \Illuminate\Support\Carbon
     */
    public function nextRunDate($currentTime = 'now', $nth = 0, $allowCurrentDate = false)
    {
        return Date::instance((new CronExpression($this->getExpression()))
            ->getNextRunDate($currentTime, $nth, $allowCurrentDate, $this->timezone));
    }

    /**
     * Get the Cron expression for the event.
     *
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * Set the event mutex implementation to be used.
     *
     * @param  \Illuminate\Console\Scheduling\EventMutex  $mutex
     * @return $this
     */
    public function preventOverlapsUsing(EventMutex $mutex)
    {
        $this->mutex = $mutex;

        return $this;
    }

    /**
     * Get the mutex name for the scheduled command.
     *
     * @return string
     */
    public function mutexName()
    {
        $mutexNameResolver = $this->mutexNameResolver;

        if (! is_null($mutexNameResolver) && is_callable($mutexNameResolver)) {
            return $mutexNameResolver($this);
        }

        return 'framework'.DIRECTORY_SEPARATOR.'schedule-'.
            sha1($this->expression.$this->normalizeCommand($this->command ?? ''));
    }

    /**
     * Set the mutex name or name resolver callback.
     *
     * @param  \Closure|string  $mutexName
     * @return $this
     */
    public function createMutexNameUsing(Closure|string $mutexName)
    {
        $this->mutexNameResolver = is_string($mutexName) ? fn () => $mutexName : $mutexName;

        return $this;
    }

    /**
     * Delete the mutex for the event.
     *
     * @return void
     */
    protected function removeMutex()
    {
        if ($this->withoutOverlapping) {
            $this->mutex->forget($this);
        }
    }

    /**
     * Format the given command string with a normalized PHP binary path.
     *
     * @param  string  $command
     * @return string
     */
    public static function normalizeCommand($command)
    {
        return str_replace([
            Application::phpBinary(),
            Application::artisanBinary(),
        ], [
            'php',
            preg_replace("#['\"]#", '', Application::artisanBinary()),
        ], $command);
    }
}
