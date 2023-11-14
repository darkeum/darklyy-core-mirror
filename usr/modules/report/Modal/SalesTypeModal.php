<?php

namespace Report\Modal;

use Sales\Models\Sales;

class SalesTypeModal extends Modal{
    static function data(){
        return Sales::getSalesType();
    }

    static function template(){
        $name = static::getName();
        $element = static::getElement();
        $button = static::getButton();

        $option = [];
        foreach(static::data() as $key => $item){
            $option[] = "<option value=\"{$key}\">{$item}</option>";
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
                    <h5 class="modal-title" id="exampleModalLabel">Выбор тип продажи</h5>
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
      return "salesType";
    }

    static function getType(){
        return "select";
    }
}