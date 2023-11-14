<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Reviews\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;

use \Boot\System\Categories\Categories;
use \Boot\Support\Facades\Translation;
use Boot\System\Http\Request;
use Reviews\Models\Reviews;
use Illuminate\Support\Facades\Storage;
use Boot\Models\Attach;
use Organizations\Model\Organizations;
use Boot\System\Database\DB;

class ManagerController extends AbstractController
{
    public static function index()
    {
        return view('reviews::manager.index');
    }

    protected static function types() {
        return [
            1 => Translation::trans('reviews.types.text'),
            2 => Translation::trans('reviews.types.audio'),
            3 => Translation::trans('reviews.types.video'),
        ];    
    }

    public static function create()
    {
        $categories = new Categories;     
        return view('reviews::manager.create',[
            'categories' =>  $categories->getModuleCategories('reviews'),
            'types' => self::types(),
        ]);
    }

    public static function store(Request $request)
    {
        $unit = Reviews::create($request->request->all());

         // Обработка превью
         if ($request->file('preview')) {
            $data = preview_create($request->file('preview'), 'reviews', $unit);
            $unit->update([
                'preview' => $data
            ]);
        }
      
        return redirect()->route('admin.reviews.create')->with(['message' =>'Отзыв создан']);
    }    

    public static function update($id, Request $request)
    {
        $unit = Reviews::find($id);
        $rq = $request->except(['preview']);

        // Обработка превью
        if ($request->file('preview')) {
            $data = preview_create($request->file('preview'), 'reviews', $unit);
            $rq['preview'] = $data;
        }
        $unit->update($rq);

        return redirect()->route('admin.reviews.edit',$id)->with(['message' =>'Отзыв отредактирован']);
    }

    public static function edit($id)
    {
        $categories = new Categories;   
        $unit = Reviews::find($id);       
        $org = Organizations::find($unit->oid);

        return view('reviews::manager.create',[
            'categories' =>  $categories->getModuleCategories('reviews'),
            'types' => self::types(),
            'unit' => $unit,
            'org' => $org
        ]);
    }

    public static function destroy($id)
    {
        $unit = Reviews::find($id)->delete();       
        return redirect()->route('admin.reviews.index')->with(['message' =>'Отзыв удален']);
    }

    public function getOrgs(Request $request)
    {
        $res = [];
        $search = $request->q;
        $res = Organizations::select("id", "name")
            ->where('name', 'LIKE', "%$search%")
            ->orWhere('id', 'LIKE', "%$search%")
            ->get();

        return response()->json($res);
    }
}
