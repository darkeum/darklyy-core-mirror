<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Support\Facades;

/**
 * @method static array getModuleCategories($module)
 * @method static mixed getCategory($key)
 * @method static mixed getCategoryFullUrl($key, $url = null)
 * @method static mixed getCategoryBySlug($key)
 * @method static mixed getCategories()
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
