<?php

namespace Boot\System\Media\Conversions\ImageGenerators;

use Illuminate\Support\Collection;
use Boot\System\Media\Conversions\Conversion;

class Pdf extends ImageGenerator
{
    public function convert(string $file, Conversion $conversion = null): string
    {
        $imageFile = pathinfo($file, PATHINFO_DIRNAME).'/'.pathinfo($file, PATHINFO_FILENAME).'.jpg';

        $pageNumber = $conversion ? $conversion->getPdfPageNumber() : 1;

        (new \Boot\System\Media\Pdf\Pdf($file))->setPage($pageNumber)->saveImage($imageFile);

        return $imageFile;
    }

    public function requirementsAreInstalled(): bool
    {
        if (! class_exists(\Imagick::class)) {
            return false;
        }

        if (! class_exists(\Boot\System\Media\Pdf\Pdf::class)) {
            return false;
        }

        return true;
    }

    public function supportedExtensions(): Collection
    {
        return collect('pdf');
    }

    public function supportedMimeTypes(): Collection
    {
        return collect(['application/pdf']);
    }
}
