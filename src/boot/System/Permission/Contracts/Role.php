<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Permission\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Role
{
    /**
     * Роли могут быть предоставлены различные разрешения.
     */
    public function permissions(): BelongsToMany;

    /**
     * Найдите роль по ее имени и имени охранника.
     *
     *
     * @throws \Boot\System\Permission\Exceptions\RoleDoesNotExist
     */
    public static function findByName(string $name, ?string $guardName): self;

    /**
     * Найдите роль по ее идентификатору и имени охранника.
     *
     *
     * @throws \Boot\System\Permission\Exceptions\RoleDoesNotExist
     */
    public static function findById(int|string $id, ?string $guardName): self;

    /**
     * Найдите или создайте роль по ее имени и имени охранника.
     */
    public static function findOrCreate(string $name, ?string $guardName): self;

    /**
     * Определите, может ли пользователь выполнить данное разрешение.
     *
     * @param  string|\Boot\System\Permission\Contracts\Permission  $permission
     */
    public function hasPermissionTo($permission, ?string $guardName): bool;
}
