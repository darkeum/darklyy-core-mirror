<?php

namespace Boot\System\Media\Image\Exceptions;

use Exception;

class InvalidImageDriver extends Exception
{
    public static function driver(string $driver): self
    {
        return new self("Driver must be `gd` or `imagick`. `{$driver}` provided.");
    }
}
