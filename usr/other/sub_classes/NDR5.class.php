<?php

class NDR5 extends NDR{
    public function modal_re(){
        return <<<HTML
            <div class="modal fade" id="reservListAllModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Список всех резервов</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body" id="reservListAll"></div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
            </div>
HTML;
    }

    public function view_modal(){
        return 'new bootstrap.Modal(document.getElementById(`reservListAllModal`)).show();';
    }
}