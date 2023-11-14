<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Abstracts\Providers;

use Boot\System\Core\Abstracts\Providers\BroadcastsProvider as AbstractBroadcastsProvider;
use Illuminate\Support\Facades\Broadcast;
// use function app_path;

class BroadcastsProvider extends AbstractBroadcastsProvider
{
    public function boot()
    {
        Broadcast::routes();

        // require app_path('App/Broadcasts/Routes.php');
    }
}
