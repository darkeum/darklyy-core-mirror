<?php

namespace Boot\System\Image\Gd\Commands;

use Boot\System\Image\Commands\AbstractCommand;

class GreyscaleCommand extends AbstractCommand
{
    /**
     * Turns an image into a greyscale version
     *
     * @param  \Boot\System\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        return imagefilter($image->getCore(), IMG_FILTER_GRAYSCALE);
    }
}
