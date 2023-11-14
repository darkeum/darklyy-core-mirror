<?php


if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}

//*************
// Change path
//*************
$dir = ROOT.'/usr/modules/form/admin/sub/';  
includeSub($dir);

global $db, $url;

switch(isset($url[3]) ? $url[3] : null) {
	default:
        main();
	break;
	case "add":
        form_add();
	break;
}
