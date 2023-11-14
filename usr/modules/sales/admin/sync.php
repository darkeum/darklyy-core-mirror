<?php
	
	if(isset($syncArray['sales']))
	{
		//Обработчик реализаций
		foreach ($syncArray['sales'] as $key => $value)
		{
			$query_sales = $db->query("SELECT * FROM ".DB_PREFIX."_sales WHERE id = '".$value['pid']."'");
            if($db->numRows($query_sales) > 0) 
            {
				$sales = $db->getRow($query_sales);	
				
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
				$query_detail = $db->query("SELECT s.*, c.sku, c.title, c.types, c.unit, u.id as uinit_id, r.id_1c FROM ".DB_PREFIX."_sales_detail as s LEFT JOIN ".DB_PREFIX."_catalog AS c on(s.cid=c.id) LEFT JOIN ".DB_PREFIX."_catalog_unit AS u on (c.unit=u.unit) LEFT JOIN ".DB_PREFIX."_sync_relation AS r on (r.pid=s.cid AND r.module = 'catalog_v2' AND r.sync_base = '".$id."') WHERE s.sales_id = '".$sales['id']."' AND s.del != '1' AND s.status != '0'");			
				$orgInfo = getOrgInfo($sales['org']);	
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
											
						if ($d['count'] == 0)
						{
							$dcount = '0';
							$dprice = '0';
						}
						else
						{
							$dcount = $d['count'];	
							$dprice = round($d['total']/$d['count']);
						}
						$listArray = [
							'LineNumber' => $i,					
							'Номенклатура_Key' => $cRef,							
							'Количество' => $dcount,
							'Цена' => $dprice,
							'Сумма' => $d['total'],							
							'СтавкаНДС' => $nds_stavka,
							'СуммаНДС' => $nds_summa,							
						];
						$syncDataList[] = $listArray;
						
					}					
				}					
				$query_oid = $db->query("SELECT * FROM ".DB_PREFIX."_organizations WHERE id = '".$sales['oid']."' ");
				if($db->numRows($query_oid) > 0) 
				{
					$r_oid = $db->getRow($query_oid);					
				}
				$relation_where = array();
				//$relation_where[] = "(module = 'stock' AND pid = '".$realization['stock_id']."')";
				$relation_where[] = "(module = 'org' AND pid = '".$sales['org']."')";
				$relation_where[] = "(module = 'organizations' AND pid = '".$sales['oid']."')";
				$relation_where[] = "(module = 'users' AND pid = '".$sales['uid']."')";
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
				if (isset($refKeyArray['org'][$sales['org']])) 
				{
					$org_key = $refKeyArray['org'][$sales['org']];
				}
				else
				{
					$error = 'Не указан ключ связи для организации: '.$sales['org'].'/'.$sales['id'];				
					$error_fix = $db->query("DELETE FROM `JMY_sync_register` WHERE `pid` = '".$sales['id']."' and `module` = 'sales'");					
					$suncReturn['sales']['error']++;
					$suncReturn['sales']['log'][] = $error;
					$suncReturn['sales']['log_detail']['error'] = $error;
					continue;
				}
				
				if (isset($refKeyArray['organizations'][$sales['oid']])) 
				{
					$oid_key = $refKeyArray['organizations'][$sales['oid']];
				}
				elseif(empty($sales['oid']) || $sales['oid'] == '0')
				{
					$oid_key = '';
				}
				else
				{
					$client->{'Catalog_Контрагенты'}->id('');
					$data_check_oid = $client->{'Catalog_Контрагенты'}->filter("ИНН eq '".$r_oid['inn']."'")->get()->values();
					//echo "ИНН eq '".$r_oid['inn']."'";
					//print_r($data_check_oid);
					//exit;
					if(count($data_check_oid) > 0)
					{
						//добавление ключа организации
						$sync_oid_ref = $data_check_oid[0]['Ref_Key'];						
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
						//print_r($sync_oid_ref);
					}
					
					//$error = 'Не указан ключ связи для организации: '.$realization['oid'].'/'.$r_oid['inn'];
					//echo $error;
					//exit;
				}
				//'Склад_Key' => $stock_key,
				
				$date = explode(' ', $sales['date']);
				$date = $date['0'].'T'.$date['1'];
				$posted = 'true';
				if($orgInfo['org']['nds'] == '0')
				{
					$nds_doc = 'true';				
				}
				else
				{		
					$nds_doc = 'false';			
				}
				if($sales['nds_includes'] == '1')
				{
					$nds_includes = 'true';				
				}
				else
				{		
					$nds_includes = 'false';			
				}
				$syncData = [				
					'Posted' => $posted,
					'Number' => $sales['numb'],
					'Date' => $date,
					'Организация_Key' => $org_key,
					'АдресДоставки' => $r_oid['yaddress'],
					'СуммаДокумента' => $sales['total'],
					'Контрагент_Key' => $oid_key,
					'КратностьВзаиморасчетов' => 'true',
					'КурсВзаиморасчетов' => 'true',
					'ВалютаДокумента_Key' => $refKeyArray['catalog_currency']['1'],
					'СуммаВключаетНДС' => $nds_includes,
					'ДокументБезНДС' => $nds_doc,
					'Товары' => $syncDataList,
				];
				if(isset($refKeyArray['users'][$sales['uid']]))
				{
					$syncData['Ответственный_Key'] = $refKeyArray['users'][$sales['uid']];
				}
				
				//print_r($syncData);
			//	exit;
				
				$query_relation = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'sales' AND pid = '".$value['pid']."' AND sync_base='".$id."'");
				if($db->numRows($query_relation) == 1) 
				{					
					$sync_relation = $db->getRow($query_relation);	
					if (empty($sync_relation['id_1c']))
					{
					
						$suncReturn['sales']['error']++;
						$error = 'Ключ связи пустой: '.$sync_relation['id'];
						$suncReturn['sales']['log'][] = $error;
						$suncReturn['sales']['log_detail']['error'] = $error;
						$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
						continue;
					}
					$data_check = $client->{'Document_СчетНаОплатуПокупателю'}->id($sync_relation['id_1c'])->get()->first();
					if(!empty($data_check))
					{
						//обновление номенклатуры
						//print_r('update');
						$client->{'Document_СчетНаОплатуПокупателю'}->id('');
						$data_catalog = $client->{'Document_СчетНаОплатуПокупателю'}->update($sync_relation['id_1c'], $syncData);
						//print_r($syncData);
						//добавление или обновление счета фактуры
						$sync_catalog_ref = $sync_relation['id_1c'];									
						//print_r($syncData);
						$suncReturn['sales']['success']++;
					}
					else
					{
						//создание номенклатуры и замена ключа синхронизации 						
						$suncReturn['sales']['error']++;
						$error = 'ошибка 2';
						$suncReturn['sales']['log'][] = $error;
						$suncReturn['sales']['log_detail']['error'] = $error;
						$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
						continue;
					}			
					
				}
				elseif($db->numRows($query_relation) > 1) 
				{
					$suncReturn['sales']['error']++;
					$error = 'Найден дубликат ключей синхронизации номенклатуры. Идентификатор номенклатуры: '.$catalog['id'];
					$suncReturn['sales']['log'][] = $error;
					$suncReturn['sales']['log_detail']['error'] = $error;
					$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
					continue;
				}
				else
				{
					//создание счета				
					$client->{'Document_СчетНаОплатуПокупателю'}->id('');					
					$data_catalog = $client->{'Document_СчетНаОплатуПокупателю'}->create($syncData);
					$sync_catalog_ref = $data_catalog->getLastId();
					$date = date('Y-m-d H:i:s');	
					syncRelation($sales['id'], 'sales', $sync_catalog_ref, $id);					
					$suncReturn['sales']['success']++;					
				}
				$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '2', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
				$count_send++;
				$suncReturn['sales']['numb']++;
			//	echo '<pre>';
			//	print_r($syncData);
			}
		
		}
	}