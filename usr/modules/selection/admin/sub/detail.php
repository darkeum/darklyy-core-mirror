<?php
function getQueryMain($id){
    global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
    $query = $db->query("SELECT * FROM JMY_selection WHERE id = '".$id."' LIMIT 1");
    return $db->getRow($query);
}

function detail($id){
    global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
    $adminTpl->admin_head('Блок | Блок - '.$id);

    $detail = getQueryMain($id);

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
        <p class="lead">БЛОК №<?php echo $id; ?></p>
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
                            '.callAddProduct($id, "https://atri-energo.ru/administration/module/selection/addProduct/").'
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
                            <h5 class="modal-title" id="exampleModalLabel">Добавить DWG схему</h5>
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

            <div class="modal fade" id="sizeModal" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Добавить DWG размеры</h5>
                        </div>
                        <div class="modal-body">
                        <form id="sizeForm">
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
                            <button type="button" class="btn btn-success" data-dismiss="modal" id="sizeSave">Сохранить</button>
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
        <h5 class="modal-title" id="exampleModalLabel">Удалить DWG схему</h5>
      </div>
      <div class="modal-body">
        Вы действительно хотите удалить DWG схему?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="rem_aid_scheme_btn" data-dismiss="modal">Удалить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="rem_aid_size_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Удалить DWG размеры</h5>
      </div>
      <div class="modal-body">
        Вы действительно хотите удалить DWG размеры?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="rem_aid_size_btn" data-dismiss="modal">Удалить</button>
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
	"update_url" => "/administration/module/selection/ajax/detail_tovar_list/".$id, 
	"status" => 1,
	"hotkeys" => callAddProduct($id, "https://atri-energo.ru/administration/module/selection/addProduct/", true).'<button type="button" class="btn btn-xs btn-system refresh"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>
    <button class="btn btn-xs btn-success saveChange">Сохранить</button>',
];
fastBar($attr);
    $adminTpl->footIncludes[] = '<script src="usr/modules/selection/admin/assets/js/detail.js"></script>';
    $adminTpl->admin_foot();
}

function ajax_detail_header($id){
    global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
    $detail = getQueryMain($id);

    $type_block = getTypeBlock();
    ?>
<div class="panel mb25 mt5">
    <div class="panel-body p20 pb10">
        <div class="section row mbn admin-form">
            <div class="col-md-9 pl15">
                <div class="section row mb15">
                    <div class="col-md-4">
                        <div class="panel br-a">
                            <div class="panel-heading" style="padding: 7px; line-height: 29px;"><span
                                    class="panel-title" style="font-size: 18px;">Меню</span></div>
                            <div class="panel-body panel-scroller scroller-xs scroller-pn pn">
                                <table class="table mbn tc-med-1 table-hover">
                                    <tbody>
                                        <tr>
                                            <td style="cursor: pointer;" onclick="window.open('/catalog_v2/specification/selection/<?php echo $id;?>','_blank'); return false;">
                                                <span style="top: 0px;"
                                                    class="glyphicon glyphicon-list-alt mr5 va-m text-warning"></span>Получить спецификацию</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <small>Код:</small><br />
                        <b><a href="javascript:void(0);" id="code"><?php echo $detail["code"];?></a></b><br /><br />
                        <small>Тип:</small><br />
                        <b><a href="javascript:void(0);" id="type"><?php echo $type_block[$detail["type"]];?></a></b><br /><br />
                        <small>Ответственный:</small><br />
                        <b><?php echo shortUserName($detail['uid']); ?></b><br /><br />
                        <small>Статус:</small><br />
                        <b><?php echo $detail['status']; ?></b>
                    </div>
                    <div class="col-md-4">
                        <small>DWG схема:</small><br />
                        <b><?php if(empty($detail["aid_scheme"])){
                                    echo '<a href="javascript:void(0);" id="addScheme">Добавить</a>';
                                } else {
                                    $ff = $db->query("SELECT name FROM JMY_attach WHERE id = '".$detail["aid_scheme"]."' LIMIT 1");
                                    $fileName = $db->getRow($ff);
                                    echo '<a target="_blank" href="/download/'.$detail["aid_scheme"].'">'.$fileName["name"].'</a><i id="rem_aid_scheme" style="color:red; font-size: 18px; margin-left: 10px; cursor: pointer;" class="fa fa-trash" aria-hidden="true"></i>';
                                }
                        ?></b><br/><br/>
                        <small>DWG размеры:</small><br />
                        <b><?php if(empty($detail["aid_size"])){
                                    echo '<a href="javascript:void(0);" id="addSize">Добавить</a>';
                                } else {
                                    $ff = $db->query("SELECT name FROM JMY_attach WHERE id = '".$detail["aid_size"]."' LIMIT 1");
                                    $fileName = $db->getRow($ff);
                                    echo '<a target="_blank" href="/download/'.$detail["aid_size"].'">'.$fileName["name"].'</a><i id="rem_aid_size" style="color:red; font-size: 18px; margin-left: 10px; cursor: pointer;" class="fa fa-trash" aria-hidden="true"></i>';
                                }
                        ?></b><br/><br/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}

function ajax_detail_tovar_list($id, $change = false, $ic_data = []){
    global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
    if($change){
        $query = $db->query("SELECT c.title, c.sku, cm.name as cmname, c.unit FROM JMY_catalog as c LEFT JOIN JMY_catalog_manufacture as cm ON(cm.id = c.man) WHERE c.id = '".$ic_data["cid"]."'");
    } else {
        $query = $db->query("SELECT sd.*, c.title, c.sku, cm.name as cmname, c.unit FROM JMY_selection_detail as sd LEFT JOIN JMY_catalog as c ON(sd.cid = c.id) LEFT JOIN JMY_catalog_manufacture as cm ON(cm.id = c.man) WHERE sd.selection_id = '".$id."' ORDER BY sd.position");
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

            $unit = get_unit('data', $r['unit'] ,'sname');

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
    <td class="noSort"><?php echo $unit;?></td>
    <td class="noSort text-center"><?php echo $status;?></td>
    <td class="noSort text-right"><?php echo '<div class="btn-group">
                    <button type="button" class="btn btn-xs btn-primary">???</button>
                    <button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
                    <ul role="menu" class="dropdown-menu">	
							<li><a href="javascript:void(0);" data-id="'.$parentId.'" onclick="deleteModal(this);">Удалить</a></li>
						</ul>
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

function ajax_detail_tovar_add(){
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

		if($db->query("INSERT INTO `" . DB_PREFIX . "_selection_detail` (`selection_id`, `cid`, `count`, `status`) VALUES ".mb_substr($str, 0, -2)."")) 
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