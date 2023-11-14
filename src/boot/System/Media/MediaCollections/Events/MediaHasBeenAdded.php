<?php

namespace Boot\System\Media\MediaCollections\Events;

use Illuminate\Queue\SerializesModels;
use Boot\System\Media\MediaCollections\Models\Media;

class MediaHasBeenAdded
{
    use SerializesModels;

    public function __construct(public Media $media)
    {
    }
}
