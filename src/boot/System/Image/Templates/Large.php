<?php

namespace Boot\System\Image\Templates;

use Boot\System\Image\Image;
use Boot\System\Image\Filters\FilterInterface;

class Large implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->fit(480, 360);
    }
}
