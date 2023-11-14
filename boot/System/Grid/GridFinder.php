<?php

namespace Boot\System\Grid;

use Exception;
use ReflectionClass;
use Boot\Support\Str;
use Boot\System\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Boot\System\Core\Foundation\Facades\Core;

class GridFinder
{
    protected $path;
    protected $files;
    protected $manifest;
    protected $manifestPath;
    protected $namespace;

    public function __construct(Filesystem $files, $manifestPath, $path, $namespace = null)
    {
        $this->files = $files;
        $this->path = $path;
        $this->manifestPath = $manifestPath;
        $this->namespace = $namespace;
    }

    public function find($alias)
    {
        $manifest = $this->getManifest();

        return $manifest[$alias] ?? $manifest["{$alias}.index"] ?? null;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function getManifest()
    {
        $this->build();
        if (!is_null($this->manifest)) {
            return $this->manifest;
        }

        if (!file_exists($this->manifestPath)) {
            $this->build();
        }


        return $this->manifest = $this->files->getRequire($this->manifestPath);
    }

    public function build()
    {
        $this->manifest = $this->getClassNames()
            ->mapWithKeys(function ($class) {
                if (!empty($namespace)) {
                    return [$namespace . '::' . $class::getName() => $class];
                }
                return [$class::getName() => $class];
            })->toArray();
        foreach ($this->getClassNamesFromModules() as $key => $value) {
            $this->manifest = array_merge($this->manifest, $value->toArray());
        }

        $this->write($this->manifest);

        return $this;
    }

    protected function write(array $manifest)
    {
        if (!is_writable(dirname($this->manifestPath))) {
            throw new Exception('The ' . dirname($this->manifestPath) . ' directory must be present and writable.');
        }

        $this->files->put($this->manifestPath, '<?php return ' . var_export($manifest, true) . ';', true);
    }

    public function getClassNames()
    {
        if (!$this->files->exists($this->path)) {
            return collect();
        }

        return collect($this->files->allFiles($this->path))
            ->map(function (SplFileInfo $file) {
                return '\\' .
                    Str::ucfirst(str($file->getPathname())
                        ->after(usr_path())
                        ->replace([DIRECTORY_SEPARATOR, '.php'], ['\\', ''])->__toString());
            })
            ->filter(function (string $class) {
                // dd(! (new ReflectionClass($class))->isAbstract());
                return is_subclass_of($class, Component::class) &&
                    !(new ReflectionClass($class))->isAbstract();
            });
    }

    public function getClassNamesFromModules()
    {
        $modulesComponents = [];
        foreach (Core::getAllContainerPaths() as $containerPath) {
            $containerDirectory = $containerPath . DIRECTORY_SEPARATOR . 'UI' . DIRECTORY_SEPARATOR . 'WEB' . DIRECTORY_SEPARATOR . 'Grids' . DIRECTORY_SEPARATOR;
            $containerName = basename($containerPath);
            if ($this->files->exists($containerDirectory)) {
                $modulesComponents[] = collect($this->files->allFiles($containerDirectory))
                    ->mapWithKeys(function (SplFileInfo $file) use ($containerName) {
                        if (str_contains($file->getPath(), 'vendor')) {
                            $start = '\\Module\\Vendor\\';
                            $containerNamePatch = modules_path('vendor' . DIRECTORY_SEPARATOR . $containerName);
                        } else {
                            $start = '\\';
                            $containerNamePatch = modules_path($containerName);
                        }
                        $class = $start .
                            Str::ucfirst($containerName . str($file->getPathname())
                                ->after($containerNamePatch)
                                ->replace([DIRECTORY_SEPARATOR, '.php'], ['\\', ''])->__toString());
                        $reflection = new \ReflectionClass($class);
                        return [Str::lower($containerName) . '::' . Str::lower($reflection->getShortName()) => $class];
                    })
                    ->filter(function (string $class) {
                        return is_subclass_of($class, GridModern::class) &&
                            !(new ReflectionClass($class))->isAbstract();
                    });
            }
        }
        return  $modulesComponents;
    }
}
