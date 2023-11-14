<?php

namespace Boot\System\Livewire;

interface Castable
{
    public function cast($value);

    public function uncast($value);
}
