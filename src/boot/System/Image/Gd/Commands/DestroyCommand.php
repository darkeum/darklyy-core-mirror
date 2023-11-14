<?php

namespace Boot\System\Image\Gd\Commands;

use Boot\System\Image\Commands\AbstractCommand;

class DestroyCommand extends AbstractCommand
{
    /**
     * Destroys current image core and frees up memory
     *
     * @param  \Boot\System\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        // destroy image core
        imagedestroy($image->getCore());

        // destroy backups
        foreach ($image->getBackups() as $backup) {
            imagedestroy($backup);
        }

        return true;
    }
}
