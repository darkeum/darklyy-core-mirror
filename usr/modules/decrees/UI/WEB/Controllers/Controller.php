<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirll
*/

namespace Decrees\UI\WEB\Controllers;

use Decrees\Models\Decrees;
use Decrees\Tasks\TypesTask;
use Organizations\Model\Org;
use Boot\System\Http\Request;
use Boot\Controller\AbstractController;
use Boot\Models\Attach;
use Boot\Models\UsersOrg;
use Boot\Support\Carbon;
use Boot\Support\Facades\Auth;
use Boot\Models\Users;
use Boot\Support\Facades\Storage;
use Boot\System\Office\Word\TemplateProcessor;
use Orgs\Models\Org as ModelsOrg;
use Boot\System\Office\OfficeSupport;

class Controller extends AbstractController
{
    static public function index()
    {
        return view('decrees::manager.index');
    }

    static public function show($id)
    {
        $unit = Decrees::findOrFail($id);
        return view('decrees::manager.show', [
            'unit' => $unit,
            'type' => app(TypesTask::class)->run($unit->type, "default"),
            'type_icon' => app(TypesTask::class)->run($unit->type, 'icon'),
        ]);
    }

    static public function create()
    {
        return view('decrees::manager.create', [
            'org' => Org::with('organization')->get(),
            'types' => app(TypesTask::class)->run(),
            'workers' => Users::all()->where('active', '=', '1'),
            'uid' => Auth::id(),
        ]);
    }

    static public function store(Request $request)
    {

        $array = $request->request->all();
        $array['uid'] = Auth::id();

        // командировка старт
        if ($request['citys'] ?? false) {
            foreach ($request['citys'] as $key => $item) {
                $array['data']['destination'][$key] =
                    [
                        'city' => $item,
                        'org' => $request['orgs'][$key] ?? null,
                        'short_report' => $request['reports'][$key] ?? null,
                    ];
            }
        }

        $array['data']['post'] = $request['post'] ?? null;
        $array['data']['target'] = $request['target'] ?? null;
        $array['data']['date_transit'] = $request['days'] ?? null;
        // командировка конец
        $decree = Decrees::create($array);

        // numb create start
        static::numb_create($decree->id);
        // numb create end

        //files start
        if ($request->file('file')) {

            $path = "files/decrees/" . $decree->id . "/";
            Storage::disk('local')->putFileAs($path, $request->file('file'), $request->file('file')->getClientOriginalName());
            $attach = Attach::create([
                "name" => $request->file('file')->getClientOriginalName(),
                "url" => $path . $request->file('file')->getClientOriginalName(),
                "pid" => $decree->id,
                "module" => 'decrees',
            ]);
            $array['file_id'] = $attach->id;
        }
        if ($request->file('scan')) {
            $path = "files/decrees/" . $decree->id . "/";
            Storage::disk('local')->putFileAs($path, $request->file('scan'), $request->file('scan')->getClientOriginalName());
            $attach = Attach::create([
                "name" => $request->file('scan')->getClientOriginalName(),
                "url" => $path . $request->file('scan')->getClientOriginalName(),
                "pid" => $decree->id,
                "module" => 'decrees',
            ]);
            $array['scan_id'] = $attach->id;
        }
        // files end

        $decree->update($array);
        // numb create start
        static::numb_create($decree->id);
        // numb create end
        return redirect()->route('admin.decrees.show', $decree->id)->with(['message' => 'Приказ создан']);
    }

    static public function destroy($id)
    {
        $decree = Decrees::find($id);
        $attach_file = $decree->file_id != null ? Attach::find($decree->file_id) : null;
        $attach_scan = $decree->scan_id != null ? Attach::find($decree->scan_id) : null;
        if ($attach_file != null) {
            // Storage::delete($attach_file->url);
            $attach_file->delete();
            $decree->update(['file_id' => null]);
        }
        if ($attach_scan != null) {
            // Storage::delete($attach_scan->url);
            $attach_scan->delete();
            $decree->update(['scan_id' => null]);
        }
        $decree->delete();
        return redirect()->route('admin.decrees.index');
    }

    static public function edit($id)
    {
        $unit = Decrees::find($id);

        return view('decrees::manager.create', [
            'unit' => $unit,
            'org' => Org::with('organization')->get(),
            'types' => app(TypesTask::class)->run(),
            'workers' => Users::all()->where('active', '=', '1'),
            'uid' => $unit->uid,
        ]);
    }

    static public function update($id, Request $request)
    {
        $decree = Decrees::find($id);
        $array = $request->request->all();
        // командировка старт
        if ($request['citys'] ?? false) {
            foreach ($request['citys'] as $key => $item) {
                $array['data']['destination'][$key] =
                    [
                        'city' => $item,
                        'org' => $request['orgs'][$key] ?? null,
                        'short_report' => $request['reports'][$key] ?? null,
                    ];
            }
        }

        $array['data']['post'] = $request['post'] ?? null;
        $array['data']['target'] = $request['target'] ?? null;
        $array['data']['date_transit'] = $request['days'] ?? null;
        // командировка конец

        $oldOrg = $decree->org;



        //files start
        if ($request->file('file')) {

            $path = "files/decrees/" . $decree->id . "/";
            Storage::disk('local')->putFileAs($path, $request->file('file'), $request->file('file')->getClientOriginalName());
            $info =  Attach::find($decree->file_id);
            if ($info != null) {
                $attach = $info->update([
                    "name" => $request->file('file')->getClientOriginalName(),
                    "url" => $path . $request->file('file')->getClientOriginalName(),
                ]);
                $array['file_id'] = $info->id;
            } else {
                $attach = Attach::create([
                    "name" => $request->file('file')->getClientOriginalName(),
                    "url" => $path . $request->file('file')->getClientOriginalName(),
                    "pid" => $decree->id,
                    "module" => 'decrees',
                ]);
                $array['file_id'] = $attach->id;
            }
        }
        if ($request->file('scan')) {
            $path = "files/decrees/" . $decree->id . "/";
            Storage::disk('local')->putFileAs($path, $request->file('scan'), $request->file('scan')->getClientOriginalName());
            $info = Attach::find($decree->scan_id);
            if ($info != null) {
                $attach = $info->update([
                    "name" => $request->file('scan')->getClientOriginalName(),
                    "url" => $path . $request->file('scan')->getClientOriginalName()
                ]);
                $array['scan_id'] = $info->id;
            } else {
                $attach = Attach::create([
                    "name" => $request->file('scan')->getClientOriginalName(),
                    "url" => $path . $request->file('scan')->getClientOriginalName(),
                    "pid" => $decree->id,
                    "module" => 'decrees',
                ]);
                $array['scan_id'] = $attach->id;
            }
        }
        // files end

        $decree->update($array);
        // numb create start
        if ($oldOrg != $request['org']) {
            static::numb_create($decree->id);
        }
        // numb create end

        return redirect()->route('admin.decrees.show', $decree->id)->with(['message' => 'Приказ отредактирован']);
    }

    static public function doc_create($id, $type)
    {

        $orderType = Decrees::find($id)->type;
        $order = Decrees::leftJoin('org', 'decrees.org', '=', 'org.id')
            ->leftJoin('organizations', 'organizations.id', '=', 'org.oid')
            ->find($id);

        switch ($orderType) {
            default:
                $document =  new TemplateProcessor(module_path('Resources' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'order_doc_1.docx'));
                $document->setValue('doc_numb', $order->numb_counter ?? '');
                break;
            case 3:
                $data = $order->data;

                $target = isset($data["target"]) ? $data["target"] : "";
                switch ($type) {
                    default:
                    case 1:
                        //приказ на командировку
                        $document =  new TemplateProcessor(module_path('Resources' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'command.docx'));

                        $destination_once = "";
                        $destination = [
                            "data" => []
                        ];
                        if (isset($data["destination"])) {
                            $flag_i = true;
                            $count = count($data["destination"]);
                            foreach ($data["destination"] as $val) {
                                if ($count > 1) {
                                    $zap = ",";
                                } else {
                                    $zap = "";
                                }
                                $org = empty($val["org"]) ? " " : " {$val["org"]}";
                                if ($flag_i) {
                                    $destination_once = "г. " . $val["city"] . $org . $zap;
                                    $flag_i = false;
                                } else {
                                    $destination["data"][] = "г. " . $val["city"] . $org . $zap;
                                }
                                $count--;
                            }
                        }
                        break;
                    case 2:
                        //служебное задание на командировку
                        $document =  new TemplateProcessor(module_path('Resources' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'job_assignment.docx'));

                        $document->setValue('date_end', date("d.m.Y", strtotime($order->ended_at)));

                        $destination_once = "";
                        $flag_once = true;
                        if (isset($data["destination"])) {
                            $count = 0;
                            foreach ($data["destination"] as $val) {
                                $destination_array[$count]["city"] = "г. " . $val["city"];
                                $destination_array[$count]["org"] = $val["org"] ?? "";
                                $short_report_array[$count]["data"] = empty($val["short_report"]) ? "" : $val["short_report"];
                                $short_report_array[$count]["target"] = $flag_once ? $target : "";
                                $flag_once = false;
                                $count += 1;
                            }
                        }
                        $document->cloneRowAndSetValues('target', $short_report_array);
                        $document->cloneRowAndSetValues('org', $destination_array);
                        $date_transit = isset($data["date_transit"]) ? $data["date_transit"] : "";
                        $document->setValue('date_transit', $date_transit);
                        break;
                }

                $document->setValue('org-name', $order->name ?? "");

                $document->setValue('date', date("d.m.Y", strtotime($order->started_at)));

                $full_name_uid = $order->worker;

                $document->setValue('full_name', fullUserName($full_name_uid[0]));

                $date_start = Carbon::parse($order->started_at);
                $date_end = Carbon::parse($order->ended_at);
                $document->setValue('date_day', $date_start->format("d"));
                $document->setValue('end_date_day', $date_end->format("d"));

                $month =  ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];
                $document->setValue('date_month', $month[$date_start->format("n") - 1]);
                $document->setValue('end_date_month', $month[$date_end->format("n") - 1]);

                $document->setValue('date_year', $date_start->format("y"));
                $document->setValue('end_date_year', $date_end->format("y"));
                $interval = $date_start->diff($date_end, true)->days + 1;
                $document->setValue('term', $interval ?? "");

                $document->setValue('numb', $order->numb ?? "");

                $director_short_name_query = UsersOrg::where('type', '=', 1)->where('oid', '=', $order->oid)->first();
                $director_short_name = shortUserName($director_short_name_query);

                $document->setValue('director_short_name', $director_short_name == "Не указан" ? "" : $director_short_name);

                $document->setValue('source_financing', $order->name ?? "");

                $document->setValue('destination_once', $destination_once ?? "");


                $post = isset($data["post"]) ? $data["post"] : "";
                $document->setValue('position', $post ?? "");

                $document->setValue('target', $target ?? "");
                break;
        }

        //$file_url = 'files/decrees/' . $id . '-' . time() . '.docx'; // Не верно!
        //$file_url = ROOT . 'usr/modules/decrees/Resources/Templates/' . $id . '-' . time() . '.docx';
        //$file_url = public_path($id . '-' . time() . '.docx');
        $file_url = storage_path($id . '-' . time() . '.docx');
        //dd($file_url);

        $document->saveAs($file_url);

        OfficeSupport::get_file($file_url);
    }

    // 0 - file_id
    // else - scan_id
    static public function getFile($id, $mode = 0)
    {

        if ($mode == 0) {
            $fid = Decrees::find($id)->file_id;
            return  response()->download(Attach::find($fid)->url);
        } else {
            $fid = Decrees::find($id)->scan_id;
            return  response()->download(Attach::find($fid)->url);
        }
    }

    static public function numb_create($id)
    {
        $decree = Decrees::find($id);
        $prefixType = [1 => "ОД", "ЛС", "К"];
        $prefix = Org::find($decree->org)->prefix;
        $type_prefix = $prefixType[$decree->type];
        $year = empty($decree->started_at) ?  $decree->created_at->format('Y') : $decree->started_at->format('Y');

        $searchDecree = Decrees::where('type', '=', $decree->type)
            ->where('org', '=', $decree->org)
            ->where('id', '!=', $decree->id)
            ->whereYear('started_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($searchDecree != null) {
            $counter = $searchDecree->numb_counter;
            if (empty($counter)) {
                $counter = 1;
            } else {
                $counter++;
            }
        } else {
            $counter = 1;
        }
        $update = [];
        $update['numb'] = $prefix . $type_prefix . "-" . $counter;
        $update['numb_counter'] = $counter;

        $decree->update($update);
    }
}
