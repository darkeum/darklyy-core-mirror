<?php

namespace Catalog\Controller;

use Illuminate\Bus\Batch;
use Boot\System\Http\Request;
use \Catalog_v2\Model\Catalog;
use \Boot\Support\Facades\Auth;
use \Catalog\Jobs\CatalogUnloadJob;
use \Illuminate\Support\Facades\Bus;
use \Catalog\Jobs\CatalogDownloadJob;
use \Boot\Controller\AbstractController;
use Boot\System\Office\OfficeSupport;
use Boot\System\Office\Spreadsheet\SpreadsheetSupport;

class CatalogExcelController extends AbstractController
{
    static function index()
    {

        display('@catalog/manager/excel/index.html.twig', [
            "categories" => \Boot\Support\Facades\Categories::getFullTree('catalog_v2'),
            "manufactures" => \Catalog_v2\Model\CatalogManufacture::all(),
            "fields" => static::get_fields(),
        ]);
    }

    //выгрузка
    static function unload(Request $req)
    {
        $uid = Auth::id();
        if (empty($uid)) {
            throw new \Exception("Uid is empty!");
        }

        $catalog = new \Catalog_v2\Model\Catalog;

        $request = $req->all();
        $fields = $request["fields"];

        //Фильтры
        $category_flag = isset($request["category"], $request["categories"]);
        $category_name = "";
        if ($category_flag) {
            $category = \Catalog_v2\Model\Categories::findOrFail($request["categories"]);
            $category_name = "Категория {$category->name} ";
            $catalog = $catalog->where("category", ",{$request["categories"]},");
        }
        if (isset($request["man"], $request["manufactures"]) || array_search("man_name", $fields)) {
            $catalog = $catalog->with("catalog_manufacture");
        }
        $man_name = "";
        if (isset($request["man"], $request["manufactures"])) {
            $man = \Catalog_v2\Model\CatalogManufacture::findOrFail($request["manufactures"]);
            $man_name = "Производитель {$man->name} ";
            $catalog = $catalog->where("man", $request["manufactures"]);
        }
        if (!isset($request["hidden"])) {
            $catalog = $catalog->where("hidden", 0);
        }
        if (array_search("count", $fields)) {
            $catalog = $catalog->with("stock_detail", function ($query) {
                $query->where('stock', 1);
            });
        }
        if (array_search("catalog_groups_id", $fields)) {
            $catalog = $catalog->with("catalog_groups");
        }

        //Сертификаты
        $catalog_certificate = [];
        if (array_search("certificate", $fields)) {
            $catalog = $catalog->with("catalog_cert.cert");
            $catalog_certificate = \Catalog\Models\CertType::all();
        }

        //Атрибуты
        $catalog_attribute = [];
        if (array_search("attribute", $fields) && $category_flag) {
            $catalog_attribute = \Catalog\Model\CatalogAttribute::whereHas("attribute_category", function ($query) use ($request) {
                $query->where("category_id", $request["categories"]);
            })->get();
        }

        $path_files = ROOT . "usr/modules/catalog/excel_files/unload/";
        $u = uniqid();
        $date = \Carbon\Carbon::now()->format("d-m-Y H-i");

        $files = [];
        $jobs = [];
        $total = $catalog->count();
        $limit = 100;
        $filename_source = "{$category_name}{$man_name}{$date} {$u}_{$uid}";
        $filename_zip = "{$filename_source}.zip";
        $num = 0;
        for ($i = 0; $i < ceil($total / $limit); $i++) {
            if (($i * $limit) % 10000 == 0) {
                $num++;
                $filename_xlsx = "{$filename_source}_{$num}.xlsx";
                $file = $path_files . $filename_xlsx;
                $files[] = $file;
            }

            $jobs[] = new CatalogUnloadJob(\AnourValar\EloquentSerialize\Facades\EloquentSerializeFacade::serialize($catalog), $i * $limit, $limit, $file, $fields, $catalog_attribute, $catalog_certificate);
        }

        $batch = Bus::batch($jobs)
            ->finally(function (Batch $batch) use ($uid, $files, $path_files, $filename_zip) {
                //Создание архива
                $zip = new \ZipArchive();
                $filename = $path_files . $filename_zip;

                if ($zip->open($filename, \ZipArchive::CREATE) !== TRUE) {
                    exit("Невозможно открыть <$filename>\n");
                }

                foreach ($files as $file) {
                    $pathinfo = pathinfo($file);
                    $zip->addFile($file, $pathinfo["basename"]);
                }
                $zip->close();
                foreach ($files as $file) {
                    unlink($file);
                }

                //Отправка уведомления
                $attr = [];
                $attr['uid'] = $uid;
                $attr['icon'] = config('app.url') . '/files/push/comment.png';
                $attr['url'] = config('app.url') . '/administration/module/catalog/excel';
                $attr['pNotif'] = true;
                $attr['pPush'] = true;
                $attr['module'] = "catalog";
                $attr['pid'] = 0;
                $attr['title'] = 'Файл выгрузки сформирован';
                $attr['text'] = 'Файл выгрузки сформирован';
                sendAllNotif($attr);
            })
            ->onQueue('catalog')
            ->name($filename_zip)
            ->dispatch();
        return response(Bus::findBatch($batch->id));
    }

    //загрузка
    static function download(Request $request)
    {
        $uid = Auth::id();
        if (empty($uid)) {
            throw new \Exception("Uid is empty!");
        }

        $file = $request->file("file");
        $hashName = $file->hashName();
        $directory = ROOT . "usr/modules/catalog/excel_files/download_temp/";
        $new_file = $directory . $hashName;
        $file->move($directory, $hashName);

        $excel = new SpreadsheetSupport();
        $excel->init_file(["tmp_name" => $new_file]);
        $document = $excel->get_excel();

        $array_name = [];
        $array_name_original = [];
        for ($i = 1; $i <= $excel->get_count_column(); $i++) {
            $arrayRes = [];
            $str =  $document->getActiveSheet()->getCellByColumnAndRow($i, 1)->getValue();
            preg_match('/[{]%.+%[}]/ui', $str, $arrayRes);
            if (empty($arrayRes[0])) {
                $array_name[$i] = null;
            } else {
                $array_name[$i] = str_replace('%}', '', str_replace('{%', '', $arrayRes[0]));
            }
            $array_name_original[$i] = $str;
        }

        array_unshift($array_name_original, "Статус");
        $main_patch = ROOT . "usr/modules/catalog/excel_files/download/";
        $finished_filename = \Carbon\Carbon::now()->format("d-m-Y H-s") . " " . uniqid() . "_" . $uid . ".xlsx";
        $finished_filename_temp = \Carbon\Carbon::now()->format("d-m-Y H-s") . " " . uniqid() . "_" . $uid . "_temp";
        $finished_file_temp = SpreadsheetSupport::data_to_excel_file(
            [
                "head" => $array_name_original,
                "data" => [],
                "filename" => $main_patch . $finished_filename_temp,
            ]
        );
        $finished_file = $main_patch . $finished_filename;
        $jobs = [];
        for ($i = 2; $i <= $excel->get_count_row(); $i++) {
            $jobs[] = new CatalogDownloadJob($new_file, $finished_file_temp, $uid, $array_name, $i);
        }
        $batch = Bus::batch($jobs)
            ->finally(function (Batch $batch) use ($new_file, $uid, $finished_file_temp, $finished_file) {
                unlink($new_file);

                rename($finished_file_temp, $finished_file);
                //Отправка уведомления
                $attr = [];
                $attr['uid'] = $uid;
                $attr['icon'] = config('app.url') . '/files/push/comment.png';
                $attr['url'] = config('app.url') . '/administration/module/catalog/excel';
                $attr['pNotif'] = true;
                $attr['pPush'] = true;
                $attr['module'] = "catalog";
                $attr['pid'] = 0;
                $attr['title'] = 'Файл загрузки сформирован';
                $attr['text'] = 'Файл загрузки сформирован';
                sendAllNotif($attr);
            })
            ->onQueue('catalog')
            ->name($finished_filename)
            ->dispatch();
        return response(Bus::findBatch($batch->id));
    }

    static function get_file(Request $request)
    {
        $file = $request->get("file");
        OfficeSupport::get_file(ROOT . $file);
    }

    static function files(Request $request)
    {
        $type = $request->get("type") ?? "unload";
        $uid = Auth::id();
        $files = [];
        $path = "usr/modules/catalog/excel_files/{$type}";
        $ext = match ($type) {
            "unload" => "zip",
            "download" => "xlsx",
        };
        foreach (glob(ROOT . "{$path}/*_{$uid}.{$ext}") as $filename) {
            $pathinfo = pathinfo($filename);
            $files[] = [
                "download" => "/$path/{$pathinfo["basename"]}",
                "path" => $filename,
                "pathinfo" => $pathinfo,
            ];
        }
        return response($files);
    }

    static function batch_list(Request $request)
    {
        $list = $request->get("batchId");
        $data = [];
        foreach ($list as $batchId) {
            $data[] = Bus::findBatch($batchId);
        }
        return response($data);
    }

    static function get_count(Request $request)
    {
        $file = $request->file("file");
        $excel = new SpreadsheetSupport;
        $excel->init_file(["tmp_name" => $file->getPathname()]);
        return response(["count" => $excel->get_count_row() - 1]);
    }

    static function cancel_batch(Request $request)
    {
        $batchId = $request->get("batchId");
        $bus = Bus::findBatch($batchId);
        $bus->cancel();
        return response(Bus::findBatch($batchId));
    }

    static function get_fields()
    {
        return [
            "id" =>                 ["field" => "id", "rus" => Catalog::translate_field("id"), "checked" => true, "disabled" => true,],
            "sku" =>                ["field" => "sku", "rus" => Catalog::translate_field("sku"), "checked" => true, "disabled" => false,],
            "sku2" =>               ["field" => "sku2", "rus" => Catalog::translate_field("sku2"), "checked" => true, "disabled" => false,],
            "sku3" =>               ["field" => "sku3", "rus" => Catalog::translate_field("sku3"), "checked" => true, "disabled" => false,],
            "sku_UdmurtSnab" =>     ["field" => "sku_UdmurtSnab", "rus" => Catalog::translate_field("sku_UdmurtSnab"), "checked" => true, "disabled" => false,],
            "unit" =>               ["field" => "unit", "rus" => Catalog::translate_field("unit"), "checked" => true, "disabled" => false,],
            "title" =>              ["field" => "title", "rus" => Catalog::translate_field("title"), "checked" => true, "disabled" => false,],
            "short" =>              ["field" => "short", "rus" => Catalog::translate_field("short"), "checked" => true, "disabled" => false,],
            "description" =>        ["field" => "description", "rus" => Catalog::translate_field("description"), "checked" => true, "disabled" => false,],
            "fulltitle" =>          ["field" => "fulltitle", "rus" => Catalog::translate_field("fulltitle"), "checked" => true, "disabled" => false,],
            "category" =>           ["field" => "category", "rus" => Catalog::translate_field("category"), "checked" => true, "disabled" => false,],
            "preview" =>            ["field" => "preview", "rus" => Catalog::translate_field("preview"), "checked" => true, "disabled" => false,],
            "man" =>                ["field" => "man", "rus" => Catalog::translate_field("man"), "checked" => true, "disabled" => false,],
            "man_name" =>           ["field" => "", "rus" => "наименование производителя", "load" => true, "checked" => true, "disabled" => false,],
            "active" =>             ["field" => "active", "rus" => Catalog::translate_field("active"), "checked" => true, "disabled" => false,],
            "active_guest" =>       ["field" => "active_guest", "rus" => Catalog::translate_field("active_guest"), "checked" => true, "disabled" => false,],
            "active_user" =>        ["field" => "active_user", "rus" => Catalog::translate_field("active_user"), "checked" => true, "disabled" => false,],
            "types" =>              ["field" => "types", "rus" => Catalog::translate_field("types"), "checked" => true, "disabled" => false,],
            "hidden" =>             ["field" => "hidden", "rus" => Catalog::translate_field("hidden"), "checked" => true, "disabled" => false,],
            "catalog_groups_id" =>  ["field" => "catalog_groups_id", "rus" => Catalog::translate_field("catalog_groups_id"), "checked" => true, "disabled" => false,],
            "date_delivery" =>      ["field" => "date_delivery", "rus" => Catalog::translate_field("date_delivery"), "checked" => true, "disabled" => false,],
            "purchase" =>           ["field" => "", "rus" => "закупочная цена", "load" => true, "checked" => true, "disabled" => false,],
            "price" =>              ["field" => "", "rus" => "прайс-лист", "load" => true, "checked" => true, "disabled" => false,],
            "count" =>              ["field" => "", "rus" => "количество", "load" => true, "checked" => true, "disabled" => false,],
            "spec" =>               ["field" => "spec", "rus" => "наименование; модель / марка; дополнительный производитель", "checked" => true, "disabled" => false,],
            "weight" =>             ["field" => "weight", "rus" => "Вес НЕТТО; Вес БРУТТО; Длина; Ширина; Высота; Объем; Длина упаковки; Ширина упаковки; Высота упаковки; Объем упаковки; Количество в упаковке", "checked" => true, "disabled" => false,],
            "certificate" =>        ["field" => "certificate", "rus" => "Сертификаты", "checked" => false, "disabled" => false,],
            "attribute" =>          ["field" => "attribute", "rus" => "Атрибуты", "checked" => false, "disabled" => true,],

        ];
    }
}
