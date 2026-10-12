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

trait WithoutMiddleware
{
    /**
     * Prevent all middleware from being executed for this test class.
     *
     * @throws \Exception
     */
    public function disableMiddlewareForAllTests()
    {
        if (method_exists($this, 'withoutMiddleware')) {
            $this->withoutMiddleware();
        } else {
            throw new Exception('Unable to disable middleware. MakesHttpRequests trait not used.');
        }
    }
}
