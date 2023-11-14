<?php

namespace Boot\System\Twig;

use Twig\Lexer;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use InvalidArgumentException;
use Twig\Extension\DebugExtension;
use Illuminate\View\FileViewFinder;
use Twig\Extension\EscaperExtension;
use Twig\Extension\ExtensionInterface;
use Boot\Support\ServiceProvider;


class TwigServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerCommands();
        $this->registerOptions();
        $this->registerTwigFinder();
        $this->registerLoaders();
        $this->registerPaths();
        $this->registerEngine();
        $this->registerAliases();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->registerExtension();
    }

    /**
     * Check if we are running Lumen or not.
     *
     * @return bool
     */
    protected function isLumen()
    {
        return strpos($this->app->version(), 'Lumen') !== false;
    }

    /**
     * Check if we are running on PHP 7.
     *
     * @return bool
     */
    protected function isRunningOnPhp7()
    {
        return version_compare(PHP_VERSION, '7.0-dev', '>=');
    }



    /**
     * Register the Twig extension in the Laravel View component.
     *
     * @return void
     */
    protected function registerExtension()
    {
        $this->app['twig.finder']->addExtension(
            $this->app['twig.extension'],
            'twig',
            function () {
                return $this->app['twig.engine'];
            }
        );
    }

    /**
     * Register console command bindings.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->app->bindIf('command.twig', function () {
            return new Command\Twig;
        });

        $this->app->bindIf('command.twig.clean', function () {
            return new Command\Clean;
        });

        $this->app->bindIf('command.twig.check', function () {
            return new Command\Check;
        });

        $this->commands(
            'command.twig',
            'command.twig.clean',
            'command.twig.check'
        );
    }


    protected function registerOptions()
    {
        $this->app->bindIf('twig.extension', function () {
            return $this->app['config']->get('twig.extension');
        });

        $this->app->bindIf('twig.options', function () {
            $options = $this->app['config']->get('twig.environment', []);

            // Check whether we have the cache path set
            if (!isset($options['cache']) || is_null($options['cache'])) {
                // No cache path set for Twig, lets set to the Laravel views storage folder
                $options['cache'] = tmp_path('cache/twig');
            }

            return $options;
        });

        $this->app->bindIf('twig.extensions', function () {
            $load = $this->app['config']->get('twig.extensions.enabled', []);

            // Is debug enabled?
            // If so enable debug extension
            $options = $this->app['twig.options'];
            $isDebug = (bool) (isset($options['debug'])) ? $options['debug'] : false;

            if ($isDebug) {
                array_unshift($load, DebugExtension::class);
            }

            return $load;
        });

        $this->app->bindIf('twig.lexer', function () {
            return null;
        });
    }

    /**
     * Register the view finder implementation.
     *
     * @return void
     */
    public function registerTwigFinder()
    {
        $this->app->bind('twig.finder', function ($app) {
            $finder = new FileViewFinder($app['files'], $app['config']['twig.paths']);
            $finder->addExtension( $this->app['twig.extension']);
            return $finder;
        });
    }

    /**
     * Register Twig loader bindings.
     *
     * @return void
     */
    protected function registerLoaders()
    {
        $this->app->bindIf('twig.templates', function () {
            return [];
        });

        $this->app->bindIf('twig.loader.array', function ($app) {
            return new ArrayLoader($app['twig.templates']);
        });
       
        $this->app->bindIf('twig.loader.finder', function () {           
           
            if (\Boot\Support\Facades\Request::is('administration/*')) {
                $twigPatch['manager'] = ROOT . 'usr/tpl/admin/';
            } else {
                $twigPatch = $this->app['config']['twig.paths'];
            }
            // dd($twigPatch);
            return new Twig\Loader\FilesystemLoader(
                $twigPatch
            );
        });
        $this->registerPaths();
        $this->app->bindIf(
            'twig.loader',
            function () {
                return new ChainLoader([
                    $this->app['twig.loader.array'],
                    $this->app['twig.loader.finder'],
                ]);
            },
            true
        );
    }

    protected function registerPaths()
    {
       
    }

    /**
     * Register Twig engine bindings.
     *
     * @return void
     */
    protected function registerEngine()
    {
        $this->app->bindIf(
            'twig',
            function () {
                $extensions = $this->app['twig.extensions'];
                $lexer = $this->app['twig.lexer'];
                $twig = new TwigManager(
                    $this->app['twig.loader'],
                    $this->app['twig.options'],
                    $this->app
                );

                foreach ($this->app['config']->get('twig.safe_classes', []) as $safeClass => $strategy) {
                    $twig->getExtension(EscaperExtension::class)->addSafeClass($safeClass, $strategy);
                }

                // Instantiate and add extensions
                foreach ($extensions as $extension) {
                    // Get an instance of the extension
                    // Support for string, closure and an object
                    if (is_string($extension)) {
                        try {
                            $extension = $this->app->make($extension);
                        } catch (\Exception $e) {
                            throw new InvalidArgumentException(
                                "Cannot instantiate Twig extension '$extension': " . $e->getMessage()
                            );
                        }
                    } elseif (is_callable($extension)) {
                        $extension = $extension($this->app, $twig);
                    } elseif (!is_a($extension, ExtensionInterface::class)) {
                        throw new InvalidArgumentException('Incorrect extension type');
                    }

                    $twig->addExtension($extension);
                }

                // Set lexer
                if (is_a($lexer, Lexer::class)) {
                    $twig->setLexer($lexer);
                }

                return $twig;
            },
            true
        );

        $this->app->alias('twig', Environment::class);
        $this->app->alias('twig', TwigManager::class);

        $this->app->bindIf('twig.compiler', function () {
            return new Engine\Compiler($this->app['twig']);
        });

        $this->app->bindIf('twig.engine', function () {
            return new Engine\Twig(
                $this->app['twig.compiler'],
                $this->app['twig.loader.viewfinder'],
                $this->app['config']->get('twigbridge.twig.globals', [])
            );
        });
    }

    /**
     * Register aliases for classes that had to be renamed because of reserved names in PHP7.
     *
     * @return void
     */
    protected function registerAliases()
    {
        if (!$this->isRunningOnPhp7() and !class_exists('TwigBridge\Extension\Laravel\String')) {
            class_alias('TwigBridge\Extension\Laravel\Str', 'TwigBridge\Extension\Laravel\String');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.twig',
            'command.twig.clean',
            'command.twig.lint',
            'twig.extension',
            'twig.options',
            'twig.extensions',
            'twig.lexer',
            'twig.templates',
            'twig.loader.array',
            'twig.loader.finder',
            'twig.loader',
            'twig',
            'twig.compiler',
            'twig.engine',
        ];
    }


    // public function register()
    // {
    //     $twigPatch =  ROOT . 'usr/tpl/main/';
    //     $loader = new \Boot\System\Twig\Twig\Loader\FilesystemLoader($twigPatch);
    //     $twig = new \Boot\System\Twig\Twig\Environment($loader, [
    //         'cache' => ROOT . '/tmp/compilation_cache',
    //         'auto_reload' => true,
    //         'debug' => true,
    //     ]);
    //     $options = [
    //         'path' => ROOT . 'tmp/assets',
    //         'path_chmod' => 0755,
    //         'url_base_path' => '/tmp/assets/',
    //         'cache_path' => ROOT . 'tmp/assets',
    //         'cache_name' => 'assets-cache',
    //         'cache_lifetime' => 0,
    //         'minify' => 1
    //     ];

    //     $twig->addExtension(new \Twig\Extension\DebugExtension());
    //     $twig->addExtension(new \Twig\Extra\String\StringExtension());

    //     $twig->addExtension(new \Boot\System\Twig\TruncateExtension());
    //     $twig->addExtension(new \Boot\System\Twig\WidgetExtension($twig));
    //     $twig->addExtension(new \Boot\System\Twig\FrameworkExtension());
    //     $twig->addExtension(new \Boot\System\Twig\ManagerExtension());
    //     // $twig->addExtension(new \Boot\System\Twig\TranslationExtension($translator));
    //     $twig->addExtension(new \Odan\Twig\TwigAssetsExtension($twig, $options));
    // }

    // public function boot()
    // {
    // }
}
