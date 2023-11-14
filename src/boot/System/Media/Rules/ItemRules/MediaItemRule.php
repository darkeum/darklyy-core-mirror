<?php

namespace Boot\System\Media\Rules\ItemRules;

use Illuminate\Contracts\Validation\Rule;
use Boot\System\Media\MediaCollections\Models\Media;

abstract class MediaItemRule implements Rule
{
    public $value;

    public function passes($attribute, $value)
    {
        $this->value = $value;

        return $this->validateMediaItem();
    }

    public function getTemporaryUploadMedia(): ?Media
    {
        $temporaryUploadModelClass = config('media.temporary_upload_model');

        $temporaryUpload = $temporaryUploadModelClass::findByMediaUuidInCurrentSession($this->value['uuid']);

        if (! $temporaryUpload) {
            return null;
        }

        return $temporaryUpload->getFirstMedia();
    }

    abstract public function validateMediaItem(): bool;
}
