<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Requests\UI\WEB\Controllers;

use Boiler\Model\BoilerBid;
use Boot\System\Http\Request;
use Boot\Controller\AbstractController;
use Boot\Models\Attach;

use Request\Model\Request as ModelRequest;
use Requests\Models\Requests;

use Boot\Support\Facades\Auth;
use Catalog\Models\Catalog;
use Catalog_v2\Model\CatalogManufacture;
use Object\Model;
use Objects\Models\Objects;
use Organizations\Model\Organizations;
use Illuminate\Support\Facades\Storage;

class RequestsController extends AbstractController
{
    static function index()
    {
        return view('requests::manager.index');
    }

    public static function show($id)
    {
        $unit = Requests::findOrFail($id);
        $attachments = Attach::where("pid", '=', $id)->where("module", '=', 'request_catalog')->get();
        $types = config('requests-config.types.text');

        switch ($unit->type) {
            //case 1:
            //    break;
            //
            //case 2:
            //    break;
            //
            //case 3:
            //    break;
            //
            //case 4:
            //    break;

            case 5:
                //$request_data = json_decode($unit->request_data, true);
                $request_data = collect($unit->request_data);
                //dd($request_data);

                $manafacture = CatalogManufacture::find($request_data['manufacture']);
                $catalog = Catalog::find($request_data['cid']);

                if (isset($request_data['object_id']) && $request_data['object_id'] != null) {
                    $obj = Object::find($request_data['object_id']);
                } else {
                    $obj = "Отсутствует";
                }

                $org = Organizations::find($request_data['oid']);

                break;

            case 6:
                $request_data = collect($unit->result);

                //$data_result = $request_data[0]['series'][array_keys($request_data[0]['series'])[0]];
                $series = collect($request_data->first()['series']);
                $data_result = collect($series[$series->keys()->first()]);
                // if (isset($unit->result[0]['series']['wp']))
                //     $data_result = $unit->result[0]['series']['wp'];
                // elseif (isset($unit->result[0]['series']['wpb']))
                //     $data_result = $unit->result[0]['series']['wpb'];
                // elseif (isset($unit->result[0]['series']['wpbs']))
                //     $data_result = $unit->result[0]['series']['wpbs'];
                // else
                //     $data_result = json_decode($unit->result,true)[0]['series']['wp'];

                #ЭЛЕКТРИКА
                $electric = collect();
                foreach ($data_result['electric'] as $item) {

                    if ($item['tm_cid'] == null) {
                        continue;
                    }

                    $price = 0;
                    if (isset($data_result['electric_price']['priceArray'][$item['tm_cid']])) {
                        $price = $data_result['electric_price']['priceArray'][$item['tm_cid']];
                    }

                    $electric[] = array(
                        'tm_cid' => $item['tm_cid'],
                        'tm_count' => $item['tm_count'],
                        'tm_sku' => $item['tm_sku'],
                        'tm_title' => $item['tm_title'],
                        'tm_price' => $price,
                        'total_price' => (float)$item['tm_count'] * (float)$price,
                    );
                }

                #СТАНЦИЯ
                $station = collect();
                foreach ($data_result['station'] as $item) {

                    if ($item['tm_cid'] == null) {
                        continue;
                    }

                    $price = 0;
                    if (isset($data_result['station_price']['priceArray'][$item['tm_cid']])) {
                        $price = $data_result['station_price']['priceArray'][$item['tm_cid']];
                    }

                    $station[] = array(
                        'tm_cid' => $item['tm_cid'],
                        'tm_count' => $item['tm_count'],
                        'tm_sku' => $item['tm_sku'],
                        'tm_title' => $item['tm_title'],
                        'tm_price' => $price,
                        'total_price' => (float)$item['tm_count'] * (float)$price,
                    );
                }

                break;

            default:

                break;
        }
        return view('requests::manager.show', [
            'unit' => $unit,
            'types' => collect($types),
            'attachments' => $attachments,

            'request_data' => $request_data ?? false,

            'obj' => $obj ?? false,
            'org' => $org ?? false,

            'manafacture' => $manafacture ?? false,
            'catalog' => $catalog ?? false,

            'electric' => $electric ?? false,
            'station' => $station ?? false,
        ]);
    }

    public static function create()
    {
        $types = config('requests-config.types.text');

        return view('requests::manager.create', [
            'types' => collect($types),
        ]);
    }

    public static function store(Request $data)
    {
        $model = new ModelRequest();
        $arr = array(
            'title' => $data['title'],
            'request' => $data['request'],
            'object' => $data['object_id'],
            'type' => $data['type'],
            'uid_dir' => Auth::id(),
            'date' => date("Y-m-d H:i:s")
        );

        $model->fill($arr);
        $model->save();

        global $designers;
        $attr = [];
        $attr['uid'] = $designers;
        $attr['title'] = 'Новая заявка на расчет!';
        $attr['text'] = "Новая заявка на расчет";
        $attr['icon'] = config('app.url') . '/files/push/tasks_ok.png';
        $attr['url'] = config('app.url') . '/administration/module/requests/' . $model->id;
        $attr['pNotif'] = true;
        $attr['pPush'] = true;
        $attr['module'] = "request";
        $attr['pid'] = $model->id;
        sendAllNotif($attr);

        if ($data["files"]) {

            $path_folder = "files/request/" . $model->id . "/";

            foreach ($data["files"] as $file) {
                $filename = $file->getClientOriginalName();
                Storage::disk('local')->putFileAs(
                    $path_folder,
                    $file,
                    $filename
                );

                $info = [];

                $info["name"] = $filename;
                $info["url"] = $path_folder . $filename;
                $info["pid"] = $model->id;
                $info["size"] = storage_size($info["url"]);
                $info["module"] = "request";

                $attach = Attach::create($info);
            }
        }

        return redirect()->route('admin.requests.index');
    }

    public static function edit($id)
    {
        $unit = Requests::find($id);
        $object = Objects::find($unit->object);
        $types = config('requests-config.types.text');

        return view('requests::manager.create', [
            'unit' => $unit,
            'types' => collect($types),
            'object' => $object,
        ]);
    }

    public static function update(Request $data)
    {
        $unit = ModelRequest::find($data['id']);


        $arr = array(
            'title' => $data['title'],
            'request' => $data['request'],
            'object' => $data['object_id'],
            'type' => $data['type'],
            'uid_dir' => Auth::id(),
            'date' => date("Y-m-d H:i:s")
        );

        $unit->update($arr);
        $unit->save();

        if ($data["files"]) {

            $path_folder = "files/request/" . $unit->id . "/";

            foreach ($data["files"] as $file) {
                $filename = $file->getClientOriginalName();
                Storage::disk('local')->putFileAs(
                    $path_folder,
                    $file,
                    $filename
                );

                $info = [];

                $info["name"] = $filename;
                $info["url"] = $path_folder . $filename;
                $info["pid"] = $unit->id;
                $info["module"] = "request";

                $attach = Attach::create($info);
            }
        }

        return redirect()->back();
    }

    public static function destroy($id)
    {
        $unit = Requests::find($id);

        //Удаление файлов
        $files = $unit->attach;

        foreach ($files as $file) {
            Storage::disk("local")->delete($file['url']);
            $file->delete();
        }


        $unit->delete();

        return redirect()->route('admin.requests.index');
    }

    public static function print($id)
    {
        $unit = Requests::findOrFail($id);
        $attachments = Attach::where("pid", '=', $id)->where("module", '=', 'request_catalog')->get();
        $types = config('requests-config.types');

        switch ($unit->type) {
            //case 1:
            //    break;
            //
            //case 2:
            //    break;
            //
            //case 3:
            //    break;
            //
            //case 4:
            //    break;

            case 5:
                $request_data = json_decode($unit->request_data, true);

                $manafacture = CatalogManufacture::find($request_data['manufacture']);
                $catalog = Catalog::find($request_data['cid']);

                if ($request_data['object_id'] != null) {
                    $obj = Object::find($request_data['object_id']);
                } else {
                    $obj = "Отсутствует";
                }

                $org = Organizations::find($request_data['oid']);

                break;

            case 6:
                $request_data = $unit->result;

                $data_result = $request_data[0]['series'][array_keys($request_data[0]['series'])[0]];

                // if (isset($unit->result[0]['series']['wp']))
                //     $data_result = $unit->result[0]['series']['wp'];
                // elseif (isset($unit->result[0]['series']['wpb']))
                //     $data_result = $unit->result[0]['series']['wpb'];
                // elseif (isset($unit->result[0]['series']['wpbs']))
                //     $data_result = $unit->result[0]['series']['wpbs'];
                // else
                //     $data_result = json_decode($unit->result,true)[0]['series']['wp'];

                $station = [];
                foreach ($data_result['station'] as $item) {

                    if ($item['tm_cid'] == null) {
                        continue;
                    }

                    $price = 0;
                    if (isset($data_result['station_price']['priceArray'][$item['tm_cid']])) {
                        $price = $data_result['station_price']['priceArray'][$item['tm_cid']];
                    }

                    $station[] = array(
                        'tm_cid' => $item['tm_cid'],
                        'tm_count' => $item['tm_count'],
                        'tm_sku' => $item['tm_sku'],
                        'tm_title' => $item['tm_title'],
                        'tm_price' => $price,
                        'total_price' => (int)$item['tm_count'] * (int)$price,
                    );
                }

                $electric = [];
                foreach ($data_result['electric'] as $item) {

                    if ($item['tm_cid'] == null) {
                        continue;
                    }

                    $price = 0;
                    if (isset($data_result['electric_price']['priceArray'][$item['tm_cid']])) {
                        $price = $data_result['electric_price']['priceArray'][$item['tm_cid']];
                    }

                    $electric[] = array(
                        'tm_cid' => $item['tm_cid'],
                        'tm_count' => $item['tm_count'],
                        'tm_sku' => $item['tm_sku'],
                        'tm_title' => $item['tm_title'],
                        'tm_price' => $price,
                        'total_price' => (int)$item['tm_count'] * (int)$price,
                    );
                }

                break;

            default:

                break;
        }

        //dd($unit);

        return view('requests::manager.print', [
            'unit' => $unit,
            'types' => collect($types),
            'attachments' => $attachments,

            'request_data' => $request_data ?? false,

            'obj' => $obj ?? false,
            'org' => $org->name ?? false,

            'manafacture' => $manafacture ?? false,
            'catalog' => $catalog ?? false,

            'electric' => $electric ?? false,
            'station' => $station ?? false,
        ]);
    }

    public function getObj(Request $request)
    {
        $res = [];
        $search = $request->q;
        $res = Objects::select("id", "name")
            ->where('name', 'LIKE', "%$search%")
            ->orWhere('id', 'LIKE', "%$search%")
            ->get();

        return response()->json($res);
    }

    public function getBoiler(Request $request)
    {
        $res = [];
        $search = $request->q;
        $res = BoilerBid::select("id", "title", 'numb', 'date_create')
            ->where('title', 'LIKE', "%$search%")
            ->orWhere('id', '=', $search)
            ->orWhere('numb', 'LIKE', "%$search%")
            ->orderByDesc('date_create')
            ->get();

        return response()->json($res);
    }
}
