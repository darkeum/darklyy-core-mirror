<?php

namespace Boot\System\Media\Downloaders;

interface Downloader
{
    public function getTempFile(string $url): string;
}
