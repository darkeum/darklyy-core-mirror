<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Slider\UI\WEB\Controllers;

use Boot\Support\Facades\Translation;
use Boot\System\Categories\Categories;
use Slider\Models\Slider;
use Boot\Support\Facades\Auth;
use Boot\Controller\AbstractController;
use Illuminate\Support\Facades\DB;
use Boot\Support\Carbon;
use Boot\Support\Facades\Storage;
use Boot\System\Http\Request;


class ManagerController extends AbstractController
{
    public function index()
    {
        return view('slider::manager.index');
    }

    //public function show($id)
    //{
    //    $unit = Slider::findOrFail($id);
    //    return redirect()->to($unit->url);
    //}

    public function create()
    {
        return view('slider::manager.create', [
            'title' => Translation::trans('slider.added'),
            'statuses' => trans('slider::lang.admin.status'),
            'do' => Translation::trans('slider.editor.add'),
            //'type' => 'store',
        ]);
    }

    public function store(Request $request)
    {
        //request()->validate([
        //]);

        //
        //$request->merge([
        //
        //]);

        $unit = Slider::create($request->request->all());

        // Обработка главного изображения
        if ($request->file('img')) {
            $img = storage_img_load($request->file('img'), 'slider', null, 'png');
        }

        if ($request->file('bg_left')) {
            $bg_left = storage_img_load($request->file('bg_left'), 'slider', null, 'png');
        }

        if ($request->file('bg_right')) {
            $bg_right = storage_img_load($request->file('bg_right'), 'slider', null, 'png');
        }

        $unit->update([
            'img' => $img ?? '',
            'bg_left' => $bg_left ?? '',
            'bg_right' => $bg_right ?? ''
        ]);

        //if ($request->input('status') == 1) {
        //    $unit->update([]);
        //}

        // Проверка статуса для мягкого удаления
        //if ($request->input('status') == 2) {
        //    $unit->delete();
        //}

        return redirect()->route('admin.slider.index');
    }

    public function edit($id)
    {
        $unit = Slider::findOrFail($id);

        return view('slider::manager.create', [
            'unit' => $unit,
            //'statuses' => trans('slider::lang.admin.status'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $unit = Slider::findOrFail($id);

        //if ($request->input('status') == 1) {
        //    $unit->restore();
        //}
        //
        //// Проверка статуса для мягкого удаления
        //if ($request->input('status') == 2) {
        //    $unit->delete();
        //}

        $rq = $request->except(['img', 'bg_left', 'bg_right']);

        // Обработка главного изображения
        if ($request->file('img')) {

            storage_img_delete($unit->img);

            $img = storage_img_load($request->file('img'), 'slider', null, 'png');
            $rq['img'] = $img ?? '';
        }

        if ($request->file('bg_left')) {

            storage_img_delete($unit->bg_left);

            $bg_left = storage_img_load($request->file('bg_left'), 'slider', null, 'png');
            $rq['bg_left'] = $bg_left ?? '';
        }

        if ($request->file('bg_right')) {

            storage_img_delete($unit->bg_right);

            $bg_right = storage_img_load($request->file('bg_right'), 'slider', null, 'png');
            $rq['bg_right'] = $bg_right ?? '';
        }

        //$unit->update($request->all());
        $unit->update($rq);

        return redirect()->route('admin.slider.index');
    }

    public function destroy($id)
    {
        //$request = [
        //    'status' => 2,
        //];
        //$unit = Slider::find($id)->update($request);

        $unit = Slider::findOrFail($id);

        storage_img_delete($unit->img);
        storage_img_delete($unit->bg_left);
        storage_img_delete($unit->bg_right);

        $unit->delete();

        return redirect()->route('admin.slider.index');
    }
}
