<?php

namespace Boot\System\Media\ImageOptimizer;

use Boot\System\Media\ImageOptimizer\Optimizers\Cwebp;
use Boot\System\Media\ImageOptimizer\Optimizers\Gifsicle;
use Boot\System\Media\ImageOptimizer\Optimizers\Jpegoptim;
use Boot\System\Media\ImageOptimizer\Optimizers\Optipng;
use Boot\System\Media\ImageOptimizer\Optimizers\Pngquant;
use Boot\System\Media\ImageOptimizer\Optimizers\Svgo;

class OptimizerChainFactory
{
    public static function create(array $config = []): OptimizerChain
    {
        $jpegQuality = '--max=85';
        $pngQuality = '--quality=85';
        if (isset($config['quality'])) {
            $jpegQuality = '--max='.$config['quality'];
            $pngQuality = '--quality='.$config['quality'];
        }

        return (new OptimizerChain())
            ->addOptimizer(new Jpegoptim([
                $jpegQuality,
                '--strip-all',
                '--all-progressive',
            ]))

            ->addOptimizer(new Pngquant([
                $pngQuality,
                '--force',
                '--skip-if-larger',
            ]))

            ->addOptimizer(new Optipng([
                '-i0',
                '-o2',
                '-quiet',
            ]))

            ->addOptimizer(new Svgo([
                '--disable={cleanupIDs,removeViewBox}',
            ]))

            ->addOptimizer(new Gifsicle([
                '-b',
                '-O3',
            ]))
            ->addOptimizer(new Cwebp([
                '-m 6',
                '-pass 10',
                '-mt',
                '-q 80',
            ]));
    }
}
