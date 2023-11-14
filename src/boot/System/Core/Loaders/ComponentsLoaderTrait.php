<?php

namespace Boot\System\Core\Loaders;

use Boot\Support\Str;
use Boot\Support\Facades\File;
use Boot\System\Filesystem\Filesystem;
use Boot\System\Livewire\Commands\ComponentParser;
use Boot\System\Livewire\LivewireComponentsFinder;

trait ComponentsLoaderTrait
{
    public function loadComponentsFromContainers($containerPath): void
    {

        $containerViewDirectory = $containerPath . '/UI/WEB/Components/';
        $containerName = basename($containerPath);
        $pathParts = explode(DIRECTORY_SEPARATOR, $containerPath);
        $sectionName = $pathParts[count($pathParts) - 2];
        $this->loadComponents($containerViewDirectory, $containerName, $sectionName);
    }

    private function loadComponents($directory, $containerName, $sectionName = null): void
    {
        $this->app->singleton(LivewireComponentsFinder::class, function () use ($directory, $containerName) {
            return new LivewireComponentsFinder(
                new Filesystem,
                config('livewire.manifest_path'),
                $directory,
                $containerName
            );
        });
    }

    // private function buildViewNamespace(?string $sectionName, string $containerName): string
    // {
    //     return Str::camel($containerName);
    // }

    // public function loadViewsFromShip(): void
    // {
    //     $portMailTemplatesDirectory = base_path('app/Ship/Mails/Templates/');
    //     $this->loadViews($portMailTemplatesDirectory, 'ship'); // Ship views accessible via `ship::`.
    // }
}
