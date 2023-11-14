<?php
function getQueryMainMeteringScheme($id){
    global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
    $query = $db->query("SELECT * FROM JMY_metering_scheme WHERE id = '".$id."' LIMIT 1");
    return $db->getRow($query);
}

function metering_detail_scheme($id){
    global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
    $adminTpl->admin_head('Блок | Блок - '.$id);

    $detail = getQueryMainMeteringScheme($id);

    xeditableInit();

    ?>

<style>
.cp {
    cursor: pointer;
}
.position{
    cursor: move;
}
</style>
<section id="content" class="animated fadeIn">
    <div class="text-center">
        <p class="lead">СХЕМА УЗЛА УЧЕТА №<?php echo $id; ?></p>
        <input type="hidden" value="<?php echo $id;?>" id="mainId">
        <input type="hidden" value="" id="saveId">
    </div>
    <div class="row" id="admin-form">

    </div>

    <div class="row">
        <div class="panel panel-dark panel-border top">
            <div class="panel-heading">
                <span class="panel-title">Список приборов:</span>
                <div class="widget-menu pull-right">
                    <div class="btn-group">
                        <?php
                            echo '<button type="button" class="btn btn-xs btn-system refresh"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>
                            '.callAddProduct($id, "https://atri-energo.ru/administration/module/selection/addProductMeteringScheme/").'
                            <button class="btn btn-xs btn-success saveChange">Сохранить</button>';
                        ?>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="schemeModal" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Добавить изображение схемы</h5>
                        </div>
                        <div class="modal-body">
                        <form id="schemeForm">
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
                    </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal" id="schemeSave">Сохранить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>
			 <div class="modal fade" id="dtModal" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Добавить изображение ДТ</h5>
                        </div>
                        <div class="modal-body">
                        <form id="dtForm">
                            <?php
                        $attribute = [];
                        $attribute["name"] = "2";
                        $attribute["script"] = true;
                        $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
                        $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
                        echo uploaderMaster("css", $attribute);
                        echo uploaderMaster("upload", $attribute);
                        echo uploaderMaster("view", $attribute);
                    ?>
                    </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal" id="dtSave">Сохранить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>
			 <div class="modal fade" id="ddModal" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Добавить изображение ДД</h5>
                        </div>
                        <div class="modal-body">
                        <form id="ddForm">
                            <?php
                        $attribute = [];
                        $attribute["name"] = "3";
                        $attribute["script"] = true;
                        $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
                        $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
                        echo uploaderMaster("css", $attribute);
                        echo uploaderMaster("upload", $attribute);
                        echo uploaderMaster("view", $attribute);
                    ?>
                    </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal" id="ddSave">Сохранить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>
			
			 <div class="modal fade" id="autoModal" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Добавить изображение схемы автоматики</h5>
                        </div>
                        <div class="modal-body">
                        <form id="autoForm">
                            <?php
                        $attribute = [];
                        $attribute["name"] = "4";
                        $attribute["script"] = true;
                        $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
                        $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
                        echo uploaderMaster("css", $attribute);
                        echo uploaderMaster("upload", $attribute);
                        echo uploaderMaster("view", $attribute);
                    ?>
                    </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal" id="autoSave">Сохранить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>
			
			<div class="modal fade" id="elModal" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Добавить изображение электрической схемы</h5>
                        </div>
                        <div class="modal-body">
                        <form id="elForm">
                            <?php
                        $attribute = [];
                        $attribute["name"] = "5";
                        $attribute["script"] = true;
                        $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
                        $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
                        echo uploaderMaster("css", $attribute);
                        echo uploaderMaster("upload", $attribute);
                        echo uploaderMaster("view", $attribute);
                    ?>
                    </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal" id="elSave">Сохранить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>
			
			<div class="modal fade" id="pdModal" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Добавить изображение схемы подключения</h5>
                        </div>
                        <div class="modal-body">
                        <form id="pdForm">
                            <?php
                        $attribute = [];
                        $attribute["name"] = "6";
                        $attribute["script"] = true;
                        $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
                        $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
                        echo uploaderMaster("css", $attribute);
                        echo uploaderMaster("upload", $attribute);
                        echo uploaderMaster("view", $attribute);
                    ?>
                    </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal" id="pdSave">Сохранить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>
			
			<div class="modal fade" id="shuModal" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Добавить изображение ШУУ</h5>
                        </div>
                        <div class="modal-body">
                        <form id="shuForm">
                            <?php
                        $attribute = [];
                        $attribute["name"] = "7";
                        $attribute["script"] = true;
                        $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
                        $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
                        echo uploaderMaster("css", $attribute);
                        echo uploaderMaster("upload", $attribute);
                        echo uploaderMaster("view", $attribute);
                    ?>
                    </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal" id="shuSave">Сохранить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>
			
			<div class="modal fade" id="plModal" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Добавить изображение схемы пломбирования</h5>
                        </div>
                        <div class="modal-body">
                        <form id="plForm">
                            <?php
                        $attribute = [];
                        $attribute["name"] = "8";
                        $attribute["script"] = true;
                        $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
                        $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
                        echo uploaderMaster("css", $attribute);
                        echo uploaderMaster("upload", $attribute);
                        echo uploaderMaster("view", $attribute);
                    ?>
                    </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal" id="plSave">Сохранить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>



            <div class="modal fade" id="delModal" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Удаление</h5>
                        </div>
                        <div class="modal-body">
                            Вы действительно хотите удалить эту позицию?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal" id="delBtn">Удалить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-body pn table-responsive">
                <form id="tablesForm">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="w40"><span class="pd-l-sm"></span>№</th>
                                <th class="w100">Артикул</th>
                                <th class="w500">Наименование</th>
                                <th class="w50">Производитель</th>
                                <th class="w100">Кол-во</th>
                                <th class="w50">Ед.</th>
                                <th class="w40 text-center">Статус</th>
                                <th class="w200 text-right">Действия</th>
                                <th class="w20 text-right">
                                    <div class="checkbox-custom mb15">
                                        <input id="all" type="checkbox" name="all"
                                            onclick="setCheckboxes('tablesForm', true); return true;" class="checks">
                                        <label for="all"></label>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tov_list">

                        </tbody>
                        <tfoot class="footer-menu">
                            <tr align="right">
                                <td align="right" colspan="12">
                                    <nav align="right" class="text-right">
                                        <select style="width: 250px; display: inline-block;" class="form-control" name="act">
                                            <option value="del">Удалить выбранное</option>
                                        </select>
                                        <input name="submit" type="button" class="btn btn-success"
                                            style="display: inline-block;" id="sub" value="Выполнить" /><span
                                            class="pd-l-sm"></span>
                                    </nav>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
        </div>
    </div>
</section>


<div class="modal fade" id="rem_aid_scheme_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Удалить изображение схемы</h5>
      </div>
      <div class="modal-body">
        Вы действительно хотите удалить изображение схемы?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="rem_aid_scheme_btn" data-dismiss="modal">Удалить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="rem_aid_dt_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Удалить изображение ДТ</h5>
      </div>
      <div class="modal-body">
        Вы действительно хотите удалить изображение ДТ?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="rem_aid_dt_btn" data-dismiss="modal">Удалить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="rem_aid_dd_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Удалить изображение ДД</h5>
      </div>
      <div class="modal-body">
        Вы действительно хотите удалить изображение ДД?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="rem_aid_dd_btn" data-dismiss="modal">Удалить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="rem_aid_auto_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Удалить изображение схемы автоматики</h5>
      </div>
      <div class="modal-body">
        Вы действительно хотите удалить изображение схемы автоматики?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="rem_aid_auto_btn" data-dismiss="modal">Удалить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="rem_aid_el_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Удалить изображение электрической схемы</h5>
      </div>
      <div class="modal-body">
        Вы действительно хотите удалить изображение электрической схемы?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="rem_aid_el_btn" data-dismiss="modal">Удалить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="rem_aid_pd_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Удалить изображение схемы подключения</h5>
      </div>
      <div class="modal-body">
        Вы действительно хотите удалить изображение схемы подключения?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="rem_aid_pd_btn" data-dismiss="modal">Удалить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="rem_aid_shu_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Удалить изображение ШУУ</h5>
      </div>
      <div class="modal-body">
        Вы действительно хотите удалить изображение ШУУ?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="rem_aid_shu_btn" data-dismiss="modal">Удалить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="rem_aid_pl_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Удалить изображение схемы пломбирования</h5>
      </div>
      <div class="modal-body">
        Вы действительно хотите удалить изображение схемы пломбирования?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="rem_aid_pl_btn" data-dismiss="modal">Удалить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="delAllModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Удалить выбранные товары</h5>
      </div>
      <div class="modal-body">
        Вы действительно хотите удалить выбранные товары?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="delAllModalBtn" data-dismiss="modal">Удалить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<?php
$attr = [];
$attr = [
	"module" => 'selection_detail', 
	"id" => $id, 
	"update" => "tov_list", 
	"update_url" => "/administration/module/selection/ajax/metering_detail_scheme_tovar_list/".$id, 
	"status" => 1,
	"hotkeys" => callAddProduct($id, "https://atri-energo.ru/administration/module/selection/addProductMeteringScheme/", true).'<button type="button" class="btn btn-xs btn-system refresh"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>
    <button class="btn btn-xs btn-success saveChange">Сохранить</button>',
];
fastBar($attr);
    $adminTpl->footIncludes[] = '<script src="usr/modules/selection/admin/assets/js/detail_metering_scheme.js"></script>';
    $adminTpl->admin_foot();
}

function ajax_metering_detail_scheme_header($id){
    global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
    $detail = getQueryMainMeteringScheme($id);

    $type_block = getTypeMetring();
    ?>
<div class="panel mb25 mt5">
    <div class="panel-body p20 pb10">
        <div class="section row mbn admin-form">
            <div class="col-md-12 pl15">
                <div class="section row mb15">
                    <div class="col-md-3">
                        <div class="panel br-a">
                            <div class="panel-heading" style="padding: 7px; line-height: 29px;"><span
                                    class="panel-title" style="font-size: 18px;">Меню</span></div>
                            <div class="panel-body panel-scroller scroller-xs scroller-pn pn">
                                <table class="table mbn tc-med-1 table-hover">
                                    <tbody>
                                        <tr>
                                            <td style="cursor: pointer;" onclick="window.open('/catalog_v2/specification/metering_scheme/<?php echo $id;?>','_blank'); return false;">
                                                <span style="top: 0px;"
                                                    class="glyphicon glyphicon-list-alt mr5 va-m text-warning"></span>Получить спецификацию АТМ</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <small>Наименование схемы:</small><br />
                        <b><a href="javascript:void(0);" id="title"><?php echo $detail["title"];?></a></b><br /><br />
						<small>Статус:</small><br />
                        <b><a>Создано: <i style="color:green; font-size: 18px; margin-left: 10px;" class="fa fa-check-circle" aria-hidden="true"></i></a></b><br /><br />
                        <small>Ответственный:</small><br />
                        <b><?php echo shortUserName($detail['uid']); ?></b>
                        
                    </div>
                    <div class="col-md-3">
                        <small>Изображение схемы:</small><br />
                        <b><?php if(empty($detail["aid_scheme"])){
                                    echo '<a href="javascript:void(0);" id="addScheme">Добавить</a>';
                                } else {
                                    $ff = $db->query("SELECT name FROM JMY_attach WHERE id = '".$detail["aid_scheme"]."' LIMIT 1");
                                    $fileName = $db->getRow($ff);
                                    echo '<a target="_blank" href="/download/'.$detail["aid_scheme"].'">'.$fileName["name"].'</a><i id="rem_aid_scheme" style="color:red; font-size: 18px; margin-left: 10px; cursor: pointer;" class="fa fa-trash" aria-hidden="true"></i>';
                                }					
								
                        ?></b><br /><br />
						<small>Изображение ДТ:</small><br />
                        <b><?php if(empty($detail["aid_dt"])){
                                    echo '<a href="javascript:void(0);" id="addDt">Добавить</a>';
                                } else {
                                    $ff = $db->query("SELECT name FROM JMY_attach WHERE id = '".$detail["aid_dt"]."' LIMIT 1");
                                    $fileName = $db->getRow($ff);
                                    echo '<a target="_blank" href="/download/'.$detail["aid_dt"].'">'.$fileName["name"].'</a><i id="rem_aid_dt" style="color:red; font-size: 18px; margin-left: 10px; cursor: pointer;" class="fa fa-trash" aria-hidden="true"></i>';
                                }					
								
                        ?></b><br /><br />
						<small>Изображение ДД:</small><br />
                        <b><?php if(empty($detail["aid_dd"])){
                                    echo '<a href="javascript:void(0);" id="addDd">Добавить</a>';
                                } else {
                                    $ff = $db->query("SELECT name FROM JMY_attach WHERE id = '".$detail["aid_dd"]."' LIMIT 1");
                                    $fileName = $db->getRow($ff);
                                    echo '<a target="_blank" href="/download/'.$detail["aid_dd"].'">'.$fileName["name"].'</a><i id="rem_aid_dd" style="color:red; font-size: 18px; margin-left: 10px; cursor: pointer;" class="fa fa-trash" aria-hidden="true"></i>';
                                }					
								
                        ?></b><br /><br />
						<small>Изображение схемы автоматики:</small><br />
                        <b><?php if(empty($detail["aid_auto"])){
                                    echo '<a href="javascript:void(0);" id="addAuto">Добавить</a>';
                                } else {
                                    $ff = $db->query("SELECT name FROM JMY_attach WHERE id = '".$detail["aid_auto"]."' LIMIT 1");
                                    $fileName = $db->getRow($ff);
                                    echo '<a target="_blank" href="/download/'.$detail["aid_auto"].'">'.$fileName["name"].'</a><i id="rem_aid_auto" style="color:red; font-size: 18px; margin-left: 10px; cursor: pointer;" class="fa fa-trash" aria-hidden="true"></i>';
                                }					
								
                        ?></b>						
                    </div>
					<div class="col-md-3">
						<small>Изображение электрической схемы:</small><br />
                        <b><?php if(empty($detail["aid_el"])){
                                    echo '<a href="javascript:void(0);" id="addEl">Добавить</a>';
                                } else {
                                    $ff = $db->query("SELECT name FROM JMY_attach WHERE id = '".$detail["aid_el"]."' LIMIT 1");
                                    $fileName = $db->getRow($ff);
                                    echo '<a target="_blank" href="/download/'.$detail["aid_el"].'">'.$fileName["name"].'</a><i id="rem_aid_el" style="color:red; font-size: 18px; margin-left: 10px; cursor: pointer;" class="fa fa-trash" aria-hidden="true"></i>';
                                }					
								
                        ?></b><br /><br />
						<small>Изображение схемы подключения:</small><br />
                        <b><?php if(empty($detail["aid_pd"])){
                                    echo '<a href="javascript:void(0);" id="addPd">Добавить</a>';
                                } else {
                                    $ff = $db->query("SELECT name FROM JMY_attach WHERE id = '".$detail["aid_pd"]."' LIMIT 1");
                                    $fileName = $db->getRow($ff);
                                    echo '<a target="_blank" href="/download/'.$detail["aid_pd"].'">'.$fileName["name"].'</a><i id="rem_aid_pd" style="color:red; font-size: 18px; margin-left: 10px; cursor: pointer;" class="fa fa-trash" aria-hidden="true"></i>';
                                }					
								
                        ?></b><br /><br />
						<small>Изображение ШУУ:</small><br />
                        <b><?php if(empty($detail["aid_shu"])){
                                    echo '<a href="javascript:void(0);" id="addShu">Добавить</a>';
                                } else {
                                    $ff = $db->query("SELECT name FROM JMY_attach WHERE id = '".$detail["aid_shu"]."' LIMIT 1");
                                    $fileName = $db->getRow($ff);
                                    echo '<a target="_blank" href="/download/'.$detail["aid_shu"].'">'.$fileName["name"].'</a><i id="rem_aid_shu" style="color:red; font-size: 18px; margin-left: 10px; cursor: pointer;" class="fa fa-trash" aria-hidden="true"></i>';
                                }					
								
                        ?></b><br /><br />
						<small>Изображение схемы пломбирования:</small><br />
                        <b><?php if(empty($detail["aid_pl"])){
                                    echo '<a href="javascript:void(0);" id="addPl">Добавить</a>';
                                } else {
                                    $ff = $db->query("SELECT name FROM JMY_attach WHERE id = '".$detail["aid_pl"]."' LIMIT 1");
                                    $fileName = $db->getRow($ff);
                                    echo '<a target="_blank" href="/download/'.$detail["aid_pl"].'">'.$fileName["name"].'</a><i id="rem_aid_pl" style="color:red; font-size: 18px; margin-left: 10px; cursor: pointer;" class="fa fa-trash" aria-hidden="true"></i>';
                                }					
								
                        ?></b>						
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}

function ajax_metering_detail_scheme_tovar_list($id, $change = false, $ic_data = []){
    global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
    if($change){
        $query = $db->query("SELECT c.title, c.sku, cm.name as cmname, cu.name as cuname FROM JMY_catalog as c LEFT JOIN JMY_catalog_manufacture as cm ON(cm.id_1c = c.manufacture) LEFT JOIN JMY_catalog_unit as cu ON(cu.unit = c.unit) WHERE c.id = '".$ic_data["cid"]."'");
    } else {
        $query = $db->query("SELECT sd.*, c.title, c.sku, cm.name as cmname, cu.name as cuname FROM JMY_metering_scheme_detail as sd LEFT JOIN JMY_catalog as c ON(sd.cid = c.id) LEFT JOIN JMY_catalog_manufacture as cm ON(cm.id_1c = c.manufacture) LEFT JOIN JMY_catalog_unit as cu ON(cu.unit = c.unit) WHERE sd.scheme_id = '".$id."' ORDER BY sd.position");
    }
    
    if($db->numRows($query) > 0){
        if($change){
            $i = $ic_data["num"];
            $parentId = $ic_data["parentId"];
            $count = $ic_data["count"];
            $status = "1";
        } else {
            $i = 1;
        }
        
        while($r = $db->getRow($query)){

            if(!$change){
                $parentId = $r["id"];
                $count = $r["count"];
                $status = $r["status"];
                ?>

    <tr id="tr_<?php echo $parentId; ?>" data-id="<?php echo $parentId; ?>">

        <?php
            }
        ?>

    <td class="position" title="<?php echo $parentId; ?>" data-id="<?php echo $parentId; ?>"><?php echo $i;?></td>
    <td class="noSort"><?php echo $r["sku"];?></td>
    <td class="noSort title cp"><?php echo $r["title"];?></td>
    <td class="noSort"><?php echo $r["cmname"];?></td>
    <td class="noSort count cp"><?php echo $count;?></td>
    <td class="noSort"><?php echo $r["cuname"];?></td>
    <td class="noSort text-center"><?php echo $status;?></td>
    <td class="noSort text-right"><?php echo '<div class="btn-group">
                    <button type="button" class="btn btn-xs btn-danger" data-id="'.$parentId.'" onclick="deleteModal(this);">X</button>
                  
                    </div>';?></td>
    <td class="noSort w20 text-right">
        <div class="checkbox-custom mb15">
            <input id="<?php echo 'checkbox'.$parentId;?>" type="checkbox" name="checks[]" value="<?php echo $parentId;?>"
                class="checks">
            <label for="<?php echo 'checkbox'.$parentId;?>"></label>
        </div>
    </td>
    <?php
    if(!$change){
        ?>
</tr>
<?php
    }
            $i++;
        }
    } else echo '<tr><td colspan="15">У данной заявки еще не заполнен список приборов!</td></tr>';
}

function ajax_metering_detail_scheme_tovar_add(){
    global $adminTpl, $core, $db, $admin_conf, $lang;
	if((isset($_POST['cid'], $_POST['id'])))
	{
		$id = intval($_POST['id']);
		$mcid = $_POST['cid'];
        
        $str = '';
        
        foreach($mcid as $cid){
            $query_stable = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '". $cid ."' LIMIT 1");
            $tovar_stable = $db->getRow($query_stable);	
            $sum = (isset($_POST['count_'.$cid]) ? $_POST['count_'.$cid] : 1);
            
            $str .= "('" . $db->safesql($id) . "', '" . $db->safesql($cid) . "', '" . $db->safesql($sum) . "', '1'), ";
        }

		if($db->query("INSERT INTO `" . DB_PREFIX . "_metering_scheme_detail` (`scheme_id`, `cid`, `count`, `status`) VALUES ".mb_substr($str, 0, -2)."")) 
		{
			echo '1';
		}
		else
		{
			echo '3';
		}
	}
	else
	{
		echo '2';
	}
}