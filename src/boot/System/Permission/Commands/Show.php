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
use Illuminate\Support\Collection;
use Boot\System\Permission\Contracts\Permission as PermissionContract;
use Boot\System\Permission\Contracts\Role as RoleContract;
use Symfony\Component\Console\Helper\TableCell;

class Show extends Command
{
    protected $signature = 'permission:show
    {guard? : Имя защитника}
    {style? : Стиль вывода (default|borderless|compact|box)}';

    protected $description = 'Показать таблицу ролей и разрешений';

    public function handle()
    {
        $permissionClass = app(PermissionContract::class);
        $roleClass = app(RoleContract::class);
        $teamsEnabled = config('permission.teams');
        $team_key = config('permission.column_names.team_foreign_key');

        $style = $this->argument('style') ?? 'default';
        $guard = $this->argument('guard');

        if ($guard) {
            $guards = Collection::make([$guard]);
        } else {
            $guards = $permissionClass::pluck('guard_name')->merge($roleClass::pluck('guard_name'))->unique();
        }

        foreach ($guards as $guard) {
            $this->info("Защитник: $guard");

            $roles = $roleClass::whereGuardName($guard)
                ->with('permissions')
                ->when($teamsEnabled, fn ($q) => $q->orderBy($team_key))
                ->orderBy('name')->get()->mapWithKeys(fn ($role) => [
                    $role->name . '_' . ($teamsEnabled ? ($role->$team_key ?: '') : '') => [
                        'permissions' => $role->permissions->pluck('id'),
                        $team_key => $teamsEnabled ? $role->$team_key : null,
                    ],
                ]);

            $permissions = $permissionClass::whereGuardName($guard)->orderBy('name')->pluck('name', 'id');

            $body = $permissions->map(
                fn ($permission, $id) => $roles->map(
                    fn (array $role_data) => $role_data['permissions']->contains($id) ? ' +' : ' -'
                )->prepend($permission)
            );

            if ($teamsEnabled) {
                $teams = $roles->groupBy($team_key)->values()->map(
                    fn ($group, $id) => new TableCell('ID команды: ' . ($id ?: 'NULL'), ['colspan' => $group->count()])
                );
            }

            $this->table(
                array_merge(
                    isset($teams) ? $teams->prepend(new TableCell(''))->toArray() : [],
                    $roles->keys()->map(function ($val) {
                        $name = explode('_', $val);
                        array_pop($name);

                        return implode('_', $name);
                    })
                        ->prepend(new TableCell(''))->toArray(),
                ),
                $body->toArray(),
                $style
            );
        }
    }
}
