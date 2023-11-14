<?php
global $adminTpl, $config, $core, $admin_conf, $db, $lang;	

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

function selection_group(){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;	
    $lnn = 'Блок';
	$adminTpl->admin_head('Список группы блоков');
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'selection';
    $atributs['module_open'] = 'group';
    $atributs['button'] = 'Добавить группу';
	$atributs['button_url'] = '';	
    $atributs['button_onclick'] = "addBlock();";
    load_grid('jqSelectionGroup', $lnn, $atributs, ROOT.'/usr/modules/selection/admin/grid/');
    
    $adminTpl->footIncludes[] = '
        <script>
            function addBlock(){
                //console.log("~");
                $("#editBlock").remove();
                $("#code").val("");
                $("#files1").html("");
                $("#debug1").html("");
                $("#addBlockBtn").html("Добавить");
                $("#addBlockModal").modal("show");
            }
            $(document).on("click", "#addBlockBtn", function(){
                //console.log("~");
                let data = $("#addBlockForm").serialize();
                $.post("/ajax.php?do=addBlockSelection", data, function(){
                    $("#jqSelectionGroup").trigger("reloadGrid");
                    $("#code").val("");
                    $("#files1").html("");
                    $("#debug1").html("");
                });
            });

            $(document).on("click", ".editBlock", function(){
                let id = $(this).data("id");
                let code = $(this).data("code");
                $("#code").val(code);
                $("#addBlockBtn").html("Изменить");
                $("#addBlockModal").modal("show");
                $("#addBlockForm").append(\'<input type="hidden" name="edit" value="\'+id+\'" id="editBlock">\');
            });
        </script>
    ';

    ?>

<div class="modal fade" id="addBlockModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавить группу</h5>
            </div>
            <div class="modal-body">
                <form id="addBlockForm">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Код</label>
                            <input type="text" class="form-control" placeholder="Введите код группы" name="code" id="code">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <label>DWG схема</label>
                            <?php
                                $attribute = [];
                                $attribute["name"] = "1";
                                $attribute["script"] = true;
                                $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
                                $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
                                echo uploaderMaster("css", $attribute);
                                echo uploaderMaster("upload", $attribute);
                                echo uploaderMaster("view", $attribute);
                            ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="addBlockBtn">Добавить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<?php

	$adminTpl->admin_foot();
}