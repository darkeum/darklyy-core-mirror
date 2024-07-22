<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Categories;

use Boot\Support\Cache;
use Boot\Models\Categories as CategoryModel;

class Categories
{
  private $categoriesList;
  private $categoriesClearList;

  public function __construct()
  {
    if(Cache::has('categories.clear')) {
      $categoriesClearList = Cache::get('categories.clear');
    } else {
      $categoriesClearList = CategoryModel::all()->toArray();
      Cache::set('categories.clear', $categoriesClearList);
    }
    $prepareList = array();
    $prepareClearList = array();
    foreach ($categoriesClearList as $key => $value) {
      $prepareClearList[$value['id']] = $value;
      $prepareList[$value['module']][$value['id']] = $value;
    }
    $this->categoriesClearList = cc($prepareClearList);
    $this->categoriesList =  cc($prepareList);
  }

  public function getModuleCategories($module)
  {
    $category = $this->categoriesClearList->where('module', $module)->all();
    if (!empty($category)) {

      foreach ($category  as $k => $v) {
        $category[$k]['url'] = '/'.$module.'/'.$this->getCategoryFullUrl($k);
      }
      return $category;
      // dd($category);
    } else {
      return [];
    }
  }

  public function getCategory($key)
  {
    // dd($this->categoriesClearList);
    $category = $this->categoriesClearList->where('id', $key)->first();
    if (!empty($category)) {
      $category['url'] = $this->getCategoryFullUrl($key);
      // dd( $category);
      return cc($category);
    } else {
      return [];
    }
  }

  public function getCategoryFullUrl($key, $url = null)
  {
    //  dd($this->categoriesClearList);
    foreach ($this->categoriesClearList as $k => $category) {
      if ($k == $key) {
        if ($category['parent_id'] == '0') {
          return $category['altname'] . '/' . $url;
        } else {
          return $this->getCategoryFullUrl($category['parent_id'], $category['altname']) . '/' . $url;
        }
      }
    }
  }

  public function getCategoryBySlug($key)
  {
    // dd($this->categoriesList);
    foreach ($this->categoriesList as $categoryModule) {
      foreach ($categoryModule as $k => $category) {
        if (isset($category['altname'])) {
          // var_dump($category->get('altname'));
          if ($category['altname'] == $key) {
            return $k;
          }
        }
      }
    }
    return false;
  }
  /*
   * Список всех категорий
   */
  public function getCategories()
  {

   return $this->categoriesList;
  }

  /**
   * getFullTree - Список категорий в форме дерева
   *
   * @param  String $module
   * @return Array
   */
  public function getFullTree(String $module):Array{
      $items = CategoryModel::where("status", 1)->where("module", $module)->get();
			foreach($items as $item){
        $cat_get[ $item->id ] = [ $item->name, $item->parent_id ];
      }
			if(isset($cat_get)){
				foreach ($cat_get as $cid => $sub_arr){
					$cats_arr[$cid] = $sub_arr[0];
					$flag = $sub_arr[1];
					while ($flag != 0){
						$cats_arr[$cid] = $cat_get[$flag][0]." / ".$cats_arr[$cid];
						$flag = $cat_get[$flag][1];
					}
				}
				asort($cats_arr);
				return $cats_arr;
			}
		return [];
  }
  /*
   * хлебные крошки
   * $id - ID категории в которой мы находимся
   */
  public function breadcrumbs($id)
  {
    $breadcrumbs_array = [];
    $array = $this->categories;
    for ($i = 0; $i < count($array); $i++) {
      if ($id) {
        $breadcrumbs_array[$array[$id]['id']] = $array[$id]['title'];
        $id = $array[$id]['id_parent'];
      }
    }
    return array_reverse($breadcrumbs_array, true);
  }
  /*
   * @bonus
   * Получаем ID всех дочерных категорий родителя
   */
  public function getChildren($id_parent)
  {
    static $children = [];
    $result = $this->db->query("SELECT `id` FROM `categories` WHERE `id_parent` = '$id_parent'")->fetchAll();
    foreach ($result as $post) {
      $children[] = $post['id'];
      $this->getChildren($post['id']);
    }
    return $children;
  }




  public function getCategoriesList()
  {
    return $this->categoriesList;
  }
}
