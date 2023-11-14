<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
*/

use \Boot\Support\Facades\Auth;

global $config, $db, $url, $core;
//подключение конфигурации
require_once ROOT . 'etc/' . ROUTE_LINK . 'global.config.php';
require_once ROOT . 'etc/admin.config.php';
require_once ROOT . 'etc/security.config.php';
require_once ROOT . 'etc/files.config.php';
require_once ROOT . 'etc/cache.config.php';
require_once ROOT . 'etc/smiles.config.php';
require_once ROOT . 'etc/user.config.php';
require_once ROOT . 'etc/log.config.php';
require_once ROOT . 'boot/db/' . $config['dbType'] . '.db.php';
require_once ROOT . 'boot/db' . (($config['dbCache'] == 1) ? '_cache' : '') . '.class.php';
require_once ROOT . 'boot/auth.class.php';
require_once ROOT . 'boot/template.class.php';
require_once ROOT . 'boot/core.class.php';


$old = true;



//установка темы
if (isset($_COOKIE['theme'])) {
    if (file_exists(ROOT . 'usr/tpl/' . $_COOKIE['theme'] . '/index.tpl')) {
        $config['tpl'] = filter($_COOKIE['theme']);
    } else {
        setcookie('theme', false, time(), '/');
    }
}
// $request->getRequest()->cookies->get('foo');

if (isset($routeMode)) {
    //указываем что используем новый маршрутизатор
    $core->setRouteMode(true);
}
$core->initCore();
// dd($core);

if (isset($coreOnly) && $coreOnly) {
    $GLOBALS['lang'] = array();
    if ($old) {
        $core->LoadLang(false, true);
        $core->LoadLang(false, false, true);
        $core->LoadLang(true, false, true);
        $core->LoadLang();
    }
} else {

    //проверка на бан
    if ($old) {
        if ($user['isBan'] == 1) {
            $banIp = configMatch($security['banIp']);
            $banArr = explode("\n", $banIp);
            foreach ($banArr as $v) {
                if (trim($v) != '') {
                    $banned = explode('=', $v);
                    preg_match("#" . Only1br($banned[0], '') . "#", $_SERVER['REMOTE_ADDR'], $match);
                    if (isset($match[0])) {
                        $core->ban = true;
                        if (isset($banned[1])) {
                            $core->banReason = $banned[1];
                        }
                    }
                }
            }
            if ($core->ban == false && $core->auth->banUser == true) {
                $core->ban = true;
            }
        }
    }

    //выбор раздела для загрузки и проверка на отвлеченность сайта	
    // if (($config['off'] == 0 || Auth::isAdmin() == true || $url[0] == ADMIN) && $core->ban == false) {
    if (($config['off'] == 0 || Auth::isAdmin() == true || $url[0] == ADMIN)) {

        //выполняем триггеры на смену языка темы и смартфона
        if ($old) {
            $core->LoadLang(false, true);
            if (isset($_GET['lang']) || isset($_GET['theme']) || isset($_GET['phone_change'])) {
                if (isset($_GET['lang'])) {
                    if (isset($core->langsLang[$_GET['lang']]) && $_GET['lang'] == $config['lang']) {
                        setcookie('lang', false, time() - 86400, '/');
                    } else if (isset($core->langsLang[$_GET['lang']])) {
                        setcookie('lang', filter($_GET['lang'], 'a'), time() + 86400, '/');
                    }
                }
                if (isset($_GET['theme'])) {
                    $theme = filter($_GET['theme'], 'a');
                    if (is_dir(ROOT . 'usr/tpl/' . $theme . '/') && file_exists(ROOT . 'usr/tpl/' . $theme . '/index.tpl')) {
                        setcookie('theme', $theme, time() + 86400, '/');
                    }
                }
                if (isset($_GET['phone_change'])) {
                    if ($_COOKIE['smartphone'] == 1) {
                        setcookie('smartphone', 0);
                    } else {
                        setcookie('smartphone', 1);
                    }
                }
                location('/?nocache=' . time());
            }
        }
        //создаем кэш категории (переделать)
        // dd(hasCache('categories'));	
        if (!hasCache('categories')) {
            $query = $db->query("SELECT id, name, altname, description, module, icon, parent_id as pid, hidden, position, preview FROM " . DB_PREFIX . "_categories WHERE status='1' ORDER BY CASE WHEN position IS NULL OR position <= 0 THEN '65535' END, position ASC");
            while ($rows = $db->getRow($query)) {
                $cat_array[$rows['module']][$rows['id']] = array('id' => $rows['id'], 'title' => $rows['name'], 'parent' => $rows['pid'], 'altname' => $rows['altname'], 'icon' => $rows['icon'], 'description' => $rows['description'], 'hidden' => $rows['hidden'], 'position' => $rows['position'], 'preview' => $rows['preview']);
                $checkCatArr[$rows['altname']] = $rows['module'];
            }
            if (isset($cat_array)) {
                setCache('categories', $cat_array);
            }
        }
        $url = $GLOBALS['url'];
        //подключаем роутинг на сайте
        $moduleBase = ROOT . 'usr/modules/' . mb_strtolower($url[0]);
        if (file_exists($moduleBase . '/index.php') && !isset($_GET['download'])) {
            if ($old) {
                $core->LoadLang();
            }
            if (!isset($no_head)) {
                $core->tpl->head();
            }
            if (count($url) == 1 && eregStrt('.htm', $url[0])) {
                $GLOBALS['url'][1] = $url[0];
                $GLOBALS['url'][0] = 'content';
            }
            require ROOT . 'usr/modules/' . mb_strtolower($url[0]) . '/index.php';
            if (!isset($no_head)) {
                $core->tpl->foot();
            }
        } elseif ($url[0] == ADMIN && (($core->auth->isUser == true && $core->auth->isAdmin == true) or $core->auth->isUser == false)) {

            //подгружаем панель управления
            $core->LoadLang(false, false, true);
            $core->LoadLang(true, false, true);
            require_once ROOT . 'root/index.php';
        } elseif (isset($_GET['download'])) {
            //скачивание файлов
            $downId = intval($_GET['download']);
            $rows = \Boot\Models\Attach::find($downId)->toArray();
            if (count($rows) > 0) {
                if ($rows['access'] != 1) {
                    if ($core->auth->isAdmin == true) {
                        get_index_file($rows);
                    } else {
                        errorLoad();
                    }
                } else {
                    get_index_file($rows);
                }
            } else {
                errorLoad();
            }
        } else {

            errorLoad();
        }
    } elseif ($core->ban == true) {
        echo (!empty($core->banReason) ? $core->banReason : $security['banIpMessage']);
    } else {
        include(ROOT . 'usr/tpl/lock.tpl');
    }
}
