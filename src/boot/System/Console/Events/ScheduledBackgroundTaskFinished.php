<?php

namespace Boot\System\Console\Events;

use Boot\System\Console\Scheduling\Event;

class ScheduledBackgroundTaskFinished
{
    /**
     * The scheduled event that ran.
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
