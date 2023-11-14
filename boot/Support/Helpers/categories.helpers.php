<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

use Boot\Support\Facades\Categories;

if (! function_exists('get_module_categories')) {
 
    function get_module_categories($module)
    {
       return  Categories::getModuleCategories($module);
    }
}

if (! function_exists('get_category')) {
 
    function get_category($key)
    {
       return  Categories::getCategory($key);
    }
}

if (! function_exists('get_category_full_url')) {
 
    function get_category_full_url($key, $url = null)
    {
       return  Categories::getCategoryFullUrl($key, $url);
    }
}

if (! function_exists('get_category_by_slug')) {
 
    function get_category_by_slug($slug)
    {
       return  Categories::getCategoryBySlug($slug);
    }
}

if (! function_exists('get_categories')) {
 
    function get_categories()
    {
       return  Categories::getCategories();
    }
}
