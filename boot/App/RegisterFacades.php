<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\App;

use \Boot\App\Application;
use \Boot\App\AliasLoader;
use \Boot\App\PackageManifest;
use \Boot\Support\Facades\Facade;
use \Boot\Support\Facades\Config;

class RegisterFacades
{
    /**
     * Запуск
     *
     * @param  \Boot\Contracts\App\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        Facade::clearResolvedInstances();

        Facade::setFacadeApplication($app);

        AliasLoader::getInstance(array_merge(
            Config::get('app.aliases', []),
            $app->make(PackageManifest::class)->aliases()
        ))->register();
    }
}
