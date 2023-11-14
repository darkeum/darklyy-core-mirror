<?php

/**
 * @name        JMY CORE
 * @link        https://jmy.su/
 * @copyright   Copyright (C) 2012-2019 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

function get_query_result_stock_transfer($transfer_id)
{
	global $db;
	$query = $db->query("SELECT * FROM `" . DB_PREFIX . "_stock_transfer` WHERE id = '" . $db->safesql($transfer_id) . "'");
	$transfer = $db->getRow($query);
	return $transfer;
}

function check_minus_count_transfer_from_active($transfer_id)
{
	global $db, $allowFuncs, $url, $core;
	if (isset($transfer_id)) {
		$id = $transfer_id;
		$query = $db->query("SELECT std.*, st.stock_out 
		FROM `" . DB_PREFIX . "_stock_transfer_detail` as std 
		LEFT JOIN `" . DB_PREFIX . "_stock_transfer` as st ON(st.id = std.transfer_id) 
		WHERE std.transfer_id = '" . $id . "'");
		if ($db->numRows($query)) {
			$data = [];
			while ($r = $db->getRow($query)) {
				if (empty($r["sdid"])) {
					$pid = $r["id"];
					$module = "transfer";
				} else {
					$pid = $r["sdid"];
					$module = "sales";
				}
				$data[$r["id"]] = [
					"pid" => $pid,
					"module" => $module,
					"count" => $r["count"],
					"cid" => $r["cid"],
					"stock_id" => $r["stock_out"]
				];
			}
			$uid = $core->auth->user_info['id'];
			if(is_null(check_minus_count_custom(["data" => $data, "module" => "transfer", "pid" => $id]))){
				return true;
			} else {
				return false;
			}
		}
		return true;
	}
}

function transfer_active($transfer_id)
{
	global $core, $db;
	if (isset($transfer_id)) {
		$transfer = get_query_result_stock_transfer($transfer_id);
		$stock_in = $transfer['stock_in'];
		$stock_out = $transfer['stock_out'];
		$org = $transfer['org'];
		$sync = new \Sync\Sync;
		if ($transfer['active'] == '0') {
			if (check_minus_count_transfer_from_active($transfer_id)) {
				$active = '1';
				transfer_active_detail($transfer_id, $stock_out, 'minus', $active);
				transfer_active_detail($transfer_id, $stock_in, 'plus', $active);
				$db->query("UPDATE " . DB_PREFIX . "_stock_transfer SET `active` = '1' WHERE `id` = '" . $db->safesql($transfer_id) . "'");
				// syncRegister('stock_transfer', $transfer_id, '2', $org);
				$sync->syncRegister('stock_transfer', $transfer_id, '1', $org);
				transfer_sales_status($transfer_id);
				setLog("info", $transfer_id, "stock_transfer", 'Проведение перемещения со склада ' . $stock_out . ' на ' . $stock_in);
				echo '1';
			} else {
				echo '3';
			}
		} elseif ($transfer['active'] == '1') {
			$active = '0';
			transfer_active_detail($transfer_id, $stock_out, 'plus', $active);
			transfer_active_detail($transfer_id, $stock_in, 'minus', $active);
			$db->query("UPDATE " . DB_PREFIX . "_stock_transfer SET `active` = '0' WHERE `id` = '" . $db->safesql($transfer_id) . "'");
			// syncRegister('stock_transfer', $transfer_id, '2', $org);
			$sync->syncRegister('stock_transfer', $transfer_id, '1', $org);
			transfer_sales_status($transfer_id);
			setLog("info", $transfer_id, "stock_transfer", 'Распроведение перемещения со склада ' . $stock_out . ' на ' . $stock_in);
			echo '2';
		} else {
			echo '3';
		}
	}
}

function get_query_transfer($transfer_id)
{
	global $db;
	$query = $db->query("SELECT std.*, sd.count as sd_count
	FROM `" . DB_PREFIX . "_stock_transfer_detail` as std 
	LEFT JOIN `" . DB_PREFIX . "_sales_detail` as sd ON(sd.id = std.sdid)
	WHERE std.transfer_id = '" . $db->safesql($transfer_id) . "' 
	AND std.status != '0'");
	return $query;
}

function transfer_active_detail($transfer_id, $stock_id, $sign, $active)
{
	global $core, $db;
	$query = get_query_transfer($transfer_id);
	$source = array();
	$source['module'] = 'storage_transfer';
	$source['pid'] = $transfer_id;
	$source['comment'] = 'Проведение документа перемещения #' . module_perfix('storage_transfer', $transfer_id);
	if ($active == '0') {
		$source['comment'] = 'Отмена документа перемещения #' . module_perfix('storage_transfer', $transfer_id);
	}
	if ($db->numRows($query) > 0) {
		require_once(ROOT . "usr/modules/supply/admin/sub/supply_stock_function.php");
		$reserv = new Reserv();
		while ($content = $db->getRow($query)) {
			$cid = $content['cid'];
			$count = $content['count'];
			if ($sign === 'minus') {
				insert_shipment_minus(['cid' => $cid, 'module' => $source['module'], 'pid' => $source['pid'], 'stock_id' => $stock_id, 'count' => $count, 'comment' => $source['comment'], 'insert' => "before"]);
			}
			$uid = $core->auth->user_info['id'];

			updateQuantity($cid, $stock_id, $count, $sign, $source);

			if ($active == '1') {
				if (!empty($content["sdid"])) {
					$reserv->end_reserv($content["sdid"], "sales", 1, $count);
				}
			}

			if ($active == '1' && $sign === "minus") {
				add_supply_stock_detail($cid, $count);
			}
		}
	}
}

function transfer_sales_status($transfer_id)
{
	global $db;

	$transfer = get_query_result_stock_transfer($transfer_id);
	$query = get_query_transfer($transfer_id);

	$url = "/administration/module/storage/transfer/detail/" . $transfer_id;

	if ($db->numRows($query) > 0) {
		while ($content = $db->getRow($query)) {
			//статусы для продажи
			if (!empty($content["sdid"])) {
				$s_count = \Sales\Support\SalesSupport::get_transfer_active_count($content['id']);
				if ($transfer["active"] == '1') {
					if ($s_count == $content["sd_count"]) {
						$status = 15;
						$text = 'Товар перемещен';
					} else {
						$status = 16;
						$text = 'Товар перемещен частично';
					}
					\Sales\Model\SalesDetail::find($content["sdid"])->update(["status" => $status]);

					\Sales\Model\SalesDetailStatusLog::create([
						"sales_detail_id" => $content["sdid"],
						"status" => $status,
						"url" => $url,
					]);
				}
			}
		}
	}
}
