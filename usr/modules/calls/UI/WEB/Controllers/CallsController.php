<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander
*/

namespace Calls\UI\WEB\Controllers;

use Boot\Abstracts\Controllers\WebController;
use Boot\Support\Facades\Config;
use Boot\Support\Facades\DB;
use Boot\System\Http\Request;
use Boot\Support\Facades\Storage;
use Calls\Models\Calls;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use Boot\Support\Facades\File;

class CallsController extends WebController
{
    static public function index()
    {
        return view('calls::manager.calls.index');
    }

    public function load(Request $request)
    {
        //Storage::disk('custom')->put('files/load/load.json', response()->json($request));

        return response()->json($request);
    }

    public function parse(Request $request)
    {
        $conf = Config::get('calls-config');

        $unit = Calls::find($request->id);
        // dd($request->id);

        if (File::exists($unit->file)) {
            $src = url($unit->file);
            $data = [
                'src' => asset($src),
            ];

            return response()->json($data);
        }

        // Создаем Клиент с редиректом для downloads
        $client = new Client(['allow_redirects' => true]);
        // Парсим // http://api.izhnet.ru/amocrm/asterisk/83/?GETFILE=
        $response = $client->request('GET', $conf['api'], [
            'query' => [
                'GETFILE' => $unit->recordingfile,
            ],
            'on_stats' => function (TransferStats $stats) use (&$data) {

                $url = $stats->getEffectiveUri();
                if (strpos($url, 'storage')) {

                    $data = [
                        'file' => pathinfo($url),
                    ];

                } else {
                    $data = false;
                }
            }
        ]);

        // Если файл есть и его можно скачать то ...
        if ($data) {

            $file = explode('_', $data['file']['basename'])[1];
            $path = $conf['folder'] . '/' . $file;

            // Если файла нет то создаем его
            if (!File::exists($path)) {
                File::put($path, $response->getBody()->getContents());
            }

            $data = [
                'src' => asset(Storage::url($path)), // Ресурсный путь для плеера
                'file' => Storage::url($path),
                'type' => $response->getHeaderLine('Content-Type')
            ];

            $unit->update([
                'file' => $data['file'],
                'type' => $data['type'],
            ]);
        } else {
            $unit->update(['file' => null]);
        }
        // dd($data);

        return response()->json($data);
    }
}
