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


function catalog_add($nid = null)
{
global $adminTpl, $core, $db, $core, $config, $lang;
	if(isset($nid))
	{
		$bb = new bb;
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '" . $nid . "'");
		$catalog = $db->getRow($query);
		$id = $catalog['id'];
		//$author = $catalog['author'];
		$uid = empty($catalog['uid']) ? $core->auth->user_info['id'] : $catalog['uid'];
		$date = date('d.m.Y H:i', strtotime($catalog['created_at']));
		//$price = get_price($catalog['price']);
		$types = $catalog["types"];
		// $groups = $catalog['groups'];
		$altname = $catalog['altname'];
		$keywords = $catalog['keywords'];
		$description = $catalog['description'];
		// $allow_comments = $catalog['allow_comments'];
		$allow_rating = $catalog['allow_rating'];
		$allow_index = $catalog['allow_index'];
		$manufacture = $catalog['man'];
		$score = $catalog['score'];
		$votes = $catalog['votes'];
		// $barcode = $catalog['barcode'];
		$preview = $catalog['preview'];
		$unit = $catalog['unit'];
		$views = $catalog['views'];
		// $comments = $catalog['comments'];
		$fix = $catalog['fixed'];
		$active = ($catalog['active'] == 2 ? 0 : $catalog['active']);
		$active_guest = $catalog['active_guest'];
		$active_user = $catalog['active_user'];
		$use_integration = $catalog['use_integration'];
		$title = htmlspecialchars($catalog['title'], ENT_QUOTES);
		$fulltitle = htmlspecialchars_decode($catalog['fulltitle'], ENT_QUOTES);
		$short = $bb->htmltobb($catalog['short']);
		$cat = $catalog['category'];
		$cat_array = explode(',', $cat);
		$catttt = explode(',', $cat);
		$edit = true;
		// $grroups = explode(',', $groups);
		$firstCat = isset($catttt[1]) ? $catttt[1] : false;
		$deleteKey = array_search($firstCat, $catttt);
		unset($catttt[$deleteKey]);
		$section = $catalog['section'];
		$section_array = explode(',', $section);
		//$old_cat = $catalog['cat'];
		//$old_cat_array = explode(',', $old_cat);
		//$old_catttt = explode(',', $old_cat);
		//$old_firstCat = $old_catttt[1];
		//$old_deleteKey = array_search($old_firstCat, $old_catttt);
		//unset($old_catttt[$old_deleteKey]);
		$langMassiv = $core->getLangList(true);
		$lln = 'Редактировать товар';
		$dosave = $lang['update'];
		$remote = ADMIN.'/module/catalog/ajax/isurl/update';
		$attribute_array = json_decode($catalog['attribute'], true);
        $spec = json_decode($catalog['spec'], true);
		$publish_urls = json_decode($catalog['publish_urls'], true);
        $weight = json_decode($catalog['weight'], true);
        $sku = $catalog['sku'];
        $sku2 = $catalog['sku2'];
        $sku3 = $catalog['sku3'];
        $sku_UdmurtSnab = $catalog['sku_UdmurtSnab'];
        $catalog_groups_id = $catalog['catalog_groups_id'];
	}
	else
	{
		$id = false;
		$fulltitle = false;
		$title = false;
		$short = false;
		$full = false;
		//$author = $core->auth->user_info['nick'];
		$uid = $core->auth->user_info['id'];
		$date = gmdate('d.m.Y H:i');
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
		//$price = 0;
		$types = false;
		$fields = false;
		$fix = '';
        $sku = '';
        $sku2 = '';
        $sku3 = '';
        $sku_UdmurtSnab = '';
		$active = 1;
		$active_guest = 0;
		$active_user = 0;
		$use_integration = 0;
		$edit = false;
		$catttt = array();
		$grroups = array();
		$firstCat = '';
		$lln = $lang['catalog_add'];
		$dosave = $lang['add'];
		$manufacture = "";
		$remote = ADMIN.'/module/catalog/ajax/isurl';
		$catalog_groups_id = null;
		$section_array = null;
		$unit = null;
	}
	$queryXF = $db->query("SELECT * FROM `".DB_PREFIX."_xfields` WHERE module='catalog'");

	$queryAT = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_atribute WHERE cat LIKE '%,".$firstCat.",%'");
	$adminTpl->admin_head('Менеджер каталога  | ' . $lln);
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
        $(".select2-single").select2(
		{
		 \'locale\': \'ru\'
		}
		);
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
	$cats_arr_old = $core->aCatList('catalog');
	$core->aCatArray = array();
	$cats_arr = $core->aCatList('catalog_v2');
	echo '<section id="content" class="table-layout animated fadeIn">
				<div class="tray tray-center">
					<form action="{MOD_LINK}/save" enctype="multipart/form-data" role="form" method="POST" name="content" id="admin-form">
					<div class="panel mb25 mt5">
						<div class="panel-heading br-b-ddd"><span class="panel-title hidden-xs">'.$lln.'</span>
							<ul class="nav panel-tabs-border panel-tabs">
								<li class="active"><a href="#tab1_1" data-toggle="tab">'.$lang['catalog_add_tab_general'].'</a></li>
								<li><a href="#tab1_4" data-toggle="tab">Настройки товара</a></li>
                                <li><a href="#tab1_6" data-toggle="tab">Спецификации</a></li>
								<li><a href="#tab1_2" data-toggle="tab">Другие настройки</a></li>
								<li><a href="#tab1_5" data-toggle="tab">Атрибуты</a></li>
								<li><a href="#tab1_3" data-toggle="tab">'.$lang['catalog_add_tab_access'].'</a></li>
								<li><a href="#tab1_7" data-toggle="tab">Файлы</a></li>
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
														<input id="title" type="text" name="title" placeholder="'.$lang['catalog_add_name'].'" class="event-name gui-input br-light light" '.(isset($nid) ? '' : 'onchange="getTranslit(gid(\'title\').value, \'translit\');"').' value="' . (isset($title) ? $title : '') . '">
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
													<label for="uid" class="field prepend-icon">
														<select name="uid" class="select2-single form-control">';

														foreach (\Boot\Models\Users::all() as $key => $value) {
															$selected = ($value->id == $uid) ? "selected" : "";
																echo '<option value="'.$value->id.'" '.$selected.'>'.$value->full_user_name.'</option>';
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

												<div class="section row mb15">

												<div class="col-xs-6">
                                                    <label class="field mb5"><b>Артикул</b></label>
                                                    <label for="images" class="field prepend-icon smart-widget sm-right">
                                                        <input id="sku" type="text" name="sku" placeholder="Введите артикул" class="gui-input" value="'.$sku.'">
                                                        <label for="sku" class="field-icon"><i class="fa fa-user"></i></label>
                                                        <button id="skuGenerate" class="button" type="button"><i class="fa fa-refresh"></i></button>
                                                    </label>
												</div>
												<div class="col-xs-6">
                                                    <label class="field mb5"><b>Артикул 2</b></label>
                                                    <label for="images" class="field prepend-icon smart-widget sm-right">
                                                        <input id="sku2" type="text" name="sku2" placeholder="Введите артикул 2" class="gui-input" value="'.$sku2.'">
                                                        <label for="sku2" class="field-icon"><i class="fa fa-user"></i></label>
                                                        <!-- <button id="skuGenerate2" class="button" type="button"><i class="fa fa-refresh"></i></button>	-->
                                                    </label>
												</div>
												<div class="col-xs-6">
                                                    <label class="field mb5"><b>Артикул 3</b></label>
                                                    <label for="images" class="field prepend-icon smart-widget sm-right">
                                                        <input id="sku3" type="text" name="sku3" placeholder="Введите артикул 3" class="gui-input" value="'.$sku3.'">
                                                        <label for="sku" class="field-icon"><i class="fa fa-user"></i></label>
                                                    </label>
												</div>
												<div class="col-xs-6">
                                                    <label class="field mb5"><b>Артикул УдмуртСнаб</b></label>
                                                    <label for="images" class="field prepend-icon smart-widget sm-right">
                                                        <input id="sku_UdmurtSnab" type="text" name="sku_UdmurtSnab" placeholder="Введите артикул УдмуртСнаб" class="gui-input" value="'.$sku_UdmurtSnab.'">
                                                        <label for="sku2" class="field-icon"><i class="fa fa-user"></i></label>
                                                    </label>
												</div>
												';

												$new_sku = \Catalog_v2\Model\Catalog::$NEW_SKU;

                                                $adminTpl->footIncludes[] = '
												<script>
													function sku_gen(sel, selector){
														if(sel != ""){
															$(selector).val("'.$new_sku.'");
															alert("Артикул будет создан автоматически");
														} else {
															alert("Выберите категорию");
														}
													}
                                                    $("#skuGenerate").click(function(){
                                                        //console.log("~");
                                                        const sel = $("#category_new").val();
                                                        sku_gen(sel, "#sku");
                                                    });
                                                    // $("#skuGenerate2").click(function(){
                                                    //     const sel = $("#category_new").val();
                                                    //     sku_gen(sel, "#sku2");
                                                    // });
                                                </script>';

									echo '<div class="col-xs-6">
											<label class="field mb5"><b>Категория:</b></label>
											<label for="category_new" class="field prepend-icon">
												<select name="category_new" class="select2-single form-control" id="category_new">
													<option value="">'.$lang['choose_cat'].'</option>';
														foreach ($cats_arr as $cid => $name)
														{
															$selected = ($cid == $firstCat) ? "selected" : "";
															echo '<option value="' . $cid . '" ' . $selected . '>' . $name . '</option>';
														}
												echo'</select>
												<label for="category_new" class="field-icon"><i class="fa fa-user"></i></label>
											</label>
										</div>
										<div class="col-xs-6">
											<label class="field mb5"><b>Группа товаров:</b></label>';
											$oSelectCustomData = [
												"mod" => "jsAdminTpl",
												"name" => "catalog_groups_id",
												"id" => $catalog_groups_id,
												"ajaxUrl" => "getCatalogGroupsToSelect",
												"allowClear" => true,
											];
											oSelectCustom($oSelectCustomData);
											$oSelectCustomData["mod"] = "select";
											oSelectCustom($oSelectCustomData);
										echo '</div>';
										if($id){
											echo '<div class="col-xs-12 mt-10"><a href="/administration/module/price/catalog_price_rival/view/'.$id.'" target="_blank">Перейти в настройку цены</a></div>';
										}
									echo '</div>

									</div>';

                                    $adminTpl->js_code[] = '$("#category_new").change(function(){
                                        ajaxGet("administration/module/catalog_v2/ajax/attr_data/"+$(this).val(), \'attrData\');
                                    });';

                                    echo '<div class="col-md-3">
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


							<div id="tab1_2" class="tab-pane">
								<div class="section row mbn">
									<div class="col-xs-6 pr15">
										<div class="section mb10">
											<label class="field mb5">'.$lang['catalog_add_fulltitle'].':</label>
											<label for="fulltitle" class="field prepend-icon">
												<input id="fulltitle" type="text" name="fulltitle" placeholder="'.$lang['catalog_add_fulltitle_pre'].'" class="event-name gui-input bg-light br-light" value="' .  (isset($fulltitle) ? $fulltitle : '') . '">
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
										<div class="section mb10">
											<label class="field mb5">Ссылка на обзор 360:</label>
											<label for="specVolume" class="field prepend-icon">
												<input id="specVolume" type="text" name="view_360" placeholder="Ссылка на 360" class="event-name gui-input bg-light br-light" value="' . (isset($publish_urls['view360']) ? $publish_urls['view360'] : '') . '">
												<label for="specVolume" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
											</label>
										</div>
									</div>
									<div class="col-xs-6">
										<label class="field option">
										 '.checkbox('status', $active, $lang['catalog_add_activate']).'
										</label><br>
										<label class="field option mt15">
										 '.checkbox('index', $allow_index, 'Рекомендуемые?').'
										</label><br>
										<label class="field option mt15">
										 '.checkbox('active_guest', $active_guest, 'Активная номенклатура для гостей').'
										</label><br>
										<label class="field option mt15">
										 '.checkbox('active_user', $active_user, 'Активная номенклатура для пользователей').'
										</label><br>
										<label class="field option mt15">
										 '.checkbox('use_integration', $use_integration, 'Использовать в интеграции?').'
										</label><br>
										<input type="hidden" name="comments" value="0" />
										<input type="hidden" name="rating" value="0" />
										<input type="hidden" name="fix" value="0" />
									</div>
								</div>
								<br>
							</div>


                            <div id="tab1_6" class="tab-pane">
							<h5>' . (isset($title) ? $title : '') . '</h5>
								<div class="section row mbn">
									<div class="col-xs-6 pr15">
										<div class="section mb10">
											<label class="field mb5">Наименование:</label>
											<label for="specName" class="field prepend-icon">
												<input id="specName" type="text" name="specName" placeholder="Наименование" class="event-name gui-input bg-light br-light" value="' .  (isset($spec[1]) ? $spec[1] : '') . '">
												<label for="specName" class="field-icon"><i class="fa fa-code"></i></label>
											</label>
										</div>
										<div class="section mb10">
											<label class="field mb5">Марка обозначения:</label>
											<label for="specMark" class="field prepend-icon">
												<input id="specMark" type="text" name="specMark" placeholder="Марка обозначения" class="event-name gui-input bg-light br-light" value="' . (isset($spec[2]) ? $spec[2] : '') . '">
												<label for="specMark" class="field-icon"><i class="fa fa-edit"></i></label>
											</label>
										</div>
										<div class="section mb10">
											<label class="field mb5">Доп. производитель:</label>
											<label for="specManufacturer" class="field prepend-icon">
												<input id="specManufacturer" type="text" name="specManufacturer" placeholder="Доп. производитель" class="event-name gui-input bg-light br-light" value="' . (isset($spec[3]) ? $spec[3] : '') . '">
												<label for="specManufacturer" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
											</label>
										</div>
										<hr class="alt short mv15">
										<p class="text-muted"><span class="fa fa-exclamation-circle text-warning fs15 pr5"></span> '.$lang['catalog_add_seo'].'</p>
									</div>
									<div class="col-xs-6">
                                    <div class="section mb10">
											<label class="field mb5">Вес:</label>
											<label for="specWeight" class="field prepend-icon">
												<input id="specWeight" type="text" name="specWeight" placeholder="Вес" class="event-name gui-input bg-light br-light" value="' .  (isset($weight[1]) ? $weight[1] : '') . '">
												<label for="specWeight" class="field-icon"><i class="fa fa-code"></i></label>
											</label>
										</div>
										<div class="section mb10">
											<label class="field mb5">Длина:</label>
											<label for="specLength" class="field prepend-icon">
												<input id="specLength" type="text" name="specLength" placeholder="Длина" class="event-name gui-input bg-light br-light" value="' . (isset($weight[2]) ? $weight[2] : '') . '">
												<label for="specLength" class="field-icon"><i class="fa fa-edit"></i></label>
											</label>
										</div>
										<div class="section mb10">
											<label class="field mb5">Объем:</label>
											<label for="specVolume" class="field prepend-icon">
												<input id="specVolume" type="text" name="specVolume" placeholder="Объем" class="event-name gui-input bg-light br-light" value="' . (isset($weight[3]) ? $weight[3] : '') . '">
												<label for="specVolume" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
											</label>
										</div>
                                        <div class="section mb10">
											<label class="field mb5">Площадь:</label>
											<label for="specSquare" class="field prepend-icon">
												<input id="specSquare" type="text" name="specSquare" placeholder="Площадь" class="event-name gui-input bg-light br-light" value="' . (isset($weight[4]) ? $weight[4] : '') . '">
												<label for="specSquare" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
											</label>
										</div>
										<div class="section mb10">
											<label class="field mb5">Количество в упаковке:</label>
											<label for="specCount" class="field prepend-icon">
												<input id="specCount" type="text" name="specCount" placeholder="Количество в упаковке" class="event-name gui-input bg-light br-light" value="' . (isset($weight[5]) ? $weight[5] : '') . '">
												<label for="specCount" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
											</label>
										</div>
										<input type="hidden" name="comments" value="0" />
										<input type="hidden" name="rating" value="0" />
										<input type="hidden" name="fix" value="0" />
									</div>
								</div>
								<br>
							</div>';

                            if(!empty($firstCat)){
                                echo '<script type="text/javascript">ajaxGet(\'{MOD_LINK}/ajax/attr_data/'.$firstCat.'/'.$id.'\', \'attrData\');</script>';
                            }
								echo '<div id="tab1_5" class="tab-pane form-horizontal">
								<div class="form-group"><label class="col-sm-3 control-label"></label><div class="col-sm-8"><h5>' . (isset($title) ? $title : '') . '</h5></div></div>
                                <div id="attrData">
										Выберите категорию
								</div>';
                                /*
								while($xfield = $db->getRow($queryAT))
								{
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
                                */
								echo '</div>';



					echo'		<div id="tab1_4" class="tab-pane">
								<div class="section row mbn">
									<div class="col-xs-6 pr15">
										<div class="section mb10">
											<label class="field mb5">Область применения:</label>
											<label for="section" class="field" id="cc">



										<select id="multiselect2" name="section[]" multiple="multiple">';


											$query_section_list = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_section");
														if($db->numRows($query_section_list) > 0)
														{
															while($section_list = $db->getRow($query_section_list))
															{
															if($section_array) $selected = in_array($section_list['id'], $section_array) ? "selected" : "";
																echo '<option value="'.$section_list['id'].'" '.$selected.'>'.$section_list['title'].'</option>';
															}
														}


                      echo     ' </select>



										</label>
										</div>
										<div class="section mb10">
											<label class="field mb5"><b>Производитель:</b></label>
											<label for="manufacture" class="field prepend-icon">
														<select name="man" class="select2-single form-control">
														<option value="" >Не указано</option>';
														$query_manufacture_list = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_manufacture ORDER BY name ASC");
														if($db->numRows($query_manufacture_list) > 0)
														{
															while($manufacture_list = $db->getRow($query_manufacture_list))
															{
															$selected = ($manufacture_list['id'] == $manufacture) ? "selected" : "";
																echo '<option value="'.$manufacture_list['id'].'" '.$selected.'>'.$manufacture_list['name'].'</option>';
															}
														}
														echo'	</select>
														<label for="translit" class="field-icon"><i class="fa fa-user"></i></label>
													</label>
										</div>
										<div class="section mb10">
											<label class="field mb5"><b>Вид номенклатуры:</b></label>
											<label for="description" class="field prepend-icon">
												<select class="select2-single form-control" name="typeOfNomenclature">';
														$typeOfNomenclatureQuery = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_types");
														while($ton = $db->getRow($typeOfNomenclatureQuery)){
															$selected = "";
															if($types === $ton["id"]){
																$selected = 'selected';
															}
															echo '<option value="'.$ton["id"].'" '.$selected.'>'.$ton["title"].'</option>';
														}
												echo '</select>
												<label for="description" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
											</label>
										</div>
										<hr class="alt short mv15">
										<p class="text-muted"><span class="fa fa-exclamation-circle text-warning fs15 pr5"></span> '.$lang['catalog_add_seo'].'</p>
									</div>
									<div class="col-xs-6">';
										echo '<div class="section mb10">
											<label class="field mb5"><b>Единица хранения:</b></label>
											<label for="unit" class="field prepend-icon">
												<select name="unit" class="select2-single form-control">';
														foreach(\Catalog_v2\Model\CatalogUnit::all() as $item){
															echo "<option value='{$item->unit}'>{$item->name}</option>";
														}
														echo'
												</select>
												<label for="translit" class="field-icon"><i class="fa fa-user"></i></label>
											</label>
										</div>
										<div class="section mb10">
											<label class="field mb5">Минимальное количество:</label>
											<label for="qmin" class="field prepend-icon">
												<input id="qmin" type="text" name="qmin" placeholder="Минимальное количество" class="event-name gui-input bg-light br-light" value="' .  (isset($catalog['qmin']) ? $catalog['qmin'] : '') . '">
												<label for="qmin" class="field-icon"><i class="fa fa-code"></i></label>
											</label>
										</div>
                                        <div class="section mb10">
											<label class="field mb5">Допустимое количество:</label>
											<label for="qnorm" class="field prepend-icon">
												<input id="qnorm" type="text" name="qnorm" placeholder="Допустимое количество" class="event-name gui-input bg-light br-light" value="' .  (isset($catalog['qnorm']) ? $catalog['qnorm'] : '') . '">
												<label for="qnorm" class="field-icon"><i class="fa fa-code"></i></label>
											</label>
										</div>

									</div>
								</div>
								<br>
							</div>';


							$template = "Файлы не приложены";
							if(isset($catalog)){
								$attach_id = json_decode($catalog["attach_id"], true);
								if(count($attach_id) > 0){
									$te = [];
									foreach($attach_id as $aid){
										$a_file = \Boot\Models\Attach::find($aid);
										if($a_file){
											$te[] = "<div class='file-item'>
												<a href='/download/{$aid}'>{$a_file->name}</a>
												<i class='fas fa-times text-danger cp ml20 file-item__delete' title='Удалить'></i>
												<input type='hidden' name='attach_id[]' value='{$aid}'>
											</div>";
										}
									}
									$template = implode("", $te);
								}
							}

							echo <<<HTML
								<div id="tab1_7" class="tab-pane">
									<div><i class="">Переместите файлы чтобы поменять порядок использования</i></div>
									<div><i class="text-danger"><b>Не забудьте сохранить товар для сохранения изменений файлов</b></i></div>
									<div class="mt20 mb20" id="file-items">{$template}</div>
									<input type='file' name='attach_id'>
								</div>
HTML;

							echo '<div id="tab1_3" class="tab-pane">';
								// echo '<div class="section">
								// 	<b>'.$lang['catalog_add_accsess_title'].'</b>
								// 	<p>'.$lang['catalog_add_accsess'].'</p>
								// 	<hr class="alt short mv15">
								// 	<label class="field select-multiple">
								// 		<select name="groups[]" id="group" class="form-control" multiple>
								// 			<option value="" ' . (empty($grroups) ? 'selected' : '') . '">'. $lang['catalog_add_accsess_all'] .'</option>';
								// 			$query = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups` ORDER BY admin DESC,moderator DESC,user DESC,guest DESC,banned DESC");
								// 			while($rows = $db->getRow($query))
								// 			{
								// 				$selected = in_array($rows['id'], $grroups) ? "selected" : "";
								// 				echo '<option value="' . $rows['id'] . '" ' . $selected . '>' . $rows['name'] . '</option>';
								// 			}
								// 	echo' </select>
								// 	</label>
								// </div>';
							echo '</div>';
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
						mkdir($dir, 0777, true);

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
							.adminArea('short', (isset($short) ? $short : ''), 5, 'textarea', 'onchange="caa(this);"', true, true).'
						</div>
						<div id="tab2" class="tab-pane">'
							.adminArea('full', (isset($full) ? $full : ''), 5, 'textarea', 'onchange="caa(this);"', true, false).'
						</div>
					</div>
				</div>
				<div class="btn-group">
				  <button type="button" class="btn btn-hover btn-success" onclick="genPreview(\'title\', \'short[' . $config['lang'] . ']\', \'full[' . $config['lang'] . ']\');"><i class="fa fa-desktop"></i></button>
				  <button  type="submit" class="btn btn-success dark">' . $dosave . '</button>
				</div>';
				if($edit)
				{
					// <script>
					// 		$(\'#admin-form\').submit(function (e) {
					// 			var form = this;
					// 			e.preventDefault();
					// 			setTimeout(function () {
					// 				form.submit();
					// 			}, 1000); // in milliseconds
					// 		});
					// 		</script>
					echo '<input type="hidden" name="edit" value="1" />';
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
	$adminTpl->footIncludes[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/add.js"></script>';
	$adminTpl->admin_foot();
}


function catalog_save()
{
global $adminTpl, $core, $db, $cats, $groupss, $config, $lang;
	// $bb = new bb;
	// $word_counter = new Counter();
	// $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
	// $fulltitle = htmlspecialchars($_POST['fulltitle'], ENT_QUOTES);
	// $uid = $_POST['uid'];
	// $ttime = 'UNIX_TIMESTAMP(NOW())';
	// $date = !empty($_POST['date']) ? filter($_POST['date']) : $ttime;
	// $oldAltName = !empty($_POST['oldAltName']) ? filter($_POST['oldAltName']) : '';
	// $translit = ($_POST['translit'] !== '') ? mb_strtolower(str_replace(array('-', ' '), array('_', '_'), $_POST['translit']), 'UTF-8') : translit($title);
	// $full = isset($_POST['full']) ? $_POST['full'] : '';
	// $qmin = isset($_POST['qmin']) ? $_POST['qmin'] : '';
	// $qnorm = isset($_POST['qnorm']) ? $_POST['qnorm'] : '';
	// $sku = isset($_POST['sku']) ? $_POST['sku'] : '';
	// $sku2 = isset($_POST['sku2']) ? $_POST['sku2'] : '';
	// $unit = isset($_POST['unit']) ? $_POST['unit'] : '';
	// $short = isset($_POST['short']) ? $_POST['short'] : '';
	// $xfield = isset($_POST['xfield']) ? $_POST['xfield'] : '';
	// $attribute_array = isset($_POST['atri']) ? $_POST['atri'] : '';
	// $man = isset($_POST['man']) ? $_POST['man'] : 'null';
	// $category_new = isset($_POST['category_new']) ? $_POST['category_new'] : '0';
	// $category = isset($_POST['old_cat']) ? array_unique($_POST['old_cat']) : '0';
	// $section = isset($_POST['section']) ? array_unique($_POST['section']) : '0';
	// $groups = isset($_POST['groups']) ? $_POST['groups'] : '0';
	// $comment = isset($_POST['comments']) ? 1 : 0;
	// $sync_title = isset($_POST['sync_title']) ? 1 : 0;
	// $sync_price = isset($_POST['sync_price']) ? 1 : 0;
	// $rating = isset($_POST['rating']) ? 1 : 0;
	// $index = isset($_POST['index']) ? 1 : 0;
	// $types = isset($_POST["typeOfNomenclature"]) ? $_POST["typeOfNomenclature"] : 0;
	// $status = isset($_POST['status']) ? 1 : 0;
	// $active_guest = isset($_POST['active_guest']) ? 1 : 0;
	// $active_user = isset($_POST['active_user']) ? 1 : 0;
	// $fix = isset($_POST['fix']) ? 1 : 0;
	$edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : '';
	// $cnt = (($full['ru'] != '') ? $full['ru'] : $short['ru']);
	// $gen_tag =  $word_counter->get_keywords(substr($cnt, 0, 500));
	// $keywords = !empty($_POST['keywords']) ? $_POST['keywords'] : $word_counter->get_keywords(substr($cnt, 0, 500));
	// $newcnt = $bb->parse(processText(filter(fileInit('catalog', $edit_id, 'content', $cnt), 'html')), $edit_id, true);
	// $description = !empty($_POST['description']) ? $_POST['description'] : substr(strip_tags($newcnt), 0, 150);


	// $publish_urlsJson = [];
	// $publish_urlsJson['view_360'] = !empty(htmlspecialchars($_POST['view_360'], ENT_QUOTES)) ? $_POST['view_360'] : null;
	// $publish_urlsJsonRes = json_encode($publish_urlsJson , JSON_UNESCAPED_UNICODE);

    // $specJson = [];
    // $specJson[1] = !empty(htmlspecialchars($_POST['specName'], ENT_QUOTES)) ? htmlspecialchars($_POST['specName'], ENT_QUOTES) : null;
    // $specJson[2] = !empty(htmlspecialchars($_POST['specMark'], ENT_QUOTES)) ? htmlspecialchars($_POST['specMark'], ENT_QUOTES) : null;
    // $specJson[3] = !empty(htmlspecialchars($_POST['specManufacturer'], ENT_QUOTES)) ? htmlspecialchars($_POST['specManufacturer'], ENT_QUOTES) : null;
    // $specJsonRes = json_encode($specJson, JSON_UNESCAPED_UNICODE);

    // $specJson2 = [];
    // $specJson2[1] = !empty(htmlspecialchars($_POST['specWeight'], ENT_QUOTES)) ? htmlspecialchars($_POST['specWeight'], ENT_QUOTES) : null;
    // $specJson2[2] = !empty(htmlspecialchars($_POST['specLength'], ENT_QUOTES)) ? htmlspecialchars($_POST['specLength'], ENT_QUOTES) : null;
    // $specJson2[3] = !empty(htmlspecialchars($_POST['specVolume'], ENT_QUOTES)) ? htmlspecialchars($_POST['specVolume'], ENT_QUOTES) : null;
    // $specJson2[4] = !empty(htmlspecialchars($_POST['specSquare'], ENT_QUOTES)) ? htmlspecialchars($_POST['specSquare'], ENT_QUOTES) : null;
	// $specJson2[5] = !empty(htmlspecialchars($_POST['specCount'], ENT_QUOTES)) ? htmlspecialchars($_POST['specCount'], ENT_QUOTES) : null;
    // $specJsonRes2 = json_encode($specJson2, JSON_UNESCAPED_UNICODE);

    // $id1c = generate_guid();
	// if($edit_id > 0)
	// {
	// 	$old_dataQ = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '" . $edit_id . "'");
	// 	$old_data = $db->getRow($old_dataQ);
	// 	$id1c = $old_data['id_1c'];
	// }
	// if($date != $ttime)
	// {
	// 	$parseDate = explode(' ', $date);
	// 	$subDate = explode('.', $parseDate[0]);
	// 	if(isset($parseDate[1]))
	// 	{
	// 		$subTime = explode(':', $parseDate[1]);
	// 	}
	// 	else
	// 	{
	// 		$subTime[0] = 12;
	// 		$subTime[1] = 0;
	// 	}
	// 	$date = gmmktime($subTime[0], $subTime[1], 0, $subDate[1], $subDate[0], $subDate[2]);
	// }

	// if(is_array($category))
	// {
	// 	$firstCat = $category[0];
	// 	unset($category[0]);
	// 	$deleteCat = array_search($firstCat, $category);
	// 	unset($category[$deleteCat]);
	// 	$category[0] = $firstCat;
	// 	ksort($category);
	// 	foreach($category as $cid)
	// 	{
	// 		$cats .= intval($cid) . ",";
	// 	}
	// }
	// else
	// {
	// 	$cats  = $category . ',';
	// }

	// if(is_array($section))
	// {
	// 	ksort($section);
	// 	$sections = "";
	// 	foreach($section as $cid)
	// 	{
	// 		$sections .= intval($cid) . ",";
	// 	}
	// }
	// else
	// {
	// 	$sections  = $section . ',';
	// }

	// $fieldsSer = '';
	// if(!empty($xfield))
	// {
	// 	foreach($xfield as $xId => $xContent)
	// 	{
	// 		if(!empty($xContent) && $xId > 0 && !empty($xfieldT[$xId]))
	// 		{
	// 			$xContent = processText(filter($xContent, 'html'));
	// 			$xId = intval($xId);
	// 			$xfieldT[$xId] = filter($xfieldT[$xId], 'title');
	// 			$fileds[$xId] = array($xfieldT[$xId], $xContent);
	// 		}
	// 	}

	// 	$fieldsSer = serialize($fileds);
	// }

	// $attribute = json_encode($attribute_array, JSON_UNESCAPED_UNICODE);


	// $cats = ',' . $cats;
	// $sections = ',' . $sections;

	// if(is_array($groups))
	// {
	// 	foreach($groups as $gid)
	// 	{
	// 		$groupss .= intval($gid) . ",";
	// 	}
	// }
	// else
	// {
	// 	$groupss  = $groups . ',';
	// }
	// $groupss = ',' . $groupss;
	$lln = $lang['catalog_adds'];
	if(isset($_POST['edit']))
		{
			$lln = $lang['catalog_edits'];
		}
	$adminTpl->admin_head($lang['catalog'] . ' | ' . $lln);

	// require_once("usr/modules/catalog_v2/admin/sub/notif.php");

	// echo '<div id="content" class="animated fadeIn">';
	// if($title && $short['ru'] && $uid && $translit)
	// {
	// 	if(isset($_POST['edit']))
	// 	{
	// 		if(isset($_POST['from_user']) && $status == 0)
	// 		{
	// 			$status = 2;
	// 		}
	// 		$update = $db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `uid` = '" . $uid . "', `spec` = '" . $specJsonRes . "', `publish_urls` = '" . $publish_urlsJsonRes . "', `weight` = '" . $specJsonRes2 . "', `qmin` = '" . $qmin . "', `qnorm` = '" . $qnorm . "', `date` = '" . $date . "', `title` = '" . $title . "', `short` = '" . $short . "', `full` = '" . $full . "', `cat` = '" . $cats . "', `category` = '," . $category_new . ",', `altname` = '" . $translit . "', `keywords` = '" . $keywords . "', `description` = '" . $description . "', `allow_comments` = '" . $comment . "', `allow_rating` = '" . $rating . "', `allow_index` = '" . $index . "', `attribute` = '" . $attribute . "', `groups` = '" . $groupss . "', `man` = " . $man . ", `fixed` = '" . $fix . "', `active` = '" . $status . "', `section` = '" . $sections . "', `unit` = '". $unit ."', `sku` =  '". $sku ."', `sku2` =  '". $sku2 ."', `types` = '".$types."', `fulltitle` = '".$fulltitle."', `active_guest` = '".$active_guest."', `active_user` = '".$active_user."' WHERE `id` = '" . $edit_id . "' LIMIT 1 ;");
	// 		$nnid = $edit_id;
	// 		syncRegister('catalog_v2', $edit_id, '2', '0');
	// 		if($update)
	// 		{
	// 			$data = [
	// 				"cid" => $nnid,
	// 				"type" => 2,
	// 			];

				$adminTpl->info($lang['catalog_edit_ok'], 'info', null, $lang['info'], $lang['catalog_list'], ADMIN.'/module/catalog-v2/');
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$insert = $db->query("INSERT INTO `" . DB_PREFIX . "_catalog` (`id_1c`, `uid`, `date`, `cat`, `altname`, `keywords`, `description`, `allow_comments`, `allow_rating`, `allow_index`, `score`, `votes`, `views`, `comments`, `fields`, `groups` , `fixed`, `active`, `spec`, `publish_urls`, `weight`, `qmin`, `qnorm`, `title`, `short`, `full`, `category`, `attribute`, `man`, `section`, `unit`, `sku`, `sku2`, `types`, `fulltitle`, `active_guest`, `active_user`) VALUES ('" . $id1c . "', '" . $uid . "', " . $date . ", '" . $cats . "', '" . $db->safesql($translit) . "', '" . $keywords . "', '" . $description . "', '" . $comment . "', '" . $rating . "', '" . $index . "', '0', '0', '0', '0', '" . $fieldsSer . "', '" . $groupss . "', '" . $fix . "', '" . $status . "', '" . $specJsonRes . "', '" . $publish_urlsJsonRes . "', '" . $specJsonRes2 . "', '" . $qmin . "', '" . $qnorm . "', '" . $title . "', '" . $short . "', '" . $full . "', '," . $category_new . ",', '" . $attribute . "', " . $man . ", '" . $sections . "', '". $unit ."', '". $sku ."', '". $sku2 ."', '".$types."', '".$fulltitle."', '".$active_guest."', '".$active_user."');");
    //         $nnid = $db->insert_id();
	// 		syncRegister('catalog_v2', $nnid, '1', '0');

    //         if($insert)
	// 		{
	// 			$nshort =  $bb->parse(processText(filter(fileInit('catalog', $nnid, 'content', $short, 'html')), $nnid, true));
	// 			$nfull =  $bb->parse(processText(filter(fileInit('catalog', $nnid, 'content', $full, 'html')), $nnid, true));
    //             if($sku == "AE05".$category_new."XXXXXX"){
    //                 $n1 = str_pad($category_new, 3, "0", STR_PAD_LEFT);
    //                 $n2 = str_pad($nnid, 6, "0", STR_PAD_LEFT);
    //                 $sku = "AE05".$n1.$n2;
    //             }
	// 			$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `short` = '" . $nshort . "', `full` = '" . $nfull . "', `sku` = '".$sku."' WHERE `id` = '".$nnid."'");

	// 			$data = [
	// 				"cid" => $nnid,
	// 				"type" => 1,
	// 			];

	// 			fileInit('catalog', $catalog['id']);
    //         }
	// 	}

	// 	catalog_change_init($data);

/*
        $queryUnit = $db->query("SELECT name FROM `" . DB_PREFIX . "_catalog_unit` WHERE `id` = '".$unit."' LIMIT 1");
        $unitName = $db->getRow($queryUnit);
        $text = '<?xml version="1.5"?>
<Message xmlns:msg="http://www.1c.ru/SSL/Exchange/Message" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<msg:Header>
		<msg:Format>http://v8.1c.ru/edi/edi_stnd/EnterpriseData/1.5</msg:Format>
		<msg:CreationDate>'.date("Y-m-d").'T'.date("H:i:s").'</msg:CreationDate>
		<msg:AvailableVersion>1.5</msg:AvailableVersion>
	</msg:Header>
	<Body xmlns="http://v8.1c.ru/edi/edi_stnd/EnterpriseData/1.5">
		<Справочник.Номенклатура>
			<КлючевыеСвойства>
				<Ссылка>'.$id1c.'</Ссылка>
				<НаименованиеПолное>'.$title.'</НаименованиеПолное>
				<КодВПрограмме>КП-'.$nnid.'</КодВПрограмме>';
                if(!empty($sku))
                  $text .=  '<Артикул>'.$sku.'</Артикул>';

				$text .= '<Наименование>'.$title.'</Наименование>
			</КлючевыеСвойства>
			<ТипНоменклатуры>Товар</ТипНоменклатуры>
			<ЕдиницаИзмерения>
				<Код>'.$unit.'</Код>
				<Наименование>'.$unitName['name'].'</Наименование>
			</ЕдиницаИзмерения>
			<СтавкаНДС>НДС20</СтавкаНДС>
			<ДанныеАлкогольнойПродукции>
				<АлкогольнаяПродукция>false</АлкогольнаяПродукция>
				<ВидАлкогольнойПродукции xsi:nil="true"/>
				<ИмпортнаяАлкогольнаяПродукция>false</ИмпортнаяАлкогольнаяПродукция>
				<ОбъемДАЛ xsi:nil="true"/>
				<ПроизводительИмпортер xsi:nil="true"/>
			</ДанныеАлкогольнойПродукции>
			<ВидНоменклатуры>
				<Ссылка>7c93962f-76d4-11e6-9543-14dda9e689bb</Ссылка>
				<Наименование>Товары</Наименование>
				<ТипНоменклатуры>Товар</ТипНоменклатуры>
			</ВидНоменклатуры>
		</Справочник.Номенклатура>
	</Body>
</Message>';
        $uname = uniqid();
		$file_name = ROOT."/files/exchange/catalog/".$uname.".xml";
        $fp = fopen($file_name, "w");
        fwrite($fp, $text);
        fclose($fp);
		//soapSend($file_name);
		unlink($file_name);
*/
		$uid = $core->auth->user_info['id'];
		$request = [];
		$request["title"] = isset($_POST["title"]) ? $_POST["title"] : '';
		$request["fulltitle"] = isset($_POST["fulltitle"]) ? $_POST["fulltitle"] : '';
		$request["altname"] = isset($_POST["translit"]) ? $_POST["translit"] : '';
		$request["full"] = isset($_POST["full"]) ? $_POST["full"] : '';
		$request["qmin"] = isset($_POST["qmin"]) ? $_POST["qmin"] : '';
		$request["qnorm"] = isset($_POST["qnorm"]) ? $_POST["qnorm"] : '';
		$request["sku"] =  empty($_POST["sku"]) ? null : $_POST["sku"];
		$request["sku2"] = empty($_POST["sku"]) ? null : $_POST["sku2"];
		$request["sku3"] = empty($_POST["sku"]) ? null : $_POST["sku3"];
		$request["sku_UdmurtSnab"] = isset($_POST["sku_UdmurtSnab"]) ? $_POST["sku_UdmurtSnab"] : '';
		$request["unit"] = isset($_POST["unit"]) ? $_POST["unit"] : '';
		$request["short"] = isset($_POST["short"]) ? $_POST["short"] : '';
		$request["attribute"] = isset($_POST["atri"]) ? $_POST["atri"] : [];
		// $request["attribute"] = json_encode($request["attribute"], JSON_UNESCAPED_UNICODE);
		$request["man"] = empty($_POST["man"]) ? null : $_POST["man"];
		$request["category"] = isset($_POST["category_new"]) ? $_POST["category_new"] : null;
		$request["catalog_groups_id"] = isset($_POST["catalog_groups_id"]) ? $_POST["catalog_groups_id"] : null;
		$request["section"] = isset($_POST["section"]) ? $_POST["section"] : '';
		$request["groups"] = isset($_POST["groups"]) ? $_POST["groups"] : '';
		$request["allow_comments"] = isset($_POST["comments"]) ? $_POST["comments"] : '';
		$request["types"] = isset($_POST["typeOfNomenclature"]) ? $_POST["typeOfNomenclature"] : '';
		$request["allow_rating"] = isset($_POST["rating"]) ? $_POST["rating"] : 0;
		$request["allow_index"] = isset($_POST["index"]) ? 1 : 0;
		$request["description"] = isset($_POST["description"]) ? $_POST["description"] : '';
		$request["active"] = isset($_POST["status"]) ? '1' : '0';
		$request["active_guest"] = isset($_POST["active_guest"]) ? 1 : 0;
		$request["active_user"] = isset($_POST["active_user"]) ? 1 : 0;
		$request["use_integration"] = isset($_POST["use_integration"]) ? 1 : 0;
		$request["fixed"] = isset($_POST["fix"]) ? $_POST["fix"] : 0;
		$request["keywords"] = isset($_POST["keywords"]) ? $_POST["keywords"] : '';
		$request["uid"] = isset($_POST["uid"]) ? $_POST["uid"] : '';
		$publish_urls = [];
		$publish_urls['view_360'] = isset($_POST["view_360"]) ? $_POST["view_360"] : null;
		$request["publish_urls"] = $publish_urls;
		$spec = [];
		$spec[1] = empty($_POST['specName']) ? null : htmlspecialchars($_POST['specName'], ENT_QUOTES);
		$spec[2] = empty($_POST['specMark']) ? null : htmlspecialchars($_POST['specMark'], ENT_QUOTES);
		$spec[3] = empty($_POST['specManufacturer']) ? null : htmlspecialchars($_POST['specManufacturer'], ENT_QUOTES);
		// $request["spec"] = json_encode($spec, JSON_UNESCAPED_UNICODE);
		$request["spec"] = $spec;
		$weight = [];
		$weight[1] = empty($_POST['specWeight']) ? null : htmlspecialchars($_POST['specWeight'], ENT_QUOTES);
		$weight[2] = empty($_POST['specLength']) ? null : htmlspecialchars($_POST['specLength'], ENT_QUOTES);
		$weight[3] = empty($_POST['specVolume']) ? null : htmlspecialchars($_POST['specVolume'], ENT_QUOTES);
		$weight[4] = empty($_POST['specSquare']) ? null : htmlspecialchars($_POST['specSquare'], ENT_QUOTES);
		$weight[5] = empty($_POST['specCount']) ? null : htmlspecialchars($_POST['specCount'], ENT_QUOTES);
		// $request["weight"] = json_encode($weight, JSON_UNESCAPED_UNICODE);
		$request["weight"] = $weight;
		$request["attach_id"] = $_POST["attach_id"] ?? [];

		// $catalog = new Catalog_v2\Catalog;
		// if(empty($edit_id)){
		// 	$nnid = $catalog->add($request);
		// } else {
		// 	$nnid = $edit_id;
		// 	$catalog->update($nnid, $request);
		// }

		if(empty($edit_id)){
			$model = \Catalog_v2\Model\Catalog::create($request);
			$nnid = $model->id;
		} else {
			$nnid = $edit_id;
			$model = \Catalog_v2\Model\Catalog::find($edit_id);
			$model->update($request);
		}


		if(isset($_FILES["attach_id"]) && !empty($_FILES["attach_id"]["tmp_name"])){
			$aid_file = $_FILES["attach_id"];
			$attach = new \Boot\Models\Attach;
			$attach->name = $aid_file["name"];
			$new_file_name = time()."_".$aid_file["name"];
			$patch = "files/calc/".$new_file_name;
			$attach->url = $patch;
			$attach->pid = $nnid;
			$attach->module = "catalog_v2";
			$attach->save();

			rename($aid_file["tmp_name"], ROOT.$patch);
			chmod(ROOT.$patch, 0755);

			$orig = $model->attach_id;
			$orig[] = $attach->id;
			$model->attach_id = $orig;
			$model->save();
		}



			if (isset($_POST['preview_del']))
			{
				if(isset($_POST['edit']))
				{
					if (file_exists(ROOT.$model->preview)) unlink(ROOT.$model->preview);
					$model->preview = '';
					$model->save();
				}
			} else {
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
						if($foo = new \Boot\System\Upload\Upload($_FILES['preview']))
						{
							$foo->file_new_name_body = 'preview_' .$nnid;
							$foo->image_resize = false;
							$foo->image_x = 600;//$catalog_conf['thumb_width'];
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
						$model->preview = "/{$file_full}";
						$model->save();
					}
			}
		echo '</div>';
		$adminTpl->admin_foot();
}
