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
    
    $dir = ROOT.'/usr/modules/doc/admin/sub/';  
    includeSub($dir);
    
    global $adminTpl, $config, $core, $url, $admin_conf, $db, $lang;

    switch(isset($url[3]) ? $url[3] : null) {
        default:
		    main();
        break;	
        case "add":
            add();
        break;
        case "view":
        $id = $url[4];
            add($id);
        break;
        case "print":
		$id = intval($url[5]);
		print_atridoc($id, $url[4]);		
	break;
    }
