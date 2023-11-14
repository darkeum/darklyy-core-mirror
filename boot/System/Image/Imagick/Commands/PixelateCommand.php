<?php

namespace Boot\System\Image\Imagick\Commands;

use Boot\System\Image\Commands\AbstractCommand;

class PixelateCommand extends AbstractCommand
{
    /**
     * Applies a pixelation effect to a given image
     *
     * @param  \Boot\System\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $size = $this->argument(0)->type('digit')->value(10);

        $width = $image->getWidth();
        $height = $image->getHeight();

        $image->getCore()->scaleImage(max(1, intval($width / $size)), max(1, intval($height / $size)));
        $image->getCore()->scaleImage($width, $height);

        return true;
    }
}
