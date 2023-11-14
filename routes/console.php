<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

use Illuminate\Foundation\Inspiring;
use Boot\Support\Facades\Darksander;

/*
|------------------------------------------------- -------------------------
| Консольные маршруты
|------------------------------------------------- -------------------------
|
| В этом файле вы можете определить всю свою консоль на основе Closure.
| команды. Каждое замыкание привязано к экземпляру команды, позволяющему
| простой подход к взаимодействию с методами ввода-вывода каждой команды.
|
*/

Darksander::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
