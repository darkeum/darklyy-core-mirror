<?php

namespace Boot\System\Image\Gd\Commands;

use Boot\System\Image\Commands\AbstractCommand;

class BlurCommand extends AbstractCommand
{
    /**
     * Applies blur effect on image
     *
     * @param  \Boot\System\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $amount = $this->argument(0)->between(0, 100)->value(1);

        for ($i=0; $i < intval($amount); $i++) {
            imagefilter($image->getCore(), IMG_FILTER_GAUSSIAN_BLUR);
        }

        return true;
    }
}
