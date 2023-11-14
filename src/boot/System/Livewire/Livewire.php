<?php

namespace Boot\System\Livewire;

use Boot\Support\Facades\Facade;

/**
 * @method static void component($alias, $viewClass)
 * @method static \Boot\System\Livewire\Testing\TestableLivewire test($name, $params = [])
 * @method static \Boot\System\Livewire\LivewireManager actingAs($user, $driver = null)
 * @method static \Boot\System\Livewire\LivewireManager withQueryParams($queryParams)
 *
 * @see \Boot\System\Livewire\LivewireManager
 */
class Livewire extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'livewire';
    }
}
