<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\WEB\Controllers;

use Scada\Models\ScadaTags;
use \Boot\Controller\AbstractController;
use Scada\UI\API\Requests\ScadaInfoRequest;

class ScadaPublicController extends AbstractController
{

    public static $module = 'scada';

    public static function project(ScadaInfoRequest $request)
    {
        return view('scada::public.project.project-' . $request->id . '.project',  [
            'type' => 'store',
            'id' => $request->id,
        ]);
    }

    public static function variable($route = null)
    {
        $tags = ScadaTags::where('scada_id', 3)->get();
        display('@' . static::$module . '/public/project/holm/variable.html.twig', [
            'tags' => $tags,
        ]);
    }

    public static function index($route = null)
    {
        // dd(Scada::getScadaLoop());
    }

    public static function ajax($id)
    {
        $scadaTags = ScadaTags::where('scada_id', $id)->get()->toArray();
        foreach ($scadaTags as $key => $value) {
        }
        echo json_encode($scadaTags, JSON_UNESCAPED_UNICODE);
        exit;
    }


    public static function chart(ScadaInfoRequest $request)
    {

        return view('scada::public.chart.index',  ['id' => $request->id]);
    }
}
