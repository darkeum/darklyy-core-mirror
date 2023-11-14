<?php

namespace Boot\System\Dashboard;

use Illuminate\Support\Facades\Blade;
use Boot\Support\ServiceProvider;
use Boot\System\Livewire\Livewire;
use Boot\System\Dashboard\Http\Components\DashboardComponent;
use Boot\System\Dashboard\Http\Components\DashboardTileComponent;
use Boot\System\Dashboard\Http\Components\UpdateModeComponent;
use Spatie\Sun\Sun;

class DashboardServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $dashboard = $this->app->make(Dashboard::class);

        if ($alpine = config('dashboard.scripts.alpinejs')) {
            $dashboard->script($alpine);
        }

        if ($inter = config('dashboard.stylesheets.inter')) {
            $dashboard->stylesheet($inter);
        }

        $dashboard->inlineStylesheet(file_get_contents(__DIR__.'/Resources/dist/dashboard.min.css'));

        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'dashboard');

        $this
            ->registerBladeComponents()
            ->registerLivewireComponents();
    }

    public function register()
    {
              $this->app->singleton(Dashboard::class);
        $this->app->alias(Dashboard::class, 'dashboard');

        $this->app->singleton(Sun::class, function () {
            return new Sun(
                config('dashboard.auto_theme_location.lat'),
                config('dashboard.auto_theme_location.lng')
            );
        });

        $this->app->when(DashboardComponent::class)
            ->needs('$defaultTheme')
            ->give(config('dashboard.theme'));
    }


    protected function registerBladeComponents(): self
    {
        Blade::component('dashboard', DashboardComponent::class);
        Blade::component('dashboard-tile', DashboardTileComponent::class);

        return $this;
    }

    protected function registerLivewireComponents(): self
    {
        Livewire::component('dashboard-update-mode', UpdateModeComponent::class);

        return $this;
    }
}
