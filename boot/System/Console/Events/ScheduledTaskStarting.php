<?php

namespace Boot\System\Console\Events;

use Boot\System\Console\Scheduling\Event;

class ScheduledTaskStarting
{
    /**
     * The scheduled event being run.
     *
     * @var \Boot\System\Scheduling\Event
     */
    public $task;

    /**
     * Create a new event instance.
     *
     * @param  \Boot\System\Scheduling\Event  $task
     * @return void
     */
    public function __construct(Event $task)
    {
        $this->task = $task;
    }
}
