<?php

namespace Boot\System\Image\Gd\Commands;

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

        return imagefilter($image->getCore(), IMG_FILTER_CONTRAST, ($level * -1));
    }
}
