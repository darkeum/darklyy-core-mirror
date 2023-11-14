<?php

/**
 * @name        JMY CMS
 * @link        http://jmy.su/
 * @copyright   Copyright (C) 2012-2022 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */

namespace Sync\Tasks;

use Sync\Models\SyncRelation;
use Sync\Traits\SyncTrait;
use Sync\Traits\SyncProgress;
use Boot\System\Core\Abstracts\Tasks\Task;

class CatalogTask extends Task
{
	use SyncTrait;
	use SyncProgress;

	protected $model = \Catalog_v2\Model\Catalog::class;

	protected $syncModule 		= 'catalog_v2';
	protected $syncRestLink 	= 'Catalog_Номенклатура';


	/**
	 * Обработчик синхронизации
	 *
	 * @param  mixed $method
	 * @return void
	 */
	public function syncAdapter($method = ['add', 'update'])
	{
		foreach ($this->syncDataComplete as $key => $value) {
			$this->progressStart(count($this->syncDataComplete[$key]), 'Синхронизация данных');
			foreach ($this->syncDataComplete[$key] as $k => $v) {
				if(!isset($this->typeList[$v['data']['types']]['Ref_Key'])){
					$v['data']['types'] = 3;
				}
				$syncData = array(
					'Артикул' => $v['data']['sku'],
					'НаименованиеПолное' => addslashes($v['data']['title']),
					'Description' => addslashes($v['data']['title']),
					'ВидНоменклатуры_Key' => $this->typeList[$v['data']['types']]['Ref_Key'],
					'ЕдиницаИзмерения_Key' => $this->unitList[$v['data']['unit']],
					'Услуга' => (($this->typeList[$v['data']['types']]['service'] != 1) ? 'false' : 'true'),
				);
				$register = false;
				if ($this->isRegister) {
					$register = $v['register'];
				}
				if (($key == 'update') && in_array($key, $method)) {
					$this->progressSetMessage('Обновление номенклатуры: ' . $v['data']['title'] . ' {' . $v['pid'] . '}', 'message');
					$this->restUpdate($syncData, $v['relation'], $register);
				}
				if (($key == 'add') && in_array($key, $method)) {
					$this->progressSetMessage('Добавление номенклатуры: ' . $v['data']['title'] . ' {' . $v['pid'] . '}', 'message');
					$relation = $this->restAdd($syncData, $v['pid'], $register);
					$this->syncDataComplete[$key][$k]['relation'] = $relation;
				}
				$this->progressAdvance();
			}
			$this->progressFinish();
		}
	}

	public function customSync()
	{		
        $this->syncUnit();
        $this->syncType();
	}



















	public $typeList = array();
	public $unitList = array();

	//обработчик единиц измерения
	public function syncUnit()
	{
		$unitList = array();
		$unitRest = $this->Client->{'Catalog_КлассификаторЕдиницИзмерения'}->get()->values();
		foreach ($unitRest as $k => $v) {
			$unitRest[$k]['Code'] = trim($unitRest[$k]['Code']);
		}
		$unitGet = \Catalog_v2\Model\CatalogUnit::all()->toArray();
		foreach ($unitGet as $uValue) {
			$dataKey = array_search($uValue['unit'], array_column($unitRest, 'Code'));
			if ($dataKey === false) {
				$sync_unit = $this->Client->{'Catalog_КлассификаторЕдиницИзмерения'}->create([
					'Code' => $uValue['unit'],
					'Description' => $uValue['sname'],
					'НаименованиеПолное' => $uValue['name'],
				]);
				$syncRef = $sync_unit->getLastId();
				$this->setSyncRelation($uValue['unit'], 'unit', $syncRef, $this->syncBaseKey);
				$unitList[$uValue['unit']] = $syncRef;
			} else {
				$unitList[$uValue['unit']] = $unitRest[$dataKey]['Ref_Key'];
				$items = SyncRelation::where("module", "unit")->where("sync_base", $this->syncBaseKey)->where("pid", $uValue['unit'])->where("status", 1)->get()->toArray();
				if (empty($items)) {
					$this->setSyncRelation($uValue['unit'], 'unit',  $unitRest[$dataKey]['Ref_Key'], $this->syncBaseKey);
				}
			}
		}
		$this->unitList = $unitList;
		return $unitList;
	}

	//обработчик типа номенклатуры
	public function syncType()
	{
		$typeList = array();
		$typeRest = $this->Client->{'Catalog_ВидыНоменклатуры'}->get()->values();
		// dd($typeRest);
		$typeGet = \Catalog_v2\Model\CatalogTypes::all()->toArray();
		foreach ($typeGet as $tValue) {
			$dataKey = array_search($tValue['title'], array_column($typeRest, 'Description'));
			if ($dataKey === false) {
				dump($tValue);
				$sync_unit = $this->Client->{'Catalog_ВидыНоменклатуры'}->create([
					'Description' => $tValue['title'],
					'Услуга' => (($tValue['service'] != 1) ? false : true),
				]);

				// dd($sync_unit);
				// dd($typeRest);
				$syncRef = $sync_unit->getLastId();
				$this->setSyncRelation($tValue['id'], 'catalog_types', $syncRef, $this->syncBaseKey);
				$typeList[$tValue['id']]['Ref_Key'] = $syncRef;
				$typeList[$tValue['id']]['service'] = $tValue['service'];
			} else {
				$typeList[$tValue['id']]['Ref_Key'] = $typeRest[$dataKey]['Ref_Key'];
				$typeList[$tValue['id']]['service'] = $tValue['service'];
			}
		}
		$this->typeList = $typeList;
		return $typeList;
	}
}
