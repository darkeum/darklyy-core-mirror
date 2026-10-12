<?php

namespace Boot\ServiceProviders;

use Boot\App\MaintenanceModeManager;
use Boot\Models\Categories;
use Boot\Support\AggregateServiceProvider;
use Boot\Support\Facades\Cache;
use Boot\System\Http\Request;
use Boot\System\Testing\LoggedExceptionCollection;
use Boot\System\Testing\ParallelTestingServiceProvider;
use Illuminate\Contracts\Foundation\MaintenanceMode as MaintenanceModeContract;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use View;

class AppServiceProvider extends AggregateServiceProvider
{
    /**
     * The provider class names.
     *
     * @var string[]
     */
    protected $providers = [
        FormRequestServiceProvider::class,
        ParallelTestingServiceProvider::class,
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../Exceptions/views' => $this->app->resourcePath('views/errors/'),
            ], 'laravel-errors');
        }

        // Отправляем данные во все шаблоны
        View::composer('*', function ($view) {

            $cat_full = Cache::remember('category.catalog', 86400, function () {
                return Categories::where('module', 'catalog_v2')->where('hidden', 0)->orderBy('position', 'ASC')->get();
            });
            $menu = $cat_full->whereIn('id', config('menu.ids'));

            // ТУТ можно передать переменные во все виды:
            $view->with([
                'cat_full' => $cat_full,
                'menu' => $menu
            ]);

        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerRequestValidation();
        $this->registerRequestSignatureValidation();
        $this->registerExceptionTracking();
        $this->registerMaintenanceModeManager();
    }

    /**
     * Register the "validate" macro on the request.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function registerRequestValidation()
    {
        Request::macro('validate', function (array $rules, ...$params) {
            $rules = $this->isPrecognitive()
                ? $this->filterPrecognitiveRules($rules)
                : $rules;

            return tap(validator($this->all(), $rules, ...$params), function ($validator) {
                if ($this->isPrecognitive()) {
                    $validator->after(Precognition::afterValidationHook($this));
                }
            })->validate();
        });

        Request::macro('validateWithBag', function (string $errorBag, array $rules, ...$params) {
            try {
                return $this->validate($rules, ...$params);
            } catch (ValidationException $e) {
                $e->errorBag = $errorBag;

                throw $e;
            }
        });
    }

    /**
     * Register the "hasValidSignature" macro on the request.
     *
     * @return void
     */
    public function registerRequestSignatureValidation()
    {
        Request::macro('hasValidSignature', function ($absolute = true) {
            return URL::hasValidSignature($this, $absolute);
        });

        Request::macro('hasValidRelativeSignature', function () {
            return URL::hasValidSignature($this, $absolute = false);
        });

        Request::macro('hasValidSignatureWhileIgnoring', function ($ignoreQuery = [], $absolute = true) {
            return URL::hasValidSignature($this, $absolute, $ignoreQuery);
        });
    }

    /**
     * Register an event listener to track logged exceptions.
     *
     * @return void
     */
    protected function registerExceptionTracking()
    {
        if (! $this->app->runningUnitTests()) {
            return;
        }

        $this->app->instance(
            LoggedExceptionCollection::class,
            new LoggedExceptionCollection
        );

        $this->app->make('events')->listen(MessageLogged::class, function ($event) {
            if (isset($event->context['exception'])) {
                $this->app->make(LoggedExceptionCollection::class)
                        ->push($event->context['exception']);
            }
        });
    }

    /**
     * Register the maintenance mode manager service.
     *
     * @return void
     */
    public function registerMaintenanceModeManager()
    {
        $this->app->singleton(MaintenanceModeManager::class);

        $this->app->bind(
            MaintenanceModeContract::class,
            fn () => $this->app->make(MaintenanceModeManager::class)->driver()
        );
    }
}
