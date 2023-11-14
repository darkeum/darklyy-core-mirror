<?php

namespace Boot\System\Media\ResponsiveImages\TinyPlaceholderGenerator;

use Boot\System\Media\Support\ImageFactory;

class Blurred implements TinyPlaceholderGenerator
{
    public function generateTinyPlaceholder(string $sourceImagePath, string $tinyImageDestinationPath): void
    {
        $sourceImage = ImageFactory::load($sourceImagePath);

        $sourceImage->width(32)->blur(5)->save($tinyImageDestinationPath);
    }
}
