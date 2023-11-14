<?php

namespace Boot\System\Livewire\Commands;

class MakeLivewireCommand extends MakeCommand
{
    protected $signature = 'make:livewire {name} {--force} {--inline} {--test} {--stub=}';
}
