<?php

//require_once("usr/modules/sales/admin/sub/ndr.php");

function ndr_init(){
    $ndr = new \NDR;
    $ndr->init();
    // ndr_modal();
    // ndr_script();
    // ndr_style();
}

function ndr_script_re(){
    $ndr = new \NDR;
    return $ndr->script_re();
//     return <<<HTML
//     <script>
//         function view_ndr_modal(cid){
//             $.post("/ajax.php?do=getReservAllList", {cid}, function(data){
//                 $("#reservListAll").html(data);
//                 $("#reservListAllModal").modal("show");
//             });
//         }
//     </script>
// HTML;
}

function ndr_script(){
    global $adminTpl;
    $adminTpl->footIncludes[] = ndr_script_re();
}

function ndr_modal_re(){
    $ndr = new \NDR;
    return $ndr->modal_re();
//     return <<<HTML
//         <div class="modal fade" id="reservListAllModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
//           <div class="modal-dialog" role="document">
//             <div class="modal-content">
//               <div class="modal-header">
//                 <h5 class="modal-title" id="exampleModalLabel">Список всех резервов</h5>
//               </div>
//               	<div class="modal-body" id="reservListAll">

//         		</div>
//         		<div class="modal-footer">
//         			<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
//         		</div>
//             </div>
//           </div>
//         </div>
// HTML;
}

function ndr_modal(){
    echo ndr_modal_re();
}

function ndr_style_re(){
    $ndr = new \NDR;
    return $ndr->style_re();
//     return <<<HTML
//     <style>
//         .ndr{
//             cursor: pointer;
//         }
//         #reservListAll>hr{
//             margin-top: 10px;
//             margin-bottom: 10px;
//         }
//     </style>
// HTML;
}

function ndr_style(){
    echo ndr_style_re();
}