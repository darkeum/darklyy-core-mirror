<?php
global $adminTpl, $config, $core, $admin_conf, $db, $lang;

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

function docPrintMain(){
    global $adminTpl, $core, $db, $lang;
    $lnn = "Разрешить отгрузку";
	$adminTpl->admin_head($lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'sales';
	$atributs['module_open'] = 'order';
	//$atributs['js'] = '  setTimeout(function () {$("#jqDocPrint").jqGrid(\'setGridParam\',{postData:{status: \'1\'}}); $(\'#jqStorageMain\').trigger(\'reloadGrid\');}, 150);';	
    load_grid('jqDocPrint', $lnn, $atributs, ROOT.'/usr/modules/storage/admin/grid/');
    $adminTpl->footIncludes[] = '<script src="/usr/modules/storage/admin/asset/js/docPrint.js"></script>';
	$adminTpl->admin_foot();
}

function docPrint(){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $adminTpl->admin_head('Документы на печать');
    ?>
    <style>
        .row{
            margin-bottom: 20px;
        }
    </style>

<div id="content" class="animated fadeIn">
    <div class="panel panel-dark panel-border top">
        <div class="panel-heading">
            <span class="panel-title">Печать документа</span>
        </div>
        <div class="panel-body admin-form">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <label>Номер документа</label>
                    <input type="text" class="form-control" placeholder="Введите номер документа" id="docNum">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <input type="button" value="Найти" class="btn btn-primary" id="search">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3" id="docPrint">
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $adminTpl->footIncludes[] = '<script src="/usr/modules/storage/admin/asset/js/docPrint.js"></script>';
    $adminTpl->admin_foot();
}