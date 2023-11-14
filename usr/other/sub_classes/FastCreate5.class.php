<?php

class FastCreate5 extends FastCreate{
    public function button_open(){
        return 'data-bs-toggle="modal" data-bs-target="#'.$this->modal_id.'"';
    }

    public function modal(){
        return <<<HTML
            <div class="modal fade admin-form" id="{$this->modal_id}" tabindex="-1" aria-labelledby="{$this->modal_id}Label" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                    <form action="javascript:void(null);" onsubmit="{$this->func_name_submit}();" id="{$this->modal_id}_form" method="POST">
                        <div class="modal-header">
                          <h5 class="modal-title" id="{$this->modal_id}Label">{$this->modal_title}</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          {$this->modal_body}
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-primary">Создать</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        </div>
                    </form>
                </div>
              </div>
            </div>
HTML;
    }
}