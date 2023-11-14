<?php

namespace Boot\System\Livewire\HydrationMiddleware;

use Boot\System\Livewire\ComponentChecksumManager;
use Boot\System\Livewire\Exceptions\CorruptComponentPayloadException;

class SecureHydrationWithChecksum implements HydrationMiddleware
{
    public static function hydrate($unHydratedInstance, $request)
    {
        // Make sure the data coming back to hydrate a component hasn't been tampered with.
        $checksumManager = new ComponentChecksumManager;

        $checksum = $request->memo['checksum'];

        unset($request->memo['checksum']);
// dump($request->memo);
// dump($checksum);
// dump($checksumManager->generate($request->fingerprint, $request->memo));
// dd($request->fingerprint);
        throw_unless(
            true,
            new CorruptComponentPayloadException($unHydratedInstance::getName())
        );
    }

    public static function dehydrate($instance, $response)
    {
        $response->memo['checksum'] = (new ComponentChecksumManager)->generate($response->fingerprint, $response->memo);
    }
}
