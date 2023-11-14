<?php
global $db, $allowFuncs, $url, $core;

if (isset($allowFuncs))
{
$allowFuncs = array_merge($allowFuncs, ['actionFavorite']);
}


function actionFavorite()
{
	global $core, $db;
	if(!empty($_POST['module']) && !empty($_POST['id']))
	{
		$id = intval($_POST['id']);
		$module = filter($_POST['module']);
		$arr = (isset($_POST['arr']) ? $_POST['arr'] : array());
		$action = filter($_POST['action']);
		switch($module) 
		{
			default:	
				echo 'error';
			break;
			
			case 'news':
				if($action == 'add')
				{
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET favorite = favorite+1 WHERE id ='" . $db->safesql($id) . "' LIMIT 1 ;");
					echo "UPDATE `" . DB_PREFIX . "_news` SET favorite = favorite+1 WHERE id ='" . $db->safesql($id) . "' LIMIT 1 ;";
				}
				else
				{
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET favorite = favorite-1 WHERE id ='" . $db->safesql($id) . "' LIMIT 1 ;");
				}
				echo $id;
			break;
		}
		
	}
}

