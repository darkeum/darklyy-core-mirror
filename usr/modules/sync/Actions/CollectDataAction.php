<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Sync\Actions;

use Sync\Models\SyncBase;
use Sync\Models\SyncRegister;
use Sync\UI\CLI\Commands\SyncCommand;
use Boot\System\Core\Abstracts\Actions\Action;

class CollectDataAction extends Action
{
	public SyncBase $base;
	public SyncCommand $console;
	
	/**
	 * Выполнение действия
	 *
	 * @param  mixed $console
	 * @param  mixed $base
	 * @return array
	 */
	public function run(SyncCommand $console, SyncBase $base): array
	{
		$this->base = $base;
		$this->console = $console;
		$this->console->info("Сбор информации для программе синхронизации с идентификатором: " . $base->id);		
		$result = $this->prepare();
		if (!empty($result)) {
			$table = [];
			foreach ($result as $key => $value) {
				$table[] = [
					'name' => $key, 
					'count' => count($value)
				];
			}
			$this->console->table(['Имя адаптера', 'Количество'], $table);
		} else {
			$this->console->info("Данные для синхронизации отсутствуют");
		}
		return $result;
	}
	
	/**
	 * Подготовка списка для синхронизации
	 *
	 * @return array
	 */
	protected function prepare()
	{
		$this->console->newLine();
		$result = [];
		$resultTemp = [];
		$chunkCount = 500;

		$syncRegister = SyncRegister::where('sync_base', $this->base->id)->where('status', '1');
		$bar = $this->console->getOutput()->createProgressBar($syncRegister->count()/$chunkCount);

		$syncRegister->chunk($chunkCount, function ($items) use (&$resultTemp, $bar) {
			$syncList = $items->toArray();
			if (!empty($syncList)) {
				foreach ($syncList as $value) {
					$resultTemp[$value['module']][$value['id']] = $value['pid'];
				}
			}
			$bar->advance();
		});
		$bar->finish();
		$this->console->newLine(2);
		
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
		if (isset($resultTemp['realization'])) {
			$result['realization'] = $resultTemp['realization'];
			unset($resultTemp['realization']);
		}
		if (isset($resultTemp['realization_bill'])) {
			$result['realizationBill'] = $resultTemp['realization_bill'];
			unset($resultTemp['realization_bill']);
		}	
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

		if($this->base->id == 3){
			if (isset($resultTemp['realization_cashbox'])) {
				$result['realizationCashbox'] = $resultTemp['realization_cashbox'];
				unset($resultTemp['realization_cashbox']);
			}
		}

		// if (!empty($resultTemp)) {
		// 	foreach ($resultTemp as $key => $value) {
		// 		$result[$key] = $value;
		// 	}
		// }

		return $result;
	}
}
