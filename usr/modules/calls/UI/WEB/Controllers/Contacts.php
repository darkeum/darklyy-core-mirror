<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander
*/

namespace Calls\UI\WEB\Controllers;

use Boot\Controller\AbstractController;
use Boot\Models\Users;
use Boot\Models\UsersPhoneid;
use Boot\System\Database\DB;
use Boot\System\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

class Contacts extends AbstractController
{
    /**
     * @return Application|Factory|View|null
     */
    function index(): View|Factory|Application|null
    {
        return view('calls::manager.contacts.index');
    }

    /**
     * @param int $id
     * @param UsersPhoneid $users
     * @return Factory|View|Application|null
     */
    function edit(int $id, UsersPhoneid $users): Factory|View|Application|null
    {
        $ids = $users->query()
            ->whereNot('id',  $id)
            ->pluck('uid');

        $user = $users::findOrFail($id);
        $phone = str($user->phoneid);

        if($phone->startsWith('8') && $phone->length() == 11) {
            $user->phoneid = $phone->replaceFirst('8', '+7')->value();
        }

        if($phone->startsWith('7') && $phone->length() == 11) {
            $user->phoneid = $phone->replaceFirst('7', '+7')->value();
        }

        return view('calls::manager.contacts.create',
            [
                'userPhone' => $user,
                'users' => $this->getUsers($ids)
            ]);
    }

    /**
     * Добавление
     *
     * @param Request $request
     */
    function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|min:12|unique:Boot\Models\UsersPhoneid,phoneid',
            'id_user' => 'required',
        ]);

        $phoneid = $request->get('phone');
        $phone = str($phoneid);

        if($phone->startsWith('8')) {
            $phoneId = $phone->replaceFirst('8', '+7')->value();
        }

        if($phone->startsWith('7')) {
            $phoneId = $phone->replaceFirst('7', '+7')->value();
        }

        UsersPhoneid::create([
            'uid' => $request->get('id_user'),
            'phoneid' => $phoneId,
            'status' => 1,
        ]);

        return redirect()->route('admin.contacts.index');
    }

    /**
     * Обновление
     *
     * @param Request $request
     * @param $id
     */
    function update(Request $request, $id)
    {
        $request->validate([
            'phone' => 'required|min:12|numeric|unique:Boot\Models\UsersPhoneid,phoneid',
        ]);

        $phone = UsersPhoneid::find($id);
        $phone->phoneid = $request->get('phone');
        $phone->save();

        return redirect()->route('admin.contacts.index');
    }

    /**
     * @return Factory|View|Application|null
     */
    function create(): Factory|View|Application|null
    {
        $ids = DB::table('users_phoneid')->pluck('uid');

        return view('calls::manager.contacts.create', ['users' => $this->getUsers($ids)]);
    }

    /**
     * Удаление
     *
     * @param int $id
     */
    function destroy(int $id)
    {
        $phone = UsersPhoneid::find($id);
        $phone->delete();

        return redirect()->route('admin.contacts.index');
    }

    /**
     * Получение контактов
     *
     * @param $ids
     * @return Collection|array
     */
    private function getUsers($ids): Collection|array
    {
        return Users::query()
            ->whereNotIn('id', $ids)
            ->where('active', 1)->get();
    }
}
