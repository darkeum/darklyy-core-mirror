<?php

namespace Boot\System\Media\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Boot\System\Livewire\FileUploadConfiguration;
use Boot\System\Livewire\TemporaryUploadedFile;
use Boot\System\Media\Conversions\FileManipulator;
use Boot\System\Media\MediaCollections\Models\Media;
use Boot\System\Media\Models\TemporaryUpload;

class ConvertLivewireUploadToMediaAction
{
    public function execute(TemporaryUploadedFile $temporaryUploadedFile): Media
    {
        return FileUploadConfiguration::isUsingS3()
            ? $this->createFromS3LivewireUpload($temporaryUploadedFile)
            : $this->createFromLocalLivewireUpload($temporaryUploadedFile);
    }

    protected function createFromLocalLivewireUpload(TemporaryUploadedFile $livewireUpload): Media
    {
        $uploadedFile = new UploadedFile($livewireUpload->path(), $livewireUpload->getClientOriginalName());

        $temporaryUploadModelClass = config('media.temporary_upload_model');

        $livewireUpload = $temporaryUploadModelClass::createForFile(
            $uploadedFile,
            session()->getId(),
            (string)Str::uuid(),
            $livewireUpload->getClientOriginalName()
        );

        return $livewireUpload->getFirstMedia();
    }

    protected function createFromS3LivewireUpload(TemporaryUploadedFile $livewireUpload): Media
    {
        $temporaryUploadModelClass = config('media.temporary_upload_model');

        /** @var TemporaryUpload $temporaryUpload */
        $temporaryUpload = $temporaryUploadModelClass::create([
            'session_id' => session()->getId(),
        ]);

        $livewireDisk = config('livewire.temporary_file_upload.disk', 's3');

        /** @var \Boot\System\Media\MediaCollections\Models\Media $media */
        $media = $temporaryUpload->media()->create([
            'name' => $livewireUpload->getClientOriginalName(),
            'collection_name' => 'default',
            'uuid' => (string)Str::uuid(),
            'disk' => $livewireDisk,
            'conversions_disk' => $livewireDisk,
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
            'size' => $livewireUpload->getSize(),
            'file_name' => $livewireUpload->getClientOriginalName(),
        ]);

        /** @var \Boot\System\Media\Support\PathGenerator\PathGenerator $pathGenerator */
        $pathGenerator = app(config('media.path_generator'));

        $targetPath = $pathGenerator->getPath($media) . '/' . $livewireUpload->getClientOriginalName();

        $livewireDirectory = config('livewire.temporary_file_upload.directory') ?? '/livewire-tmp/';

        Storage::disk($livewireDisk)->move(
            Str::of($livewireDirectory)->start('/')->finish('/') . $livewireUpload->getFilename(),
            $targetPath
        );

        /** @var \Boot\System\Media\Conversions\FileManipulator $fileManipulator */
        $fileManipulator = app(FileManipulator::class);

        $fileManipulator->createDerivedFiles($media);

        return $media;
    }
}
