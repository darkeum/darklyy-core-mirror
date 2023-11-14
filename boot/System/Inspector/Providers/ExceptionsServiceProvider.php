<?php


namespace Boot\System\Inspector\Providers;


use Illuminate\Log\Events\MessageLogged;
use Boot\Support\ServiceProvider;
use Boot\System\Inspector\Facades\Inspector;

class ExceptionsServiceProvider extends ServiceProvider
{
    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (class_exists(MessageLogged::class)) {
            // starting from L5.4 MessageLogged event class was introduced
            // https://github.com/laravel/framework/commit/57c82d095c356a0fe0f9381536afec768cdcc072
            $this->app['events']->listen(MessageLogged::class, function (MessageLogged $log) {
                $this->handleLog($log->level, $log->message, $log->context);
            });
        } else {
            $this->app['events']->listen('illuminate.log', function ($level, $message, $context) {
                $this->handleLog($level, $message, $context);
            });
        }
    }

    /**
     * Attach the event to the current transaction.
     *
     * @param string $level
     * @param mixed $message
     * @param mixed $context
     * @return mixed
     */
    protected function handleLog($level, $message, $context)
    {
        if (
            isset($context['exception']) &&
            $context['exception'] instanceof \Throwable
        ) {
            return $this->reportException($context['exception']);
        }

        if ($message instanceof \Throwable) {
            return $this->reportException($message);
        }

        // Collect general log messages
        if (Inspector::isRecording() && Inspector::hasTransaction()) {
            Inspector::transaction()
                ->addContext('logs', array_merge(
                    Inspector::transaction()->getContext()['logs'] ?? [],
                    [
                        compact('level', 'message')
                    ]
                ));
        }
    }

    protected function reportException(\Throwable $exception)
    {
        if (!Inspector::isRecording()) {
            return;
        }

        Inspector::reportException($exception, false);
        Inspector::transaction()->setResult('error');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
