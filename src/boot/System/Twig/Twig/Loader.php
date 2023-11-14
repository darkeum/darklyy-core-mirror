<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Boot\System\Twig\Twig;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\ViewFinderInterface;
use InvalidArgumentException;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Basic loader using absolute paths.
 */
class Loader implements LoaderInterface
{

    const MAIN_NAMESPACE = '__main__';
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var \Illuminate\View\ViewFinderInterface
     */
    protected $finder;

    /**
     * @var string Twig file extension.
     */
    protected $extension;

    /**
     * @var array Template lookup cache.
     */
    protected $cache = [];

    /**
     * @param \Illuminate\Filesystem\Filesystem     $files     The filesystem
     * @param \Illuminate\View\ViewFinderInterface  $finder
     * @param string                                $extension Twig file extension.
     */
    public function __construct(Filesystem $files, ViewFinderInterface $finder, $extension = 'twig')
    {
        $this->files     = $files;
        $this->finder    = $finder;
        $this->extension = $extension;
    }

    /**
     * Return path to template without the need for the extension.
     *
     * @param string $name Template file name or path.
     *
     * @throws LoaderError
     * @return string Path to template
     */
    public function findTemplate($name)
    {

        if ($this->files->exists($name)) {
            return $name;
        }

        $name = $this->normalizeName($name);

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        try {
            // dd($this->finder);
            $this->cache[$name] = $this->finder->find($name);
        } catch (InvalidArgumentException $ex) {
            throw new LoaderError($ex->getMessage());
        }

        return $this->cache[$name];
    }

    /**
     * Normalize the Twig template name to a name the ViewFinder can use
     *
     * @param  string $name Template file name.
     * @return string The parsed name
     */
    protected function normalizeName($name)
    {
        if ($this->files->extension($name) === $this->extension) {
            $name = substr($name, 0, - (strlen($this->extension) + 1));
        }

        if (strripos($name, $this->extension)) {
            $name = substr($name, 0, - (strlen($this->extension) + 1));
        }

        // return preg_replace('#/{2,}#', '/', str_replace('\\', '/', $name));
        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        try {
            $this->findTemplate($name);
        } catch (LoaderError $exception) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceContext(String $name): Source
    {
        $path = $this->findTemplate($name);

        return new Source($this->files->get($path), $name, $path);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey(string $name): string
    {
        return $this->findTemplate($name);
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh(string $name, int $time): bool
    {
        return $this->files->lastModified($this->findTemplate($name)) <= $time;
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
        $this->finder->addLocation($path);
        // $this->finder[$namespace][] = rtrim($path, '/\\');
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
