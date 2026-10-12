<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2026 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Testing\Foundation\Concerns;

use ErrorException;

trait InteractsWithDeprecationHandling
{
    /**
     * The original deprecation handler.
     *
     * @var callable|null
     */
    protected $originalDeprecationHandler;

    /**
     * Restore deprecation handling.
     *
     * @return $this
     */
    protected function withDeprecationHandling()
    {
        if ($this->originalDeprecationHandler) {
            set_error_handler(tap($this->originalDeprecationHandler, fn () => $this->originalDeprecationHandler = null));
        }

        return $this;
    }

    /**
     * Disable deprecation handling for the test.
     *
     * @return $this
     *
     * @throws \ErrorException
     */
    protected function withoutDeprecationHandling()
    {
        if ($this->originalDeprecationHandler == null) {
            $this->originalDeprecationHandler = set_error_handler(function ($level, $message, $file = '', $line = 0) {
                if (in_array($level, [E_DEPRECATED, E_USER_DEPRECATED]) || (error_reporting() & $level)) {
                    throw new ErrorException($message, 0, $level, $file, $line);
                }
            });
        }

        return $this;
    }
}
