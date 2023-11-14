<?php


namespace Boot\System\Inspector\Providers;


use Boot\System\Console\Events\CommandFinished;
use Boot\System\Console\Events\CommandStarting;
use Boot\Support\ServiceProvider;
use Boot\System\Inspector\Facades\Inspector;
use Boot\System\Inspector\Filters;
use Symfony\Component\Console\Input\ArgvInput;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $segments = [];

    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['events']->listen(CommandStarting::class, function (CommandStarting $event) {
            if (!$this->shouldBeMonitored($event->command)) {
                return;
            }

            if (Inspector::needTransaction()) {
                Inspector::startTransaction($event->command)
                    ->addContext('Command', [
                        'arguments' => $event->input->getArguments(),
                        'options' => $event->input->getOptions(),
                    ]);
            } elseif (Inspector::canAddSegments()) {
                $this->segments[$event->command] = Inspector::startSegment('darksander', $event->command);
            }
        });

        $this->app['events']->listen(CommandFinished::class, function (CommandFinished $event) {
            if (!$this->shouldBeMonitored($event->command)) {
                return;
            }

            if (Inspector::hasTransaction() && Inspector::currentTransaction()->name === $event->command) {
                Inspector::currentTransaction()->setResult($event->exitCode === 0 ? 'success' : 'error');
            } elseif (array_key_exists($event->command, $this->segments)) {
                $this->segments[$event->command]->end()->addContext('Command', [
                    'exit_code' => $event->exitCode,
                    'arguments' => $event->input->getArguments(),
                    'options' => $event->input->getOptions(),
                ]);
            }
        });
        // dd( $this->app['events']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Determine if the current command should be monitored.
     *
     * @param null|string $command
     * @return bool
     */
    protected function shouldBeMonitored(?string $command): bool
    {
        if (is_string($command)) {
            return Filters::isApprovedArtisanCommand($command, config('inspector.ignore_commands'));
        }

        return false;
    }
}
