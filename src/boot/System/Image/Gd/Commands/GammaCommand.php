<?php

namespace Boot\System\Image\Gd\Commands;

use Boot\System\Image\Commands\AbstractCommand;

class GammaCommand extends AbstractCommand
{
    /**
     * Applies gamma correction to a given image
     *
     * @param  \Boot\System\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $gamma = $this->argument(0)->type('numeric')->required()->value();

        return imagegammacorrect($image->getCore(), 1, $gamma);
    }
}
