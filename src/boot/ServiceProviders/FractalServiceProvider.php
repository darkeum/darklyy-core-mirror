<?php

namespace Boot\ServiceProviders;

use Illuminate\Support\Collection;
use Darkeum\DarklyyPackageTools\Package;
use Darkeum\DarklyyPackageTools\PackageServiceProvider;
use Darkeum\DarklyyPackageTools\Exceptions\InvalidPackage;
use Spatie\Fractal\Console\Commands\TransformerMakeCommand;

class FractalServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-fractal')
            ->hasConfigFile()
            ->hasCommand(TransformerMakeCommand::class);
    }

    public function packageBooted()
    {
        Collection::macro('transformWith', function ($transformer) {
            return fractal($this, $transformer);
        });
    }

    public function packageRegistered()
    {
        $this->app->singleton('fractal', function ($app, $arguments) {
            return fractal(...$arguments);
        });

        $this->app->alias('fractal', Fractal::class);
    }

    public function register()
    {
        $this->registeringPackage();

        $this->package = new Package();

        $this->package->setBasePath($this->getPackageBaseDir());

        $this->configurePackage($this->package);

        if (empty($this->package->name)) {
            throw InvalidPackage::nameIsRequired();
        }

        // foreach ($this->package->configFileNames as $configFileName) {
        //     $this->mergeConfigFrom($this->package->basePath("/../config/{$configFileName}.php"), $configFileName);
        // }

        $this->packageRegistered();

        return $this;
    }
}
