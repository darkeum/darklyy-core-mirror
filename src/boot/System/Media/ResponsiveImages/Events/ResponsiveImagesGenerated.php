<?php

namespace Boot\System\Media\ResponsiveImages\Events;

use Illuminate\Queue\SerializesModels;
use Boot\System\Media\MediaCollections\Models\Media;

class ResponsiveImagesGenerated
{
    use SerializesModels;

    public function __construct(public Media $media)
    {
    }
}
