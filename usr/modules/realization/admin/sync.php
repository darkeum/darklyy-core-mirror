<?php
	
	if(isset($syncArray['realization']))
	{
		//Обработчик реализаций
		foreach ($syncArray['realization'] as $key => $value)
		{
			$query_realization = $db->query("SELECT * FROM ".DB_PREFIX."_realization WHERE id = '".$value['pid']."'");
            if($db->numRows($query_realization) > 0) 
            {
				
				$realization = $db->getRow($query_realization);	
				
				$query_relation_unit = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'unit' AND sync_base='".$id."'");
				$unitRefArray =array();
				if($db->numRows($query_relation_unit) > 0) 
				{					
					while($u = $db->getRow($query_relation_unit))
					{	
						$unitRefArray[$u['pid']] = $u['id_1c'];
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
				
				$query_relation_chart = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'chart' AND sync_base='".$id."'");
				$chartRefArray =array();
				if($db->numRows($query_relation_chart) > 0) 
				{					
					while($u = $db->getRow($query_relation_chart))
					{	
						$chartRefArray[$u['pid']] = $u['id_1c'];
					}
				}		
				
				$query_relation_subconto = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'catalog_subconto' AND sync_base='".$id."'");
				$subcontoRefArray =array();
				if($db->numRows($query_relation_subconto) > 0) 
				{					
					while($u = $db->getRow($query_relation_subconto))
					{	
						$subcontoRefArray[$u['pid']] = $u['id_1c'];
					}
				}		
				
				$syncDataList = array();
				$i = 0;
				$query_detail = $db->query("SELECT s.*, c.sku, c.title, c.types, c.unit, u.id as uinit_id, r.id_1c FROM ".DB_PREFIX."_realization_detail as s LEFT JOIN ".DB_PREFIX."_catalog AS c on(s.cid=c.id) LEFT JOIN ".DB_PREFIX."_catalog_unit AS u on (c.unit=u.unit) LEFT JOIN ".DB_PREFIX."_sync_relation AS r on (r.pid=s.cid AND r.module = 'catalog_v2' AND r.sync_base = '".$id."') WHERE s.rid = '".$realization['id']."' AND s.status != '0'");			
				$orgInfo = getOrgInfo($realization['org']);	
				//print_r($orgInfo);
				//exit;
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
									//echo 'Ключ номенклатуры пустой: '.$d['cid'];									
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
							echo 'Номенклатура не найдена в базе: '.$d['cid'];							
							print_r('insert and update');
							$client->{'Catalog_Номенклатура'}->id('');
							$data_catalog = $client->{'Catalog_Номенклатура'}->create($syncData_catalog);
							$sync_catalog_ref = $data_catalog->getLastId();
							$db->query("UPDATE `" . DB_PREFIX . "_sync_relation` SET `id_1c` = '".$sync_catalog_ref."' WHERE module = 'catalog_v2' AND pid = '".$d['cid']."' AND sync_base='".$id."' LIMIT 1;");							
							//exit;							
						}
						$i++;
						if($orgInfo['org']['nds'] == '0')
						{
							$nds_stavka = 'БезНДС';
							$nds_summa = '0';				
						}
						else
						{
							$nds_stavka = 'НДС'.\Price::getNds($orgInfo['org']['nds']);
							$nds_summa = $d['nds'];					
						}
						$listArray = array();
						$listArray = [
							'LineNumber' => $i,					
							'Номенклатура_Key' => $cRef,
							'КоличествоМест' => '0',
							'ЕдиницаИзмерения_Key' => $unitRefArray[$d['uinit_id']],
							'Коэффициент' => '1',
							'СчетУчета_Key' => $chartRefArray['223'],
							'СчетДоходов_Key' => $chartRefArray['55'],
							'Субконто' => $subcontoRefArray['1'],
							'Субконто_Type' => 'StandardODATA.Catalog_НоменклатурныеГруппы',
							'СчетУчетаНДСПоРеализации_Key' => $chartRefArray['348'],
							'СчетРасходов_Key' => $chartRefArray['360'],
							'Количество' => $d['count'],
							'Цена' => round($d['total']/$d['count'], 2),
							'Сумма' => $d['total'],
							'СтавкаНДС' => $nds_stavka,
							'СуммаНДС' => $nds_summa,							
						];
						$syncDataList[] = $listArray;
					}					
				}			
				if (isset($realization['print_info']))
				{
					//{"base":{"doc":"Заказ клиента №PN-4528 от 24 августа 2019","numb":"PN-4528","date":"2019-07-24","module":"sales","pid":"4528"},"oid":{"id":"1453","address":"426008, РЕСПУБЛИКА УДМУРТСКАЯ, ГОРОД ИЖЕВСК, УЛИЦА КОММУНАРОВ, ДОМ 244, ОФИС 301","bank":{"id":null,"data":""}},"org":{"id":"1","bank":{"id":null,"data":""}},"sign":{"leader":{"id":"7","data":"Вагапов Э. В., Приказ №1 от 20.04.2018"},"buch":{"id":"8","data":"Вагапов Э. В., Приказ №1 от 20.04.2018"},"ship":{"id":"29","data":"Вагапов Эльдар Владимирович","post":"Руководитель отдела розничной торговли"}}}
					///$print_info = json_decode($realization['print_info'], true);
////print_r($print_info); 
					//exit;
					
					
				}
				$query_oid = $db->query("SELECT * FROM ".DB_PREFIX."_organizations WHERE id = '".$realization['oid']."' ");
				if($db->numRows($query_oid) > 0) 
				{
					$r_oid = $db->getRow($query_oid);					
				}
				$relation_where = array();
				$relation_where[] = "(module = 'stock' AND pid = '".$realization['stock_id']."')";
				$relation_where[] = "(module = 'org' AND pid = '".$realization['org']."')";
				$relation_where[] = "(module = 'organizations' AND pid = '".$realization['oid']."')";
				$relation_where[] = "(module = 'users' AND pid = '".$realization['uid']."')";
				$relation_where[] = "(module = 'sales' AND pid = '".$realization['sales_id']."')";
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
				if (isset($refKeyArray['stock'][$realization['stock_id']])) 
				{
					$stock_key = $refKeyArray['stock'][$realization['stock_id']];
				}
				
				else
				{
					$error = 'Не указан ключ связи для склада: '.$realization['stock_id'];
					echo $error;
					exit;
				}
				if (isset($refKeyArray['org'][$realization['org']])) 
				{
					$org_key = $refKeyArray['org'][$realization['org']];
				}
				else
				{
					$error = 'Не указан ключ связи для организации: '.$realization['org'].'/'.$realization['id'];
					$suncReturn['realization']['error']++;
					$suncReturn['realization']['log'][] = $error;
					$suncReturn['realization']['log_detail']['error'] = $error;
					$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
					continue;
				}
				
				if (isset($refKeyArray['sales'][$realization['sales_id']]) && (!empty($refKeyArray['sales'][$realization['sales_id']]))) 
				{
					$sales_key = $refKeyArray['sales'][$realization['sales_id']];
				}
				else
				{
					$sales_key ="00000000-0000-0000-0000-000000000000";
				}
				
				if (isset($refKeyArray['organizations'][$realization['oid']])) 
				{
					$oid_key = $refKeyArray['organizations'][$realization['oid']];
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
					
					//$error = 'Не указан ключ связи для организации: '.$realization['oid'].'/'.$r_oid['inn'];
					//echo $error;
					//exit;
				}
				
				
				$date = explode(' ', $realization['date']);
				$date = $date['0'].'T'.$date['1'];
				$posted = ($realization['active'] == '1' ? 'true' : 'false');
				if($orgInfo['org']['nds'] == '0')
				{
					$nds_doc = 'true';				
				}
				else
				{		
					$nds_doc = 'false';			
				}
				$syncData = [
					//'ВидОперации' => 'Товары',					
					'Posted' => $posted,
					'Склад_Key' => $stock_key,
					'Number' => $realization['numb'],
					'Date' => $date,
					'Организация_Key' => $org_key,
					'АдресДоставки' => $r_oid['yaddress'],
					'СчетНаОплатуПокупателю_Key' => $sales_key,					
					//'БанковскийСчетОрганизации' => '',
					'СпособЗачетаАвансов' => 'Автоматически',
					'СуммаДокумента' => $realization['total'],					
					
					'Контрагент_Key' => $oid_key,
					'КратностьВзаиморасчетов' => 'true',
					'КурсВзаиморасчетов' => 'true',
					'ВалютаДокумента_Key' => $refKeyArray['catalog_currency']['1'],
					'СуммаВключаетНДС' => 'true',
					'ДокументБезНДС' => $nds_doc,
					//'ГлавныйБухгалтер_Key' => 'true',
					//'ОтпускПроизвел_Key' => '',
					//'Руководитель_Key' => '',
					'ВидЭлектронногоДокумента' => 'ТОРГ12Продавец',
					'Товары' => $syncDataList,
				];
				if(isset($refKeyArray['users'][$realization['uid']]))
				{
					$syncData['Ответственный_Key'] = $refKeyArray['users'][$realization['uid']];
				}
				//print_r($syncData);
				//exit;
				
				$query_relation = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'realization' AND pid = '".$value['pid']."' AND sync_base='".$id."'");
				if($db->numRows($query_relation) == 1) 
				{					
					$sync_relation = $db->getRow($query_relation);	
					if (empty($sync_relation['id_1c']))
					{
						$error = 'Ключ связи пустой: '.$sync_relation['id'];
						echo $error;
						exit;
					}
					$data_check = $client->{'Document_РеализацияТоваровУслуг'}->id($sync_relation['id_1c'])->get()->first();
					if(!empty($data_check))
					{
						//обновление номенклатуры
						//print_r('update');
						$client->{'Document_РеализацияТоваровУслуг'}->id('');
						$data_catalog = $client->{'Document_РеализацияТоваровУслуг'}->update($sync_relation['id_1c'], $syncData);
						//print_r($syncData);
						//добавление или обновление счета фактуры
						$sync_catalog_ref = $sync_relation['id_1c'];
						$i = 1;
						$syncDataBillList = array();
						$listBillArray = array();
						$listBillArray = [
								'LineNumber' => $i,				
								'ДокументОснование' => $sync_catalog_ref,
								'ДокументОснование_Type' => 'StandardODATA.Document_РеализацияТоваровУслуг',
							];
						$date = explode(' ', $realization['date']);
						$date = $date['0'].'T'.$date['1'];
						$posted = ($realization['active'] == '1' ? 'true' : 'false');
						$syncDataBillList[] = $listBillArray;
						$syncDataBill = [
							'ВидСчетаФактуры' => 'НаРеализацию',					
							'Posted' => $posted,
							'Number' => $realization['numb'],
							'Date' => $date,
							'Организация_Key' => $org_key,
							'Контрагент_Key' => $oid_key,
							'ДокументОснование' => $sync_catalog_ref,
							'ДокументОснование_Type' => 'StandardODATA.Document_РеализацияТоваровУслуг',
							'КодВидаОперации' => '01',
							'ДатаВыставления' => $date,
							'КодСпособаВыставления' => '1',
							'Выставлен' => 'true',
							'СуммаДокумента' => $realization['total'],
							'СуммаНДСДокумента' => $realization['nds'],					
							'ВалютаДокумента_Key' => $refKeyArray['catalog_currency']['1'],
							'ПредставлениеНомера' => $realization['numb'],
							'ДокументыОснования' => $syncDataBillList,
						];
						if(isset($refKeyArray['users'][$realization['uid']]))
						{
							$syncData['Ответственный_Key'] = $refKeyArray['users'][$realization['uid']];
						}
						$data_check = $client->{'Document_СчетФактураВыданный'}->filter("ДокументОснование eq '".$sync_catalog_ref."'")->get()->values();
						if(!empty($data_check))
						{
							//$client->{'Document_СчетФактураВыданный'}->id('');
							//$data_catalog = $client->{'Document_СчетФактураВыданный'}->update($sync_relation['id_1c'], $syncData);
						}
						else
						{
							if($orgInfo['org']['taxation'] != '2')
							{
								$data_catalog_bill = $client->{'Document_СчетФактураВыданный'}->create($syncDataBill);
								$sync_catalog_bill_ref = $data_catalog_bill->getLastId();
								syncRelation($realization['id'], 'realization_bill', $sync_catalog_bill_ref, $id);
							}
						}					
						//print_r($syncData);
						$suncReturn['realization']['success']++;
					}
					else
					{
						//создание номенклатуры и замена ключа синхронизации 
						/*
						print_r('insert and update');
						$client->{'Catalog_Номенклатура'}->id('');
						$data_catalog = $client->{'Catalog_Номенклатура'}->create($syncData);
						$sync_catalog_ref = $data_catalog->getLastId();
						$db->query("UPDATE `" . DB_PREFIX . "_sync_relation` SET `id_1c` = '".$sync_catalog_ref."' WHERE module = 'catalog_v2' AND pid = '".$value['pid']."' AND sync_base='".$id."' LIMIT 1;");
						//костыль
						if($id == '1')
						{
							$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `id_1c` = '".$sync_catalog_ref."' WHERE id = '".$value['pid']."' LIMIT 1;");						
						}
						print_r($syncData);*/
						$suncReturn['realization']['error']++;
						$error = 'ошибка 2 '.$sync_relation['id_1c'];
						$suncReturn['realization']['log'][] = $error;
						$suncReturn['realization']['log_detail']['error'] = $error;
						$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
						continue;
					}			
					
				}
				elseif($db->numRows($query_relation) > 1) 
				{
					$suncReturn['realization']['error']++;
					$error = 'Найден дубликат ключей синхронизации номенклатуры. Идентификатор номенклатуры: '.$catalog['id'];
					$suncReturn['realization']['log'][] = $error;
					$suncReturn['realization']['log_detail']['error'] = $error;
					$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
					continue;
				}
				else
				{
					if($posted == 'true')
					{
						//создание реализации				
						$client->{'Document_РеализацияТоваровУслуг'}->id('');
						//print_r('insert');
						$data_catalog = $client->{'Document_РеализацияТоваровУслуг'}->create($syncData);
						//echo '<pre>';
						//print_r($data_catalog);
						//print_r($syncData);
					//exit;
						$sync_catalog_ref = $data_catalog->getLastId();
						$date = date('Y-m-d H:i:s');	
						syncRelation($realization['id'], 'realization', $sync_catalog_ref, $id);
						$i = 1;
						$syncDataBillList = array();
						$listBillArray = array();
						$listBillArray = [
								'LineNumber' => $i,				
								'ДокументОснование' => $sync_catalog_ref,
								'ДокументОснование_Type' => 'StandardODATA.Document_РеализацияТоваровУслуг',
							];
						$date = explode(' ', $realization['date']);
						$date = $date['0'].'T'.$date['1'];
						$posted = ($realization['active'] == '1' ? 'true' : 'false');
						$syncDataBillList[] = $listBillArray;
						$syncDataBill = [
							'ВидСчетаФактуры' => 'НаРеализацию',					
							'Posted' => $posted,
							'Number' => $realization['numb'],
							'Date' => $date,
							'Организация_Key' => $org_key,
							'Контрагент_Key' => $oid_key,
							'ДокументОснование' => $sync_catalog_ref,
							'ДокументОснование_Type' => 'StandardODATA.Document_РеализацияТоваровУслуг',
							'КодВидаОперации' => '01',
							'ДатаВыставления' => $date,
							'КодСпособаВыставления' => '1',
							'Выставлен' => 'true',
							'СуммаДокумента' => $realization['total'],
							'СуммаНДСДокумента' => $realization['nds'],					
							'ВалютаДокумента_Key' => $refKeyArray['catalog_currency']['1'],
							'ПредставлениеНомера' => $realization['numb'],
							'ДокументыОснования' => $syncDataBillList,
						];
						if(isset($refKeyArray['users'][$realization['uid']]))
						{
							$syncData['Ответственный_Key'] = $refKeyArray['users'][$realization['uid']];
						}
						$data_check = $client->{'Document_СчетФактураВыданный'}->filter("ДокументОснование eq '".$sync_catalog_ref."'")->get()->values();
						if(!empty($data_check))
						{
							//$client->{'Document_СчетФактураВыданный'}->id('');
							//$data_catalog = $client->{'Document_СчетФактураВыданный'}->update($sync_relation['id_1c'], $syncData);
						}
						else
						{
							if($orgInfo['org']['taxation'] != '2')
							{
								$data_catalog_bill = $client->{'Document_СчетФактураВыданный'}->create($syncDataBill);
								$sync_catalog_bill_ref = $data_catalog_bill->getLastId();
								syncRelation($realization['id'], 'realization_bill', $sync_catalog_bill_ref, $id);
							}
						}
					}
					//print_r($syncData);
					$suncReturn['realization']['success']++;
				}
				$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '2', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
				$count_send++;
				$suncReturn['realization']['numb']++;
				
			}
		
		}
	}