<?php

namespace Boot\System\Image\Imagick\Commands;

use Boot\System\Image\Commands\AbstractCommand;

class InvertCommand extends AbstractCommand
{
    /**
     * Inverts colors of an image
     *
     * @param  \Boot\System\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        return $image->getCore()->negateImage(false);
    }
}
