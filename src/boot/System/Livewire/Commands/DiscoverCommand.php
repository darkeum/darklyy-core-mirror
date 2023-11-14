<?php

namespace Boot\System\Livewire\Commands;

use Illuminate\Console\Command;
use Boot\System\Livewire\LivewireComponentsFinder;

class DiscoverCommand extends Command
{
    protected $signature = 'livewire:discover';

    protected $description = 'Regenerate Livewire component auto-discovery manifest';

    public function handle()
    {
        app(LivewireComponentsFinder::class)->build();

        $this->info('Livewire auto-discovery manifest rebuilt!');
    }
}
