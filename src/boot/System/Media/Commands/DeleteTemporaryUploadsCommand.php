<?php

namespace Boot\System\Media\Commands;

use Illuminate\Console\Command;

class DeleteTemporaryUploadsCommand extends Command
{
    protected $signature = 'media-library:delete-old-temporary-uploads';

    protected $description = 'Delete old temporary uploads';

    public function handle()
    {
        $this->info('Start removing old temporary uploads...');

        $temporaryUploadModelClass = config('media.temporary_upload_model');

        $temporaryUploads = $temporaryUploadModelClass::old()->get();

        $temporaryUploads->each->delete();

        $this->comment($temporaryUploads.' old temporary upload(s) deleted!');
    }
}
