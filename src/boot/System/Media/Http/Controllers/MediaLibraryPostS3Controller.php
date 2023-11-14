<?php

namespace Boot\System\Media\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Boot\System\Media\Conversions\FileManipulator;
use Boot\System\Media\Request\CreateTemporaryUploadFromDirectS3UploadRequest;

class MediaLibraryPostS3Controller
{
    public function __invoke(
        CreateTemporaryUploadFromDirectS3UploadRequest $request,
        FileManipulator $fileManipulator
    ) {
        $diskName = config('media.disk_name');

        $temporaryUploadModelClass = config('media.temporary_upload_model');

        $temporaryUpload = $temporaryUploadModelClass::create([
            'session_id' => session()->getId(),
        ]);

        /** @var \Boot\System\Media\MediaCollections\Models\Media $media */
        $media = $temporaryUpload->media()->create([
           'name' => $request->name,
           'uuid' => $request->uuid,
           'collection_name' => 'default',
           'file_name' => $request->name,
           'mime_type' => $request->content_type,
           'disk' => $diskName,
           'conversions_disk' => $diskName,
           'manipulations' => [],
           'custom_properties' => [],
           'responsive_images' => [],
           'generated_conversions' => [],
           'size' => $request->size,
       ]);

        /** @var \Boot\System\Media\Support\PathGenerator\PathGenerator $pathGenerator */
        $pathGenerator = app(config('media.path_generator'));

        Storage::disk($diskName)->copy(
            $request->key,
            $pathGenerator->getPath($media) . '/' . $request->name,
        );

        $fileManipulator->createDerivedFiles($media);

        return response()->json([
            'name' => $media->name,
            'file_name' => $media->file_name,
            'uuid' => $media->uuid,
            'preview_url' => $media->hasGeneratedConversion('preview') ? $media->getUrl('preview') : '',
            'original_url' => $media->getUrl(),
            'order' => $media->order_column,
            'custom_properties' => $media->custom_properties,
            'extension' => $media->extension,
            'size' => $media->size,
        ]);
    }
}
