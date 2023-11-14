<?php

namespace Boot\System\Scout\Engines\Typesense;

use Boot\System\Scout\Engines\Typesense\Engines\TypesenseEngine;
use Boot\System\Scout\Engines\Typesense\Mixin\BuilderMixin;
use Boot\Support\Facades\Config;
use Boot\Support\ServiceProvider;
use Boot\System\Scout\Builder;
use Boot\System\Scout\EngineManager;
use Typesense\Client;

/**
 * Class TypesenseServiceProvider.
 *
 * @date    4/5/20
 *
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class TypesenseServiceProvider extends ServiceProvider
{
    /**
     * @throws \ReflectionException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot(): void
    {
        
    }

    /**
     * Register singletons and aliases.
     */
    public function register(): void
    {
      
    }

   
}
