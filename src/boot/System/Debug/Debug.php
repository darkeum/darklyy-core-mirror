<?php
/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Debug class
*/

namespace Boot\System\Debug;

use Boot\System\Debug\ErrorHandler;
use Boot\System\Debug\DebugClassLoader;
use Boot\System\Debug\BufferingLogger;

class Debug
{

    public static function enable(): ErrorHandler
    {
        error_reporting(-1);

        if (!\in_array(\PHP_SAPI, ['cli', 'phpdbg'], true)) {
            ini_set('display_errors', 0);
        } elseif (!filter_var(ini_get('log_errors'), \FILTER_VALIDATE_BOOLEAN) || ini_get('error_log')) {
            // CLI - display errors only if they're not already logged to STDERR
            ini_set('display_errors', 1);
        }

        @ini_set('zend.assertions', 1);
        ini_set('assert.active', 1);
        ini_set('assert.warning', 0);
        ini_set('assert.exception', 1);

        DebugClassLoader::enable();
        if (DEBUG) {
            $debug = true;
        } else {
            $debug = false;
        }
        return ErrorHandler::register(new ErrorHandler(new BufferingLogger(),  $debug));
    }
}
