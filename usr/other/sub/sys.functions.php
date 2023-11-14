<?php

use Boot\Models\Log;
use Boot\Support\Facades\Auth;
use Carbon\Carbon;

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @info_ru     Место под ваши собственные функции
* @info_en     Place for your own functions
*/

function updateQuantity($cid, $stock_id, $count, $sign, $source = array())
{
	global $core, $db;
	$catalog = \Catalog_v2\Model\Catalog::find($cid);
	// $query_catalog = $db->query("SELECT id FROM ".DB_PREFIX."_catalog WHERE id = '" . $cid . "' LIMIT 1");
	if(!empty($catalog)) 
	{
		// $query_result = $db->getRow($query_ctalog);
		$stockDetail = \Stocks\Models\StockDetail::where('cid', $cid)->where('stock', $stock_id)->first();

		// $query = $db->query("SELECT id, sum FROM ".DB_PREFIX."_stock_detail WHERE cid = '" . $cid . "' AND stock = '".$stock_id."' LIMIT 1");
		if(!empty($stockDetail))
		{
			$uid = Auth::id();
			if(empty($uid)){
				$uid = '79';
			}
			// $stok_detail = $db->getRow($query);
			$sum = $stockDetail->sum;
			$sum_old = $sum;
			$stock_detail_id =  $stockDetail->id;
			if($sign == 'plus')
			{
				$sum = $sum + $count;
			}
			if($sign == 'minus')
			{
				$sum = $sum +-$count;
			}
			if($sign == 'set')
			{
				$sum = $count;
				//$uid = '79';
			}
			$total = $sum;
			\Stocks\Models\StockDetail::find($stock_detail_id)->update([
				'sum' => $sum,
			]);
			// $db->query("UPDATE `" . DB_PREFIX . "_stock_detail` SET `sum` = '".$sum."' WHERE `id` =".$stock_detail_id."  LIMIT 1");
			if ($stock_id == '1')
			{
				// $db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `quantity` = '".$sum."' WHERE `id` =".$cid."  LIMIT 1");
			}
			$module='';
			$pid=0;
			$comment ='';			
			if(is_array($source)&&!empty($source))
			{
				if(isset($source['module'])&&!empty($source['module'])) {$module = $source['module'];}
				if(isset($source['pid'])&&!empty($source['pid'])) {$pid = intval($source['pid']);}
				if(isset($source['comment'])&&!empty($source['comment'])) {$comment = $source['comment'];}
			}
			$stockTransaction = \Stocks\Models\StockTransaction::create([
				'cid' => $cid,
				'stock_id' => $stock_id,
				'uid' => $uid,
				'count' => $count,
				'sign' => $sign,
				'initial_value' => $sum_old,
				'sum' => $sum,
				'date' => Carbon::now(),
				'module' => $module,
				'pid' => $pid,
				'comment' => $comment,
				'status' => 1,
			]);
			// $db->query("INSERT INTO `" . DB_PREFIX . "_stock_transaction` (`id` , `cid` , `stock_id` , `uid` ,  `count` , `sign` , `initial_value` , `sum` , `date` ,  `module` , `pid` , `comment` ,`status`) VALUES (NULL, '" . $cid . "', '" . $db->safesql($stock_id) . "', '" . $db->safesql($uid) . "', '" . $count . "', '" . $sign . "', '" . $sum_old . "', '" . $sum . "', '" . date("Y-m-d H:i:s") . "', '" . $module . "', '" . $pid . "', '" . $comment . "','1');");		
		}	
		else
		{
			$cid = $catalog->id;
			$unique_id = $stock_id.'_'.$cid;
			\Stocks\Models\StockDetail::create([
				'stock' => $stock_id,
				'cid' => $cid,
				'sum' => 0,
				'status' => 1,
			]);
			// $db->query("INSERT INTO `" . DB_PREFIX . "_stock_detail` (`id` , `unique_id` , `stock` ,  `cid` , `sum` , `status`) VALUES (NULL, '".$db->safesql($unique_id)."', '".$stock_id."', '" . $cid . "', '0', '1');");	
			updateQuantity($cid, $stock_id, $count, $sign, $source);
		}
	}	
}

function checkAllStatus($id, $status, $module)
{
	global $core, $db;
	switch(isset($module) ? $module : null) {
	default:
		echo 'NULL';
	break;	
	case "sales":
		$query_ctalog = $db->query("SELECT id, status FROM ".DB_PREFIX."_sales_detail WHERE sales_id = '" . $id . "' AND status < '".$status."' LIMIT 1");
		//echo "SELECT id, status FROM ".DB_PREFIX."_sales_detail WHERE sales_id = '" . $id . "' AND status < '".$status."' LIMIT 1";
		if($db->numRows($query_ctalog) <= 0) 
		{
			$db->query("UPDATE `" . DB_PREFIX . "_sales` SET `status` = '".$status."' WHERE `id` ='".$id."'  LIMIT 1");
		}
	break;
	}		
}

function setLog($type, $pid, $module, $comment)
{
	$userinfo = new userinfo;
	Log::create([
		"type" => $type,
		"uid" => Auth::id(),
		"module" => $module,
		"pid" => $pid,
		"date" => Carbon::now(),
		"comment" => $comment,
		"agent" => $userinfo->agent,
		"ip" => $userinfo->ip,
		"browser" => $userinfo->browser .', версия: '.$userinfo->version,
		"os" => $userinfo->operating_system.', версия: '.$userinfo->os_version,
		"status" => 1,
	]);
}

function getNumbCounter($module, $org)
{
	global $core, $db;
	$query_n = $db->query("SELECT id, numb_counter FROM `" . DB_PREFIX . "_".$module."` WHERE org = '".$org."' AND numb_counter IS NOT NULL ORDER BY id DESC LIMIT 1");
	if($db->numRows($query_n) > 0){
		$real = $db->getRow($query_n);
		$numb_counter = $real['numb_counter'];
	} else {
		$numb_counter = 0;
	}
	
	$numb_counter++;
	$numb = module_perfix($module, $numb_counter, $org);
	$return_array = array();
	$return_array['numb'] = $numb;
	$return_array['numb_counter'] = $numb_counter;
	return $return_array;
}
