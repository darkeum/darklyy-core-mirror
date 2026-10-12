<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2026 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Testing\Concerns;

use Illuminate\Support\Facades\File;
use Boot\Support\Facades\ParallelTesting;
use UnexpectedValueException;

trait TestViews
{
    /**
     * The original compiled view path prior to appending the token.
     *
     * @var string|null
     */
    protected static $originalCompiledViewPath = null;

    /**
     * Boot test views for parallel testing.
     *
     * @return void
     */
    protected function bootTestViews()
    {
        ParallelTesting::setUpProcess(function () {
            if ($path = $this->parallelSafeCompiledViewPath()) {
                File::makeDirectory($path, 0755, true, true);
            }
        });

        ParallelTesting::setUpTestCase(function () {
            if ($path = $this->parallelSafeCompiledViewPath()) {
                $this->switchToCompiledViewPath($path);
            }
        });

        ParallelTesting::tearDownProcess(function () {
            if ($path = $this->parallelSafeCompiledViewPath()) {
                try {
                    File::deleteDirectory($path);
                } catch (UnexpectedValueException) {
                    // A concurrent parallel run removed the directory first, which is the state we want...
                }
            }
        });
    }

    /**
     * Get the test compiled view path.
     *
     * @return string|null
     */
    protected function parallelSafeCompiledViewPath()
    {
        self::$originalCompiledViewPath ??= $this->app['config']->get('view.compiled', '');

        if (! self::$originalCompiledViewPath) {
            return null;
        }

        return rtrim(self::$originalCompiledViewPath, '\/')
            .'/test_'
            .ParallelTesting::token();
    }

    /**
     * Switch to the given compiled view path.
     *
     * @param  string  $path
     * @return void
     */
    protected function switchToCompiledViewPath($path)
    {
        $this->app['config']->set('view.compiled', $path);

        if ($this->app->resolved('blade.compiler')) {
            $compiler = $this->app['blade.compiler'];

            (function () use ($path) {
                $this->cachePath = $path;
            })->bindTo($compiler, $compiler)();
        }
    }
}
