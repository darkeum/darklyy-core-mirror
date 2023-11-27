<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Permission\Commands;

use Boot\System\Console\Command;
use Boot\System\Permission\Contracts\Permission as PermissionContract;

class CreatePermission extends Command
{
    protected $signature = 'permission:create-permission
                {name : Имя разрешения}
                {guard? : Название защитника (WEB, API)}';

    protected $description = 'Создание разрешения';

    public function handle()
    {
        $permissionClass = app(PermissionContract::class);

        $permission = $permissionClass::findOrCreate($this->argument('name'), $this->argument('guard'));

        $this->info("Разрешение `{$permission->name}` ".($permission->wasRecentlyCreated ? 'создано' : 'уже существует'));
    }
}
