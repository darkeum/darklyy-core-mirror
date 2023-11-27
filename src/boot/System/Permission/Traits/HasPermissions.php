<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Permission\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Boot\System\Permission\Contracts\Permission;
use Boot\System\Permission\Contracts\Role;
use Boot\System\Permission\Contracts\Wildcard;
use Boot\System\Permission\Exceptions\GuardDoesNotMatch;
use Boot\System\Permission\Exceptions\PermissionDoesNotExist;
use Boot\System\Permission\Exceptions\WildcardPermissionInvalidArgument;
use Boot\System\Permission\Exceptions\WildcardPermissionNotImplementsContract;
use Boot\System\Permission\Guard;
use Boot\System\Permission\PermissionRegistrar;
use Boot\System\Permission\WildcardPermission;

trait HasPermissions
{
    private ?string $permissionClass = null;

    private ?string $wildcardClass = null;

    private array $wildcardPermissionsIndex;

    public static function bootHasPermissions()
    {
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
                return;
            }

            $teams = app(PermissionRegistrar::class)->teams;
            app(PermissionRegistrar::class)->teams = false;
            if (!is_a($model, Permission::class)) {
                $model->permissions()->detach();
            }
            if (is_a($model, Role::class)) {
                $model->users()->detach();
            }
            app(PermissionRegistrar::class)->teams = $teams;
        });
    }

    public function getPermissionClass(): string
    {
        if (!$this->permissionClass) {
            $this->permissionClass = app(PermissionRegistrar::class)->getPermissionClass();
        }

        return $this->permissionClass;
    }

    public function getWildcardClass()
    {
        if (!is_null($this->wildcardClass)) {
            return $this->wildcardClass;
        }

        $this->wildcardClass = '';

        if (config('permission.enable_wildcard_permission')) {
            $this->wildcardClass = config('permission.wildcard_permission', WildcardPermission::class);

            if (!is_subclass_of($this->wildcardClass, Wildcard::class)) {
                throw WildcardPermissionNotImplementsContract::create();
            }
        }

        return $this->wildcardClass;
    }

    /**
     * Модель может иметь несколько прямых разрешений.
     */
    public function permissions(): BelongsToMany
    {
        $relation = $this->morphToMany(
            config('permission.models.permission'),
            'model',
            config('permission.table_names.model_has_permissions'),
            config('permission.column_names.model_morph_key'),
            app(PermissionRegistrar::class)->pivotPermission
        );

        if (!app(PermissionRegistrar::class)->teams) {
            return $relation;
        }

        return $relation->wherePivot(app(PermissionRegistrar::class)->teamsKey, getPermissionsTeamId());
    }

    /**
     * Ограничьте запрос модели только определенными разрешениями.
     *
     * @param  string|int|array|Permission|Collection|\BackedEnum  $permissions
     * @param  bool  $without
     */
    public function scopePermission(Builder $query, $permissions, $without = false): Builder
    {
        $permissions = $this->convertToPermissionModels($permissions);

        $permissionKey = (new ($this->getPermissionClass())())->getKeyName();
        $roleKey = (new (is_a($this, Role::class) ? static::class : $this->getRoleClass())())->getKeyName();

        $rolesWithPermissions = is_a($this, Role::class) ? [] : array_unique(
            array_reduce($permissions, fn ($result, $permission) => array_merge($result, $permission->roles->all()), [])
        );

        return $query->where(
            fn (Builder $query) => $query
                ->{!$without ? 'whereHas' : 'whereDoesntHave'}(
                    'permissions',
                    fn (Builder $subQuery) => $subQuery
                        ->whereIn(config('permission.table_names.permissions') . ".$permissionKey", \array_column($permissions, $permissionKey))
                )
                ->when(
                    count($rolesWithPermissions),
                    fn ($whenQuery) => $whenQuery
                        ->{!$without ? 'orWhereHas' : 'whereDoesntHave'}(
                            'roles',
                            fn (Builder $subQuery) => $subQuery
                                ->whereIn(config('permission.table_names.roles') . ".$roleKey", \array_column($rolesWithPermissions, $roleKey))
                        )
                )
        );
    }

    /**
     * Охватите запрос модели только теми, у кого нет определенных разрешений,
     * косвенно по роли или по прямому разрешению.
     *
     * @param  string|int|array|Permission|Collection|\BackedEnum  $permissions
     */
    public function scopeWithoutPermission(Builder $query, $permissions): Builder
    {
        return $this->scopePermission($query, $permissions, true);
    }

    /**
     * @param  string|int|array|Permission|Collection|\BackedEnum  $permissions
     *
     * @throws PermissionDoesNotExist
     */
    protected function convertToPermissionModels($permissions): array
    {
        if ($permissions instanceof Collection) {
            $permissions = $permissions->all();
        }

        return array_map(function ($permission) {
            if ($permission instanceof Permission) {
                return $permission;
            }

            if ($permission instanceof \BackedEnum) {
                $permission = $permission->value;
            }

            $method = is_int($permission) || PermissionRegistrar::isUid($permission) ? 'findById' : 'findByName';

            return $this->getPermissionClass()::{$method}($permission, $this->getDefaultGuardName());
        }, Arr::wrap($permissions));
    }

    /**
     * Найдите разрешение.
     *
     * @param  string|int|Permission|\BackedEnum  $permission
     * @return Permission
     *
     * @throws PermissionDoesNotExist
     */
    public function filterPermission($permission, $guardName = null)
    {
        if ($permission instanceof \BackedEnum) {
            $permission = $permission->value;
        }

        if (is_int($permission) || PermissionRegistrar::isUid($permission)) {
            $permission = $this->getPermissionClass()::findById(
                $permission,
                $guardName ?? $this->getDefaultGuardName()
            );
        }

        if (is_string($permission)) {
            $permission = $this->getPermissionClass()::findByName(
                $permission,
                $guardName ?? $this->getDefaultGuardName()
            );
        }

        if (!$permission instanceof Permission) {
            throw new PermissionDoesNotExist();
        }

        return $permission;
    }

    /**
     * Определите, может ли модель выполнить данное разрешение.
     *
     * @param  string|int|Permission|\BackedEnum  $permission
     * @param  string|null  $guardName
     *
     * @throws PermissionDoesNotExist
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        if ($this->getWildcardClass()) {
            return $this->hasWildcardPermission($permission, $guardName);
        }

        $permission = $this->filterPermission($permission, $guardName);

        return $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission);
    }

    /**
     * Сравнивает разрешение с подстановочным знаком со всеми разрешениями пользователя.
     *
     * @param  string|int|Permission|\BackedEnum  $permission
     * @param  string|null  $guardName
     */
    protected function hasWildcardPermission($permission, $guardName = null): bool
    {
        $guardName = $guardName ?? $this->getDefaultGuardName();

        if ($permission instanceof \BackedEnum) {
            $permission = $permission->value;
        }

        if (is_int($permission) || PermissionRegistrar::isUid($permission)) {
            $permission = $this->getPermissionClass()::findById($permission, $guardName);
        }

        if ($permission instanceof Permission) {
            $permission = $permission->name;
        }

        if (!is_string($permission)) {
            throw WildcardPermissionInvalidArgument::create();
        }

        return app($this->getWildcardClass(), ['record' => $this])->implies(
            $permission,
            $guardName,
            app(PermissionRegistrar::class)->getWildcardPermissionIndex($this),
        );
    }

    /**
     * Псевдоним метода hasPermissionTo(), позволяющий избежать создания исключения.
     *
     * @param  string|int|Permission|\BackedEnum  $permission
     * @param  string|null  $guardName
     */
    public function checkPermissionTo($permission, $guardName = null): bool
    {
        try {
            return $this->hasPermissionTo($permission, $guardName);
        } catch (PermissionDoesNotExist $e) {
            return false;
        }
    }

    /**
     * Определите, имеет ли модель какие-либо из указанных разрешений.
     *
     * @param  string|int|array|Permission|Collection|\BackedEnum  ...$permissions
     */
    public function hasAnyPermission(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten();

        foreach ($permissions as $permission) {
            if ($this->checkPermissionTo($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Определите, имеет ли модель все указанные разрешения.
     *
     * @param  string|int|array|Permission|Collection|\BackedEnum  ...$permissions
     */
    public function hasAllPermissions(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten();

        foreach ($permissions as $permission) {
            if (!$this->checkPermissionTo($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Определите, имеет ли модель через роли данное разрешение.
     */
    protected function hasPermissionViaRole(Permission $permission): bool
    {
        if (is_a($this, Role::class)) {
            return false;
        }

        return $this->hasRole($permission->roles);
    }

    /**
     * Определите, имеет ли модель данное разрешение.
     *
     * @param  string|int|Permission|\BackedEnum  $permission
     *
     * @throws PermissionDoesNotExist
     */
    public function hasDirectPermission($permission): bool
    {
        $permission = $this->filterPermission($permission);

        return $this->permissions->contains($permission->getKeyName(), $permission->getKey());
    }

    /**
     * Верните все разрешения, которые модель имеет через роли.
     */
    public function getPermissionsViaRoles(): Collection
    {
        if (is_a($this, Role::class) || is_a($this, Permission::class)) {
            return collect();
        }

        return $this->loadMissing('roles', 'roles.permissions')
            ->roles->flatMap(fn ($role) => $role->permissions)
            ->sort()->values();
    }

    /**
     * Верните все разрешения, которые имеет модель, как напрямую, так и через роли.
     */
    public function getAllPermissions(): Collection
    {
        /** @var Collection $permissions */
        $permissions = $this->permissions;

        if (method_exists($this, 'roles')) {
            $permissions = $permissions->merge($this->getPermissionsViaRoles());
        }

        return $permissions->sort()->values();
    }

    /**
     * Возвращает идентификаторы разрешений в виде ключей массива
     *
     * @param  string|int|array|Permission|Collection|\BackedEnum  $permissions
     */
    private function collectPermissions(...$permissions): array
    {
        return collect($permissions)
            ->flatten()
            ->reduce(function ($array, $permission) {
                if (empty($permission)) {
                    return $array;
                }

                $permission = $this->getStoredPermission($permission);
                if (!$permission instanceof Permission) {
                    return $array;
                }

                $this->ensureModelSharesGuard($permission);

                $array[] = $permission->getKey();

                return $array;
            }, []);
    }

    /**
     * Предоставьте заданные разрешения роли.
     *
     * @param  string|int|array|Permission|Collection|\BackedEnum  $permissions
     * @return $this
     */
    public function givePermissionTo(...$permissions)
    {
        $permissions = $this->collectPermissions($permissions);

        $model = $this->getModel();
        $teamPivot = app(PermissionRegistrar::class)->teams && !is_a($this, Role::class) ?
            [app(PermissionRegistrar::class)->teamsKey => getPermissionsTeamId()] : [];

        if ($model->exists) {
            $currentPermissions = $this->permissions->map(fn ($permission) => $permission->getKey())->toArray();

            $this->permissions()->attach(array_diff($permissions, $currentPermissions), $teamPivot);
            $model->unsetRelation('permissions');
        } else {
            $class = \get_class($model);

            $class::saved(
                function ($object) use ($permissions, $model, $teamPivot) {
                    if ($model->getKey() != $object->getKey()) {
                        return;
                    }
                    $model->permissions()->attach($permissions, $teamPivot);
                    $model->unsetRelation('permissions');
                }
            );
        }

        if (is_a($this, Role::class)) {
            $this->forgetCachedPermissions();
        }

        $this->forgetWildcardPermissionIndex();

        return $this;
    }

    public function forgetWildcardPermissionIndex(): void
    {
        app(PermissionRegistrar::class)->forgetWildcardPermissionIndex(
            is_a($this, Role::class) ? null : $this,
        );
    }

    /**
     * Удалите все текущие разрешения и установите заданные.
     *
     * @param  string|int|array|Permission|Collection|\BackedEnum  $permissions
     * @return $this
     */
    public function syncPermissions(...$permissions)
    {
        if ($this->getModel()->exists) {
            $this->collectPermissions($permissions);
            $this->permissions()->detach();
            $this->setRelation('permissions', collect());
        }

        return $this->givePermissionTo($permissions);
    }

    /**
     * Отозвать данное разрешение(я).
     *
     * @param  Permission|Permission[]|string|string[]|\BackedEnum  $permission
     * @return $this
     */
    public function revokePermissionTo($permission)
    {
        $this->permissions()->detach($this->getStoredPermission($permission));

        if (is_a($this, Role::class)) {
            $this->forgetCachedPermissions();
        }

        $this->forgetWildcardPermissionIndex();

        $this->unsetRelation('permissions');

        return $this;
    }

    public function getPermissionNames(): Collection
    {
        return $this->permissions->pluck('name');
    }

    /**
     * @param  string|int|array|Permission|Collection|\BackedEnum  $permissions
     * @return Permission|Permission[]|Collection
     */
    protected function getStoredPermission($permissions)
    {
        if ($permissions instanceof \BackedEnum) {
            $permissions = $permissions->value;
        }

        if (is_int($permissions) || PermissionRegistrar::isUid($permissions)) {
            return $this->getPermissionClass()::findById($permissions, $this->getDefaultGuardName());
        }

        if (is_string($permissions)) {
            return $this->getPermissionClass()::findByName($permissions, $this->getDefaultGuardName());
        }

        if (is_array($permissions)) {
            $permissions = array_map(function ($permission) {
                if ($permission instanceof \BackedEnum) {
                    return $permission->value;
                }

                return is_a($permission, Permission::class) ? $permission->name : $permission;
            }, $permissions);

            return $this->getPermissionClass()::whereIn('name', $permissions)
                ->whereIn('guard_name', $this->getGuardNames())
                ->get();
        }

        return $permissions;
    }

    /**
     * @param  Permission|Role  $roleOrPermission
     *
     * @throws GuardDoesNotMatch
     */
    protected function ensureModelSharesGuard($roleOrPermission)
    {
        if (!$this->getGuardNames()->contains($roleOrPermission->guard_name)) {
            throw GuardDoesNotMatch::create($roleOrPermission->guard_name, $this->getGuardNames());
        }
    }

    protected function getGuardNames(): Collection
    {
        return Guard::getNames($this);
    }

    protected function getDefaultGuardName(): string
    {
        return Guard::getDefaultName($this);
    }

    /**
     * Забудьте о кэшированных разрешениях.
     */
    public function forgetCachedPermissions()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Проверьте, имеет ли модель все запрошенные разрешения Direct.
     *
     * @param  string|int|array|Permission|Collection|\BackedEnum  ...$permissions
     */
    public function hasAllDirectPermissions(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten();

        foreach ($permissions as $permission) {
            if (!$this->hasDirectPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Проверьте, имеет ли модель какое-либо из запрошенных разрешений Direct.
     *
     * @param  string|int|array|Permission|Collection|\BackedEnum  ...$permissions
     */
    public function hasAnyDirectPermission(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten();

        foreach ($permissions as $permission) {
            if ($this->hasDirectPermission($permission)) {
                return true;
            }
        }

        return false;
    }
}
