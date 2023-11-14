<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Calculation;
use \Calculation\Utilities\UtilitiesTool as UtilitiesTool;
use \Calculation\Library\DataLibrary as DataLibrary;
	
class FlowCalculation{	
	
	//данные
	public $requestData = array();
	public $ut;
	public $dataLibrary = array();
	public $DataLibrary = array();
	public $prepareData = array();

	//конструктор класса
	function __construct(){	
		//подключаем класс с инструментами для работы
		$this->ut = new UtilitiesTool();
		$this->dataLibrary = new DataLibrary(); 
		$this->DataLibrary = new DataLibrary(); 
		$this->UtilitiesTool = new UtilitiesTool(); 
	}

	//сбор входных данных
	public function setData($request){
		$this->requestData = array(	
			'g' 		=> $this->UtilitiesTool->prepareNumeric($request['g']),
			'gMin' 		=> $this->UtilitiesTool->prepareNumeric($request['gMin']),
			't1' 		=> $this->UtilitiesTool->prepareNumeric($request['t1']),
			't2' 		=> $this->UtilitiesTool->prepareNumeric($request['t2']),
			'p1' 		=> $this->UtilitiesTool->prepareNumeric($request['p1']),
			'p2' 		=> $this->UtilitiesTool->prepareNumeric($request['p2']),
			//'flowMin' => 0,
			'man' 		=> $this->prepareManufacture('11'),
		);	
		// dump($this->requestData);		
	}

	//приводим производителя в стандарт
	private function prepareManufacture($man){
		switch($man) {
			default:
			case "11":
			case "termotronic":
				$result = '11';
			break;				
		}
		return $result;
	}

	//расчет длины датчика температуры из диаметра трубопровода КИП
	public function getLdtByDdt($dumpt) {		
		if($dumpt == 65){
			$ldt = 80;
		} elseif(($dumpt == 80)||($dumpt == 100)){
			$ldt = 100;
		} elseif(($dumpt == 125)||($dumpt == 150)){
			$ldt = 120;
		} elseif($dumpt == 200){
			$ldt = 160;
		} else{
			$ldt = 0;	
		}
		return $ldt;
	}

	//получение диаметра трубопровода КИП по основному диаметру
	public function getDdtByDn($dn) {
		$dumpt = 65;
		if($dn > 65) {
			$dumpt = $dn;
		}
		return $dumpt;
	}

	//получение длины участка
	private function getLuchByDn($d, $lrashodomer)
	{		
		$luch = 0;
		$luch = $lrashodomer;	
		if(5*$d < 120) {
			$luch = $luch + 120;
		} else {
			$luch = $luch + 5*$d;
		}
		if(3*$d < 120) {
			$luch = $luch + 120;
		} else {
			$luch = $luch + 3*$d;
		}
		return $luch;
		
	}

	//подбор расходомера
	public function flowSelection() {		
		//подключаем класс для работы с диаметрами
		$pipe = new \Calculation\pipeCalculation();
		if(empty($this->requestData['g'])){
			//считаем g по q
		}
		//получение диаметра основного трубопровода
		$this->prepareData['dn'] = $pipe->getDnByG($this->requestData['g']);	
		//первичный расходомер для подбора
		$dnFlowMinTemp = $this->prepareData['dn']/2.8;		
		foreach($this->DataLibrary->getData('Diameter')['dn'] as $k => $v) {
			if($dnFlowMinTemp <= $v) {
				$this->prepareData['dnFlowMin'] = $v;
				$this->prepareData['dnFlowMinKey'] = $k;
				break;
			}
		}
		//получаем диаметр трубы под КИП
		$dumpt = 65;
		if($this->prepareData['dn'] > 65) {
			$dumpt = $this->prepareData['dn'];
		}
		$result = array();
		$this->prepareData['dumpt'] = $dumpt;
		
		switch($this->requestData['man']) {
			default:				
				throw new \Exception('Не выбран производитель расходомеров!');
			break;
			case "11":
				$result = $this->termotronicSelection();
				// dump($result);
			break;				
		}
		return $result;
		
	}

	public function termotronicSelection() {
		// dump('wtt');
		//указываем минимальный возможный диаметр
		if($this->prepareData['dnFlowMin']<20){
			$this->prepareData['dnFlowMinKey'] = array_search('20', $this->DataLibrary->getData('Diameter')['dn']);
			$this->prepareData['dnFlowMin'] = $this->dataLibrary->getData('Diameter')['dn'][$this->prepareData['dnFlowMinKey']];
		}
		$i = 0;
	
		do{
			$i++;
			$parametr = array();
			$parametr['category'] = '470';	
			if($this->prepareData['dnFlowMin']<64) {
				$parametr['attribute']['10'] = 'Сэндвич';
			} else {
				$parametr['attribute']['64'] = 'IP65';
			}
			$parametr['attribute']['4'] = $this->prepareData['dnFlowMin'];
			$parametr['attribute']['297'] = 'Да';
			// dump($parametr);		

			$flowList = \Catalog_v2\Model\Catalog::getCatalog($parametr);
			
			// dump($flowList);

			if(!empty($flowList)) {	
				//группируем расходомеры по Q макс. (по возрастанию) и сортируем 
				//полученные расходомеры в группах по Q мин. (по убыванию) 		
				$flowListClear = array();
				foreach ($flowList as $key => $rashodomer) {					
					$attribute = $rashodomer['attribute'];			
					$qMax  = $this->UtilitiesTool->prepareNumeric($attribute['189']);
					$qMin  = $this->UtilitiesTool->prepareNumeric($attribute['187']);
					$flowListClear[$qMax][strval($qMin)] = $rashodomer;
				}
				ksort($flowListClear);
				foreach ($flowListClear as $key => $value) {
					krsort($value);
					$flowListClear[$key] = $value;
				}
				$dc = new \Calculation\DiffuserCalculation();
				foreach ($flowListClear as $key => $value) {	
					$countClass = 0;			
					foreach ($value as $k => $valueFlow) {
						$countClass++;
						if(($this->requestData['gMin'] > $key*2/3)||($this->requestData['gMin']<$k)){
							if(count($value) == $countClass) {
							} else {
								continue;
							}
							
						}							
						$attribute = $valueFlow['attribute'];		
						$luch = $this->getLuchByDn($attribute['4'], $attribute['192']);
						$dc->setData(array('dn1' => $this->prepareData['dn'], 'dn2' => $this->prepareData['dnFlowMin']));
						$dc->diffuserSelection();
						$lKonfuser = $dc->getDiffuserL();
						$dc->setData(array('dn1' => $this->prepareData['dumpt'], 'dn2' => $this->prepareData['dnFlowMin']));
						$dc->diffuserSelection();
						$lDiffuser = $dc->getDiffuserL();
						$selectionData = array();
						$selectionData['g'] = $this->requestData['g'];
						$selectionData['t1'] = $this->requestData['t1'];
						$selectionData['p1'] = $this->requestData['p1'];
						$selectionData['dn'] = $this->prepareData['dn'];
						$selectionData['dumpt'] = $this->prepareData['dumpt'];
						$selectionData['dnFlowMin'] = $this->prepareData['dnFlowMin'];
						$selectionData['luch'] = $luch;
						$selectionData['lKonfuser'] = $lKonfuser;
						$selectionData['lDiffuser'] = $lDiffuser;
						// dump($selectionData);
						//углы конфузора и диффузора (переходов)
						if($lKonfuser == 0){
							$a1 = 0;	
						} else {
							$a1 =  round(rad2deg(atan(($this->prepareData['dn'] - $this->prepareData['dnFlowMin'])/2/($lKonfuser))), 2)*2;
						}

						if($lDiffuser == 0){
							$a2 = 0;	
						} else {
							$a2 =  round(rad2deg(atan(($this->prepareData['dumpt'] - $this->prepareData['dnFlowMin'])/2/($lDiffuser))), 2)*2;
						}
					
						$selectionData['a1'] = $a1;
						$selectionData['a2'] = $a2;					
						//плотность

						$tKelv = $this->requestData['t1']+273.15;
						$pMPa = $this->requestData['p1']*0.0980665;
						$tPrepare = 1386/$tKelv;
						$pPrepare = $pMPa/16.53;
						$selectionData['tPrepare'] = $tPrepare;
						$selectionData['pPrepare'] = $pPrepare;		
					
						$yXSum = 0;
						foreach($this->DataLibrary->getData('Flow')['density'] as $k => $v) {
							$yXTemp = -$v['n']*$v['l']*pow((7.1-$pPrepare), ($v['l']-1))*pow(($tPrepare-1.222), $v['j']);
							//dump($yXTemp);
							$yXSum += $yXTemp;
						}
						$roughnessFactor = 0.5;
						$r = 0.461526;
						$densityTemp = $pPrepare*$yXSum*$r*$tKelv/$pMPa;
						$density = 1/$densityTemp*1000;
						
						$selectionData['yXSum'] = $yXSum;
						$selectionData['densityTemp'] = $densityTemp;		
						$selectionData['density'] = $density;		
						

						$q = $this->requestData['g']*1000/$density;
						$selectionData['q'] = $q;		
					
					
					
						$v0 = ($q/3.6)/((pi()*pow($this->prepareData['dnFlowMin'],2))/4000);
						$v1 = ($q/3.6)/((pi()*pow($this->prepareData['dn'],2))/4000);
						$v2 = ($q/3.6)/((pi()*pow($this->prepareData['dumpt'],2))/4000);

						$selectionData['v0'] = $v0;
						$selectionData['v1'] = $v1;		
						$selectionData['v2'] = $v2;	
					

						$gCoeficient = 1/pow((1.14+2*log10($this->prepareData['dnFlowMin']/$roughnessFactor)), 2);
						$lossPressure = $gCoeficient*($luch-$attribute['192'])*pow($v0, 2)/($this->prepareData['dnFlowMin']*2*9.81)+$attribute['294']*pow($q, 2);
						
						if($a1 == 0) {
							$trCoeficientKonfuser = 0;	
						} else {
							$trCoeficientKonfuser = ($gCoeficient/(8*sin(deg2rad($a1/2))))*(1-pow(($this->prepareData['dnFlowMin']/$this->prepareData['dn']), 4));	
						}
						
						$lossPressureKonfuser = $trCoeficientKonfuser*pow($v0, 2)/(2*9.81);


						$rasCoeficientDiffuser = 3.2*pow(tan(deg2rad($a2/2)), 1.25)*pow((1-pow(($this->prepareData['dnFlowMin']/$this->prepareData['dumpt']), 2)), 2);
						if($a2 == 0) {
							$trCoeficientDiffuser = 0;	
						} else {
							$trCoeficientDiffuser = ($gCoeficient/(8*sin(deg2rad($a2/2))))*(1-pow(($this->prepareData['dnFlowMin']/$this->prepareData['dumpt']), 4));	
						}
						$lossPressureDiffuser = ($rasCoeficientDiffuser+$trCoeficientDiffuser)*pow($v0, 2)/(2*9.81);

						$lossPressureSum = $lossPressureDiffuser+$lossPressure+$lossPressureKonfuser;
						$lossPressureSumCompl =  $lossPressureSum/10;

						$selectionData['trCoeficientKonfuser'] = $trCoeficientKonfuser;
						$selectionData['lossPressureKonfuser'] = $lossPressureKonfuser;
						$selectionData['gCoeficient'] = $gCoeficient;	
						$selectionData['lossPressure'] = $lossPressure;	
						$selectionData['rasCoeficientDiffuser'] = $rasCoeficientDiffuser;
						$selectionData['trCoeficientDiffuser'] = $trCoeficientDiffuser;
						$selectionData['lossPressureDiffuser'] = $lossPressureDiffuser;

						$selectionData['lossPressureSum'] = $lossPressureSum;
						$selectionData['lossPressureSumCompl'] = $lossPressureSumCompl;


						//$selectionData['trCoeficientDiffuser'] = $trCoeficientDiffuser;
							
					
						//dump($selectionData);
						if($lossPressureSum <= 0.5) {
							$selectionData['cid'] = $valueFlow['id'];
							$selectionData['catalog'] = $valueFlow;
							$selectionData['title'] = 'Питерфлоу РС'.$attribute['4'].'-'.$attribute['189'];
							$selectionData['attribute'] = $attribute;
							$selectionData['parametr']['rashodomer_excel'] = 'РС'.$attribute['4'].'-'.$attribute['189'];
							$selectionData['parametr']['konfuz'] = $this->prepareData['dn'].'-'.$this->prepareData['dnFlowMin'];
							$selectionData['parametr']['diffuz'] = $this->prepareData['dn'].'-'.$this->prepareData['dumpt'];

							return $selectionData;
						}
						//exit;
					}
				}										
			} else {

			}
			$this->prepareData['dnFlowMinKey']++;
			$this->prepareData['dnFlowMin'] = $this->dataLibrary->getData('Diameter')['dn'][$this->prepareData['dnFlowMinKey']];
			if ($i>8){
				echo 'error';
				break;
			}	
		} while (( $lossPressureSum > 0.5));		
	}
}
?>