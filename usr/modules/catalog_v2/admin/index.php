<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ADMIN_SWITCH')) {
    location(' /');
    exit;
}

$dir = ROOT.'/usr/modules/catalog_v2/admin/sub/';  
includeSub($dir);

//ini_set('display_errors',1);
//error_reporting(E_ALL);

global $lang;
        
switch(isset($url[3]) ? $url[3] : null) {
	default:
		header("Location: /administration/module/catalog");
		exit();
		catalog_main();
	break;

	case "backup":
		catalog_backup();
	break;
	
	case 'tags':
		$adminTpl->admin_head($lang['modules'] . ' | ' . $lang['catalog_tags']);
		echo '<div id="content" class="animated fadeIn">';			
		if(isset($url[4]))
		{
			switch($url[4])
			{
				case 'addOk':
					$adminTpl->alert('success', $lang['info'], $lang['catalog_tags_add_ok']);
					break;				
					
				case 'delOk':
					$adminTpl->alert('success', $lang['info'], $lang['catalog_tags_delete_ok']);
					break;
			}
		}
		$adminTpl->open();
		$query = $db->query("SELECT tag FROM " . DB_PREFIX . "_tags WHERE module = 'catalog'");
		if($db->numRows($query) > 0) 
		{
			echo '<div class="panel panel-dark panel-border top">
			<div class="panel-heading">
				<span class="panel-title">' . $lang['catalog_tags_list'] . '</span>	
				<div class="widget-menu pull-right" >
					<div class="btn-group">
						<button onclick="modal_o(\'#modal-form-add\')" type="button" class="btn btn-xs btn-success btn-block">'.$lang['catalog_tags_add'].'</button>
					</div>					
				</div>
			</div>
			<div class="panel-body pn">
				<form id="tablesForm" style="margin:0; padding:0" method="POST" action="{MOD_LINK}/actionTag">
					 <table class="table table-striped">
						<thead>
							<tr>								
								<th class="w450">' . $lang['title'] . '</th>
								<th class="w350">' . $lang['catalog_tags_numb'] . '</th>
								<th>' . $lang['action'] . '</th>
								<th class="text-right">
									<div class="checkbox-custom mb15">
										<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
										<label for="all"></label>
									</div>	
								</th>
							</tr>
						</thead>
						<tbody>';		
						while($tag = $db->getRow($query)) 
						{
							$tags[] = $tag['tag'];
						}			
						$tag_cloud = new TagsCloud;
						$tag_cloud->tags = $tags;
						$cloud = Array();
						$tags_list = $tag_cloud->tags_cloud($tag_cloud->tags);
						$min_count = $tag_cloud->get_min_count($tags_list);	
						$i=0;
						foreach ($tags_list as $tag => $count) 
						{
							$i++;
							echo '
							<tr>
								<td><span class="pd-l-sm"></span>' . $tag . '</td>
								<td>' . $count . '</td>
								<td>
									<button type="button"  onclick="modal_o(\'#modal-form-'.$i.'\')" class="btn btn-xs btn-primary">'.$lang['delete'].'</button>
									<div id="modal-form-'.$i.'" class="popup-basic bg-none mfp-with-anim mfp-hide">
										<div class="panel">
											<div class="panel-heading">
												<span class="panel-icon"><i class="fa fa-check-square-o"></i></span>
												<span class="panel-title">'.$lang['confirm'].'</span>
											</div>
											<div class="panel-body">
												<h3 class="mt5">' . str_replace('[tag]', $tag, $lang['catalog_tags_delete_title']) .  '</h3>
												<hr class="short alt">
												<p>' . str_replace('[tag]', $tag, $lang['catalog_tags_delete_text']) .  '</p>
											</div>
											<div class="panel-footer text-right">
												<button type="button" onclick="location.href = \'{MOD_LINK}/tagDelete/' . $tag . '\'" class="btn btn-danger">' . $lang['delete'] .'</button>
											</div>
										</div>
									</div>	
								</td>
								<td class="text-right"> 
									<div class="checkbox-custom mb15">
										<input id="checkbox' . $i . '" type="checkbox" name="checks[]" value="' . $tag . '">
										<label for="checkbox' . $i . '"></label>
									</div>								
								</td>
							</tr>';	
						}
			echo '</tbody>
				  <tfoot class="footer-menu">
						<tr>                    
						  <td colspan="9">
							<nav class="text-right">
								<input name="submit" type="submit" class="btn btn btn-danger" id="sub" value="' . $lang['delete'] . '" />
							 </nav>
						  </td>
						</tr>
				   </tfoot> 
			</table>	
		</form>
		</div></div>';	
		} 
		else 
		{
			$adminTpl->info($lang['catalog_tags_empty'], 'empty', null, $lang['catalog_tags_list'], $lang['catalog_tags_add'], 'modal-form-add', 'modal');		
		}
		echo'<div id="modal-form-add" class="popup-basic admin-form mfp-with-anim mfp-hide">
            <div class="panel">
              <div class="panel-heading"><span class="panel-title"><i class="fa fa-tags"></i>'.$lang['catalog_tags_add'].'</span></div>
              <form id="comment" method="post" action="{MOD_LINK}/addTag">
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-12">
                      <label for="tag" class="field prepend-icon">
                        <input id="tag" type="text" name="tag" placeholder="'.$lang['catalog_tags_name'].'" class="gui-input">
                        <label for="tag" class="field-icon"><i class="fa fa-pencil"></i></label>
                      </label>
                    </div>                    
                  </div>                 
                </div>
                <div class="panel-footer">
                  <button type="submit" class="button btn-primary">'.$lang['add'].'</button>
                </div>
              </form>
            </div>
          </div>
		</div>';
		$adminTpl->close();
		$adminTpl->admin_foot();
		break;
		
	case 'addTag':
		if(!empty($_POST['tag']))
		{
			$db->query("INSERT INTO `" . DB_PREFIX . "_tags` ( `id` , `tag` , `module` ) VALUES (NULL, '" . filter($_POST['tag']) . "', 'catalog');");
			location(ADMIN.'/module/catalog/tags/addOk');
		}
		else
		{
			location(ADMIN.'/module/catalog/tags');
		}
		break;
	
	case 'tagDelete':
		if(isset($url[4]))
		{
			$tag = filter(utf_decode($url[4]));
			$db->query("DELETE FROM `" . DB_PREFIX . "_tags` WHERE `tag` = '" . $tag . "'");
			location(ADMIN.'/module/catalog/tags/delOk');
		}
		else
		{
			location(ADMIN.'/module/catalog/tags');
		}
		break;
		
	case 'actionTag':
		if(!empty($_POST['checks']))
		{
			foreach($_POST['checks'] as $id) 
			{
				if(trim($id))
				{
					$db->query("DELETE FROM `" . DB_PREFIX . "_tags` WHERE `tag` = '" . $id . "'");
				}
			}
			
			location(ADMIN.'/module/catalog/tags/delOk');
		}
		else
		{
			location(ADMIN.'/module/catalog/tags');
		}
		break;
	
	case "add":
		header("Location: /administration/module/catalog/create");
		exit();
		catalog_add();
	break;

	case "change_log":
		change_log_main();
	break;
	case "change_log_confirm":
		$id = isset($url[4]) ? $url[4] : false;
		change_log_confirm($id);
	break;

	case "pump":
		index_pump();
	break;

	case "groups":
		index_catalog_groups();
	break;
	case "groups_discount":
		index_catalog_groups_discount();
	break;
	case "groups_organizations":
		index_catalog_groups_organizations();
	break;
	case "groups_template":
		index_catalog_groups_template();
	break;

	case "union_goods":
		switch(isset($url[4]) ? $url[4] : null){
			default:
				union_goods();
			break;
			case "search":
				if(isset($_POST["value"])){
					$value = $_POST["value"];
					union_goods_search($value);
				} else {
					http_response_code(502);
				}
			break;
			case "union":
				if(isset($_POST["data"])){
					$data = $_POST["data"];
					$data = json_decode($data, true);
					union_goods_final($data);
				} else {
					http_response_code(502);
				}
			break;
		}
	break;
	
	/*
	case "script":
		$query = $db->query("SELECT id, attribute FROM JMY_catalog WHERE category = ',363,'");
		while($r = $db->getRow($query))
		{
			$json = json_decode($r['attribute']);
			
			if($json['128'] == 'Вертикальный многуступенчатый'){
				$json['128'] = 'Вертикальный многоступенчатый';
				$res = json_encode($json, JSON_UNESCAPED_UNICODE);
				$db->query("UPDATE JMY_catalog SET attribute = '".$res."' WHERE id = '".$r['id']."'");
				echo $r['id'].'<br/>';
			}
			if($json['128'] == 'Горизонтальный многуступенчатый'){
				$json['128'] = 'Горизонтальный многоступенчатый';
				$res = json_encode($json, JSON_UNESCAPED_UNICODE);
				$db->query("UPDATE JMY_catalog SET attribute = '".$res."' WHERE id = '".$r['id']."'");
				echo $r['id'].'<br/>';
			}
		}
	break;
	*/
    
	case "group":
		group_view();
	break;
	
	case "file_upload":
$adminTpl->admin_head($lang['modules'] . ' | ' . $lang['catalog_tags']);
		echo '<div id="content" class="animated fadeIn">';		
				
				$_SESSION["RF"]["fff"] ="files/catalog_file/042021/";
				
				echo '<iframe width=100% height="640" src="https://atri-energo.ru/filemanager/filemanager.php?type=0&lang=ru&popup=0&crossdomain=0&relative_url=0&akey=key&fldr=catalog_file/042021/"></iframe>';
				echo '</div>';
		$adminTpl->admin_foot();	
	break;
    
    case "saveSpecification":
        if(isset($_POST["data"])){
            $locData = $_POST["data"];
            $uid = $core->auth->user_info['id']; 
            $db->query("INSERT INTO JMY_specification_save (data, date, uid, status) VALUES ('".$locData."', '".date("Y-m-d H:i:s")."', '".$uid."', 1)");
        }
	break;
	
	case "copyCat":
		$id = $_POST["id"];
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '".$id."' LIMIT 1");
		$r = $db->getRow($query);
		$str = "(";
		$str1 = "(";
		$uid = $core->auth->user_info['id'];
		$date = time(); 
		foreach($r as $key => $val){
			switch($key){
				default:
				if(!empty($val)){
					$str .= $key.", ";
					$str1 .= "'".$val."', ";
				}
				break;
				case "id":
				case "id_1c":
				case "quantity":
				case "attach_id":
					//пропуск
				break;
				case "uid":
					$str .= $key.", ";
					$str1 .= "'".$uid."', ";
				break;
				case "date":
					$str .= $key.", ";
					$str1 .= "'".$date."', ";
				break;
			}
		}
		$str = mb_substr($str, 0, -2);
		$str1 = mb_substr($str1, 0, -2);
		$str .= ")";
		$str1 .= ")";
		//echo "INSERT INTO ".DB_PREFIX."_catalog ".$str." VALUES ".$str1."";
		$db->query("INSERT INTO ".DB_PREFIX."_catalog ".$str." VALUES ".$str1."");
		$nid = $db->insert_id();
		$source = [];
        $source['comment'] = 'Добавление товара (копирование)';
		updateQuantity($nid, 1, 0, "set", $source);
		syncRegister('catalog_v2', $nid, '1', '0');
	break;

	case "hiddenCat":
		$id = isset($_POST["id"]) ? $_POST["id"] : false;
		$type = isset($_POST["type"]) ? $_POST["type"] : false;
		$db->query("UPDATE ".DB_PREFIX."_catalog SET `hidden` = '".$type."' WHERE id = '".$id."' LIMIT 1");
	break;
    
    case "import":
    $adminTpl->admin_head('Каталог товаров | Импорт');
    importExcel();
    $adminTpl->admin_foot();
    break;
    
    case "importExcelUpdate":
        if(isset($_POST["sku"], $_POST["name"], $_POST["quantity"], $_POST["startTable"])){
            require_once("usr/plugins/PHPExcel.php");
            $sku = $_POST["sku"];
            $name = $_POST["name"];
            $quantity = $_POST["quantity"];
            $startTable = $_POST["startTable"];
            
            $excel = PHPExcel_IOFactory::load($_FILES['uploadfile']['tmp_name']);
            $count =  $excel->setActiveSheetIndex(0)->getHighestRow();
            
            echo '<input type="button" onclick="history.back();" value="Назад">';
            echo '<table><tr><td>Артикул</td><td>Наименование</td><td>Количество</td></tr>';
            for($i=$startTable;$i<=$count;$i++){
                $sku_ = $excel->getActiveSheet()->getCell($sku.$i)->getValue();
                $name_ = $excel->getActiveSheet()->getCell($name.$i)->getValue();
                $quantity_ = (integer)$excel->getActiveSheet()->getCell($quantity.$i)->getValue();
                
                $flag = false;
                if(!empty($sku_) && !empty($name_)){
                    if(empty($sku_)){
                        $query = $db->query("SELECT id FROM JMY_catalog WHERE title LIKE '".$name_."'");
                        if($db->numRows($query) == 1){
                            $db->query("UPDATE JMY_catalog SET quantity = ".$quantity_." WHERE title LIKE '".$name_."'");
                            $flag = true;
                        }
                    } else {
                        $db->query("UPDATE JMY_catalog SET quantity = ".$quantity_." WHERE sku = '".$sku_."'");
                        $flag = true;
                    }
                }
                $color = "red";
                if($flag) $color = "green";
                echo '<tr style="background-color: '.$color.'; color: white;"><td>'.$sku_.'</td><td>'.$name_.'</td><td>'.$quantity_.'</td></tr>';
            }
            echo '</table>';
        }
    break;
	
	case "cert":
		$type = isset($url[4]) ? $url[4] : false;		
		switch($type) 
		{
			default:
				cert_view();
			break;
			case "add":
				cert_add();
			break;
			case "save":
				cert_save();
			break;
			case "list":
				$id = intval($url[5]);
				cert_list($id);
			break;
			case "view":
				$id = intval($url[5]);
				cert_list_view($id);
			break;
			case "edit":
				$id = intval($url[5]);
				cert_add($id);
			break;
			case "delete":
				$cid = intval($url[5]);
				$cert_id = intval($url[6]);
				$db->query("DELETE FROM JMY_cert_relation WHERE cid = '".$cid."' AND cert_id = '".$cert_id."'");
				location(ADMIN.'/module/catalog_v2/cert/view/'.$cert_id);
			break;
			case "delete_all":
				$cert_id = intval($url[5]);
				$db->query("DELETE FROM JMY_cert_relation WHERE cert_id = '".$cert_id."'");
				location(ADMIN.'/module/catalog_v2/cert/view/'.$cert_id);
			break;
			case "delete_select_id":
				$request = $_POST;
				$id = $request["id"] ?? false;
				$data = $request["data"] ?? [];
				if(count($data) > 0 && $id){
					$cid = implode(", ", $data);
					$db->query("DELETE FROM JMY_cert_relation WHERE cert_id = '{$id}' AND cid IN({$cid})");
				}
			break;
			case "delete_certificates":
				$id = intval($url[5]);
				$db->query("DELETE FROM JMY_cert_relation WHERE cert_id = '".$id."'");
				$db->query("DELETE FROM JMY_cert WHERE id = '".$id."'");
				location(ADMIN.'/module/catalog_v2/cert');
			break;
			case "ajax_relation":
				$id = intval($url[5]);
				ajax_relation($id);
			break;
		}		
	break;	

	case "group_add":
		group_add();
	break;
	
	case "ajax_catalog_search":
		ajax_catalog_search();
	break;
	
	case "attribute":
		attribute_list();
	break;
	
	case "manufacture":
		$type = isset($url[4]) ? $url[4] : false;
		$id = 	isset($url[5]) ? $url[5] : null;
		switch($type){
			default: 
				manufacture_list();
			break;
			case "save":
				manufacture_save();
			break;
			case "add":
				manufacture_add($id);
			break;
			case "edit":
				manufacture_add($id);
			break;
		}
	break;

	case "loadCatFile":
		$link = isset($_GET["link"]) ? $_GET["link"] : false;
		if($link){
			loadCatFile(ROOT.$link);
		}
	break;
	
	case "loadCat":
		$data = loadCat($_POST);
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	break;

	case "clear_temp_file":
		clear_temp_file_load_cat();
	break;

	case "load_cat":
		load_cat_view();
	break;

	case "loadCatCheck":
		$data = loadCatCheck();
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	break;

	case "loadCatSave":
		$data = loadCatSave($_POST, $_FILES);
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	break;

	case "save":
		catalog_save();
	break;
	
	case "edit":
		$id = intval($url[4]);
		header("Location: /administration/module/catalog/{$id}/edit");
		exit();
		catalog_add($id);
	break;
    
    // case "skuGenerate":
    // 	if(isset($url[4]))
    // 	{   
    // 	    $cat = str_pad($url[4], 3, "0", STR_PAD_LEFT);
    // 	}
    // 	else
    // 	    return "";
    // 	/*
    // 	$query = $db->query("SELECT * FROM `" . DB_PREFIX . "_catalog` WHERE `sku` LIKE 'AE05".$cat."%'");
    // 	$num = $db->numRows($query);
    // 	$serial = $num + 1;
    // 	*/
    // 	if(isset($url[5])){
    // 	    $id = $url[5];
    // 	} else {
    // 	echo "AE05".$cat."XXXXXX";
    // 	break;
    // 	}
	
    // 	$serial = str_pad($id, 6, "0", STR_PAD_LEFT);
    // 	echo "AE05".$cat.$serial;
    // break;
	
	case "delete":
		$id = intval($url[4]);
		delete($id);
		if(isset($_GET['moderate']))
		{
			location(ADMIN.'/moderation/mod/catalog');
		}
		else
		{
			location(ADMIN.'/module/catalog');
		}
	break;
	
	case "activate":
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `active` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '" . $id . "'");
		$catalog = $db->getRow($query);
		$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_catalog=user_catalog+1 WHERE `nick` ='" . $catalog['author'] . "' LIMIT 1", true);
		location(ADMIN.'/module/catalog');
	break;	
	
	case "moderation":
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `active` = '2' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '" . $id . "'");
		$catalog = $db->getRow($query);
		$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_catalog=user_catalog-1 WHERE `nick` ='" . $catalog['author'] . "' LIMIT 1", true);
		location(ADMIN.'/module/catalog');
	break;	
	
	case "moder_moderation":
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `active` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '" . $id . "'");
		$catalog = $db->getRow($query);
		$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_catalog=user_catalog+1 WHERE `nick` ='" . $catalog['author'] . "' LIMIT 1", true);
		location(ADMIN.'/moderation/mod/catalog');
	break;	
	
	case "deactivate":
	global $adminTpl, $db;
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `active` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '" . $id . "'");
		$catalog = $db->getRow($query);
		$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_catalog=user_catalog-1 WHERE `nick` ='" . $catalog['author'] . "' LIMIT 1", true);
		location(ADMIN.'/module/catalog');
	break;

	case "action":
	$type = $_POST['act'];
	if(is_array($_POST['checks'])) {
		switch($type) {
			case "activate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `active` = '1' WHERE `id` = " . intval($id) . " LIMIT 1 ;");
				}
				break;			
			
			case "deActivate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `active` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;			
				
			case "reActivate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `active` = NOT `active` WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;
				
			case "nowDate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `date` = '" . time() . "' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;			
				
			case "blockIndex":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `allow_index` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;		

			case "setIndex":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `allow_index` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;						
				
			case "blockComment":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `	allow_comments` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;
				
			case "allowComment":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `	allow_comments` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;
			
			case "delete":
				foreach($_POST['checks'] as $id) {
					delete(intval($id));
				}
				break;
		}
	}
		if(isset($_GET['moderate']))
		{
			location(ADMIN.'/moderation/mod/catalog');
		}
		else
		{
			location(ADMIN.'/module/catalog');
		}
	break;
	
	case 'ajax':
		global $adminTpl, $db;
			ajaxInit();
			$type = $url[4];			
			switch($type) 
			{
				case "isurl":
					
					if(isset($_POST['translit']))
					{ 
						if(!preg_match("/^[a-zA-Z0-9_-]+$/", $_POST['translit']))
						{
							echo(json_encode($lang['catalog_add_url_err_2']));
						}
						else
						{
							$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE altname = '" . $db->safesql($_POST['translit']) . "'");
							if(($db->numRows($query) > 0) && ($url[5] != 'update')) 
							{	
								echo 'false';
							}
							else
							{
								echo 'true';
							}
						}
					}					
				break;
                case "attr_data":
                if(isset($url[5])){
                    $queryAT = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_atribute WHERE cat LIKE '%,".$url[5].",%'");
                    $attribute_array = '';
                    $id = '';
                    if(isset($url[6])){
                        $query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '" . $url[6] . "'");
                        $catalog = $db->getRow($query);
                        $attribute_array = json_decode($catalog['attribute'], true);
                    }
                    while($xfield = $db->getRow($queryAT)) 
								{
                                    if(!isset($xfield['content'])) $xfield['content'] = '';
									echo '<div class="form-group">
											<label class="col-sm-3 control-label">'. $xfield['title'] .'</label>
												<div class="col-sm-4">';
									if($xfield['type'] == 1)
									{
										$dxfield = array_map('trim', explode(",", $xfield['value']));
										$xfieldChange = '<select class="form-control" name="atri[' . $xfield['id'] . ']"><option value="">Пусто</option>';
										foreach($dxfield as $xfiled_content)
										{
											$xfieldChange .= '<option value="' . $xfiled_content . '" ' . (isset($attribute_array[$xfield['id']]) && $attribute_array[$xfield['id']] == $xfiled_content ? 'selected' : ''). '>' . $xfiled_content . '</option>';
										}
										$xfieldChange .= '</select>';
									}
									elseif($xfield['type'] == 3)
									{
										$xfieldChange = '<textarea class="form-control" name="atri[' . $xfield['id'] . ']" >' . (!empty($fields[$xfield['id']][1]) ? $fields[$xfield['id']][1] : ($id ? '' : $xfield['content'])) . '</textarea>';
									}
									else
									{
										$xfieldChange = '<input type="text" class="form-control" name="atri[' . $xfield['id'] . ']" value="' . (!empty($attribute_array[$xfield['id']]) ? $attribute_array[$xfield['id']] : ($id ? '' : $xfield['content'])) . '" />';
									}
									echo $xfieldChange;
									echo '</div></div>
											<input type="hidden" name="xfieldT[' . $xfield['id'] . ']" value="' . $xfield['title'] . '" />';
								}
                } else 
                    echo '<div class="form-group text-center"><h2>Выбирите категорию</h2></div>';
                break;
			}
		break;
	
	case 'config':
		require (ROOT.'etc/catalog.config.php');		
		$configBox = array(
			'catalog' => array(
				'varName' => 'catalog_conf',
				'title' => $lang['catalog_config'],
				'groups' => array(
					'main' => array(
						'title' => $lang['config_main'],
						'vars' => array(
							'num' => array(
								'title' => $lang['catalog_config_main_numt'],
								'description' => $lang['catalog_config_main_numd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'comments_num' => array(
								'title' => $lang['catalog_config_main_comments_numt'],
								'description' => $lang['catalog_config_main_comments_numd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),							
							'fullLink' => array(
								'title' => $lang['catalog_config_main_fulllinkt'],
								'description' => $lang['catalog_config_main_fulllinkd'],
								'content' => radio("fullLink", $catalog_conf['fullLink']),
							),	
							'noModer' => array(
								'title' => $lang['catalog_config_main_nomoder'],
								'description' => $lang['catalog_config_main_nomoder_desc'],
								'content' => changeuGroup('noModer'),
							),	
							'preModer' => array(
								'title' => $lang['catalog_config_main_premodert'],
								'description' => $lang['catalog_config_main_premoderd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),									
							'related_catalog' => array(
								'title' => $lang['catalog_config_main_relatedt'],
								'description' => $lang['catalog_config_main_relatedd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),					
							'addcatalog' => array(
								'title' => $lang['catalog_config_main_addcatalogt'],
								'description' => $lang['catalog_config_main_addcatalogd'],
								'content' => radio("addcatalog", $catalog_conf['addcatalog']),
							),
						)
					),
					'cats' => array(
						'title' => $lang['catalog_config_cats'],
						'vars' => array(
							'showCat' => array(
								'title' => $lang['catalog_config_cats_showcatt'],
								'description' => $lang['catalog_config_cats_showcatd'],
								'content' => radio("showCat", $catalog_conf['showCat']),
							),							
							'subLoad' => array(
								'title' => $lang['catalog_config_cats_subloadt'],
								'description' => $lang['catalog_config_cats_subloadd'],
								'content' => radio("subLoad", $catalog_conf['subLoad']),
							),
							'catCols' => array(
								'title' => $lang['catalog_config_cats_catcolst'],
								'description' => $lang['catalog_config_cats_catcolsd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'showBreadcumb' => array(
								'title' => $lang['catalog_config_cats_showbreadcumbt'],
								'description' => $lang['catalog_config_cats_showbreadcumbd'],
								'content' => radio("showBreadcumb", $catalog_conf['showBreadcumb']),
							),
						)
					),					
					'tags' => array(
						'title' => $lang['catalog_config_tags'],
						'vars' => array(
							'tags' => array(
								'title' => $lang['catalog_config_tags_tagst'],
								'description' => $lang['catalog_config_tags_tagsd'],
								'content' => radio("tags", $catalog_conf['tags']),
							),							
							'tags_num' => array(
								'title' => $lang['catalog_config_tags_tags_numt'],
								'description' => $lang['catalog_config_tags_tags_numd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'tagIll' => array(
								'title' => $lang['catalog_config_tags_tagillt'],
								'description' => $lang['catalog_config_tags_tagilld'],
								'content' => radio("tagIll", $catalog_conf['tagIll']),
							),
							'illFormat' => array(
								'title' => $lang['catalog_config_tags_illformatt'],
								'description' => $lang['catalog_config_tags_illformatd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
						)
					),
					'ratings' => array(
						'title' => $lang['catalog_config_ratings'],
						'vars' => array(
							'limitStar' => array(
								'title' => $lang['catalog_config_ratings_limitstart'],
								'description' => $lang['catalog_config_ratings_limitstard'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'starStyle' => array(
								'title' => $lang['catalog_config_ratings_starstylet'],
								'description' => $lang['catalog_config_ratings_starstyled'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'carma_rate' => array(
								'title' => $lang['catalog_config_ratings_carma_ratet'],
								'description' => $lang['catalog_config_ratings_carma_rated'],
								'content' => radio("carma_rate", $catalog_conf['carma_rate']),
							),
							'carma_summ' => array(
								'title' => $lang['catalog_config_ratings_carma_summt'],
								'description' => $lang['catalog_config_ratings_carma_summd'],
								'content' => radio("carma_summ", $catalog_conf['carma_summ']),
							),
						)
					),
					'files' => array(
						'title' => $lang['catalog_config_file'],
						'vars' => array(
							'fileEditor' => array(
								'title' => $lang['catalog_config_file_feditort'],
								'description' => $lang['catalog_config_file_feditord'],
								'content' => radio("fileEditor", $catalog_conf['fileEditor']),
							),
							'imgFormats' => array(
								'title' => $lang['catalog_config_file_imgformatst'],
								'description' => $lang['catalog_config_file_imgformatsd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'attachFormats' => array(
								'title' => $lang['catalog_config_file_attachformatst'],
								'description' => $lang['catalog_config_file_attachformatsd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'max_size' => array(
								'title' => $lang['catalog_config_file_max_sizet'],
								'description' => $lang['catalog_config_file_max_sized'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),			
							'thumb_width' => array(
								'title' => $lang['catalog_config_file_thumb_widtht'],
								'description' => $lang['catalog_config_file_thumb_widthd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),							
						)
					),					
				),
			),
		);		
		$ok = false;		
		if(isset($_POST['conf_file']))
		{
			$ok = true;
		}		
		generateConfig($configBox, 'catalog', '{MOD_LINK}/config', $ok);
		break;		
}
