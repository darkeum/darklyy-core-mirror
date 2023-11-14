<?php

namespace Boot\System\Media\Http\Controllers;

use Boot\System\Media\Request\UploadRequest;

class MediaLibraryUploadController
{
    public function __invoke(UploadRequest $request)
    {
        $temporaryUploadModelClass = config('media.temporary_upload_model');

        $temporaryUpload = $temporaryUploadModelClass::createForFile(
            $request->file,
            session()->getId(),
            $request->uuid,
            $request->name ?? '',
        );

        /** @var \Boot\System\Media\MediaCollections\Models\Media $media */
        $media = $temporaryUpload->getFirstMedia();

        return response()->json([
            'uuid' => $media->uuid,
            'name' => $media->name,
            'preview_url' => config('media.generate_thumbnails_for_temporary_uploads')
                ? $temporaryUpload->getFirstMediaUrl('default', 'preview')
                : '',
            'original_url' => $media->getUrl(),
            'size' => $media->size,
            'mime_type' => $media->mime_type,
            'extension' => $media->extension,
        ]);
    }
}
