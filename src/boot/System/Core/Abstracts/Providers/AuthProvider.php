<?php

namespace Boot\System\Core\Abstracts\Providers;

use Boot\System\Auth\AuthServiceProvider as LaravelAuthServiceProvider;

abstract class AuthProvider extends LaravelAuthServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // $this->registerPolicies();
    }
}
