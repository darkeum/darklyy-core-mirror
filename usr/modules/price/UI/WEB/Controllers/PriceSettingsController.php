<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\WEB\Controllers;

use Boot\Support\Carbon;
use Catalog\Models\Catalog;
use Boot\System\Http\Request;
use Price\Jobs\PriceRivalJob;
use Price\UI\CLI\PriceDaemon;
use Price\Models\CatalogPrice;
use Price\Parser\AbstractParser;
use Price\Models\CatalogPriceRival;
use Price\Models\CatalogPriceDetail;
use Price\UI\CLI\CatalogPriceDaemon;
use Boot\Controller\AbstractController;
use Price\Models\CatalogPriceRivalSite;
use Price\Models\CatalogPriceRivalDiscount;
use Boot\System\Office\Spreadsheet\SpreadsheetSupport;

class PriceSettingsController extends AbstractController
{
    static function index()
    {
        return view('price::manager.settings.index');
    }

    static function edit($id)
    {
        $c = CatalogPriceRival::find($id);
        if (!empty($c)) {
            $c = $c->toArray();
        }
        $type = !empty($c["type"]) ? $c["type"] : 1;
        $disable_btn = static::disable_btn($c);
        $settings = (object) [
            'id' => $id,
            'type' => $type,
            'disable_btn' => $disable_btn,
            'catalog' => Catalog::findOrFail($id),
            'rival' => CatalogPriceRival::find($id),
            'type_list' => CatalogPriceRival::getType(),
            'optimal_margin_data' => self::data_generate($type, [2, 3]),
            'our_supplier_discount_data' => self::data_generate($type, [1, 2, 4, 6]),
            'price_adjustments_data' => self::data_generate($type, [1, 2, 3, 4, 5, 6]),
            'price_adjustments_price_data' => self::data_generate($type, [2, 3]),
            'discount_installers_mrc_data' => self::data_generate($type, [1]),
            'rival_link_data' => self::data_generate($type, [2, 3, 4, 5]),
            'competing_links_data' => self::data_generate($type, [4, 5, 6]),
            'correction_factor_price_data' => self::data_generate($type, [2, 3, 4, 5]),
        ];

        return view('price::manager.settings.edit', ['settings' => $settings]);
    }

    static function disable_btn($c)
    {
        $disable_text = empty($c["disable"]) ? __('price::settings.catalog.btn.off') : __('price::settings.catalog.btn.on');
        $disable_title = empty($c["disable"]) ? __('price::settings.catalog.btn.off.text') : __('price::settings.catalog.btn.on.text');
        $disable_value = empty($c["disable"]) ? 1 : 0;
        $disable_class = empty($c["disable"]) ? "btn-outline-warning" : "btn-warning";
        return  '<button type="button" class="btn ' . $disable_class . ' btn-xs" title="' . $disable_title . '" id="disable_rival" data-value="' . $disable_value . '">' . $disable_text . '</button>';
    }

    static function data_generate($type, $list)
    {
        $data = (object) [];
        $data->div = in_array($type, $list) ? "" : "style='display: none;'";
        $data->dis = in_array($type, $list) ? "" : "disabled";
        $view_result = ["view_type"];
        $dis_result = ["dis_type"];
        foreach ($list as $i) {
            $view_result[] = "view_{$i}";
            $dis_result[] = "dis_{$i}";
        }
        $data->class = implode(" ", $view_result);
        $data->dis_class = implode(" ", $dis_result);

        return $data;
    }

    static function account()
    {
        $parsers = AbstractParser::get_class_for_link(["all" => true]);
        $list = [];
        foreach ($parsers as $key => $item) {
            $pa = $item->check_personal_area();
            $search_field = "";
            $search_field_translate = "";
            $field = $item->get_search_field();
            if ($field) {
                $search_field = $field;
                $search_field_translate = Catalog::translate_field($field);
            }
            $class_name = get_class($item);
            $site = CatalogPriceRivalSite::where("class_site", $class_name)->first();
            $login = $site->login ?? null;
            $password = $site->password ?? null;
            $list[] = [
                'pa' => $pa,
                'key' => $key,
                'search_field' => $search_field,
                'search_field_translate' => $search_field_translate,
                'password' => $password,
                'login' => $login,
                'class_name' => $class_name,
            ];
        }
        return view('price::manager.settings.account', [
            'list' => $list
        ]);
    }

    static function password(Request $request)
    {
        $item = CatalogPriceRivalSite::firstOrNew(["class_site" => $request->pk]);
        $item->password = $request->value;
        $item->save();
        return $item;
    }

    static function login(Request $request)
    {
        $item = CatalogPriceRivalSite::firstOrNew(["class_site" => $request->pk]);
        $item->login = $request->value;
        $item->save();
        return $item;
    }

    static function template()
    {
        $file = module_path('Resources' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'settings.xlsx');
        if (file_exists($file)) {
            return response()->download($file);
        } else {
            abort('404');
        }
    }

    static function load()
    {
        return view('price::manager.settings.load');
    }

    static function save()
    {
        return CatalogPriceRival::action($_POST);
    }

    static function update(Request $request)
    {
        $id = $request->id;
        $field = $request->field;
        $CatalogPriceRival = CatalogPriceRival::find($id);
        if (!empty($CatalogPriceRival)) {
            switch ($field) {
                default:
                    $CatalogPriceRival->{$field} = $request["value"];
                    break;
                case "disable":
                    if (empty($request["value"])) {
                        $CatalogPriceRival->{$field} = null;
                        if ($CatalogPriceRival->type == 1) {
                            $CatalogPriceRival->daemon = 0;
                        }
                    } else {
                        $CatalogPriceRival->{$field} = Carbon::now();
                        $CatalogPriceRival->iteration = null;
                        $CatalogPriceRival->iteration_count = 0;
                        $CatalogPrice = CatalogPrice::create([
                            "price_type" => 3,
                            "comment" => __('price::settings.catalog.price.comment', ['date' => Carbon::now()->format('d.m.Y')]),
                            "active" => 1,
                            "uid" => 79,
                            "auto" => 1,
                        ]);
                        CatalogPriceDetail::create([
                            "price_id" => $CatalogPrice->id,
                            "cid" => $id,
                            "sum" => 0,
                        ]);
                        CatalogPriceDaemon::key_create();
                    }
                    break;
            }

            $CatalogPriceRival->save();
            if ($CatalogPriceRival->type == 1 && empty($request["value"])) {
                PriceRivalJob::dispatch();
            }
        }
    }

    static function delete(Request $request)
    {
        return CatalogPriceRival::destroy($request->id);
    }

    static function get_disable_btn($id)
    {
        $result = [];
        $c = CatalogPriceRival::find($id);
        if (!empty($c)) {
            $c = $c->toArray();
        }
        $result["template"] = static::disable_btn($c);
        return $result;
    }

    static function count(Request $request)
    {
        $spreadsheet = new SpreadsheetSupport;
        $spreadsheet->init_file($request->file);
        return json_encode(["count" => $spreadsheet->get_count_row() - 1], JSON_UNESCAPED_UNICODE);
    }

    static function excel(Request $request)
    {
        $result = [
            "countRow" => 0,
            "last" => 0,
            "items" => [],
        ];

        $spreadsheet = new SpreadsheetSupport;
        $spreadsheet->init_file($request->file);
        $result["countRow"] = $spreadsheet->get_count_row();
        $number = intval($request->number);
        $countLast = $number + 10;
        if ($countLast >= $spreadsheet->get_count_row()) {
            $countLast = $spreadsheet->get_count_row();
        }

        $excel = $spreadsheet->get_excel();
        $array_name = [];
        for ($i = 0; $i <= $spreadsheet->get_count_column(); $i++) {
            $arrayRes = [];
            $str =  $excel->getActiveSheet()->getCellByColumnAndRow($i, 1)->getValue();
            preg_match('/[{]%.+%[}]/ui', $str, $arrayRes);
            if (empty($arrayRes[0])) {
                $array_name[$i] = null;
            } else {
                $array_name[$i] = str_replace('%}', '', str_replace('{%', '', $arrayRes[0]));
            }
        }

        for ($i = $number; $i <= $countLast; $i++) {
            $data = [];
            $id = null;
            for ($j = 0; $j <= $spreadsheet->get_count_column(); $j++) {
                $val = $excel->getActiveSheet()->getCellByColumnAndRow($j, $i)->getFormattedValue();
                if (empty($array_name[$j])) {
                    continue;
                }
                if (is_null($id) && $array_name[$j] == "id") $id = $val;
                if ($array_name[$j] != "rival_link" && $array_name[$j] != "competing_links") {
                    $val = str_replace(",", ".", $val);
                }
                $val = empty($val) ? ($val == 0 ? 0 : null) : $val;
                $data[$array_name[$j]] = $val;
            }
            CatalogPriceRival::action($data, false);
            $result["items"][] = [
                "id" => $id,
                "number" => $number,
            ];
            $number++;
        }
        app()->make(\Price\UI\CLI\Commands\PriceWorkCommand::class)->handle();
        $result["last"] = $number;

        return $result;
    }

    static function check($id)
    {
        $result = [
            "error" => false,
            "data" => [],
        ];
        $PriceDaemon = new PriceDaemon;
        $PriceDaemon->disable = false;
        $query = $PriceDaemon->get_query();
        $item = $query->where("id", $id)->first();
        // dd($item);

        if (!empty($item)) {
            $PriceDaemonTypeName = $PriceDaemon->get_class_name($item);

            if (class_exists($PriceDaemonTypeName)) {
                $PriceDaemonType = new $PriceDaemonTypeName();
                $PriceDaemonType->cpr = $item;
                $PriceGenerateName = $PriceDaemonType->get_class_name();

                try {
                    $PriceDaemonType->parse();
                    $PriceGenerate = $PriceDaemonType->price_generate($PriceGenerateName);
                    $result["data"] = $PriceGenerate->calculate_view_data();

                } catch (\Exception | \GuzzleHttp\Exception\ClientException $error) {

                    switch (get_class($error)) {
                        default:
                            $result["error"] = $error->getMessage();
                            dd($error->getMessage());
                            break;
                        case \GuzzleHttp\Exception\ClientException::class:
                            $result["error"] = $error->getMessage();
                            break;
                    }
                }
            } else {
                $result["error"] = __('price::settings.catalog.error.type');
            }
        } else {
            $result["error"] = __('price::settings.catalog.error.empty');
        }

        return $result;
    }

    static function discount()
    {
        return view('price::manager.settings.discount.index');
    }

    static function discount_edit($id)
    {
        $catalog = Catalog::find($id);
        $items = CatalogPriceRivalDiscount::where('cid', $id)->get();
        $options = AbstractParser::get_class_for_link(["all" => true]);

        $discount = (object) [
            'id' => $id,
            'items' => $items,
            'options' => $options,
            'catalog' => $catalog,
        ];

        return view('price::manager.settings.discount.edit', ['discount' => $discount]);
    }

    static function discount_item_add()
    {
        return CatalogPriceRivalDiscount::action($_POST);
    }

    static function discount_item_body($id)
    {
        $items = CatalogPriceRivalDiscount::where('cid', $id)->get();
        $options = AbstractParser::get_class_for_link(["all" => true]);

        $discount = (object) [
            'id' => $id,
            'items' => $items,
            'options' => $options,
        ];

        return view('price::manager.settings.discount.body', ['discount' => $discount]);
    }

    static function discount_item_delete($id)
    {
        return CatalogPriceRivalDiscount::destroy($id);
    }

    static function discount_item_update($id)
    {
        return  CatalogPriceRivalDiscount::findOrFail($id)->update($_POST);
    }

    static function discount_template()
    {
        $file = module_path('Resources' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'discount.xlsx');
        if (file_exists($file)) {
            return response()->download($file);
        } else {
            abort('404');
        }
    }

    static function discount_load()
    {
        return view('price::manager.settings.discount.load');
    }

    static function discount_count(Request $request)
    {
        return static::count($request);
    }

    public function discount_excel(Request $request)
    {
        $spreadsheet = new SpreadsheetSupport;
        $spreadsheet->init_file($request->file);
        $count = $spreadsheet->get_count_row();
        $spreadsheet->field_name();
        $items = $spreadsheet->parse_data();
        foreach ($items as $item) {
            CatalogPriceRivalDiscount::action($item);
        }
        return [
            "countRow" =>  $count,
            "last" =>  $count,
            "items" => []
        ];
    }
}
