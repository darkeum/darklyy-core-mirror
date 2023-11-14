<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2020 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @info_ru     Место под ваши собственные функции
* @info_en     Place for your own functions
*/

function chooseOrg($optionOnly = false, $choose_oid = false)
{
	global $db, $config, $core, $url, $user;	
	$org_list ='';
	$choose = 0;
	if(isset($core->auth->user_info['id']))
	{
	$query = $db->query("SELECT oid, post, choose, status FROM ".DB_PREFIX."_users_org WHERE uid = '".$core->auth->user_info['id']."' AND status != '3' ORDER BY oid");
			if($db->numRows($query) > 0)
			{
				while($oids = $db->getRow($query))
				{
					$oid_arr[] = $oids['oid'];
					$post_arr[$oids['oid']] = $oids['post'];
					if ($oids['choose'] == 1)
					{
						$choose = $oids['oid'];
					}
					$status_arr[$oids['oid']] = $oids['status'];
				}
				$where = " WHERE id IN ('".implode("', '", $oid_arr)."') ";
				$queryO = $db->query("SELECT id, name, inn FROM ".DB_PREFIX."_organizations ".$where." ORDER BY name");
				if($db->numRows($queryO) > 0)
				{
					while($org = $db->getRow($queryO))
					{		
						$selected = '';
						if($choose_oid != false)
						{
							if($choose_oid == $org['id'])
							{
								$selected = 'selected="selected"';
							}
						}
						else
						{
							if($choose == $org['id'])
							{
								$selected = 'selected="selected"';
							}
						}
						$org_list .= ' <option value="'.$org['id'].'" '.$selected.'>'.$org['name'].' ('.$org['inn'].')</option>';
					}
				}
			}
			else
			{
				$org_list .= ' <option value="-1" disabled>У вас нет организаций!</option>';
			}	
	}
	else
	{
		$org_list .= ' <option value="-1" disabled>У вас нет организаций!</option>';
	}
	$choose_org ='
		<div id="chooseOrgForm">
			<div class="input-group">
		  <select class="custom-select form-control" id="orgSelect" name="oid">
		   '.$org_list.'
		  </select>
		  <div class="input-group-append">
			<button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#orgAdd"><i class="fas fa-plus"></i></button>
		  </div>
		</div>
		<div class="modal fade" id="orgAdd" tabindex="-1" role="dialog" aria-labelledby="orgAddLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="orgAddLabel">Добавление организации</h5>
						<button type="button" class="close" onclick="$(\'#orgAdd\').modal(\'hide\');" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">					
						<div class="form-group">
							<label for="inn">ИНН организации</label>
							<input type="text" class="form-control" id="innOrg" name="inn" aria-describedby="innHelp" placeholder="Укажите ИНН добавляемой организации" />
							<small id="innHelp" class="form-text text-muted">Все данные об организации заполнятся автоматически.</small>
						</div>
						<div class="form-group">
							<label for="post">Ваша должность</label>
							<input type="text" class="form-control" id="postOrg" name="post" aria-describedby="postHelp" placeholder="Укажите вашу должность" />
							<small id="postHelp" class="form-text text-muted">При отсутствии должности можно указать «Представитель».</small>
						</div>
						<div class="form-group form-check">
							<input type="checkbox" class="form-check-input" id="agreeOrg">
							<label class="form-check-label" for="agreeOrg">Я согласен с правилами добавления организации</label>
						</div>					
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" onclick="$(\'#orgAdd\').modal(\'hide\');">Закрыть</button>
						<button type="button" class="btn btn-primary" onclick="addOrg();">Добавить</button>
					</div>
				</div>
			</div>
		</div>
		<script>
		$(function(){
			$("#orgSelect").select2({
				//width: "100%"
			});
		});

		function addOrg(){
			const dataLink = {};
			dataLink.inn = $("#innOrg");
			dataLink.post = $("#postOrg");
			const data = {};
			for(let key in dataLink){
				data[key] = dataLink[key].val();
			}
			$.ajax({
				type: "POST",
				url: "/ajax.php?do=addOrg",
				data,
				success: function(response) 
				{
					result = jQuery.parseJSON(response);
						$("#orgSelect").select2("destroy"); 	
						PNotify.alert({
							title: result["title"],
							text: result["text"],
							type: result["type"],
							addClass: "stack-bar-bottom",
							width: "100%",
							stack: window.stackBarBottom,
						});			
						$.post("/ajax.php?do=chooseOrgAjax&oid="+result["id"], function(data){
							$("#orgSelect").html(data);					
						});	
						$("#orgSelect").select2();
						$("#orgAdd").modal("hide");
						for(let key in dataLink){
							dataLink[key].val("");
						}
						
				},
				error: function(response) 
				{
					alert("no");
				}		
			});
		}		
		</script>
	</div>';
	if ($optionOnly)
	{
		return $org_list;	
	}
	else
	{
		return $choose_org;
		
	}
}


function chooseObject($optionOnly = false, $choose_object = false)
{
	global $db, $config, $core, $url, $user;	
	$object_list ='';
	if(isset($core->auth->user_info['id'])) {
		$query = $db->query("SELECT object, choose, status FROM ".DB_PREFIX."_users_object WHERE uid = '".$core->auth->user_info['id']."' AND status != '3' ORDER BY object");
		$choose = false;
		if($db->numRows($query) > 0)
		{
			while($objects = $db->getRow($query))
			{
				$object_arr[] = $objects['object'];
				if ($objects['choose'] == 1)
				{
					$choose = $objects['object'];
				}
				$status_arr[$objects['object']] = $objects['status'];
			}
			$where = " WHERE id IN ('".implode("', '", $object_arr)."') ";
			$queryO = $db->query("SELECT * FROM ".DB_PREFIX."_object ".$where." ORDER BY name");
			if($db->numRows($queryO) > 0)
			{
				while($object = $db->getRow($queryO))
				{		
					$selected = '';
					if($choose_object != false)
					{
						if($choose_object == $object['id'])
						{
							$selected = 'selected="selected"';
						}
					}
					else
					{
						if($choose == $object['id'])
						{
							$selected = 'selected="selected"';
						}
					}
					$object_list .= ' <option value="'.$object['id'].'" '.$selected.'>'.$object['name'].' ('.$object['address'].')</option>';
				}
			}
		}
		else
		{
			$object_list .= ' <option value="-1" disabled>У вас нет объектов!</option>';
		}
	}
	else
	{
		$object_list .= ' <option value="-1" disabled>У вас нет объектов!</option>';
	}
	$choose_object ='
		<div id="chooseObjectForm">
			<div class="input-group">
		  <select class="custom-select form-control" id="objectSelect" name="object_id">
		   '.$object_list.'
		  </select>
		  <div class="input-group-append">
			<button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#objectAdd"><i class="fas fa-plus"></i></button>
		  </div>
		</div>
		<div class="modal fade" id="objectAdd" tabindex="-1" role="dialog" aria-labelledby="objectAddLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="objectAddLabel">Добавление объекта</h5>
						<button type="button" class="close" onclick="$(\'#objectAdd\').modal(\'hide\');" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">					
						<div class="form-group">
							<label for="name">Название объекта:</label>
							<input type="text" class="form-control" id="nameObject" name="name" aria-describedby="nameHelp" placeholder="Укажите название объекта" />
							<small id="nameHelp" class="form-text text-muted">В качестве названия может выступать рабочее название проекта.</small>
						</div>
						<div class="form-group">
							<label for="address">Адрес объекта:</label>
							<input type="text" class="form-control" id="addressObject" name="address" aria-describedby="addressHelp" placeholder="Укажите адрес объекта" />
							<small id="addressHelp" class="form-text text-muted">При отсутствии конкретного адреса можно указать город.</small>
						</div>
						<div class="form-group form-check">
							<input type="checkbox" class="form-check-input" id="agreeObject">
							<label class="form-check-label" for="agreeObject">Я согласен с правилами добавления объекта</label>
						</div>					
					</div>
					<input type="hidden" id="coordsObject" name="coords" value=""/>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" onclick="$(\'#objectAdd\').modal(\'hide\');">Закрыть</button>
						<button type="button" class="btn btn-primary" onclick="addObject();">Добавить</button>
					</div>
				</div>
			</div>
		</div>
		<script>
		$(function(){
			$("#objectSelect").select2({
				//width: "100%"
			});
		});

		function addObject(){
			const dataLink = {};
			dataLink.name = $("#nameObject");
			dataLink.address = $("#addressObject");
			dataLink.coords = $("#coordsObject");
			const data = {};
			for(let key in dataLink){
				data[key] = dataLink[key].val();
			}
			$.ajax({
				type: "POST",
				url: "/ajax.php?do=addObject",
				data,
				success: function(response) 
				{
					const result = jQuery.parseJSON(response);
						$("#objectSelect").select2("destroy"); 	
						PNotify.alert({
							title: result["title"],
							text: result["text"],
							type: result["type"],
							addClass: "stack-bar-bottom",
							width: "100%",
							stack: window.stackBarBottom,
						});			
						$.post("/ajax.php?do=chooseObjectAjax&object="+result["id"], function(data){
							$("#objectSelect").html(data);					
						});	
						$("#objectSelect").select2();
						$("#objectAdd").modal("hide");
						for(let key in dataLink){
							dataLink[key].val("");
						}
				},
				error: function(response) 
				{
					alert("no");
				}		
			});
		}		
		</script>
		<style>
			.suggestions-promo{display: none !important;}
		</style>
		<link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@19.8.0/dist/css/suggestions.min.css" rel="stylesheet" />
		<script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@19.8.0/dist/js/jquery.suggestions.min.js"></script>
		<script>
			$("#address").suggestions({
				token: "ddeafb9ff43e79578243d8f031f6e9d8b047ca0e",
				type: "ADDRESS",
				/* Вызывается, когда пользователь выбирает одну из подсказок */
				onSelect: function(suggestion) {
					$("#coords").val(suggestion.data.geo_lat+","+suggestion.data.geo_lon);
					console.log(suggestion.data.geo_lat);
				}
			});
		</script>
	</div>';
	if ($optionOnly)
	{
		return $object_list;	
	}
	else
	{
		return $choose_object;
		
	}
}

