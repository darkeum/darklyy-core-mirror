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


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Boot\Support\Facades\File;

trait ConfigsLoaderTrait
{
    private function loadConfigs($configFolder): void
    {
        if (File::isDirectory($configFolder)) {
            $files = File::files($configFolder);

            foreach ($files as $file) {
                try {
                    $config = File::getRequire($file);
                    $name = File::name($file);
                    $path = $configFolder . '/' . $name . '.php';

                    $this->mergeConfigFrom($path, $name);
                } catch (FileNotFoundException $e) {
                }
            }
        }
    }

    public function loadConfigsFromContainers($containerPath): void
    {
        $containerConfigsDirectory = $containerPath . '/Configs';
        $this->loadConfigs($containerConfigsDirectory);
    }

    public function loadConfigsFromApp(): void
    {
        $portConfigsDirectory = base_path('usr/app/Configs');
        $this->loadConfigs($portConfigsDirectory);
    }
    
}
