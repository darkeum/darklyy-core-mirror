<?php
global $db, $allowFuncs, $url, $core;
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
use Boot\Support\Currency;

if (isset($allowFuncs))
{
$allowFuncs = array_merge($allowFuncs, ['selectCode' ,'selectCodeFree', 'fileAxax', 'getMetering', 'getBlocksCalculation', "loadDataResult", "getBlocksCalculationTpl"]);
}

function getBlocksCalculationTpl()
{
	$bc = new \Calculation\BlockCalculation();
	$bc->getRequsetDataFromSQL('282', true);
	$t = $bc->getResultTemplate();
	echo $t;
}

function getBlocksCalculation()
{
	$bc = new \Calculation\BlockCalculation();
	$bc->getRequsetDataFromPost();
	// if($bc->checkData()) {
		$bc->saveDataRequest();
		\Selection\Model\SelectionRequest::key_create();
	// }
	
}

function loadDataResult(){
	$request = $_POST;
	$id = empty($request["id"]) ? false : $request["id"];
	$result = [
		"result" => false,
		"template" => null,
	];
	if($id){
		$data = \Selection\Model\SelectionRequest::find($id)->toArray();
		// dd($data);
		if($data["daemon"] == 1 && !empty($data["resultData"])){
			// try{
				$bc = new \Calculation\BlockCalculation();
				$bc->getRequsetDataFromSQL($id, true);
				$t = $bc->getResultTemplate();
				$result["result"] = json_decode($data["resultData"], true);
				$result["template"] = $t;

			// } catch(\Exception $e){
				//Ошибка
			// }
		}
	}
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
}

function dotsReplase($value)
{
	 return (!empty($value) ? str_replace('.', ',', $value) : 0);
}

function getMetering()
{
	global $core, $db;
	//print_r($_POST);
	$uid = $core->auth->user_info['id'];
	$dir = ROOT.'files/metering/output';
	if (!file_exists($dir))
	{
		mkdir($dir, 0777);						
	}
	$pr_numb = (!empty($_POST['number'])  ? $_POST['number'] : 'XXXXXX');
	$raz = (!empty($_POST['raz'])  ? $_POST['raz'] : '');
	$prov = (!empty($_POST['prov'])  ? $_POST['prov'] : '');
	$gip = (!empty($_POST['gip'])  ? $_POST['gip'] : '');
	$control = (!empty($_POST['control'])  ? $_POST['control'] : '');
	$object_id = (!empty($_POST['object_id'])  ? intval($_POST['object_id']) : '');
	$oid = (!empty($_POST['oid'])  ? intval($_POST['oid']) : '');
	
	$date = date('d.m.y');
	$system_list = array();	
	$inputDataJSON = json_decode($_POST['inputDataJSON'], true);
	//print_r($inputDataJSON);
	$system_data = $inputDataJSON['data'];
	
	$request_data_array = array();
	foreach ($_POST as $key => $value) 
	{
		if($key != 'inputDataJSON')
		{
			$request_data_array[$key] = htmlspecialchars($value);
		}
		else
		{
			$request_data_array[$key] = json_decode($value, true);
		}
	}
	$request_data = json_encode($request_data_array, JSON_UNESCAPED_UNICODE);
	$request = '';
	$db->query("INSERT INTO ".DB_PREFIX."_request (`uid_dir`, `title`, `request`, `request_data`, `object`, `oid`, `type`, `date`, `status`) VALUES ('".$uid."', 'Заявка на расчет узла учета', '".$request."', '".$request_data."', '".$object_id."', '".$oid."', '4', '".date("Y-m-d H:i:s")."', '1')");	
	$request_id = $db->insert_id();

	echo $request_id;
	

		$designers = getDesignersRequest();
		$attr = [];
		$attr['uid'] = $designers;
		$attr['title'] = 'Новая заявка на расчет узла учета';
		$attr['text'] = "Новая заявка на расчет узла учета";
		$attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
		$attr['url'] = config('app.url').'/administration/module/request/view/'.$request_id;
		$attr['pNotif'] = true;
		$attr['pPush'] = true;
		sendAllNotif($attr);
	
}


function getDetail($blocks_code, $scheme_code = '0-0-0-0-0-0')
{
		global $core, $db, $url;		
		$returnJson = array();
		$typeBlockArray = getTypeBlock();
		$typeBlockArrayEng = getTypeBlock(1);
		$group_code = '';
		if(is_array($blocks_code) && count($blocks_code) > 0){
			foreach ($blocks_code as $key => $value) 
			{
				foreach ($value as $kk => $vv) 
				{
					$scheme_array = getBlockInfo($vv['scheme_id']);
					$group_code .= $scheme_array['position'].'-';
					$code_array = explode(':',$vv['code']);
					$block_info = array();
					$query = $db->query("SELECT* FROM ".DB_PREFIX."_selection WHERE type='" . $db->safesql($vv['type']) . "' AND code LIKE '%".$code_array['1']."%'");		
					if($db->numRows($query) > 0)
					{
						$info = $db->getRow($query);
						$block_info['id'] = $info['id'];
						$block_info['code'] = $info['code'];
						$block_info['type'] = $typeBlockArray[$info['type']];
						$block_info['name'] = $typeBlockArrayEng[$info['type']];
						$block_info['aid_scheme'] = empty($info['aid_scheme']) ? '-1' : $info['aid_scheme'];
						$block_info['aid_size'] = empty($info['aid_size']) ? '-1' : $info['aid_size'];		
						$query_detail = $db->query("SELECT sd.*, c.title, c.sku, cm.name as cmname, cu.name as cuname FROM JMY_selection_detail as sd LEFT JOIN JMY_catalog as c ON(sd.cid = c.id) LEFT JOIN 	JMY_catalog_manufacture as cm ON(cm.id_1c = c.manufacture) LEFT JOIN JMY_catalog_unit as cu ON(cu.unit = c.unit) WHERE sd.selection_id = '".$block_info['id']."' ORDER BY sd.position");	
						if($db->numRows($query_detail) > 0)
						{
							$block_info['specification'] = array();
							while($detail = $db->getRow($query_detail)) 
							{
								$block_info['specification'][$detail['id']]['id'] = $detail['id'];						
								$block_info['specification'][$detail['id']]['cid'] = $detail['cid'];						
								$block_info['specification'][$detail['id']]['title'] = htmlspecialchars($detail['title'], ENT_QUOTES);						
								$block_info['specification'][$detail['id']]['sku'] = $detail['sku'];						
								$block_info['specification'][$detail['id']]['count'] = $detail['count'];						
								$block_info['specification'][$detail['id']]['manufacture'] = htmlspecialchars($detail['cmname'], ENT_QUOTES);						
								$block_info['specification'][$detail['id']]['unit'] = $detail['cuname'];						
							}
						}
						switch ($vv['type']) {
							case '0':
								$cid = '39990';
							break;
							case '1':
								$cid = '39986';
							break;
							case '2':
								$cid = '39988';
							break;
							case '3':
								$cid = '39989';
							break;
						}
						$block_cart = array();
						$block_cart['id'] = $cid;					
						$block_cart['subid'] = [];					
						$query = $db->query("SELECT c.*, cu.name as cuname FROM JMY_catalog as c LEFT JOIN JMY_catalog_unit as cu ON(c.unit = cu.unit) WHERE c.id = '" . $cid . "' LIMIT 1");
						$catalog = $db->getRow($query);
						$query = $db->query("SELECT sum FROM JMY_catalog_price as cp LEFT JOIN JMY_catalog_price_detail as cpd ON(cp.id = cpd.price_id) WHERE cpd.cid = '".$cid."' AND cp.active = 1 AND price_type = '3' 	ORDER BY cp.date DESC");
						$qp = $db->getRow($query);
						if(empty($qp["sum"])){
							$query = $db->query("SELECT sum FROM JMY_catalog_price as cp LEFT JOIN JMY_catalog_price_detail as cpd ON(cp.id = cpd.price_id) WHERE cpd.cid = '".$cid."' AND cp.active = 1 AND price_type = 	'2' ORDER BY cp.date DESC");
							$qe = $db->getRow($query);
							if(empty($qe["sum"])){
								$price = 0;
							} else {
								$price = round($qe["sum"] * Currency::get_exchange()->get(978), 2);
								
							}
						} else {
							$price = $qp["sum"];
						}
					
						$block_cart['name'] = $catalog["title"];	
						$block_cart['price'] = $price;	
						$block_cart['url'] = "catalog_v2/".$catalog["altname"].".html";	
						$block_cart['photo'] = $catalog["preview"];	
						
						$block_info['cart'] = $block_cart;
						$block_info['index'] = $vv['index'];
					
						$returnJson['blocks'][] = $block_info;
					}	
					else
					{
						
						
						
						$block_info = array();
						$block_info['id'] = '-1';
						$block_info['code'] = $vv['code'];
						$block_info['type'] = $typeBlockArray[$vv['type']];	
						$block_info['name'] = $typeBlockArrayEng[$vv['type']];	
						$block_info['index'] = $vv['index'];
						$returnJson['blocks'][] = $block_info;
					}				
				}
			}
		}
		$group_code = $scheme_code;
		$query = $db->query("SELECT* FROM ".DB_PREFIX."_selection_group WHERE code LIKE '%".$group_code."%' LIMIT 1");		
		if($db->numRows($query) > 0)
		{
			$group_info = $db->getRow($query);
			$returnJson['group']['id'] = $group_info['id'];
			$returnJson['group']['code'] = $group_info['code'];
			$returnJson['group']['aid_scheme'] = $group_info['aid_scheme'];
		}
		else
		{
			$returnJson['group']['id'] = '-1';
			$returnJson['group']['code'] = $group_code;
		}
		$returnJsonComplete = json_encode($returnJson, JSON_UNESCAPED_UNICODE);
		return $returnJsonComplete;
		
}

function getBlockInfo($id)
{
	$db_name = 'atrienergo_block';
	$db_user = 'atrienergo_block';
	$db_pass = 'M0d8Q2v5';
	$db_host = 'localhost';
	$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name) 
	or die("Ошибка " . mysqli_error($link));
	$search_query = mysqli_real_escape_string($link, $id);
	$query ="SELECT * FROM constructor_block WHERE id='".$search_query."' LIMIT 1";
	$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link)); 
	if($result->num_rows > 0)
	{
		$return = mysqli_fetch_assoc($result);
	}
	mysqli_close($link);
	return $return;
}

function rusBlock($value)
{
	$return = '';
	switch ($value) {
		case 'block_vvoda':
			$return = 'Блок ввода';
		break;
		case 'block_otopleniya':
			$return = 'Отопление';
		break;
		case 'block_podpitky':
			$return = 'Подпитка';
		break;
	}
	return $return;
}

function rusTrueFalse($value)
{
	if ($value == 'true') {$return = 'Есть';} else {$return = 'Нет';}
	return $return;
}

function selectCode() {

	echo '1';
	exit;
    require_once ROOT . '/vendor/autoload.php';
    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . ROOT . '/8f715c9461e6.json');
    $client = new Google_Client;
    $client->useApplicationDefaultCredentials();
    $client->setApplicationName("Something to do with my representatives");
    $client->setScopes(['https://www.googleapis.com/auth/drive', 'https://spreadsheets.google.com/feeds']);
    if ($client->isAccessTokenExpired()) {
        $client->refreshTokenWithAssertion();
    }
    $accessToken = $client->fetchAccessTokenWithAssertion()["access_token"];
    ServiceRequestFactory::setInstance(
        new DefaultServiceRequest($accessToken)
    );
    $spreadsheet = (new Google\Spreadsheet\SpreadsheetService)
        ->getSpreadsheetFeed()
        ->getByTitle('btp_110520191430');
    $worksheets = $spreadsheet->getWorksheetFeed()->getEntries();
	
    $worksheet = $worksheets[0];

	//echo '<pre>';
	//print_r($_POST);
	if(isset($_POST['iData'])&&!empty($_POST['iData']))
	{
		$iData = json_decode($_POST['iData']);
		$iDataGeneral = json_decode($_POST['iDataGeneral']);
		$postData = json_decode($_POST['postData']);
		$postDataArray = explode('||',$_POST['postData']);
	
		$blocksArray = array();
		foreach ($postDataArray as $key => $value) {
			$block = substr($value, 0, 2);
			$value_json = json_decode(substr($value, 3));
			if (isset($value_json->block_vvoda))
			{
				$block_vvoda = $value_json->block_vvoda;
			}
				//print_r($value_json);
			$block_name = key($value_json);
			$blocksArray[$block]['index'] = $block;
			$blocksArray[$block]['name'] = $block_name;
			$blocksArray[$block]['name_rus'] = rusBlock($block_name);
			$blocksArray[$block]['data'] = $value_json;	
			$blocksArray[$block]['scheme_id'] = $value_json->$block_name->id;	
		}
		$edit_id = intval($_POST['iUrl']);
		
	}
    global $core, $db;
    $cellFeed = $worksheet->getCellFeed();
    $batchRequest = new Google\Spreadsheet\Batch\BatchRequest();
	
	
	//Обнуление 
	$batchRequest->addEntry($cellFeed->createCell(13, 14, ''));
	$batchRequest->addEntry($cellFeed->createCell(13, 18, ''));
	$batchRequest->addEntry($cellFeed->createCell(13, 23, ''));
	$batchRequest->addEntry($cellFeed->createCell(13, 29, ''));
	for ($i = 14; $i <= 17; $i++) 
	{
		$batchRequest->addEntry($cellFeed->createCell($i, 1, ''));
		$batchRequest->addEntry($cellFeed->createCell($i, 3, ''));
		$batchRequest->addEntry($cellFeed->createCell($i, 14, ''));
		$batchRequest->addEntry($cellFeed->createCell($i, 18, ''));
	}
	$batchRequest->addEntry($cellFeed->createCell(20, 3, ''));
	$batchRequest->addEntry($cellFeed->createCell(20, 9, ''));
	$batchRequest->addEntry($cellFeed->createCell(20, 15, ''));
	
	$batchRequest->addEntry($cellFeed->createCell(24, 9, ''));
	$batchRequest->addEntry($cellFeed->createCell(24, 15, ''));
	$batchRequest->addEntry($cellFeed->createCell(24, 21, ''));
	$batchRequest->addEntry($cellFeed->createCell(24, 27, ''));
	
	$batchRequest->addEntry($cellFeed->createCell(28, 9, ''));
	$batchRequest->addEntry($cellFeed->createCell(28, 15, ''));
	$batchRequest->addEntry($cellFeed->createCell(28, 21, ''));
	$batchRequest->addEntry($cellFeed->createCell(28, 27, ''));
	
	$batchRequest->addEntry($cellFeed->createCell(36, 3, ''));
	$batchRequest->addEntry($cellFeed->createCell(36, 15, ''));
	$batchRequest->addEntry($cellFeed->createCell(36, 21, ''));
	$batchRequest->addEntry($cellFeed->createCell(36, 26, ''));
	$batchRequest->addEntry($cellFeed->createCell(36, 27, ''));

	$batchRequest->addEntry($cellFeed->createCell(40, 3, ''));
	$batchRequest->addEntry($cellFeed->createCell(40, 9, ''));
	$batchRequest->addEntry($cellFeed->createCell(40, 15, ''));
	$batchRequest->addEntry($cellFeed->createCell(40, 21, ''));
	$batchRequest->addEntry($cellFeed->createCell(40, 27, ''));
	
	$batchRequest->addEntry($cellFeed->createCell(44, 3, ''));
	$batchRequest->addEntry($cellFeed->createCell(44, 9, ''));
	$batchRequest->addEntry($cellFeed->createCell(44, 15, ''));
	$batchRequest->addEntry($cellFeed->createCell(44, 21, ''));
	$batchRequest->addEntry($cellFeed->createCell(44, 27, ''));
	
	
	//iData									   строка столбец
	$batchRequest->addEntry($cellFeed->createCell(13, 14, dotsReplase($iData->iData->t1)));
    $batchRequest->addEntry($cellFeed->createCell(13, 18, dotsReplase($iData->iData->t2)));
	
	//iDataGeneral							   строка столбец
	$batchRequest->addEntry($cellFeed->createCell(13, 23, dotsReplase($iDataGeneral->iDataGeneral->p1)));
    $batchRequest->addEntry($cellFeed->createCell(13, 29, dotsReplase($iDataGeneral->iDataGeneral->p2)));
	
	$i = 0;
	$blocks_count = array();
	$blocks_count['block_vvoda'] = 0;
	$blocks_count['block_otopleniya'] = 0;
	$blocks_count['block_podpitky'] = 0;
	$blocks_count['block_gvs'] = 0;
	
	foreach ($blocksArray as $key => $value) 
	{
		//var_dump($value);
		$name = $value['name'];
		if($name != 'block_vvoda' && $name != 'block_podpitky' && $name!='block_gvs')
		{
			//			  							  строка столбец
			$batchRequest->addEntry($cellFeed->createCell(14+$i, 1, $value['name_rus']));
			$batchRequest->addEntry($cellFeed->createCell(14+$i, 3, dotsReplase($value['data']->$name->q)));
			$batchRequest->addEntry($cellFeed->createCell(14+$i, 14, dotsReplase($value['data']->$name->t11)));
			$batchRequest->addEntry($cellFeed->createCell(14+$i, 18, dotsReplase($value['data']->$name->t21)));
			$i++;
		}
		switch ($value['name']) 
		{
			case 'block_vvoda':
				$blocks_count['block_vvoda']++;
				//			  							  строка столбец
				$batchRequest->addEntry($cellFeed->createCell(20, 3, rusTrueFalse($value['data']->$name->uzel_ucheta)));
				$batchRequest->addEntry($cellFeed->createCell(20, 9, rusTrueFalse($value['data']->$name->regul_perepad)));
				$batchRequest->addEntry($cellFeed->createCell(20, 15, rusTrueFalse($value['data']->$name->bal_klapan)));
			break;
			case 'block_otopleniya':
				$blocks_count['block_otopleniya']++;
				$block_otopleniya_str = 24; 
				if($blocks_count['block_otopleniya'] != 1) {$block_otopleniya_str = 28;}	
				$pch = ($value['data']->$name->pch == 1) ? 'с ПЧ' : 'без ПЧ';
				//$batchRequest->addEntry($cellFeed->createCell($block_otopleniya_str, 3, rusTrueFalse($value['data']->$name->uzel_ucheta)));
				$batchRequest->addEntry($cellFeed->createCell($block_otopleniya_str, 9, $pch));
				$batchRequest->addEntry($cellFeed->createCell($block_otopleniya_str, 15, $value['data']->$name->regKlapan));
				$batchRequest->addEntry($cellFeed->createCell($block_otopleniya_str, 21, $value['data']->$name->kontr));
				$batchRequest->addEntry($cellFeed->createCell($block_otopleniya_str, 27, $value['data']->$name->shkafAvtomatiky));
			break;
			case 'block_podpitky':
				$blocks_count['block_podpitky']++;
				$podpitka_str = 40; 
				if($blocks_count['block_podpitky'] != 1) {$podpitka_str = 44;}		
				$nasosStan = ($value['scheme_id'] == 23) ? 'Да' : 'Нет';
				$batchRequest->addEntry($cellFeed->createCell($podpitka_str, 3, 'Да'));
				$batchRequest->addEntry($cellFeed->createCell($podpitka_str, 9, $nasosStan));
				$batchRequest->addEntry($cellFeed->createCell($podpitka_str, 15, $value['data']->$name->povNasos));
				$batchRequest->addEntry($cellFeed->createCell($podpitka_str, 21, $value['data']->$name->klapanSol));
				$batchRequest->addEntry($cellFeed->createCell($podpitka_str, 27, $value['data']->$name->rasshirBak));
			break;
			case 'block_gvs':
				$blocks_count['block_gvs']++;
				$gvsInfo = getBlockInfo($value['scheme_id']);
				$gvsType = $gvsInfo['gvs_type'];
				$shemaPodkl = ($gvsType == 1) ? 'Смешанная' : 'Параллельная';
				$batchRequest->addEntry($cellFeed->createCell(17, 3, dotsReplase($value['data']->$name->qGVS)));
				$batchRequest->addEntry($cellFeed->createCell(17, 14, $value['data']->$name->GVSt3));
				$batchRequest->addEntry($cellFeed->createCell(17, 18, $value['data']->$name->GVStv1));
				$batchRequest->addEntry($cellFeed->createCell(17, 26, $value['data']->$name->pb1));
				$batchRequest->addEntry($cellFeed->createCell(36, 3, $shemaPodkl));
				$batchRequest->addEntry($cellFeed->createCell(36, 26, 'с ПЧ'));
				$batchRequest->addEntry($cellFeed->createCell(36, 15, $value['data']->$name->regKlapanGVS));
				$batchRequest->addEntry($cellFeed->createCell(36, 21, $value['data']->$name->kontrGVS));
				$batchRequest->addEntry($cellFeed->createCell(36, 21, $value['data']->$name->kontrGVS));
				$batchRequest->addEntry($cellFeed->createCell(36, 27, $value['data']->$name->shkafAvtomatikyGVS));
			break;
		}			
	}
    $batchResponse = $cellFeed->insertBatch($batchRequest);
    $cellFeed = $worksheet->getCellFeed();	
	$blocks_count = array();
	$blocks_count['block_vvoda'] = 0;
	$blocks_count['block_otopleniya'] = 0;
	$blocks_count['block_podpitky'] = 0;
	$blocks_count['block_gvs'] = 0;	
	$blocks_code = array();
	$scheme_code = array();
	$scheme_code['1'] = 0;
	$scheme_code['2'] = 0;
	$scheme_code['3'] = 0;
	$scheme_code['4'] = 0;
	$scheme_code['5'] = 0;
	$scheme_code['6'] = 0;	
	foreach ($blocksArray as $key => $value) 
	{
		switch ($value['name']) 
		{
			case 'block_vvoda':
				//echo 'Блок ввода БВ <br>';
				$code = '';
				$blocks_count[$value['name']]++;
				for ($i = 2; $i < 21; $i++) {
					$topLeftCornerCell = $cellFeed->getCell(21, $i);
					$code .= $topLeftCornerCell->getContent();
					//echo $topLeftCornerCell->getContent();
					
				}
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['index'] = $value['index'];				
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['code'] = $code;
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['type'] = '0';
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['scheme_id'] = $value['scheme_id'];
				$scheme_array = getBlockInfo($value['scheme_id']);
				$scheme_code['1'] = $scheme_array['position'];
			break;
			case 'block_otopleniya':
				$code = '';
				$blocks_count[$value['name']]++;
				$block_otopleniya_str = 25; 
				if($blocks_count[$value['name']] != 1) 
				{
					$block_otopleniya_str = 29;
					$scheme_array = getBlockInfo($value['scheme_id']);
					$scheme_code['3'] = $scheme_array['position'];
				}
				else
				{
					$scheme_array = getBlockInfo($value['scheme_id']);
					$scheme_code['2'] = $scheme_array['position'];
				}
					//echo '<br>Блок отоп. вент. БОВ-'.$blocks_count[$value['name']].'<br>';
				for ($i = 2; $i < 25; $i++) {
					$topLeftCornerCell = $cellFeed->getCell($block_otopleniya_str, $i);
					$code .= $topLeftCornerCell->getContent();
					//echo $topLeftCornerCell->getContent();
				}
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['index'] = $value['index'];		
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['code'] = $code;
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['type'] = '1';
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['scheme_id'] = $value['scheme_id'];
			break;
			case 'block_gvs':
				$code = '';
				$blocks_count[$value['name']]++;
				$gvsInfo = getBlockInfo($value['scheme_id']);
				$gvsType = $scheme_array['gvs_type'];
				$block_gvs_str = ($gvsType == 1) ? 37 : 38;
				
				//echo '<br>Блок ввода ГВС <br>';				
				for ($i = 2; $i < 31; $i++) {
					$topLeftCornerCell = $cellFeed->getCell($block_gvs_str, $i);
					$code .= $topLeftCornerCell->getContent();
					//echo $topLeftCornerCell->getContent();								
				}
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['index'] = $value['index'];		
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['code'] = $code;
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['type'] = '2';
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['scheme_id'] = $value['scheme_id'];
				
				$scheme_array = getBlockInfo($value['scheme_id']);
				$scheme_code['4'] = $scheme_array['position'];
			break;
			case 'block_podpitky':
				$code = '';
				$blocks_count[$value['name']]++;
				$podpitka_str = 41; 
				if($blocks_count[$value['name']] != 1) 
				{
					$podpitka_str = 45;
					$scheme_array = getBlockInfo($value['scheme_id']);
					$scheme_code['6'] = $scheme_array['position'];
				}	
				else
				{
					$scheme_array = getBlockInfo($value['scheme_id']);
					$scheme_code['5'] = $scheme_array['position'];
					
				}
				//echo '<br>Блок подпитки БП-'.$blocks_count[$value['name']].'<br>';
				for ($i = 2; $i < 17; $i++) {
					$topLeftCornerCell = $cellFeed->getCell($podpitka_str, $i);
					$code .= $topLeftCornerCell->getContent();
					//echo $topLeftCornerCell->getContent();
				}
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['index'] = $value['index'];		
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['code'] = $code;
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['type'] = '3';
				$blocks_code[$value['name']][$blocks_count[$value['name']]]['scheme_id'] = $value['scheme_id'];
			break;			
		}		
	}
    
	//echo '<br><br>';
	$error_code = false;
	foreach ($blocks_code as $key => $value) 
	{
		foreach ($value as $kk => $vv) 
		{
			#N/A		
			if (!(strpos($vv['code'], '#N/A') === false)) 
			{
				$error_code = true;
			}
		}
	}
	$returnJson = array();
	if($error_code)
	{
		$returnJson['error'][] = 'Произошла ошибка возможно вы ввели не корректные данные!';
		$returnJsonComplete = json_encode($returnJson, JSON_UNESCAPED_UNICODE);
		echo $returnJsonComplete;
	}
	else
	{	
		$returnJsonComplete = getDetail($blocks_code, implode('-',$scheme_code));
		$blocksArrayAll = array();
		$blocks_codeJson = json_encode($blocks_code, JSON_UNESCAPED_UNICODE);
		$blocksArrayJson = json_encode($blocksArray, JSON_UNESCAPED_UNICODE);
		$iDataJson = json_encode($iData, JSON_UNESCAPED_UNICODE);
		$iDataGeneralJson = json_encode($iDataGeneral, JSON_UNESCAPED_UNICODE);
		$update = $db->query("UPDATE `" . DB_PREFIX . "_selection_request` SET `detail_save` = '" . $returnJsonComplete . "', datail_editor = '".$blocksArrayJson."',   detail_idata = '".$iDataJson."', detail_idatageneral = '".$iDataGeneralJson."', `detail_scheme` = '" . implode('-',$scheme_code) . "', `detail_code` = '" . $blocks_codeJson . "', `status` = '1' WHERE `id` = '" . $edit_id . "' LIMIT 1 ;");
		if($update)
		{
			echo $returnJsonComplete;
		}			
		$query = $db->query("SELECT sr.uid, sr.object, o.name as oname FROM " . DB_PREFIX . "_selection_request as sr LEFT JOIN " . DB_PREFIX . "_organizations as o ON(o.id = sr.oid) WHERE sr.id = '" . $edit_id . "' LIMIT 1");	
		$sr = $db->getRow($query);
		$request = "Заявка от контрагента ".$sr["oname"].", заказчик ".fullUserName($sr["uid"]).", <a href=\"https://atri-energo.ru/administration/module/selection/view_request/".$edit_id."\">Заявка на БТП</a>";
		$db->query("INSERT INTO ".DB_PREFIX."_request (`uid_dir`, `title`, `request`, `object`, `type`, `date`, `status`) VALUES ('".$sr["uid"]."', 'Заявка на расчет атри-блока', '".$request."', '".$sr["object"]."', '2', '".date("Y-m-d H:i:s")."', '1')");
		$request_id = $db->insert_id();

		$designers = getDesignersRequest();
		$attr = [];
		$attr['uid'] = $designers;
		$attr['title'] = 'Новая заявка на расчет!';
		$attr['text'] = "Новая заявка на расчет";
		$attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
		$attr['url'] = config('app.url').'/administration/module/request/view/'.$request_id;
		$attr['pNotif'] = true;
		$attr['pPush'] = true;
		sendAllNotif($attr);
	}
}

function selectCodeFree() {
	global $core, $db;
	$url = explode('/', $_SERVER['HTTP_REFERER']);
	$edit_id = array_pop($url);
	$codeArray = array();
	if (isset($_POST['data']))
	{
		$codeArray['data'] = json_decode($_POST['data'], true);
	}
	if (isset($_POST['blockVvod']))
	{
		$codeArray['blockVvod'] = json_decode($_POST['blockVvod'], true);
	}
	if (isset($_POST['bov']))
	{
		$codeArray['bov'] = json_decode($_POST['bov'], true);
	}
	if (isset($_POST['gvs']))
	{
		$codeArray['gvs'] = json_decode($_POST['bov'], true);
	}
	$update = $db->query("UPDATE `" . DB_PREFIX . "_selection_request` SET `type` = '2', `detail_free` = '" . json_encode($codeArray, JSON_UNESCAPED_UNICODE) . "', `status` = '1' WHERE `id` = '" . $edit_id . "' LIMIT 1 ;");
	if($update)
	{
		$query = $db->query("SELECT sr.uid, sr.object, o.name as oname FROM " . DB_PREFIX . "_selection_request as sr LEFT JOIN " . DB_PREFIX . "_organizations as o ON(o.id = sr.oid) WHERE sr.id = '" . $edit_id . "' LIMIT 1");	
		$sr = $db->getRow($query);
		$request = "Заявка от контрагента ".$sr["oname"].", заказчик ".fullUserName($sr["uid"]).", <a href=\"https://atri-energo.ru/administration/module/selection/view_request_free/".$edit_id."\">Свободная заявка на БТП</a>";
		$db->query("INSERT INTO ".DB_PREFIX."_request (`uid_dir`, `title`, `request`, `object`, `type`, `date`, `status`) VALUES ('".$sr["uid"]."', 'Заявка на расчет атри-блока', '".$request."', '".$sr["object"]."', '2', '".date("Y-m-d H:i:s")."', '1')");
		$request_id = $db->insert_id();
		$designers = getDesignersRequest();
		$attr = [];
		$attr['uid'] = $designers;
		$attr['title'] = 'Новая заявка на расчет!';
		$attr['text'] = "Новая заявка на расчет";
		$attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
		$attr['url'] = config('app.url').'/administration/module/request/view/'.$request_id;
		$attr['pNotif'] = true;
		$attr['pPush'] = true;
		sendAllNotif($attr);
		echo '1';
	}	
	else
	{
		echo '0';
	}
	//print_r($_POST);
	/*
	echo '<pre>';
	print_r($_POST);
	>Array
(
   <pre>Array
(
    [data] => {"data": {"sfWidth": "1","sfLenght": "2","sfHeight": "3","sfHeightBuilding": "4","message": "","nKolon": "1"}}
    [blockVvod] => {"blockVvoda": {"q": "1","qType": "Гкал/чГкал/чГкал/чГкал/ч","t1": "w","t2": "3","p1": "4","p2": "5","p1Type": "МПаМПаМПаМПаМПаМПаМПаМПа","p2Type": "МПаМПаМПаМПаМПаМПаМПаМПа","uzel_ucheta": "true","podpitka": "true","regulator_davleniya": "true","SFregDavVal": "6","ogranichenie_rashoda": "true","SForgRasVal": "7"}}
    [bov] => {"bov": {"q": "q","qType": "Гкал/ч","t1": "w","t2": "e","p1": "t","p1Type": "МПа","p2": "e","p2Type": "МПа","t11": "e","t21": "e","dp": "t","h": "e","SFkontr": "","SFregKlapan": "","SFshkafAvtomatiky": "","SFtypeBlock": "отопления","bp": {"SFnasosStan": "e","SFnasosPov": "e","SFklapanSol": "e","SFrasshirBak": "e"},"SFpch": "1"},"bov": {"q": "","qType": "Гкал/ч","t1": "","t2": "","p1": "","p1Type": "МПа","p2": "","p2Type": "МПа","t11": "","t21": "","dp": "","h": "","SFkontr": "","SFregKlapan": "","SFshkafAvtomatiky": "","SFtypeBlock": "undefined","bp": {"SFnasosStan": "","SFnasosPov": "","SFklapanSol": "","SFrasshirBak": ""},"SFpch": "undefined"},"bov": {"q": "","qType": "Гкал/ч","t1": "","t2": "","p1": "","p1Type": "МПа","p2": "","p2Type": "МПа","t11": "","t21": "","dp": "","h": "","SFkontr": "","SFregKlapan": "","SFshkafAvtomatiky": "","SFtypeBlock": "undefined","bp": {"SFnasosStan": "","SFnasosPov": "","SFklapanSol": "","SFrasshirBak": ""},"SFpch": "undefined"}}
    [gvs] => {gvs": {"qGVS": "","qSelGVS": "Гкал/ч","GVSt1": "","GVSt2": "","p1": "","p1Type": "МПа","p2": "","p2Type": "МПа","GVSt3": "","GVStv1": "","pb1": "","pb1SelGVS": "МПа","kontrGVS": "","regKlapanGVS": "","shkafAvtomatikyGVS": "","SFshemaPodkl": "Согласно СП","SFpch": "1"},gvs": {"qGVS": "","qSelGVS": "Гкал/ч","GVSt1": "","GVSt2": "","p1": "","p1Type": "МПа","p2": "","p2Type": "МПа","GVSt3": "","GVStv1": "","pb1": "","pb1SelGVS": "МПа","kontrGVS": "","regKlapanGVS": "","shkafAvtomatikyGVS": "","SFshemaPodkl": "Согласно СП","SFpch": "1"},gvs": {"qGVS": "","qSelGVS": "Гкал/ч","GVSt1": "","GVSt2": "","p1": "","p1Type": "МПа","p2": "","p2Type": "МПа","GVSt3": "","GVStv1": "","pb1": "","pb1SelGVS": "МПа","kontrGVS": "","regKlapanGVS": "","shkafAvtomatikyGVS": "","SFshemaPodkl": "Согласно СП","SFpch": "1"},gvs": {"qGVS": "","qSelGVS": "Гкал/ч","GVSt1": "","GVSt2": "","p1": "","p1Type": "МПа","p2": "","p2Type": "МПа","GVSt3": "","GVStv1": "","pb1": "","pb1SelGVS": "МПа","kontrGVS": "","regKlapanGVS": "","shkafAvtomatikyGVS": "","SFshemaPodkl": "Согласно СП","SFpch": "1"}}
)
)*/
}