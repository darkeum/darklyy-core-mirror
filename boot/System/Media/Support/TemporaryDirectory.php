<?php

namespace Boot\System\Media\Support;

use Illuminate\Support\Str;
use Boot\System\Media\TemporaryDirectory as BaseTemporaryDirectory;

class TemporaryDirectory
{
    public static function create(): BaseTemporaryDirectory
    {
        return new BaseTemporaryDirectory(static::getTemporaryDirectoryPath());
    }

    protected static function getTemporaryDirectoryPath(): string
    {
        $path = config('media.temporary_directory_path') ?? storage_path('media-library/temp');

        return $path.DIRECTORY_SEPARATOR.Str::random(32);
    }
}
