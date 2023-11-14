<?php

namespace Boot\System\Core\Exceptions;

use Boot\System\Core\Abstracts\Exceptions\Exception;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class MissingTestEndpointException extends Exception
{
    protected $code = SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR;
    protected $message = 'Property ($this->endpoint) is missed in your test.';
}
