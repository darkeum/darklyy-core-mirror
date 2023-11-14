<?php

namespace Database\Seeders;

use \Boot\System\Database\Seeder;
use \Boot\Models\Groups;

class GroupsSeeder extends Seeder
{
    /**
     * Запуск заполнения таблиц
     *
     * @return void
     */
    public function run()
    {
        Groups::firstOrCreate(['id' => 1,], ['name' =>'Сотрудники',        'guest' => 1, 'user' => 1, 'c' => 1, 'admin' => 1, 'banned' => 0, 'showHide' => 0, 'showAttach' => 1, 'loadAttach' => 1, 'addPost' => 1, 'addComment' => 1, 'allowRating' => 1, 'maxWidth' => 100, 'maxPms' => 50, 'control' => '', 'icon' => 'media/groups/administrator.png', 'color' => 'red',               'points' => 0, 'protect' => 1, 'special' => 0]);
        Groups::firstOrCreate(['id' => 3,], ['name' =>'Гости',             'guest' => 1, 'user' => 0, 'c' => 0, 'admin' => 0, 'banned' => 0, 'showHide' => 0, 'showAttach' => 1, 'loadAttach' => 0, 'addPost' => 1, 'addComment' => 1, 'allowRating' => 1, 'maxWidth' => 100, 'maxPms' => 50, 'control' => '', 'icon' => 'media/groups/3.png',             'color' => '',                  'points' => 0, 'protect' => 0, 'special' => 0]);
        Groups::firstOrCreate(['id' => 4,], ['name' =>'Боты',              'guest' => 1, 'user' => 0, 'c' => 0, 'admin' => 0, 'banned' => 0, 'showHide' => 0, 'showAttach' => 0, 'loadAttach' => 0, 'addPost' => 0, 'addComment' => 0, 'allowRating' => 0, 'maxWidth' => 0,   'maxPms' => 0,  'control' => '', 'icon' => 'media/groups/4.png',             'color' => '',                  'points' => 0, 'protect' => 1, 'special' => 0]);
        Groups::firstOrCreate(['id' => 2,], ['name' =>'Пользователи',      'guest' => 1, 'user' => 1, 'c' => 0, 'admin' => 0, 'banned' => 0, 'showHide' => 0, 'showAttach' => 1, 'loadAttach' => 0, 'addPost' => 1, 'addComment' => 1, 'allowRating' => 1, 'maxWidth' => 100, 'maxPms' => 50, 'control' => '', 'icon' => 'media/groups/user.png',          'color' => 'blue',              'points' => 0, 'protect' => 1, 'special' => 0]);
        Groups::firstOrCreate(['id' => 5,], ['name' =>'Забаненные',        'guest' => 0, 'user' => 0, 'c' => 0, 'admin' => 0, 'banned' => 1, 'showHide' => 0, 'showAttach' => 0, 'loadAttach' => 0, 'addPost' => 0, 'addComment' => 0, 'allowRating' => 0, 'maxWidth' => 0,   'maxPms' => 0,  'control' => '', 'icon' => '',                               'color' => '',                  'points' => 0, 'protect' => 0, 'special' => 0]);
        Groups::firstOrCreate(['id' => 6,], ['name' =>'Клиенты',           'guest' => 1, 'user' => 1, 'c' => 0, 'admin' => 0, 'banned' => 0, 'showHide' => 1, 'showAttach' => 1, 'loadAttach' => 0, 'addPost' => 0, 'addComment' => 1, 'allowRating' => 1, 'maxWidth' => 640, 'maxPms' => 50, 'control' => '', 'icon' => 'media/groups/',                  'color' => 'rgba(150,71,71,1)', 'points' => 0, 'protect' => 0, 'special' => 0]);
        Groups::firstOrCreate(['id' => 8,], ['name' =>'Администраторы',    'guest' => 1, 'user' => 1, 'c' => 1, 'admin' => 1, 'banned' => 0, 'showHide' => 1, 'showAttach' => 1, 'loadAttach' => 0, 'addPost' => 0, 'addComment' => 1, 'allowRating' => 1, 'maxWidth' => 640, 'maxPms' => 50, 'control' => '', 'icon' => 'media/groups/',                  'color' => 'rgba(255,0,0,1)',   'points' => 0, 'protect' => 0, 'special' => 1]);
    }
}
