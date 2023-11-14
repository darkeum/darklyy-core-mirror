<?php

/**
 * @name        JMY CMS
 * @link        http://jmy.su/
 * @copyright   Copyright (C) 2012-2021 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */

namespace Sync\Tasks;

use Sync\Traits\SyncTrait;
use Sync\Traits\SyncProgress;
use Boot\System\Core\Abstracts\Tasks\Task;

class OrganizationsTask extends Task
{
	use SyncTrait;
	use SyncProgress;

	protected $syncModule 		= 'organizations';
	protected $syncModuleModel 	= '\Organizations\Model\Organizations';
	protected $syncRestLink 	= 'Catalog_Контрагенты';

	protected $model = \Organizations\Model\Organizations::class;

	/**
	 * Обработчик синхронизации
	 *
	 * @param  mixed $method
	 * @return void
	 */
	public function syncAdapter($method = array('add', 'update'))
	{
		foreach ($this->syncDataComplete as $key => $value) {
			$this->progressStart(count($this->syncDataComplete[$key]), 'Синхронизация данных раздела организации');
			foreach ($this->syncDataComplete[$key] as $k => $v) {
				$legal = 'ЮридическоеЛицо';
				if ($v['data']['type'] == 'INDIVIDUAL') {
					$legal = 'ФизическоеЛицо';
				}
				$syncData = array(
					'Description' => htmlspecialchars_decode($v['data']['name']),
					'ИНН' => $v['data']['inn'],
					'КПП' =>  $v['data']['kpp'],
					'НаименованиеПолное' => htmlspecialchars_decode($v['data']['name']),
					'ЮридическоеФизическоеЛицо' => $legal,
				);
				$register = false;
				if ($this->isRegister) {
					$register = $v['register'];
				}
				if (($key == 'update') && in_array($key, $method)) {
					$this->progressSetMessage('Обновление организации: ' . $v['data']['name'] . ' (ИНН ' . $v['data']['inn'] . ') ' . ' {' . $v['pid'] . '}', 'message');
					$this->restUpdate($syncData, $v['relation'], $register);
				}
				if (($key == 'add') && in_array($key, $method)) {
					$this->progressSetMessage('Добавление организации: ' . $v['data']['name'] . ' (ИНН ' . $v['data']['inn'] . ') ' . ' {' . $v['pid'] . '}', 'message');
					$relation = $this->restAdd($syncData, $v['pid'], $register);
					$this->syncDataComplete[$key][$k]['relation'] = $relation;
				}
				$this->progressAdvance();
			}
			$this->progressFinish();
		}
	}

	public function customCheck()
	{
		$this->checkRestByInn();
	}

	public function checkRestByInn()
	{
		$data_check = array();
		$filter = array();
		$i = 0;
		$ii = 0;
		if (isset($this->syncDataComplete['add'])) {
			foreach ($this->syncDataComplete['add'] as $k => $v) {
				$i++;
				if ($i % 40 == 0) {
					$ii++;
				}
				$filter[$ii][] = "ИНН eq '" . $v['data']['inn'] . "'";
			}
			foreach ($filter as $k => $v) {
				$dataTemp = $this->Client->{$this->syncRestLink}->filter(implode(' or ', $filter[$k]))->get()->values();
				$data_check = array_merge($data_check, $dataTemp);
			}
			foreach ($this->syncDataComplete['add'] as $k => $v) {
				$dataKey = array_search($v['data']['inn'], array_column($data_check, 'ИНН'));
				if ($dataKey !== false) {
					$this->syncDataComplete['update'][$k]['register'] = $v['register'];
					$this->syncDataComplete['update'][$k]['pid'] = $v['pid'];
					$this->syncDataComplete['update'][$k]['data'] = $v['data'];
					$this->syncDataComplete['update'][$k]['relation'] = $data_check[$dataKey]['Ref_Key'];
					$this->setSyncRelation($v['pid'], 'organization', $data_check[$dataKey]['Ref_Key'], $this->syncBaseKey);
					unset($this->syncDataComplete['add'][$k]);
				}
			}
		}
	}
}
