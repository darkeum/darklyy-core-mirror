<?php

/**
* @name        JMY CORE
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2020 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Realization;

class RealizationPrint{
	
	//данные
	public $realizationId = null;
	public $realizationOrg = array();
	public $realizationData = array();
	public $realizationDataDetail = array();
	
	
	//конструктор класса
	function __construct($request){		
		$this->realizationId = $request['realization_id'];
	}
	
	//проверка реализации на наличии и проведенность
    public function checkRealization(){
		global $core;		
        $rt = \Realization\Model\Realization::find($this->realizationId)->toArray();
		if(!empty($rt)){
			if($rt[0]['active'] == '1'){
				$return['check'] = true;
				$return['data']['text'] = 'Реализация найдена и проведена, печать разрешена!'; 
			}
			else{
				if($core->auth->allowAccessById($core->auth->user_id, '18')){
					$return['check'] = true;
					$return['data']['text'] = 'Реализация найдена, но не проведена, печать разрешена - '.$core->auth->user_info['short_user_name'].' ('.$core->auth->user_id.'), согласно правам доступа!'; 
				}
				else{
					$return['check'] = false;
					$return['data']['text'] = 'Реализация найдена, но не проведена, печать запрещена!'; 
				}
			}
		}
		else{
			$return['check'] = false;
			$return['data']['text'] = 'Указанная реализация не найдена!'; 
		}
		return $return;
    }
	
	//получение информация по реализации
	public function getData(){
		$returnData = array();	
		$request = array();
		$request['realization_id'] = $this->realizationId;
        $rData = \Realization\Model\Realization::find($this->realizationId)->toArray();	
		$returnData['main'] = $rData;
		$rt = new \Realization\Torg;
        $rTorgData = $rt->get_data($request);	
        $returnData['torg'] =  $rTorgData;	
		$this->realizationData = $returnData;
		//получение содержания реализации
		$this->realizationDataDetail = \Realization\Model\RealizationDetail::where("rid", $this->realizationId)->get()->toArray();
		//получение данных организации	
		$this->realizationOrg = \Organizations\Model\Org::findOrFail($this->realizationData['main']['org'])->toArray();
		$org_nds = \Price::getNds($this->realizationOrg['nds']);
		$this->realizationOrg['nds_view'] = (($org_nds != '0') ? $org_nds.'%' : 'Без НДС');		
    }
	
	public function prepareData(){
		$rData = array();
		$monthRus = getMonthRus();	
		$rData['type_doc'] = 'Реализация товаров'; //тип документа
		$rData['s'] = (($org_nds != '0') ? $org_nds.'%' : 'Без НДС'); //статус документа
		$rData['doc_numb'] = $this->realizationData['main']['numb']; //номер документа
		$rData['day'] = date('d', strtotime($this->realizationData['main']['date'])); //дата документа (день)
		$rData['month'] = $monthRus[date('n', strtotime($this->realizationData['main']['date']))- 1]; //дата документа (день)
		$rData['year'] = date('Y', strtotime($this->realizationData['main']['date'])); //дата документа (день)
		
		
		/*
		$document->setValue('{TYPE_DOC}', 'Реализация товаров');        
			$document->setValue('{S}', $st);    
			$document->setValue('{DOC_NUMB}', $numb);                  
			$document->setValue('{COUNT}',$i);
			$document->setValue('{DAY}', date('d', $date));
			$document->setValue('{MONTH}', $Month_rus[ date( "n", $date) - 1 ]);
			$document->setValue('{YEAR}', date('Y', $date));					
			$document->setValue('{OID_NAME}', $oid['name']);
			$document->setValue('{OID_INN}', $oid['inn']);
			$document->setValue('{OID_ADDRESS}', $oid['yaddress']);
			$document->setValue('{ORG_NAME}', $org['name']);
			$document->setValue('{ORG_INN}', $org['inn']);
			$document->setValue('{ORG_ADDRESS}', $org['yaddress']);
			$document->setValue('{CURRENCY}', 'Российский рубль, 643');
			$document->setValue('{PRICE_TOTAL}', number_format($total_all,2,'.',' '));
			$document->setValue('{NDS_T}', ($org_nds_id != '0') ? number_format($nds_all,2,'.',' ') : 'без НДС');
			$document->setValue('{TOTAL}', number_format($total_nds_all,2,'.',' '));*/
			
	}
	
	public function prepareDataDetail(){
	
	}
	
	//запуск печати
	public function printStart(){
		$checkStatus = $this->checkRealization();
		if($checkStatus['check'] == true)
		{
			$this->getData();
			
		}
		else
		{
			echo $checkStatus['data']['text'];
		}
    }

    public function get_bank($oid){
        $result = [];

        $ob = \Organizations\Model\OrganizationsBank::where("oid", $oid)->get()->toArray();
        foreach($ob as $r){
            $result[$r["id"]] = $r["rschet"].', '.(empty($r["name_short"]) ? $r["name"] : $r["name_short"]);
        }

        return $result;
    }
	
	

  
}