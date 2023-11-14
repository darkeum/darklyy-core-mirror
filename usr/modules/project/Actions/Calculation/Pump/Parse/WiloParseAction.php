<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Heat Calculation class
*/

namespace Project\Actions\Calculation\Pump\Parse;

use GuzzleHttp\Client;
use \Calculation\Utilities\UtilitiesTool as UtilitiesTool;
use \Calculation\Library\HeatLibrary as HeatLibrary;
use \Boot\Models\Attach;
use \Boot\Support\Str;
use \Calculation\Model\HeatSolution;
use \Catalog_v2\Model\Catalog;
use \Price\Models\CatalogPrice;
use \Price\Models\CatalogPriceDetail;

class WiloParseAction
{
	//данные клиента
	public $client;
	public $cookieSession;
	public $token = null;
	public $state = null;
	public $logClient = array();

	//конструктор класса
	function __construct()
	{
		$this->UtilitiesTool = new UtilitiesTool();
		$this->HeatLibrary = new HeatLibrary();
	}

	//инициализация клиента
	public function initClient()
	{
		$this->client = new Client([
			'base_uri' => '',
			'verify'  => false,
			'allow_redirects' => true,
			'cookies' => true,
			'headers' => [
				'User-Agent'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36',
				'Accept'       => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Upgrade-Insecure-Requests' => '1',
				'Origin' => 'https://wilo-select.com/',
			]
		]);
	}

	/**
	 * Получение данных по заданным параметрам
	 *
	 * @param 	string    			$method Метод запроса (GET|POST)
	 * @param 	string    			$url Ссылка на которую нажно отправить запрос
	 * @param 	array     			$form Передаваемые данные
	 * @param 	string|bool|null    $token Токен для запроса, если null будет создан автоматически, если false, не будет подставлятся
	 * @param 	array     			$params Дополнительные параметры запроса
	 * @return 	int 	  			Возвращает полученные данные 
	 */
	protected function getContent($method, $url, $form = [], $token = null, $params = [])
	{
		$headers = [
			'Cookie' => $this->cookieSession,
			'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
		];
		$headers['accept'] = 'application/json, text/javascript, */*; q=0.01';
		if (isset($params['accept'])) {
			$headers['accept'] = $params['accept'];
		}
		if ($token !== false) {
			if (empty($token)) {
				if (empty($this->token)) {
					$token = $this->getTokenPage();
				} else {
					$token = $this->token;
				}
			}
			$form['__csrfToken']	= $token;
			$headers['X-CSRF-TOKEN'] = $token;
		}
		try {
			$getResult = $this->client->request($method, $url, [
				'headers' => $headers,
				'form_params' => $form,
				'debug' => false
			]);
			$result = $getResult->getBody()->getContents();
		} catch (\Exception $e) {
			// $result = $e->getResponse()->getBody()->getContents();
		}
		return $result;
	}

	//получение особый TimeStamp  для Wilo
	private function getTimeStamp()
	{
		return date('j') . time();
	}

	//получение токена
	private function getToken($data)
	{
		$token = stristr($data, 'window.__csrfToken = "');
		$token = str_replace('window.__csrfToken = "', "", $token);
		$token = stristr($token, '";', true);
		$this->token = $token;
		return $this->token;
	}

	//получение state
	private function getState($data)
	{
		$state = stristr($data, 'name="__VIEWSTATE" id="__VIEWSTATE" value="');
		$state = str_replace('name="__VIEWSTATE" id="__VIEWSTATE" value="', "", $state);
		$state = stristr($state, '" />', true);

		$stateGenerator = stristr($data, 'name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="');
		$stateGenerator = str_replace('name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="', "", $stateGenerator);
		$stateGenerator = stristr($stateGenerator, '" />', true);
		$this->state = [
			'state' => $state,
			'stateGenerator' => $stateGenerator,
		];
		return $this->state;
	}

	//получение токена со страницы
	private function getTokenPage($page = 'https://wilo-select.com/Region.aspx')
	{
		$getFormPage = $this->client->request('GET', $page, [
			'headers' => [
				'Cookie' => $this->cookieSession,
			],
		]);
		$bodyFormPage = $getFormPage->getBody()->getContents();
		$token = $this->getToken($bodyFormPage);
		return $token;
	}

	//авторизация
	public function initAuth()
	{
		//Получаем токен 
		$token = $this->getTokenPage('https://wilo-select.com/Region.aspx');
		//Выбираем регион
		$post_data = array(
			'RequestedHandler' 	=> 'WiloRegionSelector',
			'action' 	=> 'SelectRegion',
			'selectedRegion' 	=> 'NTIyNWJjMjgtMWRmYy00Mzc4LTgxMDEtZGFlZjg3YjUxOTEy',
		);
		$region = $this->getContent('GET', 'https://wilo-select.com/LggHandler.vhd', $post_data);
		//переходим на главную (нужно для цепочки)
		$start = $this->getContent('GET', 'https://wilo-select.com/StartMain.aspx');
		//меняем язык на русский
		$post_data = array(
			'RequestedHandler' => 'LggHandler',
			'lgg' => 'RUSSIAN',
			'action' => 'CHANGELANGUAGE',
		);
		$lng = $this->getContent('GET', 'https://wilo-select.com/LggHandler.vhd', $post_data);
		//делаем служебную отметку (нужно для цепочки)
		$post_data = array(
			'RequestedHandler' => 'StateMachineHandler',
			'Trigger' => '3E2969F5-B89F-448c-BCC9-00513B0F3A59',
			'action' => 'Trigger',
		);
		$trigger = $this->getContent('GET', 'https://wilo-select.com/StateMachineHandler.vhd', $post_data);
		//получаем ViewState
		$statePrepare = $this->getContent('GET', 'https://wilo-select.com/PumpProdConfig.aspx', []);
		$state = $this->getState($statePrepare);
		//делаем служебную отметку (нужно для цепочки)
		$post_data = array(
			'RequestedHandler' => 'DutyPointHandler',
			'ident' => 'GETTREE',
		);
		$point = $this->getContent('GET', 'https://wilo-select.com/DutyPointHandler.vhd', $post_data);
		//делаем служебную отметку (нужно для цепочки)	
		$post_data = array(
			'RequestedHandler' => 'StateMachineHandler',
			'action' => 'CurrentState',
		);
		$current = $this->getContent('GET', 'https://wilo-select.com/StateMachineHandler.vhd', $post_data);
		//указываем что нужно получить пустой список насосов (нужно для цепочки)	
		$post_data = array(
			'RequestedHandler' => 'PumpConfigResultHandler',
			'action' => 'GETCBVALUES',
			'key' => 'ALL',
		);
		$setAll = $this->getContent('POST', 'https://wilo-select.com/PumpConfigResultHandler.vhd', $post_data);
		//получаем пустой список насосов (нужно для цепочки)	
		$post_data = array(
			'RequestedHandler' => 'PumpConfigResultHandler',
			'action' => 'GET',
		);
		$getAll = $this->getContent('POST', 'https://wilo-select.com/PumpConfigResultHandler.vhd', $post_data);
	}

	public function getPumpSeries($value)
	{
		//задаем серию для получения
		$post_data = array(
			'RequestedHandler' => 'PumpConfigResultHandler',
			'action' => 'SET',
			'key' => 'SERIES',
			'val' => $value
		);
		$setSeries = $this->getContent('POST', 'https://wilo-select.com/PumpConfigResultHandler.vhd', $post_data);
		//делаем служебную отметку (нужно для цепочки)	
		$post_data = array(
			'RequestedHandler' => 'PumpConfigResultHandler',
			'action' => 'GETCBVALUES',
			'key' => 'PRODGRP',
		);
		$point1 = $this->getContent('POST', 'https://wilo-select.com/PumpConfigResultHandler.vhd', $post_data);
		//делаем служебную отметку (нужно для цепочки)	
		$post_data = array(
			'RequestedHandler' => 'PumpConfigResultHandler',
			'action' => 'SET',
			'key' => 'PRODGRP',
			'val' => '-',
		);
		$point2 = $this->getContent('POST', 'https://wilo-select.com/PumpConfigResultHandler.vhd', $post_data);
		//задаем параметры для отображения
		$post_data = array(
			'MultiPageDimens_ClientState' => '',
			'MultiPageLCC_Page_ClientState' => '',
			'MultiPageResults_ClientState' => '{"selectedIndex":-1,"changeLog":[]}',
			'MultiPageRunCost_Page_ClientState' => '',
			'MultiPageVDI_Page_ClientState' => '',
			'RadAJAXControlID' => 'RadAjaxManager1',
			'RadScriptManager1' => 'RadAjaxManager1SU|RadAjaxManager1',
			'RadScriptManager1_TSM' => ';;System.Web.Extensions, Version=3.5.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35:en-US:16997a38-7253-4f67-80d9-0cbcc01b3057:ea597d4b:b25378d2;Telerik.Web.UI, Version=2010.2.827.35, Culture=neutral, PublicKeyToken=29ac1a93ec063d92:en-US:9e9bdc2c-515c-4639-b9cb-05c0c3c80476:16e4e7cd:f7645509:24ee1bba:1e771326:aa288e2d:6a6d718d:4cacbc31:58366029:874f8ea2:19620875:39040b5c:e330518b:c8618e41:e4f8f289:e524c98b:8e6f0d33:490a9d4e:ed16cbdc:bd8f85e4;',
			'SessionTimedOutWindow_ClientState' => '',
			'SessionTimeoutNearWarningWindow_ClientState' => '',
			'TabCtrlLCC_Page_ClientState' => '{"selectedIndexes":["0"],"logEntries":[],"scrollState":{}}',
			'TabCtrlRunCost_ClientState' => '{"selectedIndexes":["0"],"logEntries":[],"scrollState":{}}',
			'TabCtrlVDI_Page_ClientState' => '{"selectedIndexes":["0"],"logEntries":[],"scrollState":{}}',
			'_ChangedPrecValues' => '',
			'__ASYNCPOST' => 'true',
			'__EVENTARGUMENT' => 'SettingsChanged',
			'__EVENTTARGET' => 'RadAjaxManager1',
			'__VIEWSTATE' => $this->state['state'],
			'__VIEWSTATEGENERATOR' => $this->state['stateGenerator'],
			'cbLccSettings_Page' => '',
			'cbLccSettings_Page_ClientState' => '{"logEntries":[],"value":"","text":"","enabled":true}',
			'ddDim_Abflussfl' => '2',
			'ddDim_Abflusssp' => '3',
			'ddDim_Abm' => '1',
			'ddDim_Dicht' => '2',
			'ddDim_Drehm' => '2',
			'ddDim_Druck' => '9',
			'ddDim_FoerdH' => '2',
			'ddDim_FoerdS' => '8',
			'ddDim_Freq' => '0',
			'ddDim_GasStr' => '6',
			'ddDim_Geschw' => '1',
			'ddDim_MEDDV' => '4',
			'ddDim_MEDSV' => '3',
			'ddDim_MStr' => '1',
			'ddDim_M_JM' => '0',
			'ddDim_Percent' => '0',
			'ddDim_Power' => '6',
			'ddDim_Speed' => '0',
			'ddDim_Temp' => '0',
			'ddDim_Viskk' => '4',
			'ddDim_Volumenprozent' => '0',
			'ddDim_Weight' => '1',
			'ddDutypointList' => 'A1  [Q: 0m³/h  /  H: 0m]',
			'ddDutypointList_ClientState' => '{"logEntries":[],"value":"","text":"A1  [Q: 0m³/h  /  H: 0m]","enabled":false}',
			'ddLang' => '中文',
			'ddLang_ClientState' => '{"logEntries":[],"value":"CHINESE","text":"中文","enabled":true}',
			'dd_ProdTxtType' => '',
			'dd_ProdTxtType_ClientState' => '{"logEntries":[],"value":"","text":"","enabled":true}',
			'dimensHdField' => '',
			'dispTypeInch' => '1',
			'dutyChartWindow$C$View' => 'rb_All',
			'dutyChartWindow$C$ddDutyChart' => '',
			'dutyChartWindow_C_DCPrevPumpGrid_ClientState' => '',
			'dutyChartWindow_C_DCPrevSeriesGrid_ClientState' => '',
			'dutyChartWindow_C_ddDutyChart_ClientState' => '{"logEntries":[],"value":"","text":"","enabled":false}',
			'dutyChartWindow_ClientState' => '',
			'gridPump_ClientState' => '{"selectedIndexes":["0"],"reorderedColumns":[],"expandedItems":[],"expandedGroupItems":[],"expandedFilterItems":[],"deletedItems":[],"hidedColumns":[],"showedColumns":[],"scrolledPosition":"0,0","popUpLocations":{},"draggedItemsIndexes":[]}',
			'gridPump_ctl00_ctl02_ctl00_ctl00_ClientState' => '',
			'gridPump_ctl00_ctl02_ctl00_ctl02_ClientState' => '',
			'gridPump_ctl00_ctl02_ctl00_ctl09_ClientState' => '',
			'gridPump_ctl00_ctl02_ctl00_ctl16_ClientState' => '',
			'gridPump_ctl00_ctl02_ctl00_ctl23_ClientState' => '',
			'gridPump_ctl00_ctl02_ctl00_ctl30_ClientState' => '',
			'gridPump_ctl00_ctl02_ctl00_ctl37_ClientState' => '',
			'gridPump_ctl00_ctl02_ctl00_ctl44_ClientState' => '',
			'gridPump_ctl00_ctl02_ctl00_ctl51_ClientState' => '',
			'gridPump_ctl00_ctl02_ctl00_ctl58_ClientState' => '',
			'gridPump_ctl00_ctl02_ctl00_ctl65_ClientState' => '',
			'gridPump_ctl00_ctl02_ctl00_ctl72_ClientState' => '',
			'gridPump_ctl00_ctl02_ctl00_ctxMenu_PumpGridColumns_ClientState' => '',
			'menu_AddInfo_ClientState' => '',
			'menu_Diagram_ClientState' => '',
			'menu_Diagram_DRV_ClientState' => '',
			'menu_Diagram_PCo_ClientState' => '',
			'pnlBar_Material_ClientState' => '',
			'pnlBar_Optim_ClientState' => '',
			'pnlBar_SelectionType$i0$i0$ddFreq' => '50 Hz',
			'pnlBar_SelectionType$i0$i0$ddHerst' => 'Wilo',
			'pnlBar_SelectionType$i0$i0$ddProdGrp' => '-',
			'pnlBar_SelectionType$i0$i0$ddSeries' => $value,
			'pnlBar_SelectionType_ClientState' => '{"expandedItems":["0"],"logEntries":[],"selectedItems":[]}',
			'pnlBar_SelectionType_i0_i0_ddFreq_ClientState' => '{"logEntries":[],"value":"","text":"50 Hz","enabled":true}',
			'pnlBar_SelectionType_i0_i0_ddHerst_ClientState' => '{"logEntries":[],"value":"","text":"Wilo","enabled":true}',
			'pnlBar_SelectionType_i0_i0_ddProdGrp_ClientState' => '{"logEntries":[],"value":"","text":"-","enabled":true}',
			'pnlBar_SelectionType_i0_i0_ddSeries_ClientState' => '{"logEntries":[],"value":"","text":"' . $value . '","enabled":true}',
			'prodTxtHdField' => '',
			'rwm_ClientState' => '',
			'tree_Dimension_ClientState' => '{"expandedNodes":[],"collapsedNodes":[],"logEntries":[],"selectedNodes":[],"checkedNodes":[],"scrollPosition":0}',
			'tree_ProdTxt_ClientState' => '{"expandedNodes":[],"collapsedNodes":[],"logEntries":[],"selectedNodes":[],"checkedNodes":[],"scrollPosition":0}',
			'tt_FluidAndDutyPoint_ClientState' => '',
			'windowAdditionalSystemCurves_C_menuStyleCurve_ClientState' => '',
			'windowAdditionalSystemCurves_ClientState' => '',
			'windowChangeCurveName$C$txtName' => '',
			'windowChangeCurveName_ClientState' => '',
			'windowEditApplicationRange$C$ddListApplRange' => '',
			'windowEditApplicationRange$C$txtMaxAppl' => '',
			'windowEditApplicationRange$C$txtMinAppl' => '',
			'windowEditApplicationRange_C_ddListApplRange_ClientState' => '{"logEntries":[],"value":"","text":"","enabled":true}',
			'windowEditApplicationRange_ClientState' => '',
			'windowLccSettings$C$windowLccProfile$C$cbLccProfiles' => '蓝天使',
			'windowLccSettings$C$windowLccSettings_Create$C$txtCreate' => '',
			'windowLccSettings$C$windowLccSettings_Rename$C$txtRename' => '',
			'windowLccSettings_C_TreeObjCollection_ClientState' => '{"expandedNodes":[],"collapsedNodes":[],"logEntries":[],"selectedNodes":[],"checkedNodes":[],"scrollPosition":0}',
			'windowLccSettings_C_windowLccProfile_C_cbLccProfiles_ClientState' => '{"logEntries":[],"value":"BlauerEngelProfil","text":"蓝天使","enabled":true}',
			'windowLccSettings_C_windowLccProfile_ClientState' => '',
			'windowLccSettings_C_windowLccSettings_Create_ClientState' => '',
			'windowLccSettings_C_windowLccSettings_Delete_ClientState' => '',
			'windowLccSettings_C_windowLccSettings_Rename_ClientState' => '',
			'windowLccSettings_ClientState' => '',
			'windowOP$C$selectRatedOPPoint' => '',
			'windowOP_C_selectRatedOPPoint_ClientState' => '{"logEntries":[],"value":"","text":"","enabled":true}',
			'windowOP_ClientState' => '',
			'windowOptimizeStages_ClientState' => '',
			'windowPerformanceCurveShorten$C$chkForAllCurves' => 'on',
			'windowPerformanceCurveShorten$C$txtMax' => '',
			'windowPerformanceCurveShorten$C$txtMin' => '',
			'windowPerformanceCurveShorten_ClientState' => '',
			'windowRunCostDiagramMagnifier_ClientState' => '',
			'windowRunningCosts$C$cbLccSettings' => '',
			'windowRunningCosts_C_MultiPageLCC_ClientState' => '',
			'windowRunningCosts_C_MultiPageRunCost_ClientState' => '',
			'windowRunningCosts_C_MultiPageVDI_ClientState' => '',
			'windowRunningCosts_C_TabCtrlLCC_ClientState' => '{"selectedIndexes":["0"],"logEntries":[],"scrollState":{}}',
			'windowRunningCosts_C_TabCtrlRunCost_ClientState' => '{"selectedIndexes":["0"],"logEntries":[],"scrollState":{}}',
			'windowRunningCosts_C_TabCtrlVDI_ClientState' => '{"selectedIndexes":["0"],"logEntries":[],"scrollState":{}}',
			'windowRunningCosts_C_cbLccSettings_ClientState' => '{"logEntries":[],"value":"","text":"","enabled":true}',
			'windowRunningCosts_ClientState' => '',
			'wndStdDlg_ClientState' => ''
		);
		$setView = $this->getContent('POST', 'https://wilo-select.com/PumpProdConfig.aspx', $post_data);
		//получаем список насосов этой серии
		$post_data = array(
			'RequestedHandler' => 'PumpConfigResultHandler',
			'action' => 'GET',
		);
		$pumpSeries = json_decode($this->getContent('POST', 'https://wilo-select.com/PumpConfigResultHandler.vhd', $post_data));
		//делаем служебную отметку (нужно для цепочки)	
		$post_data = array(
			'RequestedHandler' => 'DutyPointHandler',
			'ident' => 'FROMPIPECALC',
			'TimeStamp' => $this->getTimeStamp(),
		);
		$point3 = $this->getContent('GET', 'https://wilo-select.com/DutyPointHandler.vhd', $post_data);
		$this->pumpSeries = $pumpSeries;
		return $pumpSeries;
	}


	public function getPumps($row = null)
	{
		if (empty($row)) {
			$i = 0;
			foreach ($this->pumpSeries->data as $k => $v) {
				$i++;
				if ($i == 5) {
					break;
				}
				$xID = $v->xID;
				$this->getPumpData($xID);
			}
		} else {
			if (isset($this->pumpSeries->data[$row])) {
				$xID = $this->pumpSeries->data[$row]->xID;
				$this->getPumpData($xID);
			}
		}
	}



	public function getPumpData($xID)
	{
		$request = [];
		//выбираем нужный насос
		$post_data = array(
			'RequestedHandler' => 'SelectPumpHandler',
			'action' => 'selectPump',
			'xid' => $xID,
		);
		$setPump = json_decode($this->getContent('POST', 'https://wilo-select.com/SelectPumpHandler.vhd', $post_data));
		//получаем данные насоса
		$post_data = array(
			'RequestedHandler' => 'PumpCfgTableHandler',
			'Option' => 'GETTREE',
			'canedit' => 'true',
			'TimeStamp' => $this->getTimeStamp(),
		);
		$pumpData = json_decode($this->getContent('POST', 'https://wilo-select.com/PumpCfgTableHandler.vhd', $post_data));
		// dump($pumpData);
		//получаем описание и артикул

		//переходим на страницу текста
		$post_data = array(
			'RequestedHandler' => 'ResultNaviHandler',
			'action' => 'SET',
			'page' => 'PRODUCTTEXT',
			'addinfoidx' => '-1',
		);
		$pageText = json_decode($this->getContent('POST', 'https://wilo-select.com/ResultNaviHandler.vhd', $post_data));
		//Получаем hid и sid для получения текста
		$post_data = array(
			'RequestedHandler' => 'ResultProductTxtHandler',
			'action' => 'GET',
		);
		$pumpText = json_decode($this->getContent('POST', 'https://wilo-select.com/ResultProductTxtHandler.vhd', $post_data));
		//получаем артикул
		$post_data = array(
			'RequestedHandler' => 'ProductTextHandler',
			'PTID' => $pumpText->cbData[1]->HandlerID,
			'TSID' => $pumpText->cbData[1]->SourceID,
			'TT' => $pumpText->cbData[1]->Value,
			'TLGG' => $pumpText->cbData[1]->Text,
			'action' => 'get',
		);
		$pumpShortText = $this->getContent('POST', 'https://wilo-select.com/ProductTextHandler.vhd', $post_data);
		$sku = stristr($pumpShortText, '<paragraph tabs="1"><block><span style="font-size:8pt;color:#000000;font-weight:bold;font-family:Verdana;white-space: pre;">&#1053;&#1086;&#1084;&#1077;&#1088; &#1087;&#1086;&#1079;&#1080;&#1094;&#1080;&#1080;</span><span style="font-size:8pt;color:#000000;font-weight:bold;font-family:Verdana;"></span><span tab="0">&#65279;</span><span style="font-size:8pt;color:#000000;font-weight:bold;font-family:Verdana;">: </span><span style="font-size:8pt;color:#000000;font-weight:bold;font-family:Verdana;white-space: pre;">');
		$sku = str_replace('<paragraph tabs="1"><block><span style="font-size:8pt;color:#000000;font-weight:bold;font-family:Verdana;white-space: pre;">&#1053;&#1086;&#1084;&#1077;&#1088; &#1087;&#1086;&#1079;&#1080;&#1094;&#1080;&#1080;</span><span style="font-size:8pt;color:#000000;font-weight:bold;font-family:Verdana;"></span><span tab="0">&#65279;</span><span style="font-size:8pt;color:#000000;font-weight:bold;font-family:Verdana;">: </span><span style="font-size:8pt;color:#000000;font-weight:bold;font-family:Verdana;white-space: pre;">', "", $sku);
		$sku = stristr($sku, '</span><br /></block></paragraph><paragraph><block><br /></block><block><br /></block></paragraph>', true);
		$request['sku'] = $sku;
		// dump($sku);
		//получаем описание
		//получаем артикул
		$post_data = array(
			'RequestedHandler' => 'ProductTextHandler',
			'PTID' => $pumpText->cbData[0]->HandlerID,
			'TSID' => $pumpText->cbData[0]->SourceID,
			'TT' => $pumpText->cbData[0]->Value,
			'TLGG' => $pumpText->cbData[0]->Text,
			'action' => 'get',
		);
		$pumpFullText = html_entity_decode($this->getContent('POST', 'https://wilo-select.com/ProductTextHandler.vhd', $post_data));
		$pumpFullTextArray = explode('<block><br /></block>', $pumpFullText);

		$request['short'] = strip_tags($pumpFullTextArray[0]);
		$full = '';
		foreach ($pumpFullTextArray as $k => $v) {
			if (strripos($v, 'Эксплуатационные параметры') === false) {
				$full .= $v;
				unset($pumpFullTextArray[$k]);
			} else {
				break;
			}
		}
		$request['full'] = $full . '</paragraph>';
		$textBlocksArray = [];
		//получаем характеристики
		foreach ($pumpFullTextArray as $k => $v) {
			$simple = explode('</block>',  $v);
			$ar = [];
			$arok = [];
			foreach ($simple as $kk => $vv) {
				$arok = [];
				$ar = explode('</span><span style="font-size:8pt;color:#000000;font-family:Verdana;white-space: pre;">',  $vv);
				$arok[$kk]['name'] = strip_tags($ar[0]);
				unset($ar[0]);
				if (isset($ar[1])) {
					if (!empty(strip_tags((implode('', $ar))))) {
						$arok[$kk]['value'] = strip_tags((implode('', $ar)));
					}
				}
				if (isset($arok[$kk]['value'])) {
					$textBlocksArray[] = $arok;
				}
			}
		}
		$attr = [];
		foreach ($textBlocksArray as $k => $v) {
			foreach ($v as $kk => $vv) {
				$attr[] = $vv;
			}
		}
		$weight = 0;
		$length = 0;
		$attrClear = [];
		dd($attr);
		foreach ($attr as $k => $v) {
			switch ($v['name']) {
				case "Патрубок на всас. стороне DNs: ":
					$attrClear[] = [
						'key' => '133',
						'name' => $v['name'],
						'value' => $this->getDn($v['value']),
					];
					break;
				case "Патрубок на напорн. стороне DNd: ":
					$attrClear[] = [
						'key' => '131',
						'name' => $v['name'],
						'value' => $this->getDn($v['value']),
					];
					break;
				case "Корпус насоса: ":
					$attrClear[] = [
						'key' => '326',
						'name' => $v['name'],
						'value' => $v['value'],
					];
					break;
				case "Рабочее колесо: ":
					$attrClear[] = [
						'key' => '327',
						'name' => $v['name'],
						'value' => $v['value'],
					];
					break;
				case "Вал: ":
					$attrClear[] = [
						'key' => '328',
						'name' => $v['name'],
						'value' => $v['value'],
					];
					break;
				case "Монтажная длина: ":
					$va = explode(' ', $v['value']);
					$attrClear[] = [
						'key' => '207',
						'name' => $v['name'],
						'value' => $va[0],
					];
					$length = $va[0];
					break;
				case "Масса нетто прибл.: ":
					$va = explode(' ', $v['value']);					
					$weight = $va[0];
					break;
			}
		}
		$weightArray = ["1" => $weight, "2" => $length, "3" => '', "4" => '', "5" => "1"];

		$request['weight'] = $weightArray;
		$request['attr'] = $attrClear;
		dump($attr);
		dump($request);



		// 		HandlerID: "{50377130-1250-4016-AF61-CF56F2BBF5E7}"
		// SourceID: "{6A268697-92AD-44D4-B764-11AFC4744A00}"
		// Text: "Сокращенный текст"
		// Value: "SHORTTEXT"


		// //получаем файл с точками кривой
		// $pumpPoint = $this->getContent('GET', 'https://wilo-select.com/CurvePointsDocumentHandler.vhd', [], false);
		// $file = ROOT . 'test.xlsx';
		// file_put_contents($file, $pumpPoint);
		// //парсим файл с точками
		// $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
		// $cellValue = $spreadsheet->getActiveSheet()->getCell('B3')->getValue();
		// $i = 5;
		// $char  = [];
		// while (!empty($spreadsheet->getActiveSheet()->getCell('B' . $i)->getValue())) {
		// 	$char[$cellValue][] = $spreadsheet->getActiveSheet()->getCell('B' . $i)->getValue();
		// 	$i++;
		// }
		// dump($char);
		// unset($file);
	}

	public function getDn($value)
	{
		$va = explode(',', $value);
		switch ($va[0]) {
			default:
				$vaa = explode(' ', $va[0]);
				$result = $vaa[0];
				break;
			case "G 1":
				$result = 15;
				break;
			case "G 1¼":
				$result = 20;
				break;
			case "G 1½":
				$result = 25;
				break;
			case "G 2":
				$result = 32;
				break;
		}
		return  $result;
	}
}
