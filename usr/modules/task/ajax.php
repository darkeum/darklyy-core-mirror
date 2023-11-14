<?php
global $db, $allowFuncs, $url, $core;

if (isset($allowFuncs))
{
$allowFuncs = array_merge($allowFuncs, ['countTaskByUid']);
}

function countTaskByUid(){
	global $db, $allowFuncs, $url, $core;
	  $uid = $core->auth->user_info['id'];
	  if (!empty($_GET['uid']))
	  {
		$uid = intval($_GET['uid']);
	    $query = $db->query("SELECT * FROM ".DB_PREFIX."_task WHERE toid = '".$uid."'");
		echo $db->numRows($query);
	  }
	}

