<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2020 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}


$dir = ROOT.'/usr/modules/corrections/admin/sub/';  
includeSub($dir);

global $db, $url;
switch(isset($url[3]) ? $url[3] : null) {
	default:
        main();
	break;
}
