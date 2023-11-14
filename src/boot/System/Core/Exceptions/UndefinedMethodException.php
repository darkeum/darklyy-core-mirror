<?php

namespace Boot\System\Core\Exceptions;

use Boot\System\Core\Abstracts\Exceptions\Exception;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class UndefinedMethodException extends Exception
{
    protected $code = SymfonyResponse::HTTP_FORBIDDEN;
    protected $message = 'Undefined HTTP Verb!';
}
