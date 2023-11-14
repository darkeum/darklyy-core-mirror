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
use Sync\Models\SyncRegister;
use Sync\Traits\SyncProgress;
use Boot\System\Core\Abstracts\Tasks\Task;

class ReestrTask extends Task
{

	use SyncTrait;
	use SyncProgress;

	protected $model = \Reestr\Models\Reestr::class;

	protected $syncModule 		= 'reestr';
	protected $syncModuleModel 	= '\Reestr\Models\Reestr';
	protected $syncRestLink 	= 'Catalog_ДоговорыКонтрагентов';


	//обработчик
	public function syncAdapter($method = array('add', 'update'))
	{

		foreach ($this->syncDataComplete as $key => $value) {
			$this->progressStart(count($this->syncDataComplete[$key]), 'Синхронизация данных раздела договоров');
			foreach ($this->syncDataComplete[$key] as $k => $v) {
				$numb = $this->syncModuleModel::getReestrNumb($v['data']['type'], $v['data']['numb']);
				if (!empty($v['data']['other_numb'])) {
					$numb = $v['data']['other_numb'];
				}
				$goodDate = date('d.m.Y', strtotime($v['data']['date_view']));
				$description = 'Договор №' . $numb . ' от ' . $goodDate;
				$date = $this->prepareDate($v['data']['date_view']);

				$request['organizations'] = [$v['data']['oid']];
				$listOid = $this->getSyncSpecial($request);
				$currency = '1';
				$request = array();
				$request['org'] = $v['data']['org'];
				$request['catalog_currency'] = $currency;
				$request['users'] = $v['data']['uid'];
				$staticRelation = $this->getStaticRelation($request);
				$typeDoc = 'СПокупателем';
				if ($v['data']['type_doc'] == 'in') {
					$typeDoc = 'СПоставщиком';
				}
				if (!isset($staticRelation['org'][$v['data']['org']])) {
					SyncRegister::find($v['register'])->update(['status' => 3]);
                    $this->console->error('Элемент с регистрационным номером ' . $k . ' не принадлежит данной организации.');
					continue;
				}
				$syncData = array(
					'Description' => $description,
					'Owner_Key' => $listOid['organizations'][0]['relation'],
					'ВалютаДокумента_Key' => $staticRelation['catalog_currency'][$currency],
					'Организация_Key' => $staticRelation['org'][$v['data']['org']],
					'ВидДоговора' => $typeDoc,
					'Дата' => $date,
					'Номер' => $numb,
				);
				$register = false;
				if ($this->isRegister) {
					$register = $v['register'];
				}
				if (($key == 'update') && in_array($key, $method)) {
					$this->progressSetMessage('Обновление договора: ' . $numb . ' от ' . $v['data']['date_view'] . ' {' . $v['pid'] . '}', 'message');
					$this->restUpdate($syncData, $v['relation'], $register);
				}
				if (($key == 'add') && in_array($key, $method)) {
					$this->progressSetMessage('Добавление договора: ' . $numb . ' от ' . $v['data']['date_view'] . ' {' . $v['pid'] . '}', 'message');
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
		$this->checkRelationRestReestr();
	}


	//поиск договоров по базе
	public function checkRelationRestReestr()
	{
		$filter = array();
		$reestr = new $this->syncModuleModel;
		if (isset($this->syncDataComplete['add'])) {
			$cidList = array();
			foreach ($this->syncDataComplete['add'] as $dValue) {
				$cidList[] = $dValue['data']['oid'];
			}

			$request['organizations'] = $cidList;
			$catalogList = $this->getSyncSpecial($request);


			foreach ($this->syncDataComplete['add'] as $k => $v) {
				$dataKey = array_search($v['data']['oid'], array_column($catalogList, 'pid'));
				if ($dataKey !== false) {
					$this->syncDataComplete['add'][$k]['data']['oid_relation'] = $catalogList[$dataKey]['relation'];
				}
			}
			foreach ($this->syncDataComplete['add'] as $k => $v) {
				if (isset($v['data']['oid_relation'])) {
					$numb = $reestr->getReestrNumb($v['data']['type'], $v['data']['numb']);
					if (!empty($v['data']['other_numb'])) {
						$numb = $v['data']['other_numb'];
					}
					$date = $this->prepareDate($v['data']['date_view']);
					$relation = $v['data']['oid_relation'];

					$filter = "Номер eq '" . $numb . "' and Дата eq datetime'" . $date . "' and Owner_Key eq guid'" . $relation . "'";
					$dataTemp = $this->Client->{$this->syncRestLink}->filter($filter)->get()->values();
					if (!empty($dataTemp)) {
						$ref_key = $dataTemp[0]['Ref_Key'];
						$this->setSyncRelation($v['data']['id'], $this->syncModule, $ref_key, $this->syncBaseKey);
						$result['update'][$k]['register'] = $v['register'];
						$result['update'][$k]['pid'] = $v['pid'];
						$result['update'][$k]['data'] = $v['data'];
						$result['update'][$k]['relation'] = $ref_key;
						unset($this->syncDataComplete['add'][$k]);
					}
				}
			}
		}
	}
}
