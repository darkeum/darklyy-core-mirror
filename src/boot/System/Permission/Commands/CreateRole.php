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
use Boot\System\Permission\Contracts\Role as RoleContract;
use Boot\System\Permission\PermissionRegistrar;

class CreateRole extends Command
{
    protected $signature = 'permission:create-role
        {name : Имя роли}
        {guard? : Имя защитника}
        {permissions? : Список разрешений для роли, разделитель | }
        {--team-id=}';

    protected $description = 'Создание роли';

    public function handle(PermissionRegistrar $permissionRegistrar)
    {
        $roleClass = app(RoleContract::class);

        $teamIdAux = getPermissionsTeamId();
        setPermissionsTeamId($this->option('team-id') ?: null);

        if (!$permissionRegistrar->teams && $this->option('team-id')) {
            $this->warn('Функционал команд отключен, аргумент --team-id не имеет никакого эффекта. Либо включите команды его в файле конфигурации разрешений, либо удалите параметр --team-id.');
            return;
        }

        $role = $roleClass::findOrCreate($this->argument('name'), $this->argument('guard'));
        setPermissionsTeamId($teamIdAux);

        $teams_key = $permissionRegistrar->teamsKey;
        if ($permissionRegistrar->teams && $this->option('team-id') && is_null($role->$teams_key)) {
            $this->warn("Роль `{$role->name}` уже существует в глобальной команде; аргумент --team-id не имеет никакого эффекта");
        }

        $role->givePermissionTo($this->makePermissions($this->argument('permissions')));

        $this->info("Роль `{$role->name}` " . ($role->wasRecentlyCreated ? 'создана' : 'обновлена'));
    }

    /**
     * @param  array|null|string  $string
     */
    protected function makePermissions($string = null)
    {
        if (empty($string)) {
            return;
        }

        $permissionClass = app(PermissionContract::class);

        $permissions = explode('|', $string);

        $models = [];

        foreach ($permissions as $permission) {
            $models[] = $permissionClass::findOrCreate(trim($permission), $this->argument('guard'));
        }

        return collect($models);
    }
}
