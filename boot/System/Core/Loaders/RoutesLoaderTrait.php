<?php

namespace Boot\System\Core\Loaders;

use Illuminate\Support\Arr;
use Boot\Support\Facades\File;
use Boot\Support\Facades\Route;
use Boot\Support\Facades\Config;
use Symfony\Component\Finder\SplFileInfo;
use Boot\System\Core\Foundation\Facades\Core;

trait RoutesLoaderTrait
{
    /**
     * Register all the containers routes files in the framework
     */
    public function runRoutesAutoLoader(): void
    {
        $containersPaths = Core::getAllContainerPaths();

        foreach ($containersPaths as $containerPath) {
            $this->loadApiContainerRoutes($containerPath);
            $this->loadWebContainerRoutes($containerPath);
        }



    }

    /**
     * Register the Containers API routes files
     * @param string $containerPath
     */
    private function loadApiContainerRoutes(string $containerPath): void
    {
        // Build the container api routes path
        $apiRoutesPath = $containerPath . '/UI/API/Routes';
        // Build the namespace from the path
        $controllerNamespace = $containerPath . '\\UI\API\Controllers';

        if (File::isDirectory($apiRoutesPath)) {
            $files = File::allFiles($apiRoutesPath);
            $files = Arr::sort($files, function ($file) {
                return $file->getFilename();
            });
            foreach ($files as $file) {
                $this->loadApiRoute($file, $controllerNamespace);
            }
        }
    }

    /**
     * @param $file
     * @param $controllerNamespace
     */
    private function loadApiRoute($file, $controllerNamespace): void
    {
        $routeGroupArray = $this->getRouteGroup($file, $controllerNamespace);
// dump($routeGroupArray);
        Route::group($routeGroupArray, function ($router) use ($file) {
            require $file->getPathname();
        });
    }

    /**
     * @param      $endpointFileOrPrefixString
     * @param null $controllerNamespace
     *
     * @return  array
     */
    public function getRouteGroup($endpointFileOrPrefixString, $controllerNamespace = null): array
    {
        return [
            'namespace' => $controllerNamespace,
            'middleware' => $this->getMiddlewares(),
            'domain' => $this->getApiUrl(),
            // If $endpointFileOrPrefixString is a file then get the version name from the file name, else if string use that string as prefix
            'prefix' => is_string($endpointFileOrPrefixString) ? $endpointFileOrPrefixString : $this->getApiVersionPrefix($endpointFileOrPrefixString),
        ];
    }

    /**
     * @return  array
     */
    private function getMiddlewares(): array
    {
        return array_filter([
            'api',
            $this->getRateLimitMiddleware(), // Returns NULL if feature disabled. Null will be removed form the array.
        ]);
    }

    /**
     * @return  null|string
     */
    private function getRateLimitMiddleware(): ?string
    {
        $rateLimitMiddleware = null;

        if (Config::get('core.api.throttle.enabled')) {
            $rateLimitMiddleware = 'throttle:' . Config::get('core.api.throttle.attempts') . ',' . Config::get('core.api.throttle.expires');
        }

        return $rateLimitMiddleware;
    }

    /**
     * @return  mixed
     */
    private function getApiUrl()
    {
        return Config::get('core.api.url');
    }

    /**
     * @param $file
     *
     * @return  string
     */
    private function getApiVersionPrefix($file): string
    {
        return Config::get('core.api.prefix') . (Config::get('core.api.enable_version_prefix') ? $this->getRouteFileVersionFromFileName($file) : '');
    }

    /**
     * @param $file
     *
     * @return  mixed
     */
    private function getRouteFileVersionFromFileName($file)
    {
        $fileNameWithoutExtension = $this->getRouteFileNameWithoutExtension($file);

        $fileNameWithoutExtensionExploded = explode('.', $fileNameWithoutExtension);

        end($fileNameWithoutExtensionExploded);

        $apiVersion = prev($fileNameWithoutExtensionExploded); // get the array before the last one

        // Skip versioning the API's root route
        if ($apiVersion === 'ApisRoot') {
            $apiVersion = '';
        }

        return $apiVersion;
    }

    /**
     * @param SplFileInfo $file
     *
     * @return  mixed
     */
    private function getRouteFileNameWithoutExtension(SplFileInfo $file)
    {
        $fileInfo = pathinfo($file->getFileName());

        return $fileInfo['filename'];
    }

    /**
     * Register the Containers WEB routes files
     *
     * @param $containerPath
     */
    private function loadWebContainerRoutes($containerPath): void
    {

        // build the container web routes path
        $webRoutesPath = $containerPath . '/UI/WEB/Routes';
        // build the namespace from the path
        $controllerNamespace = $containerPath . '\\UI\WEB\Controllers';
        $controllerNamespace = ucfirst(basename($containerPath)) . '\\UI\WEB\Controllers';
        // dump($webRoutesPath);
        if (File::isDirectory($webRoutesPath)) {
            $files = File::allFiles($webRoutesPath);
            $files = Arr::sort($files, function ($file) {
                return $file->getFilename();
            });
            foreach ($files as $file) {
                $this->loadWebRoute($file, $controllerNamespace);
            }
        }

        // for old Router
        // build the container web routes path
        $webRoutesPathOld = $containerPath . '/Router.php';
        // build the namespace from the path
        $controllerNamespace = ucfirst(basename($containerPath)) . '\\Controller';
        // dump($webRoutesPath);
        if (File::isFile($webRoutesPathOld)) {
            $this->loadWebRouteOld($webRoutesPathOld, $controllerNamespace);
        }
    }

    /**
     * @param $file
     * @param $controllerNamespace
     */
    private function loadWebRouteOld($file, $controllerNamespace): void
    {
        Route::group([

            'middleware' => ['web'],
        ], function ($router) use ($file) {
            require $file;
        });
    }

    /**
     * @param $file
     * @param $controllerNamespace
     */
    private function loadWebRoute($file, $controllerNamespace): void
    {
        Route::group([

            'middleware' => ['web'],
        ], function ($router) use ($file) {
            require $file->getPathname();
        });
    }
}
