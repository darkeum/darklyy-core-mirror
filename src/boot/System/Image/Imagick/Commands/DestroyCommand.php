<?php

namespace Boot\System\Image\Imagick\Commands;

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
        $image->getCore()->clear();

        // destroy backups
        foreach ($image->getBackups() as $backup) {
            $backup->clear();
        }

        return true;
    }
}
