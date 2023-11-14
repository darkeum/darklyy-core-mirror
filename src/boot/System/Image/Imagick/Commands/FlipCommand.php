<?php

namespace Boot\System\Image\Imagick\Commands;

use Boot\System\Image\Commands\AbstractCommand;

class FlipCommand extends AbstractCommand
{
    /**
     * Mirrors an image
     *
     * @param  \Boot\System\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $mode = $this->argument(0)->value('h');

        if (in_array(strtolower($mode), [2, 'v', 'vert', 'vertical'])) {
            // flip vertical
            return $image->getCore()->flipImage();
        } else {
            // flip horizontal
            return $image->getCore()->flopImage();
        }
    }
}
