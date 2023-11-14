<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Certificates Manager Controller
* @editor      Gusev Kirill
*/

namespace Certificates\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;
use \Boot\System\Categories\Categories;
use Boot\System\Http\Request;
use Certificates\UI\WEB\Models\Certificates;
use Boot\Models\Attach;
use Illuminate\Support\Facades\Storage;

class ManagerController extends AbstractController
{
    public static $module = 'certificates';
    public static $lang = 'certificates';

    public static function index()
    {
      return view('certificates::manager.index');
    }

    public static function create()
    {
        $categories = new Categories;           
        return view('certificates::manager.create',[
            'categories' =>  $categories->getModuleCategories(static::$module)
        ]);
    }

    public static function store(Request $request)
    {
        $unit = Certificates::create($request->request->all());

        // Обработка превью
        if ($request->file('preview')) {
            $data = preview_create($request->file('preview'), 'certificates', $unit);
            $unit->update([
                'preview' => $data
            ]);
        }

        return redirect()->route('admin.certificates.create')->with(['message' => 'Сертификат успешно создан!']);
    }    

    public static function update($id, Request $request)
    {
        $unit = Certificates::find($id);
        $rq = $request->except(['preview']);
        // Обработка превью
        if ($request->file('preview')) {
            $data = preview_create($request->file('preview'), 'certificates', $unit);
            $rq['preview'] = $data;
        }
        $unit->update($rq);

        return redirect()->route('admin.certificates.edit',$id)->with(['message' => 'Сертификат успешно обновлен!']);
    }

    public static function edit($id)
    {
        $categories = new Categories;   
        $unit = Certificates::find($id);
        return view('certificates::manager.create',[
            'categories' =>  $categories->getModuleCategories(static::$module),
            'unit' => $unit,
        ]);
    }

    public static function destroy($id)
    {
        Certificates::find($id)->delete();
        return redirect()->route('admin.certificates.index');
    }
}
