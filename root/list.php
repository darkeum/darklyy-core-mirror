<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}
if (MODE != 'default' && file_exists(ROOT . 'root/list.'.MODE.'.php')) 
{
	include(ROOT . 'root/list.'.MODE.'.php');
}