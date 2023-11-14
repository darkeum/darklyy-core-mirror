<?php

namespace Boot\System\Livewire\Controllers;

class LivewireJavaScriptAssets
{
    use CanPretendToBeAFile;

    public function source()
    {
        return $this->pretendResponseIsFile(__DIR__.'/../Resources/dist/livewire.js');
    }

    public function maps()
    {
        return $this->pretendResponseIsFile(__DIR__.'/../Resources/dist/livewire.js.map');
    }
}
