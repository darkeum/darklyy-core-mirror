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
use \Sync\Models\SyncRegister;

class SalesAlfaTask {

	use SyncTrait;
	use SyncProgress;

	protected $model = \Sales\Model\Sales::class;

	protected $syncModule 		= 'sales'; 
	protected $syncModuleTable 	= 'sales'; 
	protected $syncModuleModel 	= '\Realization\Model\Realization';
	protected $syncRestLink 	= 'Document_СчетНаОплатуПокупателю'; 


	public function syncAdapter($method = array('add', 'update')){

		foreach ($this->syncDataComplete as $key => $value) {
			foreach ($this->syncDataComplete[$key] as $k => $v) {	
				dump($v['data']['id']);
				$detailList = \Sales\Model\SalesDetail::where("sales_id", $v['data']['id'])->orderBy("position")->get()->toArray();
				if(!empty($detailList)) {
					$cidList = array();
					foreach ($detailList as $dValue) {
						$cidList[] = $dValue['cid'];
					}
					$syncMainClass = new \Sync\Sync;
					$adapterClass = $syncMainClass->getAdapterClassName('catalog');			
					if(class_exists($adapterClass)){						
						$adapter = new $adapterClass();
						$adapter->syncSpecial($cidList, $this->syncBaseKey);
						$catalogList = array();
						foreach ($adapter->syncDataComplete as $cValue) {
							$catalogList = array_merge($catalogList, $cValue);
						}
					}
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
								'nds_type' => $dValue['type_nds'],
								'nds_includes' => $v['data']['nds_includes'],
								'discount' => $dValue['discount']
							);
							// dump($dataPrice);
							$pricePrepare = \Price::get_all($dataPrice);
							$listArray[] = array(
								'LineNumber' => $i,					
								'Номенклатура_Key' => $catalogList[$dataKey]['relation'],							
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
				$posted = 'true';
				$nds_doc = 'false';	
				$nds_includes = 'false';		
				if($orgInfo['org']['nds'] == '0') {
					$nds_doc = 'true';				
				} 
				if($v['data']['nds_includes'] == '1')	{
					$nds_includes = 'true';				
				}
				$adapterClass = $syncMainClass->getAdapterClassName('organizations');			
				if(class_exists($adapterClass)){						
					$adapter = new $adapterClass();
					$adapter->syncSpecial(array($v['data']['oid']), $this->syncBaseKey);
					$listOid = array();
					foreach ($adapter->syncDataComplete as $cValue) {
						$listOid = array_merge($listOid, $cValue);
					}
				}
				$reestr = $this->getReestr($v['data']['id']);
				if($reestr !== false) {
					$adapterClass = $syncMainClass->getAdapterClassName('reestr');			
					if(class_exists($adapterClass)){						
						$adapter = new $adapterClass();
						$adapter->syncSpecial(array($reestr), $this->syncBaseKey);
						$listReestr = array();
						foreach ($adapter->syncDataComplete as $cValue) {
							$listReestr = array_merge($listReestr, $cValue);
						}
					}
				}
				
				$currency = '1';
				$request = array();
				$request['org'] = $v['data']['org'];
				$request['catalog_currency'] = $currency;
				$request['users'] = $v['data']['uid'];
				$staticRelation = $this->getStaticRelation($request);
				if(!isset($staticRelation['org'][$v['data']['org']])){
					SyncRegister::find($v['register'])->update(['status' => 3]);
					// unset($this->syncData[$k]);
					continue;
				}
				$syncData = [				
					'Posted' => $posted,
					'Number' => $v['data']['numb'],
					'Date' => $this->prepareDate($v['data']['date']),
					'Организация_Key' => $staticRelation['org'][$v['data']['org']],
					'АдресДоставки' => $listOid[0]['data']['yaddress'],
					'СуммаДокумента' => $total,
					'Контрагент_Key' => $listOid[0]['relation'],					
					'КратностьВзаиморасчетов' => 'true',
					'КурсВзаиморасчетов' => 'true',
					'ВалютаДокумента_Key' => $staticRelation['catalog_currency'][$currency],
					'СуммаВключаетНДС' => $nds_includes,
					'ДокументБезНДС' => $nds_doc,
					'Товары' => $listArray,
				];
				if(isset($staticRelation['users'][$v['data']['uid']])){
					$syncData['Ответственный_Key'] = $staticRelation['users'][$v['data']['uid']];
				}
				if(isset($listReestr[0]['relation']) && !empty($listReestr[0]['relation'])){
					$syncData['ДоговорКонтрагента_Key'] = $listReestr[0]['relation'];					
				}
				$method = array('add', 'update');
				$register = false;
				if($this->isRegister) {
					$register = $v['register'];
				}
				if(($key == 'update') && in_array($key, $method)) {
					$this->restUpdate($syncData, $v['relation'], $register);	
				} 
				if(($key == 'add') && in_array($key, $method)) {
					// $relation = $this->restAdd($syncData, $v['pid'], $register);
					// $this->syncDataComplete[$key][$k]['relation'] = $relation;
				}
			}
		}
	}

	
}
	
?>