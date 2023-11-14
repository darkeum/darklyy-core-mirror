<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Objects\UI\WEB\Controllers;

use Boot\Support\Facades\Auth;
use Boot\Controller\AbstractController;

use Boot\Models\Users;
use Boot\Models\Relations;
use Objects\Models\Objects;

use Boot\System\Http\Request;
//use Support\UI\WEB\Requests\SupportRequest;
use Illuminate\Support\Facades\DB;
use Boot\Support\Carbon;

// TEST
use Sales\Model\SalesDetail;
use Sales\Models\Sales;
use Task\Models\Task;
use Catalog_v2\Model\Catalog;

use Users\Models\UsersObject;
use function React\Promise\all;

class ObjectsController extends AbstractController
{
    static function index()
    {
        return view('objects::manager.index');
    }

    static function create()
    {
        return view('objects::manager.create', [
            'users' => Users::where('active', 1)->orderBy('id')->get()
        ]);
    }

    static function store(Request $request)
    {
        request()->validate([
            //'published_at' => ['required'],
            // add rules for other fields
        ]);

        if (!$request->input('uid')) {
            $request->merge([
                'uid' => Auth::id()
            ]);
        }

        $unit = Objects::create($request->all());

        return redirect()->route('admin.objects.index');
    }

    static function edit($id)
    {
        $unit = Objects::find($id);

        return view('objects::manager.create', [
            'unit' => $unit,
            'users' => Users::where('active', 1)->orderBy('id')->get()
        ]);
    }

    static function update(Request $request, $id)
    {
        request()->validate([
            //'published_at' => ['required'],
            // add rules for other fields
        ]);

        $unit = Objects::find($id);

        if (!$request->input('uid')) {
            $request->merge([
                'uid' => Auth::id()
            ]);
        }

        // Обработка превью
        if ($request->file('preview')) {
            //$data = preview_create($request->file('preview'), 'news', $unit);
            //$unit->update([
            //    'preview' => $data
            //]);
            //$rq['preview'] = $data;
        }

        $unit->update($request->all());

        return redirect()->route('admin.objects.index');
    }

    static function show($id)
    {
        $object = Objects::findOrFail($id);
        $test = Sales::findOrFail(25044);
        $test2 = SalesDetail::find($test);
        $test2 = Catalog::find($test);
        $t = 25044;

        //$test3 = DB::query("SELECT s.*, d.title, p.price FROM JMY_sales_detail as s LEFT JOIN JMY_catalog AS d on(d.id=s.cid) LEFT JOIN JMY_ver_device_price as p on(d.id=p.vdid) WHERE s.sales_id = '".$t."' AND s.status != '0' AND s.del = '0'");

        //$test3 = DB::table('sales_detail')
        //    ->select('sales_detail.*', 'catalog.title', 'ver_device_price.price')
        //    //->on(function ($query) {
        //    //    $query->where('JMY_catalog.id','=',DB::raw('sales_detail.cid'));
        //    //})
        //    //->catalog(function ($query) {
        //    //    $query->where('JMY_catalog.id','=',DB::raw('ver_device_price.vdid'));
        //    //})
        //    ->where('sales_detail.sales_id','=','".{$t}."')
        //    ->where('sales_detail.status','!=','0')
        //    ->where('sales_detail.del','=','0')
        //    ->get();

        //$test3 = DB::table("sales_detail as s")
        //    ->leftJoin("catalog as d", function($join){
        //        $join->on(DB::raw("(d.id = s.cid)"));
        //    })
        //    ->leftJoin("ver_device_price as p", function($join){
        //        $join->on(DB::raw("(d.id = p.vdid)"));
        //    })
        //    ->select("s.*", "d.title", "p.price")
        //    ->where("s.sales_id", "=", '.$t.')
        //    ->where("s.status", "!=", 0)
        //    ->where("s.del", "=", 0)
        //    ->get();

        return view('objects::manager.show', [
            'object' => $object,
            //'test' => $test2,
            //'test3' => $test3,

            // Заказы
            'sales' => [
                'list' => $object->sales,
                'types' => config('other-modules-config.sales.types'),
                'status' => config('other-modules-config.sales.status'),
            ],

            // Задачи
            'tasks' => [
                //'list' => self::tasks($object),
                'status' => config('other-modules-config.tasks.status'),
            ],

            // Файлы
            'files' => self::files($object),
            // Отношения в массив сразу
            //'relations' => self::relations($object),

            // Дерево
            'tree' => self::tree($object),
            'treei' => self::treei($object),
        ]);
    }

    static function destroy($id)
    {
        dd('Функция удаления пока отключена');

        //$request = [
        //    'status' => 2,
        //];
        //$unit = News::find($id)->update($request);

        //$unit = News::find($id);
        //$unit->delete();
        //
        //return redirect()->route('admin.news.index');
    }


    // Карта
    static function map()
    {
        //Paginator::useBootstrapFive();
        $objects = Objects::where('coords', '<>', '')
            ->orderBy('id', 'desc')
            ->get();

        $pageConfigs = [
            'pageHeader' => true,
            'contentLayout' => "content-right-sidebar",
            'pageClass' => 'chat-application',
        ];

        return view('objects::manager.map', [
            'objects' => $objects,
            'pageConfigs' => $pageConfigs,
        ]);
    }
    // Координаты для карты по запросу
    static function coords()
    {
        $objects = Objects::where('coords', '<>', '')->get()->reverse()->values();
        return json_encode($objects, JSON_UNESCAPED_UNICODE);
    }

    public function users($id) {

        $object = Objects::find($id);
        // $object = Objects::find(80); // Тестовый был ID

        $uObj = $object->users->pluck('uid');
        $users_obj = Users::whereIn('id', $uObj)->get();

        $users = Users::where('group', 1)->where('active', 1)->where('hidden', 0)->orderBy('surname', 'ASC')->whereKeyNot($uObj)->get();

        return view('objects::manager.layouts.modal.users', [
            'object' => $object,
            'users' => $users,
            'users_obj' => $users_obj
        ]);
    }

    public function users_add(Request $request) {

        foreach ($request->uid as $id) {
            $data = [
                "uid" => $id,
                "object" => $request->object,
                "choose" => 0,
                "status" => 1
            ];

            UsersObject::firstOrCreate($data);
        }
    }

    // Отношения
    static function relations($object)
    {
        $module_in = Relations::where([
            ['module_out', 'object'],
            ['module_in', 'task'],
            ['pid_out', $object->id]
        ])->orderByDesc('id')->get();

        $module_out = Relations::where([
            ['module_in', 'object'],
            ['module_out', 'task'],
            ['pid_out', $object->id]
        ])->orderByDesc('id')->get();

        //dd($module_in, $module_out);

        return ['module_in' => $module_in, 'module_out' => $module_out];
    }
    // Заказы (Если нет модели)
    static function sales($object)
    {
        $sales = Sales::where([
            ['object', $object->id],
        ])->get();

        return $sales;
    }
    // Задачи (Если нет модели)
    static function tasks($object)
    {
        //dd($object->pid_in);
        //dd(self::relations($object));
        $tasks = Task::where([
            ['object', $object->id],
        ])->get();

        return $tasks;
    }
    // Файлы (Если нет модели)
    static function files($object)
    {
        //$object = Objects::findOrFail($id);

        return [];
    }

    // Дерево
    static function tree($object)
    {
        if (!is_object($object)) {
            $object = Objects::findOrFail($object);
        }

        // Объект (Родительский контейнер Объектов)
        $tree = [
            'id' => $object->id,
            'text' => $object->name,
            'type' => 'object',
            'state' => [
                'opened' => true,
            ],
            // 'children' => [],
            'a_attr' => [
                'href' => $object->id
            ],
        ];

        if ($object->sales->isNotEmpty()) {
            $tree['children'] = [];
        }

        // Заказы
        if ($object->sales->isNotEmpty()) {
            // Заказы Родительский контейнер - Группировка)
            $sales = [
                'text' => 'Заказы',
                'type' => 'sale',
                'children' => [],
            ];
            //$sales['children'] = [];

            //foreach (self::sales($object) as $sale) {
            foreach ($object->sales as $sale) {

                // Заказ клиента (Родительский контейнер Заказов)
                $sale = [
                    'id' => 'sale-' . $sale->id,
                    'text' => 'Заказ клиента #' . $sale->id,
                    'type' => 'sale',
                    //'children' => [],
                    'a_attr' => [
                        'href' => $sale->id
                    ],
                ];
                array_push($sales['children'], $sale);
            }
            // Пушим заказы в дерево
            array_push($tree['children'], $sales);
        }

        // Задачи
        if ($object->tasks->isNotEmpty()) {
            // Задачи Родительский контейнер - Группировка)
            $tasks = [
                'text' => 'Задачи',
                'type' => 'task',
                'children' => [],
            ];
            //$tasks['children'] = [];

            foreach (self::tasks($object) as $task) {
                $task = [
                    'id' => 'task-' . $task->id,
                    'text' => 'Задача #' . $task->id,
                    'type' => 'task',
                    //'children' => [],
                    'a_attr' => [
                        'href' => $task->id
                    ],
                ];
                array_push($tasks['children'], $task);
            }
            // Пушим задачи в дерево
            array_push($tree['children'], $tasks);
        }

        // Заявки
        if ($object->tasks->isNotEmpty()) {
            // Заявки Родительский контейнер - Группировка)
            $storages = [
                'text' => 'Заявки',
                'type' => 'storage',
                'children' => []
            ];
            //$storages['children'] = [];

            foreach (self::tasks($object) as $storage) {
                $storage = [
                    'id' => 'storage-' . $storage->id,
                    'text' => 'Заявка #' . $storage->id,
                    'type' => 'storage',
                    //'children' => [],
                    'a_attr' => [
                        'href' => $storage->id
                    ],
                ];
                array_push($storages['children'], $storage);
            }
            // Пушим продажи в дерево
            array_push($tree['children'], $storages);
        }


        return json_encode($tree, JSON_UNESCAPED_UNICODE);
    }

    // Идеальное дерево
    static function treei($object)
    {
        $tree_demo = [
            'text' => 'Объект #1',
            'type' => 'object',
            'state' => [
                'opened' => true,
            ],
            'children' => [
                0 => [
                    'text' => 'Заказ #1',
                    'type' => 'sale',
                    'children' => [
                        0 => [
                            'text' => 'Задача #1',
                            'type' => 'task',
                        ],
                        1 => [
                            'text' => 'Задача #2',
                            'type' => 'task',
                            'children' => [
                                0 => [
                                    'text' => 'Под задача #1',
                                    'type' => 'task',
                                ],
                                1 => [
                                    'text' => 'Под задача #2',
                                    'type' => 'task',
                                ],
                                2 => [
                                    'text' => 'Под задача #3',
                                    'type' => 'task',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        //$temp = function ($text, $type) {
        //    $branch = [
        //        'text' => $text,
        //        'parent' => '#',
        //        'type' => $type,
        //        'state' => [
        //            'opened' => true,
        //        ],
        //        'children' => [],
        //        'li_attr' => [],
        //        'a_attr' => [
        //            'href' => ''
        //        ],
        //    ];
        //    return $branch;
        //};

        // Объект (Родительский контейнер Объектов)
        $tree = [
            'id' => $object->id,
            'text' => $object->name,
            'type' => 'object',
            'state' => [
                'opened' => true,
            ],
            // 'children' => [],
            'a_attr' => [
                'href' => $object->id
            ],
        ];

        //dd($object->sales);

        if ($object->sales->isNotEmpty()) {

            // Заказы
            $sale = [];
            $tree['children'] = [];

            //foreach (self::sales($object) as $sale) {
            foreach ($object->sales as $sale) {

                // Заказ клиента (Родительский контейнер Заказов)
                $sale = [
                    'id' => $sale->id,
                    'text' => 'Заказ клиента #' . $sale->id,
                    'type' => 'sale',
                    'children' => [],
                    'a_attr' => [
                        'href' => $sale->id
                    ],
                ];

                // Задачи Родительский контейнер Задач - Группировка)
                $tasks = [
                    'text' => 'Задачи',
                    'type' => 'task',
                    // 'children' => [],
                ];

                if ($object->tasks->isNotEmpty()) {
                    $tasks['children'] = [];

                    foreach (self::tasks($object) as $task) {
                        $task = [
                            'id' => $task->id,
                            'text' => 'Задача #' . $task->id,
                            'type' => 'task',
                            'children' => [],
                            'a_attr' => [
                                'href' => $task->id
                            ],
                        ];

                        array_push($tasks['children'], $task);
                    }
                    // Пушим задания в заказ
                    array_push($sale['children'], $tasks);
                }

                // Продажи Родительский контейнер Продаж - Группировка)
                $storage = [
                    'text' => 'Продажи',
                    'type' => 'storage',
                    // 'children' => [],
                ];

                /*
                if ($object->storage->isNotEmpty()) {
                    $storage['children'] = [];

                    //foreach (self::tasks($object) as $task) {
                    //    $task = [
                    //        'id' => $task->id,
                    //        'text' => 'Задача #' . $task->id,
                    //        'type' => 'task',
                    //        'children' => [],
                    //        'a_attr' => [
                    //            'href' => $task->id
                    //        ],
                    //    ];
                    //
                    //    array_push($tasks['children'], $task);
                    //}
                    //// Пушим задания в заказ
                    //array_push($sale['children'], $tasks);
                }
                array_push($sale['children'], $storage);
                */
            }

            // Пушим в главный контейнер
            array_push($tree['children'], $sale);
        }

        return json_encode($tree, JSON_UNESCAPED_UNICODE);
    }
}