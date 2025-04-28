<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Settings\Facades;

use Boot\Support\Facades\Facade;
use Boot\System\Settings\SettingsManager;

/**
 * @method static Boot\System\Settings\SettingsManager get(string $key, $default)
 * @method static Boot\System\Settings\SettingsManager set(string $key, $value)
 */
class Settings extends Facade
{
    /**
     * Регистрация имени фасада
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return SettingsManager::class;
    }
}