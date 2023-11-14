<?php

namespace Boot\System\Media\ImageOptimizer\Optimizers;

use Boot\System\Media\ImageOptimizer\Image;

class Jpegoptim extends BaseOptimizer
{
    public $binaryName = 'jpegoptim';

    public function canHandle(Image $image): bool
    {
        return $image->mime() === 'image/jpeg';
    }
}
