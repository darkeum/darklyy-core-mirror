<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Boot\System\Twig\Extensions;

use Twig\TwigFunction;
use \Boot\Support\Cache;
use \Boot\Support\Facades\Url;
use Boot\System\Twig\Extensions\Assets\TwigAssetsEngine;
use Twig\Extension\AbstractExtension;
use \Boot\System\Categories\Categories;

class AssetsExtension extends AbstractExtension
{
    /**
     * @var TwigAssetsEngine
     */
    private $engine;

    /**
     * TwigAssetsExtension constructor.
     *    
     */
    public function __construct()
    {
        $options = [
            'path' => ROOT . 'tmp/assets',
            'path_chmod' => 0755,
            'url_base_path' => '/tmp/assets/',
            'cache_path' => ROOT . 'tmp/assets',
            'cache_name' => 'assets-cache',
            'cache_lifetime' => 0,
            'minify' => 1
        ];
        $app = app();

        // dd( $app);
        $this->engine = new TwigAssetsEngine($options);
    }

    /**
     * Get functions.
     *
     * @return array The functions
     */
    public function getFunctions()
    {
        $function = new TwigFunction('assets', [$this, 'assets'], [
            'needs_environment' => false,
            'is_safe' => ['html'],
        ]);

        $function->setArguments([]);

        return [$function];
    }

    /**
     * Assets function.
     *
     * @return string The assets
     */
    public function assets(): string
    {
        $params = func_get_args();
        $assets = $params[0]['files'];
        unset($params[0]['files']);

        $attributes = $params[0]['attributes'] ?? [];
        unset($params[0]['attributes']);

        $options = $params[0];

        return $this->engine->assets($assets, $options, $attributes);
    }

   
}
