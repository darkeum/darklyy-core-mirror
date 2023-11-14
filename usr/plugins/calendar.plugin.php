<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2014 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
    header('Location: /');
    exit;
}

use \News\Models\News;

function calendar($do, $ajax = true) 
{
global $core, $db, $date_massiv, $cache;
	if($ajax == true)
	{
		$arr = explode(',', $do);
		if(isset($arr[0], $arr[1]))
		{
			$time = strtotime(trim($arr[0]).'-'.(trim($arr[1])+1).'-01');
		}
		else
			$time = time();
	}
	else
		$time = time();

	if(time() >= $time) 
	{
		$dd = getCache('calendar_block_'.md5($time.'ru'));
		if(empty($dd))
		{
			$days = array();
			$dateFrom = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m', $time), 1, date('Y', $time)));
			$dateTo = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m', $time)+1, 1, date('Y', $time)));
			$news = News::where('status', 1)->whereBetween('published_at', [$dateFrom, $dateTo])->get()->toArray();
			if(!empty($news))
			{
				$days = array();
				foreach($news as $nn) {
					$j = date('j', strtotime($nn['published_at']));
					$days[] = $j;
				}				
			}			
			if(isset($arr[0], $arr[1]) && (time()-31536000) < $time)
			{
				if((trim($arr[0]) == date('Y', time()) && (trim($arr[1])+1) != date('m', time())) || trim($arr[0]) != date('Y', time()))
				{
					setCache('calendar_block_'.md5($time.'ru'), serialize($days), 1209600);
				}
			}
		}
		else
		{
			$days = unserialize($dd);
		}
	}
	
	if(!empty($days)) $days = array_unique($days);

	if(!empty($days)) $day = json_encode($days); else $day = '';
	$content = '';
    if($ajax == false)
    {
        return '<script type="text/javascript" src="/usr/plugins/calendar.js"></script><div id="_calendar"></div><script type="text/javascript">
		calendar();</script>';
    }
    else
    {
        echo $day;
    }
}