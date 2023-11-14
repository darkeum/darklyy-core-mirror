<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ADMIN_SWITCH')) {
    location(' /');
    exit;
}

function group_view() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Организации';
	$adminTpl->admin_head('Менеджер организации | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'organizations';
	$atributs['module_open'] = 'edit';
	$atributs['button'] = 'Добавить группу';
	$atributs['button_url'] = '/administration/module/catalog/group_add';	
	load_grid('jqCGroup', $lnn, $atributs);	
	$adminTpl->admin_foot();
}


function group_add($nid = null) 
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
		$lln =  'Добавить группу';
		$dosave = 'Добавить группу';
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
	//$section_arr  = aCaSectionList();
//	$cats_arr = aCaCatList('all');
	echo '<section id="content" class="table-layout animated fadeIn">			
				<div class="tray tray-center">
					<form action="{MOD_LINK}/save" enctype="multipart/form-data" role="form" method="POST" name="content" id="admin-form">
					<div class="panel mb25 mt5">
						<div class="panel-heading br-b-ddd"><span class="panel-title hidden-xs">'.$lln.'</span>
							<ul class="nav panel-tabs-border panel-tabs">
								<li class="active"><a href="#tab1_1" data-toggle="tab">'.$lang['catalog_add_tab_general'].'</a></li>
								<li><a href="#tab1_4" data-toggle="tab">Список товаров</a></li>
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
									<div class="col-xs-12">												
										<select id="ee" style="width:500px"></select>';
										
										
										$adminTpl->js_code[] = '$("#ee").select2({
  multiple: true,
  ajax: {
    url: "https://atri-energo.ru/administration/module/catalog_v2/ajax_catalog_search",
    dataType: \'json\',
    delay: 250,
		type: "POST", 
    data: function (params) {
      return {
        id: params.term, // search term
        page: params.page
      };
    },
    processResults: function (data, page) {
      // parse the results into the format expected by Select2.
      // since we are using custom formatting functions we do not need to
      // alter the remote JSON data
      return {
        results: data.items
      };
    },
    cache: true
  },
 escapeMarkup: function (markup) { return markup; },
    minimumInputLength: 1,
    templateResult: formatRepo,
    templateSelection: formatRepoSelection

});

function formatRepo (repo) {
    if (repo.loading) return repo.text;

    var markup = 
    \'<div class="avatar">\' +
    \'<img src="\' + repo.owner.avatar_url + \'" style="max-width: 100%" />\' +
    \'</div>\' +
    \'<div class="info">\' +
   
    \'<div class="col-sm-6">\' + repo.full_name + \'</div>\' +
    \'<div class="col-sm-3"><i class="fa fa-code-fork"></i> \' + repo.forks_count + \'</div>\' +
    \'<div class="col-sm-2"><i class="fa fa-star"></i> \' + repo.stargazers_count 
    ;

    if (repo.description) {
      markup += \'<div>\' + repo.description + \'</div>\';
    }

    markup += \'</div></div>\';

    return markup;
  }

  function formatRepoSelection (repo) {
    return repo.full_name || repo.text;
  }
';
										
										
								echo'	</div>
								</div>
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

function ajax_catalog_search() 
{	
	global $core, $db;
	$row = array();
	$return_arr = array();
	$row_array = array();
	$return_arr['items'] = array();
	if((isset($_POST['term']) && strlen($_POST['term']) > 0) || (isset($_POST['id']) && is_numeric($_POST['id'])))
	{
		if(isset($_POST['term']))
		{
			$whereClause =  " title LIKE '%" . $_POST['term'] ."%' ";
		}
		elseif(isset($_POST['id']))
		{
			$whereClause =  " id = ".$_POST['id']." ";
		}

		$limit = 100;
		$query = $db->query("SELECT id, title FROM ".DB_PREFIX."_catalog WHERE $whereClause ORDER BY title LIMIT $limit");

		if($db->numRows($query) > 0)
		{
			while($row = $db->getRow($query)) 
			{
				$row_array['id'] = $row['id'];
				$row_array['title'] = utf8_encode($row['title']);
				array_push($return_arr['items'],$row_array);
			}
		}
	}
	else
	{
		
		$row_array['id'] = '0';
		$row_array['title'] = utf8_encode('Start Typing....');
		array_push($return_arr['items'],$row_array);

	}
	$ret = array();
	/* this is the return for a single result needed by select2 for initSelection */
	if(isset($_GET['id']))
	{
		$ret = $row_array;
	}
	/* this is the return for a multiple results needed by select2
	* Your results in select2 options needs to be data.result
	*/
	else
	{
		$ret['results'] = $return_arr['items'];
	}
	echo json_encode($ret);	
}