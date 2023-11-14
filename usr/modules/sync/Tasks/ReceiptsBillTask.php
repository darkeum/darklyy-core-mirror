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


class ReceiptsBillTask extends Task{

	use SyncTrait;
	use SyncProgress;

	protected $model = \Supply\Model\Receipts::class; 

	protected $syncModule 		= 'receipts_bill'; 
	protected $syncModuleTable 	= 'receipts'; 
	protected $syncRestLink 	= 'Document_СчетФактураПолученный'; 

	public function syncAdapter($method = array('add', 'update')){
		foreach ($this->syncDataComplete as $key => $value) {
			$this->progressStart(count($this->syncDataComplete[$key]), 'Синхронизация данных раздела "Счет фактура полученный"');
			foreach ($this->syncDataComplete[$key] as $k => $v) {	
				$detailList = \Supply\Model\ReceiptsDetail::where("rid", $v['data']['id'])->get()->toArray();
				if(!empty($detailList)) {
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
						if($dataKey !== false) {							
							$nds_summa = $dValue['nds'];		
							if($dValue['type_nds'] == '0') {
								$nds_summa = '0';				
							}												
							$total += $dValue['total'];
							$total_nds += $nds_summa;
						}
					}
				}				
				$posted = ($v['data']['active'] == '1' ? 'true' : 'false');							
				$request = array();
				$request['organizations'] = array($v['data']['oid']);			
				$request['receipts'] = array($v['data']['id']);
				// $reestr = $this->getReestr($v['data']['sales_id']);
				// if($reestr !== false) {
				// 	$request['reestr'] = array($reestr);	
				// }
				$syncSpecialData = $this->getSyncSpecial($request);	
				$currency = '1';
				$request = array();
				$request['org'] = $v['data']['org'];
				$request['catalog_currency'] = $currency;
				$request['users'] = $v['data']['uid'];
				$staticRelation = $this->getStaticRelation($request);
				$syncData = [	
					'ВидСчетаФактуры' => 'НаПоступление',	
					'Number' => ' '.$v['data']['numb'],
					'Date' => $this->prepareDate($v['data']['date']),
					'Posted' => $posted,
					'Организация_Key' => $staticRelation['org'][$v['data']['org']],					
					'Контрагент_Key' => $syncSpecialData['organizations'][0]['relation'],	
					'НомерВходящегоДокумента' =>  $v['data']['doc_number'],
					'ДатаВходящегоДокумента' =>  $this->prepareDate($v['data']['date_receipts']),							
					'ДокументОснование' => $syncSpecialData['receipts'][0]['relation'],	
					'ДокументОснование_Type' => 'StandardODATA.Document_ПоступлениеТоваровУслуг',
					'КодВидаОперации' => '01',
					'КодСпособаПолучения' => '1',
					'СуммаДокумента' => $total,
					'СуммаНДСДокумента' => $total_nds,					
					'ВалютаДокумента_Key' => $staticRelation['catalog_currency'][$currency],
					'ПредставлениеНомера' => $v['data']['doc_number'],
					'ДокументыОснования' => array(array(
						'LineNumber' => '1',				
						'ДокументОснование' => $syncSpecialData['receipts'][0]['relation'],
						'ДокументОснование_Type' => 'StandardODATA.Document_ПоступлениеТоваровУслуг',
					)),

				];
				if(isset($staticRelation['users'][$v['data']['uid']])){
					$syncData['Ответственный_Key'] = $staticRelation['users'][$v['data']['uid']];
				}
				if(isset($syncSpecialData['reestr'][0]['relation']) && !empty($syncSpecialData['reestr'][0]['relation'])){
					$syncData['ДоговорКонтрагента_Key'] = $syncSpecialData['reestr'][0]['relation'];					
				} else {
					$filter = "Owner_Key eq guid'".$syncSpecialData['organizations'][0]['relation']."' and ВидДоговора eq 'СПоставщиком'";
					$dataTemp = $this->Client->{'Catalog_ДоговорыКонтрагентов'}->filter($filter)->get()->values();
					if(!empty($dataTemp)){
						$syncData['ДоговорКонтрагента_Key'] = $dataTemp[0]['Ref_Key'];		
					}
				}
				$method = array('add', 'update');
				$register = false;
				if($this->isRegister) {
					$register = $v['register'];
				}
				if(($key == 'update') && in_array($key, $method)) {
					$this->progressSetMessage('Обновление счета фактуры полученный: ' . $v['data']['numb'] . ' от ' . $v['data']['date'] . ' ' . ' {' . $v['pid'] . '}', 'message');
					$this->restUpdate($syncData, $v['relation'], $register);	
				} 
				if(($key == 'add') && in_array($key, $method)) {
					$this->progressSetMessage('Добавление счета фактуры полученный: ' . $v['data']['numb'] . ' от ' . $v['data']['date'] . ' ' . ' {' . $v['pid'] . '}', 'message');
					$relation = $this->restAdd($syncData, $v['pid'], $register);
					$this->syncDataComplete[$key][$k]['relation'] = $relation;
				}
				$this->progressAdvance();					
			}
			$this->progressFinish();
		}
	}	
}
	
?>