<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Categories;

use Boot\Models\Categories as CategoryModel;
use Boot\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class Categories
{
    const CACHE_KEY = 'categories.clear2';

    /**
     * Список всех категорий
     *
     * @var Collection<CategoryModel>
     */
    private Collection $categories_list;

    /**
     * Конструктор класса
     *
     * @return void
     */
    public function __construct()
    {
        $this->categories_list = $this->getCategoryCache();
    }

    /**
     * Получить кэш категорий
     *
     * @return Collection<CategoryModel>
     */
    public function getCategoryCache(): Collection
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return CategoryModel::all();
        });
    }

    /**
     * Очистить кэш категорий
     *
     * @return void
     */
    public function clearCategoryCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Получить категории модуля
     *
     * @param  string $module Наименованеи модуля
     * @return  Collection<CategoryModel>
     */
    public function getModuleCategories(string $module): Collection
    {
        return $this->categories_list->where('module', $module);
    }

    /**
     * Получить категорию по ее идетификатору
     *
     * @param  int $key Идетификатор категории
     * @return CategoryModel|null
     */
    public function getCategory(int $key): CategoryModel | null
    {
        return $this->categories_list->where('id', $key)->first();
    }

    /**
     * Получить категорию по ее slug
     *
     * @param  string $key Slug категории
     * @return CategoryModel|null
     */
    public function getCategoryBySlug(string $slug): CategoryModel | null
    {
        return $this->categories_list->where('altname', $slug)->first();
    }

    /**
     * Получить список всех категорий
     *
     * @return Collection<CategoryModel>
     */
    public function getCategories(): Collection
    {
        return $this->categories_list;
    }

    /**
     * Получить дочернии категории
     *
     * @param   int $parent_id Идетификатор родительской категории
     * @return  Collection<CategoryModel>
     */
    public function getChildrenCategories(int $parent_id): Collection
    {
        return $this->categories_list->where('parent_id', $parent_id);
    }

    /**
     * Получение полного URL категории
     *
     * @param  int $key Идетификатор категории
     * @param  string|null $url
     * @return string
     */
    public function getCategoryUrl(int $key, string | null $url = null): string
    {
        $category = $this->getCategory($key);
        if ($category->parent_id == '0' || $category->parent_id == null) {
            return $category->altname . ($url != null ? ('/' . $url) : '');
        } else {
            return $this->getCategoryUrl($category->parent_id, $category->altname) . ($url != null ? ('/' . $url) : '');
        }
    }

    /**
     * Список категорий в форме дерева
     *
     * @param  string $module Наименование модуля
     * @return array
     */
    public function getTree(string $module): array
    {
        foreach ($this->categories_list->where("module", $module) as $item) {
            $cat_get[$item->id] = $item;
        }
        if (isset($cat_get)) {
            foreach ($cat_get as $key => $value) {
                $cats_arr[$key]['name']       = $value->name;
                $cats_arr[$key]['category'][] = $value;
                $flag                         = $value->parent_id;
                while ($flag != 0 || $flag != null) {
                    $cats_arr[$key]['name']       = $cat_get[$flag]->name . " / " . $cats_arr[$key]['name'];
                    $cats_arr[$key]['category'][] = $cat_get[$flag];
                    $flag                         = $cat_get[$flag]->parent_id;
                }
                $cats_arr[$key]['category'] = array_reverse($cats_arr[$key]['category']);
            }
            asort($cats_arr);
            return $cats_arr;
        }
        return [];
    }

    /**
     * Список категорий в форме дерева только имя
     *
     * @param  string $module Наименование модуля
     * @return array
     */
    public function getNameTree(string $module): array
    {
        $result = [];
        foreach ($this->getTree($module) as $key => $item) {
            $result[$key] = $item['name'];
        }
        return $result;
    }

    /**
     * Список категорий в форме дерева только имя и URL
     *
     * @param  string $module Наименование модуля
     * @return array
     */
    public function getNameAndUrlTree(string $module): array
    {
        $result = [];
        foreach ($this->getTree($module) as $key => $item) {
            $result[$key]['name'] = $item['name'];
            $result[$key]['url']  = $this->getCategoryUrl($key);
        }
        return $result;
    }

    /**
     * Хлебные крошки для указанной категории
     *
     * @param  int $id Идетификато ркатегории
     * @return array
     */
    public function breadcrumbs(int $key, array $breadcrumbs = []): array
    {
        $category      = $this->getCategory($key);
        $breadcrumbs[] = $category;
        if ($category->parent_id == '0' || $category->parent_id == null) {
            return $breadcrumbs;
        } else {
            return $this->breadcrumbs($category->parent_id, $breadcrumbs);
        }
    }

}
