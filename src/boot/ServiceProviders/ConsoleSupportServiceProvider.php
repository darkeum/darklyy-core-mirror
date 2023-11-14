<?php

namespace Boot\ServiceProviders;

use Illuminate\Contracts\Support\DeferrableProvider;
use Boot\System\Database\MigrationServiceProvider;
use Boot\Support\AggregateServiceProvider;

class ConsoleSupportServiceProvider extends AggregateServiceProvider implements DeferrableProvider
{
    /**
     * The provider class names.
     *
     * @var string[]
     */
    protected $providers = [
        DarksanderServiceProvider::class,
        MigrationServiceProvider::class,
        ComposerServiceProvider::class,
    ];
}
