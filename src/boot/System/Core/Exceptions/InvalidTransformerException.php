<?php

namespace Boot\System\Core\Exceptions;

use Boot\System\Core\Abstracts\Exceptions\Exception;
use Boot\System\Core\Abstracts\Transformers\Transformer;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class InvalidTransformerException extends Exception
{
    protected $code = SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR;
    protected $message = 'Transformers must extended the ' . Transformer::class . ' class.';
}
