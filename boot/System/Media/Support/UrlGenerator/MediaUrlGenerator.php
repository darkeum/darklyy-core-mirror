<?php

namespace Boot\System\Media\Support\UrlGenerator;

use DateTimeInterface;
use Illuminate\Support\Facades\URL;

class MediaUrlGenerator extends DefaultUrlGenerator
{
    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        // dd($this->conversion);
        if (is_null($this->conversion)) {
            $conversion = null;
        } else {
            $conversion = $this->conversion->getName();
        }

        return URL::temporarySignedRoute(
            'media-storage',
            $expiration,
            ['media' => $this->media, 'conversion' => $conversion]
        );
    }
}
