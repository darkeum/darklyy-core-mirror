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
    header('Location: /');
    exit;
}

$dir = ROOT.'/usr/modules/object/admin/sub/';  
includeSub($dir);

switch(isset($url[3]) ? $url[3] : null) {
	default:
		content_main();
	break;	
	
	case "add":
		$data = isset($_POST["data"]) ? $_POST["data"] : false;
		echo add($data);
	break;
	case "add_ajax":
		echo add($_POST);
	break;
		
	case "map":
		map();
	break;
		
	case "view":
		$id = intval($url[4]);
		view2($id, true);
	break;
		
	case "file":
		$id = intval($url[4]);
		allFile($id, true);
	break;
		
	case "task":
		$id = intval($url[4]);
		relations($id, true);
	break;
		
	case "relations":
		$id = intval($url[4]);
		relations($id, false);
	break;
		
	case "sales":
		$id = intval($url[4]);
		sales($id, false);
	break;
		
	case "save":
		object_save();
	break;
}
