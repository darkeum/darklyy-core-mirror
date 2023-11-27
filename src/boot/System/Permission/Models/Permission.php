<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Permission\Models;

use Illuminate\Database\Eloquent\Collection;
use Boot\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Boot\System\Permission\Contracts\Permission as PermissionContract;
use Boot\System\Permission\Exceptions\PermissionAlreadyExists;
use Boot\System\Permission\Exceptions\PermissionDoesNotExist;
use Boot\System\Permission\Guard;
use Boot\System\Permission\PermissionRegistrar;
use Boot\System\Permission\Traits\HasRoles;
use Boot\System\Permission\Traits\RefreshesPermissionCache;

/**
 * @property ?\Boot\Support\Carbon $created_at
 * @property ?\Boot\Support\Carbon $updated_at
 */
class Permission extends Model implements PermissionContract
{
    use HasRoles;
    use RefreshesPermissionCache;

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        parent::__construct($attributes);

        $this->guarded[] = $this->primaryKey;
        $this->table = config('permission.table_names.permissions') ?: parent::getTable();
    }

    /**
     * @return PermissionContract|Permission
     *
     * @throws PermissionAlreadyExists
     */
    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

        $permission = static::getPermission(['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']]);

        if ($permission) {
            throw PermissionAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        return static::query()->create($attributes);
    }

    /**
     * Разрешение можно применить к ролям.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions'),
            app(PermissionRegistrar::class)->pivotPermission,
            app(PermissionRegistrar::class)->pivotRole
        );
    }

    /**
     * Разрешение принадлежит некоторым пользователям модели, связанным с ее охранником.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name'] ?? config('auth.defaults.guard')),
            'model',
            config('permission.table_names.model_has_permissions'),
            app(PermissionRegistrar::class)->pivotPermission,
            config('permission.column_names.model_morph_key')
        );
    }

    /**
     * Найдите разрешение по его имени (и, при необходимости, GuardName).
     *
     * @return PermissionContract|Permission
     *
     * @throws PermissionDoesNotExist
     */
    public static function findByName(string $name, string $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['name' => $name, 'guard_name' => $guardName]);
        if (! $permission) {
            throw PermissionDoesNotExist::create($name, $guardName);
        }

        return $permission;
    }

    /**
     * Найдите разрешение по его идентификатору (и, при необходимости, GuardName).
     *
     * @return PermissionContract|Permission
     *
     * @throws PermissionDoesNotExist
     */
    public static function findById(int|string $id, string $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission([(new static())->getKeyName() => $id, 'guard_name' => $guardName]);

        if (! $permission) {
            throw PermissionDoesNotExist::withId($id, $guardName);
        }

        return $permission;
    }

    /**
     * Найдите или создайте разрешение по его имени (и, возможно, GuardName).
     *
     * @return PermissionContract|Permission
     */
    public static function findOrCreate(string $name, string $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['name' => $name, 'guard_name' => $guardName]);

        if (! $permission) {
            return static::query()->create(['name' => $name, 'guard_name' => $guardName]);
        }

        return $permission;
    }

    /**
     * Получите текущие кэшированные разрешения.
     */
    protected static function getPermissions(array $params = [], bool $onlyOne = false): Collection
    {
        return app(PermissionRegistrar::class)
            ->setPermissionClass(static::class)
            ->getPermissions($params, $onlyOne);
    }

    /**
     * Получите текущее кэшированное первое разрешение.
     *
     * @return PermissionContract|Permission|null
     */
    protected static function getPermission(array $params = []): ?PermissionContract
    {
        /** @var PermissionContract|null */
        return static::getPermissions($params, true)->first();
    }
}
