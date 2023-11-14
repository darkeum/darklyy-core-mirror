<?php

namespace Boot\ServiceProviders;

use Illuminate\Contracts\Support\DeferrableProvider;
use Boot\ServiceProviders\ViewServiceProvider;
use Illuminate\Translation\Translator;
use Illuminate\Translation\FileLoader;

class TranslationServiceProvider extends ViewServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLoader();

        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = 'ru';

            $trans = new Translator($loader, $locale);

            $trans->setFallback('en');

            return $trans;
        });
    }

    /**
     * Register the translation line loader.
     *
     * @return void
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new FileLoader($app['files'], $app['path.lang']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['translator', 'translation.loader'];
    }
}
