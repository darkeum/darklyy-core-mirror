<?php

namespace Boot\System\Image;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ImageServiceProviderLeague extends AbstractServiceProvider
{
    /**
     * @var array $config
     */
    protected $config;

    /**
     * @var array $provides
     */
    protected $provides = [
        'Boot\System\Image\ImageManager'
    ];

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Register the server provider.
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->share('Boot\System\Image\ImageManager', function () {
            return new ImageManager($this->config);
        });
    }
}
