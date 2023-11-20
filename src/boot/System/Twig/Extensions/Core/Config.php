<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Boot\System\Twig\Extensions\Core;

use Boot\System\Config\Repository as ConfigRepository;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * Access Laravels config class in your Twig templates.
 */
class Config extends AbstractExtension
{
    /**
     * @var \Boot\System\Config\Repository
     */
    protected $config;

    /**
     * Create a new config extension
     *
     * @param \Boot\System\Config\Repository
     */
    public function __construct(ConfigRepository $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_Config';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('config', [$this->config, 'get']),
            new TwigFunction('config_get', [$this->config, 'get']),
            new TwigFunction('config_has', [$this->config, 'has']),
        ];
    }
}
