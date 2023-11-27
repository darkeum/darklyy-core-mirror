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

interface Permission
{
    /**
     * Разрешение можно применить к ролям.
     */
    public function roles(): BelongsToMany;

    /**
     * Найдите разрешение по его названию.
     *
     *
     * @throws \Boot\System\Permission\Exceptions\PermissionDoesNotExist
     */
    public static function findByName(string $name, ?string $guardName): self;

    /**
     * Найдите разрешение по его идентификатору.
     *
     *
     * @throws \Boot\System\Permission\Exceptions\PermissionDoesNotExist
     */
    public static function findById(int|string $id, ?string $guardName): self;

    /**
     * Найдите или создайте разрешение по его имени и имени охранника.
     */
    public static function findOrCreate(string $name, ?string $guardName): self;
}
