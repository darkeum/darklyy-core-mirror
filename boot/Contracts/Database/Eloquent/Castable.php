<?php

namespace Boot\Contracts\Database\Eloquent;

interface Castable
{
    /**
     * Get the name of the caster class to use when casting from / to this cast target.
     *
     * @return string|\Boot\Contracts\Database\Eloquent\CastsAttributes|\Boot\Contracts\Database\Eloquent\CastsInboundAttributes
     */
    public static function castUsing();
}
