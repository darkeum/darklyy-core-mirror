<?php

//require_once("usr/modules/storage/admin/sub/function.php");

function storage_update_quantity($pid, $storage_id, $cid, $count){
    $source = array();
    $source['module'] = 'storage';
    $source['pid'] = $pid;
    $source['comment'] = 'Установка значений #'.module_perfix('storage', $pid);
    updateQuantity($cid, $storage_id, $count, 'set', $source);
}

function storage_reload_file_quantity(){
    $files = glob(ROOT.'/tmp/cache/quantity_color_*.cache');
	foreach($files as $file)
	{ 
	  if(is_file($file))
		unlink($file); 
	}	
    $files2 = glob(ROOT.'/tmp/cache/quantity_color2_*.cache');
    foreach($files2 as $file)
    { 
      if(is_file($file))
        unlink($file); 
    }	
}