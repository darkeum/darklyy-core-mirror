<?php

namespace Boot\System\Image\Imagick\Commands;

use Boot\System\Image\Commands\AbstractCommand;

class ContrastCommand extends AbstractCommand
{
    /**
     * Changes contrast of image
     *
     * @param  \Boot\System\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $level = $this->argument(0)->between(-100, 100)->required()->value();

        return $image->getCore()->sigmoidalContrastImage($level > 0, $level / 4, 0);
    }
}
