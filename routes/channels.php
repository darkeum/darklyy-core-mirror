<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

use Illuminate\Support\Facades\Broadcast;

/*
|------------------------------------------------- -------------------------
| Каналы вещания
|------------------------------------------------- -------------------------
|
| Здесь вы можете зарегистрировать все каналы трансляции событий, которые у вас есть.
| Приложение поддерживает. Данные обратные вызовы авторизации канала
| используется для проверки того, может ли аутентифицированный пользователь прослушивать канал.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
