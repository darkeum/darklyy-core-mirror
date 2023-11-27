<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Permission;

use Illuminate\Support\Arr;
use Illuminate\Routing\Route;
use Boot\Support\ServiceProvider;
use Boot\Contracts\App\Application;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\View\Compilers\BladeCompiler;
use Boot\System\Permission\Contracts\Role as RoleContract;
use Boot\System\Permission\Contracts\Permission as PermissionContract;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerMacroHelpers();

        $this->registerCommands();

        $this->registerModelBindings();

        $this->registerOctaneListener();

        $this->callAfterResolving(Gate::class, function (Gate $gate, Application $app) {
            if ($this->app['config']->get('permission.register_permission_check_method')) {
                /** @var PermissionRegistrar $permissionLoader */
                $permissionLoader = $app->get(PermissionRegistrar::class);
                $permissionLoader->clearPermissionsCollection();
                $permissionLoader->registerPermissions($gate);
            }
        });

        $this->app->singleton(PermissionRegistrar::class);
    }

    public function register()
    {
        $this->callAfterResolving('blade.compiler', fn (BladeCompiler $bladeCompiler) => $this->registerBladeExtensions($bladeCompiler));
    }


    protected function registerCommands()
    {
        $this->commands([
            Commands\CacheReset::class,
        ]);

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Commands\CreateRole::class,
            Commands\CreatePermission::class,
            Commands\Show::class
        ]);
    }

    protected function registerOctaneListener(): void
    {
        if ($this->app->runningInConsole() || ! $this->app['config']->get('octane.listeners')) {
            return;
        }

        $dispatcher = $this->app[Dispatcher::class];
        // @phpstan-ignore-next-line
        $dispatcher->listen(function (\Laravel\Octane\Events\OperationTerminated $event) {
            // @phpstan-ignore-next-line
            $event->sandbox->make(PermissionRegistrar::class)->setPermissionsTeamId(null);
        });

        if (! $this->app['config']->get('permission.register_octane_reset_listener')) {
            return;
        }
        // @phpstan-ignore-next-line
        $dispatcher->listen(function (\Laravel\Octane\Events\OperationTerminated $event) {
            // @phpstan-ignore-next-line
            $event->sandbox->make(PermissionRegistrar::class)->clearPermissionsCollection();
        });
    }

    protected function registerModelBindings(): void
    {
        $this->app->bind(PermissionContract::class, fn ($app) => $app->make($app->config['permission.models.permission']));
        $this->app->bind(RoleContract::class, fn ($app) => $app->make($app->config['permission.models.role']));
    }

    public static function bladeMethodWrapper($method, $role, $guard = null): bool
    {
        return auth($guard)->check() && auth($guard)->user()->{$method}($role);
    }

    protected function registerBladeExtensions($bladeCompiler): void
    {
        $bladeMethodWrapper = '\\Boot\\System\\Permission\\PermissionServiceProvider::bladeMethodWrapper';

        $bladeCompiler->directive('role', fn ($args) => "<?php if({$bladeMethodWrapper}('hasRole', {$args})): ?>");
        $bladeCompiler->directive('elserole', fn ($args) => "<?php elseif({$bladeMethodWrapper}('hasRole', {$args})): ?>");
        $bladeCompiler->directive('endrole', fn () => '<?php endif; ?>');

        $bladeCompiler->directive('haspermission', fn ($args) => "<?php if({$bladeMethodWrapper}('checkPermissionTo', {$args})): ?>");
        $bladeCompiler->directive('elsehaspermission', fn ($args) => "<?php elseif({$bladeMethodWrapper}('checkPermissionTo', {$args})): ?>");
        $bladeCompiler->directive('endhaspermission', fn () => '<?php endif; ?>');

        $bladeCompiler->directive('hasrole', fn ($args) => "<?php if({$bladeMethodWrapper}('hasRole', {$args})): ?>");
        $bladeCompiler->directive('endhasrole', fn () => '<?php endif; ?>');

        $bladeCompiler->directive('hasanyrole', fn ($args) => "<?php if({$bladeMethodWrapper}('hasAnyRole', {$args})): ?>");
        $bladeCompiler->directive('endhasanyrole', fn () => '<?php endif; ?>');

        $bladeCompiler->directive('hasallroles', fn ($args) => "<?php if({$bladeMethodWrapper}('hasAllRoles', {$args})): ?>");
        $bladeCompiler->directive('endhasallroles', fn () => '<?php endif; ?>');

        $bladeCompiler->directive('unlessrole', fn ($args) => "<?php if(! {$bladeMethodWrapper}('hasRole', {$args})): ?>");
        $bladeCompiler->directive('endunlessrole', fn () => '<?php endif; ?>');

        $bladeCompiler->directive('hasexactroles', fn ($args) => "<?php if({$bladeMethodWrapper}('hasExactRoles', {$args})): ?>");
        $bladeCompiler->directive('endhasexactroles', fn () => '<?php endif; ?>');
    }

    protected function registerMacroHelpers()
    {
        if (! method_exists(Route::class, 'macro')) { // Lumen
            return;
        }

        Route::macro('role', function ($roles = []) {
            /** @var Route $this */
            return $this->middleware('role:'.implode('|', Arr::wrap($roles)));
        });

        Route::macro('permission', function ($permissions = []) {
            /** @var Route $this */
            return $this->middleware('permission:'.implode('|', Arr::wrap($permissions)));
        });
    }

}
