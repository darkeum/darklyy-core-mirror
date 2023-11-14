<?php
    
    if (!defined('ADMIN_SWITCH')) {
        header('Location: /');
        exit;
    }
    
    $dir = ROOT.'/usr/modules/request/admin/sub/';  
    includeSub($dir);
    
    global $adminTpl, $config, $core, $url, $admin_conf, $db, $lang;

    $designers = getGroup(["group" => "designers"]);

    switch(isset($url[3]) ? $url[3] : null) {
        default:
		    // main();
            header('Location: /administration/module/requests/');
        break;	
        case "add":
            // add();
            header('Location: /administration/module/request/add');
        break;
        case "save":
            save();
        break;
        case "view":
            $id = $url[4];
            view2($id);
        break;
        case "viewPanel":
            $id = $url[4];
            viewPanel($id);
        break;
        case "takeRequest":
            $id = $url[4];
            takeRequest($id);
        break;
        case "saveComment":
            $id = $url[4];
            saveComment($id);
        break;
        case "loadComment":
            $id = $url[4];
            loadComment($id);
        break;
        case "finalFile":
            finalFile();
        break;
        case "endQuest":
            $id = $url[4];
            endQuest($id);
        break;
        case "saveData":
            $id = $url[4];
            $field = isset($_POST["field"]) ? $_POST["field"] : false;
            $value = isset($_POST["value"]) ? $_POST["value"] : "";
            saveData($id, $field, $value);
        break;
        case "saveSpecData":
            $id = $url[4];
            saveSpecData($id);
        break;
        case "loadFile":
            $id = $url[4];
            loadFile($id);
        break;
        case "delFileRes":
            $id = $url[4];
            delFileRes($id);
        break;
		case "metering_generate":
            $id = $url[4];
			$metering = new metering($id);
        break;
		case "metering_generate_scheme":
            $id = $url[4];
            metering_generate($id. '2');
        break;
		case "metering_generate_debug":
            $id = $url[4];
            metering_generate($id. '3');
        break;
        case "get_sales":
            $id = $url[4];
            get_sales($id);
        break;
    }
