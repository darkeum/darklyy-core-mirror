<?php

namespace Boot\System\Console\Events;

class ArtisanStarting
{
    /**
     * The Artisan application instance.
     *
     * @var \Boot\System\Application
     */
    public $darksander;

    /**
     * Create a new event instance.
     *
     * @param  \Boot\System\Application  $artisan
     * @return void
     */
    public function __construct($darksander)
    {
        $this->darksander = $darksander;
    }
}
