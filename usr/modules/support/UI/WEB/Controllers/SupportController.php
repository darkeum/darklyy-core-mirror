<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Support\UI\WEB\Controllers;

use Boot\Models\Users;
use Boot\Support\Carbon;
use Support\Models\Support;
use Boot\Support\Facades\Auth;
use Support\Models\SupportMessage;
use Support\Notifications\SupportNew;
use Boot\Controller\AbstractController;
use Support\Tasks\TypesTask;
use Support\UI\WEB\Requests\SupportRequest;

class SupportController extends AbstractController
{
    static function index()
    {
        return view('support::manager.grid.my');
    }

    static function all()
    {
        return view('support::manager.grid.all');
    }

    static function create()
    {
        return view('support::manager.create', [
            'users' => Users::where('group', 1)->where('active', 1)->orderBy('surname')->get(),
            'types' => app(TypesTask::class)->run(null, 'all'),
        ]);
    }

    static function store(SupportRequest $request)
    {
        $support_request = $request->sanitizeInput([
            'type',
            'title' => null,
            'uid' => Auth::id(),
            'place' => null,
            'url' => null,
            'mark' => 1,
            'view_at' => Carbon::now(),
            'status' => 1,
        ]);

        $support = Support::create($support_request);

        // Поменять логику!!!
        $support_message_request = [
            'support_id' => $support->id,
            'uid' => $support_request['uid'],
            'message' => $request->comment,
        ];
        if (!empty($request->attaches)) {
            $support_message_request = [
                'message' => 'Название файла тут'
            ];
            //$support_message = SupportMessage::create($support_message_request);
            # https://spatie.be/docs/laravel-medialibrary/v10/api/adding-files
            //$support_message->addFromMediaLibraryRequest($request->attaches)->toMediaCollection();

            // Новое
            //dd($request->attaches);
            $support->addFromMediaLibraryRequest($request->attaches)->toMediaCollection();
        }
        SupportMessage::create($support_message_request);

        $support_manager = Users::permission('support manager')->get();
        foreach ($support_manager as $value) {
            $value->notify(new SupportNew($support));
        }
        return redirect()->route('admin.support.show', ['id' => $support->id]);
    }

    static function show($id)
    {
        $pageConfigs = [
            'pageHeader' => true,
            'contentLayout' => "content-right-sidebar",
            'pageClass' => 'chat-application',
        ];
        return view('support::manager.show', [
            'pageConfigs' => $pageConfigs,
            'id' => $id,
            'support' => Support::findOrFail($id),
        ]);
    }
}
