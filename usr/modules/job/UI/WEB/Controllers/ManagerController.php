<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Job Manager Controller
*/

namespace Job\UI\WEB\Controllers;

use Job\Models\Job;
use Boot\Support\Str;
use Boot\Models\Users;
use Job\Grid\SubscribeGrid;
use Boot\System\Http\Request;
use Boot\Support\Facades\Auth;
use Boot\Support\Facades\Translation;
use Boot\System\Categories\Categories;
use Boot\Controller\AbstractController;

class ManagerController extends AbstractController
{

    public static function index()
    {
        return view('job::manager.index');
    }

    public static function show($id)
    {
        $job = Job::findOrFail($id);
        return redirect($job->url);
    }

    public static function subscribes()
    {
        //$grid = new SubscribeGrid;
        //display('@job__/manager/subscribe.html.twig', [
        //    'title' => Translation::trans('job_subscribe_list'),
        //    'grid' =>  $grid->render(),
        //]);
        //return view('job::manager.index');
    }

    public static function create() {
        $categories = new Categories; 
        $users = Users::where('group', 1)->where('active', 1)->where('hidden', 0)->orderBy('surname', 'ASC')->get()->all();
        display('@job__/manager/editor.html.twig', [
            'title' => Translation::trans('job_added'),
            'categories' =>  $categories->getModuleCategories('job'),
            'do' => Translation::trans('job_do_add'),
            'users' => $users,
            'type' => 'store',
        ]);
    }

    public static function store(Request $request) {
        $array = $request->request->all();
        $array['slug'] = text_translit($array['title']);     
        Job::create(static::prepareRequest($array));
        return redirect()->route('admin.job.create')->with(['message' => 'Вакансия создана']);
    }

    private static function prepareRequest($request){
        $result = [];
        $result['title'] = $request['title']; 
        $result['uid'] = Auth::id(); 
        $result['description'] = $request['description']; 
        $result['salary'] = $request['salary'] ?? ''; 
        $result['category'] = intval($request['category']); 
        $result['responsible'] = $request['responsible'] == 0 ? null : intval($request['responsible']); 
        $result['slug'] = empty($request['slug']) ? Str::slug($result['title']) : $request['slug']; 
        $result['conditions'] = !empty($request['conditions']) ? explode("\r\n", $request['conditions']) : [];
        $result['duties'] = !empty($request['duties']) ? explode("\r\n", $request['duties']) : [];
        $result['requirements'] = !empty($request['requirements']) ? explode("\r\n", $request['requirements']) : [];
        return $result;
    }

    public static function update($id, Request $request) {       
        $array = $request->request->all();
        $array['slug'] = text_translit($array['title']);  
        Job::find($id)->update(static::prepareRequest($array));        
        return redirect()->route('admin.job.edit',$id)->with(['message' => 'Вакансия отредактирована']);
    }

    public static function edit($id)
    {      
        $categories = new Categories; 
        $users = Users::where('group', 1)->where('active', 1)->where('hidden', 0)->orderBy('surname', 'ASC')->get()->all();
        $unit = Job::find($id);
        $conditions = !empty($unit->conditions) ? implode("\r\n", $unit->conditions) : '';
        $duties = !empty($unit->duties) ? implode("\r\n", $unit->duties) : '';
        $requirements = !empty($unit->requirements) ? implode("\r\n", $unit->requirements) : '';
        return view('job::manager.create',[
            'usersList' => $users,
            'categories' =>  collect($categories->getModuleCategories('job')),
            'unit' => $unit,
            'conditions' => $conditions,
            'duties' => $duties,
            'requirements' => $requirements,
        ]);
    }

    public static function destroy($id)
    {
        Job::find($id)->delete();
        return redirect()->route('admin.job.index')->with(['message' => 'Вакансия удалена']);
    }
}