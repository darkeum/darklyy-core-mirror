<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Theme;

use Illuminate\View\FileViewFinder;
use Boot\System\Theme\Exceptions\ThemeBasePathNotDefined;

class ThemeViewFinder extends FileViewFinder
{
    /**
     * @var null|string
     */
    protected $activeTheme;

    /**
     * @var null|string
     */
    protected $parentTheme;

    public function getViewFinder()
    {
        return app('view')->getFinder();
    }

    public function setActiveTheme(string $theme, string $parentTheme = null): void
    {
        if ($theme) {
            $this->clearThemes();

            if ($parentTheme) {
                $this->registerTheme($parentTheme);

                $this->parentTheme = $parentTheme;
            }

            $this->registerTheme($theme);

            $this->activeTheme = $theme;
        }
    }

    public function setHints($hints): void
    {
        $this->hints = $hints;
    }

    public function getThemePath(string $theme, string $path = null): string
    {
        if (! config('theme.base_path')) {
            throw new ThemeBasePathNotDefined();
        }

        return $this->resolvePath(
            config('theme.base_path') . DIRECTORY_SEPARATOR . $theme . ($path ? DIRECTORY_SEPARATOR . $path : '')
        );
    }

    public function getThemeViewPath(string $theme = null): string
    {
        $theme = $theme ?? $this->getActiveTheme();

        return $this->getThemePath($theme, 'views');
    }

    /**
     * Получить имя активной темы.
     *
     * @return null|string
     */
    public function getActiveTheme()
    {
        return $this->activeTheme;
    }

    /**
     * Получить имя родительской темы.
     *
     * @return null|string
     */
    public function getParentTheme()
    {
        return $this->parentTheme;
    }

    public function clearThemes(): void
    {
        $paths = $this->getViewFinder()->getPaths();

        if ($this->getActiveTheme()) {
            if (($key = array_search($this->getThemeViewPath($this->getActiveTheme()), $paths)) !== false) {
                unset($paths[$key]);
            }
        }

        if ($this->getParentTheme()) {
            if (($key = array_search($this->getThemeViewPath($this->getParentTheme()), $paths)) !== false) {
                unset($paths[$key]);
            }
        }

        $this->activeTheme = null;
        $this->parentTheme = null;
        $this->getViewFinder()->setPaths($paths);
    }

    public function registerTheme(string $theme): void
    {
        $this->getViewFinder()->prependLocation($this->getThemeViewPath($theme));

        $this->registerNameSpacesForTheme($theme);
    }

    public function registerNameSpacesForTheme(string $theme): void
    {
        $vendorViewsPath = $this->getThemeViewPath($theme) . DIRECTORY_SEPARATOR . 'vendor';

        if (is_dir($vendorViewsPath)) {
            $directories = scandir($vendorViewsPath);

            foreach ($directories as $namespace) {
                if ($namespace != '.' && $namespace != '..') {
                    $path = $vendorViewsPath . DIRECTORY_SEPARATOR . $namespace;
                    $this->getViewFinder()->prependNamespace($namespace, $path);
                }
            }
        }
    }
}
