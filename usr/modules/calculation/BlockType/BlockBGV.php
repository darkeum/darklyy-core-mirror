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

	
class BlockBGV extends \Calculation\BlockCalculationLoader{
	
	public $prepareData = array();
	public $dataLibrary = array();
	//конструктор класса
	function __construct(){		
		$this->dataLibrary = new \Calculation\Library\DataLibrary(); 
	}

	//заполняем данные для подбора теплообменника
	public function prepareDataToHeatCalculation(){
		parent::prepareDataToHeatCalculation();
		switch($this->blockData['teploPr']) {
			default:
			case "1":
				$coeficient = 1;
			break;
			case "2":
				$coeficient = 0.75;
			break;
			case "3":
				$coeficient = 0.5;
			break;				
		}
		$q = '';
		$gt3 = '';
		if($this->blockData['gvs_type'] == 'Смешанная') {
			$coeficient = 1;
		} elseif($this->blockData['teplo'] == 1) {
			$coeficient = 1;
		}
		if($this->blockData['typeNag'] == 1){
			$q = round(($this->blockData['q']['clearValue']*$coeficient), 6);
		} else {
			$gt3 = round(($this->blockData['gt3']['clearValue']*$coeficient), 6);
		}
		$this->heatData['Q'] = $q;
		$this->heatData['G2'] = $gt3;
		$this->heatData['T1'] = $this->blockData['t1']['clearValue'];
		$this->heatData['T2'] = $this->blockData['t2']['clearValue'];
		$this->heatData['T3'] = $this->blockData['t4']['clearValue'];
		$this->heatData['T4'] = $this->blockData['t3']['clearValue'];
		$this->heatData['T12'] = '';
		$this->heatData['T22'] = '';
		$this->heatData['X2'] = '15';
		$this->heatData['R2'] = '2';
		if($this->blockData['gvs_type'] == 'Смешанная') {
			$this->heatData['R1'] = '1.3';			
			$this->heatData['R12'] = '0.7';
			$this->heatData['R22'] = '2';
			$this->heatData['qBalanceAuto'] = '1';
			$this->heatData['flan'] = '1';
			$this->heatData['allowChange'] = '1';
			if($this->blockData['teplo'] == 1) {
				$this->heatData['mono'] = '1';
				$this->heatData['pto_2'] = '0';
			} else {
				$this->heatData['mono'] = '0';
				$this->heatData['pto_2'] = '1';
			}
			$this->heatData['Q3'] = '';
			$this->heatData['Gco_2'] = $this->requestData['GBOVSum'];	
			$this->heatData['ptoTypeUse'] = 'ГВС двухступенчатая';				
		} else {
			$this->heatData['R1'] = '2';
			if($this->requestData['mainData']['p1']['clearValue']- $this->requestData['mainData']['p2']['clearValue'] < 7){
				$this->heatData['R1'] = '1';
			}	
			$this->heatData['ptoTypeUse'] = 'ГВС';	
		}
		
	}

	public function calculate() {
		//получаем данные теплообменника(ов)		
		if($this->blockData['gvs_type'] == 'Смешанная') {
			// $cidArray = $this->getHeatCalculationGVSLocalTemp();
			$cidArray = $this->getHeatCalculation();
			$this->prepareData['G1'] = $this->prepareNumeric($this->heatDataComplete['detailed']['lbl2_G1']);	
			$this->prepareData['G11'] = $this->prepareNumeric($this->heatDataComplete['detailed']['lbl2_G2']);	
			$this->prepareData['G2'] = $this->prepareNumeric($this->heatDataComplete['detailed']['lbl1_G1']);	
			
		} else {
			// $cidArray = $this->getHeatCalculationLocalTemp();
			$cidArray = $this->getHeatCalculation();
			$this->prepareData['GTO1'] = $this->prepareNumeric($this->heatDataComplete['detailed']['lbl_G1']);
			$this->prepareData['GTO11'] = $this->prepareNumeric($this->heatDataComplete['detailed']['lbl_G2']);
			switch($this->blockData['teploPr']) {
				default:
				case "1":
					$this->prepareData['G1'] = 	$this->prepareData['GTO1'];
					$this->prepareData['G11'] = $this->prepareData['GTO11'];
				break;
				case "2":
					$this->prepareData['G1'] = 	round(($this->prepareData['GTO1']/0.75), 6);
					$this->prepareData['G11'] = round(($this->prepareData['GTO11']/0.75), 6);
				break;
				case "3":
					$this->prepareData['G1'] = 	round(($this->prepareData['GTO1']/0.5), 6);
					$this->prepareData['G11'] = round(($this->prepareData['GTO11']/0.5), 6);
				break;				
			}
			$this->prepareData['dPto'] = $this->prepareNumeric(explode(' / ', $this->heatDataComplete['detailed']['lbl_R2_R2S'])[0]);
		}
		$specification = array();
		$i = 1;
		foreach ($cidArray as $k => $v) {		
			$parametr = array();
			$parametr['id'] = $v;		
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $this->blockData['teplo']);	
			$i++;		
		}
		// подбираем насосное оборудование	
		$pc = new \Calculation\PumpCalculation();
		$pipes = new \Calculation\pipeCalculation();
		$pumpSeriesArray = array();
		$pumpCount = 1;
		if( $this->blockData['nasos_type'] == 'Два раздельных')	{
			$pumpCount = 2;
		}		

		$requestForPump = array(
			'pumpUse' => 'Циркуляция ГВС',
			'frequency' => '0',
			'pumpSeries' => 'YONOS MAXO-Z',
			'soloOrBinary' => '0',
			'h' => ($pipes->getDnByG($this->prepareData['G11']) >= 40) ? 10 : 8,
			'q' => $this->prepareData['G11']*0.55,
			'u' => '1~ 230 V / 50 Hz',	
		);
	
		$pc->setData($requestForPump);	 
		$result = $pc->collectPump();
		if($result['cid'] != 0){
			$parametr = array();
			$parametr['id'] = $result['cid'];		
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $pumpCount);	
			$i++;
		}

		// dd($specification);
		//dump($this->blockData);			
		//подбор регулирующего клапана	
		$klapanData = $this->calulationValve($this->prepareData['G1']);	
		$klapanDataAtrr = $klapanData[0]['attribute'];		
		$specification[] = $this->structureSpecification($i, $klapanData, '1');	
		$i++;
		//подбор электропривода
		$parametr = array();
		if($klapanDataAtrr['4'] <= 50){
			$parametr['id'] = '2319';		
		}else{
			$parametr['id'] = '22106';		
		}				
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), '1');	
		$i++;		
		//датчик температуры погружной
		$parametr = array();
		$parametr['id'] = '18481';		
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), '1');	
		$i++;		
		//реле давления насоса
		$parametr = array();
		$parametr['id'] = '2287';		
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $pumpCount);	
		$i++;		
		//кран под реле давления
		$parametr = array();
		$parametr['id'] = '112808';		
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $pumpCount);	
		$i++;	
		//подключаем класс для работы с диаметрами
		$pipe = new \Calculation\pipeCalculation();
		//счетчик или как андрей говорит расходомер
		$dn = $pipe->getDnByG($this->prepareData['G11']);	
		$dnArray = $this->dataLibrary->getData('Diameter')['dn'];	
		$dnKey = array_search($dn, $dnArray); 
		if($dnKey - 3 >= 0){
			$dnTrue = $dnArray[$dnKey - 3];
		} elseif($dnKey - 2 >= 0){
			$dnTrue = $dnArray[$dnKey - 2];
		} elseif($dnKey - 1 >= 0) {
			$dnTrue = $dnArray[$dnKey - 1];
		} else {
			$dnTrue = $dnArray[0];
		}
		$parametr = array();
		$parametr['man'] = '120';
		$parametr['category'] = '467';
		$parametr['attribute'] = array(
			'4' => $dnTrue,
			'27' => 'Да',
			'28' => '90',
			'32' => 'Многоструйный'
		);	
		if($dnTrue>50) {
			$parametr['category'] = '468';
			$parametr['attribute'] = array(
				'4' => $dnTrue,
				'27' => 'Да',
				'28' => '90'
			);	
		}
		
		
		
		dump($parametr);
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
		$i++;
	
		if($this->blockData['gvs_type'] == 'Смешанная') {
			//кран на T2
			$dn = $pipe->getDnByG($this->prepareData['G2']);	
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
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $this->blockData['teplo']);	
			$i++;
			//кран T1
			$dnG1 = $pipe->getDnByG($this->prepareData['G1']);	
			$parametrG1 = array();
			$parametrG1['man'] = '134';
			$parametrG1['category'] = '556';
			$parametrG1['attribute'] = array(
				'4' => $dnG1,
				'44' => 'Сталь',
				'157' => '200',
				'195' => 'ручное (рычаг)',
				'196' => 'неполнопроходной',	
				'273' => 'Фланцевый',
			);	
			//кран с БОВ
			$dnGBOVSum = $pipe->getDnByG($this->requestData['GBOVSum']);	
			$parametrGBOVSum = array();
			$parametrGBOVSum['man'] = '134';
			$parametrGBOVSum['category'] = '556';
			$parametrGBOVSum['attribute'] = array(
				'4' => $dnGBOVSum,
				'44' => 'Сталь',
				'157' => '200',
				'195' => 'ручное (рычаг)',
				'196' => 'неполнопроходной',	
				'273' => 'Фланцевый',
			);	
			
			if($this->blockData['teplo'] == 2){
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametrG1), $this->blockData['teplo']);	
					$i++;	
			} else {
				//сортируем кран с БОВ и Т1 по диаметру
				if($this->prepareData['G1'] > $this->requestData['GBOVSum']) {				
					$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametrG1), 1);	
					$i++;				
					$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametrGBOVSum), 1);	
					$i++;
				} else {
					$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametrGBOVSum), 1);	
					$i++;
					$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametrG1), 1);	
					$i++;
				}
			}
			//затворы на перемычке
			$dn = $pipe->getDnByG($this->requestData['GBOVSum']);
			if($dn < 50) {
				$parametr = array();
				$parametr['man'] = '6';
				$parametr['category'] = '557';
				$parametr['attribute'] = array('4' => $dn,
										'195' =>  'ручное (рычаг)',		
										'244' =>  'ВР/ВР',		
										'245' =>  'Нет',	
										'291' =>  'Aquasfera Euro');	
			} else {
				$parametr = array();
				$parametr['man'] = '146';
				$parametr['category'] = '288';
				$parametr['attribute'] = array('4' => $dn,
											'37' =>  '120',		
											'49' =>  'Чугун',		
											'223' =>  'Рукоятка');	
				
			}	
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
			$i++;	
			
		} else {
			//кран T1
			$dnG1 = $pipe->getDnByG($this->prepareData['G1']);	
			$parametrG1 = array();
			$parametrG1['man'] = '134';
			$parametrG1['category'] = '556';
			$parametrG1['attribute'] = array(
				'4' => $dnG1,
				'44' => 'Сталь',
				'157' => '200',
				'195' => 'ручное (рычаг)',
				'196' => 'неполнопроходной',	
				'273' => 'Фланцевый',
			);	
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 2);	
			$i++;	
			if($this->blockData['teplo'] == 2){
				//кран GTO1
				$dnG1 = $pipe->getDnByG($this->prepareData['GTO1']);	
				$parametrG1 = array();
				$parametrG1['man'] = '134';
				$parametrG1['category'] = '556';
				$parametrG1['attribute'] = array(
					'4' => $dnG1,
					'44' => 'Сталь',
					'157' => '200',
					'195' => 'ручное (рычаг)',
					'196' => 'неполнопроходной',	
					'273' => 'Фланцевый',
				);	
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 4);	
				$i++;	
			}

		}
		//затвор на В1-Т3	
		$ztCount = 3;
		if(($this->blockData['teplo'] == 2) && ($this->blockData['gvs_type']  == 'Параллельная')){
			$ztCount = 4;
		}
		$dn = $pipe->getDnByG($this->prepareData['G11']);
		if($dn < 50) {
			$parametr = array();
			$parametr['man'] = '6';
			$parametr['category'] = '557';
			$parametr['attribute'] = array('4' => $dn,
									'195' =>  'ручное (рычаг)',		
									'244' =>  'ВР/ВР',		
									'245' =>  'Нет',	
									'291' =>  'Aquasfera Euro');	
		} else {
			$parametr = array();
			$parametr['man'] = '146';
			$parametr['category'] = '288';
			$parametr['attribute'] = array('4' => $dn,
										'37' =>  '120',		
										'49' =>  'Чугун',		
										'223' =>  'Рукоятка');	
			
		}	
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $ztCount);	
		$i++;	
		if($this->blockData['gvs_type'] == 'Параллельная') {
			//кран GTO11 (затвор на ТО)
			if($this->blockData['teplo'] == 2){				
				$dn = $pipe->getDnByG($this->prepareData['GTO11']);
				if($dn < 50) {
					$parametr = array();
					$parametr['man'] = '6';
					$parametr['category'] = '557';
					$parametr['attribute'] = array('4' => $dn,
											'195' =>  'ручное (рычаг)',		
											'244' =>  'ВР/ВР',		
											'245' =>  'Нет',	
											'291' =>  'Aquasfera Euro');	
				} else {
					$parametr = array();
					$parametr['man'] = '146';
					$parametr['category'] = '288';
					$parametr['attribute'] = array('4' => $dn,
												'37' =>  '120',		
												'49' =>  'Чугун',		
												'223' =>  'Рукоятка');	
					
				}					
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 4);	
				$i++;	
			}
		}
		//кран на T4
		$dn = $pipe->getDnByG($this->prepareData['G11']);	
		$dnArray = $this->dataLibrary->getData('Diameter')['dn'];	
		$dnKey = array_search($dn, $dnArray); 
		if($dnKey - 1 >= 0){
			$dnTrue = $dnArray[$dnKey - 1];
		}  else {
			$dnTrue = $dnArray[0];
		}
		$ztT4Count = 1;
		if($pumpCount == 2) {
			$ztT4Count = 5;
		} else {
			if($this->blockData['teplo'] == 2){	
				$ztT4Count = 1;
			} else {
				$ztT4Count = 2;
			}
		}
		if($dnTrue < 50) {
			$parametr = array();
			$parametr['man'] = '6';
			$parametr['category'] = '557';
			$parametr['attribute'] = array('4' => $dnTrue,
									'195' =>  'ручное (рычаг)',		
									'244' =>  'ВР/ВР',		
									'245' =>  'Нет',	
									'291' =>  'Aquasfera Euro');	
		} else {
			$parametr = array();
			$parametr['man'] = '146';
			$parametr['category'] = '288';
			$parametr['attribute'] = array('4' => $dnTrue,
										'37' =>  '120',		
										'49' =>  'Чугун',		
										'223' =>  'Рукоятка');	
			
		}					
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $ztT4Count);	
		$i++;	
		//спусники
		$parametr = array();
		$parametr['man'] = '6';
		$parametr['category'] = '557';
		$parametr['attribute'] = array('4' => '25',
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет',	
									   '291' =>  'Aquasfera Euro'
									);	
		$spCount = 0;
		if($this->blockData['gvs_type'] == 'Смешанная') {
			if($this->blockData['teplo'] == 1) {
				if($pumpCount == 1)	{
					$spCount = 6;
				} else {
					$spCount = 8;
				}
			} else {
				if($pumpCount == 1)	{
					$spCount = 8;
				} else {
					$spCount = 10;
				}
			}
		} else {
			if($this->blockData['teplo'] == 1) {
				if($pumpCount == 1)	{
					$spCount = 4;
				} else {
					$spCount = 6;
				}
			} else {
				if($pumpCount == 1)	{
					$spCount = 8;
				} else {
					$spCount = 10;
				}
			}
		}						
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $spCount);	
		$i++;
		//фильтр на В1 сетчатый 
		$dn = $pipe->getDnByG($this->prepareData['G11']);
		if($dn < 50) {
			//фильтр сетчатый на В1 при dn  < 50
			$parametr = array();
			$parametr['man'] = '6';
			$parametr['category'] = '284';
			$parametr['attribute'] = array('4' => $dn,
										'10' =>  'Муфтовое',		
										'44' =>  'Латунь',		
										'41' =>  'Сетчатый',	
										);
								
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
			$i++;
		} else {
			//фильтр сетчатый на В1
			$parametr = array();
			$parametr['man'] = '43';
			$parametr['category'] = '284';
			$parametr['attribute'] = array('4' => $dn,
										'28' =>  '300',		
										'44' =>  'Чугун',		
										'41' =>  'Сетчатый с магнитной вставкой',	
										);
								
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
			$i++;
		}
		//фильтр на Т4 сетчатый
		$dn = $pipe->getDnByG($this->prepareData['G11']);	
		$dnArray = $this->dataLibrary->getData('Diameter')['dn'];	
		$dnKey = array_search($dn, $dnArray); 
		if($dnKey - 1 >= 0){
			$dnTrue = $dnArray[$dnKey - 1];
		}  else {
			$dnTrue = $dnArray[0];
		}		
		if($dnTrue < 50) {
			//фильтр сетчатый на Т4 при dn  < 50
			$parametr = array();
			$parametr['man'] = '6';
			$parametr['category'] = '284';
			$parametr['attribute'] = array('4' => $dnTrue,
										'10' =>  'Муфтовое',		
										'44' =>  'Латунь',		
										'41' =>  'Сетчатый',	
										);
								
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
			$i++;				

		} else {
			//фильтр сетчатый на Т4
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
		}
		$dn = $pipe->getDnByG($this->prepareData['G11']);
		if($dn < 50) {	
			//обратный клапан на В1	при dn  < 50		
			if($pumpCount == 2){
				$parametr = array();
				$parametr['man'] = '6';
				$parametr['category'] = '289';
				$parametr['sku_like'] = '3003-';
				$parametr['attribute'] = array('4' => $dn);
									
				$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
				$i++;
			}

		} else {
			
			//обратный клапан на В1		
			$parametr = array();
			$parametr['man'] = '13';
			$parametr['category'] = '289';
			$parametr['attribute'] = array('4' => $dn,
										'10' =>  'Межфланцевое',		
										'37' =>  '120',		
										'44' =>  'Чугун',	
										);									
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
			$i++;
		}
		//обратный клапан на Т4
		$dn = $pipe->getDnByG($this->prepareData['G11']);	
		$dnArray = $this->dataLibrary->getData('Diameter')['dn'];	
		$dnKey = array_search($dn, $dnArray); 
		if($dnKey - 1 >= 0){
			$dnTrue = $dnArray[$dnKey - 1];
		}  else {
			$dnTrue = $dnArray[0];
		}		
		if($dnTrue < 50) {
		
			//обратный клапан на Т4	при dn  < 50			
			$parametr = array();
			$parametr['man'] = '6';
			$parametr['category'] = '289';
			$parametr['sku_like'] = '3003-';
			$parametr['attribute'] = array('4' => $dnTrue);
								
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $pumpCount);	
			$i++;			

		} else {			
			//обратный клапан на Т4			
			$parametr = array();
			$parametr['man'] = '13';
			$parametr['category'] = '289';
			$parametr['attribute'] = array('4' => $dnTrue,
										'10' =>  'Межфланцевое',		
										'37' =>  '120',		
										'44' =>  'Чугун',	
										);									
			$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), $pumpCount);	
			$i++;
			
		}
		//предохранительный клапан
		$dn = $pipe->getDnByG($this->prepareData['G11']);
		$parametr = array();
		$parametr['man'] = '88';
		$parametr['category'] = '312';
		$h = 0;			
		foreach ($this->requestData['blocks']['BOV'] as $k => $v) {		
			if($h < $v['h']) {
				$h = $v['h'];
			}	
		}
		if($h <= 40){
			$pSr = 6;
		}elseif($h <= 60){
			$pSr = 8;
		}else{
			$pSr = 10;
		}
		if($dn <= 100) {
			$parametr['attribute'] = array('85' => $pSr,
										   '340' => '25',		
										   '86' => 'Prescor B');	
		} else {
			$parametr['attribute'] = array('340' => '32',
										   '85' => $pSr,
										   '86' => 'Prescor S 700');	
		}
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1);	
		$i++;
		//манометр Т1					   
		$specification[] = $this->structureSpecification($i, $this->getManometrByP($this->requestData['mainData']['p1']['clearValue'], '18'), 1+$this->blockData['teplo']);	
		$i++;
		//манометры остальные 
		$mnCount = 0;
		if($this->blockData['gvs_type'] == 'Смешанная') {
			if($this->blockData['teplo'] == 1) {
				if($pumpCount == 1)	{
					$mnCount = 9;
				} else {
					$mnCount = 11;
				}
			} else {
				if($pumpCount == 1)	{
					$mnCount = 10;
				} else {
					$mnCount = 12;
				}
			}
		} else {
			if($this->blockData['teplo'] == 1) {
				if($pumpCount == 1)	{
					$mnCount = 7;
				} else {
					$mnCount = 9;
				}
			} else {
				if($pumpCount == 1)	{
					$mnCount = 10;
				} else {
					$mnCount = 12;
				}
			}
		}					
		$specification[] = $this->structureSpecification($i, $this->getManometrByP($this->requestData['mainData']['p2']['clearValue'], '18'), $mnCount);	
		$i++;
		//кран на манометр
		$parametr = array();
		$parametr['man'] = '7';
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' =>  'Кран',		
									   '156' =>  'G1/2 - М20х1.5');									
		$specification[] = $this->structureSpecification($i, \Catalog_v2\Model\Catalog::getCatalog($parametr), 1+$this->blockData['teplo']+$mnCount);	
		$i++;
		//термометр Т1
		$dn = $pipe->getDnByG($this->prepareData['G1']);
		$specification[] = $this->structureSpecification($i, $this->getTermometrByTAndDn($this->requestData['mainData']['t1']['clearValue'], $dn, '18'), $this->blockData['teplo']);	
		$i++;
		//термометр Т2
		$dn = $pipe->getDnByG($this->prepareData['G11']);
		$tCount = 0;
		if($this->blockData['gvs_type'] == 'Смешанная') {
			if($this->blockData['teplo'] == 1) {
				$tCount = 5;
			} else {
				$tCount = 7;
			}
		} else {
			if($this->blockData['teplo'] == 1) {
				$tCount = 3;
			} else {
				$tCount = 7;
			}
		}					
		$specification[] = $this->structureSpecification($i, $this->getTermometrByTAndDn($this->requestData['mainData']['t2']['clearValue'], $dn, '18'), $tCount);	
		$i++;
		$result = array();
		$result['spec'] = $specification;
		if($this->blockData['gvs_type'] == 'Смешанная') {
			$result['parameter']['maxDn'] = $pipe->getDnByG($this->prepareData['G2']);	
		} else {
			$result['parameter']['maxDn'] = $pipe->getDnByG($this->prepareData['G1']);	
		}	
		$result['parameter']['gvs_type'] = $this->blockData['gvs_type'];	
		return $result;
	}
}
?>