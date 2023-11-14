<?php


namespace Boot\System\Debug\ErrorRenderer;

use \Boot\System\Validation\Exceptions\ValidationException;
use \Boot\System\Debug\Exception\FlattenException;
use \Boot\System\Http\Request;


class JsonErrorRenderer implements ErrorRendererInterface
{

    public function render(\Throwable $exception): FlattenException
    {
        $format = 'json';
        $headers = [
            'Content-Type' => Request::getMimeTypes($format)[0] ?? $format,
            'Vary' => 'Accept',
        ];   
        $messageArray = [];
        $messageArray['message'] = $exception->getMessage();
        if($exception instanceof ValidationException) {
            $messageArray['errors'] = $exception->errors();
        }
        $flattenException = FlattenException::createFromThrowable($exception, null, $headers);
        return $flattenException->setAsString(json_encode($messageArray, JSON_UNESCAPED_UNICODE));
    }
}