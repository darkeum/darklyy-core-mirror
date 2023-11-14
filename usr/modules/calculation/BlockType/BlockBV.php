<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Calculation\BlockType;
use \Calculation\Utilities\UtilitiesTool as UtilitiesTool;
use \Calculation\Library\DataLibrary as DataLibrary;
	
class BlockBV extends \Calculation\BlockCalculationLoader{
	
	public $prepareData = array();
	public $dataLibrary = array();
	//конструктор класса
	function __construct(){		
		$this->ut = new UtilitiesTool();
		$this->dataLibrary = new DataLibrary(); 
	}
	
	public function calculate() {
		$pipe = new \Calculation\pipeCalculation();
		//получение G1 и G подпитки из блоков
		$gMin = 1000000;
		foreach ($this->requestData['blocks']['BOV'] as $k => $v) {		
			if($gMin > $v['calcData']->prepareData['G1']) {
				$gMin = $v['calcData']->prepareData['G1'];
				$gPMin = $v['calcData']->prepareData['G1'];
			}
		}
		if(isset($this->requestData['blocks']['BGV'])){
			$blockGVS = $this->requestData['blocks']['BGV'][0];			
			if($blockGVS['gvs_type'] == 'Смешанная'){
				$this->prepareData['G1'] = $blockGVS['calcData']->prepareData['G2'];	
			} else {
				$sumG1 = 0;
				foreach ($this->requestData['blocks']['BOV'] as $k => $v) {		
					$sumG1 += $v['calcData']->prepareData['G1'];
				}
				foreach ($this->requestData['blocks']['BGV'] as $k => $v) {		
					$sumG1 += $v['calcData']->prepareData['G1'];
				}
				$this->prepareData['G1'] = $sumG1;	
			}
			foreach ($this->requestData['blocks']['BGV'] as $k => $v) {		
				if($gMin > $v['calcData']->prepareData['G1']) {
					$gMin = $v['calcData']->prepareData['G1'];
				}
			}
		} else {
			$sumG1 = 0;
			foreach ($this->requestData['blocks']['BOV'] as $k => $v) {		
				$sumG1 += $v['calcData']->prepareData['G1'];
			}
			$this->prepareData['G1'] = $sumG1;	
		}
		$gPSum = 0;
		if(isset($this->requestData['blocks']['BP'][0])) {
			foreach ($this->requestData['blocks']['BP'] as $k => $v) {		
				$gPSum += $v['calcData']->prepareData['gP'];
			}
		}
		
	
	
		$i=1;
		$this->prepareData['GP'] = $gPSum;
		//dump($this->prepareData['G1']);
		//dump($this->prepareData['GP']);
		//dump($this->blockData);
		if($this->blockData['uzel_ucheta']) {	
			$ft = new \Calculation\FlowCalculation();
			//шкаф автоматики
			
			//расходомеры 
			$mtRequest = array(
				'g' => $this->prepareData['G1'],
				'gMin' => $gMin*0.1,
				't1' => $this->requestData['mainData']['t1']['clearValue'],
				't2' => $this->requestData['mainData']['t2']['clearValue'],
				'p1' => $this->requestData['mainData']['p1']['clearValue']*0.1,
				'p2' => $this->requestData['mainData']['p2']['clearValue']*0.1,				
				'man' => 11,
			);
			dump($mtRequest);
			$ft->setData($mtRequest);
			$result = $ft->flowSelection();	
			$parametr = array();
			$parametr['id'] = $result['cid'];		
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 2);	
			$i++;	
			if(isset($this->requestData['blocks']['BP'][0])) {
				$mtRequest = array(
					'g' => $this->prepareData['GP'],
					'gMin' => $gPMin*0.1,
					't1' => $this->requestData['mainData']['t2']['clearValue'],
					't2' => $this->requestData['mainData']['t1']['clearValue'],
					'p1' => $this->requestData['mainData']['p2']['clearValue']*0.1,
					'p2' => $this->requestData['mainData']['p1']['clearValue']*0.1,					
					'man' => 11,
				);				
				$ft->setData($mtRequest);
				dump($mtRequest);
				$result = $ft->flowSelection();	
				$parametr = array();
				$parametr['id'] = $result['cid'];		
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
				$i++;	
			}
			//датчик температуры			
			$ldt = $ft->getLdtByDdt($ft->getDdtByDn($pipe->getDnByG($this->prepareData['G1'])));
			$parametr = array();
			$parametr['man'] = '8';
			$parametr['category'] = '318';
			$parametr['attribute'] = array('42' => $ldt,
										'241' => 'Комплект',
										'36' =>'Pt100');	
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
			$i++;		
			//датчик давления на подаче	
			if($this->requestData['mainData']['p1']['clearValue'] > 80) {
				$diapozon = '1,6';
			} else
			{
				$diapozon = '1,0';	
			}
			$parametr = array();
			$parametr['man'] = '19';
			$parametr['category'] = '280';
			$parametr['attribute'] = array(
										'178' => $diapozon);
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
			$i++;			
			//датчик давления на обратке
			if($this->requestData['mainData']['p2']['clearValue'] > 80) {
				$diapozon = '1,6';
			} else
			{
				$diapozon = '1,0';	
			}
			$parametr = array();
			$parametr['man'] = '19';
			$parametr['category'] = '280';
			$parametr['attribute'] = array(
										'178' => $diapozon);
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
			$i++;
			//поросячий хвостик (трубка перкенса)			
			$parametr = array();
			$parametr['man'] = '1';
			$parametr['category'] = '299';
			$parametr['attribute'] = array(
										'69' => 'Отвод под манометр',
									   	'247' => 'Угловое');									   
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 2);	
			$i++;
		}
		dump($this->blockData);
		if($this->blockData['regul_dav_t1']){			
			//подбор регулирующего клапана на T1
			$dnPrepare = 18.8*sqrt($this->prepareData['G1']/3);
			$dnArray = $this->dataLibrary->getData('Diameter')['dn'];
			foreach ($dnArray as $k => $v) {		
				if($v-$dnPrepare >=0){
					$dnTrue = $v;
					break;
				}
			}
			$parametr = array();
			$parametr['man'] = '72';
			$parametr['category'] = '596';
			$parametr['attribute'] = array(
				'4' => $dnTrue,
				'10' => 'Фланцевое',
				'28' => '150',
				'40' => '16',
				'44' => 'Чугун',				
			);	
			$klapanData = $this->calulationValve($this->prepareData['G1'], $parametr);	
			$klapanDataAtrr = $klapanData[0]['attribute'];		
			$specification[] = $this->structureSpecification($i, $klapanData, '1');	
			$i++;
		}
		if($this->blockData['regul_dav_t2']){			
			//подбор регулирующего клапана на T2
			$dnPrepare = 18.8*sqrt($this->prepareData['G1']/3);
			$dnArray = $this->dataLibrary->getData('Diameter')['dn'];
			foreach ($dnArray as $k => $v) {		
				if($v-$dnPrepare >=0){
					$dnTrue = $v;
					break;
				}
			}
			$parametr = array();
			$parametr['man'] = '72';
			$parametr['category'] = '596';
			$parametr['attribute'] = array(
				'4' => $dnTrue,
				'10' => 'Фланцевое',
				'28' => '150',
				'40' => '16',
				'44' => 'Чугун',				
			);	
			$klapanData = $this->calulationValve($this->prepareData['G1'], $parametr);	
			$klapanDataAtrr = $klapanData[0]['attribute'];		
			$specification[] = $this->structureSpecification($i, $klapanData, '1');	
			$i++;
		}
		//клапан балансировочный
		if($this->blockData['bal_klapan']){	
		
			$pSr = $this->requestData['mainData']['p1']['clearValue']-$this->requestData['mainData']['p2']['clearValue']-5;
			if($this->blockData['uzel_ucheta']) {	
				$pSr = $pSr-1;
			}
			if($this->blockData['regul_dav_t1'] xor $this->blockData['regul_dav_t2']) {	
				$pSr = $pSr/2;
			} elseif($this->blockData['regul_dav_t1'] && $this->blockData['regul_dav_t2']){
				$pSr = $pSr/3;
			}
		//	dump($pSr);
			$kvs = $this->prepareData['G1']/sqrt(0.1*$pSr);
		///	dump($kvs);
			//Андрей (это КВС балансиров с настройкой где-то по середине) ---- спасибо Андрей за комментарий
			$andreyArray = array(
				'15' => '2.5',
				'20' => '5',
				'25' => '7',
				'32' => '10',
				'40' => '15',
				'50' => '25',
				'65' => '50',
				'80' => '70',
				'100' => '100',
				'125' => '150',
				'150' => '300',
				'200' => '400',
			);
			foreach ($andreyArray as $k => $v) {		
				if(($v-$kvs) >=0){				
					$dnTrue = $k;
					break;
				}
			}
			$parametr = array();
			$parametr['man'] = '13';
			$parametr['category'] = '290';
			$parametr['attribute'] = array('4' => $dnTrue,
										'10' => 'Фланцевое',									  
										'40' => '16',									  
										'44' => 'Чугун',									  
										);	
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), '1');	
			$i++;			
		}
		//кран на T1/T2	
		$dn = $pipe->getDnByG($this->prepareData['G1']);
		$parametr = array();
		$parametr['man'] = '134';
		$parametr['category'] = '556';
		$parametr['attribute'] = array(
			'4' => $dn,
			'44' => 'Сталь',
			'157' => '200',
			'195' => 'ручное (рычаг)',
			'196' => 'неполнопроходной',	
			'273' => 'Фланцевый',
		);	
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 4);	
		$i++;
		//краны на линию подпитки T94
		if(isset($this->requestData['blocks']['BP'][0])) {
			$dn = $pipe->getDnByG($this->prepareData['GP']);
			if($dn <= 50){
				$parametr = array();
				$parametr['man'] = '6';
				$parametr['category'] = '557';
				$parametr['attribute'] = array('4' => $dn,
										'195' =>  'ручное (рычаг)',		
										'244' =>  'ВР/ВР',		
										'245' =>  'Нет',	
										'291' =>  'Aquasfera Euro'
										);	
			} else {
				$parametr = array();
				$parametr['man'] = '134';
				$parametr['category'] = '556';
				$parametr['attribute'] = array(
					'4' => $dn,
					'44' => 'Сталь',
					'157' => '200',
					'195' => 'ручное (рычаг)',
					'196' => 'неполнопроходной',	
					'273' => 'Фланцевый',
				);				
			}	
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), '2');	
			$i++;
		}
		//спускники на T1/T2
		$parametr = array();
		$parametr['man'] = '6';
		$parametr['category'] = '557';
		$parametr['attribute'] = array('4' => '25',
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет',	
									   '291' =>  'Aquasfera Euro'
									);							
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 2);	
		$i++;
		//спускники на грязевике
		$parametr = array();
		$parametr['man'] = '6';
		$parametr['category'] = '557';
		$parametr['attribute'] = array('4' => '20',
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет',	
									   '291' =>  'Aquasfera Euro'
									);							
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
		$i++;
		//воздушник на грязевике
		$parametr = array();
		$parametr['man'] = '6';
		$parametr['category'] = '557';
		$parametr['attribute'] = array('4' => '15',
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет',	
									   '291' =>  'Aquasfera Euro'
									);							
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
		$i++;
		if(isset($this->requestData['blocks']['BP'][0])) {
			//спускники на линии подпитки T94
			$dn = $pipe->getDnByG($this->prepareData['GP']);	
			$dnTrue = 15;
			if($dn > 50) {	
				$dnTrue = 25;
			}
			$parametr = array();
			$parametr['man'] = '6';
			$parametr['category'] = '557';
			$parametr['attribute'] = array('4' => $dnTrue,
											'195' =>  'ручное (рычаг)',		
											'244' =>  'ВР/ВР',		
											'245' =>  'Нет',	
											'291' =>  'Aquasfera Euro'
											);	
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
			$i++;
		}
		//грязевик
		$dn = $pipe->getDnByG($this->prepareData['G1']);	
		$dnTrue = 40;
		if($dn > 40) {	
			$dnTrue = $dn;
		}
		$parametr = array();
		$parametr['man'] = '149';
		$parametr['category'] = '283';
		$parametr['attribute'] = array('4' => $dnTrue,
									'10' =>  'Фланцевое',		
									'40' =>  '16',			
									);
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
		$i++;
		//фильтр сетчатый на T1
		$dn = $pipe->getDnByG($this->prepareData['G1']);	
		$dnTrue = 40;
		if($dn > 40) {	
			$dnTrue = $dn;
		}
		$parametr = array();
		$parametr['man'] = '43';
		$parametr['category'] = '284';
		$parametr['attribute'] = array('4' => $dnTrue,
									'28' =>  '300',		
									'44' =>  'Чугун',		
									'41' =>  'Сетчатый с магнитной вставкой',	
									);
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
		$i++;
		if(isset($this->requestData['blocks']['BP'][0])) {
			//фильтр сетчатый на линию подпитки T94
			$dn = $pipe->getDnByG($this->prepareData['GP']);
			if($dn <= 50) {
				$parametr = array();
				$parametr['man'] = '6';
				$parametr['category'] = '284';
				$parametr['attribute'] = array('4' => $dn,
											'10' =>  'Муфтовое',		
											'44' =>  'Латунь',		
											'41' =>  'Сетчатый',	
											);	
			} else {
				$parametr = array();
				$parametr['man'] = '43';
				$parametr['category'] = '284';
				$parametr['attribute'] = array('4' => $dn,
											'28' =>  '300',		
											'44' =>  'Чугун',		
											'41' =>  'Магнитный',	
											);	
			}
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
			$i++;			
		}
		$mCountAll = 0;
		//манометры на Т1
		$mCount = 3;		
		if($this->blockData['uzel_ucheta']) {
			$mCount++;
		}
		if($this->blockData['regul_dav_t1']) {
			$mCount++;
		}
		$mCountAll += $mCount;
		$specification[] = $this->structureSpecification($i, $this->getManometrByP($this->requestData['mainData']['p1']['clearValue'], '18'), $mCount);	
		$i++;
		//манометры на Т2
		$mCount = 2;		
		if($this->blockData['uzel_ucheta']) {
			$mCount++;
		}
		if($this->blockData['regul_dav_t2']) {
			$mCount++;
		}	
		if($this->blockData['bal_klapan']) {
			$mCount++;
		}
		$mCountAll += $mCount;
		$specification[] = $this->structureSpecification($i, $this->getManometrByP($this->requestData['mainData']['p2']['clearValue'], '18'), $mCount);	
		$i++;
		//манометры на линию подпитки Т94
		if(isset($this->requestData['blocks']['BP'][0])) {
			$mCount = 1;		
			if($this->blockData['uzel_ucheta']) {
				$mCount++;
			}		
			$mCountAll += $mCount;	
			
			$specification[] = $this->structureSpecification($i, $this->getManometrByP($this->requestData['mainData']['p2']['clearValue'], '18'), $mCount);	
			$i++;
		}


		//добавляем краны под ПДТВХ 
		if($this->blockData['uzel_ucheta']) {
			$mCountAll = $mCountAll + 2;
		}	

		//кран на манометр
		$parametr = array();
		$parametr['man'] = '7';
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' =>  'Кран',		
									   '156' =>  'G1/2 - М20х1.5');									
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $mCountAll);	
		$i++;
		//термометр на Т1
		$dn = $pipe->getDnByG($this->prepareData['G1']);
		$termometr_dlina = '66';
		if($dn >= 100){
			$termometr_dlina = '103';
		}
		$termometr = 'От 0 до 100';
		if($this->requestData['mainData']['t1']['clearValue'] > 95){
			$termometr = 'От 0 до 150';
		}		
		$parametr = array();
		$parametr['man'] = '1';
		$parametr['category'] = '298';
		$parametr['attribute'] = array('62' => 'Жидкостный',
									   '89' => $termometr,		
									   '158' =>  $termometr_dlina);	
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
		$i++;
		//термометр на Т2
		$dn = $pipe->getDnByG($this->prepareData['G1']);
		$termometr_dlina = '66';
		if($dn >= 100){
			$termometr_dlina = '103';
		}
		$termometr = 'От 0 до 100';
		if($this->requestData['mainData']['t2']['clearValue'] > 95){
			$termometr = 'От 0 до 150';
		}		
		$parametr = array();
		$parametr['man'] = '1';
		$parametr['category'] = '298';
		$parametr['attribute'] = array('62' => 'Жидкостный',
									'89' => $termometr,		
									'158' =>  $termometr_dlina);	
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
		$i++;
		$result = array();
		$result['spec'] = $specification;	
		$result['parameter']['maxDn'] = $pipe->getDnByG($this->prepareData['G1']);	
		return $result;

	}
}
?>