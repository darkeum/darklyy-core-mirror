<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      ZeoNish
*/

namespace Calls\UI\CLI\Commands;

use Boot\Support\Facades\Cache;
use Boot\Support\Facades\Config;
use Boot\Support\Facades\DB;
use Boot\Support\Facades\File;
use Boot\Support\Facades\Storage;
use Boot\System\Core\Abstracts\Commands\ConsoleCommand;
use Calls\Models\Calls;
use Boot\Support\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;

class CallsGetCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'calls:get';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Взятие звонков';

    /**
     * Обработчик консольной команды.
     *
     */
    public function handle()
    {
        echo 'Начали - ' . date('d:m:Y H:i:s', time()) . ' ';
        // $start = microtime(true);

        # Конфиг
        $conf = Config::get('calls-config');

        // https://api.izhnet.ru/amocrm/asterisk/83/?_login=u83&_secret=TeuT9M3r0QEMWoH0iukCpQiWPDQyK9Hl&_action=cdr
        $json_url = $conf['api'] . '?_login=' . $conf['login'] . '&_secret=' . $conf['secret'] . '&_action=cdr'; // Формируем ссылку

        $json_data = file_get_contents($json_url); // Получаем text json
        $json_arr = json_decode($json_data, true);

        // Тут будет вся магия
        if ($json_arr['status'] == 'ok') {

            $calls = $json_arr['data']; // Наш массив со звонками
            if (!empty($calls)) {

                // Обновлять только те которых нет
                if ($conf['update']) {
                    $toDayDB = Calls::whereDate('created_at', Carbon::today())
                        ->whereNot('file', ['', null])
                        ->whereNot('calldate_at', ['', null])
                        ->pluck('uniqueid');
                    // dd($toDayDB);

                    $toDayGET = collect($calls)->pluck('uniqueid'); // Дернем уникальные ключи для удобства
                    // dd($toDayGET);

                    $toDayNEW = $toDayGET->diff($toDayDB); // Сравним то что есть в БД и уберем те где файл уже есть
                    // dd($toDayNEW);

                    // Фильтранем то что нам не нужно из запроса или оставим что то новое
                    $calls = collect($calls)->filter(function ($call, $key) use ($toDayNEW) {
                        return $toDayNEW->contains($call['uniqueid']);
                    })->toArray();
                    // dd($data);

                }

                // Создаем Клиент с редиректом для downloads
                $client = new Client(['allow_redirects' => true]);
                echo PHP_EOL;
                foreach ($calls as $key => $call) {

                    $calls[$key]['calldate'] = Carbon::parse($call['calldate'])->timestamp;  // ИСПРАВИТЬ для старого кода нужен был такой вывод
                    $calls[$key] += [
                        'calldate_at' => $call['calldate'], // Добавим дату
                        'status' => 1,                      // и статус
                    ];

                    // Если нужно скачивать файлы сразу демоном
                    if ($conf['downloads']) {

                        // Парсим // http://api.izhnet.ru/amocrm/asterisk/83/?GETFILE=
                        $response = $client->request('GET', $conf['api'], [
                            'query' => [
                                'GETFILE' => $call['recordingfile'],
                            ],
                            'on_stats' => function (TransferStats $stats) use (&$data) {

                                $url = $stats->getEffectiveUri();
                                if (strpos($url, 'storage')) {

                                    $data = [
                                        'url' => $url,
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
                                // Storage::disk('custom')->put($path, $response->getBody()->getContents());
                            }

                            $calls[$key] += [
                                'file' => Storage::url($path),
                                'type' => $response->getHeaderLine('Content-Type')
                            ];
                        }
                    }

                    // Выводим лог в консоль
                    echo $call['calldate'] . ' - ' . $call['uniqueid'];
                    echo PHP_EOL;

                }


                // Обновим или добавим в БД по уникальному полю
                DB::table('calls')->upsert($calls, ['uniqueid'], [
                    'file',
                    'type',
                    'calldate_at' // Временно тут что бы просто обновлять в БД после старого парсера
                ]);

            } else {
                echo 'Звонков на сегодня пока нет!';
            }

        }

        // echo PHP_EOL; // Перенос строки в консоли
        // echo 'Время выполнения скрипта: '.round(microtime(true) - $start, 4).' сек.';

        echo PHP_EOL; // Перенос строки в консоли
        echo 'Закончили - ' . date('d:m:Y H:i:s', time());
    }
}
