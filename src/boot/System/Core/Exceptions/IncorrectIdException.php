<?php

namespace Boot\System\Core\Exceptions;

use Boot\System\Core\Abstracts\Exceptions\Exception;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class IncorrectIdException extends Exception
{
    protected $code = SymfonyResponse::HTTP_BAD_REQUEST;
    protected $message = 'ID input is incorrect.';
}
