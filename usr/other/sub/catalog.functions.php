<?php

use Boot\Support\Currency;

/**
 * @name        JMY CORE
 * @link        https://jmy.su/
 * @copyright   Copyright (C) 2012-2017 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 * @info_ru     Место под ваши собственные функции
 * @info_en     Place for your own functions
 */

function get_manufacture($format = 'json', $id = null, $type = 'name')
{
	global $core, $db;
	$date = date("d/m/Y");
	$date_file = date("dmY");
	$file_cache = ROOT . '/tmp/cache/man_' . $date_file . '.cache';
	if (!file_exists($file_cache)) {
		$text = "";
		$m_array = array();
		$queryM = $db->query("SELECT id, name, preview FROM " . DB_PREFIX . "_catalog_manufacture");
		if ($db->numRows($queryM) > 0) {
			while ($catalogM = $db->getRow($queryM)) {
				$m_array[$catalogM['id']] = array(
					'id' => $catalogM['id'],
					'name' => $catalogM['name'],
					'preview' => $catalogM['preview']
				);
			}
		}
		$text = json_encode($m_array, JSON_UNESCAPED_UNICODE);
		$files = glob(ROOT . '/tmp/cache/man_*.cache');
		foreach ($files as $file) {
			if (is_file($file))
				unlink($file);
		}
		file_put_contents($file_cache, $text);
	} else {
		$text = file_get_contents($file_cache);
	}
	if ($format == 'data') {
		if (!empty($id)) {
			$m_array = json_decode($text, true);
			if (!isset($m_array[$id])) {
				$id = intval($id);
				$text = $id;
			} else {
				switch ($type) {
					default:
						$text = $m_array[$id]['name'];
						break;
					case "id":
						$text = $m_array[$id]['id'];
						break;
					case "name":
						$text = $m_array[$id]['name'];
						break;
					case "img":
						$text = $m_array[$id]['img'];
						break;
				}
			}
		} else {
			$text = 'Не указано';
		}
	} else {
		switch ($format) {
			default:
				$text = $text;
				break;
			case "json":
				$text = $text;
				break;
			case "array":
				$text = json_decode($text, true);
				break;
			case "object":
				$text = json_decode($text);
				break;
		}
	}
	return $text;
}

function get_unit($format = 'json', $id = null, $type = 'sname')
{
	global $core, $db;
	$date = date("d/m/Y");
	$date_file = date("dmY");
	$file_cache = ROOT . '/tmp/cache/unit_' . $date_file . '.cache';
	if (!file_exists($file_cache)) {
		$text = "";
		$u_array = array();
		foreach (\Catalog_v2\Model\CatalogUnit::all() as $key => $value) {
			$u_array[$value->unit] = [
				'id' => $value->id,
				'unit' => $value->unit,
				'name' => $value->name,
				'sname' => $value->sname
			];
		}		
		$text = json_encode($u_array, JSON_UNESCAPED_UNICODE);
		$files = glob(ROOT . '/tmp/cache/unit_*.cache');
		foreach ($files as $file) {
			if (is_file($file))
				unlink($file);
		}
		file_put_contents($file_cache, $text);
	} else {
		$text = file_get_contents($file_cache);
	}
	if ($format == 'data') {
		if (!empty($id)) {
			$u_array = json_decode($text, true);
			if (!isset($u_array[$id])) {
				$id = intval($id);
				$text = $id;
			} else {
				switch ($type) {
					default:
						$text = $u_array[$id]['sname'];
						break;
					case "sname":
						$text = $u_array[$id]['sname'];
						break;
					case "name":
						$text = $u_array[$id]['name'];
						break;
					case "id":
						$text = $u_array[$id]['id'];
						break;
					case "unit":
						$text = $u_array[$id]['unit'];
						break;
				}
			}
		} else {
			$text = 'Не указано';
		}
	} else {
		switch ($format) {
			default:
				$text = $text;
				break;
			case "json":
				$text = $text;
				break;
			case "array":
				$text = json_decode($text, true);
				break;
			case "object":
				$text = json_decode($text);
				break;
		}
	}
	return $text;
}

function get_quantity_color($cid)
{
	global $core, $db;
	$date = date("d/m/Y");
	$date_file = date("dmY");
	$file_cache = ROOT . '/tmp/cache/quantity_color_' . $date_file . '.cache';
	$file_cache2 = ROOT . '/tmp/cache/quantity_color2_' . $date_file . '.cache';
	//зеленый
	if (!file_exists($file_cache)) {
		$text = "";
		$qc_array = array();
		//2019-06-15 2019-11-20
		//$queryQC = $db->query("SELECT id, cid FROM ".DB_PREFIX."_stock_transaction WHERE sign = 'set' AND date <= '".date("Y-m-d H:i:s")."' AND date > '".date("Y-m-d H:i:s", strtotime("2020-01-18"))."'");		
		$queryQC = $db->query("SELECT id, cid FROM " . DB_PREFIX . "_stock_transaction WHERE sign = 'set' AND date > '" . date("Y-m-d H:i:s", strtotime("2020-01-18")) . "'");
		if ($db->numRows($queryQC) > 0) {
			while ($catalogQC = $db->getRow($queryQC)) {
				$qc_array[$catalogQC['cid']] = array('id' => $catalogQC['id']);
			}
		}
		$text = json_encode($qc_array, JSON_UNESCAPED_UNICODE);
		$files = glob(ROOT . '/tmp/cache/quantity_color_*.cache');
		foreach ($files as $file) {
			if (is_file($file))
				unlink($file);
		}
		file_put_contents($file_cache, $text);
	} else {
		$text = file_get_contents($file_cache);
		$qc_array = json_decode($text, true);
	}
	//фиолетовый
	if (!file_exists($file_cache2)) {
		$text2 = "";
		$qc_array2 = array();
		$queryQC2 = $db->query("SELECT id, cid FROM " . DB_PREFIX . "_stock_transaction WHERE sign = 'set' AND date <= '" . date("Y-m-d H:i:s") . "' AND date >= '" . date('Y-m-d H:i:s', strtotime('-1 day')) . "'");
		if ($db->numRows($queryQC2) > 0) {
			while ($catalogQC2 = $db->getRow($queryQC2)) {
				$qc_array2[$catalogQC2['cid']] = array('id' => $catalogQC2['id']);
			}
		}
		$text2 = json_encode($qc_array2, JSON_UNESCAPED_UNICODE);
		$files2 = glob(ROOT . '/tmp/cache/quantity_color2_*.cache');
		foreach ($files2 as $file) {
			if (is_file($file))
				unlink($file);
		}
		file_put_contents($file_cache2, $text2);
	} else {
		$text2 = file_get_contents($file_cache2);
		$qc_array2 = json_decode($text2, true);
	}

	//установка цвета
	$color = '';
	if (isset($qc_array[$cid])) {
		$color = "#00bb00";
	}
	if (isset($qc_array2[$cid])) {
		$color = "#ae7ef4";
	}

	return $color;
}
