<?php

namespace Boot\System\Core\Abstracts\Events\Jobs;

use Boot\System\Core\Abstracts\Events\Interfaces\ShouldHandle;
use Boot\System\Core\Abstracts\Jobs\Job;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventJob extends Job implements ShouldQueue
{
    public ShouldHandle $handler;

    public function __construct(ShouldHandle $handler)
    {
        $this->handler = $handler;
    }

    public function handle()
    {
        $this->handler->handle();
    }
}
