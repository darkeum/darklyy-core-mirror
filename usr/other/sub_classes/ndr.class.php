<?php

class NDR{

    static function get(){
        $ndr = new static;
        return [
            "modal" => $ndr->modal_re(),
            "script" => $ndr->script_re(),
            "style" => $ndr->style_re(),
        ];
    }

    public function init(){
        $this->modal();
        $this->script();
        $this->style();
    }

    public function view_modal(){
        return '$("#reservListAllModal").modal("show");';
    }

    public function script_re(){
        $view_modal = $this->view_modal();
        return <<<HTML
            <script>
                async function view_ndr_modal(cid){
                    const reservListAll = document.getElementById("reservListAll");
                    reservListAll.innerHTML = "";
                    const form = new FormData();
                    form.append("cid", cid);
                    reservListAll.innerHTML = await ( await fetch("/ajax.php?do=getReservAllList", {
                        method: "POST",
                        body: form,
                    }) ).text();
                    {$view_modal}
                }
            </script>
HTML;
    }
    
    public function script(){
        global $adminTpl;
        $adminTpl->footIncludes[] = $this->script_re();
    }
    
    public function modal_re(){
        return <<<HTML
            <div class="modal fade" id="reservListAllModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Список всех резервов</h5>
                  </div>
                      <div class="modal-body" id="reservListAll">
    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
              </div>
            </div>
HTML;
    }
    
    public function modal(){
        echo $this->modal_re();
    }
    
    public function style_re(){
        return <<<HTML
        <style>
            .ndr{
                cursor: pointer;
            }
            #reservListAll>hr{
                margin-top: 10px;
                margin-bottom: 10px;
            }
        </style>
HTML;
    }
    
    public function style(){
        echo $this->style_re();
    }
}