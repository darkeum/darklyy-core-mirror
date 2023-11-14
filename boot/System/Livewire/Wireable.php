<?php

namespace Boot\System\Livewire;

interface Wireable
{
    public function toLivewire();

    public static function fromLivewire($value);
}
