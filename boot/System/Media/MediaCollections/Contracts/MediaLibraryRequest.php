<?php

namespace Boot\System\Media\MediaCollections\Contracts;

use Illuminate\Support\Collection;

interface MediaLibraryRequest
{
    public function mediaLibraryRequestItems(string $key): Collection;
}
