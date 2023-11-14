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
/*
$dir = ROOT.'/usr/modules/catalog/admin/sub/';  
$catalog = opendir($dir);
while ($filename = readdir($catalog )) 
{  
  $filename = $dir."/".$filename;  
  include_once($filename);
}
closedir($catalog);
*/
function catalog_main() 
{
	global $adminTpl, $core, $db, $admin_conf, $url, $lang;
	$adminTpl->admin_head($lang['catalog'] . ' | ' . $lang['catalog_list']);
	echo '<div id="content" class="animated fadeIn">';
	$page = init_page();
	$cut = ($page-1)*$admin_conf['num'];
	$query_search = isset($_POST['query']) ? filter($_POST['query'], 'text') : '';
	$where = 'WHERE active != 2';
	$cat = 0;	
	if(isset($url[3]) && $url[3] == 'cat')
	{
		$cat = $url[4];
		$where .= "AND cat LIKE '%," . $db->safesql($url[4]) . ",%' ";
	}	
	$whereC = $where;		
	$where .= ' AND l.lang = \'' . $core->InitLang() . '\'';	
	if($query_search)
	{
		$where .= 'AND l.title LIKE \'%' . $db->safesql($query_search) . '%\'';
	}	
	$cats_arr = $core->aCatList('catalog');	
	$all = $db->numRows($db->query("SELECT * FROM " . DB_PREFIX . "_catalog $whereC"));
	$adminTpl->a_pages($page, $admin_conf['num'], $all, ADMIN.'/module/catalog/{page}');
	$query = $db->query("SELECT n.*, l.*, c.id as cid, c.name, c.altname as alturl FROM ".DB_PREFIX."_catalog AS n LEFT JOIN ".DB_PREFIX."_categories AS c ON (n.cat=c.id) LEFT JOIN ".DB_PREFIX."_langs as l on(l.postId=n.id and l.module='catalog') $where ORDER BY n.id DESC LIMIT " . $cut . ", " . $admin_conf['num'] . "");
	$count = $db->numRows($db->query('SELECT id FROM '.DB_PREFIX.'_catalog WHERE active=2'));
		if($count > 0)
		{
			$adminTpl->alert('warning', $lang['info'], $lang['catalog_moder']);
		}
	if($db->numRows($query) > 0) 
	{		
		echo '<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">' . $lang['catalog_list'] . ':</span>    
						<div class="widget-menu pull-right mr5" >						
							<form style=" display: inline-block;" method="POST" action="administration/module/catalog">
								<input name="query" value="" placeholder="'.$lang['search'].'" class="form-control mr10">
							</form>
							<select style="width: 150px; display: inline-block;" class="form-control" onchange="top.location=this.value">
								<option value="">'.$lang['choose_cat'].'</option>';
								foreach ($cats_arr as $cid => $name) 
									{
										echo '  <option value="' . ADMIN . '/module/catalog/cat/' . $cid . '" ' . (($cid == $cat) ? 'selected' : '') . '">'.$name.'</option>';	
									}
						echo '</select>
					</div>
              </div>
              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" method="POST" action="{ADMIN}/module/catalog/action">
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th><span class="pd-l-sm"></span>#</th>
							<th class="col-md-2">' . $lang['title'] . '</th>
							<th class="col-md-2">' . $lang['date'] . '</th>
							<th class="col-md-2">' . $lang['cats'] .'</th>
							<th class="col-md-1 text-center">' . $lang['status'] .'</th>
							<th class="col-md-1 text-center"><span class="fa fa-comments-o fa-lg"></span></th>
							<th class="col-md-1 text-center"><span class="fa fa-eye fa-lg"></span></th>
							<th class="col-md-1">' . $lang['author'] . '</th>
							<th class="col-md-3">' . $lang['actions'] . '</th>
							<th class="col-md-1">
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
									<label for="all"></label>
								</div>	
							</th>
						</tr>
                    </thead>
                    <tbody>';
		while($catalog = $db->getRow($query)) 
		{
			if ($catalog['active'] == 1)
			{
				$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
			}
			elseif ($catalog['active'] == 2)
			{
				$status_icon = '<span class="fa fa-clock-o text-warning fa-md"></span>';
			}
			else
			{
				$status_icon = '<span class="fa fa-circle text-danger fa-md"></span>';
			}			
			echo '
			<tr>
				<td><span class="pd-l-sm"></span>' . $catalog['id'] . '</td>
				<td><a href="/catalog/'.$catalog['altname'].'.html" target="_blank">' . $catalog['title'] . '</a></td>
				<td>' . formatDate($catalog['date'], true) . '</td>				
				<td>' . ($catalog['cat'] !== ',0,' ? $core->getCat('catalog', $catalog['cat'], 'short', 3) : 'Нет') . '</td>
				<td class="text-center">' . $status_icon . '</td>
				<td class="text-center">' . $catalog['comments'] . '</td>
				<td class="text-center">' . $catalog['views'] . '</td>
				<td>' . $catalog['author'] . '</td>
					<td>				
					<div class="btn-group">
						<button type="button" onclick="location.href = \'{ADMIN}/module/catalog/edit/'.$catalog['id'].'\'" class="btn btn-xs btn-primary">'.$lang['edit_short'].'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
						<ul role="menu" class="dropdown-menu">
							'.(($catalog['active'] != 2) ? '<li><a href="{ADMIN}/module/catalog/moderation/'.$catalog['id'].'">' . $lang['do_moderation'] . '</a></li>' : '' ).'
							<li><a href="{ADMIN}/module/catalog/retivate/'.$catalog['id'].'">'. (($catalog['active'] != 1) ? $lang['do_activation'] : $lang['do_deactivation']).'</a></li>  
							<li><a href="{ADMIN}/module/catalog/index/'.$catalog['id'].'">'. (($catalog['allow_index'] == 0) ? $lang['catalog_action_index'] : $lang['catalog_action_noindex']).'</a></li> 	
							<li><a href="{ADMIN}/module/catalog/fix/'.$catalog['id'].'">'. (($catalog['fixed'] == 0) ? $lang['catalog_action_fix'] : $lang['catalog_action_nofix']).'</a></li> 								
							<li class="divider"></li>
							<li><a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-form-'.$catalog['id'].'\')">' . $lang['delete'] .'</a></li>
						</ul>
					</div>
					<div id="modal-form-'.$catalog['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
						  <div class="panel-body">
							<h3 class="mt5">' . str_replace('[catalog]', $catalog['title'], $lang['catalog_delete_title']) .'</h3>					
							<hr class="short alt">
							<p>' . str_replace('[catalog]', $catalog['title'], $lang['catalog_delete_text']) .  '</p>
						  </div>
						  <div class="panel-footer text-right">
							<button type="button" onclick="location.href = \'{ADMIN}/module/catalog/delete/'.$catalog['id'].'\'" class="btn btn-danger">' . $lang['delete'] .'</button>
						  </div>
						</div>
					  </div>
				</td>
				<td>
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $catalog['id'] . '" type="checkbox" name="checks[]" value="' . $catalog['id'] . '">
						<label for="checkbox' . $catalog['id'] . '"></label>
					</div>
				</td>
			</tr>';
		}	
		echo '</tbody>
				<tfoot class="footer-menu">
                    <tr align="right">                    
					  <td align="right" colspan="10">
                        <nav align="right" class="text-right">						
							<select style="width: 250px; display: inline-block;" class="form-control" name="act">
								<option value="allowComment">' . $lang['catalog_allow_comment'] . '</option>
								<option value="blockComment">' . $lang['catalog_block_comment'] . '</option>
								<option value="blockIndex">' . $lang['catalog_remove_main'] .'</option>
								<option value="setIndex">' . $lang['catalog_set_main'] .'</option>
								<option value="nowDate">' . $lang['catalog_set_nowdate'] . '</option>
								<option value="activate">' . $lang['do_activation'] . '</option>
								<option value="deActivate">' . $lang['do_deactivation'] . '</option>
								<option value="reActivate">' . $lang['do_reactivation']  . '</option>		
								<option value="delete">' . $lang['delete'] . '</option>
							</select>
							<input name="submit" type="submit" class="btn btn-success" style="display: inline-block;" id="sub" value="' .  $lang['doit'] . '" /><span class="pd-l-sm"></span>							
						 </nav>
                      </td>					 
                    </tr>
                  </tfoot> 
				</table> 
			</form>				
          </div>
        </div';	
		$all = $db->numRows($db->query("SELECT * FROM " . DB_PREFIX . "_catalog $whereC"));
		$adminTpl->pages($page, $admin_conf['num'], $all, ADMIN.'/module/catalog/{page}');
	} 
	else
	{
		$adminTpl->info($lang['catalog_empty'], 'empty', null, $lang['catalog_list'], $lang['catalog_add'], ADMIN.'/module/catalog/add');	
	}		
	echo'</div>';	
	$adminTpl->admin_foot();
} 


function catalog_add($nid = null) 
{
global $adminTpl, $core, $db, $core, $config, $lang;
	if(isset($nid)) 
	{	
		$bb = new bb;
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '" . $nid . "'");
		$catalog = $db->getRow($query);
		$id = $catalog['id']; 
		$author = $catalog['author']; 
		$date = gmdate('d.m.Y H:i', $catalog['date']); 
		$tags = $catalog['tags']; 
		$groups = $catalog['groups']; 
		$altname = $catalog['altname']; 
		$keywords = $catalog['keywords']; 
		$description = $catalog['description']; 		
		$allow_comments = $catalog['allow_comments']; 
		$allow_rating = $catalog['allow_rating']; 
		$allow_index = $catalog['allow_index']; 
		$score = $catalog['score']; 
		$votes = $catalog['votes']; 	
		$preview = $catalog['preview']; 
		$price = $catalog['price']; 
		$views = $catalog['views']; 
		$comments = $catalog['comments']; 
		$sync_title = $catalog['sync_title']; 
		$sync_price = $catalog['sync_price']; 
		$fields = unserialize($catalog['fields']); 
		$fix = $catalog['fixed']; 
		$active = ($catalog['active'] == 2 ? 0 : $catalog['active']);
		
		$section = $catalog['section']; 
		$section_array = explode(',', $section);
		
		$cat = $catalog['cat']; 
		$cat_array = explode(',', $cat);
		$catttt = explode(',', $cat);
		
		$edit = true;
		$grroups = explode(',', $groups);
		$firstCat = $catttt[1];
		$deleteKey = array_search($firstCat, $catttt);
		unset($catttt[$deleteKey]);
		$langMassiv = $core->getLangList(true);
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_langs WHERE postId = '" . $id . "' AND module='catalog'");
		while($langs = $db->getRow($query))
		{
			$title[$langs['lang']] = prepareTitle($langs['title']);
			$fulltitle[$langs['lang']] = prepareTitle($langs['fulltitle']);
			$short[$langs['lang']] = $bb->htmltobb($langs['short']);
			$full[$langs['lang']] = $bb->htmltobb($langs['full']);
		}		
		$lln = $lang['catalog_edits'];
		$dosave = $lang['update'];
		$remote = ADMIN.'/module/catalog/ajax/isurl/update';
	} 
	else 
	{
		$id = false; 
		$fulltitle = false; 
		$title = false; 
		$short = false; 
		$full = false; 
		$author = $core->auth->user_info['nick']; 		
		$date = gmdate('d.m.Y H:i'); 
		$tags = false; 
		$cat = false; 
		$altname = false; 
		$keywords = false; 
		$description = false; 		
		$allow_comments = 1; 
		$allow_rating = 1; 
		$allow_index = 1; 
		$sync_title = 1; 
		$sync_price = 1;
		$score = false; 
		$votes = false; 
		$views = false; 
		$preview = false; 
		$comments = false; 
		$price = 0; 
		$fields = false; 
		$fix = ''; 
		$active = 1;
		$edit = false;
		$catttt = array();
		$grroups = array();
		$firstCat = '';
		$lln = $lang['catalog_add'];
		$dosave = $lang['add'];
		$remote = ADMIN.'/module/catalog/ajax/isurl';
	}
	$queryXF = $db->query("SELECT * FROM ".DB_PREFIX."_xfields WHERE module='catalog'");
	$adminTpl->admin_head($lang['catalog'] . ' | ' . $lln);
	$adminTpl->js_code[] = '	
        var selectList = $(\'.admin-form select\');
        selectList.each(function (i, e) {
          $(e).on(\'change\', function () {
            if ($(e).val() == "0") $(e).addClass("empty");
            else $(e).removeClass("empty")
          });
        });
        selectList.each(function (i, e) {
          $(e).change();
        });
        $("input#tagsinput").tagsinput({
          tagClass: function (item) {
            return \'label label-default\';
          }
        });		
        $(".select2-single").select2();
		// Dual List Plugin Init
        var demo1 = $(\'.demo1\').bootstrapDualListbox({
          nonSelectedListLabel: \'Options\',
          selectedListLabel: \'Selected\',
          preserveSelectionOnMove: \'moved\',
          moveOnSelect: true,
          nonSelectedFilter: \'\'
        });
        $("#demoform").submit(function () {
          alert("Options Selected: " + $(\'.demo1\').val());
          return false;
        });

		
		';	
	$validation_array = array(		
		'title' => array(
			'required' =>  array('true', $lang['catalog_add_name_err'])			
		),
		'translit' => array(
			'required' =>  array('true', $lang['catalog_add_url_err_1']),
			'remote' =>  array($remote,  $lang['catalog_add_url_err_3'])
		),
		'date' => array(
			'required' =>  array('true', $lang['catalog_add_date_err']),
		),
		'author' => array(
			'required' =>  array('true', $lang['catalog_add_author_err']),
		)
	);
	validationInit($validation_array);
	datetimepickerInit('date');
	ajaxInit();
	fancyboxInit();
	$section_arr  = aCaSectionList();
	$cats_arr = aCaCatList('all');
	echo '<section id="content" class="table-layout animated fadeIn">			
				<div class="tray tray-center">
					<form action="{MOD_LINK}/save" enctype="multipart/form-data" role="form" method="POST" name="content" id="admin-form">
					<div class="panel mb25 mt5">
						<div class="panel-heading br-b-ddd"><span class="panel-title hidden-xs">'.$lln.'</span>
							<ul class="nav panel-tabs-border panel-tabs">
								<li class="active"><a href="#tab1_1" data-toggle="tab">'.$lang['catalog_add_tab_general'].'</a></li>
								<li><a href="#tab1_4" data-toggle="tab">Настройки товара</a></li>
								<li><a href="#tab1_2" data-toggle="tab">Другие настройки</a></li>
								<li><a href="#tab1_3" data-toggle="tab">'.$lang['catalog_add_tab_access'].'</a></li>
								'.(($db->numRows($queryXF) > 0) ? '<li><a href="#tab1_4" data-toggle="tab">'.$lang['catalog_add_tab_xfields'].'</a></li>' : '').'
							</ul>
						</div>						
						<div class="panel-body p20 pb10">
							<div class="tab-content pn br-n admin-form">
								<div id="tab1_1" class="tab-pane active">
									<div class="section row mbn">
										<div class="col-md-9 pl15">
											<div class="section row mb15">
												<div class="col-xs-6">
													<label for="title" class="field prepend-icon">
														<input id="title" type="text" name="title" placeholder="'.$lang['catalog_add_name'].'" class="event-name gui-input br-light light" '.(isset($nid) ? '' : 'onchange="getTranslit(gid(\'title\').value, \'translit\');"').' value="' . (isset($title[$config['lang']]) ? $title[$config['lang']] : '') . '">				
														<label for="title" class="field-icon"><i class="fa fa-pencil"></i></label>
													</label>
												</div>
												<div class="col-xs-6">
													<label for="translit" class="field prepend-icon">
														<input id="translit" type="text" name="translit" placeholder="'.$lang['catalog_add_url'].'" class="event-name gui-input br-light light" value="'.$altname.'">
														<label for="translit" class="field-icon"><i class="fa fa-link"></i></label>
													</label>
												</div>
											</div>
											<div class="section row mb15">
												<div class="col-xs-6">
													<label for="author" class="field prepend-icon">
														<select name="author" class="select2-single form-control">';
														$query_users_list = $db->query("SELECT * FROM ".DB_PREFIX."_users");
														if($db->numRows($query_users_list) > 0) 
														{
															while($users_list = $db->getRow($query_users_list)) 
															{
															$selected = ($users_list['nick'] == $author) ? "selected" : "";
																echo '<option value="'.$users_list['nick'].'" '.$selected.'>'.$users_list['nick'].'</option>';
															}
														}																
														echo'	</select>	
														<label for="translit" class="field-icon"><i class="fa fa-user"></i></label>												
													</label>
												</div>
												<div class="col-xs-6">
													 <label for="date" class="field prepend-picker-icon">
														<input id="date" type="text" name="date" placeholder="'.$lang['catalog_add_date'].'" class="gui-input" value="'.$date.'">
													</label>
												</div>
											</div>
											<div class="section mb10">												
												<label class="field mb15">'.$lang['catalog_add_tags'].':</label>
												<input id="tagsinput" name="tags" type="text" value="'.$tags.'" class="bg-light mt10">
											</div>
											
									</div>
									<div class="col-md-3">
										<div data-provides="fileupload" class="fileupload fileupload-'.($preview ? 'exists' : 'new').' admin-form">
											<div class="fileupload-preview thumbnail mb15">
												<img '.($preview ? 'src="'.$config['url'].$preview.'"' : 'data-src="holder.js/100%x147/text:'.$lang['catalog_add_mini'].'"').' alt="holder">
											</div>
											<span class="button btn-system btn-file btn-block ph5">
												<span class="fileupload-new">'.$lang['upload'].'</span>
												<span class="fileupload-exists">'.$lang['upload_again'].'</span>
												<input name="preview" type="file">
											</span>
											
										</div>
										<div class="checkbox-custom checkbox-warning mb5 mt15 text-center">
										  <input id="preview_del" name="preview_del" type="checkbox">
										  <label for="preview_del">'.$lang['catalog_add_mini_del'].'</label>
										</div>
									</div>
								</div>
							</div>
							
							
								<style>
		#cc .checkbox {
        border: 0 !important;
	 background: none !important; 
	 width: 400px;
}

		</style>
		
							<div id="tab1_4" class="tab-pane">							
								<div class="section row mb15">												
									<div class="col-xs-6">												
										<label for="section" class="field" id="cc">
										
										
										
										<select id="multiselect2" name="section[]" multiple="multiple">';
								
									foreach ($section_arr as $cid => $name) 
												{
													if($section_array) $selected = in_array($cid, $section_array) ? "selected" : "";
													
													echo '<option value="' . $cid . '" ' . $selected . '>' . $name . '</option>';
												}
                              
                      echo     ' </select>
										
										
											
										</label>
									</div>
									<div class="col-xs-6">
										<label for="price" class="field prepend-icon">
											<input id="price" type="text" name="price" placeholder="Введите цену товара" class="event-name gui-input br-light light" value="'.$price.'">
											<label for="price" class="field-icon"><i class="fa fa-rub"></i></label>
													</label>
												</div>
											</div> 
										
								<div class="section row mbn">
																	
             <div class="col-xs-12">		

                    <select multiple="multiple" size="10" name="category[]" class="demo1">';
					foreach ($cats_arr as $cid => $name) 
															{
																if($cat_array) $selected = in_array($cid, $cat_array) ? "selected" : "";
																echo '<option value="' . $cid . '" ' . $selected . '>' . $name . '</option>';
															}
                
         echo       '</select>
                 
            </div>
								</div>
								<br>
							</div>
							<div id="tab1_2" class="tab-pane">
								<div class="section row mbn">
									<div class="col-xs-6 pr15">
										<div class="section mb10">
											<label class="field mb5">'.$lang['catalog_add_fulltitle'].':</label>
											<label for="fulltitle" class="field prepend-icon">												
												<input id="fulltitle" type="text" name="fulltitle" placeholder="'.$lang['catalog_add_fulltitle_pre'].'" class="event-name gui-input bg-light br-light" value="' .  (isset($fulltitle[$config['lang']]) ? $fulltitle[$config['lang']] : '') . '">
												<label for="fulltitle" class="field-icon"><i class="fa fa-code"></i></label>
											</label>
										</div>
										<div class="section mb10">
											<label class="field mb5">'.$lang['catalog_add_keywords'].':</label>
											<label for="keywords" class="field prepend-icon">												
												<input id="keywords" type="text" name="keywords" placeholder="'.$lang['catalog_add_keywords_pre'].'" class="event-name gui-input bg-light br-light" value="' . $keywords . '">
												<label for="keywords" class="field-icon"><i class="fa fa-edit"></i></label>
											</label>
										</div>
										<div class="section mb10">
											<label class="field mb5">'.$lang['catalog_add_description'].':</label>
											<label for="description" class="field prepend-icon">
												<input id="description" type="text" name="description" placeholder="'.$lang['catalog_add_description_pre'].'" class="event-name gui-input bg-light br-light" value="' . $description . '">
												<label for="description" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
											</label>
										</div>
										<hr class="alt short mv15">
										<p class="text-muted"><span class="fa fa-exclamation-circle text-warning fs15 pr5"></span> '.$lang['catalog_add_seo'].'</p>
									</div>
									<div class="col-xs-6">
										<label class="field option">
										 '.checkbox('status', $active, $lang['catalog_add_activate']).'
										</label><br>
										<label class="field option mt15">
										 '.checkbox('sync_title', $sync_title, 'Синхронизировать заголовок?').'
										</label><br>
										<label class="field option mt15">
										 '.checkbox('sync_price', $sync_price, 'Синхронизировать прайс?').'
										</label><br>										 
										<label class="field option mt15">
										 '.checkbox('index', $allow_index, 'Рекомендуемые?').'
										</label>
										<input type="hidden" name="comments" value="0" />
										<input type="hidden" name="rating" value="0" />
										<input type="hidden" name="fix" value="0" />
									</div>
								</div>
								<br>
							</div>
							<div id="tab1_3" class="tab-pane">
								<div class="section">
									<b>'.$lang['catalog_add_accsess_title'].'</b>
									<p>'.$lang['catalog_add_accsess'].'</p>
									<hr class="alt short mv15">
									<label class="field select-multiple">
										<select name="groups[]" id="group" class="form-control" multiple>
											<option value="" ' . (empty($grroups) ? 'selected' : '') . '">'. $lang['catalog_add_accsess_all'] .'</option>';
											$query = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups` ORDER BY admin DESC,moderator DESC,user DESC,guest DESC,banned DESC");
											while($rows = $db->getRow($query)) 
											{
												$selected = in_array($rows['id'], $grroups) ? "selected" : "";
												echo '<option value="' . $rows['id'] . '" ' . $selected . '>' . $rows['name'] . '</option>';
											}
									echo' </select>
									</label>
								</div>
							</div>';
							if($db->numRows($queryXF) > 0)
							{
								echo '<div id="tab1_4" class="tab-pane form-horizontal">';
								while($xfield = $db->getRow($queryXF)) 
								{
									echo '<div class="form-group">
											<label class="col-sm-3 control-label">'. $xfield['title'] .'</label>
												<div class="col-sm-4">';
									if($xfield['type'] == 3)
									{
										$dxfield = array_map('trim', explode("\n", $xfield['content']));
										$xfieldChange = '<select class="form-control" name="xfield[' . $xfield['id'] . ']"><option value="">Пусто</option>';
										foreach($dxfield as $xfiled_content)
										{
											$xfieldChange .= '<option value="' . $xfiled_content . '" ' . (isset($fields[$xfield['id']][1]) && $fields[$xfield['id']][1] == $xfiled_content ? 'selected' : ''). '>' . $xfiled_content . '</option>';
										}
										$xfieldChange .= '</select>';
									}
									elseif($xfield['type'] == 2)
									{
										$xfieldChange = '<textarea class="form-control" name="xfield[' . $xfield['id'] . ']" >' . (!empty($fields[$xfield['id']][1]) ? $fields[$xfield['id']][1] : ($id ? '' : $xfield['content'])) . '</textarea>';
									}
									else
									{
										$xfieldChange = '<input type="text" class="form-control" name="xfield[' . $xfield['id'] . ']" value="' . (!empty($fields[$xfield['id']][1]) ? $fields[$xfield['id']][1] : ($id ? '' : $xfield['content'])) . '" />';
									}
									echo $xfieldChange;
									echo '</div></div>
											<input type="hidden" name="xfieldT[' . $xfield['id'] . ']" value="' . $xfield['title'] . '" />';
								}
								echo '</div>';
							}
						echo'</div>
					</div>
				</div>';
$adminTpl->js_code[] = " // Init Boostrap Multiselects
      
        $('#multiselect2').multiselect({
          includeSelectAllOption: true
        });
       
";				
				if ($id == false)
				{
					$dir = ROOT.'files/catalog/temp';
					if (!file_exists($dir))
					{
						mkdir($dir, 0777);
						
					}
					$_SESSION["RF"]["fff"] ="files/catalog/temp/";		
				}
				else
				{
				
					$_SESSION["RF"]["fff"] ="files/catalog/".$id."/";
				}
				echo'
				<div class="tab-block mb25">
					<ul class="nav nav-tabs_editor nav-tabs-right">
						<li class="active">
							<a href="#tab1" data-toggle="tab" aria-expanded="true">'.$lang['catalog_add_short'].'</a>
						</li>
						<li class="">
							<a href="#tab2" data-toggle="tab" aria-expanded="false">'.$lang['catalog_add_full'].'</a>
						</li>
						<li class="">
							<a id="fbox" data-fancybox-type="iframe" href="usr/plugins/filemanager/dialog.php?type=2"><i class="fa fa-folder-open-o text-purple"></i> '.$lang['catalog_add_upload'].'</a>
						</li>    
					</ul>
					<div class="tab-content_editor">
						<div id="tab1" class="tab-pane active">'
							.adminArea('short[' . $config['lang'] . ']', (isset($short[$config['lang']]) ? $short[$config['lang']] : ''), 5, 'textarea', 'onchange="caa(this);"', true, true).'
						</div>
						<div id="tab2" class="tab-pane">'
							.adminArea('full[' . $config['lang'] . ']', (isset($full[$config['lang']]) ? $full[$config['lang']] : ''), 5, 'textarea', 'onchange="caa(this);"', true, false).'
						</div>
					</div>
				</div>
				<div class="btn-group">
				  <button type="button" class="btn btn-hover btn-success" onclick="genPreview(\'title\', \'short[' . $config['lang'] . ']\', \'full[' . $config['lang'] . ']\');"><i class="fa fa-desktop"></i></button>
				  <button  type="submit" class="btn btn-success dark">' . $dosave . '</button>
				</div>';				 
				if($edit) 
				{
					echo '<script>
							$(\'#admin-form\').submit(function (e) {
								var form = this;
								e.preventDefault();
								setTimeout(function () {
									form.submit();
								}, 1000); // in milliseconds
							});
							</script>
							<input type="hidden" name="edit" value="1" />';
					if($catalog['active'] == 2)
					{
						echo '<input type="hidden" name="from_user" value="1" />';
					}
					echo '<input type="hidden" name="edit_id" value="'.$id.'" />';
				}	
				if(isset($nid))
				{
					echo '<input type="hidden" name="oldAltName" value="'.$altname.'" />';
				}				
	echo'</form>
		</div>
	</section>';	
	$adminTpl->admin_foot();
} 
 
function sync() 
{
	global $adminTpl, $core, $db, $admin_conf, $url, $lang;
	$adminTpl->admin_head($lang['catalog'] . ' | ' . $lang['catalog_list']);
	echo '<div id="content" class="animated fadeIn">';	
	/*
	require_once(ROOT.'sync.php');
	$msg = '';
	$author = 'admin';
	$ttime = 'UNIX_TIMESTAMP(NOW())';
	$date = !empty($_POST['date']) ? filter($_POST['date']) : $ttime;	
	$info = '';
	//очистка таблиц
	
	//$db->query("DELETE FROM `JMY_langs` WHERE  module='catalog'");
	//$db->query("TRUNCATE JMY_catalog");
	//$db->query("TRUNCATE JMY_catalog_cats");
	//$db->query("DELETE FROM `JMY_categories` WHERE `module` = 'catalog'	");
	//$db->query("TRUNCATE JMY_catalog_manufacture");
	//$db->query("TRUNCATE JMY_catalog_xfields");	
	//$db->query("TRUNCATE JMY_catalog_xfields_values");	
	
	//импорт категорий
	$queryCats = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_cats");	
	$cats_array = array();
	if($db->numRows($queryCats) > 0)
	{
		while($resultCats = $db->getRow($queryCats))
		{	
			$cats_array[$resultCats['id_1c']] = $resultCats['cid'];
		}
	}	
	foreach ($arr_category as $items_cats)
	{		
				
		if(!isset($cats_array[$items_cats->id]))
		{
			$catsParent['cid'] = 0;	
			if (!empty($items_cats->parent))
			{
				$queryCatsParent = $db->query("SELECT cid FROM ".DB_PREFIX."_catalog_cats WHERE id_1c = '".$items_cats->parent."' LIMIT 1");
				$catsParent = $db->getRow($queryCatsParent);
			}			
			$name = filter($items_cats->name);
			$altname = translit($name);
			$db->query("INSERT INTO `" . DB_PREFIX . "_categories` (`name`, `altname`, `parent_id`, `module`) VALUES ('" . $db->safesql($name) . "', '" . $altname . "', '" . $catsParent['cid'] . "', 'catalog');");			
			$db->query("INSERT INTO `" . DB_PREFIX . "_catalog_cats` (`id_1c`, `cid`) VALUES ('" . $items_cats->id . "', '" . $db->insert_id() . "');");
			$info .= str_replace(array('[name]', '[altname]'), array($name, $altname), $lang['cats_fast_save']);
		}		
	}
	$adminTpl->info($info, 'info', null, $lang['info'], $lang['catalog_list'], ADMIN.'/module/catalog/');
	//импорт дополнительных реквизитов и их значений
	$info = '';
	foreach ($arr_property as $items_property)
	{		
		$queryProperty = $db->query("SELECT id FROM ".DB_PREFIX."_catalog_xfields WHERE id_1c = '".$items_property->id."' LIMIT 1");		
		if($db->numRows($queryProperty) <= 0)
		{
			$db->query("INSERT INTO `" . DB_PREFIX . "_catalog_xfields` (`id_1c`, `name`, `active`) VALUES ('" . $items_property->id . "', '" . $db->safesql($items_property->name) . "', '0');");
			$info .= str_replace(array('[name]'), array($items_property->name), 'Реквизит [name] добавлен!<br>');
			if (!empty($items_property->values))
			{
				foreach($items_property->values as $key => $value) 				
				{	
					
					$queryPropertyValues = $db->query("SELECT id FROM ".DB_PREFIX."_catalog_xfields_values WHERE id_1c = '".$key."' LIMIT 1");	
					if($db->numRows($queryPropertyValues) <= 0)
					{
						$queryP = $db->query("SELECT id FROM ".DB_PREFIX."_catalog_xfields WHERE id_1c = '".$items_property->id."' LIMIT 1");
						$property = $db->getRow($queryP);
						$db->query("INSERT INTO `" . DB_PREFIX . "_catalog_xfields_values` (`id_1c`, `xfield`, `value`) VALUES ('" . $key . "', '" . $property['id'] . "', '".$value."');");
						$info .= str_replace(array('[name]', '[xfield]'), array($value, $items_property->name), '+ Значение [name] для реквизта [xfield] добавлено!<br>');
					}
				}
			}			
		}		
	}	
	$adminTpl->info($info, 'info', null, $lang['info'], $lang['catalog_list'], ADMIN.'/module/catalog/');
	$i = 0;
	//импорт товаров остатков и цен
	foreach ($arr as $items )
	{
		$i++;
		if (!empty($items->name))
		{
			if (!empty($items->price['27b9ce3c-8267-11e7-a43e-704d7b330583']['value']))
			{
				$price = $items->price['27b9ce3c-8267-11e7-a43e-704d7b330583']['value'];
			}
			else
			{
				$price = 0;
			}
			$properties = array();
			$active = 0;	
			$allow_actual = 0;
			$preview = !empty($items->images) ? '/files/import/'.$items->images : false;	
			$manufacture = !empty($items->manufacture_id) ? $items->manufacture_id : false;			
			$sku = !empty($items->sku) ? $items->sku : false;			
			$description = !empty($items->description) ? $items->description : false;
			$unit = !empty($items->unit) ? $items->unit : false;
			if (!empty($items->properties))
			{
				$properties = $items->properties;		
				if (!empty($properties['a2fb1c2b-871a-11e7-a43e-704d7b330583']))
				{
					$active = (($properties['a2fb1c2b-871a-11e7-a43e-704d7b330583'] == 'Да') ? '1' : '0');
					unset($properties['a2fb1c2b-871a-11e7-a43e-704d7b330583']);
				}		
				if (!empty($properties['0c5ab4a8-871b-11e7-a43e-704d7b330583']))
				{
					$allow_actual = (($properties['0c5ab4a8-871b-11e7-a43e-704d7b330583'] == 'Да') ? '1' : '0');
					unset($properties['0c5ab4a8-871b-11e7-a43e-704d7b330583']);
				}
				sort($properties);
			}	
			if (!empty($items->manufacture_id))
			{
				$queryItems = $db->query("SELECT id FROM ".DB_PREFIX."_catalog_manufacture WHERE id_1c = '".$items->manufacture_id."' LIMIT 1");
				if($db->numRows($queryItems) > 0)
				{
					$update = '';
				}
				else
				{
					$insert = $db->query("INSERT INTO `" . DB_PREFIX . "_catalog_manufacture` ( `id` , `id_1c`, `name`) VALUES (NULL, '" . $items->manufacture_id . "', '" . $items->manufacture_name . "');");
				}
			}
			$queryItems = $db->query("SELECT id FROM ".DB_PREFIX."_catalog WHERE id_1c = '".$items->id."' LIMIT 1");
			if($db->numRows($queryItems) > 0)
			{
				$item = $db->getRow($queryItems);
				$update = $db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `price` = '" . $price . "', `sku` = '" . $sku . "',  `cat` = '," . $cats_array[$items->categories[0]] . ",', `manufacture` = '" . $manufacture . "', `unit` = '" . $unit . "' WHERE `id` = '" . $item['id'] . "' LIMIT 1 ;");
				$db->query("UPDATE `" . DB_PREFIX . "_langs` SET `title` = '" . $db->safesql(processText($items->name)) . "', `fulltitle` = '" . $db->safesql(processText($items->name)) . "', `short` = '".$description."' WHERE `postId` ='" . $item['id'] . "' AND `module` ='catalog' AND `lang`='ru' LIMIT 1 ;");			
				$msg .= $items->name.': обновлён!<br>';
			}
			else
			{
				$insert = $db->query("INSERT INTO `" . DB_PREFIX . "_catalog` ( `id` , `id_1c`, `sku`, `cat`, `unit`, `author` , `date`, `price`, `quantity`, `altname`, `preview`, `manufacture`, `allow_comments` , `allow_rating` , `allow_index` , `allow_actual` ,`score` , `votes` , `views` , `comments`, `fields`, `groups` , `fixed` , `active` ) VALUES (NULL, '" . $items->id . "', '" . $sku . "', '," . $cats_array[$items->categories[0]] . ",', '" . $unit . "', '" . $author . "', '" . $date . "', '" . $price . "', '" . $items->quantity . "', '" . rand(100, 999).$db->safesql(translit($items->name)) . "', '" . $preview . "', '" . $manufacture . "', '1', '1', '0', '0', '".$allow_actual."', '0', '0', '0', '".json_encode($properties)."', ',0,', '0', '".$active."');");
				if (!empty($items->manufacture_id))
				{
					$db->query("UPDATE `" . DB_PREFIX . "_catalog_manufacture` SET sum=sum+1 WHERE `id_1c` ='" . $items->manufacture_id . "' LIMIT 1", true);
				}
				if($insert) 
				{
					$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id_1c = '" . $items->id . "'");
					$catalog = $db->getRow($query);			
					$ntitle = filter(trim(htmlspecialchars_decode($items->name, ENT_QUOTES)), 'title');
					$ftitle = filter(trim(htmlspecialchars_decode($items->name, ENT_QUOTES)), 'title');						
					$db->query("INSERT INTO `" . DB_PREFIX . "_langs` ( `postId` , `module` , `title`, `fulltitle`, `lang` ) 	VALUES ('" . $catalog['id'] . "', 'catalog', '" . $db->safesql(processText($ntitle)) . "', '" . $db->safesql(processText($ftitle)) . "', 'ru');");	
					$dir = ROOT.'files/catalog/'.$catalog['id'];
					if (!file_exists($dir))
					{
						mkdir($dir, 0777);
					}		
				}		
				$msg .= $items->name.': добавлен!<br>';				
			}
		}
		if ($i % 50 == 0)
			usleep(1);
		
	}
	*/
	/*
	$queryM = $db->query("SELECT manufacture FROM ".DB_PREFIX."_catalog WHERE manufacture <> ''");
	$manufacture = array();
	if($db->numRows($queryM) > 0) 
	{
		while($catalogM = $db->getRow($queryM)) 
		{			
			$manufacture[$catalogM['manufacture']] = $manufacture[$catalogM['manufacture']] + 1;
		}
	}
	foreach($manufacture as $key => $value) 
	{
		$update = $db->query("UPDATE `" . DB_PREFIX . "_catalog_manufacture` SET `sum` = '" . $value . "' WHERE `id_1c` = '" . $key . "' LIMIT 1 ;");
	}	
	print_r($manufacture);*/

	$server = 'mysql.izh.one';
	$user = 'tornado_user';
	$password = 'iaApAwbJ';
	
	$dblink = mysql_connect($server, $user, $password); 

	if($dblink)
		echo 'dvdv';
	else
		die('Ошибка подключения к серверу баз данных');

	// Теперь в переменной $dblink содержится 
	// ссылка на временное подключение к базе данных

	$adminTpl->info($msg, 'info', null, $lang['info'], $lang['catalog_list'], ADMIN.'/module/catalog/');
	echo '</div>';
	$adminTpl->admin_foot();
}	
 
function catalog_save() 
{
global $adminTpl, $core, $db, $cats, $groupss, $config, $catalog_conf, $lang;
	$bb = new bb;
	$word_counter = new Counter();	
	$title = $_POST['title'];
	$langTitle = isset($_POST['langtitle']) ? $_POST['langtitle'] : '';
	$langTitle[$config['lang']] = $title;
	$fulltitle[$config['lang']] = isset($_POST['fulltitle']) ? $_POST['fulltitle'] : ''; 	
	$author = filter($_POST['author'], 'nick');
	$ttime = 'UNIX_TIMESTAMP(NOW())';
	$date = !empty($_POST['date']) ? filter($_POST['date']) : $ttime;	
	$oldAltName = !empty($_POST['oldAltName']) ? filter($_POST['oldAltName']) : '';
	$tags = isset($_POST['tags']) ? mb_strtolower(filter($_POST['tags'], 'a'), 'UTF-8') : mb_strtolower(filter($gen_tag, 'a'), 'UTF-8');
	// $tags = htmlspecialchars($tags, ENT_QUOTES); // Перевод кавычек в специальные HTML символы
	$translit = ($_POST['translit'] !== '') ? mb_strtolower(str_replace(array('-', ' '), array('_', '_'), $_POST['translit']), 'UTF-8') : translit($title);	
	$full= isset($_POST['full']) ? $_POST['full'] : '';
	$short= isset($_POST['short']) ? $_POST['short'] : '';		
	$xfield = isset($_POST['xfield']) ? $_POST['xfield'] : '';
	$xfieldT = isset($_POST['xfieldT']) ? ($_POST['xfieldT']) : '';
	$category = isset($_POST['category']) ? array_unique($_POST['category']) : '0';
	$section = isset($_POST['section']) ? array_unique($_POST['section']) : '0';
	$groups = isset($_POST['groups']) ? $_POST['groups'] : '0';
	$comment = isset($_POST['comments']) ? 1 : 0;
	$sync_title = isset($_POST['sync_title']) ? 1 : 0;
	$sync_price = isset($_POST['sync_price']) ? 1 : 0;
	$rating = isset($_POST['rating']) ? 1 : 0;
	$index = isset($_POST['index']) ? 1 : 0;
	$price = isset($_POST['price']) ? intval($_POST['price']) : 0;
	$status = isset($_POST['status']) ? 1 : 0;
	$fix = isset($_POST['fix']) ? 1 : 0;	
	$edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : '';
	$cnt = (($full['ru'] != '') ? $full['ru'] : $short['ru']);	
	$gen_tag =  $word_counter->get_keywords(substr($cnt, 0, 500)); 
	$keywords = !empty($_POST['keywords']) ? $_POST['keywords'] : $word_counter->get_keywords(substr($cnt, 0, 500)); 	
	$newcnt = $bb->parse(processText(filter(fileInit('catalog', $edit_id, 'content', $cnt), 'html')), $edit_id, true);		
	$description = !empty($_POST['description']) ? $_POST['description'] : substr(strip_tags($newcnt), 0, 150); 	
	if($edit_id > 0)
	{			
		$old_dataQ = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '" . $edit_id . "'");
		$old_data = $db->getRow($old_dataQ);
	}	
	if($date != $ttime)
	{
		$parseDate = explode(' ', $date);
		$subDate = explode('.', $parseDate[0]);
		if(isset($parseDate[1]))
		{
			$subTime = explode(':', $parseDate[1]);
		}
		else
		{
			$subTime[0] = 12;
			$subTime[1] = 0;
		}
		$date = gmmktime($subTime[0], $subTime[1], 0, $subDate[1], $subDate[0], $subDate[2]);
	}
	if(is_array($category)) 
	{
		$firstCat = $category[0];
		unset($category[0]);
		$deleteCat = array_search($firstCat, $category);
		unset($category[$deleteCat]);
		$category[0] = $firstCat;
		ksort($category);
		foreach($category as $cid) 
		{
			$cats .= intval($cid) . ",";
		}
	}
	else 
	{
		$cats  = $category . ',';
	}
	
	if(is_array($section)) 
	{ 
		ksort($section);
		foreach($section as $cid) 
		{
			$sections .= intval($cid) . ",";
		}
	}
	else 
	{
		$sections  = $section . ',';
	}
	
	$fieldsSer = '';
	if(!empty($xfield))
	{
		foreach($xfield as $xId => $xContent)
		{
			if(!empty($xContent) && $xId > 0 && !empty($xfieldT[$xId]))
			{
				$xContent = processText(filter($xContent, 'html'));
				$xId = intval($xId);
				$xfieldT[$xId] = filter($xfieldT[$xId], 'title');
				$fileds[$xId] = array($xfieldT[$xId], $xContent);
			}
		}
		
		$fieldsSer = serialize($fileds);
	}
	
	$cats = ',' . $cats;	
	$sections = ',' . $sections;
	
	if(is_array($groups)) 
	{
		foreach($groups as $gid) 
		{
			$groupss .= intval($gid) . ",";
		}
	}
	else 
	{
		$groupss  = $groups . ',';
	}
	$groupss = ',' . $groupss;
	$lln = $lang['catalog_adds'];
	if(isset($_POST['edit'])) 
		{
			$lln = $lang['catalog_edits'];
		}
	$adminTpl->admin_head($lang['catalog'] . ' | ' . $lln);
	echo '<div id="content" class="animated fadeIn">';	
	if($title && $short['ru'] && $author && $translit) 
	{		
		if(isset($_POST['edit'])) 
		{
			foreach($langTitle as $k => $v)
			{
				$ntitle = filter(trim(htmlspecialchars_decode($v, ENT_QUOTES)), 'title');
				$ftitle = filter(trim(htmlspecialchars_decode($fulltitle[$k], ENT_QUOTES)), 'title');
				$nshort = $bb->parse(processText(filter(fileInit('catalog', $edit_id, 'content', $short[$k]), 'html')), $edit_id, true);
				$nfull = $bb->parse(processText(filter(fileInit('catalog', $edit_id, 'content', $full[$k]), 'html')), $edit_id, true);	
				if(isset($_POST['empty'][$k]) && trim($v) != '' && trim($short[$k]) != '')
				{
					$db->query("INSERT INTO `" . DB_PREFIX . "_langs` ( `postId` , `module` , `title` , `fulltitle` , `short` , `full` , `lang` ) VALUES ('" . $edit_id . "', 'catalog', '" . $db->safesql(processText($ntitle)) . "', '" . $db->safesql(processText($ftitle)) . "', '" . $db->safesql($nshort) . "', '" . $db->safesql($nfull) . "' , '" . $k . "');");
				}
				elseif(!isset($_POST['empty'][$k])  && (trim($v) == '' OR trim($short[$k]) == ''))
				{
					$db->query("DELETE FROM `" . DB_PREFIX . "_langs` WHERE `postId` ='" . $edit_id . "' AND `module` ='catalog' AND `lang`='" . $k . "' LIMIT 1");
				}
				elseif(!isset($_POST['empty'][$k]) && trim($v) != '' && trim($short[$k]) != '')
				{
					$db->query("UPDATE `" . DB_PREFIX . "_langs` SET `title` = '" . $db->safesql(processText($ntitle)) . "', `fulltitle` = '" . $db->safesql(processText($ftitle)) . "', `short` = '" . $db->safesql($nshort) . "', `full` = '" . $db->safesql($nfull) . "' WHERE `postId` ='" . $edit_id . "' AND `module` ='catalog' AND `lang`='" . $k . "' LIMIT 1 ;");
				}
			}			
			if(!empty($tags) && $status == 1)
			{
				if($old_data['tags'] != $tags)
				{
					workTags($edit_id, $old_data['tags'], 'delete');
					workTags($edit_id, $tags, 'add');
				}
			}			
			if(isset($_POST['from_user']) && $status == 0)
			{
				$status = 2;
			}			
			if(($old_data['active'] == 2 && $status == 1) || ($old_data['active'] == 0 && $status == 1))
			{
				//$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_catalog=user_catalog+1 WHERE `nick` ='" . $db->safesql($author) . "' LIMIT 1", true);
				//user_points($author, 'add_catalog');
				//$access = $db->getRow($db->query("SELECT id FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE nick = '" . $db->safesql($author) . "'"));
				//delcache('userInfo_'.$access['id']);
			}
			
			if($old_data['active'] == 1 && $status == 0)
			{
				//$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_catalog=user_catalog-1 WHERE `nick` ='" . $db->safesql($author) . "' LIMIT 1", true);
				//$access = $db->getRow($db->query("SELECT id FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE nick = '" . $db->safesql($author) . "'"));
				//delcache('userInfo_'.$access['id']);
			}		
			$update = $db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `author` = '" . $author . "', `date` = '" . $date . "', `tags` = '" . $tags . "', `cat` = '" . $cats . "', `altname` = '" . $translit . "', `keywords` = '" . $keywords . "', `description` = '" . $description . "', `allow_comments` = '" . $comment . "', `allow_rating` = '" . $rating . "', `allow_index` = '" . $index . "', `fields` = '" . $fieldsSer . "', `groups` = '" . $groupss . "', `fixed` = '" . $fix . "', `active` = '" . $status . "', `price` = '" . $price . "', `sync_title` = '" . $sync_title . "',  `sync_price` = '" . $sync_price . "',`section` = '" . $sections . "' WHERE `id` = '" . $edit_id . "' LIMIT 1 ;");
			$nnid = $edit_id;
			if($update)
			{
				$adminTpl->info($lang['catalog_edit_ok'], 'info', null, $lang['info'], $lang['catalog_list'], ADMIN.'/module/catalog/');
			}
		} 
		else 
		{
			$insert = $db->query("INSERT INTO `" . DB_PREFIX . "_catalog` ( `id` , `author` , `date` , `tags` , `cat` , `altname` ,`keywords`,`description`, `allow_comments` , `allow_rating` , `allow_index` , `score` , `votes` , `views` , `comments` , `fields` , `groups` , `fixed`, `sync_title`, `sync_price` , `active` ) VALUES (NULL, '" . $author . "', " . $date . ", '" . $tags . "', '" . $cats . "', '" . $db->safesql($translit) . "', '" . $keywords . "', '" . $description . "', '" . $comment . "', '" . $rating . "', '" . $index . "', '0', '0', '0', '0', '" . $fieldsSer . "', '" . $groupss . "', '" . $fix . "', '" . $sync_title. "', '" . $sync_price . "', '" . $status . "');");
			if($insert) 
			{
				$adminTpl->info($lang['catalog_add_ok'], 'info', null, $lang['info'], $lang['catalog_list'], ADMIN.'/module/catalog/');				
				if($status == 1)
				{
					//$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_catalog=user_catalog+1 WHERE `nick` ='" . $db->safesql($author) . "' LIMIT 1", true);
					//user_points($author, 'add_catalog');
					//$access = $db->getRow($db->query("SELECT id FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE nick = '" . $db->safesql($author) . "'"));
					//delcache('userInfo_'.$access['id']);
				}				
				$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE altname = '" . $db->safesql($translit) . "'");
				$catalog = $db->getRow($query);
				foreach($langTitle as $k => $v)
				{
					if(trim($v) != '' && trim($short[$k]) != '')
					{
						$ntitle = filter(trim(htmlspecialchars_decode($v, ENT_QUOTES)), 'title');
						$ftitle = filter(trim(htmlspecialchars_decode($fulltitle[$k], ENT_QUOTES)), 'title');
						$nshort = fileInit('catalog', $catalog['id'], 'content', $bb->parse(processText(filter($short[$k], 'html')), $catalog['id'], true));
						$nfull = fileInit('catalog', $catalog['id'], 'content', $bb->parse(processText(filter($full[$k], 'html')), $catalog['id'], true));	
						$db->query("INSERT INTO `" . DB_PREFIX . "_langs` ( `postId` , `module` , `title`, `fulltitle` , `short` , `full` , `lang` ) 	VALUES ('" . $catalog['id'] . "', 'catalog', '" . $db->safesql(processText($ntitle)) . "', '" . $db->safesql(processText($ftitle)) . "', '" . $db->safesql($nshort) . "', '" . $db->safesql($nfull) . "' , '" . $k . "');");
					}
				}			
				fileInit('catalog', $catalog['id']);
				$nnid = $catalog['id'];
				workTags($catalog['id'], $tags, 'add');

			}
		}
		if (isset($_POST['preview_del']))
		{
			if(isset($_POST['edit'])) 
			{
				$queryPR = $db->query("SELECT preview FROM ".DB_PREFIX."_catalog WHERE id = '" . $nnid . "' LIMIT 1");
				$catalogPR = $db->getRow($queryPR);
				if (file_exists(ROOT.$catalogPR['preview'])) 
				{
					unlink(ROOT.$catalogPR['preview']);
				}
				$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `preview` = '' WHERE `id` = '" . $nnid . "' LIMIT 1 ;");
			}
		}
		else
		{
			if($_FILES['preview']['size'] > 0) 
				{	
					$purl = 'files/catalog/'.$nnid.'/';
					$path_info = pathinfo($_FILES['preview']['name']);
					$ext = $path_info['extension'];		
					$file_full = $purl.'preview_' .$nnid.'.'.$ext;
					if (file_exists(ROOT.$file_full)) 
					{
						@unlink(ROOT.$file_full);
					}
					if($foo = new Upload($_FILES['preview']))
					{
						$foo->file_new_name_body = 'preview_' .$nnid;
						$foo->image_resize = false;
						$foo->image_x = $catalog_conf['thumb_width'];
						$foo->image_ratio_y = true;
						$foo->file_overwrite = true;
						$foo->file_auto_rename = false;
						$foo->Process(ROOT.$purl);
						$foo->allowed = array("image/*");							
						if ($foo->processed) 
						{
							$foo->Clean();
						}
					}
					$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `preview` = '/" . $file_full . "' WHERE `id` = '" . $nnid . "' LIMIT 1 ;");
				}
		}
	}
	else 
	{
		$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
	}
	echo '</div>';
	$adminTpl->admin_foot();
}

function delete($id) {
global $db;
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '" . $id . "'");
	$catalog = $db->getRow($query);
	$db->query("DELETE FROM `" . DB_PREFIX . "_langs` WHERE `postId` = '" . $catalog['id'] . "' AND `module` = 'catalog'");
	full_rmdir(ROOT . initDC('catalog', $catalog['id']));
	$db->query("DELETE FROM `" . DB_PREFIX . "_catalog` WHERE `id` = " . $id . " LIMIT 1");
	$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_catalog=user_catalog-1 WHERE `nick` ='" . $catalog['author'] . "' LIMIT 1", true);
	workTags($id, $catalog['tags'], 'delete');
	deleteComments('catalog', $id);
}

function workTags($id, $tags, $do = 'add')
{
global $db;
	if(!empty($tags))
	{
		$tag = array_map('trim', explode(',', $tags));
		foreach($tag as $t)
		{
			if($do == 'add')
			{
				$db->query("INSERT INTO `" . DB_PREFIX . "_tags` ( `id` , `tag` , `module` ) VALUES (NULL, '" . $db->safesql($t) . "', 'catalog');");
			}
			elseif($do == 'delete')
			{
				$db->query("DELETE FROM `" . DB_PREFIX . "_tags` WHERE `tag` = '" . $t . "' and module='catalog' LIMIT 1");			
			}
		}
	}
}

function changeuGroup($var)
{
global $adminTpl, $db, $catalog_conf;
    $content = '<select class="form-control" name="{varName}">';
	$query2 = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups`");
	while($rows2 = $db->getRow($query2)) 
	{
		$sel = ($catalog_conf[$var] == $rows2['id']) ? 'selected' : '';
		$content .= '<option value="' . $rows2['id'] . '" ' . $sel . '>' . $rows2['name'] . '</option>';
	}
	$content .= '</select>';
	return $content;
}

function add_spec(){
	global  $db;
    //echo "!";
	$query2 = $db->query("SELECT id, fields FROM ".DB_PREFIX."_catalog WHERE spec IS NULL");
    $name = '48ebbf1d-3bbd-11e8-a468-704d7b330583';
    $mark = '52e71120-3bbd-11e8-a468-704d7b330583';
    $dopPro = '5d85a200-3bbd-11e8-a468-704d7b330583';
    while($rows2 = $db->getRow($query2)) 
	{
        //echo "<br/>!!";
		$obj = json_decode($rows2['fields'], true);
        if(!empty($obj[$name])){
            $nameRes = $obj[$name];
        } else $nameRes = null;
        if(!empty($obj[$mark])){
            $markRes = $obj[$mark];
        } else $markRes = null;
        if(!empty($obj[$dopPro])){
            $dopProRes = $obj[$dopPro];
        } else $dopProRes = null;
        
        $json = [];
        $json[1] = $nameRes;
        $json[2] = $markRes;
        $json[3] = $dopProRes;
        $jsonRes = json_encode($json, JSON_UNESCAPED_UNICODE);
        
        $db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `spec` = '".$jsonRes."' WHERE `id` = '" . $rows2['id'] . "' LIMIT 1 ;");
	}
}

global $lang;

switch(isset($url[3]) ? $url[3] : null) {
	default:
		catalog_main();
	break;
    
    case 'add_spec':
        add_spec();
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
		catalog_add();
	break;
	
	case "group":
		group_view();
	break;
	
	case "group_add":
		group_add();
	break;
	
	case "ajax_catalog_search":
		ajax_catalog_search();
	break;
	
	case "sync":
	//	sync();
	break;
	
	case "save":
		catalog_save();
	break;
	
	case "edit":
		$id = intval($url[4]);
		catalog_add($id);
	break;
	
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
