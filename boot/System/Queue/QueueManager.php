<?php

namespace Boot\System\Queue;
use Illuminate\Queue\QueueManager as LaravelQueueManager;

class QueueManager extends LaravelQueueManager
{

    /**
     * Get the name of the default queue connection.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']->get('queue.default');
    }

    protected function getConfig($name)
    {
        if (! is_null($name) && $name !== 'null') {
            return $this->app['config']->get("queue.connections")[$name];
        }

        return ['driver' => 'null'];
    }

}