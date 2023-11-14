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

function partner_main() 
{
	global $adminTpl, $core, $db, $admin_conf, $url, $lang;
	$adminTpl->admin_head($lang['partner'] . ' | ' . $lang['partner_list']);
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
	$cats_arr = $core->aCatList('partner');	
	$all = $db->numRows($db->query("SELECT * FROM " . DB_PREFIX . "_partner $whereC"));
	$adminTpl->a_pages($page, $admin_conf['num'], $all, ADMIN.'/module/partner/{page}');
	$query = $db->query("SELECT DISTINCT  JMY_select.*,  JMY_users.name as uname,  JMY_users.surname,  JMY_users.ochestvo,  JMY_organizations.name as orgname,  JMY_organizations.inn as orginn, JMY_object.name as objname,  JMY_object.address as objaddress FROM JMY_select
  INNER JOIN JMY_users ON JMY_select.uid = JMY_users.id INNER JOIN JMY_object  ON JMY_object.id = JMY_select.object INNER JOIN JMY_organizations ON JMY_organizations.id = JMY_select.oid ORDER BY JMY_select.id DESC LIMIT " . $cut . ", " . $admin_conf['num'] . "");
	$count = $db->numRows($db->query('SELECT id FROM '.DB_PREFIX.'_partner WHERE active=2'));
		if($count > 0)
		{
			$adminTpl->alert('warning', $lang['info'], $lang['partner_moder']);
		}
	if($db->numRows($query) > 0) 
	{		
		echo '<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">Список расчетов:</span>    
						<div class="widget-menu pull-right mr5" >						
							<form style=" display: inline-block;" method="POST" action="administration/module/partner">
								<input name="query" value="" placeholder="'.$lang['search'].'" class="form-control mr10">
							</form>
							<select style="width: 150px; display: inline-block;" class="form-control" onchange="top.location=this.value">
								<option value="">'.$lang['choose_cat'].'</option>';
								foreach ($cats_arr as $cid => $name) 
									{
										echo '  <option value="' . ADMIN . '/module/partner/cat/' . $cid . '" ' . (($cid == $cat) ? 'selected' : '') . '">'.$name.'</option>';	
									}
						echo '</select>
					</div>
              </div>
              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" method="POST" action="{ADMIN}/module/partner/action">
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th><span class="pd-l-sm"></span>#</th>
							<th class="w450">Объект</th>
							<th>Организация</th>
							<th class="w250">Контактное лицо</th>
							<th class="w150">Дата</th>							
							<th class="text-center">' . $lang['status'] .'</th>
							<th>Коды</th>					
							<th class="w200">' . $lang['actions'] . '</th>
							<th>
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
									<label for="all"></label>
								</div>	
							</th>
						</tr>
                    </thead>
                    <tbody>';
		while($select = $db->getRow($query)) 
		{
			if ($select['status'] == 2)
			{
				$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
			}
			elseif ($select['status'] == 1)
			{
				$status_icon = '<span class="fa fa-clock-o text-warning fa-md"></span>';
			}
			else
			{
				$status_icon = '<span class="fa fa-circle text-danger fa-md"></span>';
			}	
			if ($select['oid'] != '0')
			{
				$org = $select['orgname'].'<br><strong>ИНН:</strong> '.$select['orginn'];
			}
			else
			{
				$org = 'К сожалению организация не указана!';
			}
			if ($select['object'] != '0')
			{				
				$object = '<strong>Имя объекта:</strong> '.$select['objname'].'<br><strong>Адрес:</strong> '.$select['objaddress'];
			}
			else
			{
				$object = 'К сожалению информация об объекте не указана!';
			}
					
			echo '
			<tr>
				<td><span class="pd-l-sm"></span>#БС-'.str_pad($select['id'], 5, "0", STR_PAD_LEFT) .'</td>
				<td><a href="{ADMIN}/object/view/'.$select['object'].'" target="_blank">' . $object . '</a></td>
				<td><a href="{ADMIN}/module/organizations/edit/'.$select['oid'].'" target="_blank">' . $org . '</a></td>
				<td><a href="{ADMIN}/user/edit/'.$select['uid'].'" target="_blank">' . shortUserName(false, $select['surname'], $select['uname'], $select['ochestvo']) . '</a></td>
				<td>' . formatDate($select['date'], true) . '</td>					
				<td class="text-center">' . $status_icon . '</td>
				<td><a href="/partner/'.$partner['altname'].'.html" target="_blank">' . $org . '</a></td>
					<td>				
					<div class="btn-group">
						<button type="button" onclick="location.href = \'{ADMIN}/module/select/view/'.$select['id'].'\'" class="btn btn-xs btn-primary">'.$lang['view'].'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
						<ul role="menu" class="dropdown-menu">							
							<li><a href="{ADMIN}/module/partner/retivate/'.$partner['id'].'">'. (($partner['active'] != 1) ? $lang['do_activation'] : $lang['do_deactivation']).'</a></li>  
							<li><a href="{ADMIN}/module/partner/index/'.$partner['id'].'">'. (($partner['allow_index'] == 0) ? $lang['partner_action_index'] : $lang['partner_action_noindex']).'</a></li> 	
							<li><a href="{ADMIN}/module/partner/fix/'.$partner['id'].'">'. (($partner['fixed'] == 0) ? $lang['partner_action_fix'] : $lang['partner_action_nofix']).'</a></li> 								
							<li class="divider"></li>
							<li><a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-form-'.$partner['id'].'\')">' . $lang['delete'] .'</a></li>
						</ul>
					</div>
					<div id="modal-form-'.$partner['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
						  <div class="panel-body">
							<h3 class="mt5">' . str_replace('[partner]', $partner['title'], $lang['partner_delete_title']) .'</h3>					
							<hr class="short alt">
							<p>' . str_replace('[partner]', $partner['title'], $lang['partner_delete_text']) .  '</p>
						  </div>
						  <div class="panel-footer text-right">
							<button type="button" onclick="location.href = \'{ADMIN}/module/partner/delete/'.$partner['id'].'\'" class="btn btn-danger">' . $lang['delete'] .'</button>
						  </div>
						</div>
					  </div>
				</td>
				<td>
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $partner['id'] . '" type="checkbox" name="checks[]" value="' . $partner['id'] . '">
						<label for="checkbox' . $partner['id'] . '"></label>
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
								<option value="blockIndex">' . $lang['partner_remove_main'] .'</option>
								<option value="setIndex">' . $lang['partner_set_main'] .'</option>
								<option value="nowDate">' . $lang['partner_set_nowdate'] . '</option>
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
		$all = $db->numRows($db->query("SELECT * FROM " . DB_PREFIX . "_partner $whereC"));
		$adminTpl->pages($page, $admin_conf['num'], $all, ADMIN.'/module/partner/{page}');
	} 
	else
	{
		$adminTpl->info($lang['partner_empty'], 'empty', null, $lang['partner_list'], $lang['partner_add'], ADMIN.'/module/partner/add');	
	}		
	echo'</div>';	
	$adminTpl->admin_foot();
} 

function selectConstract($array, $value_is = false, $empty = false)
{
	$return = '';
	if ($empty)
	{
		 $return .= '<option selected="selected"></option>';
	}
	foreach ($array as $key => $value) {
		$return .= '<option value="'.$key.'" '.($key == $value_is ? 'selected="selected"' : '').'>'.$value.'</option>';
	}
	return($return);
}

function view($id = null) 
{
	global $core, $db;

	$query = $db->query("SELECT * FROM ".DB_PREFIX."_select WHERE id = '".intval($id)."' LIMIT 1");
	$result = $db->getRow($query);
	
	$json = json_decode($result['detail']);
	$long = $json->long;
	$width = $json->width;
	$hieght = '';
	$vistup = '';
	$house_height = $json->house_height;
	$n_1 = $json->n_1;
	$n_2 = $json->n_2;
	$n_3 = $json->n_3;
	$n_gvs = $json->n_gvs;	
	$t_11 =  $json->t_11;
	$t_12 = $json->t_12;
	$t_13 = $json->t_13;
	$t_2 = $json->t_2;
	$t_21 = $json->t_21;
	$t_22 = $json->t_22;
	$t_23 = $json->t_23;
	$t_3 = $json->t_3;
	$b_1 = $json->b_1;
	$p_1 = $json->p_1;
	$p_2 = $json->p_2;
	
	$array_block = array("Отопление"=>"Отопление", "Вентиляция"=>"Вентиляция", "Технология"=>"Технология");	
	$array_yes = array("Есть"=>"Есть", "Нет"=>"Нет");
	$array_rel = array("Зависимая"=>"Зависимая", "Независимая"=>"Независимая");
	$array_pump = array("TOP-SD"=>"TOP-SD", "Yonos MAXO-D"=>"Yonos MAXO-D", "Stratos-D"=>"Stratos-D");
	$array_klap = array("IMI Hydronic"=>"IMI Hydronic", "Danfoss"=>"Danfoss", "Siemens"=>"Siemens");
	$array_ctrl = array("Segnetics"=>"Segnetics", "Danfoss"=>"Danfoss", "Siemens"=>"Siemens");
	$array_auto = array("Отдельный"=>"Отдельный", "Учтен в БОВ-1"=>"Учтен в БОВ-1");
	
	$t_1 = selectConstract(array("150"=>"150", "130"=>"130", "115"=>"115", "105"=>"105", "95"=>"95"), $json->t_1, true);
	$block_1 = selectConstract($array_block, $json->block_1, true);
	$block_2 = selectConstract($array_block, $json->block_2, true);
	$block_3 = selectConstract($array_block, $json->block_3, true);
	$block_bv_uzel = selectConstract($array_yes, '', true);
	$block_bv_podpit = selectConstract(array("Есть"=>"Есть", "Нет"=>"Нет", "Автоматическая"=>"Автоматическая"), '', true);
	$block_bv_reg = selectConstract($array_yes, '', true);
	$block_bv_ogr = selectConstract($array_yes, '', true);
	$block_bv_reg_d = selectConstract(array("Danfoss"=>"Danfoss", "Вогез"=>"Вогез"), '', true);
	$block_bv_ogr_d = selectConstract(array("IMI Hydronic"=>"IMI Hydronic", "Danfoss"=>"Danfoss", "ADL"=>"ADL"), '', true);
	
	$bread = '<li><a href="/select">Расчет блочного теплового пункта</a></li>';
	$page_title = 'Расчет блочного теплового пункта';

	$core->tpl->loadFile('select/select.view');	
		$core->tpl->setVar('ID', $id);
	$core->tpl->setVar('LONG', $long);
	$core->tpl->setVar('WIDTH', $width);
	$core->tpl->setVar('HEIGHT', $hieght);
	$core->tpl->setVar('VISTUP', $vistup);
	$core->tpl->setVar('HOUSE_HEIGHT', $house_height);
	$core->tpl->setVar('N_1', $n_1);
	$core->tpl->setVar('N_2', $n_2);
	$core->tpl->setVar('N_3', $n_3);
	$core->tpl->setVar('T_1', $t_1);
	
	$core->tpl->setVar('T_11', $t_11);
	$core->tpl->setVar('T_12', $t_12);
	$core->tpl->setVar('T_13', $t_13);
	$core->tpl->setVar('T_2', $t_2);
	$core->tpl->setVar('T_21', $t_21);
	$core->tpl->setVar('T_22', $t_22);
	$core->tpl->setVar('T_23', $t_23);
	$core->tpl->setVar('T_3', $t_3);
	$core->tpl->setVar('B_1', $b_1);
	$core->tpl->setVar('P_1', $p_1);
	$core->tpl->setVar('P_2', $p_2);
	$core->tpl->setVar('N_GVS', $n_gvs);
	$core->tpl->setVar('BLOCK_1', $block_1);
	$core->tpl->setVar('BLOCK_2', $block_2);
	$core->tpl->setVar('BLOCK_3', $block_3);
	$core->tpl->setVar('BLOCK_BV_UZEL', $block_bv_uzel);
	$core->tpl->setVar('BLOCK_BV_PODPIT', $block_bv_podpit);
	$core->tpl->setVar('BLOCK_BV_REG', $block_bv_reg);
	$core->tpl->setVar('BLOCK_BV_OGR', $block_bv_ogr);
	$core->tpl->setVar('BLOCK_BV_REG_D', $block_bv_reg_d);
	$core->tpl->setVar('BLOCK_BV_OGR_D', $block_bv_ogr_d);
	
	$core->tpl->setVar('BLOCK_BOV_1_SCHEMA', selectConstract($array_rel, $json->block_bov_1_schema, true));
	$core->tpl->setVar('BLOCK_BOV_1_PUMP', selectConstract($array_pump, $json->block_bov_1_pump, true));
	$core->tpl->setVar('BLOCK_BOV_1_KLAPAN', selectConstract($array_klap, $json->block_bov_1_klapan, true));
	$core->tpl->setVar('BLOCK_BOV_1_CONTROL', selectConstract($array_ctrl, $json->block_bov_1_control, true));
	$core->tpl->setVar('BLOCK_BOV_1_AUTO', selectConstract(array("Отдельный"=>"Отдельный"), $json->block_bov_1_auto, true));
	
	$core->tpl->setVar('BLOCK_BOV_2_SCHEMA', selectConstract($array_rel, '', true));
	$core->tpl->setVar('BLOCK_BOV_2_PUMP', selectConstract($array_pump, '', true));
	$core->tpl->setVar('BLOCK_BOV_2_KLAPAN', selectConstract($array_klap, '', true));
	$core->tpl->setVar('BLOCK_BOV_2_CONTROL', selectConstract($array_ctrl, '', true));
	$core->tpl->setVar('BLOCK_BOV_2_AUTO', selectConstract($array_auto, '', true));
	
	$core->tpl->setVar('BLOCK_BOV_3_SCHEMA', selectConstract($array_rel, '', true));
	$core->tpl->setVar('BLOCK_BOV_3_PUMP', selectConstract($array_pump, '', true));
	$core->tpl->setVar('BLOCK_BOV_3_KLAPAN', selectConstract($array_klap, '', true));
	$core->tpl->setVar('BLOCK_BOV_3_CONTROL', selectConstract($array_ctrl, '', true));
	$core->tpl->setVar('BLOCK_BOV_3_AUTO', selectConstract($array_auto, '', true));
	
	$core->tpl->setVar('BLOCK_BGV_SCHEMA', selectConstract(array("Автоматически"=>"Автоматически", "Принудит. Смеш"=>"Принудит. Смеш", "Принуд. Паралл."=>"Принуд. Паралл."), '', true));
	$core->tpl->setVar('BLOCK_BGV_PUMP', selectConstract(array("Yonos MAXO-Z"=>"Yonos MAXO-Z", "Stratos-Z"=>"Stratos-Z"), '', true));
	$core->tpl->setVar('BLOCK_BGV_KLAPAN', selectConstract(array("IMI Hydronic"=>"IMI Hydronic", "Danfoss"=>"Danfoss", "Siemens"=>"Siemens", "Прямого действия"=>"Прямого действия"), '', true));
	$core->tpl->setVar('BLOCK_BGV_CONTROL', selectConstract(array("Segnetics"=>"Segnetics", "Danfoss"=>"Danfoss", "Siemens"=>"Siemens"), '', true));
	$core->tpl->setVar('BLOCK_BOV_3_AUTO', selectConstract($array_auto, '', true));
	
	if ($result['oid'] != '0')
			{
				$queryO = $db->query("SELECT name, inn FROM ".DB_PREFIX."_organizations WHERE id = '".$result['oid']."' LIMIT 1");
				$resultO = $db->getRow($queryO);
				$org = $resultO['name'].'<br><strong>ИНН:</strong> '.$resultO['inn'];
			}
			else
			{
				$org = 'К сожалению организация не указана!';
			}
			if ($result['object'] != '0')
			{
				$queryOB = $db->query("SELECT name, address FROM ".DB_PREFIX."_object WHERE id = '".$result['object']."' LIMIT 1");
				$resultOB = $db->getRow($queryOB);
				$object = '<strong>Имя объекта:</strong> '.$resultOB['name'].'<br><strong>Адрес:</strong> '.$resultOB['address'];
			}
			else
			{
				$object = 'К сожалению информация об объекте не указана!';
			}
	
$item = $core->tpl->return_end();	
global $adminTpl, $core, $db, $admin_conf, $url, $lang;
	$adminTpl->admin_head($lang['partner'] . ' | ' . $lang['partner_list']);
echo '<section id="content" class="table-layout animated fadeIn">
          <!-- begin: .tray-center-->
          <div class="tray tray-center">
            <!-- Begin: Content Header-->
            <div class="content-header">
              <h2>'.$resultOB['name'].'</h2>
              <p class="lead">'.$resultO['name'].'</p>
            </div>
            <!-- Validation Example-->
            <div style="padding-bottom: 175px;" class="admin-form theme-primary mw1000 center-block">
              <div class="panel heading-border panel-primary">
                <form id="admin-form" method="post" action="/">
                  <div class="panel-body bg-light">
                   
                    <!-- .section-divider-->
                    <div id="spy1" class="section row">';
	echo $item;
	echo'     
                    </div>
                    
                   
                   </div>
                    
                 
                </form>
              </div>
            </div>
          </div>
		   </section>';	
	$adminTpl->admin_foot();
}

function partner_add($nid = null) 
{
global $adminTpl, $core, $db, $core, $config, $lang;
	if(isset($nid)) 
	{	
		$bb = new bb;
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_partner WHERE id = '" . $nid . "'");
		$partner = $db->getRow($query);
		$id = $partner['id']; 
		$author = $partner['author']; 
		$date = gmdate('d.m.Y H:i', $partner['date']); 
		$tags = $partner['tags']; 
		$parhner_page = $partner['page']; 
		$groups = $partner['groups']; 
		$altname = $partner['altname']; 
		$keywords = $partner['keywords']; 
		$description = $partner['description']; 		
		$allow_comments = $partner['allow_comments']; 
		$allow_rating = $partner['allow_rating']; 
		$allow_index = $partner['allow_index']; 
		$score = $partner['score']; 
		$site = $partner['site'];
		$votes = $partner['votes']; 	
		$preview = $partner['preview']; 
		$views = $partner['views']; 
		$comments = $partner['comments']; 
		$fields = unserialize($partner['fields']); 
		$fix = $partner['fixed']; 
		$active = ($partner['active'] == 2 ? 0 : $partner['active']);
		$cat = $partner['cat']; 
		$cat_array = explode(',', $cat);
		$catttt = explode(',', $cat);
		$edit = true;
		$grroups = explode(',', $groups);
		$firstCat = $catttt[1];
		$deleteKey = array_search($firstCat, $catttt);
		unset($catttt[$deleteKey]);
		$langMassiv = $core->getLangList(true);
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_langs WHERE postId = '" . $id . "' AND module='partner'");
		while($langs = $db->getRow($query))
		{
			$title[$langs['lang']] = prepareTitle($langs['title']);
			$fulltitle[$langs['lang']] = prepareTitle($langs['fulltitle']);
			$short[$langs['lang']] = $bb->htmltobb($langs['short']);
			$full[$langs['lang']] = $bb->htmltobb($langs['full']);
		}		
		$lln = $lang['partner_edits'];
		$dosave = $lang['update'];
		$remote = ADMIN.'/module/partner/ajax/isurl/update';
	} 
	else 
	{
		$id = false; 
		$fulltitle = false; 
		$title = false; 
		$short = false; 
		$parhner_page = false; 
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
		$score = false; 
		$votes = false; 
		$views = false; 
		$preview = false; 
		$comments = false; 
		$fields = false; 
		$fix = ''; 
		$active = 1;
		$edit = false;
		$catttt = array();
		$grroups = array();
		$firstCat = '';
		$lln = $lang['partner_add'];
		$dosave = $lang['add'];
		$remote = ADMIN.'/module/partner/ajax/isurl';
	}
	$queryXF = $db->query("SELECT * FROM ".DB_PREFIX."_xfields WHERE module='partner'");
	$adminTpl->admin_head($lang['partner'] . ' | ' . $lln);
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
        $(".select2-single").select2();';	
	$validation_array = array(		
		'title' => array(
			'required' =>  array('true', $lang['partner_add_name_err'])			
		),
		'translit' => array(
			'required' =>  array('true', $lang['partner_add_url_err_1']),
			'remote' =>  array($remote,  $lang['partner_add_url_err_3'])
		),
		'date' => array(
			'required' =>  array('true', $lang['partner_add_date_err']),
		),
		'author' => array(
			'required' =>  array('true', $lang['partner_add_author_err']),
		)
	);
	validationInit($validation_array);
	datetimepickerInit('date');
	ajaxInit();
	fancyboxInit();
	$cats_arr = $core->aCatList('partner');
	echo '<section id="content" class="table-layout animated fadeIn">			
				<div class="tray tray-center">
					<form action="{MOD_LINK}/save" enctype="multipart/form-data" role="form" method="POST" name="content" id="admin-form">
					<div class="panel mb25 mt5">
						<div class="panel-heading br-b-ddd"><span class="panel-title hidden-xs">'.$lang['partner_add'].'</span>
							<ul class="nav panel-tabs-border panel-tabs">
								<li class="active"><a href="#tab1_1" data-toggle="tab">'.$lang['partner_add_tab_general'].'</a></li>
								<li><a href="#tab1_2" data-toggle="tab">'.$lang['partner_add_tab_settings'].'</a></li>								
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
														<input id="title" type="text" name="title" placeholder="'.$lang['partner_add_name'].'" class="event-name gui-input br-light light" '.(isset($nid) ? '' : 'onchange="getTranslit(gid(\'title\').value, \'translit\');"').' value="' . (isset($title[$config['lang']]) ? $title[$config['lang']] : '') . '">				
														<label for="title" class="field-icon"><i class="fa fa-pencil"></i></label>
													</label>
												</div>
												<div class="col-xs-6">
													<label for="translit" class="field prepend-icon">
														<input id="translit" type="text" name="translit" placeholder="'.$lang['partner_add_url'].'" class="event-name gui-input br-light light" value="'.$altname.'">
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
														<input id="date" type="text" name="date" placeholder="'.$lang['partner_add_date'].'" class="gui-input" value="'.$date.'">
													</label>
												</div>
											</div>											
											<div class="section row mb15">
												<div class="col-xs-6">
													<label for="site" class="field prepend-icon">
														<input id="site" type="text" name="site" placeholder="'.$lang['partner_add_site'].'" class="event-name gui-input br-light light" value="'.$site.'">
														<label for="site" class="field-icon"><i class="fa fa-external-link"></i></label>
													</label>	
												</div>
												<div class="col-xs-6">
													<label class="field option">
													 '.checkbox('parhner_page', $parhner_page, $lang['partner_add_desc_checkbox']).'
													</label>	
												</div>
											</div>
											<div class="section row mb15">
												<div class="col-xs-6">
													<label for="category" class="field">
														<select class="form-control" name="category[]" id="maincat" onchange="if(this.value != \'0\') {show(\'catSub\');}" >
															<option value="0">'.$lang['partner_add_nocat'].'</option>';	
															foreach ($cats_arr as $cid => $name) 
															{
																$selected = ($cid == $firstCat) ? "selected" : "";
																echo '<option value="' . $cid . '" ' . $selected . '>' . $name . '</option>';
															}
												  echo '</select>
													</label>
												</div>
												<div class="col-xs-6" id="catSub" style="' . ((isset($nid)&&($firstCat!=0)) ? '' : 'display:none;') . '">
												<label for="category" class="field">
													<select class="form-control" name="category[]" id="category"  multiple >';
														foreach ($cats_arr as $cid => $name) 
														{
															if($catttt) $selected = in_array($cid, $catttt) ? "selected" : "";
															echo '<option value="' . $cid . '" ' . $selected . ' id="cat_' . $cid . '">' . $name . '</option>';
														}
												echo '</select>
												</label>
												</div>
											</div> 
									</div>
									<div class="col-md-3">
										<div data-provides="fileupload" class="fileupload fileupload-'.($preview ? 'exists' : 'new').' admin-form">
											<div class="fileupload-preview thumbnail mb15">
												<img '.($preview ? 'src="'.$config['url'].$preview.'"' : 'data-src="holder.js/100%x147/text:'.$lang['partner_add_mini'].'"').' alt="holder">
											</div>
											<span class="button btn-system btn-file btn-block ph5">
												<span class="fileupload-new">'.$lang['upload'].'</span>
												<span class="fileupload-exists">'.$lang['upload_again'].'</span>
												<input name="preview" type="file">
											</span>
											
										</div>
										<div class="checkbox-custom checkbox-warning mb5 mt15 text-center">
										  <input id="preview_del" name="preview_del" type="checkbox">
										  <label for="preview_del">'.$lang['partner_add_mini_del'].'</label>
										</div>
									</div>
								</div>
							</div>
							<div id="tab1_2" class="tab-pane">
								<div class="section row mbn">
									<div class="col-xs-6 pr15">
										<div class="section mb10">
											<label class="field mb5">'.$lang['partner_add_fulltitle'].':</label>
											<label for="fulltitle" class="field prepend-icon">												
												<input id="fulltitle" type="text" name="fulltitle" placeholder="'.$lang['partner_add_fulltitle_pre'].'" class="event-name gui-input bg-light br-light" value="' .  (isset($fulltitle[$config['lang']]) ? $fulltitle[$config['lang']] : '') . '">
												<label for="fulltitle" class="field-icon"><i class="fa fa-code"></i></label>
											</label>
										</div>
										<div class="section mb10">
											<label class="field mb5">'.$lang['partner_add_keywords'].':</label>
											<label for="keywords" class="field prepend-icon">												
												<input id="keywords" type="text" name="keywords" placeholder="'.$lang['partner_add_keywords_pre'].'" class="event-name gui-input bg-light br-light" value="' . $keywords . '">
												<label for="keywords" class="field-icon"><i class="fa fa-edit"></i></label>
											</label>
										</div>
										<div class="section mb10">
											<label class="field mb5">'.$lang['partner_add_description'].':</label>
											<label for="description" class="field prepend-icon">
												<input id="description" type="text" name="description" placeholder="'.$lang['partner_add_description_pre'].'" class="event-name gui-input bg-light br-light" value="' . $description . '">
												<label for="description" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
											</label>
										</div>
										<hr class="alt short mv15">
										<p class="text-muted"><span class="fa fa-exclamation-circle text-warning fs15 pr5"></span> '.$lang['partner_add_seo'].'</p>
									</div>
									<div class="col-xs-6">
										<label class="field option">
										 '.checkbox('status', $active, $lang['partner_add_activate']).'
										</label><br>									
										<label class="field option mt15">
										 '.checkbox('fix', $fix, $lang['partner_add_fix']).'
										</label><br>
										  <label class="field option mt15">
										 '.checkbox('index', $allow_index, $lang['partner_add_index']).'
										</label>
									</div>
								</div>
								<br>
							</div>
						</div>
					</div>
				</div>';		
				if ($id == false)
				{
					$dir = ROOT.'files/partner/temp';
					if (!file_exists($dir))
					{
						mkdir($dir, 0777);
						
					}
					$_SESSION["RF"]["fff"] ="files/partner/temp/";		
				}
				else
				{
				
					$_SESSION["RF"]["fff"] ="files/partner/".$id."/";
				}
				echo'
				<div class="tab-block mb25">
					<ul class="nav nav-tabs_editor nav-tabs-right">
						<li class="active">
							<a href="#tab1" data-toggle="tab" aria-expanded="true">'.$lang['partner_add_short'].'</a>
						</li>
						<li class="">
							<a href="#tab2" data-toggle="tab" aria-expanded="false">'.$lang['partner_add_full'].'</a>
						</li>
						<li class="">
							<a id="fbox" data-fancybox-type="iframe" href="usr/plugins/filemanager/dialog.php?type=2"><i class="fa fa-folder-open-o text-purple"></i> '.$lang['partner_add_upload'].'</a>
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
					if($partner['active'] == 2)
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
 
function partner_save() 
{
global $adminTpl, $core, $db, $cats, $groupss, $config, $partner_conf, $lang;
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
	$translit = ($_POST['translit'] !== '') ? mb_strtolower(str_replace(array('-', ' '), array('_', '_'), $_POST['translit']), 'UTF-8') : translit($title);	
	$full= isset($_POST['full']) ? $_POST['full'] : '';
	$short= isset($_POST['short']) ? $_POST['short'] : '';			
	$site = isset($_POST['site']) ? $_POST['site'] : '';
	$page = isset($_POST['parhner_page']) ? 1 : 0;
	$index = isset($_POST['index']) ? 1 : 0;
	$category = isset($_POST['category']) ? array_unique($_POST['category']) : '0';
	$status = isset($_POST['status']) ? 1 : 0;
	$fix = isset($_POST['fix']) ? 1 : 0;	
	$edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : '';
	$cnt = (($full['ru'] != '') ? $full['ru'] : $short['ru']);	
	$keywords = !empty($_POST['keywords']) ? $_POST['keywords'] : $word_counter->get_keywords(substr($cnt, 0, 500)); 	
	$newcnt = $bb->parse(processText(filter(fileInit('partner', $edit_id, 'content', $cnt), 'html')), $edit_id, true);		
	$description = !empty($_POST['description']) ? $_POST['description'] : substr(strip_tags($newcnt), 0, 150); 	
	if($edit_id > 0)
	{			
		$old_dataQ = $db->query("SELECT * FROM ".DB_PREFIX."_partner WHERE id = '" . $edit_id . "'");
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
	$cats = ',' . $cats;	
	$lln = $lang['partner_adds'];
	if(isset($_POST['edit'])) 
	{
		$lln = $lang['partner_edits'];
	}
	$adminTpl->admin_head($lang['partner'] . ' | ' . $lln);
	echo '<div id="content" class="animated fadeIn">';	
	if($title && $author && $translit) 
	{		
		if(isset($_POST['edit'])) 
		{
			foreach($langTitle as $k => $v)
			{
				$ntitle = filter(trim(htmlspecialchars_decode($v, ENT_QUOTES)), 'title');
				$ftitle = filter(trim(htmlspecialchars_decode($fulltitle[$k], ENT_QUOTES)), 'title');
				$nshort = $bb->parse(processText(filter(fileInit('partner', $edit_id, 'content', $short[$k]), 'html')), $edit_id, true);
				$nfull = $bb->parse(processText(filter(fileInit('partner', $edit_id, 'content', $full[$k]), 'html')), $edit_id, true);	
				if(isset($_POST['empty'][$k]) && trim($v) != '')
				{
					$db->query("INSERT INTO `" . DB_PREFIX . "_langs` ( `postId` , `module` , `title` , `fulltitle` , `short` , `full` , `lang` ) VALUES ('" . $edit_id . "', 'partner', '" . $db->safesql(processText($ntitle)) . "', '" . $db->safesql(processText($ftitle)) . "', '" . $db->safesql($nshort) . "', '" . $db->safesql($nfull) . "' , '" . $k . "');");
				}
				elseif(!isset($_POST['empty'][$k])  && (trim($v) == ''))
				{
					$db->query("DELETE FROM `" . DB_PREFIX . "_langs` WHERE `postId` ='" . $edit_id . "' AND `module` ='partner' AND `lang`='" . $k . "' LIMIT 1");
				}
				elseif(!isset($_POST['empty'][$k]) && trim($v) != '')
				{
					$db->query("UPDATE `" . DB_PREFIX . "_langs` SET `title` = '" . $db->safesql(processText($ntitle)) . "', `fulltitle` = '" . $db->safesql(processText($ftitle)) . "', `short` = '" . $db->safesql($nshort) . "', `full` = '" . $db->safesql($nfull) . "' WHERE `postId` ='" . $edit_id . "' AND `module` ='partner' AND `lang`='" . $k . "' LIMIT 1 ;");
				}
			}	
			$update = $db->query("UPDATE `" . DB_PREFIX . "_partner` SET `author` = '" . $author . "', `date` = '" . $date . "', `cat` = '" . $cats . "',  `site` = '" . $site . "', `page` = '" . $page . "', `altname` = '" . $translit . "', `keywords` = '" . $keywords . "', `description` = '" . $description . "', `allow_index` = '" . $index . "',  `fixed` = '" . $fix . "', `active` = '" . $status . "' WHERE `id` = '" . $edit_id . "' LIMIT 1 ;");
			$nnid = $edit_id;
			if($update)
			{
				$adminTpl->info($lang['partner_edit_ok'], 'info', null, $lang['info'], $lang['partner_list'], ADMIN.'/module/partner/');
			}
		} 
		else 
		{
			$insert = $db->query("INSERT INTO `" . DB_PREFIX . "_partner` ( `id` , `author` , `date` ,  `cat` , `site` , `page` , `altname` ,`keywords`, `description`,  `allow_index` , `score` , `views`, `fixed` , `active` ) VALUES (NULL, '" . $author . "', '" . $date . "', '" . $cats . "', '" . $site . "', '" . $page . "', '" . $db->safesql($translit) . "', '" . $keywords . "', '" . $description . "', '" . $index . "', '0', '0', '" . $fix . "', '" . $status . "');");
			if($insert) 
			{
				$adminTpl->info($lang['partner_add_ok'], 'info', null, $lang['info'], $lang['partner_list'], ADMIN.'/module/partner/');					
				$query = $db->query("SELECT * FROM ".DB_PREFIX."_partner WHERE altname = '" . $db->safesql($translit) . "'");
				$partner = $db->getRow($query);
				foreach($langTitle as $k => $v)
				{
					if(trim($v) != '')
					{
						$ntitle = filter(trim(htmlspecialchars_decode($v, ENT_QUOTES)), 'title');
						$ftitle = filter(trim(htmlspecialchars_decode($fulltitle[$k], ENT_QUOTES)), 'title');
						$nshort = fileInit('partner', $partner['id'], 'content', $bb->parse(processText(filter($short[$k], 'html')), $partner['id'], true));
						$nfull = fileInit('partner', $partner['id'], 'content', $bb->parse(processText(filter($full[$k], 'html')), $partner['id'], true));	
						$db->query("INSERT INTO `" . DB_PREFIX . "_langs` ( `postId` , `module` , `title`, `fulltitle` , `short` , `full` , `lang` ) 	VALUES ('" . $partner['id'] . "', 'partner', '" . $db->safesql(processText($ntitle)) . "', '" . $db->safesql(processText($ftitle)) . "', '" . $db->safesql($nshort) . "', '" . $db->safesql($nfull) . "' , '" . $k . "');");
					}
				}			
				fileInit('partner', $partner['id']);
				$nnid = $partner['id'];
			}
		}
		if (isset($_POST['preview_del']))
		{
			if(isset($_POST['edit'])) 
			{
				$queryPR = $db->query("SELECT preview FROM ".DB_PREFIX."_partner WHERE id = '" . $nnid . "' LIMIT 1");
				$partnerPR = $db->getRow($queryPR);
				if (file_exists(ROOT.$partnerPR['preview'])) 
				{
					unlink(ROOT.$partnerPR['preview']);
				}
				$db->query("UPDATE `" . DB_PREFIX . "_partner` SET `preview` = '' WHERE `id` = '" . $nnid . "' LIMIT 1 ;");
			}
		}
		else
		{
			if($_FILES['preview']['size'] > 0) 
				{	
					$purl = 'files/partner/'.$nnid.'/';
					$path_info = pathinfo($_FILES['preview']['name']);
					$ext = strtolower($path_info['extension']);		
					$file_full = $purl.'preview_' .$nnid.'.'.$ext;
					if (file_exists(ROOT.$file_full)) 
					{
						@unlink(ROOT.$file_full);
					}
					if($foo = new Upload($_FILES['preview']))
					{
						$foo->file_new_name_body = 'preview_' .$nnid;
						$foo->image_resize = false;
						$foo->image_x = $partner_conf['thumb_width'];
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
					$db->query("UPDATE `" . DB_PREFIX . "_partner` SET `preview` = '/" . $file_full . "' WHERE `id` = '" . $nnid . "' LIMIT 1 ;");
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
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_partner WHERE id = '" . $id . "'");
	$partner = $db->getRow($query);
	$db->query("DELETE FROM `" . DB_PREFIX . "_langs` WHERE `postId` = '" . $partner['id'] . "' AND `module` = 'partner'");
	full_rmdir(ROOT . initDC('partner', $partner['id']));
	$db->query("DELETE FROM `" . DB_PREFIX . "_partner` WHERE `id` = " . $id . " LIMIT 1");
	$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_partner=user_partner-1 WHERE `nick` ='" . $partner['author'] . "' LIMIT 1", true);
	workTags($id, $partner['tags'], 'delete');
	deleteComments('partner', $id);
}


global $lang;

switch(isset($url[3]) ? $url[3] : null) {
	default:
		partner_main();
	break;	
	
	case "add":
		partner_add();
	break;
	
	case "save":
		partner_save();
	break;
	
	case "edit":
		$id = intval($url[4]);
		partner_add($id);
	break;
	
	case "view":
		$id = intval($url[4]);
		view($id);
	break;
	
	case "delete":
		$id = intval($url[4]);
		delete($id);
		if(isset($_GET['moderate']))
		{
			location(ADMIN.'/moderation/mod/partner');
		}
		else
		{
			location(ADMIN.'/module/partner');
		}
	break;
	
	case "activate":
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_partner` SET `active` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_partner WHERE id = '" . $id . "'");
		$partner = $db->getRow($query);
		$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_partner=user_partner+1 WHERE `nick` ='" . $partner['author'] . "' LIMIT 1", true);
		location(ADMIN.'/module/partner');
	break;	
	
	case "moder_moderation":
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_partner` SET `active` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_partner WHERE id = '" . $id . "'");
		$partner = $db->getRow($query);
		$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_partner=user_partner+1 WHERE `nick` ='" . $partner['author'] . "' LIMIT 1", true);
		location(ADMIN.'/moderation/mod/partner');
	break;	
	
	case "deactivate":
	global $adminTpl, $db;
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_partner` SET `active` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_partner WHERE id = '" . $id . "'");
		$partner = $db->getRow($query);
		$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_partner=user_partner-1 WHERE `nick` ='" . $partner['author'] . "' LIMIT 1", true);
		location(ADMIN.'/module/partner');
	break;

	case "action":
	$type = $_POST['act'];
	if(is_array($_POST['checks'])) {
		switch($type) {
			case "activate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_partner` SET `active` = '1' WHERE `id` = " . intval($id) . " LIMIT 1 ;");
				}
				break;			
			
			case "deActivate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_partner` SET `active` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;			
				
			case "reActivate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_partner` SET `active` = NOT `active` WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;
				
			case "nowDate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_partner` SET `date` = '" . time() . "' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;			
				
			case "blockIndex":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_partner` SET `allow_index` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;		

			case "setIndex":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_partner` SET `allow_index` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
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
			location(ADMIN.'/moderation/mod/partner');
		}
		else
		{
			location(ADMIN.'/module/partner');
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
							echo(json_encode($lang['partner_add_url_err_2']));
						}
						else
						{
							$query = $db->query("SELECT * FROM ".DB_PREFIX."_partner WHERE altname = '" . $db->safesql($_POST['translit']) . "'");
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
		require (ROOT.'etc/partner.config.php');		
		$configBox = array(
			'partner' => array(
				'varName' => 'partner_conf',
				'title' => $lang['partner_config'],
				'groups' => array(
					'main' => array(
						'title' => $lang['config_main'],
						'vars' => array(
							'num' => array(
								'title' => $lang['partner_config_main_numt'],
								'description' => $lang['partner_config_main_numd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'comments_num' => array(
								'title' => $lang['partner_config_main_comments_numt'],
								'description' => $lang['partner_config_main_comments_numd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),							
							'fullLink' => array(
								'title' => $lang['partner_config_main_fulllinkt'],
								'description' => $lang['partner_config_main_fulllinkd'],
								'content' => radio("fullLink", $partner_conf['fullLink']),
							),	
							'noModer' => array(
								'title' => $lang['partner_config_main_nomoder'],
								'description' => $lang['partner_config_main_nomoder_desc'],
								'content' => changeuGroup('noModer'),
							),	
							'preModer' => array(
								'title' => $lang['partner_config_main_premodert'],
								'description' => $lang['partner_config_main_premoderd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),									
							'related_partner' => array(
								'title' => $lang['partner_config_main_relatedt'],
								'description' => $lang['partner_config_main_relatedd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),					
							'addpartner' => array(
								'title' => $lang['partner_config_main_addpartnert'],
								'description' => $lang['partner_config_main_addpartnerd'],
								'content' => radio("addpartner", $partner_conf['addpartner']),
							),
						)
					),
					'cats' => array(
						'title' => $lang['partner_config_cats'],
						'vars' => array(
							'showCat' => array(
								'title' => $lang['partner_config_cats_showcatt'],
								'description' => $lang['partner_config_cats_showcatd'],
								'content' => radio("showCat", $partner_conf['showCat']),
							),							
							'subLoad' => array(
								'title' => $lang['partner_config_cats_subloadt'],
								'description' => $lang['partner_config_cats_subloadd'],
								'content' => radio("subLoad", $partner_conf['subLoad']),
							),
							'catCols' => array(
								'title' => $lang['partner_config_cats_catcolst'],
								'description' => $lang['partner_config_cats_catcolsd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'showBreadcumb' => array(
								'title' => $lang['partner_config_cats_showbreadcumbt'],
								'description' => $lang['partner_config_cats_showbreadcumbd'],
								'content' => radio("showBreadcumb", $partner_conf['showBreadcumb']),
							),
						)
					),					
					'tags' => array(
						'title' => $lang['partner_config_tags'],
						'vars' => array(
							'tags' => array(
								'title' => $lang['partner_config_tags_tagst'],
								'description' => $lang['partner_config_tags_tagsd'],
								'content' => radio("tags", $partner_conf['tags']),
							),							
							'tags_num' => array(
								'title' => $lang['partner_config_tags_tags_numt'],
								'description' => $lang['partner_config_tags_tags_numd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'tagIll' => array(
								'title' => $lang['partner_config_tags_tagillt'],
								'description' => $lang['partner_config_tags_tagilld'],
								'content' => radio("tagIll", $partner_conf['tagIll']),
							),
							'illFormat' => array(
								'title' => $lang['partner_config_tags_illformatt'],
								'description' => $lang['partner_config_tags_illformatd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
						)
					),
					'ratings' => array(
						'title' => $lang['partner_config_ratings'],
						'vars' => array(
							'limitStar' => array(
								'title' => $lang['partner_config_ratings_limitstart'],
								'description' => $lang['partner_config_ratings_limitstard'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'starStyle' => array(
								'title' => $lang['partner_config_ratings_starstylet'],
								'description' => $lang['partner_config_ratings_starstyled'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'carma_rate' => array(
								'title' => $lang['partner_config_ratings_carma_ratet'],
								'description' => $lang['partner_config_ratings_carma_rated'],
								'content' => radio("carma_rate", $partner_conf['carma_rate']),
							),
							'carma_summ' => array(
								'title' => $lang['partner_config_ratings_carma_summt'],
								'description' => $lang['partner_config_ratings_carma_summd'],
								'content' => radio("carma_summ", $partner_conf['carma_summ']),
							),
						)
					),
					'files' => array(
						'title' => $lang['partner_config_file'],
						'vars' => array(
							'fileEditor' => array(
								'title' => $lang['partner_config_file_feditort'],
								'description' => $lang['partner_config_file_feditord'],
								'content' => radio("fileEditor", $partner_conf['fileEditor']),
							),
							'imgFormats' => array(
								'title' => $lang['partner_config_file_imgformatst'],
								'description' => $lang['partner_config_file_imgformatsd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'attachFormats' => array(
								'title' => $lang['partner_config_file_attachformatst'],
								'description' => $lang['partner_config_file_attachformatsd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'max_size' => array(
								'title' => $lang['partner_config_file_max_sizet'],
								'description' => $lang['partner_config_file_max_sized'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),			
							'thumb_width' => array(
								'title' => $lang['partner_config_file_thumb_widtht'],
								'description' => $lang['partner_config_file_thumb_widthd'],
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
		generateConfig($configBox, 'partner', '{MOD_LINK}/config', $ok);
		break;		
}
