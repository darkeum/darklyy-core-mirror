<?php

namespace Boot\System\Image\Filters;

use Boot\System\Image\Image;

interface FilterInterface
{
    /**
     * Applies filter to given image
     *
     * @param  \Boot\System\Image\Image $image
     * @return \Boot\System\Image\Image
     */
    public function applyFilter(Image $image);
}
