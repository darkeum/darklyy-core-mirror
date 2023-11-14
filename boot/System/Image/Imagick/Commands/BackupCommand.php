<?php

namespace Boot\System\Image\Imagick\Commands;

use Boot\System\Image\Commands\AbstractCommand;

class BackupCommand extends AbstractCommand
{
    /**
     * Saves a backups of current state of image core
     *
     * @param  \Boot\System\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $backupName = $this->argument(0)->value();

        // clone current image resource
        $clone = clone $image;
        $image->setBackup($clone->getCore(), $backupName);

        return true;
    }
}
