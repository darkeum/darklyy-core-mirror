<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      ZeoNish
*/

namespace Developers\UI\CLI\Commands;

use Boot\Support\Facades\Config;
use Boot\Support\Facades\DB;
use Boot\System\Core\Abstracts\Commands\ConsoleCommand;
use Catalog\Models\Catalog;
use Developers\UI\WEB\Controllers\WiloController;

class WiloLoaderCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'dev:wilo:load';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Парсер БД Wilo';

    /**
     * Обработчик консольной команды.
     *
     */
    public function handle()
    {
        echo 'Начали - ' . date('d:m:Y H:i:s', time()) . ' ';

        ini_set('max_execution_time', 0);

        $config = config('wiloparser.params');
        $types = collect($config['types']);
        $counter = 0;

        // Конект к БД DEV
        Config::set('database.connections.wilo', $config['DB']);
        $db = DB::connection('wilo');

        $catalog = Catalog::where('hidden', 0);
        // dd($catalog);

        $sku = $catalog->pluck('id', 'sku')->diff(['']);
        // dd($sku);

        // Берем артикулы из Wilo
        $items = $db->table('Wilo_Items')
            ->pluck('REFID_', 'ARTNR_');
        // dd($items);

        // Выбираем те что есть у нас в БД
        $items = $items->filter(function ($item, $key) use ($sku) {
            return $sku->has($key);
        });

        // Поиск для PERFORMANCECHART
        $data_Pump = $db->table('Wilo_Data')
            ->whereIn('ID_', $items)
            ->where('CLASS_', 'TPumpObj')
            ->get();

        // Поиск для POINTS
        $data_Obj = $db->table('Wilo_Data')
            ->where('CLASS_', 'TKfldObj')
            ->get();
        // dd($data_Obj);

        // Общий сборщик
        foreach ($data_Pump as $key => $d_Pump) {

            // Парсим
            $d_Pump = WiloController::divider_str($d_Pump);

            $has = $d_Pump->get('FIELDS_')->get('OBJECT_PUMP');

            // Если нет OBJECT_PUMP
            if ($has) {
                $PH = $has->get('PERFORMANCECHART');
            } else {
                $PH = $d_Pump->get('ID_');
            }

            // Если в PERFORMANCECHART есть 2 значения через \t
            if (mb_substr_count($PH, "\t")) {
                $PH = explode("\t", $PH)[0];
            }

            // Поиск PERFORMANCECHART в CLASS_ => TKfldObj
            $d_Obj = $data_Obj->filter(function ($item, $key) use ($PH) {
                return $item->ID_ == $PH;
            });

            // Парсим
            $d_Obj = WiloController::divider_str($d_Obj->first());
            // dd('$d_Obj', $d_Obj);

            // ID_ из CLASS_ => TPumpObj
            $sID = $d_Pump->get('ID_');

            // Поиск артикула в Items
            $art = $items->filter(function ($item, $key) use ($sID) {
                return $item == $sID;
            });
            $art = $art->keys()->first();
            // dd($art);

            // Получаем ID из Каталога
            $cid = $sku->get($art);
            // dd($id);

            // Собираем коллекцию
            $unit = [
                'ID' => $cid,
                'FIELDS_' => $d_Pump->get('FIELDS_')->merge($d_Obj->get('FIELDS_')),
                'PERFORMANCECHART' => $PH,
                'P_ID' => $d_Pump->get('id'),
                'O_ID' => $d_Obj->get('id'),
                'CATALOG_ID' => $cid,
                'ART' => $art,
            ];
            $unit = collect($unit);
            // dd($unit);


            // Получаем данные и записываем !!!
            # ВЕС
            $w = null;
            $weight = collect([1 => null, 2 => null, 3 => null, 4 => null, 5 => null]);
            $w_d_Pump_P = $d_Pump->get('FIELDS_')->get('OBJECT_PUMP');
            $w_d_Pump_I = $d_Pump->get('FIELDS_')->get('OBJECT_INSTALLATION');

            if ($w_d_Pump_P->has('WEIGHT')) {
                $w = $w_d_Pump_P->get('WEIGHT');
            } elseif ($w_d_Pump_I->has('WEIGHT')) {
                $w = $w_d_Pump_I->get('WEIGHT');
            }

            // Если вес не пуст
            if (!is_null($w)) {
                $weight->put(1, $w);
            }
            // dd($weight);
            Catalog::find($cid)->update(['weight' => $weight]);

            # Точки
            $points = $unit->get('FIELDS_')->get('OBJECT_PART');
            if ($points) {

                // Если вдруг подзаголовок ОДИН
                if ($points->has('IDENT')) {
                    $points = collect([$points['IDENT'] => $points]);
                }

                echo "\n" . '#' .$key . ' Насос ID :' . $cid . ' // Артикул :' . $art . ' // PERFORMANCECHART :' . $PH . "\n";
                $counter++;

                $points->each(function ($item, $key) use ($cid, $types, $counter) {
                    // dd($item);

                    // Проверка, а есть ли POINTS (Бывает что нет... в MVI 212 CN)
                    if ($item->has('POINTS')) {
                        $pointToJson = collect();

                        foreach ($item->get('POINTS')->all() as $point) {
                            $point = explode("\t", $point);
                            $pointToJson->push(["Q" => $point[0], "H" => $point[1]]);
                        }

                        $text_type = '';
                        if ($types->has($key)) {
                            $type = $types->get($key);
                        } else {
                            $type = $key;
                            $text_type = ' - ' . $key . ' - ТАКОГО НЕТ';
                        }

                        DB::table('pump')->updateOrInsert(
                            [
                                'cid' => $cid,
                                'type' => $type
                            ],
                            [
                                'uid' => 79, // Костя
                                'uid_change' => 1092, // Серега
                                'data' => $pointToJson->toJson(),
                            ]
                        );

                        echo $counter . ' - ' . $cid . $text_type . "\n";

                    }

                });
            }

        }

        echo "\n\n# # # # # # # # # # # # # # # # # # # # #";
        echo "\n" . 'Всего : ' . $counter . "\n\n";

        // echo PHP_EOL; // Перенос строки в консоли

        echo 'Закончили - ' . date('d:m:Y H:i:s', time());
    }


}
