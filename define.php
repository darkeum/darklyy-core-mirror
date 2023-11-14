<?php
/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

define('ACCESS', true);
define('TIMER', microtime(1));
define('ROOT', dirname(__FILE__) . '/');
define('PLUGINS', '/usr/plugins/');
define('COOKIE_AUTH', 'auth_jmy');
define('COOKIE_PAUSE', 'pause_jmy');
define('AJAX', true);
define('PAUSE_TIME', 60);
define('VERSION_ID', '3.0.23');
define('COOKIE_TIME', 2592000);
define('ADMIN', 'administration');
define('ADMIN_TPL', 'usr/tpl/corp/');
define('MODE', 'corp');
define('HACK_SQL', '/SELECT|INSERT|ALTER|DROP|UNION|OUTFILE|WHERE/i');
define('DENIED_HTML', '/<.*?(script|meta|body|object|iframe|frame|applet|style|form|img|onmouseover).*?>/i');

if (!defined('ROUTE')) {
	define('ROUTE', '');
	define('ROUTE_LINK', '/');
} else {
	define('ROUTE_LINK', 'route/' . mb_strtolower(ROUTE) . '/');
}

if (isset($_COOKIE['DEBUG'])) {
	define('DEBUG', true);
} else {
	define('DEBUG', false);
}

define('INDEX', isset($_GET['url']) ? false : true);
@ini_set('allow_url_fopen', 1);

mb_internal_encoding("UTF-8");

if (DEBUG) {
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
}
require_once ROOT . 'boot/Support/helpers.php';
require_once ROOT . '/vendor/autoload.php';
require_once ROOT . 'boot/Autoloader.class.php';
