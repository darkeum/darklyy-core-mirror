<?php

namespace Boot\System\Media;

use Boot\Support\ServiceProvider;
use Boot\System\Media\Conversions\Commands\RegenerateCommand;
use Boot\System\Media\MediaCollections\Commands\CleanCommand;
use Boot\System\Media\MediaCollections\Commands\ClearCommand;
use Boot\System\Media\MediaCollections\Filesystem;
use Boot\System\Media\MediaCollections\MediaRepository;
use Boot\System\Media\MediaCollections\Models\Observers\MediaObserver;
use Boot\System\Media\ResponsiveImages\TinyPlaceholderGenerator\TinyPlaceholderGenerator;
use Boot\System\Media\ResponsiveImages\WidthCalculator\WidthCalculator;

class MediaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $mediaClass = config('media.media_model');

        $mediaClass::observe(new MediaObserver());

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'media-library');
    }

    public function register()
    {

        $this->app->scoped(MediaRepository::class, function () {
            $mediaClass = config('media.media_model');

            return new MediaRepository(new $mediaClass());
        });

        $this->registerCommands();
    }

    protected function registerCommands(): void
    {
        $this->app->bind(Filesystem::class, Filesystem::class);
        $this->app->bind(WidthCalculator::class, config('media.responsive_images.width_calculator'));
        $this->app->bind(TinyPlaceholderGenerator::class, config('media.responsive_images.tiny_placeholder_generator'));

        $this->app->bind('command.media-library:regenerate', RegenerateCommand::class);
        $this->app->bind('command.media-library:clear', ClearCommand::class);
        $this->app->bind('command.media-library:clean', CleanCommand::class);

        $this->commands([
            'command.media-library:regenerate',
            'command.media-library:clear',
            'command.media-library:clean',
        ]);
    }
}
