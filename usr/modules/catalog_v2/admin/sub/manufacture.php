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

function manufacture_list()
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Менеджер каталога';
	$adminTpl->admin_head('Менеджер каталога | Производители');
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'catalog_v2/manufacture';
	$atributs['module_open'] = 'edit';
	$atributs['button'] = 'Добавить';
	$atributs['button_url'] = "/administration/module/catalog_v2/manufacture/add";
	load_grid('jqManufacture', $lnn, $atributs, ROOT.'/usr/modules/catalog_v2/admin/grid/');
	$adminTpl->footIncludes[] = '
	<script>
		function viewPreview(This = null){
			let preview = "pppp";	
			if(This !== null){
				preview = $(This).data("preview");
			}
			$("#preview_view").attr("src",preview);
			$("#previewModal").modal("show");
		}
	</script>';
	echo '
  
  <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title" id="exampleModalLabel">Изображение производителя</h5>
		</div>
		<div class="modal-body">
			<center>
				<img id="preview_view" src="" width="400" />
			</center>
		</div>
		<div class="modal-footer">
		  	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		</div>
	  </div>
	</div>
  </div>
	';	
	$adminTpl->admin_foot();
}

function manufacture_add($id = null){
	global $db, $core, $adminTpl;

	$_SESSION["RF"]["fff"] ="files/manufacture/";		
	fancyboxInit();

	if(empty($id)){
		$lnn = "Добавить производителя";
		$name = "";
		$preview = "";
	} else {
		$query = $db->query("SELECT cm.* FROM ".DB_PREFIX."_catalog_manufacture as cm WHERE cm.id = '".$id."' LIMIT 1");
		$r = $db->getRow($query);

		$lnn = "Изменить производителя";
		$name = $r["name"];
		$preview = $r["preview"];
	}

	$adminTpl->admin_head($lnn);
	?>

	<style>
	.cp{
		cursor: pointer;
	}
	</style>

	<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title"><?php echo $lnn; ?></span>
			</div>
			<div class="panel-body admin-form">
				<form id="admin-form" action="/administration/module/catalog_v2/manufacture/save" method="post" name="cats" role="form" enctype="multipart/form-data" class="form-horizontal parsley-form">
					<div class="form-group">
						<label for="date"  class="col-lg-3 control-label">Наименование</label>
						<div class="col-lg-3">
							<input id="name" type="text" name="name" placeholder="Введите наименование" class="gui-input" value="<?php echo $name;?>">
						</div>	
					</div>
					<div class="form-group admin-form">	
						<label for="date"  class="col-lg-3 control-label">Картинка</label>	
						<div class="col-lg-3">								
							<label for="preview" class="field  prepend-icon smart-widget sm-right">
								<input id="preview" type="text" name="preview" placeholder="Введите ссылку на картинку" class="gui-input"  value="<?php echo $preview; ?>">
								<label for="preview" class="field-icon"><i class="fa fa-file-image-o"></i></label>
								<a id="fbox" data-fancybox-type="iframe" href="usr/plugins/filemanager/dialog.php?type=1&amp;field_id=preview&amp;relative_url=0" class="button" type="button"><i class="far fa-folder-open"></i></a>													
							</label>	
						</div>						
					</div>		
					<?php

						$query = $db->query("SELECT cpt.*
						FROM `".DB_PREFIX."_catalog_price_type` as cpt 
						WHERE cpt.currency != '643'");
						if($db->numRows($query) > 0){
							$formula_name = "{{%manufacture_price%}}";
							?>
								<div class="form-group">
									<h5 class="col-lg-3 text-right">Формулы формирования цен 
									<i class="far fa-question-circle cp" title="Используйте спец. слово <?php echo $formula_name;?> для формирования формулы. Нажмите чтобы скопировать спец. слово в буфер обмена" onclick="copy_spec('<?php echo $formula_name;?>');"></i></h5>
								</div>
								<?php
									while($price_type = $db->getRow($query)){
										$formula = "";
										if(!empty($id)){
											$q = $db->query("SELECT formula FROM `".DB_PREFIX."_catalog_manufacture_price` WHERE deleted_at IS NULL AND price_type_id = '".$price_type["id"]."' AND manufacture_id = '".$id."' LIMIT 1");
											if($db->numRows($q) > 0){
												$r = $db->getRow($q);
												$formula = $r["formula"];
											}
										} 
										
										?>
											<div class="form-group">
												<label for="price_type_<?php echo $price_type["id"]; ?>"  class="col-lg-3 control-label"><?php echo $price_type["title"]; ?></label>
												<div class="col-lg-3">
													<input type="text" name="price[<?php echo $price_type["id"]; ?>]" placeholder="Введите формулу" class="form-control" value="<?php echo $formula;?>">
												</div>	
											</div>
										<?php
									}
								?>
							<?php
						}

					?>				
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-4">
							<input type="submit" class="btn btn-primary btn-parsley" id="sub" value="<?php echo $lnn;?>" />
						</div>
					</div>					
					<?php
					if(!empty($id)){
						echo '<input type="hidden" value="'.$id.'" name="edit" id="edit">';
					}
					?>
		</form>
	</div>
</div>

	<?php
	$adminTpl->footIncludes[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/manufacture_add.js"></script>';
	$adminTpl->admin_foot();
}

function manufacture_save(){
	global $db, $core;
	$uid = $core->auth->user_info['id'];
	$name = isset($_POST["name"]) ? htmlspecialchars($_POST["name"], ENT_QUOTES) : '';
	$preview = isset($_POST["preview"]) ? $_POST["preview"] : '';
	$edit = isset($_POST["edit"]) ? $_POST["edit"] : 0;
	if((int)$edit === 0){
		$db->query("INSERT INTO `".DB_PREFIX."_catalog_manufacture` (name, preview, sum) VALUES ('".$name."', '".$preview."', '0')");
		$edit = $db->insert_id();
	} else {
		$db->query("UPDATE ".DB_PREFIX."_catalog_manufacture SET name = '".$name."', preview = '".$preview."'  WHERE id = '".$edit."' LIMIT 1");
	}
	del__cache("man_*");

	$price = isset($_POST["price"]) ? $_POST["price"] : false;
	if($price){
		$price_id = [];
		$price_del = [];
		foreach($price as $key => $val){
			$price_id[] = $key;
		}
		$price_id_str = implode(", ", $price_id);
		$query = $db->query("SELECT * FROM `".DB_PREFIX."_catalog_manufacture_price` WHERE manufacture_id = '".$edit."' AND deleted_at IS NULL AND price_type_id IN(".$price_id_str.")");
		if($db->numRows($query) > 0){
			while($r = $db->getRow($query)){
				if($r["formula"] == $price[ $r["price_type_id"] ]){
					//если значения совпали -> не изменились, не трогаем
					unset($price[ $r["price_type_id"] ]);
				} else {
					//если значения не совпали -> изменились, изменяем
					$price_del[] = $r["id"];
				}
			}
		}
		if(count($price_del) > 0){
			$price_del_str = implode(", ", $price_del);
			$db->query("UPDATE `".DB_PREFIX."_catalog_manufacture_price` SET deleted_at = '".date("Y-m-d H:i:s")."' WHERE id IN(".$price_del_str.")");
		}
		if(count($price) > 0){
			$values = [];
			$date = date("Y-m-d H:i:s");
			foreach($price as $price_type_id => $formula){
				if(!empty($formula)){
					$edit_formula = str_replace(" ", "", $formula);
					$values[] = "('".$edit."', '".$price_type_id."', '".$edit_formula."', '".$uid."', '".$date."')";
				}
			}
			if(count($values) > 0){
				$values_str = implode(", ", $values);
				$db->query("INSERT INTO `".DB_PREFIX."_catalog_manufacture_price` (manufacture_id, price_type_id, formula, uid, date_add) VALUES ".$values_str);
			}
		}
	}
	
	header('Location: /administration/module/catalog_v2/manufacture');
}