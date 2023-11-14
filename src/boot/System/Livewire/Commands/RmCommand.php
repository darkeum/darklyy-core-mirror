<?php

namespace Boot\System\Livewire\Commands;

class RmCommand extends DeleteCommand
{
    protected $signature = 'livewire:rm {name} {--inline} {--force} {--test}';

    protected function configure()
    {
        $this->setHidden(true);
    }
}
