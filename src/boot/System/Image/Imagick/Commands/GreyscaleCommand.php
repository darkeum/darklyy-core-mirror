<?php

namespace Boot\System\Image\Imagick\Commands;

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
        return $image->getCore()->modulateImage(100, 0, 100);
    }
}
