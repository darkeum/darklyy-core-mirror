<?php

namespace Boot\System\Image\Imagick\Commands;

use Boot\System\Image\Commands\AbstractCommand;
use Boot\System\Image\Size;

class GetSizeCommand extends AbstractCommand
{
    /**
     * Reads size of given image instance in pixels
     *
     * @param  \Boot\System\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        /** @var \Imagick $core */
        $core = $image->getCore();

        $this->setOutput(new Size(
            $core->getImageWidth(),
            $core->getImageHeight()
        ));

        return true;
    }
}
