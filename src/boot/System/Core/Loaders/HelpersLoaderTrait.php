<?php

namespace Boot\System\Core\Loaders;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Boot\Support\Facades\File;

trait HelpersLoaderTrait
{
    public function loadHelpersFromContainers($containerPath): void
    {
        $containerHelpersDirectory = $containerPath . '/Helpers';
        $this->loadHelpers($containerHelpersDirectory);
    }

    private function loadHelpers($helpersFolder): void
    {
        if (File::isDirectory($helpersFolder)) {
            $files = File::files($helpersFolder);

            foreach ($files as $file) {
                try {
                    require($file);
                } catch (FileNotFoundException $e) {
                }
            }
        }
    }

    public function loadHelpersFromApp(): void
    {
        $shipHelpersDirectory = base_path('usr/app/Helpers');
        $this->loadHelpers($shipHelpersDirectory);
    }
}
