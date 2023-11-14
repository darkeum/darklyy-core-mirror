<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2014 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('AJAX')) 
{
    header('Location: /');
    exit;
}

function alt_tagcode()
{
    global $db, $lang;
    $title = filter(utf_decode(urldecode($_REQUEST['title'])), 'title');
	
    list($check) = $db->fetchRow($db->query("SELECT * FROM `" . DB_PREFIX . "_tagcode` WHERE tag='" . translit($title) . "'"));
    if ($check > 0) {
        echo '<font color="red">' . $lang['tagcode_alt_error'] . '</font>';
    } else {
        echo '<font color="green">' . str_replace('{title}', $title, '' . $lang['tagcode_alt_ok'] . '') . '</font>';
    }
} 

function captcha()
{
    session_start();    
    $captcha = new KCAPTCHA;
    $_SESSION['securityCode'] = $captcha->getKeyString();
  
}

function check_login() 
{
    global $db;
    $uname = filter(utf_decode(urldecode($_REQUEST['uname'])), 'nick');
    list($check) = $db->fetchRow($db->query("SELECT Count(id) FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE nick='" . $db->safesql($uname) . "'"));
    if($check > 0) 
    {
        echo '<font color="red">' . _USER_EXISTS . '</font>';
    } 
    else 
    {
        echo '<font color="green">' . str_replace('{uname}', $uname, _USER_FREE) . '</font>';
    }
}

function cal($op) 
{
    require ROOT . 'usr/plugins/calendar.plugin.php';
    calendar($op);
}

function searchList()
{
    global $db;
    $query = filter(utf_decode($_REQUEST['query']), 'a');

    $news = $db->query("SELECT n.id, l.title, n.date, n.altname FROM " . DB_PREFIX . "_news as n LEFT JOIN ".DB_PREFIX."_langs as l on(l.postId=n.id and l.module='news') WHERE l.title LIKE '%" . $db->safesql($query) . "%'");

    if($db->numRows($news) > 0 && $query)
    {
        echo '<ul>';

        while($rows = $db->getRow($news)) 
        {
            echo '<li><a href="/news/view/'. $rows['altname'] . '">' . $rows['title'] . '</a> - ' . formatDate($rows['date']);
        }

        echo '</ul>';
    }
    else
    {
        echo '<ul>';
        echo '<li>' . _NOTHING_FOUND;
        echo '</ul>';
    }
}





function editTitle()
{
    global $db, $core, $config;
    if($core->auth->isAdmin)
    {
        $text = trim(filter(utf_decode($_REQUEST['text'])));
        $id = intval($_REQUEST['id']);
        $module = filter($_REQUEST['module'], 'module');

        switch($module)
        {
            case 'blocks':
                $db->query("UPDATE `" . DB_PREFIX . "_plugins` SET `title` = '" . $db->safesql(processText($text)) . "' WHERE `id` =" . $id . " LIMIT 1 ;");
                break;				

            case 'news':
                $db->query("UPDATE `" . DB_PREFIX . "_langs` SET `title` = '" . $db->safesql(processText($text)) . "' WHERE `postId` ='" . $id . "' AND module='news' AND lang='" . $config['lang'] . "' LIMIT 1 ;");
                break;	

            case 'content':
                $db->query("UPDATE `" . DB_PREFIX . "_langs` SET `title` = '" . $db->safesql(processText($text)) . "' WHERE `postId` ='" . $id . "' AND module='content' AND lang='" . $config['lang'] . "' LIMIT 1 ;");
                break;							

            case 'forum':
                $db->query("UPDATE `" . DB_PREFIX . "_board_forums` SET `title` = '" . $db->safesql(processText($text)) . "' WHERE `id` =" . $id . " LIMIT 1 ;");
                break;					

            case 'modules':
                $db->query("UPDATE `" . DB_PREFIX . "_plugins` SET `content` = '" . $db->safesql(processText($text)) . "' WHERE `id` =" . $id . " LIMIT 1 ;");
                break;					

            case 'blockTypes':
                $db->query("UPDATE `" . DB_PREFIX . "_blocks_types` SET `title` = '" . $db->safesql(processText($text)) . "' WHERE `type` ='" . $id . "' LIMIT 1 ;");
                break;				
            case 'poll':
                $db->query("UPDATE `" . DB_PREFIX . "_polls` SET `title` = '" . $db->safesql(processText($text)) . "' WHERE `id` =" . $id . " LIMIT 1 ;");
                break;
            case 'group':
                $db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_groups` SET `name` = '" . $db->safesql(processText($text)) . "' WHERE `id` =" . $id . " LIMIT 1 ;");
                break;
        }

        echo $text . ' - <span style="color:green">' . _TITLE_UPDATE . '</span>';
    }
}

function _fileFf($format)
{
    switch($format)
    {
        default: $icon = 'blank.png'; break;
        case 'doc': $icon = 'word.png'; break;
        case 'bmp': $icon = 'word.png'; break;		
        case 'jpg':
        case 'jpeg': $icon = 'jpg.png'; break;		
        case 'png': $icon = 'png.png'; break;		
        case 'gif': $icon = 'gif.png'; break;	
		case 'psd': $icon = 'photoshop.png'; break;	
        case 'mp3': case 'wav': case 'ogg': $icon = 'music.png'; break;			
		case 'avi': case 'flv': case 'wmv': $icon = 'music.png'; break;	
        case 'pdf': $icon = 'finerider.png'; break;
        case 'exe': $icon = 'exe.png'; break;	
        case 'txt': $icon = 'txt.png'; break;			
        case 'phps': $icon = 'php.png'; break;		
        case 'html': case 'htm': $icon = 'code.png'; break;
        case 'rar': case 'zip':  case '7z': $icon = 'rar.png'; break;
    }
	
	return $icon;
}

function fileList()
{
    global $db, $core, $admin_conf, $url;
    $id = filter($_REQUEST['id'], 'a');
    $module = filter($_REQUEST['module'], 'module');
    $delete =  isset($_REQUEST['delete']) ? filter($_REQUEST['delete'], 'url') : '';
    $truncate =  isset($_REQUEST['truncate']) ? true : false;
    $modDir = ROOT . 'files/' . $module . '/';
	$isHTML = ($id == 'temp' && $admin_conf['htmlEditor'] == 1) ? true : false;
	
    if($id && $module)
    {
        if($id == 'temp')
        {
            $pid = '0';
        }
        else
        {
            $pid = $id;
        }

        $q = $db->query("SELECT * FROM `" . DB_PREFIX . "_attach` WHERE pid='" . $pid . "'");

        while($rows = $db->getRow($q))
        {
            $dbArray[$rows['url']] = array($rows['id'], $rows['name']);
        }

        if(!is_dir($modDir)) 
        {
            mkdir($modDir, 0777);
			@chmod_R($modDir, 0777);
            mkdir($modDir.'temp', 0777);
			@chmod_R($modDir.'temp', 0777);
        }

        $dir = ROOT . 'files/' . $module . '/' . $id . '/';
        $fileDir = 'files/' . $module . '/' . $id . '/';

        if(!is_dir($dir)) 
        {
            mkdir($dir, 0777);
			@chmod_R($dir, 0777);
            mkdir($dir.'thumb', 0777);
			@chmod_R($dir.'thumb', 0777);
        } 
        else 
        {
            if(!is_dir($dir.'thumb')) 
            {
                mkdir($dir.'thumb', 0777);
				@chmod_R($dir.'thumb', 0777);
            }
        }

        if($delete && file_exists($dir.$delete))
        {
            unlink($dir.$delete);

            $db->query("DELETE FROM `" . DB_PREFIX . "_attach` WHERE `url` = '" . $fileDir.$delete . "' LIMIT 1");

            if(file_exists($dir.'thumb/thumb-'.$delete))
            {
                unlink($dir.'thumb/thumb-'.$delete);
            }
            echo '<font color="green">' . _FILE_DELETED . ' ['.$fileDir.$delete.']</font>';
        }
        elseif($truncate == true)
        {
            unlinkRecursive($dir);
            unlinkRecursive($dir.'thumb/');
        }

        echo
        '
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="upload">
        <tr>
        <th width="75%">' . _FILE . '</th>
        <th widht="10%">' . _SIZE . '</th>
        <th width="100" align="center">' . _ACTIONS . '</th>
        </tr>
        <tr>
        <th colspan="2" style="text-align:left"><img src="media/uploader/folder.png" border="0" class="icon" width="16" height="16" />' . $fileDir . '</th>
        <th style="text-align:right">
        <img src="media/uploader/trash.png" border="0" class="_pointer icon" alt="' . _CLEAR_PATH . '" title="' . _CLEAR_PATH . '" onclick="fileList(\'' . $id . '\', \'' . $module . '\', \'truncate\', \'\');" />
        <img src="media/uploader/reload.png" border="0" class="_pointer icon" alt="' . _UPDATEFILELIST . '" title="' . _UPDATEFILELIST . '" onclick="fileList(\'' . $id . '\', \'' . $module . '\', \'ok\', \'\');" />
        </th>
        </tr>
        ';
        $dh = opendir($dir);
        $c=0;
        $insertImages = '';
        $insertObject = '';
        while ($file = readdir($dh)) 
        {
            if(is_file($dir.$file) && $file != '.' && $file != '..' && $file != 'Thumbs.db') 
            {
                $yesFile = true;
                $fileInfo = explode('.', $file);
                $fileFormat = end($fileInfo);

                $deistv = '';

                $insertObjectS = isset($dbArray[$fileDir.$file]) ? '[attach=' . $dbArray[$fileDir.$file][0] . ']' : '';
				
				$icon = _fileFf($fileFormat);

                switch($fileFormat)
                {
					case 'bmp':
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'gif':
						if($isHTML)
						{
							$iI = htmlspecialchars(parseBB((file_exists($dir.'thumb/thumb-'.$file) ? '[thumb]' . $fileDir.'thumb/thumb-'.$file . '[/thumb]' : '[img]' . $fileDir.$file . '[/img]')), ENT_QUOTES);
							$insertObjectS = htmlspecialchars(parseBB((file_exists($dir.'thumb/thumb-'.$file) ? '[thumb]' . $fileDir.'thumb/thumb-'.$file . '[/thumb]' : '[img]' . $fileDir.$file . '[/img]')), ENT_QUOTES);
							$insertJS = 'tinyMCE.execCommand(\'mceInsertContent\', false, \'' . $iI . '\');return false;';
							$insertImages .= $iI;
						}
						else
						{
							$insertJS = 'insertCode(\'' . (file_exists($dir.'thumb/thumb-'.$file) ? 'thumb' : 'img') . '\', \'center\', \'' . (file_exists($dir.'thumb/thumb-'.$file) ? $fileDir.'thumb/thumb-'.$file : $fileDir.$file) . '\');';
							$insertImages .= file_exists($dir.'thumb/thumb-'.$file) ? '[thumb]' . $fileDir.'thumb/thumb-'.$file . '[/thumb]' : '[img]' . $fileDir.$file . '[/img]';
							$insertObjectS = file_exists($dir.'thumb/thumb-'.$file) ? '[thumb]' . $fileDir.'thumb/thumb-'.$file . '[/thumb]' : '[img]' . $fileDir.$file . '[/img]';
						}
							$deistv .= '<img src="media/uploader/view.png" border="0" class="_pointer icon" alt="' . _VIEW . '" title="' . _VIEW . '" onclick="CaricaFoto(\'' . $fileDir.$file . '\');" />';
							$deistv .= '<img src="media/uploader/paste.png" border="0" class="_pointer icon" alt="' . _IMAGE_PAST . '" title="' . _IMAGE_PAST . '" onclick="' . $insertJS . '" />';
                        break;	

                    case 'exe':
                    case 'rar':
                    case 'zip':
                    case '7z':
                        $deistv .= '<a href="' . $fileDir.$file . '"><img src="media/uploader/save.png" border="0" class="_pointer icon" alt="' . _DOWNLOAD_FILE . '" /></a>';
                        break;

                    case 'mp3':
                    case 'wav':
                    case 'ogg':
						$insertJS = $isHTML ? 'tinyMCE.execCommand(\'mceInsertContent\', false, \'[audio]' . $fileDir.$file . '[/audio]\');return false;' : 'insertCode(\'audio\', \'\', \'' . $fileDir.$file . '\');';
						$deistv .= '<img src="media/filetypes/music.png" border="0" class="_pointer icon" alt="Вставить аудиофайл" title="Вставить аудиофайл" onclick="' . $insertJS . '" />';
						$deistv .= '<a href="' . $fileDir.$file . '"><img src="media/uploader/save.png" border="0" class="_pointer icon" alt="' . _DOWNLOAD_FILE . '" /></a>';
                        break;			

                    case 'avi':
                    case 'flv':
                    case 'wmv':
						$insertJS = $isHTML ? 'tinyMCE.execCommand(\'mceInsertContent\', false, \'[video]' . $fileDir.$file . '[/video]\');return false;' : 'insertCode(\'video\', \'\', \'' . $fileDir.$file . '\');';
						$deistv .= '<img src="media/filetypes/video.png" border="0" class="_pointer icon" alt="Вставить видео" title="Вставить видео" onclick="' . $insertJS . '" />';
						$deistv .= '<a href="' . $fileDir.$file . '"><img src="media/uploader/save.png" border="0" class="_pointer icon" alt="' . _DOWNLOAD_FILE . '" /></a>';
                        break;	

                }

                $insertObject .= $insertObjectS;
				
				
				$insertJS = $isHTML ? 'tinyMCE.execCommand(\'mceInsertContent\', false, \'' . $insertObjectS . '\');return false;' : 'insertIN(\'' . $insertObjectS . '\', \'' . $module . '\')';
                $deistv .= '<img src="media/uploader/insertall.png" border="0" class="_pointer icon" alt="' . _ATTACH_PAST . '" title="' . _ATTACH_PAST . '" onclick="' . $insertJS . '" />';
                $deistv .= '<img src="/media/edit/cross.png" border="0" class="_pointer icon" alt="Удалить файл" title="' . _DELETE_FILE . '" onclick="fileList(\'' . $id . '\', \'' . $module . '\', \'delete\', \'' . $file . '\');" />';

                echo '
                <tr>
                <td><img src="/media/filetypes/' . $icon . '" border="0" class="icon" width="16" height="16" alt="' . $file . '" />' . $file . ' ' . (isset($dbArray[$fileDir.$file]) ? '<b><font color="green">[db]</font>' : '') . '</td>
                <td align="center">' . formatfilesize(filesize($dir.$file), true) . '</td>
                <td align="right">' . $deistv . '</td>
                </tr>
                ';		
            }
        }
        closedir($dh);

        if(!isset($yesFile))
        {
            echo '<tr><td colspan="3">' . _FILES_NOTFOUND . '</td></tr>';
        }
		
		
		$insertJS1 = $isHTML ? 'tinyMCE.execCommand(\'mceInsertContent\', false, \'' . $insertObject . '\');return false;' : 'insertIN(\'' . $insertObject . '\', \'' . $module . '\')';
		$insertJS2 = $isHTML ? 'tinyMCE.execCommand(\'mceInsertContent\', false, \'' . $insertImages . '\');return false;' : 'insertIN(\'' . $insertImages . '\', \'' . $module . '\')';
        echo '<tr>
        <th colspan="2" ><div align="left">' . _FAST_ACTIONS . '</div></th>
        <th>
		<div  align="right">
        <img src="media/uploader/insertall.png" border="0" class="_pointer icon" alt="' . _INSERTALL_ATTACH . '" title="' . _INSERTALL_ATTACH . '" onclick="' . $insertJS1 . '" />
        <img src="media/uploader/insertimages.png" border="0" class="_pointer icon" alt="' . _INSERTALL_IMAGE . '" title="' . _INSERTALL_IMAGE . '" onclick="' . $insertJS2 . '" />
        <img src="media/uploader/trash.png" border="0" class="_pointer icon" alt="' . _CLEAR_PATH . '" title="' . _CLEAR_PATH . '" onclick="fileList(\'' . $id . '\', \'' . $module . '\', \'truncate\', \'\');" />
        <img src="media/uploader/reload.png" border="0" class="_pointer icon" alt="' . _UPDATEFILELIST . '" title="' . _UPDATEFILELIST . '" onclick="fileList(\'' . $id . '\', \'' . $module . '\', \'ok\', \'\');" />
		</div>
        </th>
        </tr>
        </table>
        ';
    }
}


function loginList()
{
    global $db;
    $query = filter(utf_decode($_REQUEST['query']), 'nick');
    $input = filter($_REQUEST['input'], 'a');


    $user = $db->query("SELECT nick FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE nick LIKE '%" . $db->safesql($query) . "%'");

    if($db->numRows($user) > 0 && $query)
    {
        echo '<ul>';

        while($rows = $db->getRow($user)) 
        {
            echo '<li><a href="javascript:void(0)" onclick="gid(\'' . $input . '\').value = \'' . $rows['nick'] . '\'; showhide(\'' . $input . '\');">' . $rows['nick'] . '</a>';
        }

        echo '</ul>';
    }
	else
	{
        echo '<ul>';
        echo '<li>'._NO_RESULTS;
        echo '</ul>';
	}
}

function getTranslit()
{
    $query = ($_REQUEST['query']);
    echo translit($query);
}

function getCatByModule()
{
    global $core;
    $module = filter($_POST['mod'], 'module');
    $cats_arr = $core->aCatList($module);
    echo "<option value=\"\">Без категории</option>";
    foreach ($cats_arr as $cid => $name) 
    {
        echo '<option value="' . $cid . '">' . $name . '</option>';
    }
}

function getReestr()
{
    global $core, $db;
    $org = filter($_POST['org'], 'module');
	$query_r = $db->query("SELECT * FROM ".DB_PREFIX."_reestr_type WHERE org = '".$org."' ORDER BY id ASC");
			if($db->numRows($query_r) > 0) 
										{
											while($r_list = $db->getRow($query_r)) 
											{												
												echo '<option value="'.$r_list['id'].'" >'.$r_list['short'].' '.$r_list['name'].'</option>';
											}
										}		
	
	
	
    
}

function getVerDoc()
{
    global $core, $db;
    $org = filter($_POST['orglist'], 'module');
	$query_o = $db->query("SELECT r.*, t.short FROM ".DB_PREFIX."_reestr as r LEFT JOIN ".DB_PREFIX."_reestr_type as t on(t.id=r.type) WHERE r.type = '8' AND r.oid = '".$org."'");
	echo '<option value="0" selected>Договор не выбран</option>';
	if($db->numRows($query_o) > 0) 
	{	
		while($o_list = $db->getRow($query_o)) 
		{
			$selected = ($o_list['id'] == $org) ? "selected" : "";
			echo '<option value="'.$o_list['id'].'" '.$selected.'>Договор №'.$o_list['short'].'-'.$o_list['numb'].'</option>';
		}
	}	    
}

function blockStatus()
{
    global $core, $db;
    if($core->auth->isAdmin)
    {
        $id = intval($_REQUEST['id']);
        $to = intval($_REQUEST['to']);
        delcache('plugins');
        $db->query("UPDATE `" . DB_PREFIX . "_plugins` SET `active` = '" . $to . "' WHERE `id` =" . $id . " LIMIT 1 ;");
        delcache('plugins');
        echo 'ok';
    }
}

function userDeleteBlock()
{
    global $core, $db;
    if($core->auth->isAdmin)
    {
        $id = intval($_REQUEST['id']);
        $db->query("DELETE FROM `" . DB_PREFIX . "_plugins` WHERE `id` = " . $id . " LIMIT 1");
        delcache('plugins');
        echo 'ok';
    }
}



