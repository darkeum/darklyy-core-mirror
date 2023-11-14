<?php

namespace Report\Modal;

class CategoryModal extends Modal{
    static function data(){
        return \Boot\Support\Facades\Categories::getFullTree('catalog_v2');
    }

    static function template(){
        $name = static::getName();
        $element = static::getElement();
        $button = static::getButton();

        $option = [];
        $items = static::data();
        foreach($items as $id => $item){
            $option[] = "<option value=\",{$id},\">{$item}</option>";
        }
        $option = implode("", $option);

        return <<<HTML
            <script>
                $(function(){
                  $(`#{$element}`).select2();
                });
            </script>
            <div class="modal fade" id="{$name}" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Выбор категории</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <select class="form-select" id="{$element}">{$option}</select>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="{$button}">Установить</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
            </div>
HTML;
    }

    static function getPrefix(){
        return "category";
    }

    static function getType(){
        return "select";
    }

    static function final_query($query, $selection){
      $categories = get_all_categories_catalog();
      $all_cat = [];
      $values = $selection->value;
      if(!is_array($values)){
        $values = [$values];
      }
      foreach($values as $v){
        $cat = get_children_category(str_replace(",", "", $v), $categories);
        $all_cat = array_merge($cat, $all_cat);
      }
      $ww = [];
      foreach($all_cat as $v){
        $ww[] = ",{$v},";
      }
      return $query->where(function($query) use ($ww, $selection){
          foreach($ww as $w){
            $query->orWhere($selection->field, $w);
          }
      });
  }
}