<?php

namespace Boot\System\Media\Support;

use Boot\System\Media\Image\Image;

class ImageFactory
{
    public static function load(string $path): Image
    {
        return Image::load($path)->useImageDriver(config('media.image_driver'));
    }
}
