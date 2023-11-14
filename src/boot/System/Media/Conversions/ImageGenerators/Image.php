<?php

namespace Boot\System\Media\Conversions\ImageGenerators;

use Illuminate\Support\Collection;
use Boot\System\Media\Conversions\Conversion;

class Image extends ImageGenerator
{
    public function convert(string $path, Conversion $conversion = null): string
    {
        return $path;
    }

    public function requirementsAreInstalled(): bool
    {
        return true;
    }

    public function supportedExtensions(): Collection
    {
        $extensions = ['png', 'jpg', 'jpeg', 'gif'];
        if (config('media.image_driver') === 'imagick') {
            $extensions[] = 'tiff';
        }

        return collect($extensions);
    }

    public function supportedMimeTypes(): Collection
    {
        $mimeTypes = ['image/jpeg', 'image/gif', 'image/png'];
        if (config('media.image_driver') === 'imagick') {
            $mimeTypes[] = 'image/tiff';
        }

        return collect($mimeTypes);
    }
}
