<?php

namespace Boot\System\Image\Templates;

use Boot\System\Image\Image;
use Boot\System\Image\Filters\FilterInterface;

class Medium implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->fit(240, 180);
    }
}
