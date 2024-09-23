<?php

namespace Boot\System\Queue;

use Boot\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Throwable;
use Illuminate\Queue\Worker as LaravelWorker;
use Illuminate\Queue\WorkerOptions;
use Boot\System\Queue\QueueManager;

class Worker extends LaravelWorker
{
    public function __construct(
        QueueManager $manager,
        Dispatcher $events,
        ExceptionHandler $exceptions,
        callable $isDownForMaintenance,
        callable $resetScope = null
    ) {
        $this->events = $events;
        $this->manager = $manager;
        $this->exceptions = $exceptions;
        $this->isDownForMaintenance = $isDownForMaintenance;
        $this->resetScope = $resetScope;
    }

     /**
     * Process the given job.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  string  $connectionName
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    protected function runJob($job, $connectionName, WorkerOptions $options)
    {
        try {
            return $this->process($connectionName, $job, $options);
        } catch (Throwable $e) {
            $this->exceptions->report($e);

            $this->stopWorkerIfLostConnection($e);
        }
    }
}
