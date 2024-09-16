<?php

namespace Boot\System\Media\Support;

use Darkeum\MediaLibraryUI\Models\TemporaryUpload;
use Boot\System\Media\MediaCollections\Exceptions\FunctionalityNotAvailable;

class MediaLibraryPro
{
    public static function ensureInstalled(): void
    {
        if (! self::isInstalled()) {
            throw FunctionalityNotAvailable::mediaLibraryProRequired();
        }
    }

    public static function isInstalled(): bool
    {
        return class_exists(TemporaryUpload::class);
    }
}
