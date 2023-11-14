<?php

/**
 * @name        JMY CMS
 * @link        http://jmy.su/
 * @copyright   Copyright (C) 2012-2021 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */

namespace Sync;

use Sync\Models\SyncBase;
use Sync\Models\SyncRegister;

class Sync
{

	//количество элементов в одной итерации 
	private $limit = 10;
	private $org = false;
	private $syncData = array();
	private $syncBaseKey = false;
	protected $key = ROOT . 'tmp/key/sync.daemon.key';

	public function setOrg($org)
	{
		$this->org = intval($org);
	}

	public function getSyncBaseByOrg()
	{
		$baseList = SyncBase::where("org", $this->org)->where("status", 1)->first();
		$this->syncBaseKey = $baseList->id;
	}

	//сбор данных для синхронизации
	public function collectDataToSync()
	{
		dump('Start sync ' . $this->syncBaseKey);		
		$result = array();
		$resultTemp = array();
		$chunkCount = 0;
		SyncRegister::where('sync_base', $this->syncBaseKey)->where('status', '1')->chunk(500, function ($items) use (&$resultTemp, &$chunkCount) {
			$chunkCount++;
			dump('Get from DataBase chunk: '.$chunkCount);				
			$syncList = $items->toArray();
			if (!empty($syncList)) {
				foreach ($syncList as $value) {
					$resultTemp[$value['module']][$value['id']] = $value['pid'];
				}
			}			
		});
		if (isset($resultTemp['catalog_v2'])) {
			$result['catalog'] = $resultTemp['catalog_v2'];
			unset($resultTemp['catalog_v2']);
		}
		if (isset($resultTemp['organizations'])) {
			$result['organizations'] = $resultTemp['organizations'];
			unset($resultTemp['organizations']);
		}
		if (isset($resultTemp['reestr'])) {
			$result['reestr'] = $resultTemp['reestr'];
			unset($resultTemp['reestr']);
		}
		// if (isset($resultTemp['sales'])) {
		// 	$result['sales'] = $resultTemp['sales'];
		// 	unset($resultTemp['sales']);
		// }
		if (isset($resultTemp['realization'])) {
			$result['realization'] = $resultTemp['realization'];
			unset($resultTemp['realization']);
		}
		if (isset($resultTemp['realization_bill'])) {
			$result['realizationBill'] = $resultTemp['realization_bill'];
			unset($resultTemp['realization_bill']);
		}
		// if(isset($resultTemp['realization_correct'])) {
		// 	$result['realizationCorrect'] = $resultTemp['realization_correct'];
		// 	unset($resultTemp['realization_correct']);
		// }	
		if (isset($resultTemp['receipts'])) {
			$result['receipts'] = $resultTemp['receipts'];
			unset($resultTemp['receipts']);
		}
		if (isset($resultTemp['receipts_bill'])) {
			$result['receiptsBill'] = $resultTemp['receipts_bill'];
			unset($resultTemp['receipts_bill']);
		}
		if (isset($resultTemp['stock_transfer'])) {
			$result['transfer'] = $resultTemp['stock_transfer'];
			unset($resultTemp['stock_transfer']);
		}
		if (!empty($resultTemp)) {
			foreach ($resultTemp as $key => $value) {
				$result[$key] = $value;
			}
		}
		foreach ($result as $key => $value) {
			dump('Adapter '.$key.': '.count($result[$key]). ' items;');
		}
		
		$this->syncData = $result;
		dump('Get data is ok!');
		return $result;
	}

	public function getAdapterClassName($r)
	{
		return "\Sync\Adapter\Adapter" . ucfirst($r);
	}

	public function syncInit()
	{
		$resultArray = array();
		foreach ($this->syncData as $key => $value) {
			$adapterClass = $this->getAdapterClassName($key);
			if (class_exists($adapterClass)) {
				dump($adapterClass);
				$adapter = new $adapterClass();
				$adapter->setSyncData($value);
				$adapter->setSyncBase($this->syncBaseKey);
				try {
					$resultArray = $adapter->syncStart();
				} catch (\GuzzleHttp\Exception\ClientException $e) {
					// dump($e->getMessage());
					dump($e->getResponse()->getBody()->getContents());
				}
			}
		}
		return $resultArray;
	}

	public function syncRegisterByBaseKey($module, $pid, $type, $baseKey = '0')
	{
		$request = array();
		$request['where'] = "id = '" . $baseKey . "'";
		$request['limit'] = "1";
		$baseList = SyncBase::find($baseKey);
		$org = $baseList->org;
		$this->syncRegister($module, $pid, $type, $org);
	}

	//отмечаем необходимость синхронизации
	public function syncRegister($module, $pid, $type, $org = '0')
	{
		if (isset($module, $pid, $type)) {
			$baseList = SyncBase::where("status", 1);
			if ($org != '0') {
				$baseList->where("org", $org);
			}

			$baseList = $baseList->get()->toArray();
			if (!empty($baseList)) {
				foreach ($baseList as $value) {
					SyncRegister::firstOrCreate([
						'module' => $module,
						'pid' => $pid,
						'sync_base' => $value['id'],
						'status' => 1,
					], ['type' => $type]);
				}
			}
			$this->create_key();
		}
	}

	public function get_key()
	{
		return $this->key;
	}

	public function create_key()
	{
		$fp = fopen($this->key, "w");
		fclose($fp);
	}
}
