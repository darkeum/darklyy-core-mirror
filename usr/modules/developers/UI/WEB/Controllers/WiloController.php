<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Developers\UI\WEB\Controllers;

use Boot\Abstracts\Controllers\WebController;
use Boot\System\Http\Request;
use Catalog\Models\Catalog;
use Catalog_v2\Model\Pump;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use \Boot\Support\Facades\Auth;
use Illuminate\Support\Collection;


class WiloController extends WebController
{
    static public function index()
    {
        return view('developers::manager.wilo');
    }

    static public function loader(Request $request)
    {
        $arts = $request['arts'];

        // Берем артикулы из Wilo
        if ($arts ?? false) {

            ini_set('max_execution_time', 600);

            $config = config('wiloparser.params');
            $types = collect($config['types']);


            // Конект к БД DEV
            Config::set('database.connections.wilo', $config['DB']);
            $db = DB::connection('wilo');

            $catalog = Catalog::where('hidden', 0);
            // dd($catalog);

            $sku = $catalog->pluck('id', 'sku')->diff(['']);
            // dd($sku);

            $items = $db->table('Wilo_Items')
                ->whereIn('ARTNR_', $arts)
                ->pluck('REFID_', 'ARTNR_');
            // dd($items);

            // Выбираем те что есть у нас в БД
            $items = $items->filter(function ($item, $key) use ($sku) {
                return $sku->has($key);
            });
            // dd($items);

            // Поиск для PERFORMANCECHART
            $data_Pump = $db->table('Wilo_Data')
                ->whereIn('ID_', $items)
                ->where('CLASS_', 'TPumpObj')
                ->get();
            // dd($data_Pump);

            // Поиск для POINTS
            $data_Obj = $db->table('Wilo_Data')
                ->where('CLASS_', 'TKfldObj')
                ->get();
            // dd($data_Obj);

            // Общий сборщик
            foreach ($data_Pump as $key => $d_Pump) {

                // Парсим $d_Pump
                $d_Pump = self::divider_str($d_Pump);

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

                // Парсим $d_Obj
                $d_Obj = self::divider_str($d_Obj->first());

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
                // ВЕС
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

                // Точки
                $points = $unit->get('FIELDS_')->get('OBJECT_PART');
                if ($points) {

                    // Если вдруг подзаголовок ОДИН
                    if ($points->has('IDENT')) {
                        $points = collect([$points['IDENT'] => $points]);
                    }

                    $points->each(function ($item, $key) use ($cid, $types) {
                        // dd($item);

                        // Проверка, а есть ли POINTS (Бывает что нет... в MVI 212 CN)
                        if ($item->has('POINTS')) {
                            $pointToJson = collect();

                            foreach ($item->get('POINTS')->all() as $point) {
                                $point = explode("\t", $point);
                                $pointToJson->push(["Q" => $point[0], "H" => $point[1]]);
                            }

                            // Проверка существования ключа
                            if ($types->has($key)) {
                                $type = $types->get($key);
                            } else {
                                $type = $key;
                            }

                            DB::table('pump')->updateOrInsert(
                                [
                                    'cid' => $cid,
                                    'type' => $type
                                ],
                                [
                                    'uid' => Auth::id(),
                                    'uid_change' => Auth::id(),
                                    'data' => $pointToJson->toJson(),
                                ]
                            );

                        }

                    });
                }

            }

            return true; // 1

        } else {
            return false;
        }
    }

    // Делитель строк
    static public function divider_str($data)
    {
        $config = config('wiloparser.params'); // Конфиг
        $filters = $config['filters'];         // Данные которые надо доставать
        $groups = $config['groups'];           // Группировка данных по первому последующему слову

        $TEST = collect();           // Тестовая коллекция
        $OLD = collect();            // Старые данные коллекция

        $MAIN = collect($data);
        // dd($MAIN);

        // Работа по TYPE_
        $TYPE = $MAIN['TYPE_'] ?? false;
        if ($TYPE or $TYPE == '-') {

            $TYPE = explode("\r\n", $TYPE);
            $TYPE = array_diff($TYPE, array(0, null)); // Чистим от пустых элементов массива
            $TYPE = array_values((array)$TYPE);        // Обновляем индексацию в массиве
            // dd($TYPE);

            $MAIN->put('TYPE_', $TYPE); // Присвоить готовый массив к категории
        }

        // Работа по FIELDS_
        $FIELDS = $MAIN['FIELDS_'] ?? false;
        if ($FIELDS or $FIELDS == '-') {

            $separator = 'OBJECT'; // Разделитель для "групп" данных

            $chars = ['!', '#', '$'];                         // Символы для чистки
            $FIELDS = trim(str_replace($chars, '', $FIELDS)); // Чистим лишние символы '!', '#', '$'

            $FIELDS = explode($separator, $FIELDS);        // Делим по "сепатору"
            $FIELDS = array_diff($FIELDS, array(0, null)); // Чистим от пустых элементов массива
            $FIELDS = array_values((array)$FIELDS);        // Обновляем индексацию в массиве

            $duplicate = collect(); // ХЗ

            // Идем по массиву данных категорий (Первая строка это категория)
            foreach ($FIELDS as $FIELDS_KEY => $FIELD) {

                $FIELD = explode("\n", $FIELD);                                         // Делим на категории данных по переносу строки
                $FIELD = array_diff((array)$FIELD, array(0, null, "\r", "\n", "\r\n")); // Убрать пустые элементы массива (Мусор)
                // dd($FIELD);

                // Заголовки категорий данных (Отрезаем первую строку и цепляем к ней сепаратор)
                $FIELD_GROUP = trim($separator . '_' . preg_replace('/\t+/', '', array_shift($FIELD)));
                // dd($FIELD_GROUP);

                $FIELD = collect([$FIELD_GROUP => collect($FIELD)]);
                // dd($FIELD);

                foreach ($FIELD[$FIELD_GROUP] as $FIELD_KEY => $FIELD_ROW) {

                    // dd($FIELD_ROW);
                    // dd($FIELD[$FIELD_GROUP]);

                    $FIELD_ROW = trim($FIELD_ROW); // Чистим от мусора в начале и конце строки
                    // dd($FIELD_ROW);

                    // Проходим по фильтру поиска данных
                    foreach ($filters as $filter) {

                        // Найти то кол-во \t которое надо пропустить в строке для определения заголовка состоящего из 2-х слов и более
                        $count = mb_substr_count($filter, ' ');
                        $head = preg_replace('/\t+/', ' ', $FIELD_ROW, $count); // Получаем key для массива он же заголовок
                        // dd($head);

                        // Поиск фильтрующего слова в строке
                        if (str_contains($head, $filter . "\t") and str_starts_with($head, $filter)) {

                            $row_result = str_replace($filter . "\t", '', $FIELD_ROW);
                            $row_result = trim($row_result);
                            // dd($FIELD_ROW, $row_result);

                            // Если надо группировать по первому слову
                            $cutGroups = function ($row) use ($filter, $groups) {
                                if (in_array($filter, $groups)) {
                                    $group_result = explode("\t", $row, 2);
                                    $group_head = array_shift($group_result);

                                    return collect([$group_head => $group_result[0]]);
                                }

                                return $row;
                            };
                            // dd($row_result, $FIELD);

                            if ($FIELD->get($FIELD_GROUP)->has($filter)) {
                                // dd($row_result, $FIELD->get($FIELD_GROUP), $FIELD->get($FIELD_GROUP)->get($filter));

                                $row = $FIELD->get($FIELD_GROUP)->get($filter);
                                // dd($row, $row_result);

                                // Если результат СТРОКА то делаем из нее коллекцию
                                if (is_string($row)) {
                                    $old = $FIELD->get($FIELD_GROUP)->get($filter);
                                    $old = $cutGroups($old);
                                    $row = collect($old);

                                    $FIELD->get($FIELD_GROUP)->put($filter, $row);
                                }

                                // Если массив (Коллекция)
                                // if (is_array($row->all())) {
                                if ($row instanceof Collection) {
                                    if (in_array($filter, $groups)) {
                                        $row_result = $cutGroups($row_result);
                                        $FIELD->get($FIELD_GROUP)->get($filter)->put($row_result->keys()->first(), $row_result->first());
                                    } else {
                                        $FIELD->get($FIELD_GROUP)->get($filter)->push($row_result);
                                    }
                                }

                            } else {
                                // $row_result = $cutGroups($row_result); // ХЗ
                                $FIELD->get($FIELD_GROUP)->put($filter, $row_result);
                            }
                            // dd($row_result, $FIELD[$FIELD_GROUP]);

                            // $FIELD[$FIELD_GROUP]->forget($FIELD_KEY); // УДАЛИТЬ - обработанную строку если на найдена и обработана

                        } else {
                            // $FIELD[$FIELD_GROUP]->forget($FIELD_KEY); // УДАЛИТЬ - Строки которые не нашлись или пустые
                        }

                        // $FIELD[$FIELD_GROUP]->forget($FIELD_KEY); // УДАЛИТЬ - обрабатываемую строку

                    }

                }
                // dd($FIELD);

                // Создаем удобную коллекцию
                $FIELDS = collect($FIELDS);

                // Проверка IDENT в $FIELD_GROUP если группа уже создана
                if ($FIELDS->has($FIELD_GROUP)) {

                    // dd($FIELD_GROUP);

                    if ($FIELDS->get($FIELD_GROUP)->has('IDENT')) {
                        $old = $FIELDS->get($FIELD_GROUP); // Предыдущие данные группы
                        // dd($old);

                        $duplicate->put($old->get('IDENT'), $old);
                        // $duplicate->put($FIELD->get($FIELD_GROUP)->get('IDENT'), $FIELD->first());
                        $duplicate->put($FIELD->first()->get('IDENT'), $FIELD->first());
                        // dd($duplicate);

                        $FIELDS->put($FIELD_GROUP, $duplicate);

                    } else {
                        // $FIELDS->get($FIELD_GROUP)->put($FIELD->get($FIELD_GROUP)->get('IDENT'), $FIELD->first());
                        $FIELDS->get($FIELD_GROUP)->put($FIELD->first()->get('IDENT'), $FIELD->first());
                    }
                    // dd($duplicate);

                } else {
                    // Проверка IDENT в $FIELD_GROUP если группа еще не создана
                    // if ($FIELD->first()->has('IDENT')) {
                    //     $FIELD = collect([$FIELD->first()->get('IDENT') => $FIELD->first()]);
                    // } else {
                    //     $FIELD = $FIELD->first();
                    // }
                    // $FIELDS->put($FIELD_GROUP, $FIELD);

                    $FIELDS->put($FIELD_GROUP, $FIELD->first()); // СТАРОЕ РАБОТАЕТ

                }
                // dd($FIELD, $FIELD[$FIELD_GROUP]);

                // ФИНАЛ
                $OLD->push($FIELDS->get($FIELDS_KEY));
                $FIELDS->forget($FIELDS_KEY); // УДАЛИТЬ - обрабатываемую категорию полностью
            }
            // dd($FIELDS);

            // Сохранить старые данные
            // if ($config['old']) {
            //     $FIELDS->put('DATA_' . $MAIN->get('CLASS_'), $OLD);
            // }

            // ТЕСТ
            // $FIELDS->put('TEST_DATA', $FIELDS);

            $MAIN->put('FIELDS_', $FIELDS);
        }

        return $MAIN;
    }

}