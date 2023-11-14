<?php
/**
 * This file is part of the TwigBridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Boot\System\Twig\Extensions\Core;

use Twig\Extension\AbstractExtension;
use Boot\System\Twig\NodeVisitor\LaravelEventNodeVisitor;

/**
 * Send Laravel View Events
 */
class Event extends AbstractExtension
{

    public function getNodeVisitors()
    {
        return [
            new LaravelEventNodeVisitor(),
        ];
    }
}
