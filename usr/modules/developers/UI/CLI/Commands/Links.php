<?php

namespace Developers\UI\CLI\Commands;

use Boot\System\Console\Command;

class Links extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'developers:links
                {--relative : Create the symbolic link using relative paths}
                {--force : Recreate existing symbolic links}';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     */
    //protected static $defaultName = 'storage:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the symbolic links configured for the application';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $relative = $this->option('relative');

        foreach ($this->links() as $link => $target) {
            if (file_exists($link) && !$this->isRemovableSymlink($link, $this->option('force'))) {
                $this->error("The [$link] link already exists.");
                continue;
            }

            if (is_link($link)) {
                $this->laravel->make('files')->delete($link);
            }

            if ($relative) {
                $this->laravel->make('files')->relativeLink($target, $link);
            } else {
                $this->laravel->make('files')->link($target, $link);
            }

            $this->info("The [$link] link has been connected to [$target].");
        }

        $this->info('The links have been created.');
    }

    /**
     * Get the symbolic links that are configured for the application.
     *
     * @return array
     */
    protected function links()
    {
        return [
            // public_path('storage') => storage_path('app/public'),
            public_path('media') => base_path('media'),
            public_path('files') => base_path('files'),
            public_path('tmp') => base_path('tmp'),
            public_path('avatars') => base_path('resources' . DIRECTORY_SEPARATOR . 'avatars'),
            public_path('usr' . DIRECTORY_SEPARATOR . 'tpl') => usr_path('tpl'),
            public_path('usr' . DIRECTORY_SEPARATOR . 'plugins') => usr_path('plugins'),
            public_path('usr' . DIRECTORY_SEPARATOR . 'other') => usr_path('other'),
            public_path('usr' . DIRECTORY_SEPARATOR . 'modules') => usr_path('modules'),
            public_path('root' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'asset') => base_path('root' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'asset'),

            // public_path('storage') => base_path('public'),,
        ];
    }

    /**
     * Determine if the provided path is a symlink that can be removed.
     *
     * @param string $link
     * @param bool $force
     * @return bool
     */
    protected function isRemovableSymlink(string $link, bool $force): bool
    {
        return is_link($link) && $force;
    }
}
