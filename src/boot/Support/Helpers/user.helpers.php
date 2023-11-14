<?php

use Boot\Models\Users;
use Illuminate\Support\Facades\DB;

// Получить пользователя по UID
if (!function_exists('getUser')) {

    function getUser($id)
    {
        return Users::findOrFail($id);
    }
}

// Получить пользователей по UID
if (!function_exists('getUsers')) {

    function getUsers($param = 'id', $ids = false, $order = 'surname')
    {
        if ($ids) {
            $users = DB::table('users')->whereIn($param, $ids->unique())
                ->orderBy($order)
                ->get();
        } else {
            $users = DB::table('users')->get();
        }

        return $users;
    }
}

// Получить полное ФИО пользователя по UID
if (!function_exists('getUserFullName')) {

    function getUserFullName($id)
    {
        $user = Users::findOrFail($id);
        return $user->surname . ' ' . $user->name . ' ' . $user->ochestvo;
    }
}

// Получить аватар пользователя или no_avatar
if (!function_exists('getUserAvatar')) {

    function getUserAvatar($uid = null)
    {
        // Пока нет аватара в БД
        // $user = Users::findOrFail($id);

        $avatarGuest = "/avatars/guest_avatar.png";
        $noAvatar = "/avatars/no_avatar.png";

        if (empty($uid)) return $uid == 0 ? $avatarGuest : $noAvatar;

        $path = "resources/avatars/users/";

        if (glob(ROOT . "{$path}/av{$uid}.*"))
            foreach (glob(ROOT . "{$path}/av{$uid}.*") as $av) {
                return "/avatars/users/" . basename($av);
            }
        return $noAvatar;

    }
}
