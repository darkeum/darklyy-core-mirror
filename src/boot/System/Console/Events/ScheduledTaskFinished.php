<?php

namespace Boot\System\Console\Events;

use Boot\System\Console\Scheduling\Event;

class ScheduledTaskFinished
{
    /**
     * The scheduled event that ran.
     *
     * @var \Boot\System\Scheduling\Event
     */
    public $task;

    /**
     * The runtime of the scheduled event.
     *
     * @var float
     */
    public $runtime;

    /**
     * Create a new event instance.
     *
     * @param  \Boot\System\Scheduling\Event  $task
     * @param  float  $runtime
     * @return void
     */
    public function __construct(Event $task, $runtime)
    {
        $this->task = $task;
        $this->runtime = $runtime;
    }
}
