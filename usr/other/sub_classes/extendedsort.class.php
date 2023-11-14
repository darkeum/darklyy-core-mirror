<?php

class ExtendedSort{
    public $link;
    public $link_update;
    public $item_update;

    protected $version;

    public function __construct(){
        $this->version = time();
    }

    public function init(){
        echo $this->style();
        echo $this->modal();
        echo $this->script();
    }

    static function get_init(){
      $element = new static;
      return [
        "modal" => $element->modal(),
        "style" => $element->style(),
        "script" => $element->script(),
      ];
    }

    public function modal(){
        return <<<HTML
            <div class="modal fade" id="extendedSortModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg admin-form" role="document">
                <div class="modal-content">
                  <form id="extendedSortModalForm">
                    <input type="hidden" id="extendedSortModalLink" value="{$this->link}">
                    <input type="hidden" id="extendedSortModalLinkUpdate" value="{$this->link_update}">
                    <input type="hidden" id="extendedSortModalItemUpdate" value="{$this->item_update}">
                    <div class="modal-header">
                      <h5 class="modal-title">Сортировка</h5>
                    </div>
                    <div class="modal-body">
                      <div class="items_container">
                          <ul class="items_list" id="extendedSortModalSearchItems"></ul>
                          <ul class="items_list" id="extendedSortModalSort"></ul>
                          <div id="extendedSortModalTemplateBtn" class="d-grid">
                            <button type="button" class="btn btn-success btn-xs btn-block" id="extendedSortModalTemplateBtnSave"><i class="fas fa-save"></i></button>
                          </div>
                          <ul class="items_list" id="extendedSortModalTemplate"></ul>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Отсортировать</button>
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
HTML;
    }

    public function button($request = []){
        $class = empty($request["class"]) ? "" : $request["class"];
        return <<<HTML
            <i class="fas fa-sort-numeric-down text-system fs30 cp {$class}" id="extendedSortModalBtn" title="Сортировка"></i>
HTML;
    }

    public function style(){
        return '<link rel="stylesheet" href="/usr/other/sub_classes/extendedsort/assets/css/extendedsort.css?'.$this->version.'">';
    }
    public function main_script(){
      return '<script src="/usr/other/sub_classes/extendedsort/assets/js/extendedsort.js?'.$this->version.'"></script>';
    }
    public function script(){
      return $this->main_script().'<script src="/usr/other/sub_classes/extendedsort/assets/js/extendedsort_v3.js?'.$this->version.'"></script>';
    }

    public function get_category(){
      $cats_arr = \Catalog_v2\Model\Categories::where("module", "catalog_v2")->orderBy("name")->get()->toArray();

      $items = [
        "categories" => $cats_arr,
        "links" => [],
        "attribute" => [],
      ];
      foreach($cats_arr as $item){
        foreach(\Catalog_v2\Model\CatalogAtribute::whrre("cat", "LIKE", "%,{$item['id']},%")->get()->toArray() as $r){
          $items["attribute"][ $item["id"] ][] = $r;
        }
        $items["links"][ $item["parent_id"] ][] = $item;
      }
      return $items;
    }

    public function sort($request){
      $this->sort = empty($request["sort"]) ? [] : $request["sort"];
      $this->data = empty($request["data"]) ? [] : $request["data"];
      return $this->sort_items();
    }

    private function sort_items(){
      $result = [];

      if(count($this->sort) > 0 && count($this->data) > 0){
        $this->catalog_items();
        $this->sort_prepare();
        // dump($this->sort);
        $this->sort_logic();
        $result = $this->sort_cid_to_id();
      }

      return $result;
    }

    private function sort_prepare(){
      $this->sort = array_reverse($this->sort);
      $this->categories_children();
    }

    private function categories_children(){
      $new_sort = [];
      $categories = get_all_categories_catalog();
      $c_list_one = [];
      foreach($this->sort as $item){
        if($item["field"] == "category" && $item["type"] == "value"){
          $c_list_one[] = $item["value"];
        }
      }
      $c_list = [];
      foreach($this->sort as $item){
        if($item["field"] == "category" && $item["type"] == "value"){
          $category = $item["value"];
          $category_list = get_children_category($category, $categories);
          $category_list = array_diff($category_list, $c_list_one);
          $category_list[] = $category;
          foreach($category_list as $c){
            if(!in_array($c, $c_list)){
              $nn = $item;
              $nn["value"] = $c;
              $new_sort[] = $nn;
              $c_list[] = $c;
            }
          }
        } else {
          $new_sort[] = $item;
        }
      }
      $this->sort = $new_sort;
    }

    private function catalog_items(){
      $cid = [];
      $cid_id = [];
      foreach($this->data as $item){
        $cid[] = $item["cid"];
        $cid_id[ $item["cid"] ][] = $item["id"];
      }
      
      $this->items = [];
      foreach(\Catalog_v2\Model\Catalog::whereIn("id", $cid)->get()->toArray() as $r){
        foreach($cid_id[ $r["id"] ] as $i){
          $r["t_id"] = $i;
          $this->items[] = $r;
        }
      }
    }

    private function sort_logic(){
      $items = $this->items;
      $args = $this->sort;

      usort( $items, function( $a, $b ) use ( $args ){
        $res = 0;
    
        $a = (object) $a;
        $b = (object) $b;
    
        foreach( $args as $v ){
          $field = $v["field"];
          $value = $v["value"];
          if( $a->$field == $b->$field) continue;
            switch($v["type"]){
              case "field":
                $res = ( $a->$field < $b->$field ) ? -1 : 1;
                break;
              break;
              case "value":
                switch($field){
                  default:
                    $res = $a->$field == $value ? -1 : 1;
                  break;
                  case "category":
                    $value = ",{$value},";
                    // $res = $a->$field < $b->$field == $value ? -1 : 1;
                    if($a->$field == $value){
                      $res = -1;
                    }
                    if($b->$field == $value){
                      $res = 1;
                    }
                  break;
                  case "attribute":
                    try{
                      $attribute_a = json_decode($a->$field, true);
                    }catch(Exception $e){
                      $attribute_a = [];
                    }
                    try{
                      $attribute_b = json_decode($b->$field, true);
                    }catch(Exception $e){
                      $attribute_b = [];
                    }
                    $attribute_a_value = empty($attribute_a[ $value ]) ? "" : $attribute_a[ $value ];
                    $attribute_b_value = empty($attribute_b[ $value ]) ? "" : $attribute_b[ $value ];
                    $res = $attribute_a_value < $attribute_b_value ? -1 : 1;
                  break;
                }
              break;
              break;
            }
            if($v["order"] == 'DESC') $res= -$res;
        }
        return $res;
      } );
      $this->items = $items;
    }

    private function sort_cid_to_id(){
      $result = [];
      $data = $this->data;
      foreach($this->items as $item){
        foreach($data as $dk => $di){
          if($di["id"] == $item["t_id"]){
            $result[] = $di;
            unset($data[ $dk ]);
          } 
        }
      }
      return $result;
    }

    public function template_list(){
      global $core;
      $uid = $core->auth->user_info['id'];
      return \Boot\Models\ExtendedSortTemplate::where("uid", $uid)->where("common", 1)->orderByDesc("common")->get()->toArray();
    }

    public function template_add($request){
      $add = [];
      $add["name"] = $request["name"];
      $add["template"] = [];
      foreach($request["sort"] as $item){
        $add["template"][] = $item;
      }
      \Boot\Models\ExtendedSortTemplate::create($add);
    }
}