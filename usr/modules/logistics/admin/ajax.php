<?php
	global $db, $allowFuncs, $url, $core;
	
	$allowFuncs = array_merge($allowFuncs, ["changeLogisticFastSum"]);
    
	function changeLogisticFastSum(){
		global $db;
			if($_POST['value'] && $_POST['pk'])
			{   $new_price=$_POST['value'];
				$id=$_POST['pk'];
				$db->query("UPDATE `".DB_PREFIX."_logistics_delivery` SET delivery_price = '{$new_price}' WHERE id = {$id}");
			}

		
	}