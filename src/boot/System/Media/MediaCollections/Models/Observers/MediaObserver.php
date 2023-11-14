<?php

namespace Boot\System\Media\MediaCollections\Models\Observers;

use Illuminate\Database\Eloquent\SoftDeletes;
use Boot\System\Media\Conversions\FileManipulator;
use Boot\System\Media\MediaCollections\Filesystem;
use Boot\System\Media\MediaCollections\Models\Media;

class MediaObserver
{
    public function creating(Media $media)
    {
        if ($media->shouldSortWhenCreating()) {
            if (is_null($media->order_column)) {
                $media->setHighestOrderNumber();
            }
        }
    }

    public function updating(Media $media)
    {
        /** @var \Boot\System\Media\MediaCollections\Filesystem $filesystem */
        $filesystem = app(Filesystem::class);

        if (config('media.moves_media_on_update')) {
            $filesystem->syncMediaPath($media);
        }

        if ($media->file_name !== $media->getOriginal('file_name')) {
            $filesystem->syncFileNames($media);
        }
    }

    public function updated(Media $media)
    {
        if (is_null($media->getOriginal('model_id'))) {
            return;
        }

        $original = $media->getOriginal('manipulations');

        if ($media->manipulations !== $original) {
            $eventDispatcher = Media::getEventDispatcher();
            Media::unsetEventDispatcher();

            /** @var \Boot\System\Media\Conversions\FileManipulator $fileManipulator */
            $fileManipulator = app(FileManipulator::class);

            $fileManipulator->createDerivedFiles($media);

            Media::setEventDispatcher($eventDispatcher);
        }
    }

    public function deleted(Media $media)
    {
        if (in_array(SoftDeletes::class, class_uses_recursive($media))) {
            if (! $media->isForceDeleting()) {
                return;
            }
        }

        /** @var \Boot\System\Media\MediaCollections\Filesystem $filesystem */
        $filesystem = app(Filesystem::class);

        $filesystem->removeAllFiles($media);
    }
}
