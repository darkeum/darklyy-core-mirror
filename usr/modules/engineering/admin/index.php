<?php


if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}

$dir = ROOT.'/usr/modules/engineering/admin/sub/';  
includeSub($dir);

global $db, $url;

switch(isset($url[3]) ? $url[3] : null) {
	default:
        
    break;
    case "register":
        switch(isset($url[4]) ? $url[4] : null) {
            default:
                main_register();
            break;
            case "add":
                add_register();
            break;
            case "edit":
                $id = isset($url[5]) ? $url[5] : null;
                add_register($id);
            break;
            case "file":
                $id = isset($url[5]) ? $url[5] : null;
                file_register($id);
            break;
            case "file_save":
                file_save();
            break;
            case "file_del":
                $id = isset($url[5]) ? $url[5] : null;
                file_del($id);
            break;
            case "save":
                save_register();
            break;
            case "check_numb":
                $numb = isset($_POST["numb"]) ? $_POST["numb"] : false;
                check_numb($numb);
            break;
        }
    break;
}
