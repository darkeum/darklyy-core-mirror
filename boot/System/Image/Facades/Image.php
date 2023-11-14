<?php

namespace Boot\System\Image\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Boot\System\Image\Image make(mixed $data)
 * @method static self configure(array $config)
 * @method static \Boot\System\Image\Image canvas(int $width, int $height, mixed $background = null)
 * @method static \Boot\System\Image\Image cache(\Closure $callback, int $lifetime = null, boolean $returnObj = false)
 */
class Image extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'image';
    }
}
