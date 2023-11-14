<?php

if (!defined('ADMIN_ACCESS')) {
    header('Location: /');
    exit;
}

switch(isset($url[2]) ? $url[2] : null) {
	default:
        header("Location: /administration/module/catalog/attribute");
        exit();
		main();
    break;
    case "getAttributeList":
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            getAttributeList($id);
        }
    break;
    case "getAttribute":
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            getAttribute($id);
        }
    break;
    case "addAttribute":
        if(isset($_POST["id"], $_POST["value"])){
            $id = $_POST["id"];
            $value = $_POST["value"];
            addAttribute($id, $value);
        }
    break;
    case "delAttribute":
        if(isset($_POST["id"], $_POST["value"])){
            $id = $_POST["id"];
            $value = $_POST["value"];
            delAttribute($id, $value);
        }
    break;
    case "editAttribute":
        if(isset($_POST["id"], $_POST["valueOld"], $_POST["valueNew"])){
            $id = $_POST["id"];
            $valueOld = $_POST["valueOld"];
            $valueNew = $_POST["valueNew"];
            editAttribute($id, $valueOld, $valueNew);
        }
    break;
    case "getAttributeListVal":
        getAttributeListVal();
    break;
    case "addNewAttribute":
        addNewAttribute();
    break;
    case "appendAttribute":
        appendAttribute();
    break;
    case "delMainAttribute":
        delMainAttribute();
    break;
    case "editMainAttribute":
        editMainAttribute();
    break;
    case "moveAttribute":
        moveAttribute();
    break;
    case "moveAttribute2":
        moveAttribute2();
    break;
    case "getAttributeListData":
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            getAttributeListData($id);
        }
    break;
    case "changeHidden":
    if(isset($_POST["id"], $_POST["checked"], $_POST["category"])){
        $id = $_POST["id"];
        $checked = $_POST["checked"];
        $category = $_POST["category"];
        changeHidden($id, $category, $checked);
    }
    break;
    case "categoryAllAttribute":
        categoryAllAttribute();
    break;
}

function main(){
    global $adminTpl, $db, $core;
    $adminTpl->admin_head("Атрибуты");
    ?>
<style>
.col-sm-6 {
    margin-bottom: 20px;
}

.attributeElem {
    margin-bottom: 20px;
}

.search{
   position:relative;
}

.search_result{
    background: #FFF;
    border: 1px #ccc solid;
    width: 100%;
    border-radius: 4px;
    max-height:100px;
    overflow-y:scroll;
    display:none;
}

.search_result li{
    list-style: none;
    padding: 5px 10px;
    margin: 0 0 0 -40px;
    color: #0896D3;
    border-bottom: 1px #ccc solid;
    cursor: pointer;
    transition:0.3s;
}

.search_result li:hover{
    background: #F9FF00;
}
#listAttribute{
    margin-top: 20px;
}
#sort{
    height: 42px;
}
.sortElem{
    padding: 10px 5px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
}
.sortElem:hover{
    background-color: #dedede;
}
.changeHiddenCheck{
    display: none;
}
.changeHidden{
    font-size: 25px;
}
.chvg{
    color: green;
}
.chvr{
    color: red;
}
</style>
<div id="content" class="animated fadeIn">
    <div class="panel">
        <div class="panel-body admin-form">
            <div class="col-sm-6 col-sm-offset-3">
                <input type="button" value="Добавить общий атрибут" class="btn btn-success btn-block" id="addAttributeMain" onclick="addAttributeMainModal();" autocomplete="off">
            </div>
            <div class="col-sm-6 col-sm-offset-3">
                <label for="">Выберите категорию</label>
                <select name="" id="category" class="form-control select2-single" onchange="getAttributeList(this);">
                    <?php
                            $cats_arr = $core->aCatList('catalog_v2');
                            echo '<option value="-1" selected disabled>Выберите категорию</option>';
									foreach ($cats_arr as $cid => $name) 
									{
										echo '<option value="' . $cid . '">' . $name . '</option>';
									}													
                        ?>
                </select>
            </div>
            <div class="col-sm-6 col-sm-offset-3">
                <label for="">Выберите атрибут</label>
                <div class="input-group">
                    <select name="" id="attribute" class="form-control select2-single">

                    </select>
                    <span class="input-group-btn">
                        <button class="btn btn-primary" id="sort" onclick="sortModal();"><i class="fas fa-sort-amount-down"></i></button>
                    </span>
                </div>
            </div>
            <div class="col-sm-6 col-sm-offset-3" id="attributeData">

            </div>

            <div class="modal fade" id="addAttributeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Добавить атрибут</h5>
                        </div>
                        <div class="modal-body">
                            <input type="text" placeholder="Введите наименование" class="form-control" id="attributeValue">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" id="addAttributeBtn" onclick="addAttributeBtn();">Добавить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="delAttributeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Удалить атрибут</h5>
                        </div>
                        <div class="modal-body">
                            Вы действительно хотите удалить атрибут?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" id="delAttributeBtn" onclick="delAttributeBtn(this);">Удалить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editAttributeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Изменить атрибут</h5>
                        </div>
                        <div class="modal-body">
                        <input type="text" placeholder="Введите наименование" class="form-control" id="attributeValueEdit">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" id="editAttributeBtn" onclick="editAttributeBtn(this);">Изменить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="attributeMainModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Добавить атрибут</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">Наименование атрибута</label>
                                <input type="text" placeholder="Введите наименование атрибута" class="form-control" id="title" autocomplete="off">
                                <ul class="search_result"></ul>
                            </div>
                            <div class="form-group">
                                <ul id="listAttribute"></ul>
                            </div>
                            <div class="form-group">
                                <label for="">Выберите категорию</label>
                                <?php
                                    $cats_arr = $core->aCatList('catalog_v2');
                                    echo '<select name="category_new" class="select2-single1 form-control" id="category_new" multiple="multiple">';
                                            foreach ($cats_arr as $cid => $name) 
                                            {
                                                echo '<option value="' . $cid . '">' . $name . '</option>';
                                            }													
                                    echo'</select>';
                                ?>
                            </div>
                            <div class="form-group">
                                <label for="">Тип значений</label>
                                <div>
                                    <label class="radio-inline"><input type="radio" name="optradio" class="radioType" value="1" checked>Подставные</label>
                                    <label class="radio-inline"><input type="radio" name="optradio" class="radioType" value="2">Вводимые</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="addToCurrent" disabled>Добавить в текущий</button>
                            <button type="button" class="btn btn-primary" id="addNew" disabled>Добавить новый</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="delMainAttributeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Удалить атрибут</h5>
                        </div>
                        <div class="modal-body">
                            Вы действительно хотите удалить атрибут из категории?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" id="delMainAttributeBtn">Удалить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editMainAttributeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Редактировать атрибут</h5>
                        </div>
                        <div class="modal-body">
                            <input type="text" class="form-control" id="editMainAttributeModalInp">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" id="editMainAttributeModalBtn">Сохранить</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="sortModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Сортировка атрибутов</h5>
                        </div>
                        <div class="modal-body" id="sortModalBody">
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
<?php
    $adminTpl->footIncludes[] = '<script src="/root/modules/asset/module/attribute/js/attribute.js"></script>';
    $adminTpl->admin_foot();
}

function getAttributeList($id){
    global $adminTpl, $db, $core;
    $query = $db->query("SELECT id, title FROM JMY_catalog_atribute WHERE cat LIKE '%,".$id.",%' AND status = 1");
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){
            echo '<option value="'.$r["id"].'">'.$r["title"].' {%'.$r["id"].'%}</option>';
        }
    } else {
        echo '<option value="-1" selected disabled>Атрибуты отсутствуют</option>';
    }
}

function getAttributeListData($id){
    global $adminTpl, $db, $core;
    $query = $db->query("SELECT id, title FROM JMY_catalog_atribute WHERE cat LIKE '%,".$id.",%' AND status = 1");
    $qq = $db->query("SELECT position_filter FROM JMY_categories WHERE id = '".$id."' LIMIT 1");
    $q = $db->getRow($qq);
    $json = null;
    $array = [];
    $flag1 = true;
    if(!empty($q["position_filter"])){
        $json = json_decode($q["position_filter"], true);
        $flag1 = false;
    }
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){
            $aa = $db->query("SELECT id FROM JMY_catalog_atribute WHERE hidden LIKE '%,".$id.",%' AND id = '".$r["id"]."'");
            $hidden = "";
            $flag = false;
            if($db->numRows($aa) > 0){
                $hidden = "checked";
                $flag = true;
            }
            $str = '<div data-id="'.$r["id"].'" class="sortElem">'.$r["title"].' {%'.$r["id"].'%}<span>'.($flag ? '<i class="fas fa-eye-slash changeHidden chvr"></i>' : '<i class="fas fa-eye changeHidden chvg"></i>').'<input type="checkbox" class="changeHiddenCheck" data-id="'.$r["id"].'" '.$hidden.'></span></div>';
            if($flag1){
                echo $str;
            } else {
                $array[$r["id"]] = $str;
            }
        }
    }
    if(!$flag1){
        foreach($json as $val){
            if(isset($array[$val])){
                echo $array[$val];
                unset($array[$val]);
            }
        }
        foreach($array as $val){
            if(!empty($val)){
                echo $val;
            }
        }
    }
}

function getAttribute($id){
    global $adminTpl, $db, $core;
    $query = $db->query("SELECT value, type FROM JMY_catalog_atribute WHERE id = '".$id."' LIMIT 1");
    $r = $db->getRow($query);
    echo '<div style="margin-bottom: 20px;"><input type="button" value="Редактировать" class="btn btn-primary btn-block" id="editMainAttribute" onclick=""></div>';
    echo '<div style="margin-bottom: 20px;"><input type="button" value="Удалить атрибут" class="btn btn-danger btn-block" id="delMainAttribute" onclick=""></div>';
    if(mb_strlen($r["value"]) > 0){
        $data = explode(",", $r["value"]);
        foreach($data as $val){
            ?>
<div class="attributeElem col-sm-12">
    <div class="col-sm-7 col-sm-offset-1"><?php echo $val; ?></div>
    <div class="col-sm-4">
        <div class="btn-group">
            <button type="button" onclick="editAttribute(this);" class="btn btn-primary" id="editAttribute"
                data-value="<?php echo $val; ?>">Редактировать</button>
            <button type="button" data-toggle="dropdown" class="btn btn-primary dropdown-toggle"><span
                    class="caret"></span></button>
            <ul class="dropdown-menu">
                <li><a href="javascript:void(0);" onclick="deleteAttribute(this);" data-value="<?php echo $val; ?>">Удалить</a></li>
            </ul>
        </div>
    </div>
</div>
<?php
        }
    }
    if($r["type"] === "1"){
        echo '<div><input type="button" value="Добавить" class="btn btn-success btn-block" id="addAttribute" onclick="addAttribute();"></div>';
    }
}

function addAttribute($id, $value){
    global $adminTpl, $db, $core;
    $query = $db->query("SELECT value FROM JMY_catalog_atribute WHERE id = '".$id."' LIMIT 1");
    $r = $db->getRow($query);
    $data = [];
    if(mb_strlen($r["value"]) > 0){
        $data = explode(",", $r["value"]);
    }
    $data[] = trim($value);
    $result = implode(",", $data);

    $db->query("UPDATE JMY_catalog_atribute SET value = '".$result."' WHERE id = '".$id."' LIMIT 1");
}

function delAttribute($id, $value){
    global $adminTpl, $db, $core;
    $query = $db->query("SELECT cat, value FROM JMY_catalog_atribute WHERE id = '".$id."' LIMIT 1");
    $r = $db->getRow($query);
    $data = explode(",", $r["value"]);
    foreach($data as $key => $val){
        if($val == $value){
            unset($data[$key]);
        }
    }
    $result = implode(",", $data);
    $db->query("UPDATE JMY_catalog_atribute SET value = '".$result."' WHERE id = '".$id."' LIMIT 1");

    $aaa = explode(",", $r["cat"]);
    $searchIn = "";
    foreach($aaa as $val){
        if(!empty($val)){
            $searchIn .= "',".$val.",', ";
        }
    }
    $searchIn = mb_substr($searchIn, 0, -2);
    $query = $db->query("SELECT id, attribute FROM JMY_catalog WHERE category IN (".$searchIn.") AND attribute LIKE '%\"".$id."\":\"".$value."\"%'");
    echo "SELECT id, attribute FROM JMY_catalog WHERE category IN (".$searchIn.") AND attribute LIKE '%\"".$id."\":\"".$value."\"%'";
    if($db->numRows($query) > 0){
        $whenStr = '';
        $whenIn = '';
        while($t = $db->getRow($query)){
            $json = json_decode($t["attribute"], true);
            unset($json[$id]);
            $json = json_encode($json, JSON_UNESCAPED_UNICODE);
            $whenStr .= "WHEN id = ".$t["id"]." THEN '".$json."' ";
            $whenIn .= $t["id"].",";
        }
        $whenStr = mb_substr($whenStr, 0, -1);
        $whenIn = mb_substr($whenIn, 0, -1);
        $db->query("UPDATE JMY_catalog SET attribute = CASE ".$whenStr." END WHERE id IN (".$whenIn.")");
    }
}

function editAttribute($id, $valueOld, $valueNew){
    global $adminTpl, $db, $core;
    $query = $db->query("SELECT cat, value FROM JMY_catalog_atribute WHERE id = '".$id."' LIMIT 1");
    $r = $db->getRow($query);
    $data = explode(",", $r["value"]);
    foreach($data as $key => $val){
        if($val == $valueOld){
            $data[$key] = trim($valueNew);
        }
    }
    $result = implode(",", $data);
    $db->query("UPDATE JMY_catalog_atribute SET value = '".$result."' WHERE id = '".$id."' LIMIT 1");

    $aaa = explode(",", $r["cat"]);
    $searchIn = "";
    foreach($aaa as $val){
        if(!empty($val)){
            $searchIn .= "',".$val.",', ";
        }
    }
    $searchIn = mb_substr($searchIn, 0, -2);
    $query = $db->query("SELECT id, attribute FROM JMY_catalog WHERE category IN (".$searchIn.") AND attribute LIKE '%\"".$id."\":\"".$valueOld."\"%'");
    if($db->numRows($query) > 0){
        $whenStr = '';
        $whenIn = '';
        while($t = $db->getRow($query)){
            $json = json_decode($t["attribute"], true);
            $json[$id] = $valueNew;
            $json = json_encode($json, JSON_UNESCAPED_UNICODE);
            $whenStr .= "WHEN id = ".$t["id"]." THEN '".$json."' ";
            $whenIn .= $t["id"].",";
        }
        $whenStr = mb_substr($whenStr, 0, -1);
        $whenIn = mb_substr($whenIn, 0, -1);
        $db->query("UPDATE JMY_catalog SET attribute = CASE ".$whenStr." END WHERE id IN (".$whenIn.")");
    }
}

function getAttributeListVal(){
	global $allowFuncs, $db;
	$mod = isset($_POST["mod"]) ? $_POST["mod"] : 1;
	switch($mod){
		default:
			$search = isset($_POST["search"]) ? $_POST["search"] : "";
			$query = $db->query("SELECT * FROM JMY_catalog_atribute WHERE title LIKE '%".$search."%' OR value LIKE '%".$search."%' OR id = '".$search."'");
			while($r = $db->getRow($query)){
				echo '<li data-id="'.$r["id"].'">'.$r["title"].' ('.$r["id"].')</li>';
			}
		break;
		case 2:
			$id = $_POST["id"];
			$query = $db->query("SELECT value, type FROM JMY_catalog_atribute WHERE id = '".$id."'");
			$r = $db->getRow($query);
			echo '<li style="font-weight: bold; list-style-type: none;">Тип значений: '.($r["type"] == 1 ? 'подставные' : 'вводимые').'</li>';
			if($r["type"] == 1){
				$str = explode(",", $r["value"]);
				foreach($str as $val){
					echo '<li>'.$val.'</li>';
				}
			}
			
		break;
	}
}

function addNewAttribute(){
	global $allowFuncs, $db, $core;
	if(isset($_POST["title"], $_POST["cat"], $_POST["type"])){
		$title = $_POST["title"];
		$cat = $_POST["cat"];
		$type = $_POST["type"];
		$str = "";
		foreach($cat as $val){
			$str .= ','.$val;
		}
		$str .= ',';
		$uid = $core->auth->user_info['id'];
		echo 'add';
		$db->query("INSERT INTO `JMY_catalog_atribute`(`cat`, `title`, `type`, `uid`, `position`, `status`) VALUES ('".$str."', '".$title."', '".$type."', '".$uid."', '1', '1')");
	} else {
		echo "error";
	}
}

function appendAttribute(){
	global $allowFuncs, $db, $core;
	if(isset($_POST["id"], $_POST["cat"])){
		$id = $_POST["id"];
		$cat = $_POST["cat"];
		$query = $db->query("SELECT cat FROM JMY_catalog_atribute WHERE id = '".$id."' LIMIT 1");
		$r = $db->getRow($query);
		$flag = true;
		//проверка на повтор
		foreach($cat as $val){
			if(strripos($r["cat"], ','.$val.',') !== false){
				$flag = false;
			} 
		}
		if($flag){
			$str = empty($r["cat"]) ? "," : $r["cat"];
			foreach($cat as $val){
				$str .= $val.",";
			}
		$db->query("UPDATE JMY_catalog_atribute SET cat = '".$str."' WHERE id = '".$id."' LIMIT 1");
		echo 'Append';
		} else {
			echo 'I have this value`s';
		}
		
	} else {
		echo "error";
	}
}

function delMainAttribute(){
    global $allowFuncs, $db, $core;
    if(isset($_POST["id"], $_POST["cat"])){
        $id = $_POST["id"];
		$cat = $_POST["cat"];
		$query = $db->query("SELECT cat FROM JMY_catalog_atribute WHERE id = '".$id."' LIMIT 1");
        $r = $db->getRow($query);
        $array = explode(",", $r["cat"]);
        foreach($array as $key => $val){
            if($val == $cat || empty($val)){
                unset($array[$key]);
            }
        }
        $str = implode(",", $array);
        if(!empty($str)){
            $str = ','.$str.',';
        }
        $db->query("UPDATE JMY_catalog_atribute SET cat = '".$str."' WHERE id = '".$id."' LIMIT 1");
        
        $query = $db->query("SELECT id, attribute FROM JMY_catalog WHERE category = ',".$cat.",' AND attribute LIKE '%\"".$id."\":%'");
        if($db->numRows($query) > 0){
            $whenStr = '';
            $whenIn = '';
            while($t = $db->getRow($query)){
                $json = json_decode($t["attribute"], true);
                unset($json[$id]);
                $json = json_encode($json, JSON_UNESCAPED_UNICODE);
                $whenStr .= "WHEN id = ".$t["id"]." THEN '".$json."' ";
                $whenIn .= $t["id"].",";
            }
            $whenStr = mb_substr($whenStr, 0, -1);
            $whenIn = mb_substr($whenIn, 0, -1);
            $db->query("UPDATE JMY_catalog SET attribute = CASE ".$whenStr." END WHERE id IN (".$whenIn.")");
        }

    } else {
		echo "error";
	}
}

function editMainAttribute(){
    global $allowFuncs, $db, $core;
    if(isset($_POST["id"], $_POST["value"])){
        $id = $_POST["id"];
		$value = $_POST["value"];
        $db->query("UPDATE JMY_catalog_atribute SET title = '".$value."' WHERE id = '".$id."' LIMIT 1");
    } else {
		echo "error";
	}
}

function moveAttribute(){
    global $allowFuncs, $db, $core;
    if(isset($_POST["id"], $_POST["value"])){
        $id = $_POST["id"];
		$value = $_POST["value"];
        $db->query("UPDATE JMY_catalog_atribute SET value = '".$value."' WHERE id = '".$id."' LIMIT 1");
    } else {
		echo "error";
	}
}

function moveAttribute2(){
    global $allowFuncs, $db, $core;
    if(isset($_POST["id"], $_POST["order"])){
        $id = $_POST["id"];
		$order = $_POST["order"];
        $db->query("UPDATE JMY_categories SET position_filter = '".$order."' WHERE id = '".$id."' LIMIT 1");
    } else {
		echo "error";
	}
}

function changeHidden($id, $category, $checked){
    global $allowFuncs, $db, $core;
    $query = $db->query("SELECT hidden FROM JMY_catalog_atribute WHERE id = '".$id."' LIMIT 1");
    $r = $db->getRow($query);
    $hidden = explode(",", $r["hidden"]);
    if($checked === "true"){
        $hidden[] = $category;
    } else {
        foreach($hidden as $key => $val){
            if($val === $category){
                unset($hidden[$key]);
            }
        }
    }
    $newHidden = [];
    foreach($hidden as $val){
        if(!empty($val)){
            $newHidden[] = $val;
        }
    }
    if(count($newHidden) > 0){
        $newHidden = implode(",", $newHidden);
        $newHidden = "',".$newHidden.",'";
    } else {
        $newHidden = 'null';
    }
    
    $db->query("UPDATE JMY_catalog_atribute SET hidden = ".$newHidden." WHERE id = '".$id."' LIMIT 1");
}

function categoryAllAttribute(){
    global $core, $db;
    ?>

    <style>
        li {
            list-style-type: none; 
        }
        .submain {
            margin-left: 0; 
            padding-left: 0;
        }
        ul{
            padding-left: 120px;
        }
    </style>

    <?php
    $query = $db->query("SELECT * FROM JMY_categories WHERE module = 'catalog_v2' ORDER BY name");
    $cats = [];
	while($cat = $db->getRow($query)){
        $cats[$cat['parent_id']][$cat['id']] = $cat;
    }
    $attr = [];
    $query = $db->query("SELECT * FROM JMY_catalog_atribute");
    while($r = $db->getRow($query)){
        $attr[ $r['id'] ] = $r;
    }
    echo tree_build_categories($cats,$attr,0,true);
}

function tree_build_categories($cats,$attr,$parent_id,$check = false){
    global $db;
    if(is_array($cats) and isset($cats[$parent_id])){
        if($check) {
            $tree = '<ul class="submain">';
        } else {
            $tree = '<ul class="submenu" id="submenu_'.$parent_id.'">'; 
        }
        foreach($cats[$parent_id] as $cat){
            $hidden = "";
            if($cat["hidden"] == 1){
                $hidden = "style='color: red;'";
            }
            // $count = \Catalog_v2\Model\Catalog::where("category", ",{$cat["id"]},")->count();
            $count = "*";
            $tree .= '<li '.$hidden.'><p id="point_'.$cat["id"].'" class="point"><h3>'.$cat['id'].' - '.$cat['name'].' (Количество товаров: '.$count.')</h3></p>';
            $tree .= '<ul>';
            foreach($attr as $id => $val){
                if(preg_match("/,".$cat['id'].",/", $val["cat"]) == 1){
                    $type = "";
                    if($val["type"] == 1){
                        $type = "[".$val["value"]."]";
                    }
                    $tree .= '<li>'.$val["id"].' - '.$val["title"].$type.'</li>';
                }
            }
            $tree .= '</ul>';
            $tree .=  tree_build_categories($cats,$attr,$cat['id']);
            $tree .= '</li>';
        }
        $tree .= '</ul>';
    } else return null;
    return $tree;
}