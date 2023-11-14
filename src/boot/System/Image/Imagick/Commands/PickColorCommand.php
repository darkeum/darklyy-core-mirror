<?php

namespace Boot\System\Image\Imagick\Commands;

use Boot\System\Image\Commands\AbstractCommand;
use Boot\System\Image\Imagick\Color;

class PickColorCommand extends AbstractCommand
{
    /**
     * Read color information from a certain position
     *
     * @param  \Boot\System\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $x = $this->argument(0)->type('digit')->required()->value();
        $y = $this->argument(1)->type('digit')->required()->value();
        $format = $this->argument(2)->type('string')->value('array');

        // pick color
        $color = new Color($image->getCore()->getImagePixelColor($x, $y));

        // format to output
        $this->setOutput($color->format($format));

        return true;
    }
}
