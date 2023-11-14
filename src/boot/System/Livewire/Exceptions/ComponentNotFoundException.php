<?php

namespace Boot\System\Livewire\Exceptions;

class ComponentNotFoundException extends \Exception
{
    use BypassViewHandler;
}
