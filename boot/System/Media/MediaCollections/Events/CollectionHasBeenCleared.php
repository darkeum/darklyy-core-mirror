<?php

namespace Boot\System\Media\MediaCollections\Events;

use Illuminate\Queue\SerializesModels;
use Boot\System\Media\HasMedia;

class CollectionHasBeenCleared
{
    use SerializesModels;

    public function __construct(public HasMedia $model, public string $collectionName)
    {
    }
}
