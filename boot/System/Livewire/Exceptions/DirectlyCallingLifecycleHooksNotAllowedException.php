<?php

namespace Boot\System\Livewire\Exceptions;

class DirectlyCallingLifecycleHooksNotAllowedException extends \Exception
{
    use BypassViewHandler;

    public function __construct($method, $component)
    {
        parent::__construct(
            "Unable to call lifecycle method [{$method}] directly on component: [{$component}]"
        );
    }
}
