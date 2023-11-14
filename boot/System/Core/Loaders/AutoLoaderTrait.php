<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Core\Loaders;

use Boot\System\Core\Foundation\Facades\Core;

trait AutoLoaderTrait
{
    // Подключение трейтов загрузки компонентов
    use ConfigsLoaderTrait;
    use LocalizationLoaderTrait;
    use MigrationsLoaderTrait;
    use ViewsLoaderTrait;
    use ProvidersLoaderTrait;
    use ConsolesLoaderTrait;
    use AliasesLoaderTrait;
    use HelpersLoaderTrait;

        
    /**
     * Используется в загрузке основного поставщика услуг.
     *
     * @return void
     */
    public function runLoadersBoot(): void
    {
        $this->loadMigrationsFromShip();
        $this->loadLocalsFromShip();
        $this->loadViewsFromShip();
        $this->loadConsolesFromShip();
        $this->loadHelpersFromApp();

        // Перебрать все папки модулей и автоматически загрузить большинство компонентов.
        foreach (Core::getAllContainerPaths() as $containerPath) {
            $this->loadMigrationsFromContainers($containerPath);
            $this->loadLocalsFromContainers($containerPath);
            $this->loadViewsFromContainers($containerPath);
            $this->loadConsolesFromContainers($containerPath);
            $this->loadHelpersFromContainers($containerPath);
        }
    }
    
    /**
     * Используется при регистрации основного поставщика услуг.
     *
     * @return void
     */
    public function runLoaderRegister(): void
    {
        $this->loadConfigsFromApp();
        $this->loadOnlyShipProviderFromShip();

        foreach (Core::getAllContainerPaths() as $containerPath) {
            $this->loadConfigsFromContainers($containerPath);
            $this->loadOnlyMainProvidersFromContainers($containerPath);
        }
    }
}