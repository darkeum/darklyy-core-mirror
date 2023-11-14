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

use Sync\Models\SyncBase;
use Sync\Traits\SyncTrait;
use Sync\Traits\SyncProgress;
use Sync\UI\CLI\Commands\SyncCommand;
use Boot\System\Core\Abstracts\Tasks\Task;

class TransferTask extends Task {

	use SyncTrait;
	use SyncProgress;

	protected $model = \Storage\Model\StockTransfer::class; 

	protected $syncModule 		= 'stock_transfer'; 
	protected $syncModuleTable 	= 'stock_transfer'; 
	protected $syncRestLink 	= 'Document_ПеремещениеТоваров'; 

	public function syncAdapter($method = array('add', 'update')){
	//	dd($this->syncDataComplete);
		foreach ($this->syncDataComplete as $key => $value) {
			$this->progressStart(count($this->syncDataComplete[$key]), 'Синхронизация данных раздела "Перемещения"');
			foreach ($this->syncDataComplete[$key] as $k => $v) {	
				$detailList = \Storage\Model\StockTransferDetail::where("transfer_id", $v['data']['id'])->get()->toArray();
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
					
					foreach ($detailList as $dValue) {						
						$dataKey = array_search($dValue['cid'], array_column($catalogList, 'pid'));
						if($dataKey !== false) {
							$i++;						
							$request['unit'] = $dValue['s_unit'];
							$request['chart'] = array('223', '304');
							$staticRelation = $this->getStaticRelation($request);
							$listArray[] = array(
								'LineNumber' => $i,					
								'Номенклатура_Key' =>  $catalogList[$dataKey]['relation'],	
								'КоличествоМест' => '0',
								'ЕдиницаИзмерения_Key' => $staticRelation['unit'][$dValue['s_unit']],
								'Коэффициент' => '1',
								'Количество' => $dValue['count'],					
								'СчетУчета_Key' => $staticRelation['chart']['223'],
								'НовыйСчетУчета_Key' => $staticRelation['chart']['304'],
								
							);
						}
					}
				}

				$posted = ($v['data']['active'] == '1' ? 'true' : 'false');
				$currency = '1';
				$request = array();
				$request['org'] = $v['data']['org'];
				$request['catalog_currency'] = $currency;
				$request['stock'] = array($v['data']['stock_in'], $v['data']['stock_out']);
				$request['users'] = $v['data']['uid'];
				$staticRelation = $this->getStaticRelation($request);

				$object_name = '';	
				if(!empty($v['data']['object'])) {
					$objList = \Object\Model\ObjectModel::find($v['data']['object']);
					if(!empty($objList)) {
						$object_name = ee($objList->name);	
					}				
				}

				$syncData = [	
					'Number' => $v['data']['numb'],
					'Date' => $this->prepareDate($v['data']['date']),
					'Posted' => $posted,
					'ВидОперации' => 'ПередачаМеждуСкладами',					
					'Организация_Key' =>  $staticRelation['org'][$v['data']['org']],
					'СкладОтправитель_Key' =>$staticRelation['stock'][$v['data']['stock_out']],
					'СкладПолучатель_Key' => $staticRelation['stock'][$v['data']['stock_in']],						
					'Комментарий' => $v['data']['comment'].$object_name,					
					'Товары' => $listArray,
				];

				if(isset($staticRelation['users'][$v['data']['uid']])){
					$syncData['Ответственный_Key'] = $staticRelation['users'][$v['data']['uid']];
				}							
				$method = array('add', 'update');
				$register = false;
				if($this->isRegister) {
					$register = $v['register'];
				}
				if(($key == 'update') && in_array($key, $method)) {
					$this->progressSetMessage('Обновление перемещения: ' . $v['data']['numb'] . ' от ' . $v['data']['date'] . ' ' . ' {' . $v['pid'] . '}', 'message');
					$this->restUpdate($syncData, $v['relation'], $register);	
				} 
				if(($key == 'add') && in_array($key, $method)) {
					$this->progressSetMessage('Добавления перемещения: ' . $v['data']['numb'] . ' от ' . $v['data']['date'] . ' ' . ' {' . $v['pid'] . '}', 'message');
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