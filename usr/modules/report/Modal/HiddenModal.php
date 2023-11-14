<?php

namespace Report\Modal;

class HiddenModal extends Modal{
    static function data(){
        return ["Скрытые", "Видимые",];
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
            <div class="modal fade" id="{$name}" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Выбор видимость</h5>
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

    static function condition_text(){
        $list = parent::condition_text();
        unset($list[13]);
        return $list;
    }

    static function getPrefix(){
        return "hidden";
    }

    static function getType(){
        return "select";
    }
}