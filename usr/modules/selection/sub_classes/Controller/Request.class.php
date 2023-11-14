<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Selection\Controller;
use \Calculation\Utilities\UtilitiesTool as UtilitiesTool;
use \Calculation\Library\DataLibrary as DataLibrary;
use \Calculation\BlockCalculation as BlockCalculation;
use \Selection\Model\SelectionRequest as SelectionRequest;
	
class Request {
	//данные
	protected $requestId = false;
	public $requestData = array();
	protected $blocksArray = array();

	//конструктор класса
	function __construct(){		
		$this->UtilitiesTool = new UtilitiesTool();
		$this->DataLibrary = new DataLibrary(); 
		$this->BlockCalculation = new BlockCalculation(); 
	}

	public function setRequestId($id){
		$this->requestId = intval($id);
	}
	
	public function getSelectionRequest() {
		$this->requestData = SelectionRequest::find($this->requestId)->toArray();	
	}

	public function getCalculationData(){
		$this->BlockCalculation->getRequsetDataFromSQL($this->requestId, true);
		$this->blocksArray = $this->BlockCalculation->prepareResult();
	}

	

	//Структурирование входных данных
	public function getInputData() {
		foreach ($this->blocksArray as $key => $value) {			
			switch($value['type']) {				
				case "BV":
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Нагрузка',
						'value' => $value['blockData']['q']['value'],
						'sub' => $value['blockData']['q']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'P1',
						'value' => $this->BlockCalculation->requestData['mainData']['p1']['value'],
						'sub' => $this->BlockCalculation->requestData['mainData']['p1']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'P2',
						'value' => $this->BlockCalculation->requestData['mainData']['p2']['value'],
						'sub' => $this->BlockCalculation->requestData['mainData']['p2']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'T1',
						'value' => $this->BlockCalculation->requestData['mainData']['t1']['value'],
						'sub' => $this->BlockCalculation->requestData['mainData']['t1']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'T2',
						'value' => $this->BlockCalculation->requestData['mainData']['t2']['value'],
						'sub' => $this->BlockCalculation->requestData['mainData']['t2']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Узел учета',
						'value' => ($value['blockData']['uzel_ucheta']) ? 'Да' : 'Нет',
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Регулятор давления на T1',
						'value' => ($value['blockData']['regul_dav_t1']) ? 'Да' : 'Нет',
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Регулятор давления на T2',
						'value' => ($value['blockData']['regul_dav_t2']) ? 'Да' : 'Нет',
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Балансировочный клапан',
						'value' => ($value['blockData']['bal_klapan']) ? 'Да' : 'Нет',
					);					
				break;	
				case "BOV":
					$this->blocksArray[$key]['inputData']['q'] = array(
						'name' => 'Нагрузка',
						'value' => $value['blockData']['q']['value'],
						'sub' => $value['blockData']['q']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'P1',
						'value' => $this->BlockCalculation->requestData['mainData']['p1']['value'],
						'sub' => $this->BlockCalculation->requestData['mainData']['p1']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'P2',
						'value' => $this->BlockCalculation->requestData['mainData']['p2']['value'],
						'sub' => $this->BlockCalculation->requestData['mainData']['p2']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'T1',
						'value' => $this->BlockCalculation->requestData['mainData']['t1']['value'],
						'sub' => $this->BlockCalculation->requestData['mainData']['t1']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'T2',
						'value' => $this->BlockCalculation->requestData['mainData']['t2']['value'],
						'sub' => $this->BlockCalculation->requestData['mainData']['t2']['type'],
					);
					$this->blocksArray[$key]['inputData']['t11'] = array(
						'name' => 'T11',
						'value' => $value['blockData']['t11']['value'],
						'sub' => $value['blockData']['t11']['type'],
					);
					$this->blocksArray[$key]['inputData']['t21'] = array(
						'name' => 'T21',
						'value' => $value['blockData']['t21']['value'],
						'sub' => $value['blockData']['t21']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Потери давления',
						'value' => $value['blockData']['dp']['value'],
						'sub' => $value['blockData']['dp']['type'],
					);
					$this->blocksArray[$key]['inputData']['h'] = array(
						'name' => 'Высота',
						'value' => $value['blockData']['h'],
						'sub' => 'м.',
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Контролер',
						'value' => $value['blockData']['kontr'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Регулирующий клапан',
						'value' => $value['blockData']['regKlapan'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Регулирующий клапан',
						'value' => $value['blockData']['bov_type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Насос',
						'value' => $value['blockData']['nasos_type'],
						'sub' => ($value['blockData']['pch']) ? 'С преобразователем частоты' : 'Без преобразователя частоты',
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Расположение насоса',
						'value' => $value['blockData']['ras_nasos'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Количество теплообменноков',
						'value' => $value['blockData']['teplo'],
						'sub' => 'шт.',
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Распределение нагрузки',
						'value' => ($value['blockData']['teploPr'] == 1) ? '100%' : (($value['blockData']['teploPr'] == 2) ? '75%' : '50%'),
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Расположение р. темп.',
						'value' => $value['blockData']['reg_t'],
					);
					$this->blocksArray[$key]['inputData']['typeBlock'] = array(
						'name' => 'Назначение блока',
						'value' => ($value['blockData']['typeBlock'] == 1) ? 'Отопление' : (($value['blockData']['teploPr'] == 2) ? 'Вентиляция' : 'Технологии'),
					);
				break;
				case "BP":
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Нагрузка',
						'value' => $value['blockData']['q']['value'],
						'sub' => $value['blockData']['q']['type'],
					);
					$this->blocksArray[$key]['inputData']['t11'] = array(
						'name' => 'T11',
						'value' => $value['blockData']['t11']['value'],
						'sub' => $value['blockData']['t11']['type'],
					);
					$this->blocksArray[$key]['inputData']['t21'] = array(
						'name' => 'T21',
						'value' => $value['blockData']['t21']['value'],
						'sub' => $value['blockData']['t21']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Высота',
						'value' => $value['blockData']['h'],
						'sub' => 'м.',
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Насосная станция',
						'value' => ($value['blockData']['nasosStan'] == '2') ? 'Да' : 'Нет',
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Повысительный насос',
						'value' => $value['blockData']['povNasos'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Клапан соленоидный',
						'value' => $value['blockData']['klapanSol'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Расширительный бак',
						'value' => $value['blockData']['rasshirBak'],
					);					
				break;	
				case "BGV":
					$this->blocksArray[$key]['inputData']['q'] = array(
						'name' => 'Нагрузка',
						'value' => $value['blockData']['q']['value'],
						'sub' => $value['blockData']['q']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'P1',
						'value' => $this->BlockCalculation->requestData['mainData']['p1']['value'],
						'sub' => $this->BlockCalculation->requestData['mainData']['p1']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'P2',
						'value' => $this->BlockCalculation->requestData['mainData']['p2']['value'],
						'sub' => $this->BlockCalculation->requestData['mainData']['p2']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'T1',
						'value' => $this->BlockCalculation->requestData['mainData']['t1']['value'],
						'sub' => $this->BlockCalculation->requestData['mainData']['t1']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'T2',
						'value' => $this->BlockCalculation->requestData['mainData']['t2']['value'],
						'sub' => $this->BlockCalculation->requestData['mainData']['t2']['type'],
					);
					$this->blocksArray[$key]['inputData']['t3'] = array(
						'name' => 'T3',
						'value' => $value['blockData']['t3']['value'],
						'sub' => $value['blockData']['t3']['type'],
					);
					$this->blocksArray[$key]['inputData']['tb1'] = array(
						'name' => 'Tв1 ',
						'value' => $value['blockData']['t4']['value'],
						'sub' => $value['blockData']['t4']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Pв1 ',
						'value' => $value['blockData']['pb1']['value'],
						'sub' => $value['blockData']['pb1']['type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Распределение нагрузки',
						'value' => ($value['blockData']['teploPr'] == 1) ? '100%' : (($value['blockData']['teploPr'] == 2) ? '75%' : '50%'),
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Тип схемы',
						'value' => $value['blockData']['gvs_type'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Регулирующий клапан',
						'value' => $value['blockData']['regKlapanGVS'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Кол-во теплообменников',
						'value' => $value['blockData']['teplo'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Расположение р. темп.',
						'value' => $value['blockData']['reg_t'],
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Распределение нагрузки',
						'value' => ($value['blockData']['teploPr'] == 1) ? '100%' : (($value['blockData']['teploPr'] == 2) ? '75%' : '50%'),
					);
					$this->blocksArray[$key]['inputData'][] = array(
						'name' => 'Насос',
						'value' => $value['blockData']['nasos_type'],
					);
				break;	
			}
		}
		return $this->blocksArray;
	}



	public function getResultAdminTemplates() {
		global $adminTpl;
		$id=$this->requestId;
		$lnn = 'Информация о блоке';
		$adminTpl->admin_head('Блок | '.$lnn);
		echo '<div id="content" class="animated fadeIn">
		<div class="row">
			<div class="col-md-9">
				<div class="row">';
				$data = \Selection\Model\SelectionRequest::find($id)->toArray();
				if($data["daemon"] == 1 && !empty($data["resultData"])){			
					// dd($this->blocksArray );
					foreach ($this->blocksArray as $key => $value) {	
						echo '<div class="col-md-12">
									<div class="panel panel-dark panel-border top">
										<div class="panel-heading"><span class="panel-title"><strong>'. $value['name'] .'</strong></span></div>			
									
									<div class="panel-body pn">
										<div class="p5">
										<strong>Входные данные:</strong>
										<div class="alert alert-info alert-lighter mt-5 mb-5">
										<div class="row">';
										// dd($value['blockData'] );
										if(isset($value['inputData'])) {
											foreach ($value['inputData'] as $k => $v) {
												echo '<div class="col-md-4"><strong>'.$v['name'].': </strong>'.$v['value'].(isset($v['sub']) ? ' '.$v['sub'] : '').'</div>';
											}
										} else {
											echo 'Данные отсутствуют';
										}
										
								echo '</div></div>
								<strong>Основное оборудование:</strong>
								
								</div><div class="table-responsive">
					<form id="tablesForm">
						<table class="table table-striped">
							<thead>
								<tr>
									<th class="w40"><span class="pd-l-sm"></span>№</th>
									<th class="w100">Артикул</th>
									<th class="w500">Наименование</th>
									<th class="w50">Производитель</th>
									<th class="w100">Кол-во</th>
									<th class="w50">Ед.</th>
									<th class="w40 text-center">Статус</th>
									<th class="w200 text-right">Действия</th>
									<th class="w20 text-right">
										<div class="checkbox-custom mb15">
											<input id="all" type="checkbox" name="all"
												onclick="setCheckboxes(\'tablesForm\', true); return true;" class="checks">
											<label for="all"></label>
										</div>
									</th>
								</tr>
							</thead>
							<tbody id="tov_list">';
							$template = '';
							$i = 0;
							$total = 0;
							foreach ($value['spec'] as $sKey => $sValue) {
								$i++;
								$url = config('app.url').'/catalog_v2/fast_view_my/'.$sValue['tm_cid'];
								if($sValue['tm_aid'] != '0') {
									$url = config('app.url').'/download/'.$sValue['tm_aid'];
								}
								$price = 0;
								// dd($value);
								if(isset($value['price']['priceArray'][$sValue['tm_cid']])){
									$price = $value['price']['priceArray'][$sValue['tm_cid']];
								}
								$total = $total+$price*$sValue['tm_count'];
								$template .= '<tr>
												<td>'.$i.'</td>
												<td>'.ee($sValue['tm_title']).'</td>
												<td>'.ee($sValue['tm_model']).'</td>
												<td>'.$sValue['tm_sku'].'</td>
												<td>'.$sValue['tm_manufacter'].'</td>
												<td>'.$sValue['tm_unit'].'</td>
												<td>'.$sValue['tm_count'].'</td>
												<td>'.$sValue['tm_weight'].'</td>
											
												<td><a data-fancybox data-type="iframe" data-width="2048" data-src="'.$url.'" href="javascript:;" class="btnClassic">Каталог</a></td>
											</tr>';
							}	
							echo  $template;
														echo '</tbody>
							<tfoot class="footer-menu">
								<tr align="right">
									<td align="right" colspan="12">
										<nav align="right" class="text-right">
											<select style="width: 250px; display: inline-block;" class="form-control" name="act">
												<option value="del">Удалить выбранное</option>
											</select>
											<input name="submit" type="button" class="btn btn-success"
												style="display: inline-block;" id="sub" value="Выполнить" /><span
												class="pd-l-sm"></span>
										</nav>
									</td>
								</tr>
							</tfoot>
						</table>
					</form>
				</div> </div>
								</div></div>';
					}
				}
				
		echo    '
				</div>
			</div>
			<div class="col-md-3">
				<div class="panel panel-dark panel-border top">
					<div class="panel-heading"><span class="panel-title">'. $lnn .'</span></div>	
					<a target="_blank" href="/administration/module/selection/create/sales/'.$id.'">Создать продажу</a>		
				</div>
			</div>
		</div>
			';
		echo '</div>';
		$adminTpl->footIncludes[] = '<script src="/usr/modules/selection/admin/assets/js/request.js"></script>';
		$adminTpl->admin_foot();
	}



	
}	
	
?>