<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Support\Facades;

use Closure;
use Boot\System\Process\Factory;

/**
 * @method static \Boot\System\Process\PendingProcess command(array|string $command)
 * @method static \Boot\System\Process\PendingProcess path(string $path)
 * @method static \Boot\System\Process\PendingProcess timeout(int $timeout)
 * @method static \Boot\System\Process\PendingProcess idleTimeout(int $timeout)
 * @method static \Boot\System\Process\PendingProcess forever()
 * @method static \Boot\System\Process\PendingProcess env(array $environment)
 * @method static \Boot\System\Process\PendingProcess input(\Traversable|resource|string|int|float|bool|null $input)
 * @method static \Boot\System\Process\PendingProcess quietly()
 * @method static \Boot\System\Process\PendingProcess tty(bool $tty = true)
 * @method static \Boot\System\Process\PendingProcess options(array $options)
 * @method static \Illuminate\Contracts\Process\ProcessResult run(array|string|null $command = null, callable|null $output = null)
 * @method static \Boot\System\Process\InvokedProcess start(array|string|null $command = null, callable|null $output = null)
 * @method static \Boot\System\Process\PendingProcess withFakeHandlers(array $fakeHandlers)
 * @method static \Boot\System\Process\PendingProcess|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Boot\System\Process\PendingProcess|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Boot\System\Process\FakeProcessResult result(array|string $output = '', array|string $errorOutput = '', int $exitCode = 0)
 * @method static \Boot\System\Process\FakeProcessDescription describe()
 * @method static \Boot\System\Process\FakeProcessSequence sequence(array $processes = [])
 * @method static bool isRecording()
 * @method static \Boot\System\Process\Factory recordIfRecording(\Boot\System\Process\PendingProcess $process, \Illuminate\Contracts\Process\ProcessResult $result)
 * @method static \Boot\System\Process\Factory record(\Boot\System\Process\PendingProcess $process, \Illuminate\Contracts\Process\ProcessResult $result)
 * @method static \Boot\System\Process\Factory preventStrayProcesses(bool $prevent = true)
 * @method static bool preventingStrayProcesses()
 * @method static \Boot\System\Process\Factory assertRan(\Closure|string $callback)
 * @method static \Boot\System\Process\Factory assertRanTimes(\Closure|string $callback, int $times = 1)
 * @method static \Boot\System\Process\Factory assertNotRan(\Closure|string $callback)
 * @method static \Boot\System\Process\Factory assertDidntRun(\Closure|string $callback)
 * @method static \Boot\System\Process\Factory assertNothingRan()
 * @method static \Boot\System\Process\Pool pool(callable $callback)
 * @method static \Illuminate\Contracts\Process\ProcessResult pipe(callable|array $callback, callable|null $output = null)
 * @method static \Boot\System\Process\ProcessPoolResults concurrently(callable $callback, callable|null $output = null)
 * @method static \Boot\System\Process\PendingProcess newPendingProcess()
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 *
 * @see \Boot\System\Process\PendingProcess
 * @see \Boot\System\Process\Factory
 */
class Process extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }

    /**
     * Indicate that the process factory should fake processes.
     *
     * @param  \Closure|array|null  $callback
     * @return \Boot\System\Process\Factory
     */
    public static function fake(Closure|array $callback = null)
    {
        return tap(static::getFacadeRoot(), function ($fake) use ($callback) {
            static::swap($fake->fake($callback));
        });
    }
}
