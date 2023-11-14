<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ADMIN_ACCESS')) {
    header('Location: /');
    exit;
}

function main()
{
global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Список категорий';
	$adminTpl->admin_head('Категории | '.$lnn);
	$atributs['opts'] = 'open';
	$atributs['module'] = '';
	$atributs['module_name'] = 'cats';
	$atributs['module_open'] = 'edit';
	load_grid('jqCats', $lnn, $atributs);
	
	$adminTpl->admin_foot();
}

function add($cat = null)
{
global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	if(isset($cat))
	{
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_categories WHERE id = '" . $cat . "'");
		$ccid = $cat;
		$cat = $db->getRow($query);
		$name = prepareTitle($cat['name']);
		$altname = $cat['altname'];
		$description = $cat['description'];
		$keywords = $cat['keywords'];
		$fulltitle = $cat['fulltitle'];
		$icon = isset($cat['icon']) ? $cat['icon'] : 'no.png';
		$pid = $cat['parent_id'];
		
		$preview_array = json_decode($cat['preview'], true);
		if(is_array($preview_array))
		{
			$preview = $preview_array['full'];
		}
		else
		{
			$preview = $cat['preview'];
		}
		$preview_og = $cat['preview_og'];
		$mod = $cat['module'];
		$position = $cat['position'];
		$hidden = $cat['hidden'];
		$lnn = $lang['cats_edit'];
		$remote = ADMIN.'/cats/ajax/iscat/update';
	}
	else
	{
		$name = isset($_POST['name']) ? filter(trim($_POST['name'])) : '';
		$altname = isset($_POST['altname']) ? ($_POST['altname'] == '') ? translit($name) : translit($_POST['altname']) : '';
		$description = isset($_POST['description']) ? filter($_POST['description']) : '';
		$fulltitle = isset($_POST['fulltitle']) ? filter($_POST['fulltitle']) : '';
		$keywords = isset($_POST['keywords']) ? filter($_POST['keywords'], 'a') : '';
		$icon = isset($_POST['icon']) ? filter($_POST['icon'], 'a') : 'no.png';
		$pid = false;
		$mod = 'news';
		$position = '0';
		$hidden = '0';
		$lnn = $lang['cats_add'];
		$remote = ADMIN.'/cats/ajax/iscat';
		$preview = "";
		$preview_og = "";
	}
	$_SESSION["RF"]["fff"] ="files/cats/";		
	$adminTpl->admin_head($lang['cats'].' | '.$lnn);
	$validation_array = array(
		'name' => array(
			'required' =>  array('true', $lang['cats_name_err'])
		),
		'altname' => array(
			'required' =>  array('true',  $lang['cats_url_err_1']),
			'remote' =>  array($remote,  $lang['cats_url_err_2'])
		),
		'keywords' => array(
			'maxlength' =>  array(255, $lang['cats_keys_err'])
		)
	);
	validationInit($validation_array);
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lnn .'</span>
			</div>
			<div class="panel-body admin-form">
				<form id="admin-form" action="{ADMIN}/cats/save" method="post" name="cats" role="form" class="form-horizontal parsley-form">
					<div class="form-group">
						<label for="name"  class="col-lg-3 control-label">'. $lang['cats_name'] .'</label>
						<div class="col-lg-4">
							<label for="name" class="field prepend-icon">
								<input id="name" type="text" name="name" value="'. $name .'" placeholder="'.$lang['cats_name_pre'].'" class="gui-input" '. (!isset($cat) ? "onchange=\"getTranslit(gid('name').value, 'altname');\"" : "" ) .'>
								<label for="name" class="field-icon"><i class="far fa-pencil"></i></label>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="altname"  class="col-lg-3 control-label">'. $lang['cats_url'] .'</label>
						<div class="col-lg-4">
							<label for="altname" class="field prepend-icon">
								<input id="altname" type="text" name="altname" value="'. $altname .'" placeholder="'.$lang['cats_url_pre'].'" class="gui-input">
								<label for="altname" class="field-icon"><i class="far fa-link"></i></label>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="fulltitle"  class="col-lg-3 control-label">'. $lang['cats_fulltitle'] .'</label>
						<div class="col-lg-4">
							<label for="fulltitle" class="field prepend-icon">
								<input id="fulltitle" type="text" name="fulltitle" value="'. $fulltitle .'" placeholder="'.$lang['cats_fulltitle_pre'].'" class="gui-input">
								<label for="fulltitle" class="field-icon"><i class="far fa-code"></i></label>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="description"  class="col-lg-3 control-label">'. $lang['cats_descr'] .'</label>
						<div class="col-lg-4">
							<label for="description" class="field prepend-icon">
								<input id="description" type="text" name="description" value="'. $description .'" placeholder="'.$lang['cats_descr_pre'].'" class="gui-input">
								<label for="description" class="field-icon"><i class="far fa-keyboard"></i></label>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="keywords"  class="col-lg-3 control-label">'. $lang['cats_keys'] .'</label>
						<div class="col-lg-4">
							<label for="keywords" class="field prepend-icon">
								<input id="keywords" type="text" name="keywords" value="'. $keywords .'" placeholder="'.$lang['cats_keys_pre'].'" class="gui-input">
								<label for="keywords" class="field-icon"><i class="far fa-star"></i></label>
							</label>
						</div>
					</div>
					
				<div class="form-group">
						<label class="col-lg-3 control-label field mb5">Изображение:</label>
							<div class="col-lg-4">											
											<label for="preview" class="field  prepend-icon smart-widget sm-right">
												<input id="preview" type="text" name="preview" placeholder="Введите ссылку на картинку" class="gui-input" value="' . $preview . '" />
												<label for="preview" class="field-icon"><i class="far fa-file-image"></i></label>
												<a  class="button" type="button" data-fancybox data-type="iframe" data-src="/filemanager/filemanager.php?type=1&amp;field_id=preview&amp;relative_url=0" href="javascript:;"><i class="fas fa-folder-open"></i></a>
																					
											</label>	
											</div>											
										</div>
										<div class="form-group">
						<label class="col-lg-3 control-label field mb5">Изображение Open Graph:</label>
							<div class="col-lg-4">											
											<label for="preview_og" class="field  prepend-icon smart-widget sm-right">
												<input id="preview_og" type="text" name="preview_og" placeholder="Введите ссылку на картинку" class="gui-input" value="' . $preview_og . '" />
												<label for="preview_og" class="field-icon"><i class="far fa-file-image"></i></label>
												<a class="button" type="button" data-fancybox data-type="iframe" data-src="/filemanager/filemanager.php?type=1&amp;field_id=preview_og&amp;relative_url=0" href="javascript:;"><i class="fas fa-folder-open"></i></a>											
											</label>	
											</div>											
										</div>
					<div class="form-group">
						<label for="icon"  class="col-lg-3 control-label">'. $lang['cats_icon'] .'</label>
						<div class="col-lg-3">
							<label for="icon" class="field prepend-icon">
									<input id="icon" type="text" name="icon" value="'. $icon .'" placeholder="Укажите иконку" class="gui-input">
									<label for="icon" class="field-icon"><i class="far fa-sort-numeric-up"></i></label>
							</label>								
						</div>						
					</div>
					<div class="form-group">
						<label for="category"  class="col-lg-3 control-label">'. $lang['cats_incat'] .'</label>
						<div class="col-lg-3">
								<label class="field select">
									<select name="category" id="category" >
										<option value="">' .  $lang['cats_incat_no'] . '</option>';
											$cats_arr = $core->aCatList('catalog_v2');
											foreach ($cats_arr as $cid => $name) {
												$selected = ($pid == $cid) ? "selected" : "";
												echo '<option value="' . $cid . '" ' . $selected . '>' . $name . '</option>';
											}
								echo '</select><i class="arrow double"></i>
								</label>
						</div>
					</div>
					<div class="form-group">
						<label for="category"  class="col-lg-3 control-label">'.  $lang['cats_module'] .'</label>
						<div class="col-lg-3">
								<label class="field select">
									<select class="form-control" name="module" id="module" onchange="updateCatList(this.value, \'category\');" >';
										foreach ($core->getModList() as $module)
										{
											if(in_array($module, exceMods('categories')))
											{
												$selected = ($module == $mod) ? "selected" : "";
												echo '<option value="' . $module . '" ' . $selected . '>' . _mName($module) . '</option>';
											}
										}
								echo '</select><i class="arrow double"></i>
								</label>
						</div>
					</div>
					<div class="form-group">
						<label for="position"  class="col-lg-3 control-label">Позиция</label>
						<div class="col-lg-4">
							<label for="position" class="field prepend-icon">
								<input id="position" type="text" name="position" value="'. $position .'" placeholder="Укажите позицию категории" class="gui-input">
								<label for="position" class="field-icon"><i class="far fa-sort-numeric-up"></i></label>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Скрытая категория?</label>
						<div class="col-sm-4">
							'.radio("hidden",  $hidden).'														
						</div>	
					</div>		
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-4">
							<input type="submit" class="btn btn-primary btn-parsley" id="sub" value="'. (isset($cat) ?  $lang['update'] :  $lang['add']) .'" />
						</div>
					</div>';
					if(isset($ccid))
					{
						echo '
							
							<input type="hidden" name="edit" value="1">
							<input type="hidden" name="cid" value="' . $ccid . '">';
					}
	echo '</form></div></div>';
	$adminTpl->admin_foot();
}

function cat_save() {
global $adminTpl, $db, $core, $lang;
	$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);
	$cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
	$name = filter($_POST['name'], 'a');
	$altname = ($_POST['altname'] == '') ? translit($name) : str_replace(array('-', ' '), array('_', '_'), $_POST['altname']);
	$description = filter($_POST['description']);
	$keywords = filter($_POST['keywords'], 'a');
	$fulltitle = filter($_POST['fulltitle']);
	$icon = filter($_POST['icon'], 'a');
	$preview = filter($_POST['preview']);
	$preview_og = filter($_POST['preview_og']);
	$position = intval($_POST['position']);
		
	$module = filter($_POST['module'], 'module');
	$pid = intval($_POST['category']);
	
	$hidden = isset($_POST['hidden']) ? $_POST['hidden'] : false;
	if ($hidden != '1')
	{
		$hidden = '0';
	}
	else
	{
		$hidden = '1';
	}
	
	echo '<div id="content" class="animated fadeIn">';
	if(!empty($name) && !empty($altname))
	{			
		$data = ["name" => $name, "altname" => $altname, "description" => $description, "fulltitle" => $fulltitle, "keywords" => $keywords, "module" => $module, "preview" => $preview, "preview_og" => $preview_og, "icon" => $icon, "position" => $position, "hidden" => $hidden, "parent_id" => $pid, ];
		$data['name'] = str_replace(array("&prime",'"',"&Prime","&lsquo","&rsquo","&sbquo","&ldquo","&rdquo","&bdquo",),"'",$name);
		if(isset($_POST['edit']))
		{
			\Boot\Models\Categories::find($cid)->update($data);
			// $db->query("UPDATE `" . DB_PREFIX . "_categories` SET `name` = '" . $db->safesql(processText($name)) . "', `altname` = '" . $altname . "', `description` = '" . $db->safesql(processText($description)) . "', `fulltitle` = '" . $db->safesql(processText($fulltitle)) . "', `keywords` = '" . $db->safesql(processText($keywords)) . "', `module` = '" . $module . "', `preview` = '" . $db->safesql($preview) . "', `preview_og` = '" . $db->safesql($preview_og) . "', `icon` = '" . $icon . "', `position` = '" . $position . "', `hidden` = '" . $hidden . "',`parent_id` = '" . $pid . "' WHERE `id` =".$cid."  LIMIT 1");
			$adminTpl->info($lang['cats_edit_success'], 'info', null, $lang['info'], $lang['cats_list'], ADMIN.'/cats');
			$nnid = $cid;
		}
		else
		{
			$cat = \Boot\Models\Categories::create($data);
			// if($db->query("INSERT INTO `" . DB_PREFIX . "_categories` ( `id` , `name` , `altname` ,  `fulltitle` , `description` , `keywords` , `module` , `preview` , `preview_og` , `icon` , `position` , `parent_id`, `hidden` ) VALUES (NULL, '" . $db->safesql(processText($name)) . "', '" . $altname . "', '" . $db->safesql(processText($fulltitle)) . "', '" . $db->safesql(processText($description)) . "', '" . $keywords . "', '" . $module . "', '" . $db->safesql($preview) . "', '" . $db->safesql($preview_og) . "', '" . $icon . "', '".$position."', '" . $pid . "', '" . $hidden . "');"))
			// $nnid = $db->insert_id();
			$nnid = $cat->id;
			$adminTpl->info($lang['cats_add_success'], 'info', null, $lang['info'], $lang['cats_list'], ADMIN.'/cats');
		}
		del__cache("categories");
		del__cache("menu_*");
		del__cache("menu_all_*");
		\Boot\Support\Cache::delete('categories');
		\Boot\Support\Cache::delete('categories.clear');
		// \Boot\Support\Cache::delete('categories');
		if(!empty($preview))
		{
			// dd($preview);
			$preview = str_replace("https://atri-energo.ru/", "", $preview);
			$preview = str_replace("atri-energo.ru/", "", $preview);
			$preview = rawurldecode($preview);
			$purl = 'files/cats/view/';			
			$path_info = pathinfo($preview);
			$ext = $path_info['extension'];		
			$file_full = $preview;
			$src = ROOT.$file_full;	
			// dd($src);	
			$info = pathinfo($src);				
			switch(mb_strtolower($ext)) 
			{
				default:
					$img = imageCreateFromJpeg($src);
				break;
				
				case 'png':
					$img = imageCreateFromPng($src);
				break;
				
				case 'jpg':
					$img = imageCreateFromJpeg($src);
				break;
				
				case 'gif':
					$img = imageCreateFromGif($src);
				break;
			}				
			imageWebp($img, ROOT.$purl . '/preview_' . $nnid . '.' . 'webp', 80);
			imagedestroy($img);
			$preview_array = array();
			$preview_array['full'] = '/' . $file_full;
			$preview_array['full_webp'] = '/' . $purl.'preview_' . $nnid .'.webp';				
			$db->query("UPDATE `" . DB_PREFIX . "_categories` SET `preview` = '".json_encode($preview_array, JSON_UNESCAPED_UNICODE)."' WHERE `id` = '" . $nnid . "' LIMIT 1");
			
		}			
	}
	else
	{
		$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
	}
	echo '</div>';
	$adminTpl->admin_foot();
}

function scan($cat = null) {
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_fast_add']);
	$validation_array = array(
		'full' => array(
			'required' =>  array('true', $lang['cats_fast_list_err'])
		)
	);
	validationInit($validation_array);
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lang['cats_fast_add'] .'</span>
			</div>
			<div class="panel-body admin-form">
				<form id="admin-form" action="{ADMIN}/cats/save_scan" method="post" name="news" role="form" class="form-horizontal parsley-form">
					<div class="form-group">
						<label for="category"  class="col-lg-3 control-label">'. $lang['cats_module'] .'</label>
						<div class="col-lg-3">
								<label class="field select">
									<select class="form-control" name="module" id="module">';
										foreach ($core->getModList() as $module)
										{
											if(in_array($module, exceMods('categories')))
											{
												$selected = ($module == $mod) ? "selected" : "";
												echo '<option value="' . $module . '" ' . $selected . '>' . _mName($module) . '</option>';
											}
										}
								echo '</select><i class="arrow double"></i>
								</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['cats_fast_list'] .'</label>
						<div class="col-sm-4">
							<label for="comment" class="field prepend-icon">
								<textarea name="full" id="full" placeholder="'.$lang['cats_fast_list_pre'].'" class="gui-textarea"></textarea>
								<label for="comment" class="field-icon"><i class="fa fa-comments"></i></label><span class="input-footer">'.$lang['cats_fast_list_tt'].'</span>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-4">
							<input name="submit" type="submit" class="btn btn-primary btn-parsley" value="'. $lang['add'] .'"/>
						</div>
					</div>
				</form>
			</div>
		</div>';
	$adminTpl->admin_foot();
}

function save_scan() {

global $adminTpl, $db, $core, $lang;
	$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);
	echo '<div id="content" class="animated fadeIn">';
	$module = filter($_POST['module'], 'module');
	$full = explode("\n", $_POST['full']);
	$info = '';
	if (!empty($_POST['full']))
	{
		foreach($full as $cat)
		{
			if($cat !== '' && !is_array($cat))
			{
				/*
				$cat = substr($cat, 0, -1);
				echo $cat;
				createOrgByInn($cat);
			*/
				$name = filter($cat);
				$altname = translit($name);
				$db->query("INSERT INTO `" . DB_PREFIX . "_categories` (`name` , `altname` ,  `module` ) VALUES ('" . $db->safesql($name) . "', '" . $altname . "', '" . $module . "');");
				$info .= str_replace(array('[name]', '[altname]'), array($name, $altname), $lang['cats_fast_save']);
				
			}
		}
		$adminTpl->info($info, 'info', null, $lang['info'], $lang['cats_list'], ADMIN.'/cats');
	}
	else
	{
		$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
	}
	echo '</div>';
	@unlink(ROOT . 'tmp/cache/categories.cache');
	$adminTpl->admin_foot();
}

function delete($id) {
global $adminTpl, $db;
	$db->query("DELETE FROM `" . DB_PREFIX . "_categories` WHERE `id` = " . $id . " LIMIT 1");
	$db->query("DELETE FROM `" . DB_PREFIX . "_news` WHERE `cat` like '%," . $id . ",%'");
	if(file_exists(ROOT . 'tmp/cache/categories.cache'))
	{
		@unlink(ROOT . 'tmp/cache/categories.cache');
	}
}

switch(isset($url[2]) ? $url[2] : null) {
	default:
		main();
	break;

	case "add":
		add();
	break;

	case "save":
		cat_save();
	break;

	case "delete":
		$id = intval($url[3]);
		delete($id);
		header('Location: /'.ADMIN.'/cats');
	break;

	case "edit":
		$id = intval($url[3]);
		add($id);
	break;

	case "action":
	$type = $_POST['submit'];
	if(is_array($_POST['checks'])) {
		switch($type) {
			case _DELETE:
				foreach($_POST['checks'] as $id) {
					delete(intval($id));
				}
			break;
		}
	}
		header('Location: /'.ADMIN.'/cats');
	break;

	case "scan_cat":
		scan();
	break;

	case "save_scan":
		save_scan();
	break;

	case 'ajax':
		global $adminTpl, $db, $lang;
			ajaxInit();
			$type = $url[3];
			switch($type)
			{
				case "iscat":

					if(isset($_POST['altname']))
					{
						if(!preg_match("/^[a-zA-Z0-9_-]+$/", $_POST['altname']))
						{
							echo(json_encode($lang['cats_url_err_3']));
						}
						else
						{
							$query = $db->query("SELECT * FROM ".DB_PREFIX."_categories WHERE altname = '" . $db->safesql($_POST['altname']) . "'");
							if(($db->numRows($query) > 0) && ($url[4] != 'update'))
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
			}
		break;
}
