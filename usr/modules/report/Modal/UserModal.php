<?php

namespace Report\Modal;

class UserModal extends Modal{
    static function data(){
        return \Boot\Models\Users::where("active", 1)->where("group", 1)->orderBy("surname")->get();
    }

    static function template(){
        $name = static::getName();
        $element = static::getElement();
        $button = static::getButton();

        $option = [];
        foreach(static::data() as $item){
            $option[] = "<option value=\"{$item->id}\">{$item->short_user_name}</option>";
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
                    <h5 class="modal-title" id="exampleModalLabel">Выбор пользователя</h5>
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
      return "user";
    }

    static function getType(){
        return "select";
    }
}