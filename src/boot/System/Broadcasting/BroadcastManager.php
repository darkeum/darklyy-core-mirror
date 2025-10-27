<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Broadcasting;

use Closure;
use Throwable;
use Ably\AblyRest;
use Pusher\Pusher;
use RuntimeException;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use Illuminate\Bus\UniqueLock;
use GuzzleHttp\Client as GuzzleClient;
use Boot\Contracts\Broadcasting\ShouldRescue;
use Boot\Contracts\Broadcasting\ShouldBeUnique;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Boot\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Cache\Repository as Cache;
use Boot\System\Broadcasting\Broadcasters\LogBroadcaster;
use Boot\System\Broadcasting\Broadcasters\NullBroadcaster;
use Boot\Contracts\Broadcasting\Factory as FactoryContract;
use Boot\System\Broadcasting\Broadcasters\RedisBroadcaster;
use Boot\System\Broadcasting\Broadcasters\PusherBroadcaster;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcherContract;

/**
 * @mixin \Boot\Contracts\Broadcasting\Broadcaster
 */
class BroadcastManager implements FactoryContract
{
    /**
     * Экземпляр приложения.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Массив разрешенных широковещательных драйверов.
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * Зарегистрированные создатели пользовательских драйверов.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * Создайте новый экземпляр менеджера.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Зарегистрируйте маршруты для обработки аутентификации широковещательного канала и сокетов.
     *
     * @param  array|null  $attributes
     * @return void
     */
    public function routes(?array $attributes = null)
    {
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        $attributes = $attributes ?: ['middleware' => ['web']];

        $this->app['router']->group($attributes, function ($router) {
            $router->match(
                ['get', 'post'], '/broadcasting/auth',
                '\\'.BroadcastController::class.'@authenticate'
            )->withoutMiddleware([\Boot\App\Http\Middleware\VerifyCsrfToken::class]);
        });
    }

    /**
     * Зарегистрируйте маршруты для обработки широковещательной аутентификации пользователей.
     *
     * @param  array|null  $attributes
     * @return void
     */
    public function userRoutes(?array $attributes = null)
    {
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        $attributes = $attributes ?: ['middleware' => ['web']];

        $this->app['router']->group($attributes, function ($router) {
            $router->match(
                ['get', 'post'], '/broadcasting/user-auth',
                '\\'.BroadcastController::class.'@authenticateUser'
            )->withoutMiddleware([\Boot\App\Http\Middleware\VerifyCsrfToken::class]);
        });
    }

    /**
     * Зарегистрируйте маршруты для обработки широковещательной аутентификации и сокетов.
     *
     * Псевдоним метода "маршруты".
     *
     * @param  array|null  $attributes
     * @return void
     */
    public function channelRoutes(?array $attributes = null)
    {
        $this->routes($attributes);
    }

    /**
     * Получите идентификатор сокета для данного запроса.
     *
     * @param  \Boot\System\Http\Request|null  $request
     * @return string|null
     */
    public function socket($request = null)
    {
        if (! $request && ! $this->app->bound('request')) {
            return;
        }

        $request = $request ?: $this->app['request'];

        return $request->header('X-Socket-ID');
    }

    /**
     * Начать отправку анонимной трансляции на указанные каналы.
     */
    public function on(Channel|string|array $channels): AnonymousEvent
    {
        return new AnonymousEvent($channels);
    }

    /**
     * Начните отправку анонимной трансляции на указанные частные каналы.
     */
    public function private(string $channel): AnonymousEvent
    {
        return $this->on(new PrivateChannel($channel));
    }

    /**
     * Начните отправку анонимной трансляции на указанные каналы присутствия.
     */
    public function presence(string $channel): AnonymousEvent
    {
        return $this->on(new PresenceChannel($channel));
    }

    /**
     * Начните трансляцию мероприятия.
     *
     * @param  mixed  $event
     * @return \Boot\System\Broadcasting\PendingBroadcast
     */
    public function event($event = null)
    {
        return new PendingBroadcast($this->app->make('events'), $event);
    }

    /**
     * Поставить данное событие в очередь для трансляции.
     *
     * @param  mixed  $event
     * @return void
     */
    public function queue($event)
    {
        if ($event instanceof ShouldBroadcastNow ||
            (is_object($event) &&
             method_exists($event, 'shouldBroadcastNow') &&
             $event->shouldBroadcastNow())) {
            $dispatch = fn () => $this->app->make(BusDispatcherContract::class)
                ->dispatchNow(new BroadcastEvent(clone $event));

            return $event instanceof ShouldRescue
                ? $this->rescue($dispatch)
                : $dispatch();
        }

        $queue = null;

        if (method_exists($event, 'broadcastQueue')) {
            $queue = $event->broadcastQueue();
        } elseif (isset($event->broadcastQueue)) {
            $queue = $event->broadcastQueue;
        } elseif (isset($event->queue)) {
            $queue = $event->queue;
        }

        $broadcastEvent = new BroadcastEvent(clone $event);

        if ($event instanceof ShouldBeUnique) {
            $broadcastEvent = new UniqueBroadcastEvent(clone $event);

            if ($this->mustBeUniqueAndCannotAcquireLock($broadcastEvent)) {
                return;
            }
        }

        $push = fn () => $this->app->make('queue')
            ->connection($event->connection ?? null)
            ->pushOn($queue, $broadcastEvent);

        $event instanceof ShouldRescue
            ? $this->rescue($push)
            : $push();
    }

    /**
     * Определите, должно ли широковещательное событие быть уникальным, и определите, можем ли мы получить необходимую блокировку.
     *
     * @param  mixed  $event
     * @return bool
     */
    protected function mustBeUniqueAndCannotAcquireLock($event)
    {
        return ! (new UniqueLock(
            method_exists($event, 'uniqueVia')
                ? $event->uniqueVia()
                : $this->app->make(Cache::class)
        ))->acquire($event);
    }

    /**
     * Получите экземпляр соединения.
     *
     * @param  string|null  $driver
     * @return mixed
     */
    public function connection($driver = null)
    {
        return $this->driver($driver);
    }

    /**
     * Получите экземпляр драйвера.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function driver($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->drivers[$name] = $this->get($name);
    }

    /**
     * Попытайтесь получить соединение из локального кеша.
     *
     * @param  string  $name
     * @return \Boot\Contracts\Broadcasting\Broadcaster
     */
    protected function get($name)
    {
        return $this->drivers[$name] ?? $this->resolve($name);
    }

    /**
     * Разрешите данного вещателя.
     *
     * @param  string  $name
     * @return \Boot\Contracts\Broadcasting\Broadcaster
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Broudcast соединение [{$name}] не определено.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (! method_exists($this, $driverMethod)) {
            throw new InvalidArgumentException("Драйвер [{$config['driver']}] не поддерживается.");
        }

        try {
            return $this->{$driverMethod}($config);
        } catch (Throwable $e) {
            throw new RuntimeException("Не удалось создать транслятор для подключения. \"{$name}\" с ошибкой: {$e->getMessage()}.", 0, $e);
        }
    }

    /**
     * Позвоните создателю пользовательского драйвера.
     *
     * @param  array  $config
     * @return mixed
     */
    protected function callCustomCreator(array $config)
    {
        return $this->customCreators[$config['driver']]($this->app, $config);
    }

    /**
     * Создайте экземпляр драйвера.
     *
     * @param  array  $config
     * @return \Boot\Contracts\Broadcasting\Broadcaster
     */
    protected function createReverbDriver(array $config)
    {
        return $this->createPusherDriver($config);
    }

    /**
     * Создайте экземпляр драйвера.
     *
     * @param  array  $config
     * @return \Boot\Contracts\Broadcasting\Broadcaster
     */
    protected function createPusherDriver(array $config)
    {
        return new PusherBroadcaster($this->pusher($config));
    }

    /**
     * Получите экземпляр Pusher для данной конфигурации.
     *
     * @param  array  $config
     * @return \Pusher\Pusher
     */
    public function pusher(array $config)
    {
        $guzzleClient = new GuzzleClient(
            array_merge(
                [
                    'connect_timeout' => 10,
                    'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                    'timeout' => 30,
                ],
                $config['client_options'] ?? [],
            ),
        );

        $pusher = new Pusher(
            $config['key'],
            $config['secret'],
            $config['app_id'],
            $config['options'] ?? [],
            $guzzleClient,
        );

        if ($config['log'] ?? false) {
            $pusher->setLogger($this->app->make(LoggerInterface::class));
        }

        return $pusher;
    }

    /**
     * Создайте экземпляр драйвера.
     *
     * @param  array  $config
     * @return \Boot\Contracts\Broadcasting\Broadcaster
     */
    protected function createRedisDriver(array $config)
    {
        return new RedisBroadcaster(
            $this->app->make('redis'), $config['connection'] ?? null,
            $this->app['config']->get('database.redis.options.prefix', '')
        );
    }

    /**
     * Создайте экземпляр драйвера.
     *
     * @param  array  $config
     * @return \Boot\Contracts\Broadcasting\Broadcaster
     */
    protected function createLogDriver(array $config)
    {
        return new LogBroadcaster(
            $this->app->make(LoggerInterface::class)
        );
    }

    /**
     * Создайте экземпляр драйвера.
     *
     * @param  array  $config
     * @return \Boot\Contracts\Broadcasting\Broadcaster
     */
    protected function createNullDriver(array $config)
    {
        return new NullBroadcaster;
    }

    /**
     * Получите конфигурацию подключения.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        if (! is_null($name) && $name !== 'null') {
            return $this->app['config']["broadcasting.connections.{$name}"];
        }

        return ['driver' => 'null'];
    }

    /**
     * Получите имя драйвера по умолчанию.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['broadcasting.default'];
    }

    /**
     * Установите имя драйвера по умолчанию.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['broadcasting.default'] = $name;
    }

    /**
     * Отключите данный диск и удалите его из локального кеша.
     *
     * @param  string|null  $name
     * @return void
     */
    public function purge($name = null)
    {
        $name ??= $this->getDefaultDriver();

        unset($this->drivers[$name]);
    }

    /**
     * Зарегистрируйте средство создания собственных драйверов Closure.
     *
     * @param  string  $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Выполните заданный обратный вызов, используя «спасение», если это возможно.
     *
     * @param  \Closure  $callback
     * @return mixed
     */
    protected function rescue(Closure $callback)
    {
        if (function_exists('rescue')) {
            return rescue($callback);
        }

        return $callback();
    }

    /**
     * Получите экземпляр приложения, используемый менеджером.
     *
     * @return \Boot\Contracts\App\Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Установите экземпляр приложения, используемый менеджером.
     *
     * @param  \Boot\Contracts\App\Application  $app
     * @return $this
     */
    public function setApplication($app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Забудьте все разрешенные экземпляры драйверов.
     *
     * @return $this
     */
    public function forgetDrivers()
    {
        $this->drivers = [];

        return $this;
    }

    /**
     * Динамический вызов экземпляра драйвера по умолчанию.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}
