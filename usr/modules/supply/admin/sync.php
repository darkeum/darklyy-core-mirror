<?php
	
	if(isset($syncArray['receipts']))
	{
		//Обработчик поступлений
		
		foreach ($syncArray['receipts'] as $key => $value)
		{
			$query_receipts = $db->query("SELECT * FROM ".DB_PREFIX."_receipts WHERE id = '".$value['pid']."'");
			//echo '1';
            if($db->numRows($query_receipts) > 0) 
            {
				$receipts = $db->getRow($query_receipts);	
				$r_numb = $receipts['numb'];
				if(stripos($r_numb, 'AEPT') !== false)
				{
					$nnArray = explode("-", $r_numb);
					if($nnArray[1] >= '2736' && $nnArray[1] <= '3740')
					{
						$nnArray[1] = $nnArray[1].'/20';
					}
					$r_numb = implode('-', $nnArray);
					//dd($r_numb);
					//exit;
					//if()
				}
				$query_relation_unit = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'unit' AND sync_base='".$id."'");
				$unitRefArray =array();
				if($db->numRows($query_relation_unit) > 0) 
				{					
					while($u = $db->getRow($query_relation_unit))
					{	
						$unitRefArray[$u['pid']] = $u['id_1c'];
					}
				}	
				
				$query_relation_chart = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'chart' AND sync_base='".$id."'");
				$chartRefArray =array();
				if($db->numRows($query_relation_chart) > 0) 
				{					
					while($u = $db->getRow($query_relation_chart))
					{	
						$chartRefArray[$u['pid']] = $u['id_1c'];
					}
				}	
				
				$query_relation_types = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'catalog_types' AND sync_base='".$id."'");
				$typesRefArray =array();
				if($db->numRows($query_relation_types) > 0) 
				{					
					while($t = $db->getRow($query_relation_types))
					{	
						$typesRefArray[$t['pid']] = $t['id_1c'];
					}
				}	
				
				$syncDataList = array();
				$i = 0;
				$query_detail = $db->query("SELECT s.*, c.sku, c.title, c.types, c.unit, u.id as uinit_id, r.id_1c FROM ".DB_PREFIX."_receipts_detail as s LEFT JOIN ".DB_PREFIX."_catalog AS c on(s.cid=c.id) LEFT JOIN ".DB_PREFIX."_catalog_unit AS u on (c.unit=u.unit) LEFT JOIN ".DB_PREFIX."_sync_relation AS r on (r.pid=s.cid AND r.module = 'catalog_v2' AND r.sync_base = '".$id."') WHERE s.rid = '".$receipts['id']."' AND s.status != '0'");				
				if($db->numRows($query_detail) > 0) 
				{										
					while($d = $db->getRow($query_detail))
					{	
						$query_catalog_types = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_types WHERE id = '".$d['types']."'");				
						if($db->numRows($query_catalog_types) > 0) 
						{
							$types_data = $db->getRow($query_catalog_types);
						}
						$syncData_catalog = [
							'Артикул' => $d['sku'],					
							'НаименованиеПолное' => $d['title'],
							'Description' => $d['title'],
							'ВидНоменклатуры_Key' => $typesRefArray[$d['types']],
							'ЕдиницаИзмерения_Key' => $unitRefArray[$d['uinit_id']],
							'Услуга' => (($types_data['service'] != 1) ? 'false' : 'true'),
						];
						if(empty($d['id_1c']))
						{
							$query_catalog_check = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE pid = '".$d['cid']."' AND module = 'catalog_v2' AND sync_base = '".$id."'");				
							if($db->numRows($query_catalog_check) > 0) 
							{	
								$cr_data = $db->getRow($query_catalog_check);
								if (empty($cr_data['id_1c']))
								{	
									//echo 'ЧКлюч номенклатуры пустой: '.$d['cid'];
									$error_fix = $db->query("DELETE FROM `JMY_sync_relation` WHERE `id` = '".$cr_data['id']."' LIMIT 1");
									$data_catalog = $client->{'Catalog_Номенклатура'}->create($syncData_catalog);
									$sync_catalog_ref = $data_catalog->getLastId();
									$date = date('Y-m-d H:i:s');	
									$db->query("INSERT INTO `" . DB_PREFIX . "_sync_relation` ( `id` , `id_1c`, `module`,  `pid`, `sync_base`, `date`, `status`)  VALUES (NULL, '".$sync_catalog_ref."', 'catalog_v2', '".$d['cid']."', '".$id."', '".$date."', '1');");
									$cRef = $sync_catalog_ref;
								}
								else
								{
									echo 'Ключ номенклатуры пустой в поступлении: '.$d['cid'];									
									$cRef = $cr_data['id_1c'];
								}
								
							}
							else
							{
								$client->{'Catalog_Номенклатура'}->id('');
								//echo 'Ключ связи отсутствует: '.$d['cid'].'<br>';
								
								//print_r('insert');
								$data_catalog = $client->{'Catalog_Номенклатура'}->create($syncData_catalog);
								$sync_catalog_ref = $data_catalog->getLastId();
								$date = date('Y-m-d H:i:s');	
								$db->query("INSERT INTO `" . DB_PREFIX . "_sync_relation` ( `id` , `id_1c`, `module`,  `pid`, `sync_base`, `date`, `status`)  VALUES (NULL, '".$sync_catalog_ref."', 'catalog_v2', '".$d['cid']."', '".$id."', '".$date."', '1');");
								$cRef = $sync_catalog_ref;
							}
						}
						else
						{
							$cRef = $d['id_1c'];
						}						
						$data_check_catalog = $client->{'Catalog_Номенклатура'}->id($cRef)->get()->first();
						if(empty($data_check_catalog))
						{
							//echo 'Номенклатура не найдена в базе: '.$d['cid'];							
							//print_r('insert and update');
							$client->{'Catalog_Номенклатура'}->id('');
							$data_catalog = $client->{'Catalog_Номенклатура'}->create($syncData_catalog);
							$sync_catalog_ref = $data_catalog->getLastId();
							$db->query("UPDATE `" . DB_PREFIX . "_sync_relation` SET `id_1c` = '".$sync_catalog_ref."' WHERE module = 'catalog_v2' AND pid = '".$d['cid']."' AND sync_base='".$id."' LIMIT 1;");							
							//exit;							
						}
						if($d['type_nds'] == '0')
						{
							$nds_stavka = 'БезНДС';
							$nds_summa = '0';				
						}
						else
						{
							$nds_stavka = 'НДС'.\Price::getNds($d['type_nds']);
							$nds_summa = $d['nds'];					
						}
						$i++;
						$listArray = array();
						$listArray = [
							'LineNumber' => $i,					
							'Номенклатура_Key' => $cRef,
							'КоличествоМест' => '0',
							'ЕдиницаИзмерения_Key' => $unitRefArray[$d['uinit_id']],
							'Коэффициент' => '1',
							'Количество' => $d['count'],
							'Цена' => $d['price'],
							'Сумма' => $d['total'],
							'СтавкаНДС' => $nds_stavka,
							'СуммаНДС' => $nds_summa,	
							'СчетУчета_Key' => $chartRefArray['223'],
							'СчетУчетаНДС_Key' => $chartRefArray['290'],													
						];
						$syncDataList[] = $listArray;
					}					
				}
				$orgInfo = getOrgInfo($receipts['org']);						
				$query_oid = $db->query("SELECT * FROM ".DB_PREFIX."_organizations WHERE id = '".$receipts['oid']."' ");
				if($db->numRows($query_oid) > 0) 
				{
					$r_oid = $db->getRow($query_oid);					
				}
				$relation_where = array();
				$relation_where[] = "(module = 'stock' AND pid = '".$receipts['stock_id']."')";
				$relation_where[] = "(module = 'org' AND pid = '".$receipts['org']."')";
				$relation_where[] = "(module = 'organizations' AND pid = '".$receipts['oid']."')";
				$relation_where[] = "(module = 'users' AND pid = '".$receipts['uid']."')";
				$relation_where[] = "(module = 'catalog_currency' AND pid = '1')";
				$relation_where_str = implode('OR', $relation_where);
				$query_relation_stock = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE (".$relation_where_str.") AND sync_base='".$id."'");
				$refKeyArray = array();
				if($db->numRows($query_relation_stock) > 0) 
				{
					while($r = $db->getRow($query_relation_stock))
					{
						$refKeyArray[$r['module']][$r['pid']] = $r['id_1c'];	
					}					
				}
				if (isset($refKeyArray['stock'][$receipts['stock_id']])) 
				{
					$stock_key = $refKeyArray['stock'][$receipts['stock_id']];
				}
				else
				{
					//костыль
					$error = 'Не указан ключ связи для склада: '.$receipts['stock_id'];
					$suncReturn['receipts']['error']++;	
					$suncReturn['receipts']['log'][] = $error;
					$suncReturn['receipts']['log_detail']['error'] = $error;
					$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
					continue;
					//
					//echo $error;
					//exit;
				}
				if (isset($refKeyArray['org'][$receipts['org']])) 
				{
					$org_key = $refKeyArray['org'][$receipts['org']];
				}
				else
				{
					$error = 'Не указан ключ связи для организации: '.$receipts['org'].'/'.$receipts['id'];
					echo $error;
					exit;
				}
				
				if (isset($refKeyArray['organizations'][$receipts['oid']])) 
				{
					$oid_key = $refKeyArray['organizations'][$receipts['oid']];
				}
				else
				{
						$client->{'Catalog_Контрагенты'}->id('');
					$data_check = $client->{'Catalog_Контрагенты'}->filter("ИНН eq '".$r_oid['inn']."'")->get()->values();
					if(count($data_check) > 0)
					{
						//добавление ключа организации
						$sync_oid_ref = $data_check[0]['Ref_Key'];						
						syncRelation($r_oid['id'], 'organizations', $sync_oid_ref, $id);
						$oid_key = $sync_oid_ref;						
					}
					else
					{
						$syncDataOid = [
							'Description' => htmlspecialchars_decode($r_oid['name']),					
							'ИНН' => $r_oid['inn'],
							'КПП' =>  $r_oid['kpp'],
							'НаименованиеПолное' => $r_oid['name'],
							'ЮридическоеФизическоеЛицо' => ($r_oid['type'] == 'LEGAL' ? 'ЮридическоеЛицо' : 'ФизическоеЛицо'),							
						];
						$client->{'Catalog_Контрагенты'}->id('');
						$data_oid = $client->{'Catalog_Контрагенты'}->create($syncDataOid);
						$sync_oid_ref = $data_oid->getLastId();
						syncRelation($r_oid['id'], 'organizations', $sync_oid_ref, $id);
						$oid_key = $sync_oid_ref;
						print_r($sync_oid_ref);
					}
					
					
				}
				
				
				$date = explode(' ', $receipts['date']);
				$date = $date['0'].'T'.$date['1'];
				$date_doc = $receipts['date_receipts'].'T00:00:00';
				$posted = ($receipts['active'] == '1' ? 'true' : 'false');
				$nds_includes = ($receipts['nds_includes'] == '1' ? 'true' : 'false');
				$syncData = [
					'Number' => $r_numb,
					'Date' => $date,
					'Posted' => $posted,
					'ВидОперации' => 'Товары',					
					'Организация_Key' => $org_key,
					'Склад_Key' => $stock_key,
					'Контрагент_Key' => $oid_key,
					'СпособЗачетаАвансов' => 'Автоматически',
					'СчетУчетаРасчетовСКонтрагентом_Key' => $chartRefArray['94'],
					'СчетУчетаРасчетовПоАвансам_Key' => $chartRefArray['267'],
					'НомерВходящегоДокумента' => $receipts['doc_number'],
					'ДатаВходящегоДокумента' => $date_doc,
					'КратностьВзаиморасчетов' => 'true',
					'КурсВзаиморасчетов' => 'true',
					'СуммаВключаетНДС' => $nds_includes,
					'СуммаДокумента' => $receipts['total'],					
					'Товары' => $syncDataList,
				];
				if(isset($refKeyArray['users'][$receipts['uid']]))
				{
					$syncData['Ответственный_Key'] = $refKeyArray['users'][$receipts['uid']];
				}
				
				
				$query_relation = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'receipts' AND pid = '".$value['pid']."' AND sync_base='".$id."'");
				if($db->numRows($query_relation) == 1) 
				{					
					$sync_relation = $db->getRow($query_relation);	
					if (empty($sync_relation['id_1c']))
					{					
						$suncReturn['receipts']['error']++;	$error = 'Ключ связи пустой: '.$sync_relation['id'];
						$suncReturn['receipts']['log'][] = $error;
						$suncReturn['receipts']['log_detail']['error'] = $error;
						$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
						continue;
					}
					$data_check = $client->{'Document_ПоступлениеТоваровУслуг'}->id($sync_relation['id_1c'])->get()->first();
					if(!empty($data_check))
					{
						//обновление поступления
						//print_r('update');						
						$client->{'Document_ПоступлениеТоваровУслуг'}->id('');
						$data_catalog = $client->{'Document_ПоступлениеТоваровУслуг'}->update($sync_relation['id_1c'], $syncData);
						//echo '<pre>';
						//print_r($data_catalog);
						//exit;
						//добавление или обновление счета фактуры
						$sync_catalog_ref = $sync_relation['id_1c'];
						$i = 1;
						$syncDataBillList = array();
						$listBillArray = array();
						$listBillArray = [
								'LineNumber' => $i,				
								'ДокументОснование' => $sync_catalog_ref,
								'ДокументОснование_Type' => 'StandardODATA.Document_ПоступлениеТоваровУслуг',
							];
						$date = explode(' ', $receipts['date']);
						$date = $date['0'].'T'.$date['1'];
						$date_doc = $receipts['date_receipts'].'T00:00:00';
						$posted = ($receipts['active'] == '1' ? 'true' : 'false');
						$nds_includes = ($receipts['nds_includes'] == '1' ? 'true' : 'false');
						$syncDataBillList[] = $listBillArray;
						$syncDataBill = [
							'Number' => $r_numb,
							'Date' => $date,
							'Posted' => $posted,
							'Организация_Key' => $org_key,
							'ВидСчетаФактуры' => 'НаПоступление',					
							'Контрагент_Key' => $oid_key,
							'НомерВходящегоДокумента' => $receipts['doc_number'],
							'ДатаВходящегоДокумента' => $date_doc,							
							'ДокументОснование' => $sync_catalog_ref,
							'ДокументОснование_Type' => 'StandardODATA.Document_ПоступлениеТоваровУслуг',
							'КодВидаОперации' => '01',
							'КодСпособаПолучения' => '1',
							'СуммаДокумента' => $receipts['total'],
							'СуммаНДСДокумента' => $receipts['nds'],					
							'ВалютаДокумента_Key' => $refKeyArray['catalog_currency']['1'],
							'ПредставлениеНомера' => $receipts['doc_number'],
							'ДокументыОснования' => $syncDataBillList,
						];	
						if(isset($refKeyArray['users'][$receipts['uid']]))
						{
							$syncDataBill['Ответственный_Key'] = $refKeyArray['users'][$receipts['uid']];
						}	
						$data_check = $client->{'Document_СчетФактураПолученный'}->filter("ДокументОснование eq '".$sync_catalog_ref."'")->get()->values();
						if(!empty($data_check))
						{
							//$client->{'Document_СчетФактураПолученный'}->id('');
							//$data_catalog = $client->{'Document_СчетФактураПолученный'}->update($sync_relation['id_1c'], $syncData);
						}
						else
						{
							$data_catalog_bill = $client->{'Document_СчетФактураПолученный'}->create($syncDataBill);
							$sync_catalog_bill_ref = $data_catalog_bill->getLastId();
							syncRelation($receipts['id'], 'receipts_bill', $sync_catalog_bill_ref, $id);
							//print_r($syncData);
							//print_r($syncDataBill);
							$suncReturn['receipts']['success']++;
						}					
						//print_r($syncData);
					}
					else
					{
						//создание номенклатуры и замена ключа синхронизации 						
						//print_r('insert and update');
						$client->{'Document_ПоступлениеТоваровУслуг'}->id('');
						//print_r('insert');
						$data_catalog = $client->{'Document_ПоступлениеТоваровУслуг'}->create($syncData);
						$sync_catalog_ref = $data_catalog->getLastId();
						$date = date('Y-m-d H:i:s');	
						//syncRelation($receipts['id'], 'receipts', $sync_catalog_ref, $id);
						$db->query("UPDATE `" . DB_PREFIX . "_sync_relation` SET `id_1c` = '".$sync_catalog_ref."' WHERE module = 'receipts' AND pid = '".$value['pid']."' AND sync_base='".$id."' LIMIT 1;");
						$i = 1;
						$syncDataBillList = array();
						$listBillArray = array();
						$listBillArray = [
							'LineNumber' => $i,				
							'ДокументОснование' => $sync_catalog_ref,
							'ДокументОснование_Type' => 'StandardODATA.Document_ПоступлениеТоваровУслуг',
						];
						$date = explode(' ', $receipts['date']);
						$date = $date['0'].'T'.$date['1'];
						$date_doc = $receipts['date_receipts'].'T00:00:00';
						$posted = ($receipts['active'] == '1' ? 'true' : 'false');
						$nds_includes = ($receipts['nds_includes'] == '1' ? 'true' : 'false');
						$syncDataBillList[] = $listBillArray;
						$syncDataBill = [
							'Number' => $r_numb,
							'Date' => $date,
							'Posted' => $posted,
							'Организация_Key' => $org_key,
							'ВидСчетаФактуры' => 'НаПоступление',					
							'Контрагент_Key' => $oid_key,
							'НомерВходящегоДокумента' => $receipts['doc_number'],
							'ДатаВходящегоДокумента' => $date_doc,							
							'ДокументОснование' => $sync_catalog_ref,
							'ДокументОснование_Type' => 'StandardODATA.Document_ПоступлениеТоваровУслуг',
							'КодВидаОперации' => '01',
							'КодСпособаПолучения' => '1',
							'СуммаДокумента' => $receipts['total'],
							'СуммаНДСДокумента' => $receipts['nds'],					
							'ВалютаДокумента_Key' => $refKeyArray['catalog_currency']['1'],
							'ПредставлениеНомера' => $receipts['doc_number'],
							'ДокументыОснования' => $syncDataBillList,
						];	
						if(isset($refKeyArray['users'][$receipts['uid']]))
						{
							$syncDataBill['Ответственный_Key'] = $refKeyArray['users'][$receipts['uid']];
						}
						$data_check = $client->{'Document_СчетФактураПолученный'}->filter("ДокументОснование eq '".$sync_catalog_ref."'")->get()->values();
						if(!empty($data_check))
						{
							//$client->{'Document_СчетФактураПолученный'}->id('');
							//$data_catalog = $client->{'Document_СчетФактураПолученный'}->update($sync_relation['id_1c'], $syncData);
						}
						else
						{
							$data_catalog_bill = $client->{'Document_СчетФактураПолученный'}->create($syncDataBill);
							//print_r($data_catalog_bill);
							$sync_catalog_bill_ref = $data_catalog_bill->getLastId();
							syncRelation($receipts['id'], 'receipts_bill', $sync_catalog_bill_ref, $id);
						}
						$suncReturn['receipts']['success']++;
					}			
				}
				elseif($db->numRows($query_relation) > 1) 
				{
					$suncReturn['receipts']['error']++;
					$error = 'Найден дубликат ключей синхронизации номенклатуры. Идентификатор номенклатуры: '.$catalog['id'];
					$suncReturn['receipts']['log'][] = $error;
					$suncReturn['receipts']['log_detail']['error'] = $error;
					$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
					continue;
				}
				else
				{
					//создание поступления				
					$client->{'Document_ПоступлениеТоваровУслуг'}->id('');
					//print_r('insert');
					$data_catalog = $client->{'Document_ПоступлениеТоваровУслуг'}->create($syncData);
					$sync_catalog_ref = $data_catalog->getLastId();
					//echo '<pre>';
					//print_r($data_catalog);
					//print_r($syncData);
					//exit;
					$date = date('Y-m-d H:i:s');	
					syncRelation($receipts['id'], 'receipts', $sync_catalog_ref, $id);
					$i = 1;
					$syncDataBillList = array();
					$listBillArray = array();
					$listBillArray = [
						'LineNumber' => $i,				
						'ДокументОснование' => $sync_catalog_ref,
						'ДокументОснование_Type' => 'StandardODATA.Document_ПоступлениеТоваровУслуг',
					];
					$date = explode(' ', $receipts['date']);
					$date = $date['0'].'T'.$date['1'];
					$date_doc = $receipts['date_receipts'].'T00:00:00';
					$posted = ($receipts['active'] == '1' ? 'true' : 'false');
					$nds_includes = ($receipts['nds_includes'] == '1' ? 'true' : 'false');
					$syncDataBillList[] = $listBillArray;
					$syncDataBill = [
						'Number' => $r_numb,
						'Date' => $date,
						'Posted' => $posted,
						'Организация_Key' => $org_key,
						'ВидСчетаФактуры' => 'НаПоступление',					
						'Контрагент_Key' => $oid_key,
						'НомерВходящегоДокумента' => $receipts['doc_number'],
						'ДатаВходящегоДокумента' => $date_doc,							
						'ДокументОснование' => $sync_catalog_ref,
						'ДокументОснование_Type' => 'StandardODATA.Document_ПоступлениеТоваровУслуг',
						'КодВидаОперации' => '01',
						'КодСпособаПолучения' => '1',
						'СуммаДокумента' => $receipts['total'],
						'СуммаНДСДокумента' => $receipts['nds'],					
						'ВалютаДокумента_Key' => $refKeyArray['catalog_currency']['1'],
						'ПредставлениеНомера' => $receipts['doc_number'],
						'ДокументыОснования' => $syncDataBillList,
					];	
					if(isset($refKeyArray['users'][$receipts['uid']]))
					{
						$syncDataBill['Ответственный_Key'] = $refKeyArray['users'][$receipts['uid']];
					}
					$data_check = $client->{'Document_СчетФактураПолученный'}->filter("ДокументОснование eq '".$sync_catalog_ref."'")->get()->values();
					if(!empty($data_check))
					{
						//$client->{'Document_СчетФактураПолученный'}->id('');
						//$data_catalog = $client->{'Document_СчетФактураПолученный'}->update($sync_relation['id_1c'], $syncData);
					}
					else
					{
						$data_catalog_bill = $client->{'Document_СчетФактураПолученный'}->create($syncDataBill);
						//print_r($data_catalog_bill);
						$sync_catalog_bill_ref = $data_catalog_bill->getLastId();
						syncRelation($receipts['id'], 'receipts_bill', $sync_catalog_bill_ref, $id);
					}
						
				
					
					
					$suncReturn['receipts']['success']++;
					
					
					//print_r($syncData);
					//print_r($syncDataBill);
				}
				$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '2', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
				$count_send++;
				$suncReturn['receipts']['numb']++;
			}
		}
	}
