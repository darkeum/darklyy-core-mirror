<?php

namespace Boot\System\Cache\RateLimiting;

class GlobalLimit extends Limit
{
    /**
     * Create a new limit
     *
     * @param  int  $maxAttempts
     * @param  int  $decaySeconds
     * @return void
     */
    public function __construct(int $maxAttempts, int $decaySeconds = 60)
    {
        parent::__construct('', $maxAttempts, $decaySeconds);
    }
}
