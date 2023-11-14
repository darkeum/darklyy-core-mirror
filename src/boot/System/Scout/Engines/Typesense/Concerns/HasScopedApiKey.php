<?php

namespace Boot\System\Scout\Engines\Typesense\Concerns;

use Boot\System\Scout\Engines\Typesense\Typesense;

trait HasScopedApiKey
{
    /**
     * @param $key
     * @return static
     */
    public static function setScopedApiKey($key): static
    {
        app(Typesense::class)->setScopedApiKey($key);

        return new static;
    }
}
