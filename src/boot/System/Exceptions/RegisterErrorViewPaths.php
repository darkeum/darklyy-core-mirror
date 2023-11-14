<?php

namespace Boot\System\Exceptions;

use Illuminate\Support\Facades\View;

class RegisterErrorViewPaths
{
    /**
     * Register the error view paths.
     *
     * @return void
     */
    public function __invoke()
    {
        // dd(View::replaceNamespace());
        View::replaceNamespace('errors', collect(config('view.paths'))->map(function ($path) {
            return "{$path}/errors";
        })->push(__DIR__.'/views')->all());
    }
}
