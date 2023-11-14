<?php

namespace Boot\System\Media\Conversions\Events;

use Illuminate\Queue\SerializesModels;
use Boot\System\Media\Conversions\Conversion;
use Boot\System\Media\MediaCollections\Models\Media;

class ConversionWillStart
{
    use SerializesModels;

    public function __construct(public Media $media, public Conversion $conversion, public string $copiedOriginalFile)
    {
    }
}
