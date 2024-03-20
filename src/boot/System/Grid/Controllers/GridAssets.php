<?php

namespace Boot\System\Grid\Controllers;

use MatthiasMullie\Minify\Js;
use MatthiasMullie\Minify\Css;

class GridAssets
{
    use CanPretendToBeAFile;

    //
    public function source($path)
    {
        return $this->pretendResponseIsFile($this->generateSourceResource($path));
    }

    //
    public function script()
    {
        return $this->pretendResponseIsFile($this->generateSourceJavaScripts());
    }

    //
    public function style()
    {
        return $this->pretendResponseIsFile($this->generateSourceStyle(), 'text/css');
    }

    //
    public function aGridAssets($path)
    {
        return $this->pretendResponseIsFile($this->generateSourceResource($path));
    }

    //
    protected function generateSourceResource($path)
    {
        $source = __DIR__ . '/../Resources/' . $path;
        //if (!file_exists($source)) {
        //$minify = new Js();
        //$minify->minify($source);
        //}

        return $source;
    }

    //
    protected function generateSourceJavaScripts()
    {
        $source = __DIR__ . '/../Resources/dist/grid.js';
        if (!file_exists($source)) {
            $minify = new Js();
            $dir = __DIR__ . '/../Resources/src/js/';
            $f = scandir($dir);
            foreach ($f as $file) {
                if (preg_match('/\.(js)/', $file)) {
                    $minify->add($dir . $file);
                }
            }
            $minify->minify($source);
        }
        return $source;
    }

    protected function generateSourceStyle()
    {
        $source = __DIR__ . '/../Resources/dist/grid.css';
        // if (!file_exists($source)) {
            $minify = new Css();
            $dir = __DIR__ . '/../Resources/src/css/';
            $f = scandir($dir);
            foreach ($f as $file) {
                if (preg_match('/\.(css)/', $file)) {
                    $minify->add($dir . $file);
                }
            }
            $minify->minify($source);
        // }
        return $source;
    }
}
