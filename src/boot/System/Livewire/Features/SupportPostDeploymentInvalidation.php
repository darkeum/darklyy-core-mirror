<?php

namespace Boot\System\Livewire\Features;

use Exception;
use Boot\System\Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Boot\System\Livewire\Exceptions\LivewirePageExpiredBecauseNewDeploymentHasSignificantEnoughChanges;

class SupportPostDeploymentInvalidation
{
    public static $LIVEWIRE_DEPLOYMENT_INVALIDATION_HASH = 'acj';

    static function init() { return new static; }

    function __construct()
    {
        Livewire::listen('component.dehydrate.initial', function ($component, $response) {
            $response->fingerprint['v'] = static::$LIVEWIRE_DEPLOYMENT_INVALIDATION_HASH;
        });

        Livewire::listen('component.hydrate.subsequent', function ($component, $request) {
            if (! isset($request->fingerprint['v'])) return;

            if ($v = $request->fingerprint['v']) {
                if ($v != static::$LIVEWIRE_DEPLOYMENT_INVALIDATION_HASH) {
                    throw new LivewirePageExpiredBecauseNewDeploymentHasSignificantEnoughChanges;
                }
            }
        });
    }
}
