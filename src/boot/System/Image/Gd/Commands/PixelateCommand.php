<?php

namespace Boot\System\Image\Gd\Commands;

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

        return imagefilter($image->getCore(), IMG_FILTER_PIXELATE, $size, true);
    }
}
