<?php

namespace Boot\System\Core\Loaders;

use Boot\Support\Facades\File;
use Boot\Support\Str;

trait ViewsLoaderTrait
{
    public function loadViewsFromContainers($containerPath): void
    {

        $containerViewDirectory = $containerPath . '/UI/WEB/Views/';
        $containerViewDirectoryResources = $containerPath . '/UI/WEB/Resources/Views/';
        $containerMailTemplatesDirectory = $containerPath . '/Mails/Templates/';

        $containerName = basename($containerPath);
        $pathParts = explode(DIRECTORY_SEPARATOR, $containerPath);
        $sectionName = $pathParts[count($pathParts) - 2];
// dd($containerViewDirectory, $containerName, $sectionName);
        $this->loadViews($containerViewDirectory, $containerName, $sectionName);
        $this->loadViews($containerViewDirectoryResources, $containerName, $sectionName);
        $this->loadViews($containerMailTemplatesDirectory, $containerName, $sectionName);
    }

    private function loadViews($directory, $containerName, $sectionName = null): void
    {
        if (File::isDirectory($directory)) {
            $this->loadViewsFrom($directory, $this->buildViewNamespace($sectionName, $containerName));
        }
    }

    private function buildViewNamespace(?string $sectionName, string $containerName): string
    {
        return Str::camel($containerName);
    }

    public function loadViewsFromShip(): void
    {
        $portMailTemplatesDirectory = base_path('app/Ship/Mails/Templates/');
        $this->loadViews($portMailTemplatesDirectory, 'ship'); // Ship views accessible via `ship::`.
    }
}
