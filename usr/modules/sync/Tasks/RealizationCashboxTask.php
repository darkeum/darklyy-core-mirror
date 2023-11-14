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
use Cashbox\Model\CashboxChecks;

class RealizationCashboxTask extends Task
{

	use SyncTrait;
	use SyncProgress;

	protected $model = \Realization\Model\Realization::class;

	protected $syncModule 		= 'realization';
	protected $syncModuleTable 	= 'realization';
	protected $syncModuleModel 	= '\Realization\Model\Realization';
	protected $syncRestLink 	= 'Document_ОтчетОРозничныхПродажах';

	public function syncAdapter($method = ['add', 'update'])
	{
		foreach ($this->syncDataComplete as $key => $value) {
			$this->progressStart(count($this->syncDataComplete[$key]), 'Синхронизация данных раздела "Отчет о розничных продажах"');
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
					$listArray = array();
					$i = 0;
					$total = 0;
					foreach ($detailList as $dValue) {
						$dataKey = array_search($dValue['cid'], array_column($catalogList, 'pid'));
						if ($dataKey !== false) {
							$i++;
							$dataPrice = array(
								'price' => $dValue['s_price'],
								'count' => $dValue['count'],
								'nds_type' => $dValue['nds_type'],
								'nds_includes' => $v['data']['nds_includes'],
								'discount' => $dValue['discount']
							);
							$request['unit'] = $dValue['s_unit'];
							$request['chart'] = array('223', '55', '348', '360');
							$request['catalog_subconto'] = '2';
							$staticRelation = $this->getStaticRelation($request);
							$pricePrepare = \Price::get_all($dataPrice);
							$listArray[] = array(
								'LineNumber' => $i,
								'Номенклатура_Key' => $catalogList[$dataKey]['relation'],
								'КоличествоМест' => '0',
								'ЕдиницаИзмерения_Key' => $staticRelation['unit'][$dValue['s_unit']],
								'Коэффициент' => '1',
								'СчетУчета_Key' => $staticRelation['chart']['223'],
								'СчетДоходов_Key' => $staticRelation['chart']['55'],
								'Субконто' => $staticRelation['catalog_subconto']['2'],
								'Субконто_Type' => 'StandardODATA.Catalog_НоменклатурныеГруппы',
								'СчетУчетаНДСПоРеализации_Key' => $staticRelation['chart']['348'],
								'СчетРасходов_Key' => $staticRelation['chart']['360'],
								'Количество' => $dValue['count'],
								'Цена' => $pricePrepare['discount_price'],
								'Сумма' => $pricePrepare['total'],
								'СтавкаНДС' => $pricePrepare['nds_1C'],
								'СуммаНДС' => $pricePrepare['nds'],

							);
							$total += $pricePrepare['total'];
						}
					}
				}
				$orgInfo = getOrgInfo($v['data']['org']);
				$posted = ($v['data']['active'] == '1' ? 'true' : 'false');
				$nds_doc = 'false';
				if ($orgInfo['org']['nds'] == '0') {
					$nds_doc = 'true';
				}

				$request = array();
				$request['organizations'] = array($v['data']['oid']);
				$request['sales'] = array($v['data']['sales_id']);
				$reestr = $this->getReestr($v['data']['sales_id']);
				if ($reestr !== false) {
					$request['reestr'] = array($reestr);
				}
				$syncSpecialData = $this->getSyncSpecial($request);

				$currency = '1';
				$request = array();
				$request['org'] = $v['data']['org'];
				$request['catalog_currency'] = $currency;
				$request['stock'] = $v['data']['stock_id'];
				$request['users'] = $v['data']['uid'];
				$staticRelation = $this->getStaticRelation($request);

				$syncData = [
					'Posted' => $posted,
					'Склад_Key' => $staticRelation['stock'][$v['data']['stock_id']],
					'Number' => $v['data']['numb'],
					'Date' => $this->prepareDate($v['data']['date']),
					'ВидОперации' => 'ОтчетККМОПродажах',
					'Организация_Key' => $staticRelation['org'][$v['data']['org']],
					'АдресДоставки' => $syncSpecialData['organizations'][0]['data']['yaddress'],
					'СпособЗачетаАвансов' => 'Автоматически',
					'СуммаДокумента' => $total,
					'Контрагент_Key' => $syncSpecialData['organizations'][0]['relation'],
					'КратностьВзаиморасчетов' => 'true',
					'КурсВзаиморасчетов' => 'true',
					'ВалютаДокумента_Key' => $staticRelation['catalog_currency'][$currency],
					'СуммаВключаетНДС' => 'true',
					'ДокументБезНДС' => $nds_doc,
					// 'ВидЭлектронногоДокумента' => 'ТОРГ12Продавец',
					'Товары' => $listArray,
				];


				$CashboxChecks = \Cashbox\Model\CashboxChecks::where("realization_id", $v['data']['id'])->where("TypeCheck", 0)->orderByDesc("id")->first();

				if (!empty($CashboxChecks)) {
					$payArray = [];
					$payArray[] = [
						'LineNumber' => 1,
						'ВидОплаты_Key' => '0aecaf7e-ff25-11eb-2c81-704d7b330583',
						'СуммаОплаты' => $CashboxChecks->data->ElectronicPayment,

					];
					$syncData['Оплата'] = $payArray;
				}



				if (isset($staticRelation['users'][$v['data']['uid']])) {
					$syncData['Ответственный_Key'] = $staticRelation['users'][$v['data']['uid']];
				}



				$method = array('add', 'update');
				$register = false;
				if ($this->isRegister) {
					$register = $v['register'];
				}
				if (($key == 'update') && in_array($key, $method)) {
					$this->progressSetMessage('Обновление "Отчет о розничных продажах": ' . $v['data']['numb'] . ' от ' . $v['data']['date'] . ' ' . ' {' . $v['pid'] . '}', 'message');
					$this->restUpdate($syncData, $v['relation'], $register);
				}
				if (($key == 'add') && in_array($key, $method)) {
					$this->progressSetMessage('Добавление "Отчет о розничных продажах": ' . $v['data']['numb'] . ' от ' . $v['data']['date'] . ' ' . ' {' . $v['pid'] . '}', 'message');
					$relation = $this->restAdd($syncData, $v['pid'], $register);
					$this->syncDataComplete[$key][$k]['relation'] = $relation;
				}
				$this->progressAdvance();
			}
			$this->progressFinish();
		}
	}
}
