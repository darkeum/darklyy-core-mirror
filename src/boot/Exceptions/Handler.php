<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Exceptions;

use Exception;

class Handler {

    protected $errorHandler;
	protected $errorsAsExceptions;
	protected $canceled;

	public function __construct($errorHandler=NULL, $errorsAsExceptions=false, $overwriteHandler=false) {
		if($errorHandler && !is_callable($errorHandler)) {
			throw new \InvalidArgumentException('$errorHandler установлен, он не является вызываемой функцией!');
		}
		$this->errorHandler = $errorHandler;
		$this->errorsAsExceptions = (bool) $errorsAsExceptions;
		if($errorsAsExceptions){
			$oldErrorHandler = set_error_handler(array($this, 'errorHandler'));
			if($oldErrorHandler && !$overwriteHandler) {
				restore_error_handler();
				throw new \InvalidArgumentException('Обработчик ошибок уже установлен');
			}
		}
		register_shutdown_function(array($this, 'shutdownHandler'));
	}

	public function __destruct() {
		if($this->errorsAsExceptions){
			restore_error_handler();
		}
		$this->canceled = true;
	}

	public function shutdownHandler() {
		try{
			$error = error_get_last();
			if($error && !$this->canceled){
				if($this->errorsAsExceptions) {
					if(!$this->errorHandler) {
						$this->errorHandler = set_exception_handler(array($this, 'errorHandler'));
						restore_error_handler();
						if(!$this->errorHandler) {
							error_log('Не могу найти обработчик исключения для ошибки: '.print_r($error, true));
							return;
						}
					}

                			call_user_func($this->errorHandler,new Exception($error['message'], 0, $error['type'], $error['file'], $error['line']));
				}
				else {
					if(!$this->errorHandler) {
						$this->errorHandler = set_error_handler(array($this, 'errorHandler'));
						restore_error_handler();
						if(!$this->errorHandler) {
							return;
						}
					}
					call_user_func($this->errorHandler, $error['type'], $error['message'], $error['file'], $error['line']);
				}
			}
		}
		catch(\Exception $e){
			error_log("Исключение не может быть выброшено из shutdownHandler:\n".print_r($e, true), 4);
        	}
	}

	public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext=NULL) {
		throw New \ErrorException($errstr, 0, $errno, $errfile, $errline);
	}

    public static function run()
    {
        error_reporting(-1);

        if (!\in_array(\PHP_SAPI, ['cli', 'phpdbg'], true)) {
            ini_set('display_errors', 0);
        } elseif (!filter_var(ini_get('log_errors'), \FILTER_VALIDATE_BOOLEAN) || ini_get('error_log')) {
            ini_set('display_errors', 1);
        }

        @ini_set('zend.assertions', 1);
        ini_set('assert.active', 1);
        ini_set('assert.warning', 0);
        ini_set('assert.exception', 1);


    }
    
}