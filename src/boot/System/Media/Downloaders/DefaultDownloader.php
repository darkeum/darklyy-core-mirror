<?php

namespace Boot\System\Media\Downloaders;

use Boot\System\Media\MediaCollections\Exceptions\UnreachableUrl;

class DefaultDownloader implements Downloader
{
    public function getTempFile(string $url): string
    {
        if (! $stream = @fopen($url, 'r')) {
            throw UnreachableUrl::create($url);
        }

        $temporaryFile = tempnam(sys_get_temp_dir(), 'media-library');

        file_put_contents($temporaryFile, $stream);

        return $temporaryFile;
    }
}
