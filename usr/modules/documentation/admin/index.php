<?php


if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}

$dir = ROOT.'/usr/modules/documentation/admin/sub/';  
includeSub($dir);

global $db, $url;

switch(isset($url[3]) ? $url[3] : null) {
	default:
        documentation();
	break;	
    case "lists":
        $id = isset($url[4]) ? $url[4] : false;
        documentation_lists($id);
    break;
    case "append":
        edit();
    break;
    case "edit":
        $id = isset($url[4]) ? $url[4] : false;
        edit($id);
    break;
    case "create":
        $did = isset($url[4]) ? $url[4] : false;
        create($did);
    break;
    case "save":
        $id = isset($url[4]) ? $url[4] : false;
        save($id);
    break;
    case "view":
        $id = isset($url[4]) ? $url[4] : false;
        view2($id);
    break;
    case "moderation":
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        $field = isset($_POST["field"]) ? $_POST["field"] : false;
        $value = isset($_POST["value"]) ? $_POST["value"] : 0;
        change_fields($id, $field, $value);
    break;
    case "comment_get":
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        comment_get($id);
    break;
    case "comment_set":
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        $comment = isset($_POST["comment"]) ? $_POST["comment"] : false;
        comment_set($id, $comment);
    break;
    case "search":
        $search = isset($_POST["search"]) ? $_POST["search"] : false;
        documentation_search($search);
    break;
}
