<?php
namespace Boot\App\Loaders;

use Boot\Contracts\Config\Repository as RepositoryContract;
use Boot\System\Config\Repository;
use Closure;
use \Boot\Contracts\App\Application;
use \Exception;
use \SplFileInfo;
use \Symfony\Component\Finder\Finder;

class LoadConfiguration
{

    /**
     * The closure that resolves the permanent, static configuration if applicable.
     *
     * @var (Closure(Application): array<array-key, mixed>)|null
     */
    protected static ?Closure $alwaysUseConfig = null;

    /**
     * Bootstrap the given application.
     *
     * @param  \Boot\Contracts\App\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $items = [];

        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.

        if (static::$alwaysUseConfig !== null) {
            $items = $app->call(self::$alwaysUseConfig);

            $loadedFromCache = true;
        } elseif (is_file($cached = $app->getCachedConfigPath())) {
            $items = require $cached;

            $loadedFromCache = true;
        }

        // Next we will spin through all of the configuration files in the configuration
        // directory and load each one into the repository. This will make all of the
        // options available to the developer for use in various parts of this app.
        $app->instance('config', $config = new Repository($items));

        if (! isset($loadedFromCache)) {
            $this->loadConfigurationFiles($app, $config);
        }

        // Finally, we will set the application's environment based on the configuration
        // values that were loaded. We will pass a callback which will be used to get
        // the environment in a web context where an "--env" switch is not present.
        $app->detectEnvironment(function () use ($config) {
            return $config->get('app.env', 'production');
        });

        date_default_timezone_set($config->get('app.timezone', 'UTC'));

        mb_internal_encoding('UTF-8');
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Config\Repository  $repository
     * @return void
     *
     * @throws \Exception
     */
    protected function loadConfigurationFiles(Application $app, RepositoryContract $repository)
    {
        $files = $this->getConfigurationFiles($app);

        if (! isset($files['app'])) {
            throw new Exception('Unable to load the "app" configuration file.');
        }

        foreach ($files as $key => $path) {
            $repository->set($key, require $path);
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return array
     */
    protected function getConfigurationFiles(Application $app)
    {
        $files = [];

        $configPath = realpath($app->configPath());

        foreach (Finder::create()->files()->name('*.config.php')->in($configPath) as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory . basename($file->getRealPath(), '.config.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param  \SplFileInfo  $file
     * @param  string  $configPath
     * @return string
     */
    protected function getNestedDirectory(SplFileInfo $file, $configPath)
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested) . '.';
        }

        return $nested;
    }

    /**
     * Set a callback to return the permanent, static configuration values.
     *
     * @param  (Closure(Application): array<array-key, mixed>)|null  $alwaysUseConfig
     * @return void
     */
    public static function alwaysUse(?Closure $alwaysUseConfig): void
    {
        static::$alwaysUseConfig = $alwaysUseConfig;
    }
}
