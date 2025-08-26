<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Support\Facades;

/**
 * @method static void clearCategoryCache()
 * @method static Illuminate\Database\Eloquent\Collection<Boot\Models\Categories> getModuleCategories(string $module)
 * @method static Boot\Models\Categories getCategory(int $key)
 * @method static Boot\Models\Categories getCategoryBySlug(string $slug)
 * @method static Illuminate\Database\Eloquent\Collection<Boot\Models\Categories> getCategories()
 * @method static Illuminate\Database\Eloquent\Collection<Boot\Models\Categories> getChildrenCategories(int $parent_id)
 * @method static string getCategoryUrl(int $key, string | null $url = null)
 * @method static array getTree(string $module)
 * @method static array getNameTree(string $module)
 * @method static array getNameAndUrlTree(string $module)
 * @method static array breadcrumbs(int $key, array $breadcrumbs = [])
 *
 * @see \Boot\System\Categories\Categories
 */

class Categories extends Facade
{

    /**
     * Получение имя компонента.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'categories';
    }
}
