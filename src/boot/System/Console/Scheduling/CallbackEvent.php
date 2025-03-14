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

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Reflector;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use Throwable;

class CallbackEvent extends Event
{
    /**
     * Обратный вызов.
     *
     * @var string
     */
    protected $callback;

    /**
     * Параметры для передачи методу.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Результат выполнения обратного вызова.
     *
     * @var mixed
     */
    protected $result;

    /**
     * Исключение, возникшее при вызове обратного вызова, если таковое имеется.
     *
     * @var \Throwable|null
     */
    protected $exception;

    /**
     * Создайте новый экземпляр события.
     *
     * @param  \Boot\System\Console\Scheduling\EventMutex  $mutex
     * @param  string|callable  $callback
     * @param  array  $parameters
     * @param  \DateTimeZone|string|null  $timezone
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(EventMutex $mutex, $callback, array $parameters = [], $timezone = null)
    {
        if (! is_string($callback) && ! Reflector::isCallable($callback)) {
            throw new InvalidArgumentException(
                'Недопустимое запланированное событие обратного вызова. Должна быть строкой или вызываемой.'
            );
        }

        $this->mutex = $mutex;
        $this->callback = $callback;
        $this->parameters = $parameters;
        $this->timezone = $timezone;
    }

    /**
     * Запустите событие обратного вызова.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return mixed
     *
     * @throws \Throwable
     */
    public function run(Container $container)
    {
        parent::run($container);

        if ($this->exception) {
            throw $this->exception;
        }

        return $this->result;
    }

    /**
     * Определите, следует ли пропустить событие из-за наложения другого процесса.
     *
     * @return bool
     */
    public function shouldSkipDueToOverlapping()
    {
        return $this->description && parent::shouldSkipDueToOverlapping();
    }

    /**
     * Укажите, что обратный вызов должен выполняться в фоновом режиме.
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    public function runInBackground()
    {
        throw new RuntimeException('Запланированные закрытия не могут выполняться в фоновом режиме.');
    }

    /**
     * Запустите обратный вызов.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return int
     */
    protected function execute($container)
    {
        try {
            $this->result = is_object($this->callback)
                ? $container->call([$this->callback, '__invoke'], $this->parameters)
                : $container->call($this->callback, $this->parameters);

            return $this->result === false ? 1 : 0;
        } catch (Throwable $e) {
            $this->exception = $e;

            return 1;
        }
    }

    /**
     * Не допускайте, чтобы события накладывались друг на друга.
     *
     * Срок действия базовой блокировки кэша может быть указан в минутах.
     *
     * @param  int  $expiresAt
     * @return $this
     *
     * @throws \LogicException
     */
    public function withoutOverlapping($expiresAt = 1440)
    {
        if (! isset($this->description)) {
            throw new LogicException(
                "Имя запланированного события необходимо для предотвращения перекрытия. Используйте метод 'name' перед 'withoutOverlapping'."
            );
        }

        return parent::withoutOverlapping($expiresAt);
    }

    /**
     * Разрешить запуск события только на одном сервере для каждого выражения cron.
     *
     * @return $this
     *
     * @throws \LogicException
     */
    public function onOneServer()
    {
        if (! isset($this->description)) {
            throw new LogicException(
                "Имя запланированного события требуется для запуска только на одном сервере. Используйте метод 'name' перед 'onOneServer'."
            );
        }

        return parent::onOneServer();
    }

    /**
     * Получите сводку события для отображения.
     *
     * @return string
     */
    public function getSummaryForDisplay()
    {
        if (is_string($this->description)) {
            return $this->description;
        }

        return is_string($this->callback) ? $this->callback : 'Callback';
    }

    /**
     * Получите имя mutex для запланированной команды.
     *
     * @return string
     */
    public function mutexName()
    {
        return 'framework/schedule-' . sha1($this->description ?? '');
    }

    /**
     * Очистить mutex для события.
     *
     * @return void
     */
    protected function removeMutex()
    {
        if ($this->description) {
            parent::removeMutex();
        }
    }
}
