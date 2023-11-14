<?php

class ExtendedSort5 extends ExtendedSort{

    public function modal(){
        return <<<HTML
            <div class="modal fade" id="extendedSortModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg admin-form">
                <div class="modal-content">
                    <form id="extendedSortModalForm">
                        <input type="hidden" id="extendedSortModalLink" value="{$this->link}">
                        <input type="hidden" id="extendedSortModalLinkUpdate" value="{$this->link_update}">
                        <input type="hidden" id="extendedSortModalItemUpdate" value="{$this->item_update}">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Сортировка</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        </div>
                    </form>
                </div>
              </div>
            </div>
HTML;
    }

    public function script(){
        return $this->main_script().'<script src="/usr/other/sub_classes/extendedsort/assets/js/extendedsort_v5.js?'.$this->version.'"></script>';
    }
}