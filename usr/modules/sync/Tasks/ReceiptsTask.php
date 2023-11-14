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


class ReceiptsTask extends Task
{

	use SyncTrait;
	use SyncProgress;

	protected $model = \Supply\Model\Receipts::class;

	protected $syncModule 		= 'receipts';
	protected $syncModuleTable 	= 'receipts';
	protected $syncRestLink 	= 'Document_ПоступлениеТоваровУслуг';

	public function syncAdapter($method = ['add', 'update'])
	{

		foreach ($this->syncDataComplete as $key => $value) {
			$this->progressStart(count($this->syncDataComplete[$key]), 'Синхронизация данных раздела "Поступления"');
			foreach ($this->syncDataComplete[$key] as $k => $v) {
				$detailList = \Supply\Model\ReceiptsDetail::where("rid", $v['data']['id'])->get()->toArray();
				if(strtotime($v['data']['date']) < strtotime('2023-07-01 00:00:00')){
					SyncRegister::find($v['register'])->update(['status' => 3]);
					$this->console->error('Элемент с регистрационным номером ' . $k . ' устарел.');
					continue;
				}
				$chart = $this->getChart(['41.01', '19.03', '60.01', '60.02']);
				if (!empty($detailList)) {
					$cidList = array();
					foreach ($detailList as $dValue) {
						$cidList[] = $dValue['cid'];
					}
					$request = array();
					$request['catalog'] = $cidList;
					$syncSpecialData = $this->getSyncSpecial($request);
					$catalogList = $syncSpecialData['catalog'];
					$listArray = array();
					$i = 0;
					$total = 0;					
					foreach ($detailList as $dValue) {
						$dataKey = array_search($dValue['cid'], array_column($catalogList, 'pid'));
						if ($dataKey !== false) {
							$i++;
							$dataPrice = array(
								'price' => $dValue['price'],
								'count' => $dValue['count'],
								'nds_type' => $dValue['type_nds'],
								'nds_includes' => $v['data']['nds_includes']
							);
							$request['unit'] = $dValue['unit'];
							$staticRelation = $this->getStaticRelation($request);
							$nds_summa = $dValue['nds'];
							if ($dValue['type_nds'] == '0') {
								$nds_summa = '0';
							}

							$pricePrepare = \Price::get_all($dataPrice);
							$listArray[] = array(
								'LineNumber' => $i,
								'Номенклатура_Key' => $catalogList[$dataKey]['relation'],
								'КоличествоМест' => '0',
								'ЕдиницаИзмерения_Key' => $staticRelation['unit'][$dValue['unit']],
								'Коэффициент' => '1',
								'Количество' => $dValue['count'],
								'Цена' => $dValue['price'],
								'Сумма' => $dValue['total'],
								'СтавкаНДС' => $pricePrepare['nds_1C'],
								'СуммаНДС' => $nds_summa,
								'СчетУчета_Key' => $chart['41.01'],
								'СчетУчетаНДС_Key' => $chart['19.03'],

							);
							$total += $dValue['total'];
						}
					}
				} else {
					// dd($this->syncDataComplete);
					SyncRegister::find($v['register'])->update(['status' => 3]);
					$this->console->error('Не возможно выгрузить пустое поступление ' . $k);
					continue;
				}
				$posted = ($v['data']['active'] == '1' ? 'true' : 'false');
				$nds_includes = 'false';
				if ($v['data']['nds_includes'] == '1') {
					$nds_includes = 'true';
				}

				$request = array();
				$request['organizations'] = array($v['data']['oid']);
				$syncSpecialData = $this->getSyncSpecial($request);

				$currency = '1';
				$request = array();
				$request['org'] = $v['data']['org'];
				$request['catalog_currency'] = $currency;
				$request['stock'] = $v['data']['stock_id'];
				$request['users'] = $v['data']['uid'];
				$staticRelation = $this->getStaticRelation($request);

				if (!isset($staticRelation['org'][$v['data']['org']])) {
					SyncRegister::find($v['register'])->update(['status' => 3]);
					$this->console->error('Элемент с регистрационным номером ' . $k . ' не принадлежит данной организации.');
					continue;
				}

				$syncData = [
					'Number' => $v['data']['numb'],
					'Date' =>  $this->prepareDate($v['data']['date']),
					'Posted' => $posted,
					'ВидОперации' => 'Товары',
					'Организация_Key' => $staticRelation['org'][$v['data']['org']],
					'Склад_Key' => $staticRelation['stock'][$v['data']['stock_id']],
					'Контрагент_Key' => $syncSpecialData['organizations'][0]['relation'],
					'СпособЗачетаАвансов' => 'Автоматически',
					'СчетУчетаРасчетовСКонтрагентом_Key' => $chart['60.01'],
					'СчетУчетаРасчетовПоАвансам_Key' => $chart['60.02'],
					'НомерВходящегоДокумента' => $v['data']['doc_number'],
					'ДатаВходящегоДокумента' => $this->prepareDate($v['data']['date_receipts']),
					'КратностьВзаиморасчетов' => 'true',
					'КурсВзаиморасчетов' => 'true',
					'СуммаВключаетНДС' => $nds_includes,
					'СуммаДокумента' => $total,
					'Товары' => $listArray,
				];

				if (isset($staticRelation['users'][$v['data']['uid']])) {
					$syncData['Ответственный_Key'] = $staticRelation['users'][$v['data']['uid']];
				}
				if (isset($syncSpecialData['reestr'][0]['relation']) && !empty($syncSpecialData['reestr'][0]['relation'])) {
					$syncData['ДоговорКонтрагента_Key'] = $syncSpecialData['reestr'][0]['relation'];
				} else {
					$filter = "Owner_Key eq guid'" . $syncSpecialData['organizations'][0]['relation'] . "' and ВидДоговора eq 'СПоставщиком'";
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
					$this->progressSetMessage('Обновление поступления: ' . $v['data']['numb'] . ' от ' . $v['data']['date'] . ' ' . ' {' . $v['pid'] . '}', 'message');
					$this->restUpdate($syncData, $v['relation'], $register);
				}
				if (($key == 'add') && in_array($key, $method)) {
					$this->progressSetMessage('Добавления поступления: ' . $v['data']['numb'] . ' от ' . $v['data']['date'] . ' ' . ' {' . $v['pid'] . '}', 'message');
					$relation = $this->restAdd($syncData, $v['pid'], $register);
					$this->syncDataComplete[$key][$k]['relation'] = $relation;
				}

				$sync = new \Sync\Sync();
				$sync->syncRegisterByBaseKey('receipts_bill', $v['data']['id'], '1', $this->base->id);
				$this->progressAdvance();
			}
			$this->progressFinish();
		}
	}
}
