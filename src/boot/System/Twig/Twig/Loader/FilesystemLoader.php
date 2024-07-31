<?php


namespace Boot\System\Twig\Twig\Loader;

use \Boot\Support\Str;
use \Twig\Error\LoaderError;
use \Twig\Loader\LoaderInterface;
use \Twig\Source;

/**
 * Loads template from the filesystem.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FilesystemLoader  implements LoaderInterface
{

    /** Identifier of the main namespace. */
    const MAIN_NAMESPACE = '__main__';

    protected $paths = [];
    protected $cache = [];
    protected $errorCache = [];

    private $rootPath;

    /**
     * @param string|array $paths    A path or an array of paths where to look for templates
     * @param string|null  $rootPath The root path common to all relative paths (null for getcwd())
     */
    public function __construct($paths = [], string $rootPath = null)
    {
        $this->rootPath = (null === $rootPath ? getcwd() : $rootPath) . \DIRECTORY_SEPARATOR;
        if (false !== $realPath = realpath($rootPath)) {
            $this->rootPath = $realPath . \DIRECTORY_SEPARATOR;
        }

        if ($paths) {
            $this->setPaths($paths);
        }

        $module = ['news', 'support', 'feedback', 'catalog', 'job', 'organizations', 'scan', 'portfolio', 'order', 'service', 'cashbox', 'reviews', 'project', 'promotions', 'certificates', 'bank', 'home', 'partner', 'control', 'scada', 'victoria', 'task2', 'selection', 'sales2', 'dev', 'warranty_card', 'users', 'chat', 'away', 'org', 'calculation', 'report', 'profile', 'price2'];
        foreach ($module as $key => $value) {
            $view = ROOT . 'usr/modules/' . $value . '/resources/views/';
            $view_upper = ROOT . 'usr/modules/' . $value . '/Resources/views/';
            $viewWEB = ROOT . 'usr/modules/' . $value . '/UI/WEB/Resources/Views/';
            $viewWEBNew = ROOT . 'usr/modules/' . $value . '/UI/WEB/Views/';
            $resources = ROOT . 'usr/modules/' . $value . '/';
            $router = ROOT . 'usr/modules/' . $value . '/Router.php';

            $lang = ROOT . 'usr/modules/' . $value . '/resources/langs/' . $value . '.ru.lang.php';

            if (is_dir($viewWEBNew)) {
                $this->addPath($viewWEBNew, $value . '__');
            }


            if (is_dir($viewWEB)) {
                // dump($viewWEB);
                $this->addPath($viewWEB, $value);
            } elseif (is_dir($view)) {
                $this->addPath($view, $value);
            } elseif (is_dir($view_upper)) {
                $this->addPath($view_upper, $value);
            } else {
                if (is_dir($viewWEBNew)) {
                    $this->addPath($viewWEBNew, $value);
                }
            }
            if (is_dir($resources)) {
                // dd($resources);
                $this->addPath($resources, $value . '_');
            }
            // dump($this);
            // if (file_exists($lang)) {
            //     $translator->addResource('php', $lang, 'ru');
            // }
            // $translator->addResource('php', ROOT . 'resources/langs/ru.grid.lang.php', 'ru');

            // $dirLng = ROOT . 'usr/modules/' . $value . '/Languages/ru';
            // if (is_dir($dirLng . '/')) {
            //     $filesLng = glob($dirLng . '/*.lang.php');
            //     foreach ($filesLng as $fileLng) {
            //         if (file_exists($fileLng)) {
            //             $translator->addResource('php', $fileLng, 'ru');
            //         }
            //     }
            // }

        }
        // dd($this);
    }

    /**
     * Returns the paths to the templates.
     */
    public function getPaths(string $namespace = self::MAIN_NAMESPACE): array
    {
        return isset($this->paths[$namespace]) ? $this->paths[$namespace] : [];
    }

    /**
     * Returns the path namespaces.
     *
     * The main namespace is always defined.
     */
    public function getNamespaces(): array
    {
        return array_keys($this->paths);
    }

    /**
     * @param string|array $paths A path or an array of paths where to look for templates
     */
    public function setPaths($paths, string $namespace = self::MAIN_NAMESPACE): void
    {
        if (!\is_array($paths)) {
            $paths = [$paths];
        }

        $this->paths[$namespace] = [];
        foreach ($paths as $path) {
            $this->addPath($path, $namespace);
        }
    }

    /**
     * @throws LoaderError
     */
    public function addPath(string $path, string $namespace = self::MAIN_NAMESPACE): void
    {
        // invalidate the cache
        $this->cache = $this->errorCache = [];

        $checkPath = $this->isAbsolutePath($path) ? $path : $this->rootPath . $path;
        if (!is_dir($checkPath)) {
            throw new LoaderError(sprintf('The "%s" directory does not exist ("%s").', $path, $checkPath));
        }

        $this->paths[$namespace][] = rtrim($path, '/\\');
    }

    /**
     * @throws LoaderError
     */
    public function prependPath(string $path, string $namespace = self::MAIN_NAMESPACE): void
    {
        // invalidate the cache
        $this->cache = $this->errorCache = [];

        $checkPath = $this->isAbsolutePath($path) ? $path : $this->rootPath . $path;
        if (!is_dir($checkPath)) {
            throw new LoaderError(sprintf('The "%s" directory does not exist ("%s").', $path, $checkPath));
        }

        $path = rtrim($path, '/\\');

        if (!isset($this->paths[$namespace])) {
            $this->paths[$namespace][] = $path;
        } else {
            array_unshift($this->paths[$namespace], $path);
        }
    }

    public function getSourceContext(string $name): Source
    {
        if (null === $path = $this->findTemplate($name)) {
            return new Source('', $name, '');
        }

        return new Source(file_get_contents($path), $name, $path);
    }

    public function getCacheKey(string $name): string
    {
        if (null === $path = $this->findTemplate($name)) {
            return '';
        }
        $len = \strlen($this->rootPath);
        if (0 === strncmp($this->rootPath, $path, $len)) {
            return substr($path, $len);
        }

        return $path;
    }

    /**
     * @return bool
     */
    public function exists(string $name)
    {

        $name = $this->normalizeName($name);

        if (isset($this->cache[$name])) {
            return true;
        }

        return null !== $this->findTemplate($name, false);
    }

    public function isFresh(string $name, int $time): bool
    {
        // false support to be removed in 3.0
        if (null === $path = $this->findTemplate($name)) {
            return false;
        }

        return filemtime($path) < $time;
    }


    /**
     * @return string|null
     */
    protected function findTemplate(string $name, bool $throw = true)
    {
        $name = $this->normalizeName($name);

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        if (isset($this->errorCache[$name])) {
            if (!$throw) {
                return null;
            }

            throw new LoaderError($this->errorCache[$name]);
        }

        try {
            $this->validateName($name);

            list($namespace, $shortname) = $this->parseName($name);
        } catch (LoaderError $e) {
            if (!$throw) {
                return null;
            }

            throw $e;
        }

        if (!isset($this->paths[$namespace])) {
            $this->errorCache[$name] = sprintf('There are no registered paths for namespace "%s".', $namespace);

            if (!$throw) {
                return null;
            }

            throw new LoaderError($this->errorCache[$name]);
        }

        foreach ($this->paths[$namespace] as $path) {
            if (!$this->isAbsolutePath($path)) {
                $path = $this->rootPath . $path;
            }

            if (is_file($path . '/' . $shortname)) {
                if (false !== $realpath = realpath($path . '/' . $shortname)) {
                    return $this->cache[$name] = $realpath;
                }

                return $this->cache[$name] = $path . '/' . $shortname;
            } elseif (is_file($shortname)) {
                if (false !== $realpath = realpath($shortname)) {
                    return $this->cache[$name] = $realpath;
                }

                return $this->cache[$name] = $path . '/' . $shortname;
            } elseif (is_file($path . '/' . $shortname . '.html.twig')) {
                if (false !== $realpath_ext = realpath($path . '/' . $shortname . '.html.twig')) {
                    return $this->cache[$name . '.html.twig'] = $realpath_ext;
                }
                return $this->cache[$name . '.html.twig'] = $path . '/' . $shortname . '.html.twig';
            }
        }

        $this->errorCache[$name] = sprintf('Unable to find template "%s" (looked into: %s).', $name, implode(', ', $this->paths[$namespace]));

        if (!$throw) {
            return null;
        }

        throw new LoaderError($this->errorCache[$name]);
    }

    private function normalizeName(string $name): string
    {
        return preg_replace('#/{2,}#', '/', str_replace('\\', '/', $name));
    }

    private function parseName(string $name, string $default = self::MAIN_NAMESPACE): array
    {
        if (isset($name[0]) && '@' == $name[0]) {
            if (false === $pos = strpos($name, '/')) {
                throw new LoaderError(sprintf('Malformed namespaced template name "%s" (expecting "@namespace/template_name").', $name));
            }

            $namespace = substr($name, 1, $pos - 1);
            $shortname = substr($name, $pos + 1);

            return [$namespace, $shortname];
        }

        return [$default, $name];
    }

    private function validateName(string $name): void
    {
        if (false !== strpos($name, "\0")) {
            throw new LoaderError('A template name cannot contain NUL bytes.');
        }

        $name = ltrim($name, '/');
        $parts = explode('/', $name);
        $level = 0;
        foreach ($parts as $part) {
            if ('..' === $part) {
                --$level;
            } elseif ('.' !== $part) {
                ++$level;
            }

            if ($level < 0) {
                throw new LoaderError(sprintf('Looks like you try to load a template outside configured directories (%s).', $name));
            }
        }
    }

    private function isAbsolutePath(string $file): bool
    {
        return strspn($file, '/\\', 0, 1)
            || (\strlen($file) > 3 && ctype_alpha($file[0])
                && ':' === $file[1]
                && strspn($file, '/\\', 2, 1))
            || null !== parse_url($file, PHP_URL_SCHEME);
    }
}
