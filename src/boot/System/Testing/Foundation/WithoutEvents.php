<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2026 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Testing\Foundation;

use Exception;

trait WithoutEvents
{
    /**
     * Prevent all event handles from being executed.
     *
     * @throws \Exception
     */
    public function disableEventsForAllTests()
    {
        if (method_exists($this, 'withoutEvents')) {
            $this->withoutEvents();
        } else {
            throw new Exception('Unable to disable events. ApplicationTrait not used.');
        }
    }
}
