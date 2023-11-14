<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace App\Controllers;

use Storage;
use Illuminate\Http\Request;
use Boot\Abstracts\Controllers\Controller;
use Boot\System\Media\MediaCollections\Models\Media;

class MediaController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media $media
     * @return \Illuminate\Http\Response
     */
    public function download(Media $media)
    {
        // dd($media->previewUrl());
        // return response()->download($media->getPath(), $conversion);
        // $this->authorize('view', $media->model);

        return $media;
    }
}