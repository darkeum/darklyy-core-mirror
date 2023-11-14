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

class RealizationBillTask extends Task
{
	use SyncTrait;
	use SyncProgress;

	protected $model = \Realization\Model\Realization::class;

	protected $syncModule 		= 'realization_bill';
	protected $syncModuleTable 	= 'realization';
	protected $syncModuleModel 	= '\Realization\Model\Realization';
	protected $syncRestLink 	= 'Document_СчетФактураВыданный';	

	public function syncAdapter($method = ['add', 'update'])
	{
		foreach ($this->syncDataComplete as $key => $value) {
			$this->progressStart(count($this->syncDataComplete[$key]), 'Синхронизация данных раздела "Счета фактуры выданные"');
			foreach ($this->syncDataComplete[$key] as $k => $v) {
				$detailList = \Realization\Model\RealizationDetail::where("rid", $v['data']['id'])->get()->toArray();
				if (!empty($detailList)) {
					$cidList = array();
					foreach ($detailList as $dValue) {
						$cidList[] = $dValue['cid'];
					}
					$request = array();
					$request['catalog'] = $cidList;
					$syncSpecialData = $this->getSyncSpecial($request);
					$catalogList = $syncSpecialData['catalog'];
					$total = 0;
					$total_nds = 0;
					foreach ($detailList as $dValue) {
						$dataKey = array_search($dValue['cid'], array_column($catalogList, 'pid'));
						if ($dataKey !== false) {
							$dataPrice = array(
								'price' => $dValue['s_price'],
								'count' => $dValue['count'],
								'nds_type' => $dValue['nds_type'],
								'nds_includes' => $v['data']['nds_includes'],
								'discount' => $dValue['discount']
							);
							$pricePrepare = \Price::get_all($dataPrice);
							$total += $pricePrepare['total'];
							$total_nds += $pricePrepare['nds'];
						}
					}
				}
				$posted = ($v['data']['active'] == '1' ? 'true' : 'false');
				$request = array();
				$request['organizations'] = array($v['data']['oid']);
				$request['sales'] = array($v['data']['sales_id']);
				$request['realization'] = array($v['data']['id']);
				$reestr = $this->getReestr($v['data']['sales_id']);
				if ($reestr !== false) {
					$request['reestr'] = array($reestr);
				}
				$syncSpecialData = $this->getSyncSpecial($request);
				$currency = '1';
				$request = array();
				$request['org'] = $v['data']['org'];
				$request['catalog_currency'] = $currency;
				$request['users'] = $v['data']['uid'];
				$staticRelation = $this->getStaticRelation($request);
				$syncData = [
					'ВидСчетаФактуры' => 'НаРеализацию',
					'Posted' => $posted,
					'Number' => ' ' . $v['data']['numb'],
					'Date' => $this->prepareDate($v['data']['date']),
					'Организация_Key' => $staticRelation['org'][$v['data']['org']],
					'Контрагент_Key' =>  $syncSpecialData['organizations'][0]['relation'],
					'ДокументОснование' => $syncSpecialData['realization'][0]['relation'],
					'ДокументОснование_Type' => 'StandardODATA.Document_РеализацияТоваровУслуг',
					'КодВидаОперации' => '01',
					'ДатаВыставления' => $this->prepareDate($v['data']['date']),
					'КодСпособаВыставления' => '1',
					'Выставлен' => 'true',
					'СуммаДокумента' => $total,
					'СуммаНДСДокумента' => $total_nds,
					'ВалютаДокумента_Key' => $staticRelation['catalog_currency'][$currency],
					'ПредставлениеНомера' => ' ' . $v['data']['numb'],
					'ДокументыОснования' => array(array(
						'LineNumber' => '1',
						'ДокументОснование' => $syncSpecialData['realization'][0]['relation'],
						'ДокументОснование_Type' => 'StandardODATA.Document_РеализацияТоваровУслуг',
					)),
				];
				if (isset($staticRelation['users'][$v['data']['uid']])) {
					$syncData['Ответственный_Key'] = $staticRelation['users'][$v['data']['uid']];
				}
				if (isset($syncSpecialData['reestr'][0]['relation']) && !empty($syncSpecialData['reestr'][0]['relation'])) {
					$syncData['ДоговорКонтрагента_Key'] = $syncSpecialData['reestr'][0]['relation'];
				} else {
					$filter = "Owner_Key eq guid'" . $syncSpecialData['organizations'][0]['relation'] . "' and ВидДоговора eq 'СПокупателем'";
					$dataTemp = $this->Client->{'Catalog_ДоговорыКонтрагентов'}->filter($filter)->get()->values();
					if (!empty($dataTemp)) {
						$syncData['ДоговорКонтрагента_Key'] = $dataTemp[0]['Ref_Key'];
					}
				}
				$method = array('add', 'update');
				$register = false;
				if ($this->isRegister) {
					$register = $v['register'];
				}
				if (($key == 'update') && in_array($key, $method)) {
					$this->progressSetMessage('Обновление счета фактуры: ' . $v['data']['numb'] . ' от ' . $v['data']['date'] . ' ' . ' {' . $v['pid'] . '}', 'message');
					$this->restUpdate($syncData, $v['relation'], $register);
				}
				if (($key == 'add') && in_array($key, $method)) {
					$relation = $this->restAdd($syncData, $v['pid'], $register);
					$this->progressSetMessage('Добавление счета фактуры: ' . $v['data']['numb'] . ' от ' . $v['data']['date'] . ' ' . ' {' . $v['pid'] . '}', 'message');
					$this->syncDataComplete[$key][$k]['relation'] = $relation;
				}
				$this->progressAdvance();
			}
			$this->progressFinish();
		}
	}


}
