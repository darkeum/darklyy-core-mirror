<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace News\UI\WEB\Controllers;

use Boot\Support\Facades\Translation;
use Boot\System\Categories\Categories;
use News\Models\News;
use Boot\Support\Facades\Auth;
use Boot\Controller\AbstractController;
use Illuminate\Support\Facades\DB;
use Boot\Support\Carbon;

use Boot\System\Http\Request;

class ManagerController extends AbstractController
{
    static function index()
    {
        return view('news::manager.index');
    }

    static function show($id)
    {
        $unit = News::find($id);
        return redirect()->to($unit->url);
    }

    static function create()
    {
        $categories = new Categories;
        //$dir = ROOT.'files/news/temp';
        //if (!file_exists($dir)) {
        //    mkdir($dir, 0777);
        //}

        return view('news::manager.create', [
            'title' => Translation::trans('news.added'),
            'categories' => $categories->getModuleCategories('news'),
            'statuses' => trans('news::lang.admin.status'),
            'do' => Translation::trans('news.editor.add'),
            'type' => 'store',
        ]);
    }

    static function store(Request $request)
    {
        request()->validate([
            //'published_at' => ['required'],
            // add rules for other fields
        ]);

        $keywords = $request->input('keywords');

        //
        $request->merge([
            'keywords' => $keywords ? implode(',', $keywords) : ''
        ]);

        $unit = News::create($request->all());

        // Обработка превью
        if ($request->file('preview')) {
            $data = preview_create($request->file('preview'), 'news', $unit);
            $unit->update([
                'preview' => $data
            ]);
        }

        $unit->update([
            'slug' => $unit->id . '-' . text_translit($request->input('title')),
        ]);

        //if ($request->input('status') == 1) {
        //    $unit->update([]);
        //}

        // Проверка статуса для мягкого удаления
        if ($request->input('status') == 2) {
            $unit->delete();
        }

        return redirect()->route('admin.news.index');
    }

    static function edit($id)
    {
        $categories = new Categories;
        $unit = News::withTrashed()->findOrFail($id);

        return view('news::manager.create', [
            //'title' => Translation::trans('news.editor.edited'),
            'categories' => $categories->getModuleCategories('news'), // Просто дергает все категории для новостей из таблицы с категориями
            // 'do' => trans('news.editor.edit'),
            'unit' => $unit,
            'statuses' => trans('news::lang.admin.status'),
            //'type' => 'update',
            //'id' => $id,
        ]);
    }

    static function update(Request $request, $id)
    {
        $unit = News::withTrashed()->findOrFail($id);

        if ($request->input('status') == 1) {
            $unit->restore();
        }

        // Проверка статуса для мягкого удаления
        if ($request->input('status') == 2) {
            $unit->delete();
        }

        //$unit->update([
        //    'slug' => $unit->id . '-' . text_translit($request->input('title'))
        //]);

        //
        $keywords = $request->input('keywords');

        //
        $request->merge([
            'keywords' => $keywords ? implode(',', $keywords) : ''
        ]);

        $rq = $request->except(['preview']);

        // Обработка превью
        if ($request->file('preview')) {
            $data = preview_create($request->file('preview'), 'news', $unit);
            //$unit->update([
            //    'preview' => $data
            //]);
            $rq['preview'] = $data;
        }

        $unit->update($rq);

        //$unit->tags()->attach($request->input('tags'));

        return redirect()->route('admin.news.index');
    }

    static function destroy($id)
    {
        //$request = [
        //    'status' => 2,
        //];
        //$unit = News::find($id)->update($request);

        $unit = News::find($id);
        $unit->delete();

        return redirect()->route('admin.news.index');
    }
}
