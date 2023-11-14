<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description File for grid load
*/

$coreOnly = true;
require ROOT . 'lib/loader_core.php';
// dd('fefffuuu');
require ROOT . 'lib/for_ajax.php';
header('Access-Control-Allow-Origin: *');
global $allowFuncs;
// header('Content-type: text/plain; charset=utf-8');
$allowFuncs = array('draw_rating', 'set_rating', 'fast_edit', 'fast_save', 'add_comment', 'show_comments', 'checkCommentGuest', 'commentDeleteAjax', 'commentPage', 'comment_savea', 'commentEditAjax', 'commentEditAjaxSave', 'check_login', 'cal', 'online', 'searchList', 'ajaxPoll', 'addCarma', 'editTitle', 'fileList', 'loginList', 'getTranslit', 'getCatByModule', 'blockStatus', 'userDeleteBlock', 'fullAjax', 'blockList', 'setBlockStatus', 'setCommentStatus', 'deleteBlock', 'moveUp', 'moveDown', 'captcha', 'carmaHistory', 'genRating', 'commentSubscribe', 'friendsAjax', 'vote', 'inputTags', 'getPreview', 'carma_vote',  'getReestr', 'getVerDoc', 'alt_tagcode');
if (file_exists(ROOT . 'usr/other/other.ajax.php')) {
	require_once(ROOT . 'usr/other/other.ajax.php');
}
if ($core->auth->isAdmin) {
	if (file_exists(ROOT . 'root/other/other.ajax.php')) {
		require_once(ROOT . 'root/other/other.ajax.php');
	}
}
$query = $db->query("SELECT id,title,active FROM " . DB_PREFIX . "_plugins WHERE service='modules' ORDER BY title ASC");
$exceMods = array('feed', 'pm', 'search', 'poll', 'mainpage');
if ($db->numRows($query) > 0) {
	while ($mod = $db->getRow($query)) {
		if (!in_array($mod['title'], $exceMods)) {
			if ($mod['active'] == 1) {
				$file = ROOT . 'usr/modules/' . $mod['title'] . '/ajax.php';
				$file_admin = ROOT . 'usr/modules/' . $mod['title'] . '/admin/ajax.php';
				if (file_exists($file)) {
					require_once($file);
				}
				if ($core->auth->isAdmin) {
					if (file_exists($file_admin)) {
						require_once($file_admin);
					}
				}
			}
		}
	}
}
if (isset($_GET['do']) && !empty($_GET['do'])) {
	$do = $_GET['do'];
	$GLOBALS['url'][0] = 'ajax';
	$tempLink = explode('/', $do);

	for ($i = 0, $max = count($tempLink); $i < $max; $i++) {
		if ($tempLink[$i] == '') {
			continue;
		} else {
			$GLOBALS['url'][] = filter($tempLink[$i], 'url');
		}
	}


	$op = isset($url[2]) ? $url[2] : 0;
	$do = isset($url[3]) ? $url[3] : 0;


	switch (isset($url[1]) ? $url[1] : null) {
		default:

			if (in_array($url[1], $allowFuncs)) {
				$url[1]();
			} else {
				echo $url[1];
			}

			break;

		case 'calendar':
			cal($op);
			break;

		case 'check':
			echo 'It\'s ok';
			break;

		case 'version':
			version_check();
			break;

		case 'captcha_reload':
			echo captcha_image(false);
			break;

		case 'poll':
			ajaxPoll();
			break;

		case 'smiles':
			global $config;
			require_once(ROOT . 'etc/smiles.cofnig.php');
			echo
			'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
				<html>
				<head>
				<base href="' . $config['url'] . '/" />
				</head>
				<body bgcolor="white" leftmargin="0" rightmargin="0" marginwidth="0" marginheight="0" topmargin="0" bottommargin="0">';
			foreach ($smiles as $smile => $info) {
				echo '<img src="' . $info['url'] . '" border="0" title="' . $info['title'] . '" onclick="javascript:insertBB(\'' . $smile . '\', \'' . $op . '\')" class="_pointer">' . "\n";
			}
			echo '</body></html>';
			break;
		case 'search':
			if (!empty($_POST["referal"])) {
				$referal = trim(strip_tags(stripcslashes(htmlspecialchars($_POST["referal"]))));
				$masRef = explode(" ", $referal);
				$search1 = "(";
				$search2 = "(";
				foreach ($masRef as $val) {
					$search1 .= "(sku LIKE '%" . $val . "%' OR title LIKE '%" . $val . "%') AND ";
					$search2 .= "name LIKE '%" . $val . "%' AND ";
				}
				$search1 = mb_substr($search1, 0, -4);
				$search2 = mb_substr($search2, 0, -4);
				$search1 .= ")";
				$search2 .= ")";
				$db_referal = $db->query("SELECT * FROM " . DB_PREFIX . "_categories WHERE module = 'catalog_v2' AND " . $search2);
				if ($db->numRows($db_referal) > 0) {
					echo "<li class='category'>Категории</li>";
					while ($row = $db->getRow($db_referal)) {
						echo "\n<li class='elem' onclick=\"return location.href = '/catalog_v2/" . $row["altname"] . "'\">" . $row["name"] . "</li>"; //$row["name"] - имя поля таблицы
					}
				}
				$db_referal = $db->query("SELECT * FROM `" . DB_PREFIX . "_catalog` WHERE active = 1 AND hidden = 0 AND " . $search1);
				if ($db->numRows($db_referal) > 0) {
					echo "<li class='category'>Товары</li>";
					while ($row = $db->getRow($db_referal)) {
						echo "\n<li class='elem' onclick=\"return location.href = '/catalog_v2/" . $row["altname"] . ".html'\">" . $row["title"] . "</li>"; //$row["name"] - имя поля таблицы
					}
				}
			}
			break;
		case 'search_admin':
			if (!empty($_POST["referal"])) {
				$referal = trim(strip_tags(stripcslashes(htmlspecialchars($_POST["referal"]))));
				$db_referal = $db->query("SELECT id,title FROM " . DB_PREFIX . "_catalog WHERE active=1 AND (sku LIKE '%$referal%' OR title LIKE '%$referal%') ");
				while ($row = $db->getRow($db_referal)) {
					echo "\n<option value=\"" . $row["id"] . "\">" . $row["title"] . "</option>"; //$row["name"] - имя поля таблицы
				}
			}
			break;
	}	
} else {
	echo 'Error';
}

