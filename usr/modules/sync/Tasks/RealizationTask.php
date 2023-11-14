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


class RealizationTask extends Task{

	use SyncTrait;
	use SyncProgress;

	protected $model = \Realization\Model\Realization::class; 

	protected $syncModule 		= 'realization'; 
	protected $syncModuleTable 	= 'realization'; 
	protected $syncModuleModel 	= '\Realization\Model\Realization'; 	
	protected $syncRestLink 	= 'Document_РеализацияТоваровУслуг'; 

	public function syncAdapter($method = ['add', 'update']){
		foreach ($this->syncDataComplete as $key => $value) {
			$this->progressStart(count($this->syncDataComplete[$key]), 'Синхронизация данных раздела "Реализации"');
			foreach ($this->syncDataComplete[$key] as $k => $v) {	
				$detailList = \Realization\Model\RealizationDetail::where("rid", $v['data']['id'])->get()->toArray();
				$chart = $this->getChart(['41.01', '90.01.1', '90.03', '90.02.1']);
				if(strtotime($v['data']['date']) < strtotime('2023-07-01 00:00:00')){
					SyncRegister::find($v['register'])->update(['status' => 3]);
					$this->console->error('Элемент с регистрационным номером ' . $k . ' устарел.');
					continue;
				}
				if(!empty($detailList)) {
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
						if($dataKey !== false) {
							$i++;
							$dataPrice = array(
								'price' => $dValue['s_price'],
								'count' => $dValue['count'],
								'nds_type' => $dValue['nds_type'],
								'nds_includes' => $v['data']['nds_includes'],
								'discount' => $dValue['discount']
							);
							$request['unit'] = $dValue['s_unit'];
							$request['catalog_subconto'] = '1';
							$staticRelation = $this->getStaticRelation($request);
							$pricePrepare = \Price::get_all($dataPrice);
							$listArray[] = array(
								'LineNumber' => $i,					
								'Номенклатура_Key' => $catalogList[$dataKey]['relation'],	
								'КоличествоМест' => '0',
								'ЕдиницаИзмерения_Key' => $staticRelation['unit'][$dValue['s_unit']],	
								'Коэффициент' => '1',	
								'СчетУчета_Key' => $chart['41.01'],
								'СчетДоходов_Key' => $chart['90.01.1'],
								'Субконто' => $staticRelation['catalog_subconto']['1'],
								'Субконто_Type' => 'StandardODATA.Catalog_НоменклатурныеГруппы',
								'СчетУчетаНДСПоРеализации_Key' => $chart['90.03'],
								'СчетРасходов_Key' => $chart['90.02.1'],
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
				if($orgInfo['org']['nds'] == '0') {
					$nds_doc = 'true';				
				} 

				$request = array();
				$request['organizations'] = array($v['data']['oid']);
				$request['sales'] = array($v['data']['sales_id']);
				$reestr = $this->getReestr($v['data']['sales_id']);
				if($reestr !== false) {
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
					'Date' =>$this->prepareDate($v['data']['date']),
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

				if(isset($staticRelation['users'][$v['data']['uid']])){
					$syncData['Ответственный_Key'] = $staticRelation['users'][$v['data']['uid']];
				}
				if(isset($syncSpecialData['reestr'][0]['relation']) && !empty($syncSpecialData['reestr'][0]['relation'])){
					$syncData['ДоговорКонтрагента_Key'] = $syncSpecialData['reestr'][0]['relation'];					
				} else {
					$filter = "Owner_Key eq guid'".$syncSpecialData['organizations'][0]['relation']."' and ВидДоговора eq 'СПокупателем'";
					// dump($filter);
					$dataTemp = $this->Client->{'Catalog_ДоговорыКонтрагентов'}->filter($filter)->get()->values();
					if(!empty($dataTemp)){
						$syncData['ДоговорКонтрагента_Key'] = $dataTemp[0]['Ref_Key'];		
					}
				}
				if(isset($syncSpecialData['sales'][0]['relation']) && !empty($syncSpecialData['sales'][0]['relation'])){
					$syncData['СчетНаОплатуПокупателю_Key'] = $syncSpecialData['sales'][0]['relation'];					
				}

				$method = array('add', 'update');
				$register = false;
				if($this->isRegister) {
					$register = $v['register'];
				}
				if(($key == 'update') && in_array($key, $method)) {
					$this->progressSetMessage('Обновление реализации: ' . $v['data']['numb'] . ' от ' . $v['data']['date'] . ' ' . ' {' . $v['pid'] . '}', 'message');
					$this->restUpdate($syncData, $v['relation'], $register);	
				} 
				if(($key == 'add') && in_array($key, $method)) {
					$this->progressSetMessage('Добавление реализации: ' . $v['data']['numb'] . ' от ' . $v['data']['date'] . ' ' . ' {' . $v['pid'] . '}', 'message');
					$relation = $this->restAdd($syncData, $v['pid'], $register);
					$this->syncDataComplete[$key][$k]['relation'] = $relation;
				}
				if($orgInfo['org']['taxation'] != '2')	{
					$sync = new \Sync\Sync();				
					$sync->syncRegisterByBaseKey('realization_bill',$v['data']['id'], '1', $this->base->id);
				}	
				$this->progressAdvance();		
			}
			$this->progressFinish();
			
		}
	}

	public function setRelationForBroken($syncData, $pid){
		$filter[] = "Number eq '" . $syncData['Number'] . "'";
		$filter[] = "Date eq '" . $syncData['Date'] . "'";
		$data = $this->Client->{$this->syncRestLink}->filter(implode(' and ', $filter))->get()->values();
		$this->setSyncRelation($pid, $this->syncModule, $data['ref_key'], $this->base->id);
	}



	
}
