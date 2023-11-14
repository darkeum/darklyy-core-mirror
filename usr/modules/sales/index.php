<?php
    
/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2020 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
    header('Location: /');
    exit;
}
require (ROOT.'etc/content.config.php');
        
    global $lang;
    switch(isset($url[1]) ? $url[1] : null) 
    {
        default:
			location('/');
        break;
		
		case "print":
		    require_once (ROOT.'usr/modules/sales/admin/sub/print.php');
		    $id = intval($url[3]);
		    print_sales($id, $url[2]);	
		    exit;
		break;
        
    }                