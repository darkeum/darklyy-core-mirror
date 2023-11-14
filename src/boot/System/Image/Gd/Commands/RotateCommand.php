<?php

namespace Boot\System\Image\Gd\Commands;

use Boot\System\Image\Commands\AbstractCommand;
use Boot\System\Image\Gd\Color;

class RotateCommand extends AbstractCommand
{
    /**
     * Rotates image counter clockwise
     *
     * @param  \Boot\System\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $angle = $this->argument(0)->type('numeric')->required()->value();
        $color = $this->argument(1)->value();
        $color = new Color($color);

        // restrict rotations beyond 360 degrees, since the end result is the same
        $angle = fmod($angle, 360);

        // rotate image
        $image->setCore(imagerotate($image->getCore(), $angle, $color->getInt()));

        return true;
    }
}
