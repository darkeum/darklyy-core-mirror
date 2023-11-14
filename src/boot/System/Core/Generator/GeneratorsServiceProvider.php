<?php

namespace Boot\System\Core\Generator;

use Boot\System\Core\Generator\Commands\ActionGenerator;
use Boot\System\Core\Generator\Commands\ConfigurationGenerator;
use Boot\System\Core\Generator\Commands\ModuleApiGenerator;
use Boot\System\Core\Generator\Commands\ModuleGenerator;
use Boot\System\Core\Generator\Commands\ModuleWebGenerator;
use Boot\System\Core\Generator\Commands\ControllerGenerator;
use Boot\System\Core\Generator\Commands\EventGenerator;
use Boot\System\Core\Generator\Commands\EventHandlerGenerator;
use Boot\System\Core\Generator\Commands\ExceptionGenerator;
use Boot\System\Core\Generator\Commands\JobGenerator;
use Boot\System\Core\Generator\Commands\MailGenerator;
use Boot\System\Core\Generator\Commands\MigrationGenerator;
use Boot\System\Core\Generator\Commands\ModelGenerator;
use Boot\System\Core\Generator\Commands\NotificationGenerator;
use Boot\System\Core\Generator\Commands\ReadmeGenerator;
use Boot\System\Core\Generator\Commands\RepositoryGenerator;
use Boot\System\Core\Generator\Commands\RequestGenerator;
use Boot\System\Core\Generator\Commands\RouteGenerator;
use Boot\System\Core\Generator\Commands\SeederGenerator;
use Boot\System\Core\Generator\Commands\ServiceProviderGenerator;
use Boot\System\Core\Generator\Commands\SubActionGenerator;
use Boot\System\Core\Generator\Commands\TaskGenerator;
use Boot\System\Core\Generator\Commands\TestFunctionalTestGenerator;
use Boot\System\Core\Generator\Commands\TestTestCaseGenerator;
use Boot\System\Core\Generator\Commands\TestUnitTestGenerator;
use Boot\System\Core\Generator\Commands\TransformerGenerator;
use Boot\System\Core\Generator\Commands\ValueGenerator;
use Boot\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // all generators ordered by name
        $this->registerGenerators([
            ActionGenerator::class,
            ConfigurationGenerator::class,
            ModuleGenerator::class,
            ModuleApiGenerator::class,
            ModuleWebGenerator::class,
            ControllerGenerator::class,
            EventGenerator::class,
            EventHandlerGenerator::class,
            ExceptionGenerator::class,
            JobGenerator::class,
            MailGenerator::class,
            MigrationGenerator::class,
            ModelGenerator::class,
            NotificationGenerator::class,
            ReadmeGenerator::class,
            RepositoryGenerator::class,
            RequestGenerator::class,
            RouteGenerator::class,
            SeederGenerator::class,
            ServiceProviderGenerator::class,
            SubActionGenerator::class,
            TestFunctionalTestGenerator::class,
            TestTestCaseGenerator::class,
            TestUnitTestGenerator::class,
            TaskGenerator::class,
            TransformerGenerator::class,
            ValueGenerator::class,
        ]);
    }

    /**
     * Register the generators.
     * @param array $classes
     */
    private function registerGenerators(array $classes): void
    {
        foreach ($classes as $class) {
            $lowerClass = strtolower($class);

            $this->app->singleton("command.porto.$lowerClass", function ($app) use ($class) {
                return $app[$class];
            });

            $this->commands("command.porto.$lowerClass");
        }
    }
}
